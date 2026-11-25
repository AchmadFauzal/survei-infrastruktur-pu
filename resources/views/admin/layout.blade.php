<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Panel') — InfraLens | Kementerian PU</title>

    <link rel="icon" type="image/png" href="{{ asset('images/Logo_Pu.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                    },
                    colors: {
                        'pu-navy': '#002359',
                        'pu-gold': '#FABD03',
                    }
                }
            }
        }
    </script>

    <style>
        /* Latar Belakang Sidebar Resmi PU (Full Tinggi Layar Tanpa Garis Sekat) */
        .pu-sidebar-bg {
            background-color: #002359;
            background-image:
                linear-gradient(180deg, rgba(0, 24, 62, 0.93) 0%, rgba(0, 38, 96, 0.84) 55%, rgba(0, 18, 48, 0.94) 100%),
                url("{{ asset('images/background-sidenav.png') }}");
            background-size: cover;
            background-position: center bottom;
            background-repeat: no-repeat;
        }

        /* Scrollbar Tipis pada Sidebar */
        #admin-sidebar::-webkit-scrollbar { width: 4px; }
        #admin-sidebar::-webkit-scrollbar-track { background: transparent; }
        #admin-sidebar::-webkit-scrollbar-thumb { background: rgba(255, 255, 255, 0.16); border-radius: 99px; }
        #admin-sidebar::-webkit-scrollbar-thumb:hover { background: #FABD03; }

        /* Animasi Gerak Halus & Warna Saat Hover pada Menu Sidebar */
        .nav-item-pu {
            transition: transform 0.25s cubic-bezier(0.34, 1.56, 0.64, 1),
                        background-color 0.2s ease,
                        color 0.2s ease,
                        box-shadow 0.2s ease;
        }
        .nav-item-pu:not(.is-active):hover {
            transform: translateX(6px);
            background-color: rgba(255, 255, 255, 0.13);
            color: #FABD03;
            box-shadow: 0 4px 14px rgba(0, 15, 40, 0.22);
        }
        .nav-item-pu.is-active {
            background: linear-gradient(135deg, #FABD03 0%, #f59e0b 100%);
            color: #002359;
            box-shadow: 0 6px 18px rgba(250, 189, 3, 0.28);
        }
        .nav-item-pu.is-active:hover {
            transform: translateX(4px);
            box-shadow: 0 8px 22px rgba(250, 189, 3, 0.38);
        }
        .nav-item-pu .nav-icon {
            transition: transform 0.25s cubic-bezier(0.34, 1.56, 0.64, 1), color 0.2s ease;
        }
        .nav-item-pu:hover .nav-icon {
            transform: scale(1.16) rotate(-4deg);
            color: #FABD03;
        }
        .nav-item-pu.is-active:hover .nav-icon {
            color: #002359;
            transform: scale(1.14);
        }

        /* Efek Hover Mengangkat Halus untuk Card Konten */
        .pu-card {
            transition: transform 0.24s cubic-bezier(0.22, 1, 0.36, 1), box-shadow 0.24s ease;
        }
        .pu-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 14px 30px -6px rgba(0, 41, 102, 0.1);
        }
    </style>
    @stack('styles')
