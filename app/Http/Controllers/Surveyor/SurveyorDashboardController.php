<?php

namespace App\Http\Controllers\Surveyor;

use App\Http\Controllers\Controller;
use App\Models\Infrastructure;
use App\Models\SurveyLog;
use App\Models\SurveyLogItem;
use App\Models\SurveyPhoto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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

        // Tugas aktif yang belum disurvei
        $assignedTasks = Infrastructure::where('assigned_to', $userId)
            ->where('status_survey', false)
            ->latest()
            ->get();

        // Tugas yang sudah selesai disurvei, namun belum dikirim ke Log Admin
        $readyToReportTasks = Infrastructure::where('assigned_to', $userId)
            ->where('status_survey', true)
            ->whereNotIn('id', function ($q) use ($userId) {
                $q->select('infrastructure_id')
                  ->from('survey_log_items')
                  ->join('survey_logs', 'survey_logs.id', '=', 'survey_log_items.survey_log_id')
                  ->where('survey_logs.user_id', $userId);
            })
            ->latest('surveyed_at')
            ->get();

        $allUnsurveyed = Infrastructure::where('status_survey', false)
            ->orderBy('provinsi')
            ->orderBy('kota_kabupaten')
            ->orderBy('nama_infrastruktur')
            ->get();

        $stats = [
            'tugas_aktif'  => $assignedTasks->count(),
            'siap_dilapor' => $readyToReportTasks->count(),
            'saya_survei'  => Infrastructure::where('surveyed_by', $userId)->where('status_survey', true)->count(),
            'sisa_wilayah' => $allUnsurveyed->count(),
        ];

        return view('surveyor.assignments', compact('assignedTasks', 'readyToReportTasks', 'allUnsurveyed', 'stats'));
    }

    /**
     * Aksi: Kirim Laporan Tugas Selesai ke Log Admin (Bisa Bertahap / Parsial)
     */
    public function submitBatchCompletion(Request $request)
    {
        $userId = Auth::id();

        // Ambil tugas milik surveyor ini yang sudah disurvei (status_survey = true)
        // namun BELUM pernah dilaporkan ke dalam SurveyLog sebelumnya
        $completedTasks = Infrastructure::where('assigned_to', $userId)
            ->where('status_survey', true)
            ->whereNotIn('id', function ($q) use ($userId) {
                $q->select('infrastructure_id')
                  ->from('survey_log_items')
                  ->join('survey_logs', 'survey_logs.id', '=', 'survey_log_items.survey_log_id')
                  ->where('survey_logs.user_id', $userId);
            })
            ->get();

        if ($completedTasks->isEmpty()) {
            return back()->with('error', 'Belum ada tugas survei baru yang selesai untuk dilaporkan.');
        }

        // Cek sisa tugas yang masih belum disurvei (hanya untuk informasi pesan notifikasi)
        $remainingTasksCount = Infrastructure::where('assigned_to', $userId)
            ->where('status_survey', false)
            ->count();

        DB::transaction(function () use ($userId, $completedTasks, $request) {
            // 1. Buat 1 Header Log Utama untuk batch yang dilaporkan
            $log = SurveyLog::create([
                'user_id'               => $userId,
                'total_infrastructures' => $completedTasks->count(),
                'catatan'               => $request->catatan,
            ]);

            // 2. Masukkan titik-titik yang sudah selesai tersebut ke dalam detail log
            foreach ($completedTasks as $infra) {
                SurveyLogItem::create([
                    'survey_log_id'     => $log->id,
                    'infrastructure_id' => $infra->id,
                    'survey_latitude'   => $infra->survey_latitude ?? $infra->latitude,
                    'survey_longitude'  => $infra->survey_longitude ?? $infra->longitude,
                ]);
            }
        });

        $pesanSisa = $remainingTasksCount > 0
            ? " (Sisa {$remainingTasksCount} tugas lagi dapat Anda laporkan setelah selesai)."
            : " Seluruh paket penugasan Anda telah tuntas!";

        return back()->with('success', "Berhasil! Laporan penyelesaian {$completedTasks->count()} infrastruktur telah masuk ke Log Admin.{$pesanSisa}");
    }
}