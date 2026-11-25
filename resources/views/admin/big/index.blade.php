@extends('admin.layout')

@section('title', 'Status Lapor BIG')

@section('content')
    <!-- Toast Notifikasi AJAX (Floating Pojok Kanan Bawah) -->
    <div id="ajax-toast"
         class="fixed bottom-5 right-5 z-50 hidden items-center gap-2.5 px-4 py-3 rounded-2xl text-xs font-bold shadow-xl border transition-all duration-300">
        <span id="ajax-toast-icon" class="w-5 h-5 rounded-full flex items-center justify-center shrink-0"></span>
        <span id="ajax-toast-text"></span>
    </div>

    <!-- ================= 1. KARTU RINGKASAN STATUS PELAPORAN BIG ================= -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Total Infrastruktur -->
        <div class="pu-card bg-white p-5 rounded-2xl shadow-xs border border-slate-200 border-b-4 border-b-pu-navy">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Total Infrastruktur</p>
                    <p class="text-3xl font-extrabold text-pu-navy mt-1">{{ number_format($stats['total']) }}</p>
                    <p class="text-[11px] text-slate-500 mt-1">Seluruh titik terdaftar</p>
                </div>
                <div class="w-10 h-10 rounded-xl bg-pu-navy/10 text-pu-navy flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Sudah Lapor BIG -->
        @php
            $persenBig = $stats['total'] > 0 ? round(($stats['sudah_big'] / $stats['total']) * 100, 1) : 0;
        @endphp
        <div class="pu-card bg-white p-5 rounded-2xl shadow-xs border border-slate-200 border-b-4 border-b-emerald-500">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <p class="text-[11px] font-bold uppercase tracking-wider text-emerald-600">Sudah Lapor BIG</p>
                    <div class="flex items-baseline gap-2 mt-1">
                        <p id="stat-sudah-big" class="text-3xl font-extrabold text-emerald-700">{{ number_format($stats['sudah_big']) }}</p>
                        <span id="stat-persen-big" class="text-[11px] font-bold px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200">
                            {{ $persenBig }}%
                        </span>
                    </div>
                    <p class="text-[11px] text-slate-500 mt-1">Terkirim ke Badan Informasi Geospasial</p>
                </div>
                <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 border border-emerald-100 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.14 0M1.394 9.393c5.857-5.857 15.355-5.857 21.213 0"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Belum Lapor BIG -->
        <div class="pu-card bg-white p-5 rounded-2xl shadow-xs border border-slate-200 border-b-4 border-b-amber-500">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <p class="text-[11px] font-bold uppercase tracking-wider text-amber-600">Belum Lapor BIG</p>
                    <p id="stat-belum-big" class="text-3xl font-extrabold text-amber-600 mt-1">{{ number_format($stats['belum_big']) }}</p>
                    <p class="text-[11px] text-slate-500 mt-1">Menunggu sinkronisasi pelaporan</p>
                </div>
                <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 border border-amber-100 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="9"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 7v5l3 3"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Siap Lapor (Sudah Survei) -->
        <div class="pu-card bg-white p-5 rounded-2xl shadow-xs border border-slate-200 border-b-4 border-b-sky-500">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <p class="text-[11px] font-bold uppercase tracking-wider text-sky-600">Siap Lapor BIG</p>
                    <p id="stat-siap-lapor" class="text-3xl font-extrabold text-sky-700 mt-1">{{ number_format($stats['siap_lapor']) }}</p>
                    <p class="text-[11px] text-slate-500 mt-1">Sudah disurvei & belum dilaporkan</p>
                </div>
                <div class="w-10 h-10 rounded-xl bg-sky-50 text-sky-600 border border-sky-100 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.3" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- ================= 2. FILTER & PENCARIAN ================= -->
    <div class="bg-white p-5 rounded-2xl shadow-xs border border-slate-200">
        <div class="flex flex-wrap items-center justify-between gap-2 mb-3.5 pb-2.5 border-b border-slate-100">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-pu-navy" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                </svg>
                <h2 class="text-xs font-extrabold uppercase tracking-wider text-pu-navy">Filter Status Pelaporan BIG</h2>
            </div>
            <p class="text-[11px] text-slate-400 hidden sm:block">Gunakan sakelar pada tabel untuk memperbarui status secara instan</p>
        </div>

        <form method="GET" action="{{ route('admin.big.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3.5 items-end text-xs">
            <div class="lg:col-span-4">
                <label class="block text-[11px] font-bold text-slate-600 mb-1">Kata Kunci Pencarian</label>
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="Nama infrastruktur, kode, atau kota..."
                           class="w-full h-9 bg-slate-50 focus:bg-white border border-slate-300 focus:border-pu-navy rounded-xl pl-8 pr-3 text-xs outline-none transition">
                    <svg class="w-3.5 h-3.5 text-slate-400 absolute left-2.5 top-2.5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 104.5 4.5a7.5 7.5 0 0012.15 12.15z"/>
                    </svg>
                </div>
            </div>

            <div class="lg:col-span-2">
                <label class="block text-[11px] font-bold text-slate-600 mb-1">Status Lapor BIG</label>
                <select name="status_big" class="w-full h-9 bg-slate-50 focus:bg-white border border-slate-300 focus:border-pu-navy rounded-xl px-3 text-xs outline-none transition">
                    <option value="">Semua Status BIG</option>
                    <option value="sudah" {{ request('status_big') === 'sudah' ? 'selected' : '' }}>Sudah Lapor BIG</option>
                    <option value="belum" {{ request('status_big') === 'belum' ? 'selected' : '' }}>Belum Lapor BIG</option>
                </select>
            </div>

            <div class="lg:col-span-2">
                <label class="block text-[11px] font-bold text-slate-600 mb-1">Status Survei</label>
                <select name="status_survey" class="w-full h-9 bg-slate-50 focus:bg-white border border-slate-300 focus:border-pu-navy rounded-xl px-3 text-xs outline-none transition">
                    <option value="">Semua Status Survei</option>
                    <option value="sudah" {{ request('status_survey') === 'sudah' ? 'selected' : '' }}>Sudah Survei</option>
                    <option value="belum" {{ request('status_survey') === 'belum' ? 'selected' : '' }}>Belum Survei</option>
                </select>
            </div>

            <div class="lg:col-span-2">
                <label class="block text-[11px] font-bold text-slate-600 mb-1">Kategori PU</label>
                <select name="kategori" class="w-full h-9 bg-slate-50 focus:bg-white border border-slate-300 focus:border-pu-navy rounded-xl px-3 text-xs outline-none transition">
                    <option value="">Semua Kategori</option>
                    @foreach(['BM', 'CK', 'SDA', 'PS', 'SETJEN'] as $kat)
                        <option value="{{ $kat }}" {{ request('kategori') === $kat ? 'selected' : '' }}>{{ $kat }}</option>
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
                @if(request()->hasAny(['search', 'status_big', 'status_survey', 'kategori']))
                    <a href="{{ route('admin.big.index') }}"
                       title="Reset Filter"
                       class="h-9 px-3 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl transition inline-flex items-center justify-center">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- ================= 3. TOOLBAR AKSI MASSAL & TABEL DATA ================= -->
    <form id="bulk-form" method="POST" action="{{ route('admin.big.bulk') }}" class="space-y-4">
        @csrf
        <input type="hidden" name="action" id="bulk-action-input" value="">

        <!-- Toolbar Aksi Massal (Navy PU Style) -->
        <div class="bg-pu-navy text-white px-4 py-3 rounded-2xl shadow-xs flex flex-wrap items-center justify-between gap-3 text-xs">
            <div class="flex items-center gap-2.5">
                <span id="selected-count" class="px-2.5 py-1 rounded-lg bg-pu-gold font-extrabold text-pu-navy text-[11px] tracking-wide">
                    0 dipilih
                </span>
                <span class="text-sky-100/85">Centang baris infrastruktur untuk memperbarui status pelaporan BIG sekaligus.</span>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                <button type="button" onclick="submitBulk('mark_reported')"
                        id="btn-bulk-reported" disabled
                        class="px-3.5 py-2 bg-emerald-600 hover:bg-emerald-500 active:scale-[0.98] text-white text-xs font-bold rounded-xl transition inline-flex items-center gap-1.5 shadow-xs disabled:opacity-40 disabled:cursor-not-allowed cursor-pointer">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span>Tandai Sudah Lapor BIG</span>
                </button>
                <button type="button" onclick="submitBulk('mark_unreported')"
                        id="btn-bulk-unreported" disabled
                        class="px-3.5 py-2 bg-amber-500 hover:bg-amber-400 active:scale-[0.98] text-pu-navy text-xs font-extrabold rounded-xl transition inline-flex items-center gap-1.5 shadow-xs disabled:opacity-40 disabled:cursor-not-allowed cursor-pointer">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    <span>Tandai Belum Lapor</span>
                </button>
            </div>
        </div>

        <!-- Tabel Utama -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
            <div class="px-5 py-3.5 border-b border-slate-100 flex flex-wrap items-center justify-between gap-2 bg-slate-50/70">
                <span class="text-xs font-extrabold text-pu-navy uppercase tracking-wider">Daftar Sinkronisasi Data BIG</span>
                <span class="text-[11px] font-semibold text-slate-500">
                    Menampilkan {{ $infrastructures->firstItem() ?? 0 }}–{{ $infrastructures->lastItem() ?? 0 }} dari {{ $infrastructures->total() }} data
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100 text-xs">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-500 text-[11px] uppercase font-bold tracking-wider">
                        <tr>
                            <th class="px-4 py-3.5 text-center w-10">
                                <input type="checkbox" id="check-all" class="w-4 h-4 rounded accent-[#002359] cursor-pointer">
                            </th>
                            <th class="px-4 py-3.5 text-left">Kode & Nama Infrastruktur</th>
                            <th class="px-4 py-3.5 text-left whitespace-nowrap">Kategori</th>
                            <th class="px-4 py-3.5 text-left">Wilayah</th>
                            <th class="px-4 py-3.5 text-center whitespace-nowrap">Status Survei</th>
                            <th class="px-4 py-3.5 text-center whitespace-nowrap">Status Lapor BIG</th>
                            <th class="px-4 py-3.5 text-center whitespace-nowrap">Switch Lapor</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($infrastructures as $item)
                            <tr class="hover:bg-sky-50/40 transition-colors" id="row-{{ $item->id }}">
                                <td class="px-4 py-3.5 text-center">
                                    <input type="checkbox" name="ids[]" value="{{ $item->id }}"
                                           class="row-checkbox w-4 h-4 rounded accent-[#002359] cursor-pointer">
                                </td>
                                <td class="px-4 py-3.5 min-w-[220px]">
                                    <div class="font-bold text-slate-900 text-xs sm:text-[13px] leading-snug">{{ $item->nama_infrastruktur }}</div>
                                    <div class="text-[11px] text-slate-400 mt-0.5">
                                        {{ $item->kode_infrastruktur ?? 'Tanpa Kode' }} · {{ $item->jenis_infrastruktur ?? '-' }}
                                    </div>
                                </td>
                                <td class="px-4 py-3.5 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 text-[11px] font-extrabold rounded-lg bg-pu-navy/10 text-pu-navy border border-pu-navy/15">
                                        {{ $item->kategori ?? '-' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3.5 min-w-[160px]">
                                    <div class="text-slate-800 font-semibold">{{ $item->kota_kabupaten ?? '-' }}</div>
                                    <div class="text-[11px] text-slate-400 mt-0.5">{{ $item->provinsi ?? '-' }}</div>
                                </td>
                                <td class="px-4 py-3.5 text-center whitespace-nowrap">
                                    @if($item->status_survey)
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-sky-50 text-sky-700 border border-sky-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-sky-500"></span>
                                            Sudah Survei
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-slate-100 text-slate-600 border border-slate-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                                            Belum Survei
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3.5 text-center whitespace-nowrap">
                                    <span id="badge-big-{{ $item->id }}"
                                          class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold border {{ $item->status_lapor_big ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-amber-50 text-amber-800 border-amber-200' }}">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $item->status_lapor_big ? 'bg-emerald-500' : 'bg-amber-500' }}"></span>
                                        <span>{{ $item->status_lapor_big ? 'Sudah Lapor BIG' : 'Belum Lapor' }}</span>
                                    </span>
                                </td>
                                <td class="px-4 py-3.5 text-center whitespace-nowrap">
                                    <label class="relative inline-flex items-center cursor-pointer" title="Ubah Status Lapor BIG">
                                        <input type="checkbox"
                                               class="sr-only peer big-toggle-switch"
                                               data-id="{{ $item->id }}"
                                               data-url="{{ route('admin.big.toggle', $item->id) }}"
                                               {{ $item->status_lapor_big ? 'checked' : '' }}>
                                        <div class="w-10 h-5 bg-slate-300 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-emerald-400/50 rounded-full peer peer-checked:after:translate-x-5 peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-emerald-600 shadow-inner"></div>
                                    </label>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-12 text-center text-slate-400 text-xs">
                                    Tidak ada data infrastruktur yang sesuai dengan filter.
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
    </form>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';
    const toast = document.getElementById('ajax-toast');
    const toastIcon = document.getElementById('ajax-toast-icon');
    const toastText = document.getElementById('ajax-toast-text');
    let toastTimer;

    function showToast(message, isSuccess = true) {
        clearTimeout(toastTimer);
        toast.className = `fixed bottom-5 right-5 z-50 flex items-center gap-2.5 px-4 py-3 rounded-2xl text-xs font-bold shadow-xl border transition-all duration-300 ${
            isSuccess
                ? 'bg-emerald-600 text-white border-emerald-500'
                : 'bg-red-600 text-white border-red-500'
        }`;
        toastIcon.className = `w-5 h-5 rounded-full flex items-center justify-center shrink-0 ${
            isSuccess ? 'bg-white/20 text-white' : 'bg-white/20 text-white'
        }`;
        toastIcon.innerHTML = isSuccess
            ? `<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>`
            : `<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>`;
        toastText.textContent = message;
        toast.classList.remove('hidden');
        toastTimer = setTimeout(() => toast.classList.add('hidden'), 3000);
    }

    // 1. Toggle Switch Instan (AJAX)
    document.querySelectorAll('.big-toggle-switch').forEach(toggle => {
        toggle.addEventListener('change', async function () {
            const id = this.dataset.id;
            const url = this.dataset.url;
            const isChecked = this.checked;
            const badge = document.getElementById(`badge-big-${id}`);

            this.disabled = true;

            try {
                const response = await fetch(url, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ status_lapor_big: isChecked })
                });

                const data = await response.json();

                if (data.success) {
                    if (data.status_lapor_big) {
                        badge.className = 'inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold border bg-emerald-50 text-emerald-700 border-emerald-200';
                        badge.innerHTML = `<span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span><span>Sudah Lapor BIG</span>`;
                    } else {
                        badge.className = 'inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold border bg-amber-50 text-amber-800 border-amber-200';
                        badge.innerHTML = `<span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span><span>Belum Lapor</span>`;
                    }

                    if (data.stats) {
                        document.getElementById('stat-sudah-big').textContent = data.stats.sudah_big.toLocaleString('id-ID');
                        document.getElementById('stat-belum-big').textContent = data.stats.belum_big.toLocaleString('id-ID');
                        document.getElementById('stat-siap-lapor').textContent = data.stats.siap_lapor.toLocaleString('id-ID');

                        const persenEl = document.getElementById('stat-persen-big');
                        if (persenEl && data.stats.total > 0) {
                            const pct = ((data.stats.sudah_big / data.stats.total) * 100).toFixed(1).replace(/\.0$/, '');
                            persenEl.textContent = `${pct}%`;
                        }
                    }

                    showToast(data.message, true);
                } else {
                    this.checked = !isChecked;
                    showToast('Gagal memperbarui status.', false);
                }
            } catch (error) {
                this.checked = !isChecked;
                showToast('Terjadi kesalahan jaringan.', false);
            } finally {
                this.disabled = false;
            }
        });
    });

    // 2. Checkbox Pilih Semua & Aksi Massal
    const checkAll = document.getElementById('check-all');
    const rowCheckboxes = document.querySelectorAll('.row-checkbox');
    const selectedCountEl = document.getElementById('selected-count');
    const btnReported = document.getElementById('btn-bulk-reported');
    const btnUnreported = document.getElementById('btn-bulk-unreported');

    function updateBulkButtons() {
        const checkedCount = document.querySelectorAll('.row-checkbox:checked').length;
        selectedCountEl.textContent = `${checkedCount} dipilih`;
        btnReported.disabled = checkedCount === 0;
        btnUnreported.disabled = checkedCount === 0;

        if (checkAll && rowCheckboxes.length > 0) {
            checkAll.checked = checkedCount === rowCheckboxes.length;
            checkAll.indeterminate = checkedCount > 0 && checkedCount < rowCheckboxes.length;
        }
    }

    if (checkAll) {
        checkAll.addEventListener('change', function () {
            rowCheckboxes.forEach(cb => cb.checked = this.checked);
            updateBulkButtons();
        });
    }

    rowCheckboxes.forEach(cb => {
        cb.addEventListener('change', updateBulkButtons);
    });
});

function submitBulk(actionType) {
    const checkedCount = document.querySelectorAll('.row-checkbox:checked').length;
    if (checkedCount === 0) return;

    const label = actionType === 'mark_reported' ? 'Sudah Lapor BIG' : 'Belum Lapor';
    if (confirm(`Ubah ${checkedCount} data terpilih menjadi "${label}"?`)) {
        document.getElementById('bulk-action-input').value = actionType;
        document.getElementById('bulk-form').submit();
    }
}
</script>
@endpush