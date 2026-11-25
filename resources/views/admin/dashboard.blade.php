@extends('admin.layout')

@section('title', 'Monitoring Data Infrastruktur')

@section('content')
    <!-- Kartu Ringkasan Utama -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
        <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-200">
            <p class="text-xs font-bold text-gray-500 uppercase">Total Titik Infrastruktur</p>
            <p class="text-3xl font-extrabold text-gray-900 mt-1">{{ $stats['total'] }}</p>
        </div>
        <div class="bg-white p-5 rounded-xl shadow-sm border-l-4 border-green-500">
            <p class="text-xs font-bold text-green-600 uppercase">Sudah Disurvei</p>
            <p class="text-3xl font-extrabold text-green-700 mt-1">{{ $stats['sudah'] }}</p>
        </div>
        <div class="bg-white p-5 rounded-xl shadow-sm border-l-4 border-red-500">
            <p class="text-xs font-bold text-red-600 uppercase">Belum Disurvei</p>
            <p class="text-3xl font-extrabold text-red-700 mt-1">{{ $stats['belum'] }}</p>
        </div>
    </div>

    <!-- Rincian Per Kategori PU (BM, CK, SDA, PS) -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        @foreach(['BM' => 'Bina Marga', 'CK' => 'Cipta Karya', 'SDA' => 'Sumber Daya Air', 'PS' => 'Prasarana Strategis'] as $kode => $namaKat)
            @php
                $totKat = $stats['per_kategori'][$kode]['total'];
                $sdhKat = $stats['per_kategori'][$kode]['sudah'];
                $persen = $totKat > 0 ? round(($sdhKat / $totKat) * 100) : 0;
            @endphp
            <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-200">
                <div class="flex justify-between items-center">
                    <span class="text-xs font-bold px-2 py-0.5 rounded bg-slate-800 text-white">{{ $kode }}</span>
                    <span class="text-xs font-semibold text-gray-500">{{ $sdhKat }}/{{ $totKat }} ({{ $persen }}%)</span>
                </div>
                <p class="text-xs text-gray-600 mt-2 font-medium">{{ $namaKat }}</p>
                <div class="w-full bg-gray-200 rounded-full h-1.5 mt-2">
                    <div class="bg-blue-600 h-1.5 rounded-full" style="width: {{ $persen }}%"></div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Filter Multi-Kriteria -->
    <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-200 mb-6">
        <form method="GET" action="{{ route('admin.dashboard') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-3">
            <div class="lg:col-span-2">
                <label class="block text-xs font-semibold text-gray-600 mb-1">Kata Kunci</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Nama objek, jenis, catatan..."
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-xs">
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Kategori</label>
                <select name="kategori" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-xs">
                    <option value="">Semua Kategori</option>
                    <option value="BM" {{ request('kategori') == 'BM' ? 'selected' : '' }}>BM (Bina Marga)</option>
                    <option value="CK" {{ request('kategori') == 'CK' ? 'selected' : '' }}>CK (Cipta Karya)</option>
                    <option value="SDA" {{ request('kategori') == 'SDA' ? 'selected' : '' }}>SDA (Sumber Daya Air)</option>
                    <option value="PS" {{ request('kategori') == 'PS' ? 'selected' : '' }}>PS (Prasarana Strategis)</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Status</label>
                <select name="status_survei" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-xs">
                    <option value="">Semua Status</option>
                    <option value="sudah" {{ request('status_survei') == 'sudah' ? 'selected' : '' }}>Sudah Survei</option>
                    <option value="belum" {{ request('status_survei') == 'belum' ? 'selected' : '' }}>Belum Survei</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Provinsi</label>
                <select name="provinsi" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-xs">
                    <option value="">Semua Provinsi</option>
                    @foreach($daftarProvinsi as $prov)
                        <option value="{{ $prov }}" {{ request('provinsi') == $prov ? 'selected' : '' }}>{{ $prov }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Kota/Kabupaten</label>
                <select name="kota" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-xs">
                    <option value="">Semua Kota</option>
                    @foreach($daftarKota as $kota)
                        <option value="{{ $kota }}" {{ request('kota') == $kota ? 'selected' : '' }}>{{ $kota }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Petugas Surveyor</label>
                <select name="surveyed_by" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-xs">
                    <option value="">Semua Petugas</option>
                    @foreach($daftarSurveyor as $srv)
                        <option value="{{ $srv->id }}" {{ request('surveyed_by') == $srv->id ? 'selected' : '' }}>{{ $srv->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="lg:col-span-5 flex items-end justify-end gap-2">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-5 rounded-lg text-xs">
                    Terapkan Filter
                </button>
                <a href="{{ route('admin.dashboard') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold py-2 px-4 rounded-lg text-xs">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Tabel Data Infrastruktur -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead class="bg-gray-50 border-b border-gray-200 uppercase text-gray-600">
                    <tr>
                        <th class="py-3.5 px-4">Infrastruktur</th>
                        <th class="py-3.5 px-4">Kategori</th>
                        <th class="py-3.5 px-4">Wilayah</th>
                        <th class="py-3.5 px-4">Status</th>
                        <th class="py-3.5 px-4">Petugas & Waktu Survei</th>
                        <th class="py-3.5 px-4 text-center">Foto</th>
                        <th class="py-3.5 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($infrastructures as $item)
                        <tr class="hover:bg-gray-50">
                            <td class="py-3 px-4">
                                <p class="font-bold text-gray-900 text-sm">{{ $item->nama_objek }}</p>
                                <p class="text-gray-500">{{ $item->jenis_infrastruktur ?? 'Jenis belum diisi' }}</p>
                            </td>
                            <td class="py-3 px-4">
                                <span class="px-2.5 py-1 font-bold rounded-full bg-slate-200 text-slate-800">{{ $item->kategori }}</span>
                            </td>
                            <td class="py-3 px-4">
                                <p class="font-semibold">{{ $item->kota }}, {{ $item->provinsi }}</p>
                                <p class="text-gray-400">{{ $item->latitude }}, {{ $item->longitude }}</p>
                            </td>
                            <td class="py-3 px-4">
                                @if($item->status_survei === 'sudah')
                                    <span class="px-2.5 py-1 font-bold rounded-full bg-green-100 text-green-700">✓ Sudah Survei</span>
                                @else
                                    <span class="px-2.5 py-1 font-bold rounded-full bg-red-100 text-red-700">✕ Belum Survei</span>
                                @endif
                            </td>
                            <td class="py-3 px-4">
                                @if($item->surveyor)
                                    <p class="font-bold text-gray-800">👤 {{ $item->surveyor->name }}</p>
                                    <p class="text-gray-500">🕒 {{ $item->surveyed_at ? $item->surveyed_at->format('d M Y, H:i') . ' WIB' : '-' }}</p>
                                @else
                                    <span class="text-gray-400 italic">Belum ada aktivitas</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-center">
                                <span class="font-semibold {{ $item->photos->count() > 0 ? 'text-blue-600' : 'text-gray-400' }}">
                                    {{ $item->photos->count() }}/5
                                </span>
                            </td>
                            <td class="py-3 px-4 text-right">
                                <button type="button" onclick='openDetailModal(@json($item))'
                                        class="bg-slate-800 hover:bg-slate-900 text-white px-3 py-1.5 rounded-lg font-semibold">
                                    Detail & Foto
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-gray-500">Tidak ada data infrastruktur yang ditemukan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-gray-200">
            {{ $infrastructures->links() }}
        </div>
    </div>

    <!-- MODAL DETAIL SURVEI & UNDUH FOTO -->
    <div id="detailModal" class="fixed inset-0 bg-black/70 hidden items-center justify-center z-50 p-4">
        <div class="bg-white rounded-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto p-6 shadow-2xl">
            <div class="flex justify-between items-start border-b pb-3 mb-4">
                <div>
                    <span id="det_kategori" class="text-xs font-bold px-2.5 py-0.5 rounded-full bg-blue-100 text-blue-800"></span>
                    <h3 id="det_nama" class="text-xl font-bold text-gray-900 mt-1"></h3>
                    <p id="det_wilayah" class="text-xs text-gray-500"></p>
                </div>
                <button onclick="closeDetailModal()" class="text-gray-400 hover:text-gray-700 text-2xl font-bold">×</button>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs bg-gray-50 p-4 rounded-xl border mb-4">
                <div>
                    <p class="text-gray-500">Petugas Surveyor:</p>
                    <p id="det_surveyor" class="font-bold text-gray-900 text-sm mb-2"></p>

                    <p class="text-gray-500">Waktu Penyelesaian Survei:</p>
                    <p id="det_waktu" class="font-semibold text-gray-800 mb-2"></p>

                    <p class="text-gray-500">Jenis Infrastruktur & Nama Hasil Survei:</p>
                    <p id="det_jenis" class="font-semibold text-gray-800"></p>
                </div>
                <div>
                    <p class="text-gray-500">Koordinat Awal Database:</p>
                    <p id="det_coord_awal" class="font-mono text-gray-800 mb-2"></p>

                    <p class="text-gray-500">Koordinat Aktual Survei Lapangan & Jarak dari Titik:</p>
                    <div id="det_coord_survei" class="font-mono font-bold text-gray-800 mb-2"></div>

                    <p class="text-gray-500">Catatan / Alasan Verifikasi:</p>
                    <p id="det_catatan" class="text-gray-800 italic"></p>
                </div>
            </div>

            <div class="flex flex-wrap justify-between items-center gap-2 mb-3">
                <h4 class="font-bold text-sm text-gray-800">Dokumentasi Foto Lapangan</h4>
                <a id="btn_admin_download_zip" href="#"
                   class="hidden inline-flex items-center gap-1.5 bg-slate-800 hover:bg-slate-900 text-white text-xs font-bold px-3.5 py-1.5 rounded-lg shadow transition">
                    📦 Unduh Semua Foto (.ZIP)
                </a>
            </div>
            <div id="det_photos_grid" class="grid grid-cols-1 sm:grid-cols-2 gap-4"></div>
        </div>
    </div>

    <script>
        // Fungsi rumus Haversine untuk menghitung jarak 2 koordinat (dalam meter)
        function calculateDistanceMeters(lat1, lon1, lat2, lon2) {
            const R = 6371000; // Radius bumi dalam meter
            const toRad = (deg) => deg * (Math.PI / 180);

            const dLat = toRad(lat2 - lat1);
            const dLon = toRad(lon2 - lon1);

            const a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
                      Math.cos(toRad(lat1)) * Math.cos(toRad(lat2)) *
                      Math.sin(dLon / 2) * Math.sin(dLon / 2);

            const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
            return R * c;
        }

        // Helper format tampilan jarak & warna status akurasi lokasi
        function formatDistanceBadge(meters) {
            const isNear = meters <= 100; // Toleransi jarak dekat 100 meter (dapat disesuaikan)
            const textJarak = meters >= 1000
                ? `${(meters / 1000).toFixed(2)} km`
                : `${Math.round(meters)} meter`;

            const badgeClass = isNear
                ? 'bg-emerald-100 text-emerald-800 border-emerald-300'
                : 'bg-red-100 text-red-800 border-red-300';

            const labelStatus = isNear ? '✓ Sesuai Lokasi' : '⚠️ Jauh dari Titik';

            return `<span class="inline-block px-2 py-0.5 rounded border text-[10px] font-bold ${badgeClass}">
                        📏 ${textJarak} (${labelStatus})
                    </span>`;
        }

        function openDetailModal(item) {
            document.getElementById('det_kategori').textContent = item.kategori;
            document.getElementById('det_nama').textContent = item.nama_objek;
            document.getElementById('det_wilayah').textContent = `${item.kota}, ${item.provinsi}`;
            document.getElementById('det_surveyor').textContent = item.surveyor ? item.surveyor.name : 'Belum disurvei';
            document.getElementById('det_waktu').textContent = item.surveyed_at ? new Date(item.surveyed_at).toLocaleString('id-ID') : '-';
            document.getElementById('det_jenis').textContent = `${item.jenis_infrastruktur || '-'} / ${item.nama_hasil_survei || '-'}`;

            const origLat = parseFloat(item.latitude);
            const origLng = parseFloat(item.longitude);
            document.getElementById('det_coord_awal').textContent = `${item.latitude}, ${item.longitude}`;

            // Ambil koordinat GPS dari foto-foto yang diambil di lapangan & hitung selisih jaraknya
            const photosWithGps = (item.photos || []).filter(p => p.photo_latitude && p.photo_longitude);
            const coordSurveiEl = document.getElementById('det_coord_survei');
            const btnZip = document.getElementById('btn_admin_download_zip');

            if (photosWithGps.length > 0 && !isNaN(origLat) && !isNaN(origLng)) {
                coordSurveiEl.innerHTML = photosWithGps.map((p, idx) => {
                    const pLat = parseFloat(p.photo_latitude);
                    const pLng = parseFloat(p.photo_longitude);
                    const jarakMeter = calculateDistanceMeters(origLat, origLng, pLat, pLng);

                    return `
                        <div class="mb-1.5 pb-1.5 border-b border-gray-200 last:border-b-0">
                            <div class="text-green-700">Foto #${idx + 1}: ${pLat.toFixed(7)}, ${pLng.toFixed(7)}</div>
                            <div class="mt-0.5">${formatDistanceBadge(jarakMeter)}</div>
                        </div>
                    `;
                }).join('');
            } else {
                coordSurveiEl.innerHTML = `<span class="text-gray-400 font-normal italic">Koordinat GPS foto tidak tersedia</span>`;
            }

            document.getElementById('det_catatan').textContent = item.alasan_verifikasi || '-';

            const grid = document.getElementById('det_photos_grid');
            grid.innerHTML = '';

            if (item.photos && item.photos.length > 0) {
                item.photos.forEach((photo, idx) => {
                    const hasGps = photo.photo_latitude && photo.photo_longitude;
                    const gpsInfo = hasGps ? `${photo.photo_latitude}, ${photo.photo_longitude}` : 'Koordinat tidak terekam';
                    const sourceBadge = photo.source_type === 'camera' ? '📸 Kamera GPS' : '📁 Upload File';

                    let jarakHtml = '';
                    if (hasGps && !isNaN(origLat) && !isNaN(origLng)) {
                        const jarakMeter = calculateDistanceMeters(
                            origLat,
                            origLng,
                            parseFloat(photo.photo_latitude),
                            parseFloat(photo.photo_longitude)
                        );
                        jarakHtml = `<div class="pt-1">${formatDistanceBadge(jarakMeter)}</div>`;
                    }

                    grid.innerHTML += `
                        <div class="border rounded-xl overflow-hidden bg-white shadow-sm">
                            <img src="/storage/${photo.file_path}" class="w-full h-44 object-cover">
                            <div class="p-3 text-xs space-y-1">
                                <div class="flex justify-between items-center">
                                    <span class="font-bold text-gray-800">Foto #${idx + 1}</span>
                                    <span class="text-[10px] px-2 py-0.5 rounded bg-indigo-50 text-indigo-700 font-semibold">${sourceBadge}</span>
                                </div>
                                <p class="text-[11px] text-gray-500 font-mono">📍 ${gpsInfo}</p>
                                ${jarakHtml}
                                <a href="/survey/photo/${photo.id}/download" 
                                   class="mt-2 block text-center bg-blue-600 hover:bg-blue-700 text-white font-semibold py-1.5 rounded-lg transition">
                                    ⬇ Unduh Foto Asli
                                </a>
                            </div>
                        </div>
                    `;
                });
            } else {
                grid.innerHTML = `<p class="text-xs text-gray-400 col-span-2 py-6 text-center border rounded-xl">Belum ada foto dokumentasi untuk titik ini.</p>`;
            }

            // Set link unduh ZIP
            if (item.photos && item.photos.length > 0) {
                btnZip.href = `/survey/${item.id}/download-zip`;
                btnZip.textContent = `📦 Unduh Semua (${item.photos.length} Foto .ZIP)`;
                btnZip.classList.remove('hidden');
            } else {
                btnZip.classList.add('hidden');
            }

            const modal = document.getElementById('detailModal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeDetailModal() {
            const modal = document.getElementById('detailModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }
    </script>
@endsection