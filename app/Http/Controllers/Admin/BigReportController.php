<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Infrastructure;
use Illuminate\Http\Request;

class BigReportController extends Controller
{
    public function index(Request $request)
    {
        $query = Infrastructure::query();

        // Filter Pencarian (Nama Infrastruktur, Kode, atau Kota/Kabupaten)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_infrastruktur', 'ilike', "%{$search}%")
                  ->orWhere('kode_infrastruktur', 'ilike', "%{$search}%")
                  ->orWhere('kota_kabupaten', 'ilike', "%{$search}%");
            });
        }

        // Filter Status Lapor BIG ('sudah' / 'belum')
        if ($request->filled('status_big')) {
            if ($request->status_big === 'sudah') {
                $query->where('status_lapor_big', true);
            } elseif ($request->status_big === 'belum') {
                $query->where('status_lapor_big', false);
            }
        }

        // Filter Status Survei Lapangan ('sudah' / 'belum')
        if ($request->filled('status_survey')) {
            $query->where('status_survey', $request->status_survey === 'sudah');
        }

        // Filter Kategori (BM, CK, PS, SDA, SETJEN)
        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }

        $infrastructures = $query->orderByDesc('updated_at')->paginate(20)->withQueryString();

        // Statistik Ringkas untuk Kartu Header
        $stats = [
            'total'      => Infrastructure::count(),
            'sudah_big'  => Infrastructure::where('status_lapor_big', true)->count(),
            'belum_big'  => Infrastructure::where('status_lapor_big', false)->count(),
            'siap_lapor' => Infrastructure::where('status_survey', true)
                                          ->where('status_lapor_big', false)
                                          ->count(),
        ];

        return view('admin.big.index', compact('infrastructures', 'stats'));
    }

    /**
     * Toggle satu data infrastruktur (AJAX Switch)
     */
    public function toggle(Request $request, Infrastructure $infrastructure)
    {
        $newStatus = $request->has('status_lapor_big')
            ? filter_var($request->status_lapor_big, FILTER_VALIDATE_BOOLEAN)
            : !$infrastructure->status_lapor_big;

        $infrastructure->update([
            'status_lapor_big' => $newStatus,
        ]);

        return response()->json([
            'success'          => true,
            'id'               => $infrastructure->id,
            'status_lapor_big' => (bool) $infrastructure->status_lapor_big,
            'message'          => $newStatus
                ? "Status '{$infrastructure->nama_infrastruktur}' diubah menjadi Sudah Lapor BIG."
                : "Status '{$infrastructure->nama_infrastruktur}' dikembalikan ke Belum Lapor.",
            'stats'            => [
                'sudah_big'  => Infrastructure::where('status_lapor_big', true)->count(),
                'belum_big'  => Infrastructure::where('status_lapor_big', false)->count(),
                'siap_lapor' => Infrastructure::where('status_survey', true)
                                              ->where('status_lapor_big', false)
                                              ->count(),
            ],
        ]);
    }

    /**
     * Update massal (tandai beberapa data sekaligus)
     */
    public function bulkUpdate(Request $request)
    {
        $request->validate([
            'ids'    => 'required|array',
            'ids.*'  => 'exists:infrastructures,id',
            'action' => 'required|in:mark_reported,mark_unreported',
        ]);

        $status = $request->action === 'mark_reported';

        Infrastructure::whereIn('id', $request->ids)->update([
            'status_lapor_big' => $status,
        ]);

        return redirect()
            ->route('admin.big.index')
            ->with('success', count($request->ids) . ' data infrastruktur berhasil diperbarui status pelaporan BIG-nya.');
    }
}