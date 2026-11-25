<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Infrastructure;
use App\Models\User;
use Illuminate\Http\Request;

class AssignmentController extends Controller
{
    public function index(Request $request)
    {
        // Menampilkan daftar titik yang sudah ditugaskan (atau difilter)
        $query = Infrastructure::with(['assignedSurveyor', 'surveyor'])
            ->whereNotNull('assigned_to');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_objek', 'ilike', "%{$search}%")
                  ->orWhere('kota', 'ilike', "%{$search}%")
                  ->orWhere('provinsi', 'ilike', "%{$search}%");
            });
        }

        if ($request->filled('surveyor_id')) {
            $query->where('assigned_to', $request->surveyor_id);
        }

        if ($request->filled('status_survei')) {
            $query->where('status_survei', $request->status_survei);
        }

        $assignments = $query->latest('updated_at')->paginate(20)->withQueryString();

        // Daftar titik yang belum ditugaskan untuk pilihan pada Modal Tambah Penugasan
        $unassignedInfrastructures = Infrastructure::whereNull('assigned_to')
            ->orderBy('provinsi')
            ->orderBy('nama_objek')
            ->get();

        $surveyors = User::where('role', 'surveyor')->orderBy('name')->get();

        // Statistik ringkas penugasan
        $stats = [
            'total_tugas'   => Infrastructure::whereNotNull('assigned_to')->count(),
            'belum_selesai' => Infrastructure::whereNotNull('assigned_to')->where('status_survei', 'belum')->count(),
            'sudah_selesai' => Infrastructure::whereNotNull('assigned_to')->where('status_survei', 'sudah')->count(),
            'belum_dibagi'  => Infrastructure::whereNull('assigned_to')->count(),
        ];

        return view('admin.assignments.index', compact(
            'assignments',
            'unassignedInfrastructures',
            'surveyors',
            'stats'
        ));
    }

    /**
     * Tambah penugasan baru (bisa memilih beberapa titik sekaligus untuk 1 surveyor)
     */
    public function store(Request $request)
    {
        $request->validate([
            'assigned_to'          => 'required|exists:users,id',
            'infrastructure_ids'   => 'required|array|min:1',
            'infrastructure_ids.*' => 'exists:infrastructures,id',
        ]);

        $count = Infrastructure::whereIn('id', $request->infrastructure_ids)->update([
            'assigned_to' => $request->assigned_to,
        ]);

        return back()->with('success', "Berhasil menugaskan {$count} titik infrastruktur kepada surveyor.");
    }

    /**
     * Edit penugasan (ganti surveyor, status survei, atau data titik)
     */
    public function update(Request $request, Infrastructure $infrastructure)
    {
        $request->validate([
            'assigned_to'         => 'nullable|exists:users,id',
            'status_survei'       => 'required|in:belum,sudah',
            'nama_objek'          => 'required|string|max:255',
            'kota'                => 'required|string|max:255',
            'provinsi'            => 'required|string|max:255',
        ]);

        $infrastructure->update([
            'assigned_to'   => $request->assigned_to ?: null,
            'status_survei' => $request->status_survei,
            'nama_objek'    => $request->nama_objek,
            'kota'          => $request->kota,
            'provinsi'      => $request->provinsi,
        ]);

        return back()->with('success', "Penugasan untuk '{$infrastructure->nama_objek}' berhasil diperbarui.");
    }

    /**
     * Batalkan / hapus penugasan satuan (mengembalikan titik ke status belum ditugaskan)
     */
    public function destroy(Infrastructure $infrastructure)
    {
        $nama = $infrastructure->nama_objek;
        $infrastructure->update(['assigned_to' => null]);

        return back()->with('success', "Penugasan untuk '{$nama}' berhasil dibatalkan.");
    }

    /**
     * Batalkan / hapus penugasan massal via checklist
     */
    public function bulkUnassign(Request $request)
    {
        $request->validate([
            'ids'   => 'required|array|min:1',
            'ids.*' => 'exists:infrastructures,id',
        ]);

        $count = Infrastructure::whereIn('id', $request->ids)->update(['assigned_to' => null]);

        return back()->with('success', "{$count} penugasan terpilih berhasil dibatalkan.");
    }
}