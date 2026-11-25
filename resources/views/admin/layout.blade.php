<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Panel') - SIP-PU</title>
    <script src="https://cdn.tailwindcss.com"></script>
    @stack('styles')
</head>
<body class="bg-gray-100 font-sans text-gray-800 antialiased">

    <div class="min-h-screen flex items-start">
        <!-- Backdrop Gelap Mobile saat Sidebar Terbuka -->
        <div id="admin-sidebar-backdrop" onclick="toggleAdminSidebar()"
             class="fixed inset-0 bg-black/50 z-30 hidden lg:hidden"></div>

        <!-- SIDEBAR ADMIN BUKA-TUTUP (DIAM / STICKY FULL HEIGHT) -->
        <aside id="admin-sidebar"
               class="fixed lg:sticky lg:top-0 lg:h-screen inset-y-0 left-0 z-40 w-64 bg-slate-900 text-white flex flex-col transition-all duration-300 ease-in-out -translate-x-full lg:translate-x-0 shrink-0 shadow-xl overflow-y-auto">
            
            <!-- Brand Header -->
            <div class="h-16 px-4 flex items-center justify-between border-b border-slate-800 shrink-0">
                <div class="flex items-center gap-2.5 overflow-hidden">
                    <span class="w-8 h-8 rounded-lg bg-amber-400 text-slate-950 font-extrabold flex items-center justify-center shrink-0">PU</span>
                    <div class="admin-sidebar-label truncate">
                        <span class="font-bold text-sm text-amber-300 block leading-tight">SIP-PU Admin</span>
                        <span class="text-[10px] text-slate-400">Manajemen Infrastruktur</span>
                    </div>
                </div>
                <button type="button" onclick="toggleAdminSidebar()" class="lg:hidden text-slate-300 hover:text-white text-xl font-bold">
                    ×
                </button>
            </div>

            <!-- Menu Navigasi Utama Admin -->
            <nav class="flex-1 px-3 py-4 space-y-1.5 text-xs">
                <p class="admin-sidebar-label px-3 text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-2">Menu Administrasi</p>

                <a href="{{ route('admin.dashboard') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-semibold transition {{ request()->routeIs('admin.dashboard') ? 'bg-blue-600 text-white shadow' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <span class="text-base shrink-0">📊</span>
                    <span class="admin-sidebar-label truncate">Monitoring Survei</span>
                </a>

                <a href="{{ route('admin.infrastructures.index') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-semibold transition {{ request()->routeIs('admin.infrastructures.*') ? 'bg-blue-600 text-white shadow' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <span class="text-base shrink-0">🏗️</span>
                    <span class="admin-sidebar-label truncate">Manajemen Data</span>
                </a>

                <a href="{{ route('admin.assignments.index') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-semibold transition {{ request()->routeIs('admin.assignments.*') ? 'bg-amber-500 text-slate-950 font-bold shadow' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <span class="text-base shrink-0">📌</span>
                    <span class="admin-sidebar-label truncate">Penugasan Surveyor</span>
                </a>

                <a href="{{ route('admin.users.index') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-semibold transition {{ request()->routeIs('admin.users.*') ? 'bg-blue-600 text-white shadow' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <span class="text-base shrink-0">👥</span>
                    <span class="admin-sidebar-label truncate">Kelola Akun User</span>
                </a>

                <div class="pt-3 mt-3 border-t border-slate-800">
                    <p class="admin-sidebar-label px-3 text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-2">Visualisasi GIS</p>
                    <a href="{{ route('landing') }}"
                       class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-semibold text-emerald-400 hover:bg-slate-800 transition">
                        <span class="text-base shrink-0">🗺️</span>
                        <span class="admin-sidebar-label truncate">Buka Peta Utama GIS</span>
                    </a>
                </div>
            </nav>

            <!-- Info Admin & Logout -->
            <div class="p-3 border-t border-slate-800 text-xs space-y-2 shrink-0">
                <div class="admin-sidebar-label px-2">
                    <p class="text-[10px] text-slate-400">Administrator:</p>
                    <p class="font-bold text-white truncate">{{ auth()->user()->name }}</p>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                            class="w-full flex items-center justify-center gap-2 bg-red-600/90 hover:bg-red-600 text-white py-2 px-3 rounded-xl font-semibold transition">
                        <span>🚪</span>
                        <span class="admin-sidebar-label">Logout</span>
                    </button>
                </form>
            </div>
        </aside>

        <!-- KONTEN UTAMA ADMIN -->
        <div class="flex-1 flex flex-col min-w-0 min-h-screen">
            <!-- Top Bar dengan Tombol Toggle Sidebar & Judul Halaman -->
            <header class="h-16 bg-white border-b border-gray-200 px-4 sm:px-6 flex items-center justify-between sticky top-0 z-20 shadow-sm">
                <div class="flex items-center gap-3">
                    <button type="button" onclick="toggleAdminSidebar()"
                            title="Buka / Tutup Sidebar"
                            class="p-2 rounded-lg border border-gray-200 hover:bg-gray-100 text-gray-700 transition">
                        ☰
                    </button>
                    <div>
                        <h1 class="font-bold text-sm sm:text-base text-gray-900">@yield('title', 'Panel Admin')</h1>
                        <p class="text-[11px] text-gray-500 hidden sm:block">Sistem Informasi Geografis & Monitoring Infrastruktur PU</p>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    @yield('header_actions')
                    <a href="{{ route('landing') }}"
                       class="bg-slate-800 hover:bg-slate-900 text-white px-3.5 py-2 rounded-xl text-xs font-bold shadow-sm transition">
                        🗺️ Peta GIS
                    </a>
                </div>
            </header>

            <!-- Alert Notifikasi Sukses / Error -->
            <main class="flex-1 p-4 sm:p-6 max-w-7xl w-full mx-auto space-y-5">
                @if(session('success'))
                    <div class="bg-green-600 text-white px-4 py-3 rounded-xl shadow text-xs font-semibold flex justify-between items-center">
                        <span>✓ {{ session('success') }}</span>
                        <button type="button" onclick="this.parentElement.remove()" class="font-bold text-base">×</button>
                    </div>
                @endif

                @if($errors->any())
                    <div class="bg-red-600 text-white px-4 py-3 rounded-xl shadow text-xs font-semibold">
                        <ul class="list-disc pl-4">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    <script>
        let isAdminSidebarCollapsed = false;

        function toggleAdminSidebar() {
            const sidebar = document.getElementById('admin-sidebar');
            const backdrop = document.getElementById('admin-sidebar-backdrop');
            const isMobile = window.innerWidth < 1024;

            if (isMobile) {
                const isClosed = sidebar.classList.contains('-translate-x-full');
                if (isClosed) {
                    sidebar.classList.remove('-translate-x-full');
                    backdrop.classList.remove('hidden');
                } else {
                    sidebar.classList.add('-translate-x-full');
                    backdrop.classList.add('hidden');
                }
            } else {
                isAdminSidebarCollapsed = !isAdminSidebarCollapsed;
                const labels = sidebar.querySelectorAll('.admin-sidebar-label');
                if (isAdminSidebarCollapsed) {
                    sidebar.classList.remove('w-64');
                    sidebar.classList.add('w-20');
                    labels.forEach(el => el.classList.add('hidden'));
                } else {
                    sidebar.classList.remove('w-20');
                    sidebar.classList.add('w-64');
                    labels.forEach(el => el.classList.remove('hidden'));
                }
            }
        }
    </script>
    @stack('scripts')
</body>
</html>