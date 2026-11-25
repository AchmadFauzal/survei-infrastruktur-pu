<?php

namespace App\Http\Controllers;

use App\Models\Infrastructure;
use Illuminate\Http\Request;

class MapController extends Controller
{
    public function index()
    {
        $infrastructures = Infrastructure::with(['photos', 'surveyor'])->get();
        return view('landing', compact('infrastructures'));
    }

    /**
     * Tambah 1 titik infrastruktur baru secara manual dari klik peta
     */
    public function storePoint(Request $request)
    {
        $request->validate([
            'nama_infrastruktur'          => 'required|string|max:255',
            'kategori'            => 'required|in:BM,CK,SDA,PS,SETJEN',
            'jenis_infrastruktur' => 'nullable|string|max:255',
            'kota_kabupaten'      => 'required|string|max:255',
            'provinsi'            => 'required|string|max:255',
            'latitude'            => 'required|numeric|between:-90,90',
            'longitude'           => 'required|numeric|between:-180,180',
        ]);

        Infrastructure::create([
            'kode_infrastruktur'  => 'MANUAL-' . time(),
            'nama_infrastruktur'          => $request->nama_infrastruktur,
            'kategori'            => $request->kategori,
            'jenis_infrastruktur' => $request->jenis_infrastruktur,
            'kota_kabupaten'      => $request->kota_kabupaten,
            'provinsi'            => $request->provinsi,
            'latitude'            => $request->latitude,
            'longitude'           => $request->longitude,
            'status_survey'       => false,
        ]);

        return back()->with('success', 'Titik infrastruktur baru berhasil ditambahkan ke peta!');
    }

    /**
     * Import data infrastruktur massal dari file CSV atau JSON
     */
    public function importData(Request $request)
    {
        $request->validate([
            'file_import' => 'required|file|mimes:csv,txt,json|max:10240',
        ]);

        $file = $request->file('file_import');
        $ext  = strtolower($file->getClientOriginalExtension());
        $rows = [];

        if ($ext === 'json') {
            $decoded = json_decode(file_get_contents($file->getRealPath()), true);
            if (!is_array($decoded)) {
                return back()->withErrors(['file_import' => 'Format file JSON tidak valid.']);
            }
            $rows = $decoded;
        } else {
            // Baca file CSV (mendukung pemisah koma ',' maupun titik koma ';')
            $handle = fopen($file->getRealPath(), 'r');
            $firstLine = fgets($handle);
            $delimiter = (substr_count($firstLine, ';') > substr_count($firstLine, ',')) ? ';' : ',';
            rewind($handle);

            $headers = fgetcsv($handle, 0, $delimiter);
            if ($headers) {
                // Bersihkan BOM UTF-8 jika ada
                $headers[0] = preg_replace('/^\xEF\xBB\xBF/', '', $headers[0]);
                $headers = array_map(fn($h) => strtolower(trim($h)), $headers);

                while (($data = fgetcsv($handle, 0, $delimiter)) !== false) {
                    if (count($data) === count($headers)) {
                        $rows[] = array_combine($headers, $data);
                    }
                }
            }
            fclose($handle);
        }

        $insertedCount = 0;

        foreach ($rows as $row) {
            // Normalisasi key menjadi lowercase agar fleksibel membaca berbagai nama kolom CSV/JSON
            $item = array_change_key_case($row, CASE_LOWER);

            $nama = $item['nama_infrastruktur'] ?? $item['nama_infrastruktur'] ?? $item['nama'] ?? null;
            $lat  = $item['latitude'] ?? $item['lat'] ?? null;
            $lon  = $item['longitude'] ?? $item['lon'] ?? $item['lng'] ?? null;

            if (!$nama || !is_numeric($lat) || !is_numeric($lon)) {
                continue; // Lewati baris yang tidak memiliki nama atau koordinat valid
            }

            $rawKat = strtoupper(trim($item['kategori'] ?? $item['unor'] ?? 'CK'));
            $kategori = in_array($rawKat, ['BM', 'CK', 'SDA', 'PS', 'SETJEN']) ? $rawKat : 'CK';

            Infrastructure::create([
                'kode_infrastruktur'  => $item['kode_infrastruktur'] ?? $item['id'] ?? ('IMP-' . uniqid()),
                'nama_infrastruktur'          => trim($nama),
                'kategori'            => $kategori,
                'jenis_infrastruktur' => $item['jenis_infrastruktur'] ?? $item['jenis'] ?? null,
                'kota_kabupaten'      => trim($item['kota_kabupaten'] ?? $item['kota'] ?? '-'),
                'provinsi'            => trim($item['provinsi'] ?? '-'),
                'latitude'            => (float) $lat,
                'longitude'           => (float) $lon,
                'status_survey'       => $this->parseBoolean($item['status_survey'] ?? $item['status_survei'] ?? false),
                'status_lapor_big'    => $this->parseBoolean($item['status_lapor_big'] ?? false),
                'is_coordinates_close' => $this->parseNullableBoolean($item['is_coordinates_close'] ?? null),
                'yolo_detection'      => $this->parseNullableBoolean($item['yolo_detection'] ?? null),
                'best_score'          => is_numeric($item['best_score'] ?? null) ? (float) $item['best_score'] : null,
                'g_latitude'          => is_numeric($item['g_latitude'] ?? null) ? (float) $item['g_latitude'] : null,
                'g_longitude'         => is_numeric($item['g_longitude'] ?? null) ? (float) $item['g_longitude'] : null,
                'distance_diff_m'     => is_numeric($item['distance_diff_m'] ?? null) ? (float) $item['distance_diff_m'] : null,
            ]);

            $insertedCount++;
        }

        if ($insertedCount === 0) {
            return back()->withErrors(['file_import' => 'Tidak ada baris valid yang dapat diimpor. Pastikan file memiliki kolom nama_infrastruktur (atau nama), latitude, dan longitude.']);
        }

        return back()->with('success', "Berhasil mengimpor {$insertedCount} titik infrastruktur ke dalam peta!");
    }

    private function parseBoolean(mixed $value): bool
    {
        if (is_bool($value)) {
            return $value;
        }

        $normalized = strtolower(trim((string) $value));
        return in_array($normalized, ['1', 'true', 'ya', 'y', 'yes', 'sudah'], true);
    }

    private function parseNullableBoolean(mixed $value): ?bool
    {
        if ($value === null || $value === '') {
            return null;
        }

        return $this->parseBoolean($value);
    }
}