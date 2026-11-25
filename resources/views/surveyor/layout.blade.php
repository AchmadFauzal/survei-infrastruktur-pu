<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Panel Surveyor') - SIP-PU</title>
    <script src="https://cdn.tailwindcss.com"></script>
    @stack('styles')
</head>
<body class="bg-gray-100 font-sans text-gray-800 antialiased">

    <div class="min-h-screen flex items-start">
        <!-- Backdrop Gelap Mobile saat Sidebar Terbuka -->
        <div id="sidebar-backdrop" onclick="toggleSurveyorSidebar()"
             class="fixed inset-0 bg-black/50 z-30 hidden lg:hidden"></div>

        <!-- SIDEBAR BUKA-TUTUP (DIAM / STICKY SAAT SCROLL) -->
        <aside id="surveyor-sidebar"
               class="fixed lg:sticky lg:top-0 lg:h-screen inset-y-0 left-0 z-40 w-64 bg-blue-950 text-white flex flex-col transition-all duration-300 ease-in-out -translate-x-full lg:translate-x-0 shrink-0 shadow-xl overflow-y-auto">
            <!-- Brand Header -->
            <div class="h-16 px-4 flex items-center justify-between border-b border-blue-900 shrink-0">
                <div class="flex items-center gap-2.5 overflow-hidden">
                    <span class="w-8 h-8 rounded-lg bg-amber-400 text-blue-950 font-extrabold flex items-center justify-center shrink-0">PU</span>
                    <div class="sidebar-label truncate">
                        <span class="font-bold text-sm text-amber-300 block leading-tight">SIP-PU Surveyor</span>
                        <span class="text-[10px] text-blue-300">Sistem Survei Infrastruktur</span>
                    </div>
                </div>
                <button type="button" onclick="toggleSurveyorSidebar()" class="lg:hidden text-blue-200 hover:text-white text-xl font-bold">
                    ×
                </button>
            </div>

            <!-- Menu Navigasi Utama -->
            <nav class="flex-1 px-3 py-4 space-y-1.5 text-xs">
                <p class="sidebar-label px-3 text-[10px] font-bold uppercase tracking-wider text-blue-400 mb-2">Menu Petugas</p>

                <a href="{{ route('surveyor.dashboard') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-semibold transition {{ request()->routeIs('surveyor.dashboard') ? 'bg-blue-700 text-white shadow' : 'text-blue-200 hover:bg-blue-900 hover:text-white' }}">
                    <span class="text-base shrink-0">📋</span>
                    <span class="sidebar-label truncate">Riwayat & Pelaporan</span>
                </a>

                <a href="{{ route('surveyor.assignments') }}"
                   class="flex items-center justify-between px-3 py-2.5 rounded-xl font-semibold transition {{ request()->routeIs('surveyor.assignments') ? 'bg-amber-500 text-blue-950 shadow font-bold' : 'text-blue-200 hover:bg-blue-900 hover:text-white' }}">
                    <div class="flex items-center gap-3 truncate">
                        <span class="text-base shrink-0">🧭</span>
                        <span class="sidebar-label truncate">Penugasan & Rute</span>
                    </div>
                    @if(isset($stats['tugas_aktif']) && $stats['tugas_aktif'] > 0)
                        <span class="sidebar-label px-2 py-0.5 text-[10px] font-extrabold rounded-full {{ request()->routeIs('surveyor.assignments') ? 'bg-blue-950 text-amber-300' : 'bg-amber-500 text-blue-950' }}">
                            {{ $stats['tugas_aktif'] }}
                        </span>
                    @endif
                </a>

                <div class="pt-3 mt-3 border-t border-blue-900">
                    <p class="sidebar-label px-3 text-[10px] font-bold uppercase tracking-wider text-blue-400 mb-2">Peta Lapangan</p>
                    <a href="{{ route('landing') }}"
                       class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-semibold text-emerald-300 hover:bg-blue-900 transition">
                        <span class="text-base shrink-0">🗺️</span>
                        <span class="sidebar-label truncate">Buka Peta Utama GIS</span>
                    </a>
                </div>
            </nav>

            <!-- Info User & Logout -->
            <div class="p-3 border-t border-blue-900 text-xs space-y-2 shrink-0">
                <div class="sidebar-label px-2">
                    <p class="text-[10px] text-blue-300">Login sebagai:</p>
                    <p class="font-bold text-white truncate">{{ auth()->user()->name }}</p>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                            class="w-full flex items-center justify-center gap-2 bg-red-600/90 hover:bg-red-600 text-white py-2 px-3 rounded-xl font-semibold transition">
                        <span>🚪</span>
                        <span class="sidebar-label">Logout</span>
                    </button>
                </form>
            </div>
        </aside>

        <!-- KONTEN UTAMA -->
        <div class="flex-1 flex flex-col min-w-0 min-h-screen">
            <!-- Top Bar dengan Tombol Toggle Sidebar -->
            <header class="h-16 bg-white border-b border-gray-200 px-4 sm:px-6 flex items-center justify-between sticky top-0 z-20 shadow-sm">
                <div class="flex items-center gap-3">
                    <button type="button" onclick="toggleSurveyorSidebar()"
                            title="Buka / Tutup Sidebar"
                            class="p-2 rounded-lg border border-gray-200 hover:bg-gray-100 text-gray-700 transition">
                        ☰
                    </button>
                    <div>
                        <h1 class="font-bold text-sm sm:text-base text-gray-900">@yield('page_title')</h1>
                        <p class="text-[11px] text-gray-500 hidden sm:block">@yield('page_subtitle')</p>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    @yield('header_actions')
                    <a href="{{ route('landing') }}"
                       class="bg-blue-700 hover:bg-blue-800 text-white px-3.5 py-2 rounded-xl text-xs font-bold shadow-sm transition">
                        🗺️ Peta GIS
                    </a>
                </div>
            </header>

            <main class="flex-1 p-4 sm:p-6 max-w-7xl w-full mx-auto space-y-6">
                @yield('content')
            </main>
        </div>
    </div>

    <script>
        let isDesktopCollapsed = false;

        function toggleSurveyorSidebar() {
            const sidebar = document.getElementById('surveyor-sidebar');
            const backdrop = document.getElementById('sidebar-backdrop');
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
                isDesktopCollapsed = !isDesktopCollapsed;
                const labels = sidebar.querySelectorAll('.sidebar-label');
                if (isDesktopCollapsed) {
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