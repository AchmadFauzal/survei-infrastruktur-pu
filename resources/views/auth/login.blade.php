<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk Petugas — InfraLens | Kementerian PU</title>
    
    <link rel="icon" type="image/png" href="{{ asset('images/Logo_Pu.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            background-color: #e2f3fc;
            background-image: url('https://ehrm.pu.go.id/dev/assets/bg-index-oFXyhgte.png');
            background-size: 620px auto;
            background-repeat: no-repeat;
            background-position: right bottom;
            background-attachment: fixed;
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4 sm:p-6 relative overflow-x-hidden font-sans antialiased text-slate-800">

    <!-- Card 2 Kolom Proporsional -->
    <div class="relative z-10 w-full max-w-4xl bg-white/95 backdrop-blur-md rounded-2xl shadow-[0_20px_50px_rgba(0,41,102,0.14)] border border-white/90 overflow-hidden grid grid-cols-1 md:grid-cols-12">
        
        <!-- Panel Kiri: Brand Visual -->
        <div class="md:col-span-5 bg-gradient-to-br from-pu-navy via-[#002255] to-pu-dark text-white p-8 sm:p-9 flex flex-col justify-between relative overflow-hidden">
            <div class="absolute -bottom-20 -right-20 w-64 h-64 rounded-full bg-pu-gold/10 blur-2xl pointer-events-none"></div>
            <div class="absolute -top-16 -left-16 w-48 h-48 rounded-full bg-sky-400/10 blur-2xl pointer-events-none"></div>

            <!-- Badge Atas -->
            <div class="relative z-10">
                <div class="inline-flex items-center gap-2 bg-white/10 border border-white/15 text-pu-gold text-[11px] font-bold px-3 py-1 rounded-full tracking-wide uppercase">
                    <span class="w-2 h-2 rounded-full bg-pu-gold animate-pulse"></span>
                    Web-GIS Infrastruktur
                </div>
            </div>

            <!-- Logo & Nama Utama -->
            <div class="relative z-10 my-10">
                <div class="w-16 h-16 rounded-2xl bg-white/10 border border-white/15 backdrop-blur-sm p-2.5 flex items-center justify-center shadow-lg mb-5">
                    <img src="{{ asset('images/Logo_Pu.png') }}" alt="Logo Kementerian PU" class="w-full h-full object-contain">
                </div>

                <h1 class="text-3xl font-extrabold tracking-tight text-white leading-none">
                    Infra<span class="text-pu-gold">Lens</span>
                </h1>
                <p class="text-sm font-semibold text-sky-200 mt-2">
                    Kementerian Pekerjaan Umum
                </p>
                <div class="w-12 h-1 bg-pu-gold rounded-full mt-4"></div>
            </div>

            <!-- Footer Panel Kiri -->
            <div class="relative z-10 text-[11px] text-slate-300/80">
                Sistem Informasi Spasial & Survei Lapangan
            </div>
        </div>

        <!-- Panel Kanan: Form Login -->
        <div class="md:col-span-7 p-8 sm:p-10 flex flex-col justify-center bg-white/95">
            <div class="mb-6">
                <h2 class="text-xl font-bold text-pu-navy tracking-tight">Masuk ke Sistem</h2>
                <p class="text-xs text-slate-500 mt-1">
                    Gunakan akun <strong class="text-slate-700 font-semibold">Admin</strong> atau <strong class="text-slate-700 font-semibold">Surveyor</strong> terdaftar.
                </p>
            </div>

            @if (session('status'))
                <div class="mb-4 bg-emerald-50 border border-emerald-200 text-emerald-700 px-3.5 py-2.5 rounded-xl text-xs font-medium">
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-3.5 py-2.5 rounded-xl text-xs">
                    <p class="font-bold">Gagal Masuk:</p>
                    <ul class="list-disc pl-4 mt-1 space-y-0.5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-4">
                @csrf

                <!-- Email -->
                <div>
                    <label for="email" class="block text-xs font-bold text-slate-700 mb-1.5">Alamat Email</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                           placeholder="nama@pu.go.id"
                           class="w-full bg-slate-50/80 border border-slate-300 focus:bg-white focus:border-pu-navy focus:ring-2 focus:ring-pu-navy/15 rounded-xl px-4 py-2.5 text-sm text-slate-800 placeholder:text-slate-400 outline-none transition">
                </div>

                <!-- Kata Sandi -->
                <div>
                    <div class="flex justify-between items-center mb-1.5">
                        <label for="password" class="text-xs font-bold text-slate-700">Kata Sandi</label>
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="text-xs text-pu-navy hover:underline font-semibold">
                                Lupa kata sandi?
                            </a>
                        @endif
                    </div>
                    <div class="relative">
                        <input id="password" type="password" name="password" required autocomplete="current-password"
                               placeholder="••••••••"
                               class="w-full bg-slate-50/80 border border-slate-300 focus:bg-white focus:border-pu-navy focus:ring-2 focus:ring-pu-navy/15 rounded-xl px-4 py-2.5 pr-10 text-sm text-slate-800 placeholder:text-slate-400 outline-none transition">
                        <button type="button" onclick="togglePassword()" aria-label="Tampilkan Kata Sandi"
                                class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-pu-navy transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Ingat Saya -->
                <div class="flex items-center pt-0.5">
                    <label for="remember_me" class="inline-flex items-center gap-2 cursor-pointer text-xs text-slate-600 select-none">
                        <input id="remember_me" type="checkbox" name="remember" class="w-4 h-4 accent-[#002966] rounded border-slate-300">
                        <span>Ingat sesi login saya</span>
                    </label>
                </div>

                <!-- Tombol Submit -->
                <button type="submit"
                        class="group w-full bg-pu-navy hover:bg-pu-gold active:bg-pu-gold text-white hover:text-pu-navy active:text-pu-navy font-bold py-3 px-4 rounded-xl shadow-[0_6px_16px_rgba(0,41,102,0.22)] hover:shadow-[0_6px_18px_rgba(250,189,3,0.35)] transition-all duration-200 flex items-center justify-center gap-2 text-sm mt-2">
                    <svg class="w-4 h-4 text-pu-gold group-hover:text-pu-navy group-active:text-pu-navy transition-colors" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 11a1.5 1.5 0 1 0 0 3 1.5 1.5 0 0 0 0-3zm0 3v2"/>
                    </svg>
                    <span>Masuk ke Sistem</span>
                </button>
            </form>

            <!-- Footer -->
            <div class="mt-7 pt-5 border-t border-slate-100 flex justify-between items-center text-xs text-slate-400">
                <a href="{{ route('landing') }}" class="text-pu-navy hover:text-blue-700 font-semibold inline-flex items-center gap-1.5 transition">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    <span>Kembali ke Peta Publik</span>
                </a>
                <span>© {{ date('Y') }} InfraLens — Kementerian PU</span>
            </div>
        </div>
    </div>

    <script>
        function togglePassword() {
            const pwdInput = document.getElementById('password');
            pwdInput.type = pwdInput.type === 'password' ? 'text' : 'password';
        }
    </script>
</body>
</html>