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
                $q->where('nama_objek', 'ilike', "%{$search}%")
                  ->orWhere('jenis_infrastruktur', 'ilike', "%{$search}%")
                  ->orWhere('nama_hasil_survei', 'ilike', "%{$search}%")
                  ->orWhere('alasan_verifikasi', 'ilike', "%{$search}%")
                  ->orWhere('kota', 'ilike', "%{$search}%")
                  ->orWhere('provinsi', 'ilike', "%{$search}%");
            });
        }

        // Filter Kategori (BM, CK, SDA, PS)
        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }

        // Filter Status Survei
        if ($request->filled('status_survei')) {
            $query->where('status_survei', $request->status_survei);
        }

        // Filter Provinsi & Kota
        if ($request->filled('provinsi')) {
            $query->where('provinsi', $request->provinsi);
        }
        if ($request->filled('kota')) {
            $query->where('kota', $request->kota);
        }

        // Filter Petugas Surveyor
        if ($request->filled('surveyed_by')) {
            $query->where('surveyed_by', $request->surveyed_by);
        }

        $infrastructures = $query->orderByRaw("CASE WHEN status_survei = 'sudah' THEN 0 ELSE 1 END")
                                 ->latest('surveyed_at')
                                 ->paginate(15)
                                 ->withQueryString();

        // Statistik Utama + Rincian per Kategori (BM, CK, SDA, PS)
        $stats = [
            'total'   => Infrastructure::count(),
            'sudah'   => Infrastructure::where('status_survei', 'sudah')->count(),
            'belum'   => Infrastructure::where('status_survei', 'belum')->count(),
            'per_kategori' => [
                'BM'  => [
                    'total' => Infrastructure::where('kategori', 'BM')->count(),
                    'sudah' => Infrastructure::where('kategori', 'BM')->where('status_survei', 'sudah')->count(),
                ],
                'CK'  => [
                    'total' => Infrastructure::where('kategori', 'CK')->count(),
                    'sudah' => Infrastructure::where('kategori', 'CK')->where('status_survei', 'sudah')->count(),
                ],
                'SDA' => [
                    'total' => Infrastructure::where('kategori', 'SDA')->count(),
                    'sudah' => Infrastructure::where('kategori', 'SDA')->where('status_survei', 'sudah')->count(),
                ],
                'PS'  => [
                    'total' => Infrastructure::where('kategori', 'PS')->count(),
                    'sudah' => Infrastructure::where('kategori', 'PS')->where('status_survei', 'sudah')->count(),
                ],
            ]
        ];

        $daftarProvinsi = Infrastructure::select('provinsi')->distinct()->orderBy('provinsi')->pluck('provinsi');
        $daftarKota     = Infrastructure::select('kota')->distinct()->orderBy('kota')->pluck('kota');
        $daftarSurveyor = User::orderBy('name')->get();

        return view('admin.dashboard', compact(
            'infrastructures', 'stats', 'daftarProvinsi', 'daftarKota', 'daftarSurveyor'
        ));
    }
}