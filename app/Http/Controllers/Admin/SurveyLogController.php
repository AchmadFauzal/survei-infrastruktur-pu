<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SurveyLog;
use App\Models\SurveyLogItem;
use App\Models\User;
use Illuminate\Http\Request;

class SurveyLogController extends Controller
{
    public function index(Request $request)
    {
        $query = SurveyLog::with(['user', 'items.infrastructure']);

        // Filter berdasarkan surveyor
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Cari nama infrastruktur di dalam item log
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('items.infrastructure', function ($q) use ($search) {
                $q->where('nama_infrastruktur', 'ilike', "%{$search}%")
                  ->orWhere('kota_kabupaten', 'ilike', "%{$search}%");
            });
        }

        // Urutkan log terbaru di paling atas
        $logs = $query->latest()->paginate(15)->withQueryString();

        $surveyors = User::where('role', 'surveyor')->orderBy('name')->get();

        $stats = [
            'total_laporan' => SurveyLog::count(),
            'total_infra'   => SurveyLogItem::count(),
            'hari_ini'      => SurveyLog::whereDate('created_at', today())->count(),
        ];

        return view('admin.logs.index', compact('logs', 'surveyors', 'stats'));
    }
}