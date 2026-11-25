@extends('admin.layout')

@section('title', 'Log Aktivitas Survei')

@section('content')
    <!-- ================= 1. KARTU RINGKASAN AKTIVITAS SURVEI ================= -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <!-- Total Laporan Selesai -->
        <div class="pu-card bg-white p-5 rounded-2xl shadow-xs border border-slate-200 border-b-4 border-b-pu-navy">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Total Laporan Selesai</p>
                    <p class="text-3xl font-extrabold text-pu-navy mt-1">{{ number_format($stats['total_laporan']) }}</p>
                    <p class="text-[11px] text-slate-500 mt-1">Akumulasi batch pengiriman tugas</p>
                </div>
                <div class="w-10 h-10 rounded-xl bg-pu-navy/10 text-pu-navy flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Total Infrastruktur Dilaporkan -->
        <div class="pu-card bg-white p-5 rounded-2xl shadow-xs border border-slate-200 border-b-4 border-b-emerald-500">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <p class="text-[11px] font-bold uppercase tracking-wider text-emerald-600">Infrastruktur Dilaporkan</p>
                    <p class="text-3xl font-extrabold text-emerald-700 mt-1">{{ number_format($stats['total_infra']) }}</p>
                    <p class="text-[11px] text-slate-500 mt-1">Total titik selesai diverifikasi</p>
                </div>
                <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 border border-emerald-100 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.4" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Laporan Masuk Hari Ini -->
        <div class="pu-card bg-white p-5 rounded-2xl shadow-xs border border-slate-200 border-b-4 border-b-sky-500">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <p class="text-[11px] font-bold uppercase tracking-wider text-sky-600">Laporan Masuk Hari Ini</p>
                    <p class="text-3xl font-extrabold text-sky-700 mt-1">{{ number_format($stats['hari_ini']) }}</p>
                    <p class="text-[11px] text-slate-500 mt-1">{{ now()->translatedFormat('l, d F Y') }}</p>
                </div>
                <div class="w-10 h-10 rounded-xl bg-sky-50 text-sky-600 border border-sky-100 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="9"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 7v5l3 3"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- ================= 2. FILTER LOG AKTIVITAS ================= -->
    <div class="bg-white p-5 rounded-2xl shadow-xs border border-slate-200">
        <div class="flex flex-wrap items-center justify-between gap-2 mb-3.5 pb-2.5 border-b border-slate-100">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-pu-navy" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                </svg>
                <h2 class="text-xs font-extrabold uppercase tracking-wider text-pu-navy">Filter Riwayat Aktivitas Survei</h2>
            </div>
            <p class="text-[11px] text-slate-400 hidden sm:block">Menampilkan laporan penyelesaian survei secara kronologis</p>
        </div>

        <form method="GET" action="{{ route('admin.logs.index') }}" class="grid grid-cols-1 md:grid-cols-12 gap-3.5 items-end text-xs">
            <div class="md:col-span-6">
                <label class="block text-[11px] font-bold text-slate-600 mb-1">Kata Kunci Infrastruktur / Wilayah</label>
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="Cari nama infrastruktur atau kota/kabupaten dalam log..."
                           class="w-full h-9 bg-slate-50 focus:bg-white border border-slate-300 focus:border-pu-navy rounded-xl pl-8 pr-3 text-xs outline-none transition">
                    <svg class="w-3.5 h-3.5 text-slate-400 absolute left-2.5 top-2.5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 104.5 4.5a7.5 7.5 0 0012.15 12.15z"/>
                    </svg>
                </div>
            </div>

            <div class="md:col-span-4">
                <label class="block text-[11px] font-bold text-slate-600 mb-1">Petugas Surveyor</label>
                <select name="user_id" class="w-full h-9 bg-slate-50 focus:bg-white border border-slate-300 focus:border-pu-navy rounded-xl px-3 text-xs outline-none transition">
                    <option value="">Semua Petugas Surveyor</option>
                    @foreach($surveyors as $surveyor)
                        <option value="{{ $surveyor->id }}" {{ request('user_id') == $surveyor->id ? 'selected' : '' }}>
                            {{ $surveyor->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="md:col-span-2 flex items-center gap-2">
                <button type="submit"
                        class="flex-1 h-9 bg-pu-navy hover:bg-pu-gold text-white hover:text-pu-navy text-xs font-bold rounded-xl shadow-xs transition inline-flex items-center justify-center gap-1.5 cursor-pointer">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.4" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 104.5 4.5a7.5 7.5 0 0012.15 12.15z"/>
                    </svg>
                    <span>Filter</span>
                </button>
                @if(request()->hasAny(['search', 'user_id']))
                    <a href="{{ route('admin.logs.index') }}"
                       class="h-9 px-3 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl transition inline-flex items-center justify-center">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- ================= 3. DAFTAR KARTU LOG PER SURVEYOR ================= -->
    <div class="space-y-4">
        @forelse($logs as $log)
            <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden transition-shadow hover:shadow-md">
                <!-- Header Log: Nama Surveyor, Tanggal & Waktu Penyelesaian -->
                <div class="px-5 py-4 bg-slate-50/80 border-b border-slate-200/80 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div class="flex items-center gap-3.5">
                        <div class="w-10 h-10 rounded-xl bg-pu-navy text-pu-gold font-extrabold text-sm flex items-center justify-center shrink-0 shadow-xs">
                            {{ strtoupper(substr($log->user->name ?? 'S', 0, 1)) }}
                        </div>
                        <div>
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="font-extrabold text-sm text-pu-navy">{{ $log->user->name ?? 'Surveyor' }}</span>
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    <svg class="w-3 h-3 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2.6" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    <span>Menyelesaikan {{ $log->total_infrastructures }} Tugas Survei</span>
                                </span>
                            </div>

                            <div class="flex flex-wrap items-center gap-x-3 gap-y-1 text-[11px] text-slate-500 mt-1">
                                <span class="inline-flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                                        <line x1="16" y1="2" x2="16" y2="6"/>
                                        <line x1="8" y1="2" x2="8" y2="6"/>
                                        <line x1="3" y1="10" x2="21" y2="10"/>
                                    </svg>
                                    <span>{{ $log->created_at->translatedFormat('l, d F Y') }}</span>
                                </span>
                                <span class="text-slate-300">·</span>
                                <span class="inline-flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <circle cx="12" cy="12" r="9"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 7v5l3 3"/>
                                    </svg>
                                    <span>Pukul {{ $log->created_at->format('H:i') }} WIB</span>
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="pl-13 sm:pl-0 self-start sm:self-center">
                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg bg-white border border-slate-200 text-[11px] font-semibold text-slate-500">
                            {{ $log->created_at->diffForHumans() }}
                        </span>
                    </div>
                </div>

                <!-- Daftar Infrastruktur yang Diselesaikan dalam Laporan Ini -->
                <div class="p-4 sm:p-5">
                    <p class="text-[11px] font-extrabold uppercase tracking-wider text-slate-400 mb-2.5">
                        Daftar Infrastruktur yang Diselesaikan
                    </p>
                    <div class="divide-y divide-slate-100 border border-slate-200 rounded-xl overflow-hidden">
                        @foreach($log->items as $index => $item)
                            @php $infra = $item->infrastructure; @endphp
                            <div class="px-4 py-3 bg-white hover:bg-sky-50/40 flex flex-col sm:flex-row sm:items-center justify-between gap-2.5 text-xs transition-colors">
                                <div class="flex items-start gap-3">
                                    <span class="w-6 h-6 rounded-lg bg-slate-100 text-slate-600 font-extrabold text-[11px] flex items-center justify-center shrink-0 mt-0.5">
                                        {{ $index + 1 }}
                                    </span>
                                    <div>
                                        <span class="font-bold text-slate-900 text-xs sm:text-[13px] leading-snug block">
                                            {{ $infra->nama_infrastruktur ?? 'Infrastruktur Terhapus' }}
                                        </span>
                                        @if($infra)
                                            <div class="flex flex-wrap items-center gap-2 text-[11px] text-slate-500 mt-1">
                                                <span class="px-2 py-0.5 rounded-md bg-pu-navy/10 text-pu-navy border border-pu-navy/15 font-extrabold text-[10.5px]">
                                                    {{ $infra->kategori ?? '-' }}
                                                </span>
                                                <span class="font-medium text-slate-600">{{ $infra->jenis_infrastruktur ?? 'Jenis belum diisi' }}</span>
                                                <span class="text-slate-300">·</span>
                                                <span class="inline-flex items-center gap-1 text-slate-500">
                                                    <svg class="w-3 h-3 text-slate-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 21s-6-5.686-6-10a6 6 0 1112 0c0 4.314-6 10-6 10z"/>
                                                        <circle cx="12" cy="11" r="2"/>
                                                    </svg>
                                                    <span>{{ $infra->kota_kabupaten ?? '-' }}, {{ $infra->provinsi ?? '-' }}</span>
                                                </span>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                @if($infra)
                                    <a href="{{ route('landing', ['focus' => $infra->id]) }}"
                                       class="group self-start sm:self-center px-3 py-1.5 rounded-xl bg-pu-navy hover:bg-pu-gold text-white hover:text-pu-navy text-[11px] font-bold transition inline-flex items-center gap-1.5 shrink-0 shadow-2xs">
                                        <span>Lihat Titik</span>
                                        <svg class="w-3 h-3 transition-transform duration-200 group-hover:translate-x-0.5 group-hover:-translate-y-0.5" fill="none" stroke="currentColor" stroke-width="2.4" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M7 17L17 7M7 7h10v10"/>
                                        </svg>
                                    </a>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-2xl border border-slate-200 p-12 text-center shadow-xs">
                <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="9"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 7v5l3 3"/>
                    </svg>
                </div>
                <p class="text-xs font-bold text-slate-700">Belum ada log aktivitas survei</p>
                <p class="text-[11px] text-slate-400 mt-1">Riwayat laporan penyelesaian tugas oleh petugas surveyor akan tercatat otomatis di sini.</p>
            </div>
        @endforelse
    </div>

    <!-- ================= 4. PAGINATION KUSTOM KOMPAK ================= -->
    @if($logs->hasPages())
        <div class="bg-white px-5 py-3 rounded-2xl border border-slate-200 shadow-xs flex flex-col sm:flex-row items-center justify-between gap-2.5 text-[11px]">
            <p class="text-slate-500 font-medium">
                Halaman <span class="font-bold text-pu-navy">{{ $logs->currentPage() }}</span> dari <span class="font-bold text-slate-700">{{ $logs->lastPage() }}</span>
                <span class="text-slate-400 mx-1">·</span>
                Total <span class="font-bold text-slate-700">{{ number_format($logs->total()) }}</span> batch laporan
            </p>

            <nav class="inline-flex items-center gap-1" aria-label="Pagination">
                @if($logs->onFirstPage())
                    <span class="w-7 h-7 inline-flex items-center justify-center rounded-lg border border-slate-200 bg-slate-100/70 text-slate-300 cursor-not-allowed">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.4" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                        </svg>
                    </span>
                @else
                    <a href="{{ $logs->previousPageUrl() }}"
                       class="w-7 h-7 inline-flex items-center justify-center rounded-lg border border-slate-200 bg-white hover:bg-pu-navy text-slate-600 hover:text-white transition">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.4" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                        </svg>
                    </a>
                @endif

                @php
                    $current = $logs->currentPage();
                    $last    = $logs->lastPage();
                    $start   = max(1, $current - 2);
                    $end     = min($last, $current + 2);
                @endphp

                @if($start > 1)
                    <a href="{{ $logs->url(1) }}" class="min-w-[28px] h-7 px-2 inline-flex items-center justify-center rounded-lg border border-slate-200 bg-white hover:bg-slate-100 text-slate-600 font-bold transition">1</a>
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
                        <a href="{{ $logs->url($page) }}"
                           class="min-w-[28px] h-7 px-2 inline-flex items-center justify-center rounded-lg border border-slate-200 bg-white hover:bg-slate-100 text-slate-600 font-bold transition">
                            {{ $page }}
                        </a>
                    @endif
                @endfor

                @if($end < $last)
                    @if($end < $last - 1)
                        <span class="px-1 text-slate-400">...</span>
                    @endif
                    <a href="{{ $logs->url($last) }}" class="min-w-[28px] h-7 px-2 inline-flex items-center justify-center rounded-lg border border-slate-200 bg-white hover:bg-slate-100 text-slate-600 font-bold transition">{{ $last }}</a>
                @endif

                @if($logs->hasMorePages())
                    <a href="{{ $logs->nextPageUrl() }}"
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
@endsection