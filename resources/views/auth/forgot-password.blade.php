<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Kata Sandi — InfraLens | Kementerian PU</title>

    <link rel="icon" type="image/png" href="{{ asset('images/Logo_Pu.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        *, *::before, *::after {
            box-sizing: border-box;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #e6f2fa;
            background-image:
                radial-gradient(circle at 12% 18%, rgba(0, 35, 89, 0.08) 0%, transparent 45%),
                url('https://ehrm.pu.go.id/dev/assets/bg-index-oFXyhgte.png');
            background-size: auto, 280px auto;
            background-repeat: no-repeat, no-repeat;
            background-position: left top, right bottom;
            background-attachment: fixed, fixed;
            margin: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 16px;
            color: #1e293b;
        }

        @media (min-width: 768px) {
            body {
                background-size: auto, 560px auto;
                padding: 24px;
            }
        }

        /* Kartu Utama 2 Kolom */
        .pu-auth-card {
            position: relative;
            z-index: 10;
            width: 100%;
            max-width: 880px;
            border-radius: 22px;
            overflow: hidden;
            box-shadow: 0 22px 55px rgba(0, 35, 89, 0.16);
            border: 1px solid rgba(255, 255, 255, 0.95);
            background-color: #ffffff;
            display: grid;
            grid-template-columns: 1fr;
        }

        @media (min-width: 768px) {
            .pu-auth-card {
                border-radius: 26px;
                grid-template-columns: 5fr 7fr;
            }
        }

        /* ================= PANEL KIRI (BRAND VISUAL) ================= */
        .pu-brand-panel {
            position: relative;
            overflow: hidden;
            padding: 22px 24px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            background: linear-gradient(155deg, #002359 0%, #001a44 55%, #00112e 100%);
            background-image:
                linear-gradient(to right, rgba(255, 255, 255, 0.045) 1px, transparent 1px),
                linear-gradient(to bottom, rgba(255, 255, 255, 0.045) 1px, transparent 1px),
                linear-gradient(155deg, #002359 0%, #001a44 55%, #00112e 100%);
            background-size: 24px 24px, 24px 24px, 100% 100%;
            color: #ffffff;
        }

        @media (min-width: 768px) {
            .pu-brand-panel {
                padding: 36px;
            }
        }

        /* Badge Atas Panel Kiri */
        .pu-top-badge {
            display: none;
        }
        @media (min-width: 768px) {
            .pu-top-badge {
                display: inline-flex;
                align-items: center;
                gap: 8px;
                padding: 6px 14px;
                border-radius: 9999px;
                background: rgba(255, 255, 255, 0.1);
                border: 1px solid rgba(255, 255, 255, 0.18);
                color: #FABD03;
                font-size: 11px;
                font-weight: 800;
                letter-spacing: 0.06em;
                text-transform: uppercase;
            }
        }

        /* Kontainer Tengah Panel Kiri */
        .pu-brand-center {
            position: relative;
            z-index: 10;
            display: flex;
            align-items: center;
            gap: 14px;
        }

        @media (min-width: 768px) {
            .pu-brand-center {
                display: block;
                margin-top: 28px;
                margin-bottom: 28px;
            }
        }

        .pu-logo-box {
            width: 50px;
            height: 50px;
            border-radius: 14px;
            background-color: #ffffff;
            padding: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.25), 0 0 0 3px rgba(255, 255, 255, 0.14);
        }

        @media (min-width: 768px) {
            .pu-logo-box {
                width: 64px;
                height: 64px;
                border-radius: 18px;
                padding: 10px;
                margin-bottom: 20px;
                box-shadow: 0 10px 25px rgba(0, 0, 0, 0.25), 0 0 0 4px rgba(255, 255, 255, 0.14);
            }
        }

        .pu-brand-divider {
            display: none;
        }
        @media (min-width: 768px) {
            .pu-brand-divider {
                display: block;
                width: 48px;
                height: 4px;
                border-radius: 9999px;
                background-color: #FABD03;
                margin-top: 16px;
                margin-bottom: 22px;
            }
        }

        /* Daftar Fitur & Footer Panel Kiri (Tampil di Desktop) */
        .pu-feature-list {
            display: none;
        }
        @media (min-width: 768px) {
            .pu-feature-list {
                display: flex;
                flex-direction: column;
                gap: 12px;
                font-size: 12px;
                color: rgba(224, 242, 254, 0.92);
            }
        }

        .pu-feature-item {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .pu-feature-icon {
            width: 24px;
            height: 24px;
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.12);
            color: #FABD03;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .pu-brand-footer {
            display: none;
        }
        @media (min-width: 768px) {
            .pu-brand-footer {
                position: relative;
                z-index: 10;
                display: flex;
                align-items: center;
                justify-content: space-between;
                padding-top: 16px;
                border-top: 1px solid rgba(255, 255, 255, 0.14);
                font-size: 11px;
                color: rgba(186, 230, 253, 0.8);
            }
        }

        /* ================= PANEL KANAN (FORM) ================= */
        .pu-form-panel {
            padding: 24px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            background-color: #ffffff;
        }

        @media (min-width: 768px) {
            .pu-form-panel {
                padding: 40px;
            }
        }

        .pu-input-field {
            width: 100%;
            height: 46px;
            padding-left: 44px;
            padding-right: 16px;
            background-color: #f8fafc;
            border: 1.5px solid #cbd5e1;
            border-radius: 14px;
            font-size: 16px;
            color: #1e293b;
            outline: none;
            transition: all 0.2s ease;
            -webkit-appearance: none;
        }

        @media (min-width: 640px) {
            .pu-input-field {
                font-size: 14px;
            }
        }

        .pu-input-field:focus {
            background-color: #ffffff;
            border-color: #002359;
            box-shadow: 0 0 0 3px rgba(0, 35, 89, 0.14);
        }

        .pu-input-icon-left {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            width: 18px;
            height: 18px;
            color: #94a3b8;
            pointer-events: none;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Tombol Submit PU */
        .btn-pu-primary {
            width: 100%;
            min-height: 46px;
            padding: 12px 16px;
            border: none;
            border-radius: 14px;
            background-color: #002359;
            color: #ffffff;
            font-weight: 800;
            font-size: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            cursor: pointer;
            box-shadow: 0 8px 20px rgba(0, 35, 89, 0.22);
            transition: all 0.22s cubic-bezier(0.22, 1, 0.36, 1);
        }
        .btn-pu-primary .btn-icon {
            color: #FABD03;
            transition: color 0.22s ease, transform 0.22s ease;
        }
        .btn-pu-primary:hover,
        .btn-pu-primary:focus-visible {
            background-color: #FABD03;
            color: #002359;
            transform: translateY(-1px);
            box-shadow: 0 10px 24px -4px rgba(250, 189, 3, 0.45);
        }
        .btn-pu-primary:hover .btn-icon,
        .btn-pu-primary:focus-visible .btn-icon {
            color: #002359;
            transform: translateX(2px);
        }
        .btn-pu-primary:active {
            background-color: #FABD03;
            color: #002359;
            transform: scale(0.99);
        }
    </style>
</head>
<body>

    <div class="pu-auth-card">

        <!-- ================= PANEL KIRI: BRAND VISUAL PU ================= -->
        <div class="pu-brand-panel">
            <!-- Dekorasi Cahaya Halus -->
            <div style="position: absolute; bottom: -90px; right: -90px; width: 260px; height: 260px; border-radius: 9999px; pointer-events: none; background: radial-gradient(circle, rgba(250,189,3,0.18) 0%, transparent 70%);"></div>
            <div style="position: absolute; top: -80px; left: -80px; width: 220px; height: 220px; border-radius: 9999px; pointer-events: none; background: radial-gradient(circle, rgba(56,189,248,0.15) 0%, transparent 70%);"></div>

            <!-- Badge Atas (Desktop) -->
            <div style="position: relative; z-index: 10;">
                <div class="pu-top-badge">
                    <span style="width: 8px; height: 8px; border-radius: 9999px; background-color: #FABD03;"></span>
                    <span>Web-GIS Infrastruktur</span>
                </div>
            </div>

            <!-- Logo, Nama Sistem & Panduan -->
            <div class="pu-brand-center">
                <div class="pu-logo-box">
                    <img src="{{ asset('images/Logo_Pu.png') }}" alt="Logo Kementerian PU" style="width: 100%; height: 100%; object-fit: contain;">
                </div>

                <div>
                    <h1 style="margin: 0; font-size: 28px; font-weight: 800; letter-spacing: -0.02em; color: #ffffff; line-height: 1.1;">
                        Infra<span style="color: #FABD03;">Lens</span>
                    </h1>
                    <p style="margin: 6px 0 0 0; font-size: 13px; font-weight: 700; color: #bae6fd;">
                        Kementerian Pekerjaan Umum
                    </p>
                </div>

                <div class="pu-brand-divider"></div>

                <div class="pu-feature-list">
                    <div class="pu-feature-item">
                        <span class="pu-feature-icon">
                            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.4" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                        </span>
                        <span>Tautan Reset Dikirim ke Email Terdaftar</span>
                    </div>
                    <div class="pu-feature-item">
                        <span class="pu-feature-icon">
                            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.4" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                        </span>
                        <span>Verifikasi Keamanan Akun Petugas</span>
                    </div>
                </div>
            </div>

            <!-- Footer Panel Kiri (Desktop) -->
            <div class="pu-brand-footer">
                <span>Pemulihan Akses Akun</span>
                <span style="font-weight: 700; color: #FABD03;">InfraLens PU</span>
            </div>
        </div>

        <!-- ================= PANEL KANAN: FORM LUPA KATA SANDI ================= -->
        <div class="pu-form-panel">
            <div style="margin-bottom: 22px;">
                <span style="font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.06em; color: rgba(0, 35, 89, 0.7);">
                    Pemulihan Kredensial
                </span>
                <h2 style="margin: 4px 0 0 0; font-size: 24px; font-weight: 800; letter-spacing: -0.02em; color: #002359;">
                    Lupa Kata Sandi?
                </h2>
                <p style="margin: 6px 0 0 0; font-size: 12px; color: #64748b; line-height: 1.6;">
                    Masukkan alamat email akun <strong style="color: #334155;">Administrator</strong> atau <strong style="color: #334155;">Surveyor</strong> Anda. Kami akan mengirimkan tautan resmi untuk mengatur ulang kata sandi.
                </p>
            </div>

            <!-- Alert Status Pengiriman Link -->
            @if (session('status'))
                <div style="margin-bottom: 16px; background-color: #ecfdf5; border: 1px solid #a7f3d0; color: #047857; padding: 12px 14px; border-radius: 12px; font-size: 12px; font-weight: 600; display: flex; align-items: flex-start; gap: 10px;">
                    <svg width="16" height="16" style="flex-shrink: 0; margin-top: 1px;" fill="none" stroke="currentColor" stroke-width="2.4" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>{{ session('status') }}</span>
                </div>
            @endif

            <!-- Alert Error Validasi -->
            @if ($errors->any())
                <div style="margin-bottom: 16px; background-color: #fef2f2; border: 1px solid #fecaca; color: #b91c1c; padding: 12px 14px; border-radius: 12px; font-size: 12px;">
                    <div style="display: flex; align-items: center; gap: 6px; font-weight: 800;">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.4" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                        </svg>
                        <span>Gagal Mengirim Tautan</span>
                    </div>
                    <ul style="margin: 6px 0 0 0; padding-left: 20px;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}">
                @csrf

                <!-- Alamat Email -->
                <div style="margin-bottom: 18px;">
                    <label for="email" style="display: block; font-size: 12px; font-weight: 700; color: #334155; margin-bottom: 6px;">
                        Alamat Email Terdaftar
                    </label>
                    <div style="position: relative;">
                        <span class="pu-input-icon-left">
                            <svg width="17" height="17" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                        </span>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="email"
                               placeholder="nama@pu.go.id"
                               class="pu-input-field">
                    </div>
                </div>

                <!-- Tombol Kirim Tautan -->
                <button type="submit" class="btn-pu-primary">
                    <svg class="btn-icon" width="17" height="17" fill="none" stroke="currentColor" stroke-width="2.3" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                    <span>Kirim Tautan Reset Kata Sandi</span>
                </button>
            </form>

            <!-- Footer -->
            <div style="margin-top: 26px; padding-top: 18px; border-top: 1px solid #f1f5f9; display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; gap: 8px; font-size: 12px; color: #94a3b8;">
                <a href="{{ route('login') }}" style="color: #002359; font-weight: 700; text-decoration: none; display: inline-flex; align-items: center; gap: 6px;">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    <span>Kembali ke Halaman Login</span>
                </a>
                <span>© {{ date('Y') }} InfraLens — Kementerian PU</span>
            </div>
        </div>
    </div>

</body>
</html>