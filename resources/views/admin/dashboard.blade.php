@extends('admin.layout')

@section('title', 'Monitoring Data Infrastruktur')

@section('content')
    <!-- ================= 1. KARTU RINGKASAN UTAMA ================= -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <!-- Total Titik -->
        <div class="pu-card bg-white p-5 rounded-2xl shadow-xs border border-slate-200 border-b-4 border-b-pu-navy">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Total Titik Infrastruktur</p>
                    <p class="text-3xl font-extrabold text-pu-navy mt-1">{{ number_format($stats['total']) }}</p>
                    <p class="text-[11px] text-slate-500 mt-1">Seluruh aset terdaftar dalam basis data</p>
                </div>
                <div class="w-10 h-10 rounded-xl bg-pu-navy/10 text-pu-navy flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 21s-6-5.686-6-10a6 6 0 1112 0c0 4.314-6 10-6 10z"/>
                        <circle cx="12" cy="11" r="2.5"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Sudah Disurvei -->
        @php
            $persenTotal = $stats['total'] > 0 ? round(($stats['sudah'] / $stats['total']) * 100, 1) : 0;
        @endphp
        <div class="pu-card bg-white p-5 rounded-2xl shadow-xs border border-slate-200 border-b-4 border-b-emerald-500">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <p class="text-[11px] font-bold text-emerald-600 uppercase tracking-wider">Sudah Disurvei</p>
                    <div class="flex items-baseline gap-2 mt-1">
                        <p class="text-3xl font-extrabold text-emerald-700">{{ number_format($stats['sudah']) }}</p>
                        <span class="text-[11px] font-bold px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200">
                            {{ $persenTotal }}%
                        </span>
                    </div>
                    <p class="text-[11px] text-slate-500 mt-1">Terverifikasi oleh petugas lapangan</p>
                </div>
                <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 border border-emerald-100 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.4" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Belum Disurvei -->
        <div class="pu-card bg-white p-5 rounded-2xl shadow-xs border border-slate-200 border-b-4 border-b-red-500">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <p class="text-[11px] font-bold text-red-600 uppercase tracking-wider">Belum Disurvei</p>
                    <p class="text-3xl font-extrabold text-red-600 mt-1">{{ number_format($stats['belum']) }}</p>
                    <p class="text-[11px] text-slate-500 mt-1">Menunggu jadwal kunjungan lapangan</p>
                </div>
                <div class="w-10 h-10 rounded-xl bg-red-50 text-red-600 border border-red-100 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="9"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- ================= 2. RINCIAN PER KATEGORI PU (BM, CK, SDA, PS, SETJEN) ================= -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3.5">
        @php
            $daftarKategoriPU = [
                'BM'     => 'Bina Marga',
                'CK'     => 'Cipta Karya',
                'SDA'    => 'Sumber Daya Air',
                'PS'     => 'Prasarana Strategis',
                'SETJEN' => 'Sekretariat Jenderal',
            ];
        @endphp

        @foreach($daftarKategoriPU as $kode => $namaKat)
            @php
                $totKat = $stats['per_kategori'][$kode]['total'] ?? 0;
                $sdhKat = $stats['per_kategori'][$kode]['sudah'] ?? 0;
                $persen = $totKat > 0 ? round(($sdhKat / $totKat) * 100) : 0;
            @endphp
            <div class="pu-card bg-white p-4 rounded-2xl shadow-xs border border-slate-200 flex flex-col justify-between">
                <div>
                    <div class="flex justify-between items-center gap-2">
                        <span class="text-[10.5px] font-extrabold px-2.5 py-0.5 rounded-lg bg-pu-navy text-pu-gold tracking-wide">
                            {{ $kode }}
                        </span>
                        <span class="text-xs font-extrabold text-pu-navy">{{ $persen }}%</span>
                    </div>
                    <p class="text-xs text-slate-800 mt-2.5 font-bold leading-snug">{{ $namaKat }}</p>
                    <p class="text-[11px] text-slate-500 mt-0.5">
                        <strong class="text-slate-700">{{ $sdhKat }}</strong> dari {{ $totKat }} titik selesai
                    </p>
                </div>
                <div class="w-full bg-slate-100 rounded-full h-1.5 mt-3 overflow-hidden">
                    <div class="bg-pu-navy h-full rounded-full transition-all duration-500" style="width: {{ $persen }}%"></div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- ================= 3. FILTER MULTI-KRITERIA (4 KOLOM SEJAJAR) ================= -->
    <div class="bg-white p-5 rounded-2xl shadow-xs border border-slate-200">
        <div class="flex items-center gap-2 mb-3.5 pb-2.5 border-b border-slate-100">
            <svg class="w-4 h-4 text-pu-navy" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
            </svg>
            <h2 class="text-xs font-extrabold uppercase tracking-wider text-pu-navy">Filter Data Survei</h2>
        </div>

        <form method="GET" action="{{ route('admin.dashboard') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3.5">
            <!-- Baris 1 -->
            <div>
                <label class="block text-[11px] font-bold text-slate-600 mb-1">Kata Kunci Pencarian</label>
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Nama objek, jenis, catatan..."
                           class="w-full h-9 bg-slate-50 focus:bg-white border border-slate-300 focus:border-pu-navy rounded-xl pl-8 pr-3 text-xs outline-none transition">
                    <svg class="w-3.5 h-3.5 text-slate-400 absolute left-2.5 top-2.5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 104.5 4.5a7.5 7.5 0 0012.15 12.15z"/>
                    </svg>
                </div>
            </div>

            <div>
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

            <div>
                <label class="block text-[11px] font-bold text-slate-600 mb-1">Status Survei</label>
                <select name="status_survei" class="w-full h-9 bg-slate-50 focus:bg-white border border-slate-300 focus:border-pu-navy rounded-xl px-3 text-xs outline-none transition">
                    <option value="">Semua Status</option>
                    <option value="sudah" {{ request('status_survei') == 'sudah' ? 'selected' : '' }}>Sudah Survei</option>
                    <option value="belum" {{ request('status_survei') == 'belum' ? 'selected' : '' }}>Belum Survei</option>
                </select>
            </div>

            <div>
                <label class="block text-[11px] font-bold text-slate-600 mb-1">Provinsi</label>
                <select name="provinsi" class="w-full h-9 bg-slate-50 focus:bg-white border border-slate-300 focus:border-pu-navy rounded-xl px-3 text-xs outline-none transition">
                    <option value="">Semua Provinsi</option>
                    @foreach($daftarProvinsi as $prov)
                        <option value="{{ $prov }}" {{ request('provinsi') == $prov ? 'selected' : '' }}>{{ $prov }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Baris 2 -->
            <div>
                <label class="block text-[11px] font-bold text-slate-600 mb-1">Kota / Kabupaten</label>
                <select name="kota_kabupaten" class="w-full h-9 bg-slate-50 focus:bg-white border border-slate-300 focus:border-pu-navy rounded-xl px-3 text-xs outline-none transition">
                    <option value="">Semua Kota</option>
                    @foreach($daftarKota as $kota)
                        <option value="{{ $kota }}" {{ request('kota_kabupaten') == $kota ? 'selected' : '' }}>{{ $kota }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-[11px] font-bold text-slate-600 mb-1">Petugas Surveyor</label>
                <select name="surveyed_by" class="w-full h-9 bg-slate-50 focus:bg-white border border-slate-300 focus:border-pu-navy rounded-xl px-3 text-xs outline-none transition">
                    <option value="">Semua Petugas</option>
                    @foreach($daftarSurveyor as $srv)
                        <option value="{{ $srv->id }}" {{ request('surveyed_by') == $srv->id ? 'selected' : '' }}>{{ $srv->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="sm:col-span-2 flex items-end justify-end gap-2">
                <a href="{{ route('admin.dashboard') }}"
                   class="h-9 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold px-4 rounded-xl text-xs transition inline-flex items-center gap-1.5 whitespace-nowrap">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    <span>Reset</span>
                </a>
                <button type="submit"
                        class="h-9 bg-pu-navy hover:bg-pu-gold text-white hover:text-pu-navy font-bold px-5 rounded-xl text-xs shadow-xs transition inline-flex items-center gap-1.5 whitespace-nowrap">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.4" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 104.5 4.5a7.5 7.5 0 0012.15 12.15z"/>
                    </svg>
                    <span>Terapkan Filter</span>
                </button>
            </div>
        </form>
    </div>

    <!-- ================= 4. TABEL DATA INFRASTRUKTUR ================= -->
    <div class="bg-white rounded-2xl shadow-xs border border-slate-200 overflow-hidden">
        <div class="px-5 py-3.5 border-b border-slate-100 flex flex-wrap items-center justify-between gap-2 bg-slate-50/70">
            <span class="text-xs font-extrabold text-pu-navy uppercase tracking-wider">Daftar Titik Infrastruktur</span>
            <span class="text-[11px] font-semibold text-slate-500">
                Menampilkan {{ $infrastructures->firstItem() ?? 0 }}–{{ $infrastructures->lastItem() ?? 0 }} dari {{ $infrastructures->total() }} data
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead class="bg-slate-50 border-b border-slate-200 text-[11px] font-bold uppercase tracking-wider text-slate-500">
                    <tr>
                        <th class="py-3.5 px-4">Infrastruktur</th>
                        <th class="py-3.5 px-4 whitespace-nowrap">Kategori</th>
                        <th class="py-3.5 px-4">Wilayah & Koordinat</th>
                        <th class="py-3.5 px-4 whitespace-nowrap">Status</th>
                        <th class="py-3.5 px-4 whitespace-nowrap">Petugas & Waktu Survei</th>
                        <th class="py-3.5 px-4 text-center whitespace-nowrap">Foto</th>
                        <th class="py-3.5 px-4 text-right whitespace-nowrap">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($infrastructures as $item)
                        <tr class="hover:bg-sky-50/40 transition-colors">
                            <td class="py-3.5 px-4 min-w-[200px]">
                                <p class="font-bold text-slate-900 text-xs sm:text-[13px] leading-snug">{{ $item->nama_infrastruktur }}</p>
                                <p class="text-[11px] text-slate-500 mt-0.5">{{ $item->jenis_infrastruktur ?? 'Jenis belum diisi' }}</p>
                            </td>
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 text-[11px] font-extrabold rounded-lg bg-pu-navy/10 text-pu-navy border border-pu-navy/15">
                                    {{ $item->kategori }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 min-w-[170px]">
                                <p class="font-semibold text-slate-800">{{ $item->kota_kabupaten }}, {{ $item->provinsi }}</p>
                                <p class="text-[11px] text-slate-400 mt-0.5">{{ $item->latitude }}, {{ $item->longitude }}</p>
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
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                @if($item->surveyor)
                                    <p class="font-bold text-slate-800">{{ $item->surveyor->name }}</p>
                                    <p class="text-[11px] text-slate-500 mt-0.5">
                                        {{ $item->surveyed_at ? $item->surveyed_at->format('d M Y, H:i') . ' WIB' : '-' }}
                                    </p>
                                @else
                                    <span class="text-slate-400 italic text-[11px]">Belum ada aktivitas</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-md text-[11px] font-bold {{ $item->photos->count() > 0 ? 'bg-blue-50 text-blue-700 border border-blue-200' : 'bg-slate-100 text-slate-400' }}">
                                    {{ $item->photos->count() }}/5
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                <button type="button" onclick='openDetailModal(@json($item))'
                                        class="bg-pu-navy hover:bg-pu-gold text-white hover:text-pu-navy px-3 py-1.5 rounded-xl font-bold text-[11px] transition inline-flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    <span>Detail</span>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-10 text-center text-slate-400">
                                Tidak ada data infrastruktur yang sesuai dengan kriteria filter.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination Kustom Rapi & Ukuran Kecil -->
        @if($infrastructures->hasPages())
            <div class="px-5 py-3 border-t border-slate-100 bg-slate-50/50 flex flex-col sm:flex-row items-center justify-between gap-2.5 text-[11px]">
                <p class="text-slate-500 font-medium">
                    Halaman <span class="font-bold text-pu-navy">{{ $infrastructures->currentPage() }}</span> dari <span class="font-bold text-slate-700">{{ $infrastructures->lastPage() }}</span>
                    <span class="text-slate-400 mx-1">·</span>
                    Total <span class="font-bold text-slate-700">{{ number_format($infrastructures->total()) }}</span> titik
                </p>

                <nav class="inline-flex items-center gap-1" aria-label="Pagination">
                    {{-- Tombol Previous --}}
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

                    {{-- Nomor Halaman Kompak --}}
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

                    {{-- Tombol Next --}}
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

    <!-- ================= 5. MODAL DETAIL SURVEI & VERIFIKASI GPS (FULL BLUR) ================= -->
    <div id="detailModal" onclick="if(event.target === this) closeDetailModal()"
         style="margin-top: 0 !important;"
         class="fixed inset-0 w-screen h-screen bg-slate-950/65 backdrop-blur-sm hidden items-center justify-center z-50 p-4">
        <div class="bg-white rounded-2xl max-w-2xl w-full max-h-[88vh] overflow-y-auto p-6 shadow-2xl border-t-4 border-pu-navy">
            <div class="flex justify-between items-start border-b border-slate-100 pb-3.5 mb-4">
                <div>
                    <span id="det_kategori" class="text-[11px] font-extrabold px-2.5 py-0.5 rounded-lg bg-pu-navy text-pu-gold"></span>
                    <h3 id="det_nama" class="text-lg font-extrabold text-pu-navy mt-1.5 leading-snug"></h3>
                    <p id="det_wilayah" class="text-xs text-slate-500 mt-0.5"></p>
                </div>
                <button type="button" onclick="closeDetailModal()" class="p-1.5 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs bg-slate-50 p-4 rounded-2xl border border-slate-200/80 mb-5">
                <div class="space-y-2.5">
                    <div>
                        <p class="text-[11px] font-semibold text-slate-400">Petugas Surveyor</p>
                        <p id="det_surveyor" class="font-bold text-slate-900 text-sm"></p>
                    </div>
                    <div>
                        <p class="text-[11px] font-semibold text-slate-400">Waktu Penyelesaian Survei</p>
                        <p id="det_waktu" class="font-semibold text-slate-800"></p>
                    </div>
                    <div>
                        <p class="text-[11px] font-semibold text-slate-400">Jenis Infrastruktur</p>
                        <p id="det_jenis" class="font-semibold text-slate-800"></p>
                    </div>
                    <div>
                        <p class="text-[11px] font-semibold text-slate-400">Nama Hasil Survei</p>
                        <p id="det_nama_hasil_survey" class="font-semibold text-slate-800"></p>
                    </div>
                </div>

                <div class="space-y-2.5">
                    <div>
                        <p class="text-[11px] font-semibold text-slate-400">Koordinat Awal Database</p>
                        <p id="det_coord_awal" class="font-semibold text-slate-800 bg-white px-2.5 py-1 rounded-lg border border-slate-200 inline-block mt-0.5"></p>
                    </div>
                    <div>
                        <p class="text-[11px] font-semibold text-slate-400">Koordinat Aktual Survei & Akurasi Jarak</p>
                        <div id="det_coord_survei" class="font-semibold text-slate-800 mt-0.5"></div>
                    </div>
                    <div>
                        <p class="text-[11px] font-semibold text-slate-400">Catatan / Alasan Verifikasi</p>
                        <p id="det_catatan" class="text-slate-700 italic bg-white p-2 rounded-lg border border-slate-200/80 mt-0.5"></p>
                    </div>
                </div>
            </div>

            <div class="flex flex-wrap justify-between items-center gap-2 mb-3">
                <h4 class="font-extrabold text-sm text-pu-navy">Dokumentasi Foto Lapangan</h4>
                <a id="btn_admin_download_zip" href="#"
                   class="hidden inline-flex items-center gap-1.5 bg-pu-navy hover:bg-pu-gold text-white hover:text-pu-navy text-xs font-bold px-3.5 py-1.5 rounded-xl shadow-xs transition">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5 5-5M12 15V3"/>
                    </svg>
                    <span id="btn_admin_download_zip_label">Unduh Semua Foto (.ZIP)</span>
                </a>
            </div>
            <div id="det_photos_grid" class="grid grid-cols-1 sm:grid-cols-2 gap-4"></div>
        </div>
    </div>

    <script>
        function calculateDistanceMeters(lat1, lon1, lat2, lon2) {
            const R = 6371000;
            const toRad = (deg) => deg * (Math.PI / 180);
            const dLat = toRad(lat2 - lat1);
            const dLon = toRad(lon2 - lon1);
            const a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
                      Math.cos(toRad(lat1)) * Math.cos(toRad(lat2)) *
                      Math.sin(dLon / 2) * Math.sin(dLon / 2);
            const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
            return R * c;
        }

        function formatDistanceBadge(meters) {
            const isNear = meters <= 100;
            const textJarak = meters >= 1000 ? `${(meters / 1000).toFixed(2)} km` : `${Math.round(meters)} meter`;
            const badgeClass = isNear ? 'bg-emerald-50 text-emerald-800 border-emerald-200' : 'bg-red-50 text-red-800 border-red-200';
            const iconSvg = isNear
                ? `<svg class="w-3 h-3 text-emerald-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>`
                : `<svg class="w-3 h-3 text-red-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>`;
            const labelStatus = isNear ? 'Sesuai Lokasi' : 'Jauh dari Titik';

            return `<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md border text-[10px] font-bold ${badgeClass}">
                        ${iconSvg}
                        <span>${textJarak} (${labelStatus})</span>
                    </span>`;
        }

        function openDetailModal(item) {
            document.getElementById('det_kategori').textContent = item.kategori;
            document.getElementById('det_nama').textContent = item.nama_infrastruktur;
            document.getElementById('det_wilayah').textContent = `${item.kota_kabupaten}, ${item.provinsi}`;
            document.getElementById('det_surveyor').textContent = item.surveyor ? item.surveyor.name : 'Belum disurvei';
            document.getElementById('det_waktu').textContent = item.surveyed_at ? new Date(item.surveyed_at).toLocaleString('id-ID') : '-';
            document.getElementById('det_jenis').textContent = item.jenis_infrastruktur || '-';
            document.getElementById('det_nama_hasil_survey').textContent = item.nama_hasil_survey || '-';

            const origLat = parseFloat(item.latitude);
            const origLng = parseFloat(item.longitude);
            document.getElementById('det_coord_awal').textContent = `${item.latitude}, ${item.longitude}`;

            const photosWithGps = (item.photos || []).filter(p => p.photo_latitude && p.photo_longitude);
            const coordSurveiEl = document.getElementById('det_coord_survei');
            const btnZip = document.getElementById('btn_admin_download_zip');
            const btnZipLabel = document.getElementById('btn_admin_download_zip_label');

            if (photosWithGps.length > 0 && !isNaN(origLat) && !isNaN(origLng)) {
                coordSurveiEl.innerHTML = photosWithGps.map((p, idx) => {
                    const pLat = parseFloat(p.photo_latitude);
                    const pLng = parseFloat(p.photo_longitude);
                    const jarakMeter = calculateDistanceMeters(origLat, origLng, pLat, pLng);

                    return `
                        <div class="mb-2 pb-2 border-b border-slate-200 last:border-b-0 last:mb-0 last:pb-0">
                            <div class="text-emerald-700 font-bold text-xs">Foto #${idx + 1}: ${pLat.toFixed(7)}, ${pLng.toFixed(7)}</div>
                            <div class="mt-1">${formatDistanceBadge(jarakMeter)}</div>
                        </div>
                    `;
                }).join('');
            } else {
                coordSurveiEl.innerHTML = `<span class="text-slate-400 font-normal italic">Koordinat GPS foto tidak tersedia</span>`;
            }

            document.getElementById('det_catatan').textContent = item.alasan_verifikasi || '-';

            const grid = document.getElementById('det_photos_grid');
            grid.innerHTML = '';

            if (item.photos && item.photos.length > 0) {
                item.photos.forEach((photo, idx) => {
                    const hasGps = photo.photo_latitude && photo.photo_longitude;
                    const gpsInfo = hasGps ? `${photo.photo_latitude}, ${photo.photo_longitude}` : 'Koordinat tidak terekam';
                    const sourceBadge = photo.source_type === 'camera' ? 'Kamera GPS' : 'Upload File';

                    let jarakHtml = '';
                    if (hasGps && !isNaN(origLat) && !isNaN(origLng)) {
                        const jarakMeter = calculateDistanceMeters(
                            origLat,
                            origLng,
                            parseFloat(photo.photo_latitude),
                            parseFloat(photo.photo_longitude)
                        );
                        jarakHtml = `<div class="pt-1">${formatDistanceBadge(jarakMeter)}</div>`;
                    }

                    grid.innerHTML += `
                        <div class="border border-slate-200 rounded-2xl overflow-hidden bg-white shadow-xs">
                            <a href="/storage/${photo.file_path}" target="_blank" class="block overflow-hidden">
                                <img src="/storage/${photo.file_path}" class="w-full h-44 object-cover hover:scale-105 transition-transform duration-300">
                            </a>
                            <div class="p-3.5 text-xs space-y-1.5">
                                <div class="flex justify-between items-center">
                                    <span class="font-extrabold text-pu-navy">Foto #${idx + 1}</span>
                                    <span class="text-[10px] px-2 py-0.5 rounded-md bg-slate-100 text-slate-700 font-bold border border-slate-200">${sourceBadge}</span>
                                </div>
                                <p class="text-[11px] font-medium text-slate-600 truncate">${gpsInfo}</p>
                                ${jarakHtml}
                                <a href="/survey/photo/${photo.id}/download" 
                                   class="mt-2 flex items-center justify-center gap-1.5 bg-pu-navy hover:bg-pu-gold text-white hover:text-pu-navy font-bold py-2 rounded-xl transition">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5 5-5M12 15V3"/>
                                    </svg>
                                    <span>Unduh Foto Asli</span>
                                </a>
                            </div>
                        </div>
                    `;
                });
            } else {
                grid.innerHTML = `<p class="text-xs text-slate-400 col-span-2 py-8 text-center border border-dashed border-slate-200 rounded-2xl">Belum ada foto dokumentasi untuk titik ini.</p>`;
            }

            if (item.photos && item.photos.length > 0) {
                btnZip.href = `/survey/${item.id}/download-zip`;
                btnZipLabel.textContent = `Unduh Semua (${item.photos.length} Foto .ZIP)`;
                btnZip.classList.remove('hidden');
            } else {
                btnZip.classList.add('hidden');
            }

            const modal = document.getElementById('detailModal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeDetailModal() {
            const modal = document.getElementById('detailModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }
    </script>
@endsection