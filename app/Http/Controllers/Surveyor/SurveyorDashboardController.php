<?php

namespace App\Http\Controllers\Surveyor;

use App\Http\Controllers\Controller;
use App\Models\Infrastructure;
use App\Models\SurveyPhoto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SurveyorDashboardController extends Controller
{
    /**
     * Halaman 1: Riwayat & Pelaporan Hasil Survei (Berita Acara)
     */
    public function index(Request $request)
    {
        $userId = Auth::id();

        $query = Infrastructure::with('photos')
            ->where('surveyed_by', $userId)
            ->where('status_survei', 'sudah');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_objek', 'ilike', "%{$search}%")
                  ->orWhere('kota', 'ilike', "%{$search}%")
                  ->orWhere('jenis_infrastruktur', 'ilike', "%{$search}%");
            });
        }

        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }

        $mySurveys = $query->latest('surveyed_at')->get();

        $stats = [
            'tugas_aktif'  => Infrastructure::where('assigned_to', $userId)->where('status_survei', 'belum')->count(),
            'saya_survei'  => Infrastructure::where('surveyed_by', $userId)->where('status_survei', 'sudah')->count(),
            'total_foto'   => SurveyPhoto::whereHas('infrastructure', fn($q) => $q->where('surveyed_by', $userId))->count(),
            'sisa_wilayah' => Infrastructure::where('status_survei', 'belum')->count(),
        ];

        return view('surveyor.dashboard', compact('mySurveys', 'stats'));
    }

    /**
     * Halaman 2: Penugasan & Perencana Rute Lapangan
     */
    public function assignments()
    {
        $userId = Auth::id();

        $assignedTasks = Infrastructure::where('assigned_to', $userId)
            ->where('status_survei', 'belum')
            ->latest()
            ->get();

        $allUnsurveyed = Infrastructure::where('status_survei', 'belum')
            ->orderBy('provinsi')
            ->orderBy('kota')
            ->orderBy('nama_objek')
            ->get();

        $stats = [
            'tugas_aktif'  => $assignedTasks->count(),
            'saya_survei'  => Infrastructure::where('surveyed_by', $userId)->where('status_survei', 'sudah')->count(),
            'sisa_wilayah' => $allUnsurveyed->count(),
        ];

        return view('surveyor.assignments', compact('assignedTasks', 'allUnsurveyed', 'stats'));
    }
}