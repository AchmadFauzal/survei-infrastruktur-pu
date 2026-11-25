@extends('admin.layout')

@section('title', 'Manajemen Data & Penugasan Survei')

@section('content')
    <!-- Header & Tombol Tambah Data -->
    <div class="flex flex-wrap justify-between items-center gap-3 mb-5">
        <div>
            <h1 class="text-xl font-bold text-gray-900">🗂️ Manajemen Data Infrastruktur & Penugasan</h1>
            <p class="text-xs text-gray-500">Tambah, ubah, hapus massal, atau tugaskan titik infrastruktur kepada petugas surveyor.</p>
        </div>
        <button type="button" onclick="openAddModal()"
                class="bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold px-4 py-2.5 rounded-xl shadow flex items-center gap-1.5">
            ➕ Tambah Data Infrastruktur
        </button>
    </div>

    <!-- Filter Pencarian -->
    <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-200 mb-4">
        <form method="GET" action="{{ route('admin.infrastructures.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 text-xs">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama / kota / provinsi..."
                   class="border border-gray-300 rounded-lg px-3 py-2">

            <select name="kategori" class="border border-gray-300 rounded-lg px-3 py-2">
                <option value="">Semua Kategori</option>
                <option value="BM" {{ request('kategori') == 'BM' ? 'selected' : '' }}>BM (Bina Marga)</option>
                <option value="CK" {{ request('kategori') == 'CK' ? 'selected' : '' }}>CK (Cipta Karya)</option>
                <option value="SDA" {{ request('kategori') == 'SDA' ? 'selected' : '' }}>SDA (Sumber Daya Air)</option>
                <option value="PS" {{ request('kategori') == 'PS' ? 'selected' : '' }}>PS (Prasarana Strategis)</option>
            </select>

            <select name="status_survei" class="border border-gray-300 rounded-lg px-3 py-2">
                <option value="">Semua Status</option>
                <option value="belum" {{ request('status_survei') == 'belum' ? 'selected' : '' }}>Belum Survei</option>
                <option value="sudah" {{ request('status_survei') == 'sudah' ? 'selected' : '' }}>Sudah Survei</option>
            </select>

            <select name="assigned_to" class="border border-gray-300 rounded-lg px-3 py-2">
                <option value="">Semua Penugasan</option>
                <option value="unassigned" {{ request('assigned_to') == 'unassigned' ? 'selected' : '' }}>Belum Ditugaskan</option>
                @foreach($surveyors as $srv)
                    <option value="{{ $srv->id }}" {{ request('assigned_to') == $srv->id ? 'selected' : '' }}>
                        Tugas: {{ $srv->name }}
                    </option>
                @endforeach
            </select>

            <div class="flex gap-2">
                <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg">
                    Filter
                </button>
                <a href="{{ route('admin.infrastructures.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold py-2 px-3 rounded-lg">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Action Bar Massal (Muncul Aktif Saat Ada Checkbox Dicentang) -->
    <div class="bg-slate-800 text-white p-3.5 rounded-xl shadow mb-4 flex flex-wrap justify-between items-center gap-3 text-xs">
        <div class="flex items-center gap-2">
            <span id="selected-count-badge" class="bg-amber-400 text-slate-900 font-extrabold px-2.5 py-1 rounded-lg">
                0 dipilih
            </span>
            <span class="text-slate-300">Centang data di tabel bawah untuk aksi massal:</span>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <!-- Form Penugasan Surveyor Massal -->
            <form id="bulkAssignForm" method="POST" action="{{ route('admin.infrastructures.bulk_assign') }}" class="flex items-center gap-1.5">
                @csrf
                <div id="bulk-assign-inputs"></div>
                <select name="assigned_to" class="bg-white text-gray-800 rounded-lg px-2.5 py-1.5 font-medium">
                    <option value="">-- Lepas / Kosongkan Tugas --</option>
                    @foreach($surveyors as $srv)
                        <option value="{{ $srv->id }}">Tugaskan ke: {{ $srv->name }}</option>
                    @endforeach
                </select>
                <button type="button" onclick="submitBulkAssign()"
                        class="bg-blue-600 hover:bg-blue-500 text-white font-bold px-3 py-1.5 rounded-lg">
                    👷 Terapkan Tugas
                </button>
            </form>

            <div class="h-5 w-[1px] bg-slate-600 mx-1"></div>

            <!-- Form Hapus Massal -->
            <form id="bulkDeleteForm" method="POST" action="{{ route('admin.infrastructures.bulk_delete') }}">
                @csrf
                <div id="bulk-delete-inputs"></div>
                <button type="button" onclick="submitBulkDelete()"
                        class="bg-red-600 hover:bg-red-500 text-white font-bold px-3 py-1.5 rounded-lg">
                    🗑️ Hapus Terpilih
                </button>
            </form>
        </div>
    </div>

    <!-- Tabel Data Infrastruktur -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead class="bg-gray-50 border-b border-gray-200 uppercase text-gray-600">
                    <tr>
                        <th class="py-3.5 px-4 w-10 text-center">
                            <input type="checkbox" id="selectAllRows" class="w-4 h-4 accent-blue-600 rounded cursor-pointer" title="Pilih Semua di Halaman Ini">
                        </th>
                        <th class="py-3.5 px-4">Nama Infrastruktur</th>
                        <th class="py-3.5 px-4">Kategori</th>
                        <th class="py-3.5 px-4">Wilayah & Koordinat</th>
                        <th class="py-3.5 px-4">Penugasan Surveyor</th>
                        <th class="py-3.5 px-4">Status</th>
                        <th class="py-3.5 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($infrastructures as $item)
                        <tr class="hover:bg-blue-50/40 transition">
                            <td class="py-3 px-4 text-center">
                                <input type="checkbox" value="{{ $item->id }}" class="row-checkbox w-4 h-4 accent-blue-600 rounded cursor-pointer">
                            </td>
                            <td class="py-3 px-4">
                                <p class="font-bold text-gray-900 text-sm">{{ $item->nama_objek }}</p>
                                <p class="text-gray-500">{{ $item->jenis_infrastruktur ?? '-' }}</p>
                            </td>
                            <td class="py-3 px-4">
                                <span class="px-2.5 py-1 font-bold rounded-full bg-slate-200 text-slate-800">{{ $item->kategori }}</span>
                            </td>
                            <td class="py-3 px-4">
                                <p class="font-semibold text-gray-800">{{ $item->kota }}, {{ $item->provinsi }}</p>
                                <p class="text-gray-400 font-mono text-[11px]">{{ $item->latitude }}, {{ $item->longitude }}</p>
                            </td>
                            <td class="py-3 px-4">
                                @if($item->assignedSurveyor)
                                    <span class="px-2.5 py-1 rounded-lg bg-indigo-50 text-indigo-700 font-bold border border-indigo-200 inline-block">
                                        👷 {{ $item->assignedSurveyor->name }}
                                    </span>
                                @else
                                    <span class="text-gray-400 italic">Belum ditugaskan</span>
                                @endif
                            </td>
                            <td class="py-3 px-4">
                                @if($item->status_survei === 'sudah')
                                    <span class="px-2.5 py-1 font-bold rounded-full bg-green-100 text-green-700">✓ Sudah</span>
                                @else
                                    <span class="px-2.5 py-1 font-bold rounded-full bg-red-100 text-red-700">✕ Belum</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-right space-x-1 whitespace-nowrap">
                                <button type="button" onclick='openEditModal(@json($item))'
                                        class="bg-amber-500 hover:bg-amber-600 text-white px-2.5 py-1 rounded font-semibold">
                                    Edit
                                </button>
                                <form method="POST" action="{{ route('admin.infrastructures.destroy', $item) }}" class="inline"
                                      onsubmit="return confirm('Hapus titik {{ $item->nama_objek }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-2.5 py-1 rounded font-semibold">
                                        Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-gray-500">Belum ada data infrastruktur.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-gray-200">
            {{ $infrastructures->links() }}
        </div>
    </div>

    <!-- MODAL TAMBAH DATA INFRASTRUKTUR -->
    <div id="addModal" class="fixed inset-0 bg-black/60 hidden items-center justify-center z-50 p-4">
        <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl">
            <div class="flex justify-between items-center border-b pb-3 mb-4">
                <h3 class="text-base font-bold text-gray-900">➕ Tambah Data Infrastruktur Baru</h3>
                <button type="button" onclick="closeAddModal()" class="text-gray-400 hover:text-gray-700 text-xl font-bold">×</button>
            </div>
            <form method="POST" action="{{ route('admin.infrastructures.store') }}" class="space-y-3 text-xs">
                @csrf
                <div>
                    <label class="block font-semibold text-gray-700 mb-1">Nama Objek Infrastruktur *</label>
                    <input type="text" name="nama_objek" required class="w-full border border-gray-300 rounded-lg px-3 py-2">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-gray-700 mb-1">Kategori PU *</label>
                        <select name="kategori" required class="w-full border border-gray-300 rounded-lg px-3 py-2">
                            <option value="BM">BM (Bina Marga)</option>
                            <option value="CK">CK (Cipta Karya)</option>
                            <option value="SDA">SDA (Sumber Daya Air)</option>
                            <option value="PS">PS (Prasarana Strategis)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block font-semibold text-gray-700 mb-1">Jenis Infrastruktur</label>
                        <input type="text" name="jenis_infrastruktur" placeholder="Contoh: Jembatan / TPA" class="w-full border border-gray-300 rounded-lg px-3 py-2">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-gray-700 mb-1">Kota / Kabupaten *</label>
                        <input type="text" name="kota" required class="w-full border border-gray-300 rounded-lg px-3 py-2">
                    </div>
                    <div>
                        <label class="block font-semibold text-gray-700 mb-1">Provinsi *</label>
                        <input type="text" name="provinsi" required class="w-full border border-gray-300 rounded-lg px-3 py-2">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-gray-700 mb-1">Latitude (Y) *</label>
                        <input type="number" step="any" name="latitude" required placeholder="-6.236013" class="w-full border border-gray-300 rounded-lg px-3 py-2 font-mono">
                    </div>
                    <div>
                        <label class="block font-semibold text-gray-700 mb-1">Longitude (X) *</label>
                        <input type="number" step="any" name="longitude" required placeholder="106.800272" class="w-full border border-gray-300 rounded-lg px-3 py-2 font-mono">
                    </div>
                </div>
                <div>
                    <label class="block font-semibold text-gray-700 mb-1">Tugaskan ke Surveyor (Opsional)</label>
                    <select name="assigned_to" class="w-full border border-gray-300 rounded-lg px-3 py-2">
                        <option value="">-- Belum Ditugaskan --</option>
                        @foreach($surveyors as $srv)
                            <option value="{{ $srv->id }}">{{ $srv->name }} ({{ $srv->email }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex justify-end gap-2 pt-3 border-t">
                    <button type="button" onclick="closeAddModal()" class="px-4 py-2 bg-gray-200 rounded-lg font-semibold">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-emerald-600 text-white rounded-lg font-bold">Simpan Data</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL EDIT DATA INFRASTRUKTUR -->
    <div id="editModal" class="fixed inset-0 bg-black/60 hidden items-center justify-center z-50 p-4">
        <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl">
            <div class="flex justify-between items-center border-b pb-3 mb-4">
                <h3 class="text-base font-bold text-gray-900">✏️ Edit Data Infrastruktur</h3>
                <button type="button" onclick="closeEditModal()" class="text-gray-400 hover:text-gray-700 text-xl font-bold">×</button>
            </div>
            <form id="editForm" method="POST" class="space-y-3 text-xs">
                @csrf
                @method('PUT')
                <div>
                    <label class="block font-semibold text-gray-700 mb-1">Nama Objek Infrastruktur *</label>
                    <input type="text" name="nama_objek" id="edit_nama_objek" required class="w-full border border-gray-300 rounded-lg px-3 py-2">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-gray-700 mb-1">Kategori PU *</label>
                        <select name="kategori" id="edit_kategori" required class="w-full border border-gray-300 rounded-lg px-3 py-2">
                            <option value="BM">BM (Bina Marga)</option>
                            <option value="CK">CK (Cipta Karya)</option>
                            <option value="SDA">SDA (Sumber Daya Air)</option>
                            <option value="PS">PS (Prasarana Strategis)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block font-semibold text-gray-700 mb-1">Jenis Infrastruktur</label>
                        <input type="text" name="jenis_infrastruktur" id="edit_jenis" class="w-full border border-gray-300 rounded-lg px-3 py-2">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-gray-700 mb-1">Kota / Kabupaten *</label>
                        <input type="text" name="kota" id="edit_kota" required class="w-full border border-gray-300 rounded-lg px-3 py-2">
                    </div>
                    <div>
                        <label class="block font-semibold text-gray-700 mb-1">Provinsi *</label>
                        <input type="text" name="provinsi" id="edit_provinsi" required class="w-full border border-gray-300 rounded-lg px-3 py-2">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-gray-700 mb-1">Latitude (Y) *</label>
                        <input type="number" step="any" name="latitude" id="edit_latitude" required class="w-full border border-gray-300 rounded-lg px-3 py-2 font-mono">
                    </div>
                    <div>
                        <label class="block font-semibold text-gray-700 mb-1">Longitude (X) *</label>
                        <input type="number" step="any" name="longitude" id="edit_longitude" required class="w-full border border-gray-300 rounded-lg px-3 py-2 font-mono">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-gray-700 mb-1">Status Survei *</label>
                        <select name="status_survei" id="edit_status" required class="w-full border border-gray-300 rounded-lg px-3 py-2">
                            <option value="belum">Belum Survei</option>
                            <option value="sudah">Sudah Survei</option>
                        </select>
                    </div>
                    <div>
                        <label class="block font-semibold text-gray-700 mb-1">Penugasan Surveyor</label>
                        <select name="assigned_to" id="edit_assigned_to" class="w-full border border-gray-300 rounded-lg px-3 py-2">
                            <option value="">-- Belum Ditugaskan --</option>
                            @foreach($surveyors as $srv)
                                <option value="{{ $srv->id }}">{{ $srv->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="flex justify-end gap-2 pt-3 border-t">
                    <button type="button" onclick="closeEditModal()" class="px-4 py-2 bg-gray-200 rounded-lg font-semibold">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg font-bold">Simpan Perubahan</button>
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
                alert('⚠️ Silakan centang minimal 1 data yang ingin dihapus.');
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
                alert('⚠️ Silakan centang minimal 1 data infrastruktur untuk ditugaskan ke surveyor.');
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
            document.getElementById('edit_nama_objek').value = item.nama_objek;
            document.getElementById('edit_kategori').value = item.kategori;
            document.getElementById('edit_jenis').value = item.jenis_infrastruktur || '';
            document.getElementById('edit_kota').value = item.kota;
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