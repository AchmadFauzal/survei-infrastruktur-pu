<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>InfraLens — Web-GIS Infrastruktur Kementerian PU</title>
    
    <link rel="icon" type="image/png" href="{{ asset('images/Logo_Pu.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="{{ asset('css/gis/map-ui.css') }}?v=1" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
</head>
<body>

    <!-- ================= TOP BAR ================= -->
    <header class="hud-topbar">
        <!-- Kiri: Brand InfraLens -->
        <a href="{{ route('landing') }}" class="brand-pill">
            <img src="{{ asset('images/Logo_Pu.png') }}" alt="Logo PU">
            <div class="brand-text">
                <span class="brand-title">Infra<span>Lens</span></span>
                <span class="brand-sub">Kementerian PU</span>
            </div>
        </a>

        <!-- Tengah: Searchbar -->
        <div class="search-wrapper">
            <div class="search-box">
                <svg width="16" height="16" fill="none" stroke="#94a3b8" stroke-width="2.2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 104.5 4.5a7.5 7.5 0 0012.15 12.15z"/>
                </svg>
                <input type="text" id="map-search-input" autocomplete="off" placeholder="Cari infrastruktur, kota, kategori...">
                <button type="button" id="btn-clear-search" onclick="clearMapSearch()" style="display:none; background:none; border:none; color:#94a3b8; cursor:pointer; padding:2px;" title="Hapus pencarian">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.4" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <div id="map-search-results" class="search-results-dropdown" style="display:none;"></div>
        </div>

        <!-- Kanan: Tombol Aksi Petugas & Ikon Langsung Dashboard -->
        <div class="right-actions">
            @auth
                <div class="toolbar-pill">
                    @if(auth()->user()->role === 'surveyor')
                        <label style="display:inline-flex; align-items:center; gap:6px; background:#fffbeb; border:1px solid #fcd34d; padding:4px 8px; border-radius:9px; cursor:pointer; font-size:11px; font-weight:700; color:#78350f;">
                            <span>Tugas Saya</span>
                            <input type="checkbox" id="toggleAssignedOnly" {{ request('filter') === 'assigned' ? 'checked' : '' }}>
                            <span id="assigned-badge-count" style="background:#d97706; color:#fff; padding:1px 5px; border-radius:5px; font-size:10px;">0</span>
                        </label>
                    @endif

                    <!-- Tombol Tambah Titik -->
                    <button type="button" id="btn-toggle-add-point" onclick="toggleAddPointMode()" class="btn-tool btn-tool-add" title="Tambah Titik Baru di Peta">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.4" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 21s-6-5.686-6-10a6 6 0 1112 0c0 4.314-6 10-6 10z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v6M9 11h6"/>
                        </svg>
                        <span class="tool-label">Tambah Titik</span>
                    </button>

                    <!-- Tombol Import -->
                    <button type="button" onclick="openImportModal()" class="btn-tool btn-tool-import" title="Import CSV atau JSON">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5 5-5M12 15V3"/>
                        </svg>
                        <span class="tool-label">Import</span>
                    </button>
                </div>

                <!-- Klik Ikon Profil Langsung Menuju Dashboard -->
                @if(auth()->user()->role === 'admin')
                    <a href="{{ route('admin.dashboard') }}" class="btn-dashboard" title="Buka Dashboard Admin">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                            <rect x="3" y="3" width="7" height="7" rx="1.5"/>
                            <rect x="14" y="3" width="7" height="7" rx="1.5"/>
                            <rect x="3" y="14" width="7" height="7" rx="1.5"/>
                            <rect x="14" y="14" width="7" height="7" rx="1.5"/>
                        </svg>
                        <span>Dashboard</span>
                    </a>
                @else
                    <a href="{{ route('surveyor.dashboard') }}" class="btn-dashboard" title="Buka Dashboard Surveyor">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                            <rect x="3" y="3" width="7" height="7" rx="1.5"/>
                            <rect x="14" y="3" width="7" height="7" rx="1.5"/>
                            <rect x="3" y="14" width="7" height="7" rx="1.5"/>
                            <rect x="14" y="14" width="7" height="7" rx="1.5"/>
                        </svg>
                        <span>Dashboard</span>
                    </a>
                @endif
            @else
                <a href="{{ route('login') }}" class="btn-dashboard" title="Masuk ke Dashboard Petugas">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/>
                        <circle cx="12" cy="7" r="4"/>
                    </svg>
                    <span>Dashboard</span>
                </a>
            @endauth
        </div>
    </header>

    <!-- ================= BOTTOM BAR: LEGENDA & BASEMAP + ZOOM ================= -->
    <div class="hud-bottombar">
        <div class="legend-pill">
            <span class="legend-item"><span class="dot-green"></span> Sudah Survei</span>
            <span class="legend-item"><span class="dot-red"></span> Belum Survei</span>
            @auth
                @if(auth()->user()->role === 'surveyor')
                    <span class="legend-item"><span class="dot-assigned"></span> Tugas Saya</span>
                @endif
            @endauth
        </div>

        <div class="map-controls-right">
            <div id="basemap-popover" class="basemap-menu" style="display:none; font-size:12px;">
                <div style="padding:4px 8px; font-size:10px; font-weight:700; color:#94a3b8; text-transform:uppercase;">Tampilan Peta</div>
                <button type="button" onclick="switchCustomBasemap('osm')" data-basemap="osm" class="basemap-opt"
                        style="width:100%; text-align:left; background:#f0f9ff; color:#002966; border:none; padding:7px 10px; border-radius:9px; font-weight:700; cursor:pointer; display:flex; justify-content:space-between; align-items:center;">
                    <span>Peta Jalan (OSM)</span>
                    <svg width="14" height="14" fill="none" stroke="#002966" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                </button>
                <button type="button" onclick="switchCustomBasemap('satellite')" data-basemap="satellite" class="basemap-opt"
                        style="width:100%; text-align:left; background:transparent; color:#334155; border:none; padding:7px 10px; border-radius:9px; font-weight:600; cursor:pointer; display:flex; justify-content:space-between; align-items:center; margin-top:2px;">
                    <span>Citra Satelit</span>
                </button>
                <button type="button" onclick="switchCustomBasemap('topo')" data-basemap="topo" class="basemap-opt"
                        style="width:100%; text-align:left; background:transparent; color:#334155; border:none; padding:7px 10px; border-radius:9px; font-weight:600; cursor:pointer; display:flex; justify-content:space-between; align-items:center; margin-top:2px;">
                    <span>Topografi</span>
                </button>
                <button type="button" onclick="switchCustomBasemap('light')" data-basemap="light" class="basemap-opt"
                        style="width:100%; text-align:left; background:transparent; color:#334155; border:none; padding:7px 10px; border-radius:9px; font-weight:600; cursor:pointer; display:flex; justify-content:space-between; align-items:center; margin-top:2px;">
                    <span>Peta Terang</span>
                </button>
            </div>

            <button type="button" onclick="toggleBasemapPopover(event)" class="btn-basemap" title="Ganti Basemap">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/>
                </svg>
            </button>

            <div class="zoom-group">
                <button type="button" onclick="if(window.map) window.map.zoomIn()" class="zoom-btn" title="Perbesar">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14M5 12h14"/>
                    </svg>
                </button>
                <button type="button" onclick="if(window.map) window.map.zoomOut()" class="zoom-btn" title="Perkecil">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- BANNER PERINGATAN MODE TAMBAH TITIK AKTIF -->
    <div id="add-point-banner" class="hidden">
        <svg width="15" height="15" fill="none" stroke="#FABD03" stroke-width="2.3" viewBox="0 0 24 24" style="flex-shrink:0;">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 21s-6-5.686-6-10a6 6 0 1112 0c0 4.314-6 10-6 10z"/>
            <circle cx="12" cy="11" r="2"/>
        </svg>
        <span><strong style="color:#FABD03;">Mode Tambah Titik Aktif:</strong> Klik lokasi pada peta</span>
        <button type="button" onclick="toggleAddPointMode()"
                style="background:#dc2626; color:#ffffff; border:none; padding:4px 10px; border-radius:8px; font-size:11px; font-weight:700; cursor:pointer; display:inline-flex; align-items:center; gap:4px; margin-left:2px;">
            <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
            </svg>
            <span>Batal</span>
        </button>
    </div>

    <!-- PANEL PANDUAN RUTE TERCEPAT MELAYANG -->
    <div id="floating-route-panel" style="display:none; position:fixed; bottom:74px; left:16px; z-index:1000; width:320px; max-width:92vw; background:#fff; border-radius:16px; box-shadow:0 16px 36px rgba(0,41,102,0.2); border:1px solid #cbd5e1; overflow:hidden; font-size:12px;">
        <div style="background:#002966; color:#fff; padding:10px 14px; display:flex; justify-content:space-between; align-items:center;">
            <div style="display:flex; align-items:center; gap:8px;">
                <svg width="16" height="16" fill="none" stroke="#FABD03" stroke-width="2.2" viewBox="0 0 24 24">
                    <polygon points="3 11 22 2 13 21 11 13 3 11"/>
                </svg>
                <div>
                    <span style="font-weight:700; display:block;">Panduan Rute Tercepat</span>
                    <span id="floating-route-stats" style="font-size:11px; color:#FABD03; font-weight:600;">Menghitung rute...</span>
                </div>
            </div>
            <div style="display:flex; gap:6px;">
                <a id="floating-gmaps-link" href="#" target="_blank" style="background:#FABD03; color:#002966; font-weight:700; padding:4px 8px; border-radius:6px; font-size:10px; text-decoration:none;">
                    Google Maps
                </a>
                <button type="button" onclick="clearActiveRouteOnMap()" title="Tutup Rute" style="background:#dc2626; color:#fff; border:none; font-weight:700; padding:4px 7px; border-radius:6px; cursor:pointer; display:inline-flex; align-items:center;">
                    <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>
        <ol id="floating-route-list" style="padding:10px; margin:0; max-height:220px; overflow-y:auto;"></ol>
    </div>

    <!-- Alert Notifikasi -->
    @if(session('success'))
        <div id="toast-alert" style="position:fixed; top:74px; right:16px; z-index:1100; background:#059669; color:#fff; padding:10px 14px; border-radius:12px; box-shadow:0 8px 20px rgba(0,0,0,0.15); font-size:12px; font-weight:600; display:flex; align-items:center; gap:8px;">
            <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
            </svg>
            <span>{{ session('success') }}</span>
            <button onclick="document.getElementById('toast-alert').remove()" style="background:none; border:none; color:#fff; cursor:pointer; display:inline-flex;">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
    @endif

    @if($errors->any())
        <div id="toast-error" style="position:fixed; top:74px; right:16px; z-index:1100; background:#dc2626; color:#fff; padding:10px 14px; border-radius:12px; box-shadow:0 8px 20px rgba(0,0,0,0.15); font-size:12px; max-width:340px;">
            <div style="display:flex; justify-content:space-between; gap:8px;">
                <ul style="margin:0; padding-left:16px;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button onclick="document.getElementById('toast-error').remove()" style="background:none; border:none; color:#fff; cursor:pointer; display:inline-flex;">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        </div>
    @endif

    <!-- Container Peta -->
    <div id="map"></div>

    @auth
    <!-- MODAL TAMBAH TITIK MANUAL -->
    <div id="modalAddPoint" class="fixed inset-0 bg-black/60 hidden items-center justify-center z-[2000] p-4">
        <div class="bg-white rounded-2xl w-full shadow-2xl max-h-[90vh] overflow-y-auto border-t-4 border-pu-navy">
            <div class="flex justify-between items-center border-b border-slate-100 pb-2.5 mb-3">
                <div class="flex items-center gap-2 text-pu-navy">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 21s-6-5.686-6-10a6 6 0 1112 0c0 4.314-6 10-6 10z"/>
                        <circle cx="12" cy="11" r="2.5"/>
                    </svg>
                    <h3 class="font-bold">Tambah Titik Infrastruktur</h3>
                </div>
                <button type="button" onclick="closeAddPointModal()" class="text-slate-400 hover:text-slate-700" style="padding:2px !important;">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form action="{{ route('infrastructure.store_point') }}" method="POST" class="space-y-2.5 text-xs">
                @csrf
                <div class="grid grid-cols-2 gap-2 bg-slate-50 p-2 rounded-xl border border-slate-200">
                    <div>
                        <label class="block font-semibold text-slate-600">Latitude (Y)</label>
                        <input type="text" name="latitude" id="manual_lat" readonly required
                               class="w-full bg-white border border-slate-300 font-mono text-slate-800">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-600">Longitude (X)</label>
                        <input type="text" name="longitude" id="manual_lng" readonly required
                               class="w-full bg-white border border-slate-300 font-mono text-slate-800">
                    </div>
                </div>

                <div>
                    <label class="block font-semibold text-slate-700">Nama Infrastruktur <span class="text-red-500">*</span></label>
                    <input type="text" name="nama_infrastruktur" id="manual_nama_infrastruktur" required placeholder="Contoh: Jembatan Krueng Meureudu"
                           class="w-full border border-slate-300 focus:border-pu-navy outline-none">
                </div>

                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block font-semibold text-slate-700">Kategori PU <span class="text-red-500">*</span></label>
                        <select name="kategori" id="manual_kategori" required onchange="updateManualJenisDropdown()"
                                class="w-full border border-slate-300 bg-white">
                            <option value="BM">BM (Bina Marga)</option>
                            <option value="CK">CK (Cipta Karya)</option>
                            <option value="PS">PS (Prasarana Strategis)</option>
                            <option value="SDA">SDA (Sumber Daya Air)</option>
                            <option value="SETJEN">SETJEN (Sekretariat Jenderal)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700">Jenis Infrastruktur <span class="text-red-500">*</span></label>
                        <div id="manual_jenis_container"></div>
                    </div>
                </div>

                <div class="bg-sky-50/70 p-2 rounded-xl border border-sky-100 space-y-1.5">
                    <div class="flex justify-between items-center">
                        <span class="text-[10.5px] font-bold text-pu-navy">Deteksi Wilayah Otomatis</span>
                        <span id="geocode-status" class="text-[10px] font-semibold text-blue-600">Mendeteksi lokasi...</span>
                    </div>

                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block font-semibold text-slate-700">Kota / Kabupaten <span class="text-red-500">*</span></label>
                            <input type="text" name="kota_kabupaten" id="manual_kota" required placeholder="Otomatis terisi..."
                                   class="w-full border border-slate-300 bg-white">
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-700">Provinsi <span class="text-red-500">*</span></label>
                            <input type="text" name="provinsi" id="manual_provinsi" required placeholder="Otomatis terisi..."
                                   class="w-full border border-slate-300 bg-white">
                        </div>
                    </div>
                </div>

                <div class="flex justify-end gap-2 pt-2.5 border-t border-slate-100">
                    <button type="button" onclick="closeAddPointModal()" class="bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold">
                        Batal
                    </button>
                    <button type="submit" class="bg-pu-navy hover:bg-pu-dark text-white font-bold shadow">
                        Simpan Titik
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL IMPORT CSV / JSON -->
    <div id="modalImportData" class="fixed inset-0 bg-black/60 hidden items-center justify-center z-[2000] p-4">
        <div class="bg-white rounded-2xl w-full shadow-2xl border-t-4 border-pu-navy">
            <div class="flex justify-between items-center border-b border-slate-100 pb-2.5 mb-3">
                <div class="flex items-center gap-2 text-pu-navy">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5 5-5M12 15V3"/>
                    </svg>
                    <h3 class="font-bold">Import Data Infrastruktur (CSV / JSON)</h3>
                </div>
                <button type="button" onclick="closeImportModal()" class="text-slate-400 hover:text-slate-700" style="padding:2px !important;">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form action="{{ route('infrastructure.import') }}" method="POST" enctype="multipart/form-data" class="space-y-3 text-xs">
                @csrf
                <div class="bg-slate-50 p-2.5 rounded-xl border border-slate-200 text-slate-600 space-y-1">
                    <p class="font-bold text-slate-800">Format Kolom yang Didukung:</p>
                    <p>• <strong>Wajib:</strong> <code class="bg-slate-200 px-1 rounded">nama_infrastruktur</code>, <code class="bg-slate-200 px-1 rounded">latitude</code>, <code class="bg-slate-200 px-1 rounded">longitude</code></p>
                    <p>• <strong>Opsional:</strong> <code class="bg-slate-200 px-1 rounded">kategori</code> (BM/CK/PS/SDA/SETJEN), <code class="bg-slate-200 px-1 rounded">jenis_infrastruktur</code>, <code class="bg-slate-200 px-1 rounded">kota_kabupaten</code>, <code class="bg-slate-200 px-1 rounded">provinsi</code></p>
                </div>

                <div>
                    <label class="block font-semibold text-slate-700">Pilih File (.csv atau .json)</label>
                    <input type="file" name="file_import" accept=".csv,.json,.txt" required
                           class="w-full border border-slate-300 bg-slate-50">
                </div>

                <div class="flex justify-end gap-2 pt-2.5 border-t border-slate-100">
                    <button type="button" onclick="closeImportModal()" class="bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold">
                        Batal
                    </button>
                    <button type="submit" class="bg-pu-navy hover:bg-pu-dark text-white font-bold shadow">
                        Mulai Import Data
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endauth

    <!-- Konfigurasi Data dari Laravel ke JavaScript Eksternal -->
    <script>
        window.GIS_CONFIG = {
            infrastructures: @json($infrastructures),
            isAuthenticated: @json(auth()->check()),
            currentUserId: @json(auth()->id()),
            currentUserRole: @json(auth()->user()->role ?? null),
            csrfToken: "{{ csrf_token() }}",
            routes: {
                wilayahProv: "{{ route('api.wilayah.geojson', ['level' => 1]) }}",
                wilayahKab: "{{ route('api.wilayah.geojson', ['level' => 2]) }}"
            }
        };
    </script>

    <!-- File JavaScript Modular -->
    <script src="{{ asset('js/gis/infra-categories.js') }}?v=1"></script>
    <script src="{{ asset('js/gis/map-boundaries.js') }}?v=1"></script>
    <script src="{{ asset('js/gis/map-core.js') }}?v=2"></script>
    <script src="{{ asset('js/gis/map-ui.js') }}?v=1"></script>
</body>
</html>