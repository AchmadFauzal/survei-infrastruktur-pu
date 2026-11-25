@extends('admin.layout')

@section('title', 'Manajemen Akun Pengguna')

@section('header_actions')
    <button type="button" onclick="openAddUserModal()"
            class="group bg-pu-gold hover:bg-amber-400 active:scale-[0.98] text-pu-navy text-xs font-extrabold px-3.5 py-2 rounded-xl shadow-xs hover:shadow-md transition-all duration-200 inline-flex items-center gap-1.5 whitespace-nowrap cursor-pointer">
        <svg class="w-4 h-4 transition-transform duration-200 group-hover:rotate-90" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
        </svg>
        <span>Tambah Akun Baru</span>
    </button>
@endsection

@section('content')
    <!-- ================= 1. KARTU RINGKASAN AKUN PENGGUNA ================= -->
    @php
        $totalAkun    = $users->total();
        $jumlahAdmin  = $users->getCollection()->where('role', 'admin')->count();
        $jumlahSurvey = $users->getCollection()->where('role', 'surveyor')->count();
    @endphp
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <!-- Total Akun Terdaftar -->
        <div class="pu-card bg-white p-5 rounded-2xl shadow-xs border border-slate-200 border-b-4 border-b-pu-navy">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Total Akun Terdaftar</p>
                    <p class="text-3xl font-extrabold text-pu-navy mt-1">{{ number_format($totalAkun) }}</p>
                    <p class="text-[11px] text-slate-500 mt-1">Seluruh pengguna aktif sistem InfraLens</p>
                </div>
                <div class="w-10 h-10 rounded-xl bg-pu-navy/10 text-pu-navy flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Akun Surveyor Lapangan -->
        <div class="pu-card bg-white p-5 rounded-2xl shadow-xs border border-slate-200 border-b-4 border-b-sky-500">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <p class="text-[11px] font-bold uppercase tracking-wider text-sky-600">Surveyor Lapangan</p>
                    <p class="text-3xl font-extrabold text-sky-700 mt-1">{{ number_format($stats['surveyor'] ?? $jumlahSurvey) }}</p>
                    <p class="text-[11px] text-slate-500 mt-1">Petugas verifikasi & dokumentasi GPS</p>
                </div>
                <div class="w-10 h-10 rounded-xl bg-sky-50 text-sky-600 border border-sky-100 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 21s-6-5.686-6-10a6 6 0 1112 0c0 4.314-6 10-6 10z"/>
                        <circle cx="12" cy="11" r="2.2"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Akun Administrator -->
        <div class="pu-card bg-white p-5 rounded-2xl shadow-xs border border-slate-200 border-b-4 border-b-amber-500">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <p class="text-[11px] font-bold uppercase tracking-wider text-amber-600">Administrator</p>
                    <p class="text-3xl font-extrabold text-amber-600 mt-1">{{ number_format($stats['admin'] ?? $jumlahAdmin) }}</p>
                    <p class="text-[11px] text-slate-500 mt-1">Pengelola data spasial & penugasan</p>
                </div>
                <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 border border-amber-100 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- ================= 2. FILTER PENCARIAN AKUN ================= -->
    <div class="bg-white p-5 rounded-2xl shadow-xs border border-slate-200">
        <div class="flex flex-wrap items-center justify-between gap-2 mb-3.5 pb-2.5 border-b border-slate-100">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-pu-navy" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                </svg>
                <h2 class="text-xs font-extrabold uppercase tracking-wider text-pu-navy">Filter Akun Pengguna</h2>
            </div>
            <p class="text-[11px] text-slate-400 hidden sm:block">Kelola hak akses akun Surveyor Lapangan dan Administrator</p>
        </div>

        <form method="GET" action="{{ route('admin.users.index') }}" class="grid grid-cols-1 md:grid-cols-12 gap-3.5 items-end text-xs">
            <div class="md:col-span-6">
                <label class="block text-[11px] font-bold text-slate-600 mb-1">Kata Kunci Nama atau Email</label>
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama lengkap atau alamat email..."
                           class="w-full h-9 bg-slate-50 focus:bg-white border border-slate-300 focus:border-pu-navy rounded-xl pl-8 pr-3 text-xs outline-none transition">
                    <svg class="w-3.5 h-3.5 text-slate-400 absolute left-2.5 top-2.5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 104.5 4.5a7.5 7.5 0 0012.15 12.15z"/>
                    </svg>
                </div>
            </div>

            <div class="md:col-span-3">
                <label class="block text-[11px] font-bold text-slate-600 mb-1">Role Hak Akses</label>
                <select name="role" class="w-full h-9 bg-slate-50 focus:bg-white border border-slate-300 focus:border-pu-navy rounded-xl px-3 text-xs outline-none transition">
                    <option value="">Semua Role</option>
                    <option value="surveyor" {{ request('role') == 'surveyor' ? 'selected' : '' }}>Surveyor Lapangan</option>
                    <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Administrator</option>
                </select>
            </div>

            <div class="md:col-span-3 flex items-center gap-2">
                <button type="submit"
                        class="flex-1 h-9 bg-pu-navy hover:bg-pu-gold text-white hover:text-pu-navy text-xs font-bold rounded-xl shadow-xs transition inline-flex items-center justify-center gap-1.5 cursor-pointer">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.4" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 104.5 4.5a7.5 7.5 0 0012.15 12.15z"/>
                    </svg>
                    <span>Cari Akun</span>
                </button>
                <a href="{{ route('admin.users.index') }}"
                   class="h-9 px-3.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl transition inline-flex items-center justify-center">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- ================= 3. TABEL DAFTAR AKUN PENGGUNA ================= -->
    <div class="bg-white rounded-2xl shadow-xs border border-slate-200 overflow-hidden">
        <div class="px-5 py-3.5 border-b border-slate-100 flex flex-wrap items-center justify-between gap-2 bg-slate-50/70">
            <span class="text-xs font-extrabold text-pu-navy uppercase tracking-wider">Daftar Akun Pengguna</span>
            <span class="text-[11px] font-semibold text-slate-500">
                Menampilkan {{ $users->firstItem() ?? 0 }}–{{ $users->lastItem() ?? 0 }} dari {{ $users->total() }} akun
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead class="bg-slate-50 border-b border-slate-200 text-[11px] font-bold uppercase tracking-wider text-slate-500">
                    <tr>
                        <th class="py-3.5 px-4">Nama Pengguna</th>
                        <th class="py-3.5 px-4">Alamat Email</th>
                        <th class="py-3.5 px-4 whitespace-nowrap">Role Akses</th>
                        <th class="py-3.5 px-4 whitespace-nowrap">Tanggal Terdaftar</th>
                        <th class="py-3.5 px-4 text-right whitespace-nowrap">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($users as $user)
                        <tr class="hover:bg-sky-50/40 transition-colors">
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                <div class="flex items-center gap-3">
                                    <span class="w-8 h-8 rounded-xl {{ $user->role === 'admin' ? 'bg-pu-navy text-pu-gold' : 'bg-sky-100 text-pu-navy' }} font-extrabold flex items-center justify-center text-xs shrink-0 shadow-2xs">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </span>
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <p class="font-bold text-slate-900 text-xs sm:text-[13px] leading-tight">{{ $user->name }}</p>
                                            @if($user->id === auth()->id())
                                                <span class="px-2 py-0.5 text-[10px] font-extrabold rounded-md bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                    Akun Anda
                                                </span>
                                            @endif
                                        </div>
                                        <p class="text-[11px] text-slate-400 mt-0.5">ID Pengguna #{{ $user->id }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3.5 px-4 text-slate-600 font-medium">{{ $user->email }}</td>
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                @if($user->role === 'admin')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-[11px] font-bold rounded-full bg-pu-navy/10 text-pu-navy border border-pu-navy/20">
                                        <span class="w-1.5 h-1.5 rounded-full bg-pu-gold"></span>
                                        Administrator
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-[11px] font-bold rounded-full bg-sky-50 text-sky-700 border border-sky-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-sky-500"></span>
                                        Surveyor Lapangan
                                    </span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-[11px] text-slate-500 whitespace-nowrap">
                                {{ $user->created_at->translatedFormat('d M Y') }}
                            </td>
                            <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                <div class="inline-flex items-center gap-1.5">
                                    <button type="button"
                                            data-update-url="{{ route('admin.users.update', $user) }}"
                                            data-name="{{ $user->name }}"
                                            data-email="{{ $user->email }}"
                                            data-role="{{ $user->role }}"
                                            onclick="openEditUserModal(this)"
                                            class="bg-pu-gold hover:bg-amber-400 text-pu-navy px-3 py-1.5 rounded-xl font-bold text-[11px] transition inline-flex items-center gap-1 cursor-pointer">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                        <span>Edit</span>
                                    </button>

                                    @if($user->id !== auth()->id())
                                        <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="inline"
                                              onsubmit="return confirm('Yakin ingin menghapus akun {{ $user->name }}?')">
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
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-12 text-center text-slate-400">
                                <p class="font-semibold text-slate-600">Tidak ada akun pengguna yang sesuai pencarian.</p>
                                <p class="text-[11px] mt-1">Klik tombol <strong class="text-pu-navy">Tambah Akun Baru</strong> di kanan atas untuk membuat akun.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination Kustom Kompak -->
        @if($users->hasPages())
            <div class="px-5 py-3 border-t border-slate-100 bg-slate-50/50 flex flex-col sm:flex-row items-center justify-between gap-2.5 text-[11px]">
                <p class="text-slate-500 font-medium">
                    Halaman <span class="font-bold text-pu-navy">{{ $users->currentPage() }}</span> dari <span class="font-bold text-slate-700">{{ $users->lastPage() }}</span>
                    <span class="text-slate-400 mx-1">·</span>
                    Total <span class="font-bold text-slate-700">{{ number_format($users->total()) }}</span> akun
                </p>

                <nav class="inline-flex items-center gap-1" aria-label="Pagination">
                    @if($users->onFirstPage())
                        <span class="w-7 h-7 inline-flex items-center justify-center rounded-lg border border-slate-200 bg-slate-100/70 text-slate-300 cursor-not-allowed">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.4" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                            </svg>
                        </span>
                    @else
                        <a href="{{ $users->previousPageUrl() }}"
                           class="w-7 h-7 inline-flex items-center justify-center rounded-lg border border-slate-200 bg-white hover:bg-pu-navy text-slate-600 hover:text-white transition">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.4" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                            </svg>
                        </a>
                    @endif

                    @php
                        $current = $users->currentPage();
                        $last    = $users->lastPage();
                        $start   = max(1, $current - 2);
                        $end     = min($last, $current + 2);
                    @endphp

                    @if($start > 1)
                        <a href="{{ $users->url(1) }}" class="min-w-[28px] h-7 px-2 inline-flex items-center justify-center rounded-lg border border-slate-200 bg-white hover:bg-slate-100 text-slate-600 font-bold transition">1</a>
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
                            <a href="{{ $users->url($page) }}"
                               class="min-w-[28px] h-7 px-2 inline-flex items-center justify-center rounded-lg border border-slate-200 bg-white hover:bg-slate-100 text-slate-600 font-bold transition">
                                {{ $page }}
                            </a>
                        @endif
                    @endfor

                    @if($end < $last)
                        @if($end < $last - 1)
                            <span class="px-1 text-slate-400">...</span>
                        @endif
                        <a href="{{ $users->url($last) }}" class="min-w-[28px] h-7 px-2 inline-flex items-center justify-center rounded-lg border border-slate-200 bg-white hover:bg-slate-100 text-slate-600 font-bold transition">{{ $last }}</a>
                    @endif

                    @if($users->hasMorePages())
                        <a href="{{ $users->nextPageUrl() }}"
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

    <!-- ================= 4. MODAL TAMBAH AKUN (FULL BLUR) ================= -->
    <div id="modalAddUser" onclick="if(event.target === this) closeAddUserModal()"
         style="margin-top: 0 !important;"
         class="fixed inset-0 w-screen h-screen bg-slate-950/65 backdrop-blur-sm hidden items-center justify-center z-50 p-4">
        <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border-t-4 border-pu-navy">
            <div class="flex justify-between items-center border-b border-slate-100 pb-3.5 mb-4">
                <div>
                    <span class="text-[10.5px] font-extrabold px-2.5 py-0.5 rounded-lg bg-pu-navy text-pu-gold uppercase tracking-wider">Registrasi Akun</span>
                    <h3 class="text-base font-extrabold text-pu-navy mt-1">Tambah Akun Pengguna Baru</h3>
                </div>
                <button type="button" onclick="closeAddUserModal()" class="p-1.5 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <form action="{{ route('admin.users.store') }}" method="POST" class="space-y-3.5 text-xs">
                @csrf
                <div>
                    <label class="block text-[11px] font-bold text-slate-700 mb-1">Nama Lengkap *</label>
                    <input type="text" name="name" required placeholder="Contoh: Budi Santoso"
                           class="w-full h-9 bg-slate-50 focus:bg-white border border-slate-300 focus:border-pu-navy rounded-xl px-3 outline-none transition">
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-slate-700 mb-1">Alamat Email *</label>
                    <input type="email" name="email" required placeholder="nama@pu.go.id"
                           class="w-full h-9 bg-slate-50 focus:bg-white border border-slate-300 focus:border-pu-navy rounded-xl px-3 outline-none transition">
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-slate-700 mb-1">Role Hak Akses *</label>
                    <select name="role" class="w-full h-9 bg-slate-50 focus:bg-white border border-slate-300 focus:border-pu-navy rounded-xl px-3 outline-none transition">
                        <option value="surveyor">Surveyor Lapangan</option>
                        <option value="admin">Administrator</option>
                    </select>
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-slate-700 mb-1">Password * <span class="text-[10.5px] font-normal text-slate-400">(Minimal 6 karakter)</span></label>
                    <input type="password" name="password" required minlength="6" placeholder="••••••••"
                           class="w-full h-9 bg-slate-50 focus:bg-white border border-slate-300 focus:border-pu-navy rounded-xl px-3 outline-none transition">
                </div>
                <div class="flex justify-end gap-2 pt-3 border-t border-slate-100">
                    <button type="button" onclick="closeAddUserModal()"
                            class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-bold transition">
                        Batal
                    </button>
                    <button type="submit"
                            class="px-5 py-2 bg-pu-navy hover:bg-pu-gold text-white hover:text-pu-navy rounded-xl font-bold shadow-xs transition cursor-pointer">
                        Simpan Akun
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ================= 5. MODAL EDIT AKUN (FULL BLUR) ================= -->
    <div id="modalEditUser" onclick="if(event.target === this) closeEditUserModal()"
         style="margin-top: 0 !important;"
         class="fixed inset-0 w-screen h-screen bg-slate-950/65 backdrop-blur-sm hidden items-center justify-center z-50 p-4">
        <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border-t-4 border-pu-gold">
            <div class="flex justify-between items-center border-b border-slate-100 pb-3.5 mb-4">
                <div>
                    <span class="text-[10.5px] font-extrabold px-2.5 py-0.5 rounded-lg bg-pu-gold/25 text-pu-navy uppercase tracking-wider">Perbarui Akun</span>
                    <h3 class="text-base font-extrabold text-pu-navy mt-1">Edit Akun Pengguna</h3>
                </div>
                <button type="button" onclick="closeEditUserModal()" class="p-1.5 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <form id="formEditUser" method="POST" class="space-y-3.5 text-xs">
                @csrf
                @method('PUT')
                <div>
                    <label class="block text-[11px] font-bold text-slate-700 mb-1">Nama Lengkap *</label>
                    <input type="text" name="name" id="edit_name" required
                           class="w-full h-9 bg-slate-50 focus:bg-white border border-slate-300 focus:border-pu-navy rounded-xl px-3 outline-none transition">
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-slate-700 mb-1">Alamat Email *</label>
                    <input type="email" name="email" id="edit_email" required
                           class="w-full h-9 bg-slate-50 focus:bg-white border border-slate-300 focus:border-pu-navy rounded-xl px-3 outline-none transition">
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-slate-700 mb-1">Role Hak Akses *</label>
                    <select name="role" id="edit_role" class="w-full h-9 bg-slate-50 focus:bg-white border border-slate-300 focus:border-pu-navy rounded-xl px-3 outline-none transition">
                        <option value="surveyor">Surveyor Lapangan</option>
                        <option value="admin">Administrator</option>
                    </select>
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-slate-700 mb-1">
                        Password Baru <span class="text-[10.5px] font-normal text-slate-400">(Kosongkan jika tidak diubah)</span>
                    </label>
                    <input type="password" name="password" minlength="6" placeholder="••••••••"
                           class="w-full h-9 bg-slate-50 focus:bg-white border border-slate-300 focus:border-pu-navy rounded-xl px-3 outline-none transition">
                </div>
                <div class="flex justify-end gap-2 pt-3 border-t border-slate-100">
                    <button type="button" onclick="closeEditUserModal()"
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
        function openAddUserModal() {
            const modal = document.getElementById('modalAddUser');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeAddUserModal() {
            const modal = document.getElementById('modalAddUser');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        function openEditUserModal(button) {
            document.getElementById('formEditUser').action = button.dataset.updateUrl;
            document.getElementById('edit_name').value = button.dataset.name;
            document.getElementById('edit_email').value = button.dataset.email;
            document.getElementById('edit_role').value = button.dataset.role;

            const modal = document.getElementById('modalEditUser');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeEditUserModal() {
            const modal = document.getElementById('modalEditUser');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }
    </script>
@endsection