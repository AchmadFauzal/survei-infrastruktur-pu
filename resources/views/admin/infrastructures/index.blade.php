@extends('admin.layout')

@section('title', 'Manajemen Data & Penugasan Survei')

@section('header_actions')
    <button type="button" onclick="openAddModal()"
            class="group bg-pu-gold hover:bg-amber-400 active:scale-[0.98] text-pu-navy text-xs font-extrabold px-3.5 py-2 rounded-xl shadow-xs hover:shadow-md transition-all duration-200 inline-flex items-center gap-1.5 whitespace-nowrap cursor-pointer">
        <svg class="w-4 h-4 transition-transform duration-200 group-hover:rotate-90" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
        </svg>
        <span>Tambah Data Infrastruktur</span>
    </button>
@endsection

@section('content')
    <!-- ================= 1. FILTER PENCARIAN ================= -->
    <div class="bg-white p-5 rounded-2xl shadow-xs border border-slate-200">
        <div class="flex flex-wrap items-center justify-between gap-2 mb-3.5 pb-2.5 border-b border-slate-100">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-pu-navy" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                </svg>
                <h2 class="text-xs font-extrabold uppercase tracking-wider text-pu-navy">Filter Data Infrastruktur</h2>
            </div>
            <p class="text-[11px] text-slate-400 hidden sm:block">Kelola basis data titik infrastruktur dan distribusi surveyor</p>
        </div>

        <form method="GET" action="{{ route('admin.infrastructures.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3.5 items-end text-xs">
            <div class="lg:col-span-4">
                <label class="block text-[11px] font-bold text-slate-600 mb-1">Kata Kunci Pencarian</label>
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama infrastruktur, kota, provinsi..."
                           class="w-full h-9 bg-slate-50 focus:bg-white border border-slate-300 focus:border-pu-navy rounded-xl pl-8 pr-3 text-xs outline-none transition">
                    <svg class="w-3.5 h-3.5 text-slate-400 absolute left-2.5 top-2.5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 104.5 4.5a7.5 7.5 0 0012.15 12.15z"/>
                    </svg>
                </div>
            </div>

            <div class="lg:col-span-2">
                <label class="block text-[11px] font-bold text-slate-600 mb-1">Kategori PU</label>
                <select name="kategori" class="w-full h-9 bg-slate-50 focus:bg-white border border-slate-300 focus:border-pu-navy rounded-xl px-3 text-xs outline-none transition">
                    <option value="">Semua Kategori</option>
                    <option value="BM" {{ request('kategori') == 'BM' ? 'selected' : '' }}>BM (Bina Marga)</option>
                    <option value="CK" {{ request('kategori') == 'CK' ? 'selected' : '' }}>CK (Cipta Karya)</option>
                    <option value="SDA" {{ request('kategori') == 'SDA' ? 'selected' : '' }}>SDA (Sumber Daya Air)</option>
                    <option value="PS" {{ request('kategori') == 'PS' ? 'selected' : '' }}>PS (Prasarana Strategis)</option>
                    <option value="SETJEN" {{ request('kategori') == 'SETJEN' ? 'selected' : '' }}>SETJEN (Sekretariat Jenderal)</option>
                </select>
            </div>

            <div class="lg:col-span-2">
                <label class="block text-[11px] font-bold text-slate-600 mb-1">Status Survei</label>
                <select name="status_survei" class="w-full h-9 bg-slate-50 focus:bg-white border border-slate-300 focus:border-pu-navy rounded-xl px-3 text-xs outline-none transition">
                    <option value="">Semua Status</option>
                    <option value="belum" {{ request('status_survei') == 'belum' ? 'selected' : '' }}>Belum Survei</option>
                    <option value="sudah" {{ request('status_survei') == 'sudah' ? 'selected' : '' }}>Sudah Survei</option>
                </select>
            </div>

            <div class="lg:col-span-2">
                <label class="block text-[11px] font-bold text-slate-600 mb-1">Penugasan</label>
                <select name="assigned_to" class="w-full h-9 bg-slate-50 focus:bg-white border border-slate-300 focus:border-pu-navy rounded-xl px-3 text-xs outline-none transition">
                    <option value="">Semua Penugasan</option>
                    <option value="unassigned" {{ request('assigned_to') == 'unassigned' ? 'selected' : '' }}>Belum Ditugaskan</option>
                    @foreach($surveyors as $srv)
                        <option value="{{ $srv->id }}" {{ request('assigned_to') == $srv->id ? 'selected' : '' }}>
                            {{ $srv->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="lg:col-span-2 flex items-center gap-2">
                <button type="submit"
                        class="flex-1 h-9 bg-pu-navy hover:bg-pu-gold text-white hover:text-pu-navy text-xs font-bold rounded-xl shadow-xs transition inline-flex items-center justify-center gap-1.5 cursor-pointer">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.4" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 104.5 4.5a7.5 7.5 0 0012.15 12.15z"/>
                    </svg>
                    <span>Filter</span>
                </button>
                <a href="{{ route('admin.infrastructures.index') }}"
                   class="h-9 px-3 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl transition inline-flex items-center justify-center">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- ================= 2. ACTION BAR MASSAL (NAVY PU STYLE) ================= -->
    <div class="bg-pu-navy text-white px-4 py-3 rounded-2xl shadow-xs flex flex-wrap justify-between items-center gap-3 text-xs">
        <div class="flex items-center gap-2.5">
            <span id="selected-count-badge" class="bg-pu-gold text-pu-navy font-extrabold px-2.5 py-1 rounded-lg text-[11px] tracking-wide">
                0 dipilih
            </span>
            <span class="text-sky-100/85">Centang titik pada tabel untuk penugasan atau penghapusan massal.</span>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <!-- Form Penugasan Surveyor Massal -->
            <form id="bulkAssignForm" method="POST" action="{{ route('admin.infrastructures.bulk_assign') }}" class="flex flex-wrap items-center gap-2">
                @csrf
                <div id="bulk-assign-inputs"></div>
                <select name="assigned_to" class="h-8 bg-white text-slate-800 rounded-xl px-3 text-xs font-semibold outline-none">
                    <option value="">-- Lepas / Kosongkan Tugas --</option>
                    @foreach($surveyors as $srv)
                        <option value="{{ $srv->id }}">Tugaskan ke: {{ $srv->name }}</option>
                    @endforeach
                </select>
                <button type="button" onclick="submitBulkAssign()"
                        class="h-8 bg-sky-500 hover:bg-sky-400 active:scale-[0.98] text-white font-bold px-3.5 rounded-xl transition inline-flex items-center gap-1.5 shadow-xs cursor-pointer">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.3" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    <span>Terapkan Tugas</span>
                </button>
            </form>

            <div class="hidden sm:block h-5 w-px bg-white/20 mx-0.5"></div>

            <!-- Form Hapus Massal -->
            <form id="bulkDeleteForm" method="POST" action="{{ route('admin.infrastructures.bulk_delete') }}">
                @csrf
                <div id="bulk-delete-inputs"></div>
                <button type="button" onclick="submitBulkDelete()"
                        class="h-8 bg-red-600 hover:bg-red-500 active:scale-[0.98] text-white font-bold px-3.5 rounded-xl transition inline-flex items-center gap-1.5 shadow-xs cursor-pointer">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.3" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    <span>Hapus Terpilih</span>
                </button>
            </form>
        </div>
    </div>

    <!-- ================= 3. TABEL DATA INFRASTRUKTUR ================= -->
    <div class="bg-white rounded-2xl shadow-xs border border-slate-200 overflow-hidden">
        <div class="px-5 py-3.5 border-b border-slate-100 flex flex-wrap items-center justify-between gap-2 bg-slate-50/70">
            <span class="text-xs font-extrabold text-pu-navy uppercase tracking-wider">Master Data Infrastruktur</span>
            <span class="text-[11px] font-semibold text-slate-500">
                Menampilkan {{ $infrastructures->firstItem() ?? 0 }}–{{ $infrastructures->lastItem() ?? 0 }} dari {{ $infrastructures->total() }} titik
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead class="bg-slate-50 border-b border-slate-200 uppercase text-slate-500 text-[11px] font-bold tracking-wider">
                    <tr>
                        <th class="py-3.5 px-4 w-10 text-center">
                            <input type="checkbox" id="selectAllRows" class="w-4 h-4 accent-[#002359] rounded cursor-pointer" title="Pilih Semua di Halaman Ini">
                        </th>
                        <th class="py-3.5 px-4">Nama Infrastruktur</th>
                        <th class="py-3.5 px-4 whitespace-nowrap">Kategori</th>
                        <th class="py-3.5 px-4">Wilayah & Koordinat</th>
                        <th class="py-3.5 px-4 whitespace-nowrap">Penugasan Surveyor</th>
                        <th class="py-3.5 px-4 whitespace-nowrap">Status Survei</th>
                        <th class="py-3.5 px-4 text-right whitespace-nowrap">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($infrastructures as $item)
                        <tr class="hover:bg-sky-50/40 transition-colors">
                            <td class="py-3.5 px-4 text-center">
                                <input type="checkbox" value="{{ $item->id }}" class="row-checkbox w-4 h-4 accent-[#002359] rounded cursor-pointer">
                            </td>
                            <td class="py-3.5 px-4 min-w-[210px]">
                                <p class="font-bold text-slate-900 text-xs sm:text-[13px] leading-snug">{{ $item->nama_infrastruktur }}</p>
                                <p class="text-[11px] text-slate-500 mt-0.5">{{ $item->jenis_infrastruktur ?? 'Jenis belum diisi' }}</p>
                            </td>
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 text-[11px] font-extrabold rounded-lg bg-pu-navy/10 text-pu-navy border border-pu-navy/15">
                                    {{ $item->kategori }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 min-w-[175px]">
                                <p class="font-semibold text-slate-800">{{ $item->kota_kabupaten }}, {{ $item->provinsi }}</p>
                                <p class="text-[11px] text-slate-400 mt-0.5">{{ $item->latitude }}, {{ $item->longitude }}</p>
                            </td>
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                @if($item->assignedSurveyor)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl bg-sky-50 text-pu-navy font-bold border border-sky-200 text-[11px]">
                                        <span class="w-4 h-4 rounded-md bg-pu-navy text-pu-gold text-[10px] font-extrabold flex items-center justify-center shrink-0">
                                            {{ strtoupper(substr($item->assignedSurveyor->name, 0, 1)) }}
                                        </span>
                                        <span>{{ $item->assignedSurveyor->name }}</span>
                                    </span>
                                @else
                                    <span class="text-slate-400 italic text-[11px]">Belum ditugaskan</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                @if($item->status_survei === 'sudah')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-[11px] font-bold rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                        Sudah Survei
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-[11px] font-bold rounded-full bg-red-50 text-red-700 border border-red-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                                        Belum Survei
                                    </span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                <div class="inline-flex items-center gap-1.5">
                                    <button type="button" onclick='openEditModal(@json($item))'
                                            class="bg-pu-gold hover:bg-amber-400 text-pu-navy px-3 py-1.5 rounded-xl font-bold text-[11px] transition inline-flex items-center gap-1 cursor-pointer">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                        <span>Edit</span>
                                    </button>
                                    <form method="POST" action="{{ route('admin.infrastructures.destroy', $item) }}" class="inline"
                                          onsubmit="return confirm('Hapus titik {{ $item->nama_infrastruktur }}?')">
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
                            <td colspan="7" class="py-12 text-center text-slate-400">
                                <p class="font-semibold text-slate-600">Belum ada data infrastruktur yang ditemukan.</p>
                                <p class="text-[11px] mt-1">Klik tombol <strong class="text-pu-navy">Tambah Data Infrastruktur</strong> di kanan atas untuk menambahkan titik baru.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination Kustom Kompak -->
        @if($infrastructures->hasPages())
            <div class="px-5 py-3 border-t border-slate-100 bg-slate-50/50 flex flex-col sm:flex-row items-center justify-between gap-2.5 text-[11px]">
                <p class="text-slate-500 font-medium">
                    Halaman <span class="font-bold text-pu-navy">{{ $infrastructures->currentPage() }}</span> dari <span class="font-bold text-slate-700">{{ $infrastructures->lastPage() }}</span>
                    <span class="text-slate-400 mx-1">·</span>
                    Total <span class="font-bold text-slate-700">{{ number_format($infrastructures->total()) }}</span> titik
                </p>

                <nav class="inline-flex items-center gap-1" aria-label="Pagination">
                    @if($infrastructures->onFirstPage())
                        <span class="w-7 h-7 inline-flex items-center justify-center rounded-lg border border-slate-200 bg-slate-100/70 text-slate-300 cursor-not-allowed">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.4" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                            </svg>
                        </span>
                    @else
                        <a href="{{ $infrastructures->previousPageUrl() }}"
                           class="w-7 h-7 inline-flex items-center justify-center rounded-lg border border-slate-200 bg-white hover:bg-pu-navy text-slate-600 hover:text-white transition">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.4" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                            </svg>
                        </a>
                    @endif

                    @php
                        $current = $infrastructures->currentPage();
                        $last    = $infrastructures->lastPage();
                        $start   = max(1, $current - 2);
                        $end     = min($last, $current + 2);
                    @endphp

                    @if($start > 1)
                        <a href="{{ $infrastructures->url(1) }}" class="min-w-[28px] h-7 px-2 inline-flex items-center justify-center rounded-lg border border-slate-200 bg-white hover:bg-slate-100 text-slate-600 font-bold transition">1</a>
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
                            <a href="{{ $infrastructures->url($page) }}"
                               class="min-w-[28px] h-7 px-2 inline-flex items-center justify-center rounded-lg border border-slate-200 bg-white hover:bg-slate-100 text-slate-600 font-bold transition">
                                {{ $page }}
                            </a>
                        @endif
                    @endfor

                    @if($end < $last)
                        @if($end < $last - 1)
                            <span class="px-1 text-slate-400">...</span>
                        @endif
                        <a href="{{ $infrastructures->url($last) }}" class="min-w-[28px] h-7 px-2 inline-flex items-center justify-center rounded-lg border border-slate-200 bg-white hover:bg-slate-100 text-slate-600 font-bold transition">{{ $last }}</a>
                    @endif

                    @if($infrastructures->hasMorePages())
                        <a href="{{ $infrastructures->nextPageUrl() }}"
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

    <!-- ================= 4. MODAL TAMBAH DATA INFRASTRUKTUR (FULL BLUR) ================= -->
    <div id="addModal" onclick="if(event.target === this) closeAddModal()"
         style="margin-top: 0 !important;"
         class="fixed inset-0 w-screen h-screen bg-slate-950/65 backdrop-blur-sm hidden items-center justify-center z-50 p-4">
        <div class="bg-white rounded-2xl max-w-lg w-full max-h-[88vh] overflow-y-auto p-6 shadow-2xl border-t-4 border-pu-navy">
            <div class="flex justify-between items-center border-b border-slate-100 pb-3.5 mb-4">
                <div>
                    <span class="text-[10.5px] font-extrabold px-2.5 py-0.5 rounded-lg bg-pu-navy text-pu-gold uppercase tracking-wider">Data Baru</span>
                    <h3 class="text-base font-extrabold text-pu-navy mt-1">Tambah Data Infrastruktur</h3>
                </div>
                <button type="button" onclick="closeAddModal()" class="p-1.5 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <form method="POST" action="{{ route('admin.infrastructures.store') }}" class="space-y-3.5 text-xs">
                @csrf
                <div>
                    <label class="block text-[11px] font-bold text-slate-700 mb-1">Nama Objek Infrastruktur *</label>
                    <input type="text" name="nama_infrastruktur" required placeholder="Contoh: Jembatan Suramadu Sisi Madura"
                           class="w-full h-9 bg-slate-50 focus:bg-white border border-slate-300 focus:border-pu-navy rounded-xl px-3 outline-none transition">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 mb-1">Kategori PU *</label>
                        <select name="kategori" required class="w-full h-9 bg-slate-50 focus:bg-white border border-slate-300 focus:border-pu-navy rounded-xl px-3 outline-none transition">
                            <option value="BM">BM (Bina Marga)</option>
                            <option value="CK">CK (Cipta Karya)</option>
                            <option value="SDA">SDA (Sumber Daya Air)</option>
                            <option value="PS">PS (Prasarana Strategis)</option>
                            <option value="SETJEN">SETJEN (Sekretariat Jenderal)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 mb-1">Jenis Infrastruktur</label>
                        <input type="text" name="jenis_infrastruktur" placeholder="Contoh: Jembatan / TPA / Bendungan"
                               class="w-full h-9 bg-slate-50 focus:bg-white border border-slate-300 focus:border-pu-navy rounded-xl px-3 outline-none transition">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 mb-1">Kota / Kabupaten *</label>
                        <input type="text" name="kota_kabupaten" required
                               class="w-full h-9 bg-slate-50 focus:bg-white border border-slate-300 focus:border-pu-navy rounded-xl px-3 outline-none transition">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 mb-1">Provinsi *</label>
                        <input type="text" name="provinsi" required
                               class="w-full h-9 bg-slate-50 focus:bg-white border border-slate-300 focus:border-pu-navy rounded-xl px-3 outline-none transition">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 mb-1">Latitude (Y) *</label>
                        <input type="number" step="any" name="latitude" required placeholder="-6.236013"
                               class="w-full h-9 bg-slate-50 focus:bg-white border border-slate-300 focus:border-pu-navy rounded-xl px-3 outline-none transition">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 mb-1">Longitude (X) *</label>
                        <input type="number" step="any" name="longitude" required placeholder="106.800272"
                               class="w-full h-9 bg-slate-50 focus:bg-white border border-slate-300 focus:border-pu-navy rounded-xl px-3 outline-none transition">
                    </div>
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-slate-700 mb-1">Tugaskan ke Surveyor (Opsional)</label>
                    <select name="assigned_to" class="w-full h-9 bg-slate-50 focus:bg-white border border-slate-300 focus:border-pu-navy rounded-xl px-3 outline-none transition">
                        <option value="">-- Belum Ditugaskan --</option>
                        @foreach($surveyors as $srv)
                            <option value="{{ $srv->id }}">{{ $srv->name }} ({{ $srv->email }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex justify-end gap-2 pt-3 border-t border-slate-100">
                    <button type="button" onclick="closeAddModal()"
                            class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-bold transition">
                        Batal
                    </button>
                    <button type="submit"
                            class="px-5 py-2 bg-pu-navy hover:bg-pu-gold text-white hover:text-pu-navy rounded-xl font-bold shadow-xs transition cursor-pointer">
                        Simpan Data
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ================= 5. MODAL EDIT DATA INFRASTRUKTUR (FULL BLUR) ================= -->
    <div id="editModal" onclick="if(event.target === this) closeEditModal()"
         style="margin-top: 0 !important;"
         class="fixed inset-0 w-screen h-screen bg-slate-950/65 backdrop-blur-sm hidden items-center justify-center z-50 p-4">
        <div class="bg-white rounded-2xl max-w-lg w-full max-h-[88vh] overflow-y-auto p-6 shadow-2xl border-t-4 border-pu-gold">
            <div class="flex justify-between items-center border-b border-slate-100 pb-3.5 mb-4">
                <div>
                    <span class="text-[10.5px] font-extrabold px-2.5 py-0.5 rounded-lg bg-pu-gold/25 text-pu-navy uppercase tracking-wider">Perbarui Data</span>
                    <h3 class="text-base font-extrabold text-pu-navy mt-1">Edit Data Infrastruktur</h3>
                </div>
                <button type="button" onclick="closeEditModal()" class="p-1.5 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <form id="editForm" method="POST" class="space-y-3.5 text-xs">
                @csrf
                @method('PUT')
                <div>
                    <label class="block text-[11px] font-bold text-slate-700 mb-1">Nama Objek Infrastruktur *</label>
                    <input type="text" name="nama_infrastruktur" id="edit_nama_infrastruktur" required
                           class="w-full h-9 bg-slate-50 focus:bg-white border border-slate-300 focus:border-pu-navy rounded-xl px-3 outline-none transition">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 mb-1">Kategori PU *</label>
                        <select name="kategori" id="edit_kategori" required class="w-full h-9 bg-slate-50 focus:bg-white border border-slate-300 focus:border-pu-navy rounded-xl px-3 outline-none transition">
                            <option value="BM">BM (Bina Marga)</option>
                            <option value="CK">CK (Cipta Karya)</option>
                            <option value="SDA">SDA (Sumber Daya Air)</option>
                            <option value="PS">PS (Prasarana Strategis)</option>
                            <option value="SETJEN">SETJEN (Sekretariat Jenderal)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 mb-1">Jenis Infrastruktur</label>
                        <input type="text" name="jenis_infrastruktur" id="edit_jenis"
                               class="w-full h-9 bg-slate-50 focus:bg-white border border-slate-300 focus:border-pu-navy rounded-xl px-3 outline-none transition">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 mb-1">Kota / Kabupaten *</label>
                        <input type="text" name="kota_kabupaten" id="edit_kota" required
                               class="w-full h-9 bg-slate-50 focus:bg-white border border-slate-300 focus:border-pu-navy rounded-xl px-3 outline-none transition">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 mb-1">Provinsi *</label>
                        <input type="text" name="provinsi" id="edit_provinsi" required
                               class="w-full h-9 bg-slate-50 focus:bg-white border border-slate-300 focus:border-pu-navy rounded-xl px-3 outline-none transition">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 mb-1">Latitude (Y) *</label>
                        <input type="number" step="any" name="latitude" id="edit_latitude" required
                               class="w-full h-9 bg-slate-50 focus:bg-white border border-slate-300 focus:border-pu-navy rounded-xl px-3 outline-none transition">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 mb-1">Longitude (X) *</label>
                        <input type="number" step="any" name="longitude" id="edit_longitude" required
                               class="w-full h-9 bg-slate-50 focus:bg-white border border-slate-300 focus:border-pu-navy rounded-xl px-3 outline-none transition">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 mb-1">Status Survei *</label>
                        <select name="status_survei" id="edit_status" required class="w-full h-9 bg-slate-50 focus:bg-white border border-slate-300 focus:border-pu-navy rounded-xl px-3 outline-none transition">
                            <option value="belum">Belum Survei</option>
                            <option value="sudah">Sudah Survei</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 mb-1">Penugasan Surveyor</label>
                        <select name="assigned_to" id="edit_assigned_to" class="w-full h-9 bg-slate-50 focus:bg-white border border-slate-300 focus:border-pu-navy rounded-xl px-3 outline-none transition">
                            <option value="">-- Belum Ditugaskan --</option>
                            @foreach($surveyors as $srv)
                                <option value="{{ $srv->id }}">{{ $srv->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="flex justify-end gap-2 pt-3 border-t border-slate-100">
                    <button type="button" onclick="closeEditModal()"
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
        const selectAll = document.getElementById('selectAllRows');
        const rowCheckboxes = document.querySelectorAll('.row-checkbox');
        const countBadge = document.getElementById('selected-count-badge');

        function getSelectedIds() {
            return Array.from(document.querySelectorAll('.row-checkbox:checked')).map(cb => cb.value);
        }

        function updateSelectionUI() {
            const ids = getSelectedIds();
            countBadge.textContent = `${ids.length} dipilih`;
            if (rowCheckboxes.length > 0) {
                selectAll.checked = ids.length === rowCheckboxes.length;
                selectAll.indeterminate = ids.length > 0 && ids.length < rowCheckboxes.length;
            }
        }

        if (selectAll) {
            selectAll.addEventListener('change', (e) => {
                rowCheckboxes.forEach(cb => { cb.checked = e.target.checked; });
                updateSelectionUI();
            });
        }

        rowCheckboxes.forEach(cb => cb.addEventListener('change', updateSelectionUI));

        function submitBulkDelete() {
            const ids = getSelectedIds();
            if (ids.length === 0) {
                alert('Silakan centang minimal 1 data yang ingin dihapus.');
                return;
            }
            if (!confirm(`Yakin ingin menghapus ${ids.length} data infrastruktur terpilih secara permanen?`)) return;

            const container = document.getElementById('bulk-delete-inputs');
            container.innerHTML = ids.map(id => `<input type="hidden" name="ids[]" value="${id}">`).join('');
            document.getElementById('bulkDeleteForm').submit();
        }

        function submitBulkAssign() {
            const ids = getSelectedIds();
            if (ids.length === 0) {
                alert('Silakan centang minimal 1 data infrastruktur untuk ditugaskan ke surveyor.');
                return;
            }
            const container = document.getElementById('bulk-assign-inputs');
            container.innerHTML = ids.map(id => `<input type="hidden" name="ids[]" value="${id}">`).join('');
            document.getElementById('bulkAssignForm').submit();
        }

        function openAddModal() {
            document.getElementById('addModal').classList.remove('hidden');
            document.getElementById('addModal').classList.add('flex');
        }
        function closeAddModal() {
            document.getElementById('addModal').classList.add('hidden');
            document.getElementById('addModal').classList.remove('flex');
        }

        function openEditModal(item) {
            document.getElementById('editForm').action = `/admin/infrastructures/${item.id}`;
            document.getElementById('edit_nama_infrastruktur').value = item.nama_infrastruktur;
            document.getElementById('edit_kategori').value = item.kategori;
            document.getElementById('edit_jenis').value = item.jenis_infrastruktur || '';
            document.getElementById('edit_kota').value = item.kota_kabupaten;
            document.getElementById('edit_provinsi').value = item.provinsi;
            document.getElementById('edit_latitude').value = item.latitude;
            document.getElementById('edit_longitude').value = item.longitude;
            document.getElementById('edit_status').value = item.status_survei;
            document.getElementById('edit_assigned_to').value = item.assigned_to || '';

            document.getElementById('editModal').classList.remove('hidden');
            document.getElementById('editModal').classList.add('flex');
        }
        function closeEditModal() {
            document.getElementById('editModal').classList.add('hidden');
            document.getElementById('editModal').classList.remove('flex');
        }
    </script>
@endsection