</head>
<body class="bg-slate-100 font-sans text-slate-800 antialiased selection:bg-pu-navy selection:text-white">

    <div class="min-h-screen flex items-start">
        <!-- Backdrop Gelap Mobile saat Sidebar Terbuka -->
        <div id="admin-sidebar-backdrop" onclick="toggleAdminSidebar()"
             class="fixed inset-0 bg-slate-950/60 backdrop-blur-sm z-30 hidden lg:hidden"></div>

        <!-- ================= WRAPPER SIDEBAR RESPONSIF (FULL HEIGHT) ================= -->
        <div id="admin-sidebar-wrapper"
             style="height: 100vh; width: 264px;"
             class="fixed lg:sticky top-0 left-0 z-40 transition-all duration-300 ease-in-out -translate-x-full lg:translate-x-0 shrink-0">

            <aside id="admin-sidebar"
                   class="pu-sidebar-bg w-full h-full text-white flex flex-col shadow-2xl overflow-y-auto overflow-x-hidden">
                
                <!-- Brand Header dengan Logo PU (Tanpa Tombol X & Tanpa Garis Kaku) -->
                <div class="h-16 px-4 pt-2 flex items-center shrink-0">
                    <a href="{{ route('admin.dashboard') }}" class="group flex items-center gap-3 overflow-hidden">
                        <div class="w-9 h-9 rounded-xl bg-white/15 p-1.5 flex items-center justify-center shrink-0 shadow-sm transition-all duration-300 group-hover:scale-110 group-hover:rotate-6 group-hover:bg-pu-gold/25">
                            <img src="{{ asset('images/Logo_Pu.png') }}" alt="Logo Kementerian PU" class="w-full h-full object-contain">
                        </div>
                        <div class="admin-sidebar-label truncate">
                            <span class="font-extrabold text-base tracking-tight text-white block leading-none transition-colors group-hover:text-pu-gold">
                                Infra<span class="text-pu-gold group-hover:text-white transition-colors">Lens</span>
                            </span>
                            <span class="text-[10px] font-medium text-sky-200/80 block mt-1 leading-none">
                                Kementerian PU · Admin
                            </span>
                        </div>
                    </a>
                </div>

                <!-- Menu Navigasi Utama Admin -->
                <nav class="flex-1 px-3.5 py-4 space-y-1 text-xs">
                    <p class="admin-sidebar-label px-3 text-[10px] font-bold uppercase tracking-widest text-sky-200/50 mb-2">
                        Menu Administrasi
                    </p>

                    <!-- 1. Monitoring Survei -->
                    <a href="{{ route('admin.dashboard') }}"
                       class="nav-item-pu flex items-center gap-3 px-3 py-2.5 rounded-xl font-semibold {{ request()->routeIs('admin.dashboard') ? 'is-active font-bold' : 'text-slate-100' }}">
                        <svg class="nav-icon w-4 h-4 shrink-0 {{ request()->routeIs('admin.dashboard') ? 'text-pu-navy' : 'text-pu-gold' }}" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 3v18h18M7 16v-4m4 4V8m4 8v-6m4 6V5"/>
                        </svg>
                        <span class="admin-sidebar-label truncate">Monitoring Survei</span>
                    </a>

                    <!-- 2. Manajemen Data -->
                    <a href="{{ route('admin.infrastructures.index') }}"
                       class="nav-item-pu flex items-center gap-3 px-3 py-2.5 rounded-xl font-semibold {{ request()->routeIs('admin.infrastructures.*') ? 'is-active font-bold' : 'text-slate-100' }}">
                        <svg class="nav-icon w-4 h-4 shrink-0 {{ request()->routeIs('admin.infrastructures.*') ? 'text-pu-navy' : 'text-pu-gold' }}" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                        <span class="admin-sidebar-label truncate">Manajemen Data</span>
                    </a>

                    <!-- 3. Penugasan Surveyor -->
                    <a href="{{ route('admin.assignments.index') }}"
                       class="nav-item-pu flex items-center gap-3 px-3 py-2.5 rounded-xl font-semibold {{ request()->routeIs('admin.assignments.*') ? 'is-active font-bold' : 'text-slate-100' }}">
                        <svg class="nav-icon w-4 h-4 shrink-0 {{ request()->routeIs('admin.assignments.*') ? 'text-pu-navy' : 'text-pu-gold' }}" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 21s-6-5.686-6-10a6 6 0 1112 0c0 4.314-6 10-6 10z"/>
                            <circle cx="12" cy="11" r="2"/>
                        </svg>
                        <span class="admin-sidebar-label truncate">Penugasan Surveyor</span>
                    </a>

                    <!-- 4. Status Lapor BIG -->
                    <a href="{{ route('admin.big.index') }}"
                       class="nav-item-pu flex items-center gap-3 px-3 py-2.5 rounded-xl font-semibold {{ request()->routeIs('admin.big.*') ? 'is-active font-bold' : 'text-slate-100' }}">
                        <svg class="nav-icon w-4 h-4 shrink-0 {{ request()->routeIs('admin.big.*') ? 'text-pu-navy' : 'text-pu-gold' }}" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.14 0M1.394 9.393c5.857-5.857 15.355-5.857 21.213 0"/>
                        </svg>
                        <span class="admin-sidebar-label truncate">Status Lapor BIG</span>
                    </a>

                    <!-- 5. Log Aktivitas Survei -->
                    <a href="{{ route('admin.logs.index') }}"
                       class="nav-item-pu flex items-center gap-3 px-3 py-2.5 rounded-xl font-semibold {{ request()->routeIs('admin.logs.*') ? 'is-active font-bold' : 'text-slate-100' }}">
                        <svg class="nav-icon w-4 h-4 shrink-0 {{ request()->routeIs('admin.logs.*') ? 'text-pu-navy' : 'text-pu-gold' }}" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                            <circle cx="12" cy="12" r="9"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 7v5l3 3"/>
                        </svg>
                        <span class="admin-sidebar-label truncate">Log Aktivitas Survei</span>
                    </a>

                    <!-- 6. Kelola Akun User -->
                    <a href="{{ route('admin.users.index') }}"
                       class="nav-item-pu flex items-center gap-3 px-3 py-2.5 rounded-xl font-semibold {{ request()->routeIs('admin.users.*') ? 'is-active font-bold' : 'text-slate-100' }}">
                        <svg class="nav-icon w-4 h-4 shrink-0 {{ request()->routeIs('admin.users.*') ? 'text-pu-navy' : 'text-pu-gold' }}" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <span class="admin-sidebar-label truncate">Kelola Akun User</span>
                    </a>

                    <!-- Bagian Visualisasi Spasial -->
                    <div class="pt-4">
                        <p class="admin-sidebar-label px-3 text-[10px] font-bold uppercase tracking-widest text-sky-200/50 mb-1.5">
                            Visualisasi Spasial
                        </p>
                        <a href="{{ route('landing') }}"
                           class="nav-item-pu flex items-center gap-3 px-3 py-2.5 rounded-xl font-semibold text-pu-gold">
                            <svg class="nav-icon w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                                <polygon points="1 6 1 22 8 18 16 22 23 18 23 2 16 6 8 2 1 6"/>
                                <line x1="8" y1="2" x2="8" y2="18"/>
                                <line x1="16" y1="6" x2="16" y2="22"/>
                            </svg>
                            <span class="admin-sidebar-label truncate">Buka Peta Utama GIS</span>
                        </a>
                    </div>
                </nav>

                <!-- Info Admin & Logout di Bawah (Menempel Rapi di Dasar Sidebar) -->
                <div class="p-3.5 mt-auto text-xs space-y-2.5 shrink-0">
                    <div class="admin-sidebar-label flex items-center gap-2.5 px-3 py-2 rounded-xl bg-white/10">
                        <div class="w-8 h-8 rounded-lg bg-pu-gold text-pu-navy font-extrabold flex items-center justify-center shrink-0 text-xs uppercase shadow-sm">
                            {{ substr(auth()->user()->name, 0, 1) }}
                        </div>
                        <div class="min-w-0">
                            <p class="text-[10px] text-sky-200/75 leading-none">Administrator</p>
                            <p class="font-bold text-white truncate mt-1 leading-tight">{{ auth()->user()->name }}</p>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                                class="group w-full flex items-center justify-center gap-2 bg-red-600 hover:bg-red-500 active:scale-[0.98] text-white py-2.5 px-3 rounded-xl font-bold transition-all duration-200 shadow-md">
                            <svg class="w-4 h-4 shrink-0 transition-transform duration-200 group-hover:translate-x-1" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                            </svg>
                            <span class="admin-sidebar-label">Keluar</span>
                        </button>
                    </form>
                </div>
            </aside>

            <!-- TOMBOL BUKA-TUTUP BULAT KUNING DI TENGAH TEPI KANAN SIDEBAR (DESKTOP & HP) -->
            <button type="button" onclick="toggleAdminSidebar()" title="Buka / Tutup Sidebar" aria-label="Buka / Tutup Sidebar"
                    style="top: 50%; right: -13px; transform: translateY(-50%);"
                    class="flex items-center justify-center w-6 h-9 rounded-lg bg-pu-gold hover:bg-amber-400 active:scale-95 text-pu-navy shadow-lg border-2 border-white absolute z-50 transition-all duration-200 cursor-pointer">
                <svg id="sidebar-chevron-icon" class="w-3.5 h-3.5 transition-transform duration-300" fill="none" stroke="currentColor" stroke-width="2.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                </svg>
            </button>
        </div>

        <!-- ================= KONTEN UTAMA ADMIN ================= -->
        <div class="flex-1 flex flex-col min-w-0 min-h-screen">
            <!-- Top Bar -->
            <header class="h-16 bg-white/95 backdrop-blur-md px-4 sm:px-6 flex items-center justify-between sticky top-0 z-20 shadow-sm">
                <div class="flex items-center gap-3">
                    <!-- Tombol Buka Sidebar di Layar Kecil / HP -->
                    <button type="button" onclick="toggleAdminSidebar()"
                            title="Buka Menu Sidebar" aria-label="Buka Menu Sidebar"
                            class="lg:hidden group p-2 rounded-xl bg-slate-100 hover:bg-pu-navy active:scale-95 text-pu-navy hover:text-pu-gold transition-all duration-200">
                        <svg class="w-4 h-4 transition-transform duration-200 group-hover:scale-110" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>
                    <div>
                        <h1 class="font-extrabold text-sm sm:text-base text-pu-navy tracking-tight leading-tight">
                            @yield('title', 'Panel Admin')
                        </h1>
                        <p class="text-[11px] text-slate-500 hidden sm:block mt-0.5 leading-tight">
                            Sistem Informasi Spasial & Survei Infrastruktur Kementerian PU
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-2.5">
                    @yield('header_actions')
                    <a href="{{ route('landing') }}"
                       class="group bg-pu-navy hover:bg-pu-gold active:scale-[0.98] text-white hover:text-pu-navy px-3.5 py-2 rounded-xl text-xs font-bold shadow-sm hover:shadow-md transition-all duration-200 inline-flex items-center gap-2 whitespace-nowrap">
                        <svg class="w-3.5 h-3.5 text-pu-gold group-hover:text-pu-navy transition-transform duration-200 group-hover:rotate-6" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                            <polygon points="1 6 1 22 8 18 16 22 23 18 23 2 16 6 8 2 1 6"/>
                            <line x1="8" y1="2" x2="8" y2="18"/>
                            <line x1="16" y1="6" x2="16" y2="22"/>
                        </svg>
                        <span>Peta GIS</span>
                        <svg class="w-3 h-3 opacity-80 transition-transform duration-200 group-hover:translate-x-1" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>
                </div>
            </header>

            <!-- Area Konten & Alert Notifikasi -->
            <main class="flex-1 p-4 sm:p-6 max-w-7xl w-full mx-auto space-y-5">
                @if(session('success'))
                    <div class="bg-emerald-600 text-white px-4 py-3 rounded-xl shadow-sm text-xs font-semibold flex justify-between items-center">
                        <span class="inline-flex items-center gap-2">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span>{{ session('success') }}</span>
                        </span>
                        <button type="button" onclick="this.parentElement.remove()" class="hover:opacity-80 p-0.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                @endif

                @if($errors->any())
                    <div class="bg-red-600 text-white px-4 py-3 rounded-xl shadow-sm text-xs font-semibold">
                        <ul class="list-disc pl-4 space-y-0.5">
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
            const wrapper = document.getElementById('admin-sidebar-wrapper');
            const backdrop = document.getElementById('admin-sidebar-backdrop');
            const chevron = document.getElementById('sidebar-chevron-icon');
            const isMobile = window.innerWidth < 1024;

            if (isMobile) {
                const isClosed = wrapper.classList.contains('-translate-x-full');
                wrapper.classList.toggle('-translate-x-full', !isClosed);
                backdrop.classList.toggle('hidden', !isClosed);
                if (chevron) {
                    chevron.style.transform = isClosed ? 'rotate(0deg)' : 'rotate(180deg)';
                }
            } else {
                isAdminSidebarCollapsed = !isAdminSidebarCollapsed;
                const labels = wrapper.querySelectorAll('.admin-sidebar-label');
                wrapper.style.width = isAdminSidebarCollapsed ? '74px' : '264px';
                labels.forEach(el => el.classList.toggle('hidden', isAdminSidebarCollapsed));
                if (chevron) {
                    chevron.style.transform = isAdminSidebarCollapsed ? 'rotate(180deg)' : 'rotate(0deg)';
                }
            }
        }

        window.addEventListener('resize', () => {
            const wrapper = document.getElementById('admin-sidebar-wrapper');
            const backdrop = document.getElementById('admin-sidebar-backdrop');
            if (window.innerWidth >= 1024) {
                backdrop.classList.add('hidden');
                wrapper.style.width = isAdminSidebarCollapsed ? '74px' : '264px';
            } else {
                wrapper.style.width = '264px';
            }
        });
    </script>
    @stack('scripts')
</body>
</html>