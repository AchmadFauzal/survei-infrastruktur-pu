<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Infrastructure;
use App\Models\User;
use Illuminate\Http\Request;

class InfrastructureManagementController extends Controller
{
    public function index(Request $request)
    {
        $query = Infrastructure::with(['assignedSurveyor', 'surveyor']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_infrastruktur', 'like', "%{$search}%")
                  ->orWhere('kota_kabupaten', 'like', "%{$search}%")
                  ->orWhere('provinsi', 'like', "%{$search}%")
                  ->orWhere('jenis_infrastruktur', 'like', "%{$search}%");
            });
        }

        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }

        if ($request->filled('status_survei')) {
            $query->where('status_survey', $request->status_survei === 'sudah');
        }

        if ($request->filled('assigned_to')) {
            if ($request->assigned_to === 'unassigned') {
                $query->whereNull('assigned_to');
            } else {
                $query->where('assigned_to', $request->assigned_to);
            }
        }

        $infrastructures = $query->latest()->paginate(20)->withQueryString();
        $surveyors = User::where('role', 'surveyor')->orderBy('name')->get();

        return view('admin.infrastructures.index', compact('infrastructures', 'surveyors'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_infrastruktur'          => 'required|string|max:255',
            'kategori'            => 'required|in:BM,CK,SDA,PS,SETJEN',
            'jenis_infrastruktur' => 'nullable|string|max:255',
            'kota_kabupaten'      => 'required|string|max:255',
            'provinsi'            => 'required|string|max:255',
            'latitude'            => 'required|numeric|between:-90,90',
            'longitude'           => 'required|numeric|between:-180,180',
            'assigned_to'         => 'nullable|exists:users,id',
        ]);

        Infrastructure::create([
            'kode_infrastruktur'  => 'ADM-' . time(),
            'nama_infrastruktur'          => $request->nama_infrastruktur,
            'kategori'            => $request->kategori,
            'jenis_infrastruktur' => $request->jenis_infrastruktur,
            'kota_kabupaten'      => $request->kota_kabupaten,
            'provinsi'            => $request->provinsi,
            'latitude'            => $request->latitude,
            'longitude'           => $request->longitude,
            'assigned_to'         => $request->assigned_to,
            'status_survey'       => false,
        ]);

        return back()->with('success', 'Data infrastruktur berhasil ditambahkan.');
    }

    public function update(Request $request, Infrastructure $infrastructure)
    {
        $request->validate([
            'nama_infrastruktur'          => 'required|string|max:255',
            'kategori'            => 'required|in:BM,CK,SDA,PS,SETJEN',
            'jenis_infrastruktur' => 'nullable|string|max:255',
            'kota_kabupaten'      => 'required|string|max:255',
            'provinsi'            => 'required|string|max:255',
            'latitude'            => 'required|numeric|between:-90,90',
            'longitude'           => 'required|numeric|between:-180,180',
            'status_survei'       => 'required|in:belum,sudah',
            'assigned_to'         => 'nullable|exists:users,id',
        ]);

        $infrastructure->update([
            'nama_infrastruktur'          => $request->nama_infrastruktur,
            'kategori'            => $request->kategori,
            'jenis_infrastruktur' => $request->jenis_infrastruktur,
            'kota_kabupaten'      => $request->kota_kabupaten,
            'provinsi'            => $request->provinsi,
            'latitude'            => $request->latitude,
            'longitude'           => $request->longitude,
            'status_survey'       => $request->status_survei === 'sudah',
            'assigned_to'         => $request->assigned_to,
        ]);

        return back()->with('success', "Data '{$infrastructure->nama_infrastruktur}' berhasil diperbarui.");
    }

    public function destroy(Infrastructure $infrastructure)
    {
        $nama = $infrastructure->nama_infrastruktur;
        $infrastructure->delete();

        return back()->with('success', "Data '{$nama}' berhasil dihapus.");
    }

    /**
     * Hapus massal berdasarkan checklist yang dipilih Admin
     */
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids'   => 'required|array|min:1',
            'ids.*' => 'exists:infrastructures,id',
        ]);

        $count = Infrastructure::whereIn('id', $request->ids)->delete();

        return back()->with('success', "{$count} data infrastruktur terpilih berhasil dihapus.");
    }

    /**
     * Tugaskan surveyor secara massal ke titik infrastruktur yang dicentang
     */
    public function bulkAssign(Request $request)
    {
        $request->validate([
            'ids'         => 'required|array|min:1',
            'ids.*'       => 'exists:infrastructures,id',
            'assigned_to' => 'nullable|exists:users,id',
        ]);

        $count = Infrastructure::whereIn('id', $request->ids)->update([
            'assigned_to' => $request->assigned_to ?: null,
        ]);

        $msg = $request->assigned_to
            ? "{$count} titik infrastruktur berhasil ditugaskan ke surveyor."
            : "Penugasan surveyor untuk {$count} titik berhasil dibatalkan.";

        return back()->with('success', $msg);
    }
}