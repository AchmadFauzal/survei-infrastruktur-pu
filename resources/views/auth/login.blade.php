<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Petugas - Web-GIS Infrastruktur Kementerian PU</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-slate-950 flex items-center justify-center p-4 relative overflow-hidden font-sans">

    <!-- Aksen Dekoratif Latar Belakang Grid Peta -->
    <div class="absolute inset-0 opacity-20 pointer-events-none"
         style="background-image: radial-gradient(#38bdf8 1px, transparent 1px); background-size: 28px 28px;"></div>
    <div class="absolute -top-32 -left-32 w-96 h-96 bg-blue-600/30 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute -bottom-32 -right-32 w-96 h-96 bg-amber-500/20 rounded-full blur-3xl pointer-events-none"></div>

    <div class="relative z-10 w-full max-w-4xl bg-white rounded-3xl shadow-2xl overflow-hidden grid grid-cols-1 md:grid-cols-12">
        
        <!-- Panel Kiri: Identitas Sistem Web-GIS PU -->
        <div class="md:col-span-5 bg-gradient-to-br from-blue-950 via-slate-900 to-blue-900 text-white p-8 flex flex-col justify-between">
            <div>
                <div class="inline-flex items-center gap-2 bg-amber-400/20 border border-amber-400/40 text-amber-300 text-[11px] font-bold px-3 py-1 rounded-full mb-5">
                    <span>🗺️</span> SISTEM INFORMASI SPASIAL
                </div>
                <h1 class="text-2xl font-extrabold leading-snug tracking-tight">
                    Web-GIS Survei Infrastruktur
                </h1>
                <p class="text-amber-400 font-bold text-sm mt-1">Kementerian Pekerjaan Umum</p>
                <p class="text-slate-300 text-xs leading-relaxed mt-4">
                    Platform akuisisi koordinat lapangan, dokumentasi foto ber-watermark GPS, serta penyusunan Berita Acara survei infrastruktur secara real-time.
                </p>
            </div>

            <div class="mt-8 pt-6 border-t border-slate-700/80 space-y-2.5 text-xs text-slate-300">
                <div class="flex items-center gap-2.5">
                    <span class="w-6 h-6 rounded-lg bg-emerald-500/20 text-emerald-400 flex items-center justify-center font-bold">✓</span>
                    <span>Pemetaan Interaktif BM, CK, SDA & PS</span>
                </div>
                <div class="flex items-center gap-2.5">
                    <span class="w-6 h-6 rounded-lg bg-blue-500/20 text-blue-400 flex items-center justify-center font-bold">✓</span>
                    <span>Kamera Geotagging & Cap Waktu Otomatis</span>
                </div>
                <div class="flex items-center gap-2.5">
                    <span class="w-6 h-6 rounded-lg bg-amber-500/20 text-amber-400 flex items-center justify-center font-bold">✓</span>
                    <span>Export Berita Acara Excel & TTD Digital</span>
                </div>
            </div>
        </div>

        <!-- Panel Kanan: Form Login -->
        <div class="md:col-span-7 p-8 sm:p-10 flex flex-col justify-center bg-white">
            <div class="mb-6">
                <h2 class="text-xl font-extrabold text-gray-900">Masuk ke Akun Petugas</h2>
                <p class="text-xs text-gray-500 mt-1">Gunakan email dan kata sandi yang terdaftar sebagai Admin atau Surveyor.</p>
            </div>

            @if (session('status'))
                <div class="mb-4 bg-green-50 border-l-4 border-green-600 text-green-700 p-3 rounded text-xs">
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-4 bg-red-50 border-l-4 border-red-600 text-red-700 p-3 rounded text-xs">
                    <p class="font-bold">Gagal Masuk:</p>
                    <ul class="list-disc pl-4 mt-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-4 text-xs">
                @csrf

                <div>
                    <label for="email" class="block font-bold text-gray-700 mb-1.5">Alamat Email</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                           placeholder="nama@pu.go.id"
                           class="w-full border border-gray-300 focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20 rounded-xl px-4 py-2.5 text-sm outline-none transition">
                </div>

                <div>
                    <div class="flex justify-between items-center mb-1.5">
                        <label for="password" class="font-bold text-gray-700">Kata Sandi</label>
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="text-blue-600 hover:underline font-semibold">
                                Lupa kata sandi?
                            </a>
                        @endif
                    </div>
                    <input id="password" type="password" name="password" required autocomplete="current-password"
                           placeholder="••••••••"
                           class="w-full border border-gray-300 focus:border-blue-600 focus:ring-2 focus:ring-blue-600/20 rounded-xl px-4 py-2.5 text-sm outline-none transition">
                </div>

                <div class="flex items-center justify-between pt-1">
                    <label for="remember_me" class="inline-flex items-center gap-2 cursor-pointer text-gray-600">
                        <input id="remember_me" type="checkbox" name="remember" class="w-4 h-4 accent-blue-600 rounded">
                        <span>Ingat sesi login saya</span>
                    </label>
                </div>

                <button type="submit"
                        class="w-full bg-blue-700 hover:bg-blue-800 text-white font-bold py-3 rounded-xl shadow-lg shadow-blue-700/30 transition text-sm mt-2">
                    Masuk ke Sistem →
                </button>
            </form>

            <div class="mt-6 pt-5 border-t border-gray-100 flex justify-between items-center text-xs text-gray-500">
                <a href="{{ route('landing') }}" class="text-slate-700 hover:text-blue-700 font-semibold flex items-center gap-1">
                    ← Kembali ke Peta Publik
                </a>
                <span>© {{ date('Y') }} Pusdatin PU</span>
            </div>
        </div>
    </div>

</body>
</html>