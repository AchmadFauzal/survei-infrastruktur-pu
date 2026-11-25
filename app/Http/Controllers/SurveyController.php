<?php

namespace App\Http\Controllers;

use App\Models\Infrastructure;
use App\Models\SurveyPhoto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use ZipArchive;

class SurveyController extends Controller
{
    public function update(Request $request, Infrastructure $infrastructure)
    {
        $request->validate([
            'status_survei'       => 'required|in:sudah,belum',
            'jenis_infrastruktur' => 'nullable|string|max:255',
            'nama_hasil_survei'   => 'nullable|string|max:255',
            'alasan_verifikasi'   => 'nullable|string',
            'survey_latitude'     => 'nullable|numeric',
            'survey_longitude'    => 'nullable|numeric',
            'photos.*'            => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120', // Maks 5MB per foto
            'camera_photos.*'     => 'nullable|string', // Base64 dari kamera langsung
            'camera_lat.*'        => 'nullable|numeric',
            'camera_lng.*'        => 'nullable|numeric',
        ]);

        // Hitung total foto yang sudah ada + foto baru
        $existingCount = $infrastructure->photos()->count();
        $uploadedFiles = $request->file('photos', []);
        $cameraPhotos  = $request->input('camera_photos', []);
        $totalNew      = count($uploadedFiles) + count($cameraPhotos);

        if (($existingCount + $totalNew) > 5) {
            return back()->withErrors(['photos' => 'Maksimal total 5 foto per infrastruktur! Saat ini sudah ada ' . $existingCount . ' foto.']);
        }

        // 1. Update data infrastruktur
        $infrastructure->update([
            'status_survei'       => $request->status_survei,
            'jenis_infrastruktur' => $request->jenis_infrastruktur,
            'nama_hasil_survei'   => $request->nama_hasil_survei,
            'alasan_verifikasi'   => $request->alasan_verifikasi,
            'survey_latitude'     => $request->survey_latitude ?? $infrastructure->survey_latitude,
            'survey_longitude'    => $request->survey_longitude ?? $infrastructure->survey_longitude,
            'surveyed_by'         => auth()->id(),
            'surveyed_at'         => now(),
        ]);

        // 2. Simpan foto dari Upload File biasa
        foreach ($uploadedFiles as $file) {
            $path = $file->store('survey_photos', 'public');
            SurveyPhoto::create([
                'infrastructure_id' => $infrastructure->id,
                'file_path'         => $path,
                'source_type'       => 'upload',
                'photo_latitude'    => $request->survey_latitude,
                'photo_longitude'   => $request->survey_longitude,
                'captured_at'       => now(),
            ]);
        }

        // 3. Simpan foto dari Kamera Langsung (Base64 + Koordinat GPS saat jepret)
        $cameraLats = $request->input('camera_lat', []);
        $cameraLngs = $request->input('camera_lng', []);

        foreach ($cameraPhotos as $index => $base64Image) {
            if (preg_match('/^data:image\/(\w+);base64,/', $base64Image, $type)) {
                $data = substr($base64Image, strpos($base64Image, ',') + 1);
                $type = strtolower($type[1]); // jpg, png, webp
                $data = base64_decode($data);

                $fileName = 'survey_photos/cam_' . uniqid() . '.' . $type;
                Storage::disk('public')->put($fileName, $data);

                SurveyPhoto::create([
                    'infrastructure_id' => $infrastructure->id,
                    'file_path'         => $fileName,
                    'source_type'       => 'camera',
                    'photo_latitude'    => $cameraLats[$index] ?? null,
                    'photo_longitude'   => $cameraLngs[$index] ?? null,
                    'captured_at'       => now(),
                ]);
            }
        }

        return back()->with('success', 'Data hasil survei dan foto berhasil disimpan!');
    }

    public function destroyPhoto(SurveyPhoto $photo)
    {
        Storage::disk('public')->delete($photo->file_path);
        $photo->delete();

        return back()->with('success', 'Foto berhasil dihapus.');
    }

    /**
     * Helper untuk membersihkan karakter terlarang pada nama file OS (Windows/Linux)
     */
    private function sanitizeFileName(string $name): string
    {
        $clean = preg_replace('/[\/\\\\:*?"<>|]/', '', $name);
        $clean = preg_replace('/\s+/', ' ', trim($clean));
        return $clean !== '' ? $clean : 'Infrastruktur';
    }

    /**
     * Unduh 1 file foto dengan format nama: "{Nama Infrastruktur}_foto {urutan}.{ext}"
     */
    public function downloadPhoto(SurveyPhoto $photo)
    {
        $path = storage_path('app/public/' . $photo->file_path);

        if (!file_exists($path)) {
            return back()->withErrors(['file' => 'File foto tidak ditemukan di server.']);
        }

        $infrastructure = $photo->infrastructure;
        $namaInfra = $this->sanitizeFileName($infrastructure->nama_objek ?? 'Infrastruktur');

        $allPhotoIds = $infrastructure
            ? $infrastructure->photos()->orderBy('id')->pluck('id')->toArray()
            : [$photo->id];

        $urutan = array_search($photo->id, $allPhotoIds);
        $nomorFoto = ($urutan !== false) ? ($urutan + 1) : 1;

        $ext = pathinfo($path, PATHINFO_EXTENSION) ?: 'jpg';
        $downloadName = "{$namaInfra}_foto {$nomorFoto}.{$ext}";

        return response()->download($path, $downloadName);
    }

    /**
     * Unduh semua foto (maks 5 foto) milik 1 infrastruktur sekaligus dalam file .ZIP
     */
    public function downloadAllPhotosZip(Infrastructure $infrastructure)
    {
        $photos = $infrastructure->photos()->orderBy('id')->get();

        if ($photos->isEmpty()) {
            return back()->withErrors(['file' => 'Belum ada foto dokumentasi pada titik ini untuk diunduh.']);
        }

        $namaInfra = $this->sanitizeFileName($infrastructure->nama_objek ?? 'Infrastruktur');
        $zipFileName = "{$namaInfra}_Dokumentasi_Foto.zip";
        $tempDir = storage_path('app/temp');

        if (!is_dir($tempDir)) {
            mkdir($tempDir, 0755, true);
        }

        $zipFullPath = $tempDir . DIRECTORY_SEPARATOR . Str::slug($namaInfra, '_') . '_' . time() . '.zip';
        $zip = new ZipArchive();

        if ($zip->open($zipFullPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            return back()->withErrors(['file' => 'Gagal membuat arsip ZIP di server.']);
        }

        $addedCount = 0;
        foreach ($photos as $index => $photo) {
            $realFilePath = storage_path('app/public/' . $photo->file_path);
            if (file_exists($realFilePath)) {
                $ext = pathinfo($realFilePath, PATHINFO_EXTENSION) ?: 'jpg';
                $nomorFoto = $index + 1;
                $entryName = "{$namaInfra}_foto {$nomorFoto}.{$ext}";
                $zip->addFile($realFilePath, $entryName);
                $addedCount++;
            }
        }

        $zip->close();

        if ($addedCount === 0) {
            @unlink($zipFullPath);
            return back()->withErrors(['file' => 'File fisik foto tidak ditemukan di penyimpanan.']);
        }

        return response()->download($zipFullPath, $zipFileName)->deleteFileAfterSend(true);
    }
}