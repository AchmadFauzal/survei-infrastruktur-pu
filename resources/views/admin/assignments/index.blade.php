@extends('admin.layout')

@section('title', 'List Penugasan Surveyor')

@section('content')
    <!-- Header & Tombol Tambah Penugasan -->
    <div class="flex flex-wrap justify-between items-center gap-3 mb-5">
        <div>
            <h1 class="text-xl font-bold text-gray-900">👷 List Penugasan Survei Lapangan</h1>
            <p class="text-xs text-gray-500">Pantau pembagian tugas surveyor, tambah penugasan baru, ubah petugas, atau batalkan tugas.</p>
        </div>
        <button type="button" onclick="openCreateAssignmentModal()"
                class="bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold px-4 py-2.5 rounded-xl shadow flex items-center gap-1.5">
            ➕ Tambah Penugasan Baru
        </button>
    </div>

    <!-- Kartu Ringkasan Penugasan -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-5">
        <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-200">
            <p class="text-[11px] font-bold text-gray-500 uppercase">Total Titik Ditugaskan</p>
            <p class="text-2xl font-extrabold text-gray-900 mt-1">{{ $stats['total_tugas'] }}</p>
        </div>
        <div class="bg-white p-4 rounded-xl shadow-sm border-l-4 border-amber-500">
            <p class="text-[11px] font-bold text-amber-600 uppercase">Sedang Dikerjakan (Belum)</p>
            <p class="text-2xl font-extrabold text-amber-700 mt-1">{{ $stats['belum_selesai'] }}</p>
        </div>
        <div class="bg-white p-4 rounded-xl shadow-sm border-l-4 border-green-500">
            <p class="text-[11px] font-bold text-green-600 uppercase">Tugas Selesai Survei</p>
            <p class="text-2xl font-extrabold text-green-700 mt-1">{{ $stats['sudah_selesai'] }}</p>
        </div>
        <div class="bg-white p-4 rounded-xl shadow-sm border-l-4 border-slate-400">
            <p class="text-[11px] font-bold text-slate-500 uppercase">Titik Belum Dibagi</p>
            <p class="text-2xl font-extrabold text-slate-700 mt-1">{{ $stats['belum_dibagi'] }}</p>
        </div>
    </div>

    <!-- Filter List Penugasan -->
    <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-200 mb-4">
        <form method="GET" action="{{ route('admin.assignments.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 text-xs">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama infrastruktur / kota..."
                   class="border border-gray-300 rounded-lg px-3 py-2">

            <select name="surveyor_id" class="border border-gray-300 rounded-lg px-3 py-2">
                <option value="">Semua Petugas Surveyor</option>
                @foreach($surveyors as $srv)
                    <option value="{{ $srv->id }}" {{ request('surveyor_id') == $srv->id ? 'selected' : '' }}>
                        {{ $srv->name }}
                    </option>
                @endforeach
            </select>

            <select name="status_survei" class="border border-gray-300 rounded-lg px-3 py-2">
                <option value="">Semua Progres Tugas</option>
                <option value="belum" {{ request('status_survei') == 'belum' ? 'selected' : '' }}>Belum Disurvei</option>
                <option value="sudah" {{ request('status_survei') == 'sudah' ? 'selected' : '' }}>Sudah Disurvei</option>
            </select>

            <div class="flex gap-2">
                <button type="submit" class="flex-1 bg-slate-800 hover:bg-slate-900 text-white font-semibold py-2 px-4 rounded-lg">
                    Filter
                </button>
                <a href="{{ route('admin.assignments.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold py-2 px-3 rounded-lg">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Action Bar Batalkan Tugas Massal -->
    <div class="bg-slate-800 text-white p-3 rounded-xl shadow mb-4 flex flex-wrap justify-between items-center gap-3 text-xs">
        <div class="flex items-center gap-2">
            <span id="selected-assign-badge" class="bg-amber-400 text-slate-900 font-extrabold px-2.5 py-1 rounded-lg">
                0 dipilih
            </span>
            <span class="text-slate-300">Centang baris tugas untuk membatalkan penugasan sekaligus:</span>
        </div>

        <form id="bulkUnassignForm" method="POST" action="{{ route('admin.assignments.bulk_unassign') }}">
            @csrf
            <div id="bulk-unassign-inputs"></div>
            <button type="button" onclick="submitBulkUnassign()"
                    class="bg-red-600 hover:bg-red-500 text-white font-bold px-3.5 py-1.5 rounded-lg">
                ✕ Batalkan Penugasan Terpilih
            </button>
        </form>
    </div>

    <!-- Tabel List Penugasan -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead class="bg-gray-50 border-b border-gray-200 uppercase text-gray-600">
                    <tr>
                        <th class="py-3.5 px-4 w-10 text-center">
                            <input type="checkbox" id="selectAllAssign" class="w-4 h-4 accent-blue-600 rounded cursor-pointer">
                        </th>
                        <th class="py-3.5 px-4">Petugas Surveyor</th>
                        <th class="py-3.5 px-4">Target Infrastruktur</th>
                        <th class="py-3.5 px-4">Wilayah & Koordinat</th>
                        <th class="py-3.5 px-4">Progres Survei</th>
                        <th class="py-3.5 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($assignments as $item)
                        <tr class="hover:bg-blue-50/40 transition">
                            <td class="py-3 px-4 text-center">
                                <input type="checkbox" value="{{ $item->id }}" class="assign-checkbox w-4 h-4 accent-blue-600 rounded cursor-pointer">
                            </td>
                            <td class="py-3 px-4">
                                <div class="flex items-center gap-2">
                                    <span class="w-7 h-7 rounded-full bg-indigo-100 text-indigo-700 font-bold flex items-center justify-center">
                                        {{ strtoupper(substr($item->assignedSurveyor->name ?? '?', 0, 1)) }}
                                    </span>
                                    <div>
                                        <p class="font-bold text-gray-900">{{ $item->assignedSurveyor->name ?? '-' }}</p>
                                        <p class="text-[11px] text-gray-500">{{ $item->assignedSurveyor->email ?? '' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3 px-4">
                                <span class="px-2 py-0.5 text-[10px] font-bold rounded bg-slate-200 text-slate-800">{{ $item->kategori }}</span>
                                <p class="font-bold text-gray-900 text-sm mt-1">{{ $item->nama_infrastruktur }}</p>
                                <p class="text-gray-500">{{ $item->jenis_infrastruktur ?? '-' }}</p>
                            </td>
                            <td class="py-3 px-4">
                                <p class="font-semibold text-gray-800">{{ $item->kota_kabupaten }}, {{ $item->provinsi }}</p>
                                <p class="text-gray-400 font-mono text-[11px]">{{ $item->latitude }}, {{ $item->longitude }}</p>
                            </td>
                            <td class="py-3 px-4">
                                @if($item->status_survei === 'sudah')
                                    <span class="px-2.5 py-1 font-bold rounded-full bg-green-100 text-green-700">✓ Selesai Disurvei</span>
                                    <p class="text-[11px] text-gray-500 mt-1">
                                        {{ $item->surveyed_at ? $item->surveyed_at->format('d M Y H:i') : '' }}
                                    </p>
                                @else
                                    <span class="px-2.5 py-1 font-bold rounded-full bg-amber-100 text-amber-800">⏳ Menunggu Survei</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-right space-x-1 whitespace-nowrap">
                                <button type="button" onclick='openEditAssignmentModal(@json($item))'
                                        class="bg-amber-500 hover:bg-amber-600 text-white px-2.5 py-1 rounded font-semibold">
                                    Edit
                                </button>
                                <form method="POST" action="{{ route('admin.assignments.destroy', $item) }}" class="inline"
                                      onsubmit="return confirm('Batalkan penugasan untuk {{ $item->nama_infrastruktur }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-2.5 py-1 rounded font-semibold">
                                        Hapus Tugas
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-gray-500">
                                Belum ada penugasan surveyor. Klik tombol <strong>+ Tambah Penugasan Baru</strong> untuk mulai membagi tugas.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-gray-200">
            {{ $assignments->links() }}
        </div>
    </div>

    <!-- MODAL TAMBAH PENUGASAN BARU (PILIH SURVEYOR + CENTANG TITIK) -->
    <div id="createAssignmentModal" class="fixed inset-0 bg-black/60 hidden items-center justify-center z-50 p-4">
        <div class="bg-white rounded-2xl max-w-2xl w-full p-6 shadow-2xl max-h-[90vh] flex flex-col">
            <div class="flex justify-between items-center border-b pb-3 mb-4">
                <h3 class="text-base font-bold text-gray-900">➕ Tambah Penugasan Survei Baru</h3>
                <button type="button" onclick="closeCreateAssignmentModal()" class="text-gray-400 hover:text-gray-700 text-xl font-bold">×</button>
            </div>

            <form method="POST" action="{{ route('admin.assignments.store') }}" class="space-y-4 text-xs flex-1 overflow-y-auto pr-1">
                @csrf
                <div>
                    <label class="block font-bold text-gray-700 mb-1">1. Pilih Petugas Surveyor *</label>
                    <select name="assigned_to" required class="w-full border border-gray-300 rounded-xl px-3 py-2.5 bg-blue-50/50 font-semibold">
                        <option value="">-- Pilih Akun Surveyor --</option>
                        @foreach($surveyors as $srv)
                            <option value="{{ $srv->id }}">{{ $srv->name }} ({{ $srv->email }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <div class="flex justify-between items-center mb-1.5">
                        <label class="font-bold text-gray-700">2. Pilih Titik Infrastruktur yang Belum Ditugaskan ({{ $unassignedInfrastructures->count() }} tersedia) *</label>
                        <input type="text" id="filterUnassignedInput" onkeyup="filterUnassignedList()" placeholder="Cari cepat nama / kota..."
                               class="border border-gray-300 rounded-lg px-2.5 py-1 text-[11px] w-48">
                    </div>

                    <div id="unassignedListContainer" class="border border-gray-300 rounded-xl max-h-64 overflow-y-auto divide-y divide-gray-200 bg-gray-50">
                        @forelse($unassignedInfrastructures as $infra)
                            <label class="unassigned-row flex items-center gap-3 p-2.5 hover:bg-blue-50 cursor-pointer">
                                <input type="checkbox" name="infrastructure_ids[]" value="{{ $infra->id }}" class="w-4 h-4 accent-blue-600 rounded">
                                <div class="flex-1">
                                    <p class="font-bold text-gray-900">
                                        <span class="px-1.5 py-0.5 text-[10px] rounded bg-slate-200 mr-1">{{ $infra->kategori }}</span>
                                        {{ $infra->nama_infrastruktur }}
                                    </p>
                                    <p class="text-[11px] text-gray-500">{{ $infra->kota_kabupaten }}, {{ $infra->provinsi }} ({{ $infra->latitude }}, {{ $infra->longitude }})</p>
                                </div>
                            </label>
                        @empty
                            <p class="p-6 text-center text-gray-500">Semua titik infrastruktur sudah memiliki petugas surveyor.</p>
                        @endforelse
                    </div>
                </div>

                <div class="flex justify-end gap-2 pt-3 border-t">
                    <button type="button" onclick="closeCreateAssignmentModal()" class="px-4 py-2 bg-gray-200 rounded-lg font-semibold">Batal</button>
                    <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-bold shadow">
                        Simpan Penugasan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL EDIT PENUGASAN -->
    <div id="editAssignmentModal" class="fixed inset-0 bg-black/60 hidden items-center justify-center z-50 p-4">
        <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl">
            <div class="flex justify-between items-center border-b pb-3 mb-4">
                <h3 class="text-base font-bold text-gray-900">✏️ Edit Detail Penugasan</h3>
                <button type="button" onclick="closeEditAssignmentModal()" class="text-gray-400 hover:text-gray-700 text-xl font-bold">×</button>
            </div>

            <form id="editAssignmentForm" method="POST" class="space-y-3 text-xs">
                @csrf
                @method('PUT')

                <div>
                    <label class="block font-semibold text-gray-700 mb-1">Petugas Surveyor (Kosongkan untuk membatalkan tugas)</label>
                    <select name="assigned_to" id="edit_assign_surveyor" class="w-full border border-gray-300 rounded-lg px-3 py-2 font-semibold">
                        <option value="">-- Lepas / Belum Ditugaskan --</option>
                        @foreach($surveyors as $srv)
                            <option value="{{ $srv->id }}">{{ $srv->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block font-semibold text-gray-700 mb-1">Nama Objek Infrastruktur *</label>
                    <input type="text" name="nama_infrastruktur" id="edit_assign_nama" required class="w-full border border-gray-300 rounded-lg px-3 py-2">
                </div>

                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block font-semibold text-gray-700 mb-1">Kota / Kabupaten *</label>
                        <input type="text" name="kota_kabupaten" id="edit_assign_kota" required class="w-full border border-gray-300 rounded-lg px-3 py-2">
                    </div>
                    <div>
                        <label class="block font-semibold text-gray-700 mb-1">Provinsi *</label>
                        <input type="text" name="provinsi" id="edit_assign_provinsi" required class="w-full border border-gray-300 rounded-lg px-3 py-2">
                    </div>
                </div>

                <div>
                    <label class="block font-semibold text-gray-700 mb-1">Status Survei *</label>
                    <select name="status_survei" id="edit_assign_status" required class="w-full border border-gray-300 rounded-lg px-3 py-2">
                        <option value="belum">Menunggu Survei (Belum)</option>
                        <option value="sudah">Selesai Disurvei (Sudah)</option>
                    </select>
                </div>

                <div class="flex justify-end gap-2 pt-3 border-t">
                    <button type="button" onclick="closeEditAssignmentModal()" class="px-4 py-2 bg-gray-200 rounded-lg font-semibold">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg font-bold">Simpan Perubahan</button>
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
                alert('⚠️ Silakan centang minimal 1 penugasan yang ingin dibatalkan.');
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