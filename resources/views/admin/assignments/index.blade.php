@extends('admin.layout')

@section('title', 'List Penugasan Surveyor')

@section('header_actions')
    <button type="button" onclick="openCreateAssignmentModal()"
            class="group bg-pu-gold hover:bg-amber-400 active:scale-[0.98] text-pu-navy text-xs font-extrabold px-3.5 py-2 rounded-xl shadow-xs hover:shadow-md transition-all duration-200 inline-flex items-center gap-1.5 whitespace-nowrap cursor-pointer">
        <svg class="w-4 h-4 transition-transform duration-200 group-hover:rotate-90" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
        </svg>
        <span>Tambah Penugasan</span>
    </button>
@endsection

@section('content')
    <!-- ================= 1. KARTU RINGKASAN PENUGASAN ================= -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Total Titik Ditugaskan -->
        <div class="pu-card bg-white p-5 rounded-2xl shadow-xs border border-slate-200 border-b-4 border-b-pu-navy">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Total Ditugaskan</p>
                    <p class="text-3xl font-extrabold text-pu-navy mt-1">{{ number_format($stats['total_tugas']) }}</p>
                    <p class="text-[11px] text-slate-500 mt-1">Titik memiliki petugas aktif</p>
                </div>
                <div class="w-10 h-10 rounded-xl bg-pu-navy/10 text-pu-navy flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Sedang Dikerjakan (Belum) -->
        <div class="pu-card bg-white p-5 rounded-2xl shadow-xs border border-slate-200 border-b-4 border-b-amber-500">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <p class="text-[11px] font-bold text-amber-600 uppercase tracking-wider">Dalam Proses Survei</p>
                    <p class="text-3xl font-extrabold text-amber-600 mt-1">{{ number_format($stats['belum_selesai']) }}</p>
                    <p class="text-[11px] text-slate-500 mt-1">Menunggu laporan lapangan</p>
                </div>
                <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 border border-amber-100 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="9"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 7v5l3 3"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Tugas Selesai Survei -->
        <div class="pu-card bg-white p-5 rounded-2xl shadow-xs border border-slate-200 border-b-4 border-b-emerald-500">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <p class="text-[11px] font-bold text-emerald-600 uppercase tracking-wider">Selesai Disurvei</p>
                    <p class="text-3xl font-extrabold text-emerald-700 mt-1">{{ number_format($stats['sudah_selesai']) }}</p>
                    <p class="text-[11px] text-slate-500 mt-1">Tugas lapangan telah tuntas</p>
                </div>
                <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 border border-emerald-100 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.4" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Titik Belum Dibagi -->
        <div class="pu-card bg-white p-5 rounded-2xl shadow-xs border border-slate-200 border-b-4 border-b-slate-400">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <p class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Belum Dibagi</p>
                    <p class="text-3xl font-extrabold text-slate-700 mt-1">{{ number_format($stats['belum_dibagi']) }}</p>
                    <p class="text-[11px] text-slate-500 mt-1">Titik tersedia untuk penugasan</p>
                </div>
                <div class="w-10 h-10 rounded-xl bg-slate-100 text-slate-600 border border-slate-200 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 21s-6-5.686-6-10a6 6 0 1112 0c0 4.314-6 10-6 10z"/>
                        <circle cx="12" cy="11" r="2.5"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- ================= 2. FILTER LIST PENUGASAN ================= -->
    <div class="bg-white p-5 rounded-2xl shadow-xs border border-slate-200">
        <div class="flex flex-wrap items-center justify-between gap-2 mb-3.5 pb-2.5 border-b border-slate-100">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-pu-navy" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                </svg>
                <h2 class="text-xs font-extrabold uppercase tracking-wider text-pu-navy">Filter Penugasan Surveyor</h2>
            </div>
            <p class="text-[11px] text-slate-400 hidden sm:block">Kelola pembagian tugas lapangan per petugas atau wilayah</p>
        </div>

        <form method="GET" action="{{ route('admin.assignments.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3.5 text-xs">
            <div>
                <label class="block text-[11px] font-bold text-slate-600 mb-1">Kata Kunci Infrastruktur / Wilayah</label>
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Nama objek, kota, provinsi..."
                           class="w-full h-9 bg-slate-50 focus:bg-white border border-slate-300 focus:border-pu-navy rounded-xl pl-8 pr-3 text-xs outline-none transition">
                    <svg class="w-3.5 h-3.5 text-slate-400 absolute left-2.5 top-2.5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 104.5 4.5a7.5 7.5 0 0012.15 12.15z"/>
                    </svg>
                </div>
            </div>

            <div>
                <label class="block text-[11px] font-bold text-slate-600 mb-1">Petugas Surveyor</label>
                <select name="surveyor_id" class="w-full h-9 bg-slate-50 focus:bg-white border border-slate-300 focus:border-pu-navy rounded-xl px-3 text-xs outline-none transition">
                    <option value="">Semua Petugas Surveyor</option>
                    @foreach($surveyors as $srv)
                        <option value="{{ $srv->id }}" {{ request('surveyor_id') == $srv->id ? 'selected' : '' }}>
                            {{ $srv->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-[11px] font-bold text-slate-600 mb-1">Progres Tugas</label>
                <select name="status_survei" class="w-full h-9 bg-slate-50 focus:bg-white border border-slate-300 focus:border-pu-navy rounded-xl px-3 text-xs outline-none transition">
                    <option value="">Semua Progres Tugas</option>
                    <option value="belum" {{ request('status_survei') == 'belum' ? 'selected' : '' }}>Menunggu Survei (Belum)</option>
                    <option value="sudah" {{ request('status_survei') == 'sudah' ? 'selected' : '' }}>Selesai Disurvei (Sudah)</option>
                </select>
            </div>

            <div class="flex items-end justify-end gap-2">
                <a href="{{ route('admin.assignments.index') }}"
                   class="h-9 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold px-4 rounded-xl text-xs transition inline-flex items-center gap-1.5 whitespace-nowrap">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    <span>Reset</span>
                </a>
                <button type="submit"
                        class="h-9 bg-pu-navy hover:bg-pu-gold text-white hover:text-pu-navy font-bold px-5 rounded-xl text-xs shadow-xs transition inline-flex items-center gap-1.5 whitespace-nowrap cursor-pointer">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.4" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 104.5 4.5a7.5 7.5 0 0012.15 12.15z"/>
                    </svg>
                    <span>Terapkan</span>
                </button>
            </div>
        </form>
    </div>

    <!-- ================= 3. ACTION BAR BATALKAN TUGAS MASSAL ================= -->
    <div class="bg-pu-navy text-white px-4 py-3 rounded-2xl shadow-xs flex flex-wrap justify-between items-center gap-3 text-xs">
        <div class="flex items-center gap-2.5">
            <span id="selected-assign-badge" class="bg-pu-gold text-pu-navy font-extrabold px-2.5 py-1 rounded-lg text-[11px] tracking-wide">
                0 dipilih
            </span>
            <span class="text-sky-100/85">Centang baris tugas pada tabel untuk membatalkan penugasan secara massal.</span>
        </div>

        <form id="bulkUnassignForm" method="POST" action="{{ route('admin.assignments.bulk_unassign') }}">
            @csrf
            <div id="bulk-unassign-inputs"></div>
            <button type="button" onclick="submitBulkUnassign()"
                    class="group bg-red-600 hover:bg-red-500 active:scale-[0.98] text-white font-bold px-3.5 py-2 rounded-xl transition-all duration-200 inline-flex items-center gap-1.5 shadow-xs cursor-pointer">
                <svg class="w-3.5 h-3.5 transition-transform duration-200 group-hover:rotate-90" fill="none" stroke="currentColor" stroke-width="2.4" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
                <span>Batalkan Penugasan Terpilih</span>
            </button>
        </form>
    </div>

    <!-- ================= 4. TABEL LIST PENUGASAN ================= -->
    <div class="bg-white rounded-2xl shadow-xs border border-slate-200 overflow-hidden">
        <div class="px-5 py-3.5 border-b border-slate-100 flex flex-wrap items-center justify-between gap-2 bg-slate-50/70">
            <span class="text-xs font-extrabold text-pu-navy uppercase tracking-wider">Daftar Penugasan Aktif</span>
            <span class="text-[11px] font-semibold text-slate-500">
                Menampilkan {{ $assignments->firstItem() ?? 0 }}–{{ $assignments->lastItem() ?? 0 }} dari {{ $assignments->total() }} penugasan
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead class="bg-slate-50 border-b border-slate-200 text-[11px] font-bold uppercase tracking-wider text-slate-500">
                    <tr>
                        <th class="py-3.5 px-4 w-10 text-center">
                            <input type="checkbox" id="selectAllAssign" class="w-4 h-4 accent-[#002359] rounded cursor-pointer">
                        </th>
                        <th class="py-3.5 px-4 whitespace-nowrap">Petugas Surveyor</th>
                        <th class="py-3.5 px-4">Target Infrastruktur</th>
                        <th class="py-3.5 px-4">Wilayah & Koordinat</th>
                        <th class="py-3.5 px-4 whitespace-nowrap">Progres Survei</th>
                        <th class="py-3.5 px-4 text-right whitespace-nowrap">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($assignments as $item)
                        <tr class="hover:bg-sky-50/40 transition-colors">
                            <td class="py-3.5 px-4 text-center">
                                <input type="checkbox" value="{{ $item->id }}" class="assign-checkbox w-4 h-4 accent-[#002359] rounded cursor-pointer">
                            </td>
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                <div class="flex items-center gap-2.5">
                                    <span class="w-8 h-8 rounded-xl bg-pu-navy text-pu-gold font-extrabold flex items-center justify-center text-xs shrink-0 shadow-2xs">
                                        {{ strtoupper(substr($item->assignedSurveyor->name ?? '?', 0, 1)) }}
                                    </span>
                                    <div>
                                        <p class="font-bold text-slate-900 leading-tight">{{ $item->assignedSurveyor->name ?? '-' }}</p>
                                        <p class="text-[11px] text-slate-500 mt-0.5">{{ $item->assignedSurveyor->email ?? '' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3.5 px-4 min-w-[220px]">
                                <div class="flex items-center gap-1.5 mb-1">
                                    <span class="px-2 py-0.5 text-[10.5px] font-extrabold rounded-md bg-pu-navy/10 text-pu-navy border border-pu-navy/15">
                                        {{ $item->kategori }}
                                    </span>
                                    <span class="text-[11px] text-slate-400 truncate">{{ $item->jenis_infrastruktur ?? 'Jenis belum diisi' }}</span>
                                </div>
                                <p class="font-bold text-slate-900 text-xs sm:text-[13px] leading-snug">{{ $item->nama_infrastruktur }}</p>
                            </td>
                            <td class="py-3.5 px-4 min-w-[175px]">
                                <p class="font-semibold text-slate-800">{{ $item->kota_kabupaten }}, {{ $item->provinsi }}</p>
                                <p class="text-[11px] text-slate-400 mt-0.5">{{ $item->latitude }}, {{ $item->longitude }}</p>
                            </td>
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                @if($item->status_survei === 'sudah')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-[11px] font-bold rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                        Selesai Disurvei
                                    </span>
                                    @if($item->surveyed_at)
                                        <p class="text-[10.5px] text-slate-400 mt-1">
                                            {{ $item->surveyed_at->format('d M Y, H:i') }} WIB
                                        </p>
                                    @endif
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-[11px] font-bold rounded-full bg-amber-50 text-amber-800 border border-amber-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                        Menunggu Survei
                                    </span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                <div class="inline-flex items-center gap-1.5">
                                    <button type="button" onclick='openEditAssignmentModal(@json($item))'
                                            class="bg-pu-gold hover:bg-amber-400 text-pu-navy px-3 py-1.5 rounded-xl font-bold text-[11px] transition inline-flex items-center gap-1 cursor-pointer">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                        <span>Edit</span>
                                    </button>
                                    <form method="POST" action="{{ route('admin.assignments.destroy', $item) }}" class="inline"
                                          onsubmit="return confirm('Batalkan penugasan untuk {{ $item->nama_infrastruktur }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="bg-red-50 hover:bg-red-600 text-red-600 hover:text-white border border-red-200 hover:border-red-600 px-3 py-1.5 rounded-xl font-bold text-[11px] transition inline-flex items-center gap-1 cursor-pointer">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                            <span>Hapus</span>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-slate-400">
                                <p class="font-semibold text-slate-600">Belum ada penugasan surveyor yang aktif.</p>
                                <p class="text-[11px] mt-1">Klik tombol <strong class="text-pu-navy">Tambah Penugasan</strong> di kanan atas untuk mulai membagi tugas.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination Kustom Kompak -->
        @if($assignments->hasPages())
            <div class="px-5 py-3 border-t border-slate-100 bg-slate-50/50 flex flex-col sm:flex-row items-center justify-between gap-2.5 text-[11px]">
                <p class="text-slate-500 font-medium">
                    Halaman <span class="font-bold text-pu-navy">{{ $assignments->currentPage() }}</span> dari <span class="font-bold text-slate-700">{{ $assignments->lastPage() }}</span>
                    <span class="text-slate-400 mx-1">·</span>
                    Total <span class="font-bold text-slate-700">{{ number_format($assignments->total()) }}</span> penugasan
                </p>

                <nav class="inline-flex items-center gap-1" aria-label="Pagination">
                    @if($assignments->onFirstPage())
                        <span class="w-7 h-7 inline-flex items-center justify-center rounded-lg border border-slate-200 bg-slate-100/70 text-slate-300 cursor-not-allowed">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.4" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                            </svg>
                        </span>
                    @else
                        <a href="{{ $assignments->previousPageUrl() }}"
                           class="w-7 h-7 inline-flex items-center justify-center rounded-lg border border-slate-200 bg-white hover:bg-pu-navy text-slate-600 hover:text-white transition">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.4" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                            </svg>
                        </a>
                    @endif

                    @php
                        $current = $assignments->currentPage();
                        $last    = $assignments->lastPage();
                        $start   = max(1, $current - 2);
                        $end     = min($last, $current + 2);
                    @endphp

                    @if($start > 1)
                        <a href="{{ $assignments->url(1) }}" class="min-w-[28px] h-7 px-2 inline-flex items-center justify-center rounded-lg border border-slate-200 bg-white hover:bg-slate-100 text-slate-600 font-bold transition">1</a>
                        @if($start > 2)
                            <span class="px-1 text-slate-400">...</span>
                        @endif
                    @endif

                    @for($page = $start; $page <= $end; $page++)
                        @if($page == $current)
                            <span class="min-w-[28px] h-7 px-2 inline-flex items-center justify-center rounded-lg bg-pu-navy text-pu-gold font-extrabold shadow-xs">
                                {{ $page }}
                            </span>
                        @else
                            <a href="{{ $assignments->url($page) }}"
                               class="min-w-[28px] h-7 px-2 inline-flex items-center justify-center rounded-lg border border-slate-200 bg-white hover:bg-slate-100 text-slate-600 font-bold transition">
                                {{ $page }}
                            </a>
                        @endif
                    @endfor

                    @if($end < $last)
                        @if($end < $last - 1)
                            <span class="px-1 text-slate-400">...</span>
                        @endif
                        <a href="{{ $assignments->url($last) }}" class="min-w-[28px] h-7 px-2 inline-flex items-center justify-center rounded-lg border border-slate-200 bg-white hover:bg-slate-100 text-slate-600 font-bold transition">{{ $last }}</a>
                    @endif

                    @if($assignments->hasMorePages())
                        <a href="{{ $assignments->nextPageUrl() }}"
                           class="w-7 h-7 inline-flex items-center justify-center rounded-lg border border-slate-200 bg-white hover:bg-pu-navy text-slate-600 hover:text-white transition">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.4" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                            </svg>
                        </a>
                    @else
                        <span class="w-7 h-7 inline-flex items-center justify-center rounded-lg border border-slate-200 bg-slate-100/70 text-slate-300 cursor-not-allowed">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.4" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                            </svg>
                        </span>
                    @endif
                </nav>
            </div>
        @endif
    </div>

    <!-- ================= 5. MODAL TAMBAH PENUGASAN BARU (FULL BLUR) ================= -->
    <div id="createAssignmentModal" onclick="if(event.target === this) closeCreateAssignmentModal()"
         style="margin-top: 0 !important;"
         class="fixed inset-0 w-screen h-screen bg-slate-950/65 backdrop-blur-sm hidden items-center justify-center z-50 p-4">
        <div class="bg-white rounded-2xl max-w-2xl w-full p-6 shadow-2xl border-t-4 border-pu-navy max-h-[88vh] flex flex-col">
            <div class="flex justify-between items-center border-b border-slate-100 pb-3.5 mb-4">
                <div>
                    <span class="text-[10.5px] font-extrabold px-2.5 py-0.5 rounded-lg bg-pu-navy text-pu-gold uppercase tracking-wider">Penugasan Baru</span>
                    <h3 class="text-base font-extrabold text-pu-navy mt-1">Tambah Penugasan Survei Lapangan</h3>
                </div>
                <button type="button" onclick="closeCreateAssignmentModal()" class="p-1.5 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <form method="POST" action="{{ route('admin.assignments.store') }}" class="space-y-4 text-xs flex-1 overflow-y-auto pr-1">
                @csrf
                <div>
                    <label class="block text-[11px] font-bold text-slate-700 mb-1.5">1. Pilih Petugas Surveyor *</label>
                    <select name="assigned_to" required class="w-full h-10 bg-slate-50 focus:bg-white border border-slate-300 focus:border-pu-navy rounded-xl px-3 font-semibold text-slate-800 outline-none transition">
                        <option value="">-- Pilih Akun Surveyor --</option>
                        @foreach($surveyors as $srv)
                            <option value="{{ $srv->id }}">{{ $srv->name }} ({{ $srv->email }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <div class="flex flex-wrap justify-between items-center gap-2 mb-2">
                        <label class="text-[11px] font-bold text-slate-700">
                            2. Pilih Titik Infrastruktur ({{ $unassignedInfrastructures->count() }} tersedia) *
                        </label>
                        <div class="flex items-center gap-2">
                            <span id="modal-unassigned-count" class="text-[10.5px] font-extrabold px-2 py-0.5 rounded-md bg-pu-navy/10 text-pu-navy">
                                0 titik dipilih
                            </span>
                            <div class="relative">
                                <input type="text" id="filterUnassignedInput" onkeyup="filterUnassignedList()" placeholder="Cari nama / kota..."
                                       class="border border-slate-300 focus:border-pu-navy rounded-xl pl-7 pr-2.5 py-1 text-[11px] w-44 outline-none transition">
                                <svg class="w-3 h-3 text-slate-400 absolute left-2.5 top-2" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 104.5 4.5a7.5 7.5 0 0012.15 12.15z"/>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div id="unassignedListContainer" class="border border-slate-200 rounded-2xl max-h-64 overflow-y-auto divide-y divide-slate-100 bg-slate-50/70">
                        @forelse($unassignedInfrastructures as $infra)
                            <label class="unassigned-row flex items-center gap-3 p-3 hover:bg-sky-50/60 cursor-pointer transition-colors">
                                <input type="checkbox" name="infrastructure_ids[]" value="{{ $infra->id }}" onchange="updateModalUnassignedCount()"
                                       class="modal-infra-checkbox w-4 h-4 accent-[#002359] rounded cursor-pointer">
                                <div class="flex-1 min-w-0">
                                    <p class="font-bold text-slate-900 truncate">
                                        <span class="px-2 py-0.5 text-[10px] font-extrabold rounded bg-pu-navy text-pu-gold mr-1.5">{{ $infra->kategori }}</span>
                                        {{ $infra->nama_infrastruktur }}
                                    </p>
                                    <p class="text-[11px] text-slate-500 mt-0.5">{{ $infra->kota_kabupaten }}, {{ $infra->provinsi }} · {{ $infra->latitude }}, {{ $infra->longitude }}</p>
                                </div>
                            </label>
                        @empty
                            <p class="p-8 text-center text-slate-400">Semua titik infrastruktur sudah memiliki petugas surveyor.</p>
                        @endforelse
                    </div>
                </div>

                <div class="flex justify-end gap-2 pt-3 border-t border-slate-100">
                    <button type="button" onclick="closeCreateAssignmentModal()"
                            class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-bold transition">
                        Batal
                    </button>
                    <button type="submit"
                            class="px-5 py-2 bg-pu-navy hover:bg-pu-gold text-white hover:text-pu-navy rounded-xl font-bold shadow-xs transition cursor-pointer">
                        Simpan Penugasan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ================= 6. MODAL EDIT PENUGASAN (FULL BLUR) ================= -->
    <div id="editAssignmentModal" onclick="if(event.target === this) closeEditAssignmentModal()"
         style="margin-top: 0 !important;"
         class="fixed inset-0 w-screen h-screen bg-slate-950/65 backdrop-blur-sm hidden items-center justify-center z-50 p-4">
        <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border-t-4 border-pu-gold">
            <div class="flex justify-between items-center border-b border-slate-100 pb-3.5 mb-4">
                <div>
                    <span class="text-[10.5px] font-extrabold px-2.5 py-0.5 rounded-lg bg-pu-gold/25 text-pu-navy uppercase tracking-wider">Perbarui Tugas</span>
                    <h3 class="text-base font-extrabold text-pu-navy mt-1">Edit Detail Penugasan</h3>
                </div>
                <button type="button" onclick="closeEditAssignmentModal()" class="p-1.5 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <form id="editAssignmentForm" method="POST" class="space-y-3.5 text-xs">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-[11px] font-bold text-slate-700 mb-1">Petugas Surveyor (Kosongkan untuk melepas tugas)</label>
                    <select name="assigned_to" id="edit_assign_surveyor"
                            class="w-full h-9 bg-slate-50 focus:bg-white border border-slate-300 focus:border-pu-navy rounded-xl px-3 font-semibold text-slate-800 outline-none transition">
                        <option value="">-- Lepas / Belum Ditugaskan --</option>
                        @foreach($surveyors as $srv)
                            <option value="{{ $srv->id }}">{{ $srv->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-slate-700 mb-1">Nama Objek Infrastruktur *</label>
                    <input type="text" name="nama_infrastruktur" id="edit_assign_nama" required
                           class="w-full h-9 bg-slate-50 focus:bg-white border border-slate-300 focus:border-pu-navy rounded-xl px-3 outline-none transition">
                </div>

                <div class="grid grid-cols-2 gap-2.5">
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 mb-1">Kota / Kabupaten *</label>
                        <input type="text" name="kota_kabupaten" id="edit_assign_kota" required
                               class="w-full h-9 bg-slate-50 focus:bg-white border border-slate-300 focus:border-pu-navy rounded-xl px-3 outline-none transition">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 mb-1">Provinsi *</label>
                        <input type="text" name="provinsi" id="edit_assign_provinsi" required
                               class="w-full h-9 bg-slate-50 focus:bg-white border border-slate-300 focus:border-pu-navy rounded-xl px-3 outline-none transition">
                    </div>
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-slate-700 mb-1">Status Survei *</label>
                    <select name="status_survei" id="edit_assign_status" required
                            class="w-full h-9 bg-slate-50 focus:bg-white border border-slate-300 focus:border-pu-navy rounded-xl px-3 outline-none transition">
                        <option value="belum">Menunggu Survei (Belum)</option>
                        <option value="sudah">Selesai Disurvei (Sudah)</option>
                    </select>
                </div>

                <div class="flex justify-end gap-2 pt-3 border-t border-slate-100">
                    <button type="button" onclick="closeEditAssignmentModal()"
                            class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-bold transition">
                        Batal
                    </button>
                    <button type="submit"
                            class="px-5 py-2 bg-pu-navy hover:bg-pu-gold text-white hover:text-pu-navy rounded-xl font-bold shadow-xs transition cursor-pointer">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const selectAllAssign = document.getElementById('selectAllAssign');
        const assignCheckboxes = document.querySelectorAll('.assign-checkbox');
        const assignBadge = document.getElementById('selected-assign-badge');

        function getSelectedAssignIds() {
            return Array.from(document.querySelectorAll('.assign-checkbox:checked')).map(cb => cb.value);
        }

        function updateAssignSelection() {
            const ids = getSelectedAssignIds();
            assignBadge.textContent = `${ids.length} dipilih`;
            if (assignCheckboxes.length > 0) {
                selectAllAssign.checked = ids.length === assignCheckboxes.length;
                selectAllAssign.indeterminate = ids.length > 0 && ids.length < assignCheckboxes.length;
            }
        }

        if (selectAllAssign) {
            selectAllAssign.addEventListener('change', (e) => {
                assignCheckboxes.forEach(cb => { cb.checked = e.target.checked; });
                updateAssignSelection();
            });
        }
        assignCheckboxes.forEach(cb => cb.addEventListener('change', updateAssignSelection));

        function submitBulkUnassign() {
            const ids = getSelectedAssignIds();
            if (ids.length === 0) {
                alert('Silakan centang minimal 1 penugasan yang ingin dibatalkan.');
                return;
            }
            if (!confirm(`Batalkan penugasan surveyor untuk ${ids.length} titik terpilih?`)) return;

            const container = document.getElementById('bulk-unassign-inputs');
            container.innerHTML = ids.map(id => `<input type="hidden" name="ids[]" value="${id}">`).join('');
            document.getElementById('bulkUnassignForm').submit();
        }

        function filterUnassignedList() {
            const q = document.getElementById('filterUnassignedInput').value.toLowerCase();
            document.querySelectorAll('.unassigned-row').forEach(row => {
                row.style.display = row.textContent.toLowerCase().includes(q) ? 'flex' : 'none';
            });
        }

        function updateModalUnassignedCount() {
            const count = document.querySelectorAll('.modal-infra-checkbox:checked').length;
            const badge = document.getElementById('modal-unassigned-count');
            if (badge) badge.textContent = `${count} titik dipilih`;
        }

        function openCreateAssignmentModal() {
            document.getElementById('createAssignmentModal').classList.remove('hidden');
            document.getElementById('createAssignmentModal').classList.add('flex');
        }
        function closeCreateAssignmentModal() {
            document.getElementById('createAssignmentModal').classList.add('hidden');
            document.getElementById('createAssignmentModal').classList.remove('flex');
        }

        function openEditAssignmentModal(item) {
            document.getElementById('editAssignmentForm').action = `/admin/assignments/${item.id}`;
            document.getElementById('edit_assign_surveyor').value = item.assigned_to || '';
            document.getElementById('edit_assign_nama').value = item.nama_infrastruktur;
            document.getElementById('edit_assign_kota').value = item.kota_kabupaten;
            document.getElementById('edit_assign_provinsi').value = item.provinsi;
            document.getElementById('edit_assign_status').value = item.status_survei;

            document.getElementById('editAssignmentModal').classList.remove('hidden');
            document.getElementById('editAssignmentModal').classList.add('flex');
        }
        function closeEditAssignmentModal() {
            document.getElementById('editAssignmentModal').classList.add('hidden');
            document.getElementById('editAssignmentModal').classList.remove('flex');
        }
    </script>
@endsection