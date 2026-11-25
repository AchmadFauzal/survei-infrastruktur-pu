@extends('surveyor.layout')

@section('title', 'Penugasan & Rute Survei')
@section('page_title', '🧭 Daftar Penugasan & Pemilihan Rute')
@section('page_subtitle', 'Centang titik yang ingin dikunjungi untuk menyusun list rute tercepat di Peta Utama GIS.')

@section('header_actions')
    <a href="{{ route('surveyor.dashboard') }}"
       class="bg-slate-200 hover:bg-slate-300 text-slate-800 px-3.5 py-2 rounded-xl text-xs font-bold transition">
        ← Riwayat & Laporan
    </a>
@endsection

@section('content')
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

        <!-- KOLOM KIRI (7 Kolom): Daftar Penugasan & Pemilihan Titik -->
        <div class="lg:col-span-7 space-y-6">

            <!-- Kotak Daftar Penugasan Resmi dari Admin -->
            <section class="bg-amber-50 border border-amber-300 rounded-2xl p-5 shadow-sm">
                <div class="flex flex-wrap justify-between items-center gap-2 mb-3 border-b border-amber-200 pb-3">
                    <div>
                        <h2 class="font-bold text-sm text-amber-950 flex items-center gap-1.5">
                            📌 Daftar Penugasan Survei dari Admin
                        </h2>
                        <p class="text-[11px] text-amber-800">Klik <strong>Survei</strong> untuk langsung zoom ke 1 titik, atau centang beberapa titik di bawah untuk membuat rute.</p>
                    </div>
                    <span class="bg-amber-600 text-white text-xs font-bold px-3 py-1 rounded-full">
                        {{ $assignedTasks->count() }} Lokasi
                    </span>
                </div>

                @if($assignedTasks->count() > 0)
                    <div class="space-y-2.5 max-h-60 overflow-y-auto pr-1 text-xs">
                        @foreach($assignedTasks as $task)
                            <div class="bg-white p-3 rounded-xl border border-amber-200 flex justify-between items-center gap-3 shadow-sm">
                                <div class="min-w-0">
                                    <div class="flex items-center gap-1.5">
                                        <span class="px-1.5 py-0.5 text-[10px] font-bold rounded bg-amber-200 text-amber-950">{{ $task->kategori }}</span>
                                        <span class="font-bold text-gray-900 truncate">{{ $task->nama_objek }}</span>
                                    </div>
                                    <p class="text-gray-600 text-[11px] mt-0.5">{{ $task->kota }}, {{ $task->provinsi }} • <span class="font-mono text-[10px] text-gray-400">{{ $task->latitude }}, {{ $task->longitude }}</span></p>
                                </div>
                                <a href="{{ route('landing', ['focus_id' => $task->id, 'filter' => 'assigned']) }}"
                                   class="bg-amber-600 hover:bg-amber-700 text-white px-3.5 py-1.5 rounded-lg font-bold shrink-0 shadow-sm transition">
                                    Survei
                                </a>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-xs text-amber-900 bg-white/80 p-4 rounded-xl border border-amber-200 text-center">
                        Tidak ada penugasan khusus saat ini. Anda dapat memilih titik secara bebas dari tab <strong>Semua Belum Survei</strong> di bawah.
                    </p>
                @endif
            </section>

            <!-- Kotak Pemilihan Titik untuk Masuk ke List Rute -->
            <section class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="bg-slate-900 text-white px-5 py-3.5 flex flex-wrap justify-between items-center gap-2">
                    <h3 class="font-bold text-sm">☑️ Pilih Titik untuk Masuk ke List Rute</h3>
                    <div class="flex gap-2 text-xs">
                        <button type="button" onclick="selectAllAssignedForRoute()"
                                class="bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold px-2.5 py-1 rounded-lg">
                            Centang Semua Tugas ({{ $assignedTasks->count() }})
                        </button>
                        <button type="button" onclick="clearRouteSelection()"
                                class="bg-slate-700 hover:bg-slate-600 text-white font-semibold px-2.5 py-1 rounded-lg">
                            Kosongkan
                        </button>
                    </div>
                </div>

                <div class="p-4 space-y-3">
                    <!-- Tab Filter & Pencarian -->
                    <div class="flex flex-col sm:flex-row gap-2">
                        <div class="flex gap-1.5 text-xs shrink-0">
                            <button type="button" id="tab-route-assigned" onclick="switchRouteSourceTab('assigned')"
                                    class="py-2 px-3 rounded-xl font-bold bg-amber-600 text-white">
                                Tugas Saya ({{ $assignedTasks->count() }})
                            </button>
                            <button type="button" id="tab-route-all" onclick="switchRouteSourceTab('all')"
                                    class="py-2 px-3 rounded-xl font-bold bg-gray-100 border border-gray-300 text-gray-700 hover:bg-gray-200">
                                Semua Belum Survei ({{ $allUnsurveyed->count() }})
                            </button>
                        </div>
                        <input type="text" id="searchRoutePoints" onkeyup="filterRoutePointList()"
                               placeholder="🔍 Cari nama infrastruktur / kota..."
                               class="flex-1 border border-gray-300 rounded-xl px-3 py-2 text-xs">
                    </div>

                    <!-- Daftar Checklist Titik -->
                    <div id="route-points-checklist" class="border border-gray-200 rounded-xl divide-y divide-gray-100 max-h-80 overflow-y-auto text-xs">
                        @forelse($allUnsurveyed as $pt)
                            @php $isAssigned = ($pt->assigned_to == auth()->id()); @endphp
                            <label class="route-point-row flex items-start gap-3 p-3 hover:bg-blue-50/70 cursor-pointer transition"
                                   data-assigned="{{ $isAssigned ? '1' : '0' }}">
                                <input type="checkbox" value="{{ $pt->id }}"
                                       class="route-point-cb mt-0.5 w-4 h-4 accent-blue-600 rounded shrink-0"
                                       {{ $isAssigned ? 'checked' : '' }}
                                       onchange="syncRouteBasket()">
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-1.5 flex-wrap">
                                        <span class="px-1.5 py-0.5 text-[10px] font-bold rounded bg-slate-200 text-slate-800">{{ $pt->kategori }}</span>
                                        @if($isAssigned)
                                            <span class="px-1.5 py-0.5 text-[10px] font-bold rounded bg-amber-100 text-amber-800 border border-amber-300">Tugas Saya</span>
                                        @endif
                                        <span class="font-bold text-gray-900">{{ $pt->nama_objek }}</span>
                                    </div>
                                    <p class="text-[11px] text-gray-500 mt-0.5">{{ $pt->kota }}, {{ $pt->provinsi }}</p>
                                </div>
                            </label>
                        @empty
                            <p class="p-6 text-center text-gray-400">Semua titik infrastruktur sudah selesai disurvei.</p>
                        @endforelse
                    </div>
                </div>
            </section>
        </div>

        <!-- KOLOM KANAN (5 Kolom): LIST RUTE YANG AKAN DITEMPUH (OTOMATIS TERISI) -->
        <aside class="lg:col-span-5 sticky top-20">
            <div class="bg-white rounded-2xl shadow-md border-2 border-blue-600 overflow-hidden">
                <div class="bg-blue-900 text-white px-5 py-4 flex justify-between items-center">
                    <div>
                        <h3 class="font-bold text-sm flex items-center gap-2">
                            🗺️ List Rute yang Akan Ditempuh
                        </h3>
                        <p class="text-[11px] text-blue-200">Otomatis terisi dari titik yang Anda centang</p>
                    </div>
                    <span id="route-selected-badge" class="bg-amber-400 text-blue-950 font-extrabold text-xs px-3 py-1 rounded-full">
                        0 Titik
                    </span>
                </div>

                <div class="p-4 space-y-4">
                    <!-- Opsi Mulai dari GPS -->
                    <label class="flex items-center gap-2.5 bg-blue-50 border border-blue-200 p-3 rounded-xl cursor-pointer text-xs">
                        <input type="checkbox" id="useMyGpsStart" checked class="w-4 h-4 accent-blue-600 rounded">
                        <div>
                            <span class="font-bold text-blue-950 block">🚩 Mulai dari Lokasi GPS Saya Sekarang</span>
                            <span class="text-[11px] text-blue-700">Sistem akan menghitung jalur jalan tercepat dari posisi Anda</span>
                        </div>
                    </label>

                    <!-- Daftar Otomatis Titik Terpilih -->
                    <div>
                        <div class="flex justify-between items-center mb-1.5">
                            <span class="text-[11px] font-bold uppercase text-gray-500">Daftar Tujuan Terpilih:</span>
                            <button type="button" onclick="clearRouteSelection()" class="text-[11px] text-red-600 hover:underline font-semibold">
                                Hapus Semua
                            </button>
                        </div>

                        <div id="selected-route-basket" class="border border-gray-200 rounded-xl bg-gray-50 max-h-72 overflow-y-auto divide-y divide-gray-200 text-xs">
                            <!-- Diisi otomatis oleh JavaScript -->
                        </div>
                    </div>

                    <!-- Tombol Pindah ke Peta Utama GIS -->
                    <button type="button" id="btn-launch-gis-route" onclick="openRouteOnMainMap()"
                            class="w-full bg-blue-700 hover:bg-blue-800 disabled:bg-gray-300 disabled:cursor-not-allowed text-white font-bold py-3.5 px-5 rounded-xl shadow-lg transition text-sm flex items-center justify-center gap-2">
                        🚀 Tampilkan Rute Tercepat di Peta GIS →
                    </button>
                </div>
            </div>
        </aside>

    </div>
