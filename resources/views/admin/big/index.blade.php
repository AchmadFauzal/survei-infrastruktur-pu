@extends('admin.layout')

@section('title', 'Status Lapor BIG')

@section('content')
<div class="space-y-5">
    <!-- Toast Notifikasi AJAX (Floating Pojok Kanan Bawah) -->
    <div id="ajax-toast"
         class="fixed bottom-5 right-5 z-50 hidden items-center gap-2.5 px-4 py-3 rounded-xl text-xs font-semibold shadow-lg transition-all duration-300">
        <span id="ajax-toast-icon"></span>
        <span id="ajax-toast-text"></span>
    </div>

    <!-- Kartu Statistik Ringkas -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm">
            <p class="text-[11px] font-bold uppercase tracking-wider text-gray-400">Total Infrastruktur</p>
            <p class="text-2xl font-extrabold text-gray-900 mt-1">{{ number_format($stats['total']) }}</p>
            <p class="text-[11px] text-gray-400 mt-0.5">Seluruh titik terdaftar</p>
        </div>

        <div class="bg-white rounded-xl border border-emerald-200 p-4 shadow-sm">
            <p class="text-[11px] font-bold uppercase tracking-wider text-emerald-600">Sudah Lapor BIG</p>
            <p id="stat-sudah-big" class="text-2xl font-extrabold text-emerald-700 mt-1">{{ number_format($stats['sudah_big']) }}</p>
            <p class="text-[11px] text-emerald-600/80 mt-0.5">Terkirim ke Badan Informasi Geospasial</p>
        </div>

        <div class="bg-white rounded-xl border border-amber-200 p-4 shadow-sm">
            <p class="text-[11px] font-bold uppercase tracking-wider text-amber-600">Belum Lapor BIG</p>
            <p id="stat-belum-big" class="text-2xl font-extrabold text-amber-700 mt-1">{{ number_format($stats['belum_big']) }}</p>
            <p class="text-[11px] text-amber-600/80 mt-0.5">Menunggu pelaporan</p>
        </div>

        <div class="bg-white rounded-xl border border-blue-200 p-4 shadow-sm">
            <p class="text-[11px] font-bold uppercase tracking-wider text-blue-600">Siap Lapor (Sudah Survei)</p>
            <p id="stat-siap-lapor" class="text-2xl font-extrabold text-blue-700 mt-1">{{ number_format($stats['siap_lapor']) }}</p>
            <p class="text-[11px] text-blue-600/80 mt-0.5">Terverifikasi lapangan & belum lapor</p>
        </div>
    </div>

    <!-- Filter & Pencarian -->
    <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm">
        <form method="GET" action="{{ route('admin.big.index') }}" class="grid grid-cols-1 md:grid-cols-12 gap-3 items-center">
            <div class="md:col-span-4">
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Cari nama infrastruktur, kode, atau kota/kabupaten..."
                       class="w-full rounded-xl border border-gray-300 px-3.5 py-2 text-xs focus:border-blue-500 focus:ring-blue-500">
            </div>

            <div class="md:col-span-2">
                <select name="status_big" class="w-full rounded-xl border border-gray-300 px-3 py-2 text-xs focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Semua Status BIG</option>
                    <option value="sudah" {{ request('status_big') === 'sudah' ? 'selected' : '' }}>Sudah Lapor BIG</option>
                    <option value="belum" {{ request('status_big') === 'belum' ? 'selected' : '' }}>Belum Lapor BIG</option>
                </select>
            </div>

            <div class="md:col-span-2">
                <select name="status_survey" class="w-full rounded-xl border border-gray-300 px-3 py-2 text-xs focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Semua Status Survei</option>
                    <option value="sudah" {{ request('status_survey') === 'sudah' ? 'selected' : '' }}>Sudah Survei</option>
                    <option value="belum" {{ request('status_survey') === 'belum' ? 'selected' : '' }}>Belum Survei</option>
                </select>
            </div>

            <div class="md:col-span-2">
                <select name="kategori" class="w-full rounded-xl border border-gray-300 px-3 py-2 text-xs focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Semua Kategori</option>
                    @foreach(['BM', 'CK', 'PS', 'SDA', 'SETJEN'] as $kat)
                        <option value="{{ $kat }}" {{ request('kategori') === $kat ? 'selected' : '' }}>{{ $kat }}</option>
                    @endforeach
                </select>
            </div>

            <div class="md:col-span-2 flex gap-2">
                <button type="submit"
                        class="flex-1 px-3.5 py-2 bg-slate-800 hover:bg-slate-900 text-white text-xs font-bold rounded-xl transition">
                    Filter
                </button>
                @if(request()->hasAny(['search', 'status_big', 'status_survey', 'kategori']))
                    <a href="{{ route('admin.big.index') }}"
                       title="Reset Filter"
                       class="px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold rounded-xl transition">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Form Bulk Action & Tabel Data -->
    <form id="bulk-form" method="POST" action="{{ route('admin.big.bulk') }}">
        @csrf
        <input type="hidden" name="action" id="bulk-action-input" value="">

        <!-- Toolbar Aksi Massal -->
        <div class="flex flex-wrap items-center justify-between gap-3 bg-slate-50 border border-gray-200 rounded-t-xl px-4 py-3">
            <div class="flex items-center gap-2 text-xs text-gray-600">
                <span id="selected-count" class="px-2 py-0.5 rounded-md bg-slate-200 font-bold text-gray-900">0</span>
                <span>data dipilih</span>
            </div>

            <div class="flex items-center gap-2">
                <button type="button" onclick="submitBulk('mark_reported')"
                        id="btn-bulk-reported" disabled
                        class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded-lg transition disabled:opacity-40 disabled:cursor-not-allowed">
                    ✓ Tandai Sudah Lapor BIG
                </button>
                <button type="button" onclick="submitBulk('mark_unreported')"
                        id="btn-bulk-unreported" disabled
                        class="px-3 py-1.5 bg-amber-500 hover:bg-amber-600 text-white text-xs font-semibold rounded-lg transition disabled:opacity-40 disabled:cursor-not-allowed">
                    ✕ Tandai Belum Lapor
                </button>
            </div>
        </div>

        <!-- Tabel Utama -->
        <div class="bg-white border-x border-b border-gray-200 rounded-b-xl shadow-sm overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-xs">
                <thead class="bg-gray-50 text-gray-600 text-[11px] uppercase font-bold tracking-wider">
                    <tr>
                        <th class="px-4 py-3 text-left w-10">
                            <input type="checkbox" id="check-all" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        </th>
                        <th class="px-4 py-3 text-left">Kode & Nama Infrastruktur</th>
                        <th class="px-4 py-3 text-left">Kategori</th>
                        <th class="px-4 py-3 text-left">Wilayah</th>
                        <th class="px-4 py-3 text-center">Status Survei</th>
                        <th class="px-4 py-3 text-center">Status Lapor BIG</th>
                        <th class="px-4 py-3 text-center">Switch Lapor</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($infrastructures as $item)
                        <tr class="hover:bg-slate-50/80 transition-colors" id="row-{{ $item->id }}">
                            <td class="px-4 py-3">
                                <input type="checkbox" name="ids[]" value="{{ $item->id }}"
                                       class="row-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            </td>
                            <td class="px-4 py-3">
                                <div class="font-bold text-gray-900 text-xs">{{ $item->nama_infrastruktur }}</div>
                                <div class="text-[11px] text-gray-400 mt-0.5">
                                    {{ $item->kode_infrastruktur ?? 'Tanpa Kode' }} • {{ $item->jenis_infrastruktur ?? '-' }}
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <span class="px-2.5 py-1 text-[11px] font-bold rounded-md bg-slate-100 text-slate-700">
                                    {{ $item->kategori ?? '-' }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="text-gray-800 font-semibold">{{ $item->kota_kabupaten ?? '-' }}</div>
                                <div class="text-[11px] text-gray-400">{{ $item->provinsi ?? '-' }}</div>
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($item->status_survey)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-blue-100 text-blue-800">
                                        Sudah Survei
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-gray-100 text-gray-600">
                                        Belum Survei
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span id="badge-big-{{ $item->id }}"
                                      class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold {{ $item->status_lapor_big ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                                    {{ $item->status_lapor_big ? 'Sudah Lapor BIG' : 'Belum Lapor' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <label class="relative inline-flex items-center cursor-pointer" title="Ubah Status Lapor BIG">
                                    <input type="checkbox"
                                           class="sr-only peer big-toggle-switch"
                                           data-id="{{ $item->id }}"
                                           data-url="{{ route('admin.big.toggle', $item->id) }}"
                                           {{ $item->status_lapor_big ? 'checked' : '' }}>
                                    <div class="w-10 h-5 bg-gray-300 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-emerald-400 rounded-full peer peer-checked:after:translate-x-5 peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-emerald-600"></div>
                                </label>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-10 text-center text-gray-400 text-xs">
                                Tidak ada data infrastruktur yang sesuai dengan filter.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </form>

    <!-- Pagination -->
    <div>
        {{ $infrastructures->links() }}
    </div>
</div>
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
        toast.className = `fixed bottom-5 right-5 z-50 flex items-center gap-2.5 px-4 py-3 rounded-xl text-xs font-semibold shadow-lg transition-all duration-300 ${
            isSuccess ? 'bg-emerald-600 text-white' : 'bg-red-600 text-white'
        }`;
        toastIcon.textContent = isSuccess ? '✓' : '✕';
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
                        badge.textContent = 'Sudah Lapor BIG';
                        badge.className = 'inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-100 text-emerald-800';
                    } else {
                        badge.textContent = 'Belum Lapor';
                        badge.className = 'inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-amber-100 text-amber-800';
                    }

                    if (data.stats) {
                        document.getElementById('stat-sudah-big').textContent = data.stats.sudah_big.toLocaleString('id-ID');
                        document.getElementById('stat-belum-big').textContent = data.stats.belum_big.toLocaleString('id-ID');
                        document.getElementById('stat-siap-lapor').textContent = data.stats.siap_lapor.toLocaleString('id-ID');
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
        selectedCountEl.textContent = checkedCount;
        btnReported.disabled = checkedCount === 0;
        btnUnreported.disabled = checkedCount === 0;
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