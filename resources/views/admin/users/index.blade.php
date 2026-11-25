@extends('admin.layout')

@section('title', 'Manajemen Akun Pengguna')

@section('content')
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Monitoring & Manajemen Akun</h1>
            <p class="text-sm text-gray-500">Kelola hak akses akun Surveyor Lapangan dan Administrator</p>
        </div>
        <button onclick="document.getElementById('modalAddUser').classList.remove('hidden')" 
                class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 py-2.5 rounded-lg text-sm shadow">
            + Tambah Akun Baru
        </button>
    </div>

    <!-- Filter Pencarian Akun -->
    <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-200 mb-6">
        <form method="GET" action="{{ route('admin.users.index') }}" class="flex flex-wrap gap-3">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama atau email..."
                   class="flex-1 min-w-[200px] border border-gray-300 rounded-lg px-3 py-2 text-sm">
            <select name="role" class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
                <option value="">Semua Role</option>
                <option value="surveyor" {{ request('role') == 'surveyor' ? 'selected' : '' }}>Surveyor</option>
                <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
            </select>
            <button type="submit" class="bg-slate-800 text-white px-4 py-2 rounded-lg text-sm font-medium">Cari</button>
            <a href="{{ route('admin.users.index') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium">Reset</a>
        </form>
    </div>

    <!-- Tabel Daftar Akun -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <table class="w-full text-left border-collapse text-sm">
            <thead class="bg-gray-50 border-b border-gray-200 text-xs uppercase text-gray-600">
                <tr>
                    <th class="py-3.5 px-4">Nama Pengguna</th>
                    <th class="py-3.5 px-4">Email</th>
                    <th class="py-3.5 px-4">Role</th>
                    <th class="py-3.5 px-4">Dibuat Pada</th>
                    <th class="py-3.5 px-4 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach($users as $user)
                    <tr class="hover:bg-gray-50">
                        <td class="py-3 px-4 font-semibold text-gray-900">{{ $user->name }}</td>
                        <td class="py-3 px-4 text-gray-600">{{ $user->email }}</td>
                        <td class="py-3 px-4">
                            @if($user->role === 'admin')
                                <span class="px-2.5 py-1 text-xs font-bold rounded-full bg-purple-100 text-purple-700">Admin</span>
                            @else
                                <span class="px-2.5 py-1 text-xs font-bold rounded-full bg-sky-100 text-sky-700">Surveyor</span>
                            @endif
                        </td>
                        <td class="py-3 px-4 text-xs text-gray-500">{{ $user->created_at->format('d M Y') }}</td>
                        <td class="py-3 px-4 text-right space-x-2">
                            <button type="button"
                                    data-update-url="{{ route('admin.users.update', $user) }}"
                                    data-name="{{ $user->name }}"
                                    data-email="{{ $user->email }}"
                                    data-role="{{ $user->role }}"
                                    onclick="openEditModal(this)"
                                    class="text-amber-600 hover:text-amber-800 font-semibold text-xs">
                                Edit
                            </button>

                            @if($user->id !== auth()->id())
                                <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="inline"
                                      onsubmit="return confirm('Yakin ingin menghapus akun {{ $user->name }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800 font-semibold text-xs">
                                        Hapus
                                    </button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="p-4 border-t border-gray-200">
            {{ $users->links() }}
        </div>
    </div>

    <!-- MODAL TAMBAH AKUN -->
    <div id="modalAddUser" class="fixed inset-0 bg-black/50 hidden flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-xl max-w-md w-full p-6 shadow-xl">
            <h3 class="font-bold text-lg mb-4">Tambah Akun Pengguna Baru</h3>
            <form action="{{ route('admin.users.store') }}" method="POST" class="space-y-3 text-sm">
                @csrf
                <div>
                    <label class="block font-medium mb-1">Nama Lengkap</label>
                    <input type="text" name="name" required class="w-full border rounded-lg px-3 py-2">
                </div>
                <div>
                    <label class="block font-medium mb-1">Email</label>
                    <input type="email" name="email" required class="w-full border rounded-lg px-3 py-2">
                </div>
                <div>
                    <label class="block font-medium mb-1">Role Akses</label>
                    <select name="role" class="w-full border rounded-lg px-3 py-2">
                        <option value="surveyor">Surveyor Lapangan</option>
                        <option value="admin">Administrator</option>
                    </select>
                </div>
                <div>
                    <label class="block font-medium mb-1">Password</label>
                    <input type="password" name="password" required minlength="6" class="w-full border rounded-lg px-3 py-2">
                </div>
                <div class="flex justify-end gap-2 pt-3">
                    <button type="button" onclick="document.getElementById('modalAddUser').classList.add('hidden')" class="px-4 py-2 bg-gray-200 rounded-lg">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white font-semibold rounded-lg">Simpan Akun</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL EDIT AKUN -->
    <div id="modalEditUser" class="fixed inset-0 bg-black/50 hidden flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-xl max-w-md w-full p-6 shadow-xl">
            <h3 class="font-bold text-lg mb-4">Edit Akun Pengguna</h3>
            <form id="formEditUser" method="POST" class="space-y-3 text-sm">
                @csrf
                @method('PUT')
                <div>
                    <label class="block font-medium mb-1">Nama Lengkap</label>
                    <input type="text" name="name" id="edit_name" required class="w-full border rounded-lg px-3 py-2">
                </div>
                <div>
                    <label class="block font-medium mb-1">Email</label>
                    <input type="email" name="email" id="edit_email" required class="w-full border rounded-lg px-3 py-2">
                </div>
                <div>
                    <label class="block font-medium mb-1">Role Akses</label>
                    <select name="role" id="edit_role" class="w-full border rounded-lg px-3 py-2">
                        <option value="surveyor">Surveyor Lapangan</option>
                        <option value="admin">Administrator</option>
                    </select>
                </div>
                <div>
                    <label class="block font-medium mb-1">Password Baru <span class="text-xs text-gray-400">(Kosongkan jika tidak diubah)</span></label>
                    <input type="password" name="password" minlength="6" class="w-full border rounded-lg px-3 py-2">
                </div>
                <div class="flex justify-end gap-2 pt-3">
                    <button type="button" onclick="document.getElementById('modalEditUser').classList.add('hidden')" class="px-4 py-2 bg-gray-200 rounded-lg">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-amber-600 text-white font-semibold rounded-lg">Update Akun</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openEditModal(button) {
            document.getElementById('formEditUser').action = button.dataset.updateUrl;
            document.getElementById('edit_name').value = button.dataset.name;
            document.getElementById('edit_email').value = button.dataset.email;
            document.getElementById('edit_role').value = button.dataset.role;
            document.getElementById('modalEditUser').classList.remove('hidden');
        }
    </script>
@endsection