@endsection

@push('scripts')
<script>
    const allUnsurveyedData = @json($allUnsurveyed);
    let activeRouteTab = 'assigned';

    function switchRouteSourceTab(tab) {
        activeRouteTab = tab;
        const btnAssigned = document.getElementById('tab-route-assigned');
        const btnAll = document.getElementById('tab-route-all');

        if (tab === 'assigned') {
            btnAssigned.className = 'py-2 px-3 rounded-xl font-bold bg-amber-600 text-white';
            btnAll.className = 'py-2 px-3 rounded-xl font-bold bg-gray-100 border border-gray-300 text-gray-700 hover:bg-gray-200';
        } else {
            btnAll.className = 'py-2 px-3 rounded-xl font-bold bg-blue-600 text-white';
            btnAssigned.className = 'py-2 px-3 rounded-xl font-bold bg-gray-100 border border-gray-300 text-gray-700 hover:bg-gray-200';
        }
        filterRoutePointList();
    }

    function filterRoutePointList() {
        const keyword = document.getElementById('searchRoutePoints').value.toLowerCase();
        document.querySelectorAll('.route-point-row').forEach(row => {
            const isAssigned = row.dataset.assigned === '1';
            const matchesTab = (activeRouteTab === 'all') || isAssigned;
            const matchesKeyword = row.textContent.toLowerCase().includes(keyword);
            row.style.display = (matchesTab && matchesKeyword) ? 'flex' : 'none';
        });
    }

    // Sinkronisasi otomatis antara checkbox di kiri dan keranjang "List Rute yang Akan Ditempuh" di kanan
    function syncRouteBasket() {
        const checkedIds = Array.from(document.querySelectorAll('.route-point-cb:checked')).map(cb => Number(cb.value));
        const selectedPoints = allUnsurveyedData.filter(pt => checkedIds.includes(Number(pt.id)));

        const badge = document.getElementById('route-selected-badge');
        const basket = document.getElementById('selected-route-basket');
        const launchBtn = document.getElementById('btn-launch-gis-route');

        badge.textContent = `${selectedPoints.length} Titik`;
        launchBtn.disabled = (selectedPoints.length === 0);

        if (selectedPoints.length === 0) {
            basket.innerHTML = `
                <div class="p-6 text-center text-gray-400">
                    Belum ada titik yang dicentang.<br>Silakan centang titik di sebelah kiri untuk memasukkannya ke list rute.
                </div>
            `;
            return;
        }

        basket.innerHTML = selectedPoints.map((pt, idx) => `
            <div class="p-2.5 bg-white flex items-center justify-between gap-2">
                <div class="flex items-center gap-2 min-w-0">
                    <span class="w-5 h-5 rounded-full bg-blue-700 text-white text-[10px] font-bold flex items-center justify-center shrink-0">
                        ${idx + 1}
                    </span>
                    <div class="min-w-0">
                        <p class="font-bold text-gray-900 truncate">${pt.nama_objek}</p>
                        <p class="text-[10px] text-gray-500 truncate">${pt.kategori} • ${pt.kota}, ${pt.provinsi}</p>
                    </div>
                </div>
                <button type="button" onclick="uncheckRoutePoint(${pt.id})"
                        title="Keluarkan dari list rute"
                        class="text-red-500 hover:bg-red-50 font-bold px-2 py-1 rounded text-xs shrink-0">
                    ✕
                </button>
            </div>
        `).join('');
    }

    function uncheckRoutePoint(id) {
        const cb = document.querySelector(`.route-point-cb[value="${id}"]`);
        if (cb) {
            cb.checked = false;
            syncRouteBasket();
        }
    }

    function selectAllAssignedForRoute() {
        document.querySelectorAll('.route-point-row').forEach(row => {
            row.querySelector('.route-point-cb').checked = (row.dataset.assigned === '1');
        });
        switchRouteSourceTab('assigned');
        syncRouteBasket();
    }

    function clearRouteSelection() {
        document.querySelectorAll('.route-point-cb').forEach(cb => { cb.checked = false; });
        syncRouteBasket();
    }

    function openRouteOnMainMap() {
        const selectedIds = Array.from(document.querySelectorAll('.route-point-cb:checked')).map(cb => cb.value);
        if (selectedIds.length === 0) {
            alert('⚠️ Silakan centang minimal 1 titik untuk ditampilkan rutenya di peta.');
            return;
        }

        const useGps = document.getElementById('useMyGpsStart').checked ? '1' : '0';
        window.location.href = `{{ route('landing') }}?route_ids=${selectedIds.join(',')}&use_gps=${useGps}`;
    }

    // Inisialisasi saat halaman dimuat
    switchRouteSourceTab('assigned');
    syncRouteBasket();
</script>
@endpush