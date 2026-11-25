@extends('admin.layout')

@section('title', 'Log Aktivitas Survei')

@section('content')
<div class="space-y-5">
    <!-- Statistik Ringkas -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm">
            <p class="text-[11px] font-bold uppercase tracking-wider text-gray-400">Total Laporan Selesai</p>
            <p class="text-2xl font-extrabold text-gray-900 mt-1">{{ number_format($stats['total_laporan']) }}</p>
            <p class="text-[11px] text-gray-400 mt-0.5">Batch pengiriman tugas</p>
        </div>

        <div class="bg-white rounded-xl border border-emerald-200 p-4 shadow-sm">
            <p class="text-[11px] font-bold uppercase tracking-wider text-emerald-600">Total Infrastruktur Dilaporkan</p>
            <p class="text-2xl font-extrabold text-emerald-700 mt-1">{{ number_format($stats['total_infra']) }}</p>
            <p class="text-[11px] text-emerald-600/80 mt-0.5">Akumulasi seluruh titik selesai</p>
        </div>

        <div class="bg-white rounded-xl border border-blue-200 p-4 shadow-sm">
            <p class="text-[11px] font-bold uppercase tracking-wider text-blue-600">Laporan Masuk Hari Ini</p>
            <p class="text-2xl font-extrabold text-blue-700 mt-1">{{ number_format($stats['hari_ini']) }}</p>
            <p class="text-[11px] text-blue-600/80 mt-0.5">{{ now()->translatedFormat('l, d F Y') }}</p>
        </div>
    </div>

    <!-- Filter -->
    <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm">
        <form method="GET" action="{{ route('admin.logs.index') }}" class="grid grid-cols-1 md:grid-cols-12 gap-3 items-center">
            <div class="md:col-span-6">
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Cari nama infrastruktur atau kota/kabupaten dalam log..."
                       class="w-full rounded-xl border border-gray-300 px-3.5 py-2 text-xs focus:border-blue-500 focus:ring-blue-500">
            </div>

            <div class="md:col-span-4">
                <select name="user_id" class="w-full rounded-xl border border-gray-300 px-3 py-2 text-xs focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Semua Surveyor</option>
                    @foreach($surveyors as $surveyor)
                        <option value="{{ $surveyor->id }}" {{ request('user_id') == $surveyor->id ? 'selected' : '' }}>
                            {{ $surveyor->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="md:col-span-2 flex gap-2">
                <button type="submit"
                        class="flex-1 px-3.5 py-2 bg-slate-800 hover:bg-slate-900 text-white text-xs font-bold rounded-xl transition">
                    Filter
                </button>
                @if(request()->hasAny(['search', 'user_id']))
                    <a href="{{ route('admin.logs.index') }}"
                       class="px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold rounded-xl transition">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Daftar Kartu Log Per Surveyor (Terbaru di Paling Atas) -->
    <div class="space-y-4">
        @forelse($logs as $log)
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                <!-- Header Log: Nama Surveyor, Hari, Tanggal & Waktu Penyelesaian -->
                <div class="px-5 py-3.5 bg-slate-50 border-b border-gray-200 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-full bg-blue-600 text-white font-bold text-xs flex items-center justify-center shrink-0 shadow-sm">
                            {{ strtoupper(substr($log->user->name ?? 'S', 0, 1)) }}
                        </div>
                        <div>
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="font-bold text-sm text-gray-900">{{ $log->user->name ?? 'Surveyor' }}</span>
                                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800">
                                    ✓ Menyelesaikan {{ $log->total_infrastructures }} Tugas Survei
                                </span>
                            </div>
                            <p class="text-xs text-gray-500 mt-0.5">
                                📅 {{ $log->created_at->translatedFormat('l, d F Y') }} • 🕒 Pukul {{ $log->created_at->format('H:i') }} WIB
                            </p>
                        </div>
                    </div>

                    <span class="text-[11px] font-semibold text-gray-400 pl-12 sm:pl-0">
                        {{ $log->created_at->diffForHumans() }}
                    </span>
                </div>

                <!-- Daftar Infrastruktur yang Diselesaikan dalam Laporan Ini -->
                <div class="p-4 sm:px-5">
                    <p class="text-[11px] font-bold uppercase tracking-wider text-gray-400 mb-2.5">
                        Daftar Infrastruktur yang Diselesaikan:
                    </p>
                    <div class="divide-y divide-gray-100 border border-gray-200 rounded-xl overflow-hidden">
                        @foreach($log->items as $index => $item)
                            @php $infra = $item->infrastructure; @endphp
                            <div class="px-3.5 py-2.5 bg-white hover:bg-slate-50 flex flex-col sm:flex-row sm:items-center justify-between gap-2 text-xs">
                                <div class="flex items-start gap-2.5">
                                    <span class="font-bold text-gray-400 w-5 shrink-0">{{ $index + 1 }}.</span>
                                    <div>
                                        <span class="font-bold text-gray-900">
                                            {{ $infra->nama_infrastruktur ?? 'Infrastruktur Terhapus' }}
                                        </span>
                                        @if($infra)
                                            <div class="flex flex-wrap items-center gap-2 text-[11px] text-gray-500 mt-0.5">
                                                <span class="px-2 py-0.5 rounded bg-slate-100 text-slate-700 font-bold">
                                                    {{ $infra->kategori ?? '-' }}
                                                </span>
                                                <span>{{ $infra->jenis_infrastruktur ?? '-' }}</span>
                                                <span>• 📍 {{ $infra->kota_kabupaten ?? '-' }}, {{ $infra->provinsi ?? '-' }}</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                @if($infra)
                                    <a href="{{ route('landing', ['focus' => $infra->id]) }}"
                                       class="self-start sm:self-center px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-[11px] font-semibold transition shrink-0">
                                        Lihat Titik ↗
                                    </a>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-xl border border-gray-200 p-10 text-center text-xs text-gray-400 shadow-sm">
                Belum ada laporan penyelesaian tugas dari surveyor.
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div>
        {{ $logs->links() }}
    </div>
</div>
@endsection