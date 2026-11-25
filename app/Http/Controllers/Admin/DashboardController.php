<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Infrastructure;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $query = Infrastructure::with(['photos', 'surveyor']);

        // Filter Kata Kunci
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_infrastruktur', 'like', "%{$search}%")
                  ->orWhere('jenis_infrastruktur', 'like', "%{$search}%")
                  ->orWhere('nama_hasil_survey', 'like', "%{$search}%")
                  ->orWhere('alasan_verifikasi', 'like', "%{$search}%")
                  ->orWhere('kota_kabupaten', 'like', "%{$search}%")
                  ->orWhere('provinsi', 'like', "%{$search}%");
            });
        }

        // Filter Kategori (BM, CK, SDA, PS)
        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }

        // Filter Status Survei
        if ($request->filled('status_survei')) {
            $query->where('status_survey', $request->status_survei === 'sudah');
        }

        // Filter Provinsi & Kota
        if ($request->filled('provinsi')) {
            $query->where('provinsi', $request->provinsi);
        }
        if ($request->filled('kota_kabupaten')) {
            $query->where('kota_kabupaten', $request->kota_kabupaten);
        }

        // Filter Petugas Surveyor
        if ($request->filled('surveyed_by')) {
            $query->where('surveyed_by', $request->surveyed_by);
        }

        $infrastructures = $query->orderBy('status_survey', 'desc')
                                 ->latest('surveyed_at')
                                 ->paginate(15)
                                 ->withQueryString();

        // Statistik Utama + Rincian per Kategori (BM, CK, SDA, PS)
        $stats = [
            'total'   => Infrastructure::count(),
            'sudah'   => Infrastructure::where('status_survey', true)->count(),
            'belum'   => Infrastructure::where('status_survey', false)->count(),
            'per_kategori' => [
                'BM'  => [
                    'total' => Infrastructure::where('kategori', 'BM')->count(),
                    'sudah' => Infrastructure::where('kategori', 'BM')->where('status_survey', true)->count(),
                ],
                'CK'  => [
                    'total' => Infrastructure::where('kategori', 'CK')->count(),
                    'sudah' => Infrastructure::where('kategori', 'CK')->where('status_survey', true)->count(),
                ],
                'SDA' => [
                    'total' => Infrastructure::where('kategori', 'SDA')->count(),
                    'sudah' => Infrastructure::where('kategori', 'SDA')->where('status_survey', true)->count(),
                ],
                'PS'  => [
                    'total' => Infrastructure::where('kategori', 'PS')->count(),
                    'sudah' => Infrastructure::where('kategori', 'PS')->where('status_survey', true)->count(),
                ],
            ]
        ];

        $daftarProvinsi = Infrastructure::select('provinsi')->distinct()->orderBy('provinsi')->pluck('provinsi');
        $daftarKota     = Infrastructure::select('kota_kabupaten')->distinct()->orderBy('kota_kabupaten')->pluck('kota_kabupaten');
        $daftarSurveyor = User::orderBy('name')->get();

        return view('admin.dashboard', compact(
            'infrastructures', 'stats', 'daftarProvinsi', 'daftarKota', 'daftarSurveyor'
        ));
    }
}