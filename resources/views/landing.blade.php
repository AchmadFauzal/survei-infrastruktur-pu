<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Web-GIS Survei Infrastruktur PU</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <style>
        #map { height: 100vh; width: 100vw; z-index: 1; }
        .leaflet-popup-content { width: 325px !important; max-height: 460px; overflow-y: auto; }
        .leaflet-top.leaflet-right { top: 76px; }

        /* Hapus kotak hitam focus outline saat poligon wilayah diklik */
        .leaflet-interactive,
        .leaflet-interactive:focus,
        svg path:focus {
            outline: none !important;
            box-shadow: none !important;
        }
    </style>
</head>
<body class="m-0 p-0 overflow-hidden font-sans">

    <!-- Top Floating Navbar -->
    <div class="fixed top-4 left-14 right-4 z-[1000] flex flex-wrap justify-between items-center gap-2 pointer-events-none">
        <!-- Brand & Legenda -->
        <div class="bg-white/95 backdrop-blur px-4 py-2.5 rounded-xl shadow-lg border border-gray-200 pointer-events-auto">
            <h1 class="font-bold text-gray-800 text-sm md:text-base">Web-GIS Infrastruktur Kementerian PU</h1>
            <div class="flex flex-wrap gap-3 text-xs mt-1">
                <span class="flex items-center gap-1"><span class="w-3 h-3 rounded-full bg-green-600 inline-block"></span> Sudah Survei</span>
                <span class="flex items-center gap-1"><span class="w-3 h-3 rounded-full bg-red-600 inline-block"></span> Belum Survei</span>
                @auth
                    @if(auth()->user()->role === 'surveyor')
                        <span class="flex items-center gap-1"><span class="w-3 h-3 rounded-full bg-red-600 ring-2 ring-amber-400 inline-block"></span> Tugas Saya</span>
                    @endif
                @endauth
            </div>
        </div>

        <!-- Panel Tombol Aksi Petugas -->
        <div class="bg-white/95 backdrop-blur px-3 py-2 rounded-xl shadow-lg border border-gray-200 pointer-events-auto flex flex-wrap items-center gap-2">
            @auth
                @if(auth()->user()->role === 'surveyor')
                    <label class="inline-flex items-center gap-2 bg-amber-50 border border-amber-300 px-2.5 py-1 rounded-lg cursor-pointer select-none"
                           title="Filter tampilan titik di peta">
                        <span class="text-[11px] font-bold text-amber-900">Tugas Saya Saja</span>
                        <div class="relative inline-flex items-center">
                            <input type="checkbox" id="toggleAssignedOnly" class="sr-only peer"
                                   {{ request('filter') === 'assigned' ? 'checked' : '' }}>
                            <div class="w-9 h-5 bg-gray-300 rounded-full peer peer-checked:bg-amber-600 after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:after:translate-x-4"></div>
                        </div>
                        <span id="assigned-badge-count" class="text-[10px] font-extrabold bg-amber-600 text-white px-1.5 py-0.5 rounded">0</span>
                    </label>
                @endif

                <button type="button" id="btn-toggle-add-point" onclick="toggleAddPointMode()"
                        class="bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold px-3 py-1.5 rounded-lg shadow-sm transition flex items-center gap-1">
                    ➕ Tambah Titik
                </button>

                <button type="button" onclick="openImportModal()"
                        class="bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold px-3 py-1.5 rounded-lg shadow-sm transition flex items-center gap-1">
                    📥 Import CSV/JSON
                </button>

                <div class="h-5 w-[1px] bg-gray-300 mx-0.5 hidden sm:block"></div>

                <span class="text-xs text-gray-600 hidden md:inline">
                    <strong>{{ auth()->user()->name }}</strong> ({{ strtoupper(auth()->user()->role) }})
                </span>

                @if(auth()->user()->role === 'admin')
                    <a href="{{ route('admin.dashboard') }}" class="bg-slate-800 text-white text-xs px-3 py-1.5 rounded-lg hover:bg-slate-700 font-semibold">
                        Dashboard Admin
                    </a>
                @else
                    <a href="{{ route('surveyor.dashboard') }}" class="bg-blue-600 text-white text-xs px-3 py-1.5 rounded-lg hover:bg-blue-700 font-semibold">
                        Dashboard & Pilih Rute
                    </a>
                @endif

                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button type="submit" class="bg-red-100 text-red-700 text-xs px-2.5 py-1.5 rounded-lg hover:bg-red-200 font-semibold">
                        Logout
                    </button>
                </form>
            @else
                <a href="{{ route('login') }}" class="bg-blue-600 text-white text-xs font-semibold px-4 py-2 rounded-lg hover:bg-blue-700">
                    Login Petugas Survei
                </a>
            @endauth
        </div>
    </div>

    <!-- PANEL PANDUAN RUTE TERCEPAT MELAYANG -->
    <div id="floating-route-panel" class="hidden fixed bottom-5 left-4 z-[1000] w-80 max-w-[92vw] bg-white/95 backdrop-blur rounded-2xl shadow-2xl border border-blue-300 overflow-hidden text-xs">
        <div class="bg-blue-900 text-white px-3.5 py-2.5 flex justify-between items-center">
            <div>
                <span class="font-bold block">🧭 Panduan Rute Tercepat</span>
                <span id="floating-route-stats" class="text-[11px] text-amber-300 font-semibold">Menghitung rute...</span>
            </div>
            <div class="flex items-center gap-1.5">
                <a id="floating-gmaps-link" href="#" target="_blank"
                   class="bg-emerald-500 hover:bg-emerald-600 text-slate-950 font-bold px-2 py-1 rounded text-[10px]">
                    Google Maps
                </a>
                <button type="button" onclick="clearActiveRouteOnMap()" title="Tutup Rute"
                        class="bg-red-600 hover:bg-red-700 text-white font-bold px-2 py-1 rounded text-[10px]">
                    ✕
                </button>
            </div>
        </div>
        <ol id="floating-route-list" class="p-2.5 space-y-1.5 max-h-56 overflow-y-auto divide-y divide-gray-100"></ol>
    </div>

    <!-- Banner Instruksi Saat Mode Tambah Titik Aktif -->
    <div id="add-point-banner" class="hidden fixed bottom-6 left-1/2 -translate-x-1/2 z-[1000] bg-emerald-900 text-white px-5 py-3 rounded-2xl shadow-2xl border border-emerald-500 flex items-center gap-3 text-xs">
        <span class="animate-ping w-2.5 h-2.5 rounded-full bg-emerald-400"></span>
        <span><strong>Mode Tambah Titik Aktif:</strong> Klik atau sentuh lokasi infrastruktur langsung pada peta.</span>
        <button type="button" onclick="toggleAddPointMode()" class="bg-red-600 hover:bg-red-700 px-2.5 py-1 rounded-lg font-bold ml-2">
            Batal
        </button>
    </div>

    <!-- Alert Notifikasi -->
    @if(session('success'))
        <div id="toast-alert" class="fixed top-20 right-4 z-[1100] bg-green-600 text-white px-4 py-3 rounded-xl shadow-lg text-xs font-medium flex items-center gap-3">
            <span>✓ {{ session('success') }}</span>
            <button onclick="document.getElementById('toast-alert').remove()" class="font-bold">×</button>
        </div>
    @endif

    @if($errors->any())
        <div id="toast-error" class="fixed top-20 right-4 z-[1100] bg-red-600 text-white px-4 py-3 rounded-xl shadow-lg text-xs font-medium max-w-sm">
            <div class="flex justify-between items-start gap-2">
                <ul class="list-disc pl-4">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button onclick="document.getElementById('toast-error').remove()" class="font-bold">×</button>
            </div>
        </div>
    @endif

    <!-- Container Peta -->
    <div id="map"></div>

    @auth
    <!-- MODAL TAMBAH TITIK MANUAL DARI KLIK PETA -->
    <div id="modalAddPoint" class="fixed inset-0 bg-black/60 hidden items-center justify-center z-[2000] p-4">
        <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl max-h-[92vh] overflow-y-auto">
            <div class="flex justify-between items-center border-b pb-3 mb-4">
                <h3 class="font-bold text-base text-gray-900">📍 Tambah Titik Infrastruktur Baru</h3>
                <button type="button" onclick="closeAddPointModal()" class="text-gray-400 hover:text-gray-700 text-xl font-bold">×</button>
            </div>

            <form action="{{ route('infrastructure.store_point') }}" method="POST" class="space-y-3 text-xs">
                @csrf
                <div class="grid grid-cols-2 gap-2 bg-emerald-50 p-2.5 rounded-xl border border-emerald-200">
                    <div>
                        <label class="block font-semibold text-emerald-800 mb-0.5">Latitude (Y)</label>
                        <input type="text" name="latitude" id="manual_lat" readonly required
                               class="w-full bg-white border border-emerald-300 rounded px-2 py-1.5 font-mono text-gray-800">
                    </div>
                    <div>
                        <label class="block font-semibold text-emerald-800 mb-0.5">Longitude (X)</label>
                        <input type="text" name="longitude" id="manual_lng" readonly required
                               class="w-full bg-white border border-emerald-300 rounded px-2 py-1.5 font-mono text-gray-800">
                    </div>
                </div>

                <div>
                    <label class="block font-semibold text-gray-700 mb-1">Nama Infrastruktur <span class="text-red-500">*</span></label>
                    <input type="text" name="nama_objek" id="manual_nama_objek" required placeholder="Contoh: Jembatan Krueng Meureudu"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-emerald-200 focus:border-emerald-500">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                    <div>
                        <label class="block font-semibold text-gray-700 mb-1">Kategori PU <span class="text-red-500">*</span></label>
                        <select name="kategori" id="manual_kategori" required onchange="updateManualJenisDropdown()"
                                class="w-full border border-gray-300 rounded-lg px-2.5 py-1.5 bg-white">
                            <option value="BM">BM (Bina Marga)</option>
                            <option value="CK">CK (Cipta Karya)</option>
                            <option value="PS">PS (Prasarana Strategis)</option>
                            <option value="SDA">SDA (Sumber Daya Air)</option>
                            <option value="SETJEN">SETJEN (Sekretariat Jenderal)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block font-semibold text-gray-700 mb-1">Jenis Infrastruktur <span class="text-red-500">*</span></label>
                        <div id="manual_jenis_container"></div>
                    </div>
                </div>

                <!-- Bagian Kota & Provinsi Otomatis -->
                <div class="bg-blue-50/60 p-2.5 rounded-xl border border-blue-100 space-y-2">
                    <div class="flex justify-between items-center">
                        <span class="text-[11px] font-bold text-blue-900">Deteksi Wilayah Otomatis</span>
                        <span id="geocode-status" class="text-[10px] font-semibold text-blue-600">Mendeteksi lokasi...</span>
                    </div>

                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block font-semibold text-gray-700 mb-1">Kota / Kabupaten <span class="text-red-500">*</span></label>
                            <input type="text" name="kota" id="manual_kota" required placeholder="Otomatis terisi..."
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 bg-white">
                        </div>
                        <div>
                            <label class="block font-semibold text-gray-700 mb-1">Provinsi <span class="text-red-500">*</span></label>
                            <input type="text" name="provinsi" id="manual_provinsi" required placeholder="Otomatis terisi..."
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 bg-white">
                        </div>
                    </div>
                </div>

                <div class="flex justify-end gap-2 pt-3 border-t">
                    <button type="button" onclick="closeAddPointModal()" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded-lg font-semibold">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg font-bold shadow">
                        Simpan Titik ke Peta
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL IMPORT CSV / JSON -->
    <div id="modalImportData" class="fixed inset-0 bg-black/60 hidden items-center justify-center z-[2000] p-4">
        <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl">
            <div class="flex justify-between items-center border-b pb-3 mb-4">
                <h3 class="font-bold text-base text-gray-900">📥 Import Data Infrastruktur (CSV / JSON)</h3>
                <button type="button" onclick="closeImportModal()" class="text-gray-400 hover:text-gray-700 text-xl font-bold">×</button>
            </div>

            <form action="{{ route('infrastructure.import') }}" method="POST" enctype="multipart/form-data" class="space-y-4 text-xs">
                @csrf
                <div class="bg-slate-50 p-3 rounded-xl border border-slate-200 text-gray-600 space-y-1">
                    <p class="font-bold text-gray-800">Format Kolom yang Didukung:</p>
                    <p>• <strong>Wajib:</strong> <code class="bg-gray-200 px-1 rounded">nama_objek</code>, <code class="bg-gray-200 px-1 rounded">latitude</code>, <code class="bg-gray-200 px-1 rounded">longitude</code></p>
                    <p>• <strong>Opsional:</strong> <code class="bg-gray-200 px-1 rounded">kategori</code> (BM/CK/PS/SDA/SETJEN), <code class="bg-gray-200 px-1 rounded">jenis_infrastruktur</code>, <code class="bg-gray-200 px-1 rounded">kota</code>, <code class="bg-gray-200 px-1 rounded">provinsi</code></p>
                </div>

                <div>
                    <label class="block font-semibold text-gray-700 mb-1">Pilih File (.csv atau .json)</label>
                    <input type="file" name="file_import" accept=".csv,.json,.txt" required
                           class="w-full border border-gray-300 rounded-lg p-2 bg-gray-50">
                </div>

                <div class="flex justify-end gap-2 pt-3 border-t">
                    <button type="button" onclick="closeImportModal()" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded-lg font-semibold">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-bold shadow">
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

    <!-- File JavaScript Terpisah (Modular) -->
    <script src="{{ asset('js/gis/infra-categories.js') }}?v=1"></script>
    <script src="{{ asset('js/gis/map-boundaries.js') }}?v=1"></script>
    <script src="{{ asset('js/gis/map-core.js') }}?v=1"></script>
</body>
</html>