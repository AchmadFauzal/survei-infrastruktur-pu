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
            ->where('status_survey', true);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_infrastruktur', 'like', "%{$search}%")
                  ->orWhere('kota_kabupaten', 'like', "%{$search}%")
                  ->orWhere('jenis_infrastruktur', 'like', "%{$search}%");
            });
        }

        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }

        $mySurveys = $query->latest('surveyed_at')->get();

        $stats = [
            'tugas_aktif'  => Infrastructure::where('assigned_to', $userId)->where('status_survey', false)->count(),
            'saya_survei'  => Infrastructure::where('surveyed_by', $userId)->where('status_survey', true)->count(),
            'total_foto'   => SurveyPhoto::whereHas('infrastructure', fn($q) => $q->where('surveyed_by', $userId))->count(),
            'sisa_wilayah' => Infrastructure::where('status_survey', false)->count(),
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
            ->where('status_survey', false)
            ->latest()
            ->get();

        $allUnsurveyed = Infrastructure::where('status_survey', false)
            ->orderBy('provinsi')
            ->orderBy('kota_kabupaten')
            ->orderBy('nama_infrastruktur')
            ->get();

        $stats = [
            'tugas_aktif'  => $assignedTasks->count(),
            'saya_survei'  => Infrastructure::where('surveyed_by', $userId)->where('status_survey', true)->count(),
            'sisa_wilayah' => $allUnsurveyed->count(),
        ];

        return view('surveyor.assignments', compact('assignedTasks', 'allUnsurveyed', 'stats'));
    }
}