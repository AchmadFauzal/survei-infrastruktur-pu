// =========================================================================
// INISIALISASI PETA UTAMA, MARKER, POPUP, RUTE OSRM & KAMERA GPS
// =========================================================================
const {
    infrastructures,
    isAuthenticated,
    currentUserId,
    currentUserRole,
    csrfToken
} = window.GIS_CONFIG;

const urlParams = new URLSearchParams(window.location.search);
const focusId = urlParams.get('focus_id') ? Number(urlParams.get('focus_id')) : null;
const routeIdsParam = urlParams.get('route_ids');
const useGpsParam = urlParams.get('use_gps') === '1';

// Inisialisasi dropdown jenis awal pada modal tambah titik
updateManualJenisDropdown();

// 1. Setup 3 Basemap Layers
const basemapLayers = {
    osm: L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© <a href="https://www.openstreetmap.org/copyright" target="_blank">OpenStreetMap</a> | Batas Wilayah: <a href="https://github.com/cahyadsn/wilayah_boundaries" target="_blank" class="underline">cahyadsn</a>',
        maxZoom: 19
    }),
    topo: L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Topo_Map/MapServer/tile/{z}/{y}/{x}', {
        attribution: 'Tiles © Esri',
        maxZoom: 18
    }),
    satellite: L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
        attribution: 'Tiles © Esri',
        maxZoom: 18
    })
};

const map = L.map('map', {
    center: [-2.5, 118.0],
    zoom: 5,
    layers: [basemapLayers.osm]
});

// Muat poligon batas wilayah lokal
loadWilayahBoundaries(map);

L.control.layers(
    {
        "🗺️ OpenStreetMap": basemapLayers.osm,
        "⛰️ Esri World Topo": basemapLayers.topo,
        "🛰️ Esri Satelit": basemapLayers.satellite
    },
    {
        "📐 Batas Kab/Kota (GeoJSON)": batasWilayahLayer
    },
    { position: 'topright' }
).addTo(map);

// 2. Render Marker & Popup Survei
const markersLayer = L.layerGroup().addTo(map);
const routeOverlayLayer = L.layerGroup().addTo(map);
const markerById = {};
let routeOrderMap = {};

function getMarkerIcon(status, isMyTask, routeOrder = null) {
    const color = status === 'sudah' ? '#16a34a' : '#dc2626';
    if (routeOrder !== null) {
        return L.divIcon({
            className: '',
            html: `<div style="width:26px;height:26px;border-radius:50%;background:#1d4ed8;color:#fff;font-size:12px;font-weight:800;display:flex;align-items:center;justify-content:center;border:3px solid #fbbf24;box-shadow:0 3px 8px rgba(0,0,0,0.55);">${routeOrder}</div>`,
            iconSize: [26, 26],
            iconAnchor: [13, 13],
            popupAnchor: [0, -13]
        });
    }

    const ringStyle = isMyTask ? 'box-shadow: 0 0 0 4px #f59e0b, 0 3px 8px rgba(0,0,0,0.5);' : 'box-shadow: 0 2px 6px rgba(0,0,0,0.4);';
    return L.divIcon({
        className: '',
        html: `<div style="width:18px;height:18px;border-radius:50%;background:${color};border:3px solid #fff;${ringStyle}"></div>`,
        iconSize: [18, 18],
        iconAnchor: [9, 9],
        popupAnchor: [0, -10]
    });
}

function escapeHtmlAttribute(value) {
    return String(value ?? '').replace(/[&<>"']/g, character => ({
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    })[character]);
}

function buildPopupContent(item, lat, lng, isMyTask) {
    const routeNum = routeOrderMap[Number(item.id)] || null;
    let photosHtml = '';
    if (item.photos && item.photos.length > 0) {
        photosHtml = `<div class="grid grid-cols-3 gap-1.5 mt-2">`;
        item.photos.forEach(photo => {
            const gpsTag = photo.photo_latitude ? `GPS: ${Number(photo.photo_latitude).toFixed(4)}, ${Number(photo.photo_longitude).toFixed(4)}` : 'Upload File';
            photosHtml += `
                <div class="relative group border rounded overflow-hidden">
                    <a href="/storage/${photo.file_path}" target="_blank">
                        <img src="/storage/${photo.file_path}" class="w-full h-16 object-cover" title="${gpsTag}">
                    </a>
                    ${isAuthenticated ? `
                    <form method="POST" action="/survey/photo/${photo.id}" onsubmit="return confirm('Hapus foto ini?')" class="absolute top-0 right-0">
                        <input type="hidden" name="_token" value="${csrfToken}">
                        <input type="hidden" name="_method" value="DELETE">
                        <button type="submit" class="bg-red-600 text-white text-[10px] px-1 leading-tight">×</button>
                    </form>` : ''}
                </div>
            `;
        });
        photosHtml += `</div>`;
    }

    let popupContent = `
        <div class="text-xs space-y-1.5">
            ${routeNum ? `<div class="bg-blue-700 text-white px-2 py-1 rounded font-bold text-[10px]">🧭 RUTE KUNJUNGAN URUTAN #${routeNum}</div>` : ''}
            ${isMyTask ? `<div class="bg-amber-100 text-amber-900 border border-amber-300 px-2 py-1 rounded font-bold text-[10px]">📌 TUGAS SURVEI ANDA</div>` : ''}
            <div class="flex justify-between items-start gap-1">
                <span class="font-bold text-sm text-gray-900 leading-snug">${item.nama_infrastruktur}</span>
                <span class="px-2 py-0.5 rounded text-[10px] font-bold text-white shrink-0 ${item.status_survei === 'sudah' ? 'bg-green-600' : 'bg-red-600'}">
                    ${item.status_survei === 'sudah' ? 'SUDAH SURVEI' : 'BELUM SURVEI'}
                </span>
            </div>
            <div class="text-gray-600">
                <span class="font-semibold">Kategori:</span> ${item.kategori} | 
                <span class="font-semibold">Wilayah:</span> ${item.kota_kabupaten}, ${item.provinsi}
            </div>
            ${item.nama_hasil_survey ? `<div class="text-gray-700"><span class="font-semibold">Nama hasil survei:</span> ${item.nama_hasil_survey}</div>` : ''}
            <div class="text-gray-500 text-[11px]">Koordinat Data: ${lat.toFixed(6)}, ${lng.toFixed(6)}</div>
            ${item.surveyor ? `<div class="text-[11px] text-blue-700 font-medium">Disurvei oleh: ${item.surveyor.name}</div>` : ''}
            ${photosHtml}
            ${(isAuthenticated && item.photos && item.photos.length > 0) ? `
                <div class="mt-1.5">
                    <a href="/survey/${item.id}/download-zip"
                       class="block text-center bg-slate-800 hover:bg-slate-900 text-white text-[11px] font-semibold py-1 rounded-lg transition">
                        📦 Unduh Semua Foto (.ZIP)
                    </a>
                </div>
            ` : ''}
    `;

    if (isAuthenticated) {
        const sisaSlot = 5 - (item.photos ? item.photos.length : 0);
        popupContent += `
            <hr class="my-2 border-gray-200">
            <form method="POST" action="/survey/${item.id}/update" enctype="multipart/form-data" class="space-y-2">
                <input type="hidden" name="_token" value="${csrfToken}">
                <input type="hidden" name="survey_latitude" id="lat-${item.id}" value="${lat}">
                <input type="hidden" name="survey_longitude" id="lng-${item.id}" value="${lng}">

                <div>
                    <label class="font-semibold block text-gray-700 mb-0.5">Status Survei:</label>
                    <select name="status_survei" class="w-full border border-gray-300 rounded-lg p-1.5 bg-gray-50">
                        <option value="belum" ${item.status_survei === 'belum' ? 'selected' : ''}>Belum Survei</option>
                        <option value="sudah" ${item.status_survei === 'sudah' ? 'selected' : ''}>Sudah Survei</option>
                    </select>
                </div>

                <div>
                    <label class="font-semibold block text-gray-700 mb-0.5">Jenis Infrastruktur (${item.kategori}):</label>
                    ${renderCustomJenisDropdown(`popup-${item.id}`, item.kategori, item.jenis_infrastruktur || '')}
                </div>

                <div>
                    <label class="font-semibold block text-gray-700 mb-0.5">Nama Hasil Survei:</label>
                    <input type="text" name="nama_hasil_survey" maxlength="255"
                           value="${escapeHtmlAttribute(item.nama_hasil_survey)}"
                           placeholder="Contoh: Jembatan Meureudu (Lajur Nasional)"
                           class="w-full border border-gray-300 rounded-lg p-1.5">
                </div>

                <div>
                    <label class="font-semibold block text-gray-700 mb-0.5">Catatan / Alasan Verifikasi:</label>
                    <textarea name="alasan_verifikasi" rows="2" class="w-full border border-gray-300 rounded-lg p-1.5">${item.alasan_verifikasi || ''}</textarea>
                </div>

                ${sisaSlot > 0 ? `
                <div class="bg-gray-50 p-2 rounded-lg border border-gray-200">
                    <label class="font-semibold block text-gray-700 mb-1">Tambah Foto (Sisa kuota: ${sisaSlot}/5):</label>
                    <input type="file" name="photos[]" multiple accept="image/*" class="w-full text-[11px] mb-1.5">

                    <button type="button" onclick="openCamera(${item.id})" class="w-full bg-indigo-600 text-white py-1.5 rounded-lg text-[11px] font-medium hover:bg-indigo-700">
                        📸 Buka Kamera + Rekam GPS
                    </button>
                    <div id="cam-box-${item.id}" class="hidden mt-2 space-y-1">
                        <video id="video-${item.id}" autoplay playsinline class="w-full rounded bg-black max-h-36"></video>
                        <button type="button" onclick="takeSnapshot(${item.id})" class="w-full bg-amber-500 text-white py-1 rounded text-[11px] font-bold">
                            Ambil Jepretan GPS
                        </button>
                    </div>
                    <div id="cam-inputs-${item.id}"></div>
                </div>
                ` : `<div class="text-[11px] text-amber-700 bg-amber-50 p-1.5 rounded">Kuota maksimal 5 foto telah tercapai.</div>`}

                <button type="submit" class="w-full bg-emerald-600 text-white font-bold py-2 rounded-lg hover:bg-emerald-700 shadow-sm">
                    💾 Simpan Hasil Survei
                </button>
            </form>
        `;
    }

    popupContent += `</div>`;
    return popupContent;
}

const myAssignedCount = infrastructures.filter(item =>
    currentUserRole === 'surveyor' && Number(item.assigned_to) === Number(currentUserId)
).length;

const badgeCountEl = document.getElementById('assigned-badge-count');
if (badgeCountEl) badgeCountEl.textContent = myAssignedCount;

function renderMapMarkers(onlyAssigned = false, shouldFitBounds = true) {
    markersLayer.clearLayers();
    Object.keys(markerById).forEach(k => delete markerById[k]);

    const bounds = [];

    infrastructures.forEach(item => {
        if (!item.latitude || !item.longitude) return;

        const isMyTask = (currentUserRole === 'surveyor' && Number(item.assigned_to) === Number(currentUserId));
        const routeOrder = routeOrderMap[Number(item.id)] || null;

        if (onlyAssigned && !isMyTask && routeOrder === null) return;

        const lat = parseFloat(item.latitude);
        const lng = parseFloat(item.longitude);
        bounds.push([lat, lng]);

        const marker = L.marker([lat, lng], {
            icon: getMarkerIcon(item.status_survei, isMyTask, routeOrder)
        });

        marker.bindPopup(buildPopupContent(item, lat, lng, isMyTask), {
            maxWidth: 360,
            minWidth: 320,
            autoPanPadding: [30, 80]
        });
        markersLayer.addLayer(marker);
        markerById[Number(item.id)] = marker;
    });

    if (shouldFitBounds && bounds.length > 0) {
        map.fitBounds(bounds, { padding: [50, 50], maxZoom: 13 });
    }
}

const toggleAssignedEl = document.getElementById('toggleAssignedOnly');
const initialOnlyAssigned = toggleAssignedEl ? toggleAssignedEl.checked : false;

if (toggleAssignedEl) {
    toggleAssignedEl.addEventListener('change', (e) => {
        renderMapMarkers(e.target.checked, true);
    });
}

// 3. Rute Tercepat OSRM
function getCurrentPositionPromise() {
    return new Promise((resolve) => {
        if (!navigator.geolocation) return resolve(null);
        navigator.geolocation.getCurrentPosition(
            (pos) => resolve({ lat: pos.coords.latitude, lng: pos.coords.longitude }),
            () => resolve(null),
            { enableHighAccuracy: true, timeout: 7000 }
        );
    });
}

function sortNearestNeighbor(points) {
    if (points.length <= 1) return points;
    const visited = [points[0]];
    const remaining = points.slice(1);
    while (remaining.length > 0) {
        const last = visited[visited.length - 1];
        let nearestIdx = 0;
        let minDist = Infinity;
        remaining.forEach((cand, idx) => {
            const d = Math.hypot(cand.lat - last.lat, cand.lng - last.lng);
            if (d < minDist) { minDist = d; nearestIdx = idx; }
        });
        visited.push(remaining.splice(nearestIdx, 1)[0]);
    }
    return visited;
}

function focusToRoutePoint(infraId) {
    const marker = markerById[Number(infraId)];
    if (marker) {
        map.setView(marker.getLatLng(), 17);
        marker.openPopup();
    }
}

function clearActiveRouteOnMap() {
    routeOverlayLayer.clearLayers();
    routeOrderMap = {};
    document.getElementById('floating-route-panel').classList.add('hidden');
    renderMapMarkers(toggleAssignedEl ? toggleAssignedEl.checked : false, false);
    window.history.replaceState({}, '', window.location.pathname);
}

async function initRouteFromQueryParams(idsArray, useGps) {
    const targetInfras = infrastructures.filter(item =>
        idsArray.includes(Number(item.id)) && item.latitude && item.longitude
    );
    if (targetInfras.length === 0) return;

    const panel = document.getElementById('floating-route-panel');
    const statsEl = document.getElementById('floating-route-stats');
    panel.classList.remove('hidden');

    let startGps = useGps ? await getCurrentPositionPromise() : null;

    const waypoints = [];
    if (startGps) {
        waypoints.push({ type: 'start', lat: startGps.lat, lng: startGps.lng, name: 'Lokasi GPS Saya' });
    }
    targetInfras.forEach(pt => {
        waypoints.push({
            type: 'point',
            id: Number(pt.id),
            lat: parseFloat(pt.latitude),
            lng: parseFloat(pt.longitude),
            name: pt.nama_infrastruktur,
            kota: pt.kota_kabupaten
        });
    });

    let orderedWaypoints = waypoints;
    let geometry = null;
    let distMeters = null;
    let durSeconds = null;

    if (waypoints.length > 1) {
        try {
            const coordString = waypoints.map(w => `${w.lng},${w.lat}`).join(';');
            const sourceParam = startGps ? '&source=first' : '';
            const res = await fetch(`https://router.project-osrm.org/trip/v1/driving/${coordString}?roundtrip=false${sourceParam}&overview=full&geometries=geojson`);
            const data = await res.json();

            if (data.code === 'Ok' && data.trips && data.trips.length > 0) {
                orderedWaypoints = waypoints
                    .map((wp, idx) => ({ ...wp, order: data.waypoints[idx].waypoint_index }))
                    .sort((a, b) => a.order - b.order);
                geometry = data.trips[0].geometry;
                distMeters = data.trips[0].distance;
                durSeconds = data.trips[0].duration;
            } else {
                orderedWaypoints = sortNearestNeighbor(waypoints);
            }
        } catch (e) {
            orderedWaypoints = sortNearestNeighbor(waypoints);
        }
    }

    routeOrderMap = {};
    let stepNum = 1;
    orderedWaypoints.forEach(wp => {
        if (wp.type === 'point') routeOrderMap[wp.id] = stepNum++;
    });

    renderMapMarkers(false, false);
    routeOverlayLayer.clearLayers();

    const bounds = [];
    orderedWaypoints.forEach(wp => {
        bounds.push([wp.lat, wp.lng]);
        if (wp.type === 'start') {
            const startIcon = L.divIcon({
                className: '',
                html: `<div style="width:26px;height:26px;border-radius:50%;background:#2563eb;color:#fff;font-size:12px;display:flex;align-items:center;justify-content:center;border:3px solid #fff;box-shadow:0 2px 8px rgba(0,0,0,0.5);">🚩</div>`,
                iconSize: [26, 26],
                iconAnchor: [13, 13]
            });
            L.marker([wp.lat, wp.lng], { icon: startIcon })
                .bindPopup('<strong>🚩 Titik Berangkat (Lokasi GPS Anda)</strong>')
                .addTo(routeOverlayLayer);
        }
    });

    if (geometry) {
        L.geoJSON(geometry, { style: { color: '#1d4ed8', weight: 5, opacity: 0.85 } }).addTo(routeOverlayLayer);
    } else if (bounds.length > 1) {
        L.polyline(bounds, { color: '#1d4ed8', weight: 4, dashArray: '6, 8' }).addTo(routeOverlayLayer);
    }

    if (bounds.length > 0) map.fitBounds(bounds, { padding: [70, 70], maxZoom: 15 });

    if (distMeters !== null && durSeconds !== null) {
        const km = (distMeters / 1000).toFixed(1);
        const mins = Math.round(durSeconds / 60);
        const durStr = mins >= 60 ? `${Math.floor(mins / 60)} jam ${mins % 60} mnt` : `${mins} menit`;
        statsEl.textContent = `${km} km • ± ${durStr}`;
    } else {
        statsEl.textContent = `${targetInfras.length} Titik Tujuan`;
    }

    const listEl = document.getElementById('floating-route-list');
    listEl.innerHTML = orderedWaypoints.map(wp => {
        if (wp.type === 'start') {
            return `<li class="py-1.5 flex items-center gap-2 text-blue-800 font-semibold">
                        <span class="px-1.5 py-0.5 rounded bg-blue-600 text-white text-[10px]">Start</span>
                        <span>Lokasi GPS Anda</span>
                    </li>`;
        }
        const num = routeOrderMap[wp.id];
        return `<li class="py-1.5 flex items-center justify-between gap-2">
                    <button type="button" onclick="focusToRoutePoint(${wp.id})" class="text-left truncate flex-1 hover:text-blue-700">
                        <span class="inline-block w-4 h-4 rounded-full bg-blue-700 text-white text-center text-[10px] font-bold mr-1">${num}</span>
                        <strong class="text-gray-900">${wp.name}</strong>
                        <span class="text-gray-500 text-[10px] block pl-5">${wp.kota}</span>
                    </button>
                    <button type="button" onclick="focusToRoutePoint(${wp.id})"
                            class="bg-amber-600 hover:bg-amber-700 text-white px-2 py-1 rounded font-bold text-[10px] shrink-0">
                        Survei
                    </button>
                </li>`;
    }).join('');

    const gmapsBtn = document.getElementById('floating-gmaps-link');
    const gmapsCoords = orderedWaypoints.map(w => `${w.lat},${w.lng}`);
    if (gmapsCoords.length === 1) {
        gmapsBtn.href = `https://www.google.com/maps/search/?api=1&query=${gmapsCoords[0]}`;
    } else {
        const origin = gmapsCoords[0];
        const destination = gmapsCoords[gmapsCoords.length - 1];
        const middle = gmapsCoords.slice(1, -1).join('|');
        gmapsBtn.href = `https://www.google.com/maps/dir/?api=1&origin=${origin}&destination=${destination}${middle ? `&waypoints=${encodeURIComponent(middle)}` : ''}&travelmode=driving`;
    }
}

// Render awal peta
if (routeIdsParam) {
    const parsedIds = routeIdsParam.split(',').map(Number).filter(n => !isNaN(n) && n > 0);
    renderMapMarkers(false, false);
    initRouteFromQueryParams(parsedIds, useGpsParam);
} else if (focusId) {
    renderMapMarkers(initialOnlyAssigned, false);
    const targetMarker = markerById[focusId];
    if (targetMarker) {
        map.setView(targetMarker.getLatLng(), 17);
        setTimeout(() => targetMarker.openPopup(), 300);
    }
} else {
    renderMapMarkers(initialOnlyAssigned, true);
}

// 4. Mode Tambah Titik Klik Peta & Modal Import
let isAddPointMode = false;
let tempNewMarker = null;

function toggleAddPointMode() {
    isAddPointMode = !isAddPointMode;
    const btn = document.getElementById('btn-toggle-add-point');
    const banner = document.getElementById('add-point-banner');
    const mapContainer = document.getElementById('map');

    if (isAddPointMode) {
        btn.classList.remove('bg-emerald-600', 'hover:bg-emerald-700');
        btn.classList.add('bg-red-600', 'hover:bg-red-700');
        btn.innerHTML = '✕ Batal Tambah';
        banner.classList.remove('hidden');
        mapContainer.style.cursor = 'crosshair';
    } else {
        btn.classList.add('bg-emerald-600', 'hover:bg-emerald-700');
        btn.classList.remove('bg-red-600', 'hover:bg-red-700');
        btn.innerHTML = '➕ Tambah Titik';
        banner.classList.add('hidden');
        mapContainer.style.cursor = '';
        if (tempNewMarker) {
            map.removeLayer(tempNewMarker);
            tempNewMarker = null;
        }
    }
}

map.on('click', function(e) {
    if (!isAddPointMode || !isAuthenticated) return;

    const lat = e.latlng.lat.toFixed(7);
    const lng = e.latlng.lng.toFixed(7);

    if (tempNewMarker) map.removeLayer(tempNewMarker);
    tempNewMarker = L.marker([lat, lng]).addTo(map);

    document.getElementById('manual_lat').value = lat;
    document.getElementById('manual_lng').value = lng;
    updateManualJenisDropdown();

    const modal = document.getElementById('modalAddPoint');
    modal.classList.remove('hidden');
    modal.classList.add('flex');

    setTimeout(() => document.getElementById('manual_nama_infrastruktur').focus(), 100);
    autoDetectRegion(lat, lng);
});

function closeAddPointModal() {
    const modal = document.getElementById('modalAddPoint');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    if (isAddPointMode) toggleAddPointMode();
}

function openImportModal() {
    const modal = document.getElementById('modalImportData');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function closeImportModal() {
    const modal = document.getElementById('modalImportData');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

// 5. Kamera Lapangan + Watermark GPS & Waktu
const activeStreams = {};

async function openCamera(id) {
    const box = document.getElementById(`cam-box-${id}`);
    const video = document.getElementById(`video-${id}`);
    box.classList.remove('hidden');
    try {
        const stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' }, audio: false });
        video.srcObject = stream;
        activeStreams[id] = stream;
    } catch (err) {
        alert('Kamera tidak dapat diakses. Pastikan izin kamera aktif dan menggunakan HTTPS atau localhost.');
    }
}

function renderPhotoWithWatermark(video, lat, lng) {
    const canvas = document.createElement('canvas');
    const w = video.videoWidth || 640;
    const h = video.videoHeight || 480;
    canvas.width = w;
    canvas.height = h;

    const ctx = canvas.getContext('2d');
    ctx.drawImage(video, 0, 0, w, h);

    const now = new Date();
    const pad = (n) => String(n).padStart(2, '0');
    const tanggalStr = `${pad(now.getDate())}/${pad(now.getMonth() + 1)}/${now.getFullYear()}, ${pad(now.getHours())}.${pad(now.getMinutes())}.${pad(now.getSeconds())}`;
    const gpsStr = (lat !== '' && lng !== '') ? `GPS: ${Number(lat).toFixed(7)}, ${Number(lng).toFixed(7)}` : `GPS: Tidak Tersedia`;
    const watermarkText = `${gpsStr} | ${tanggalStr}`;

    const barHeight = Math.max(28, Math.round(h * 0.08));
    const margin = Math.max(10, Math.round(w * 0.02));
    const fontSize = Math.max(12, Math.round(barHeight * 0.48));

    ctx.fillStyle = 'rgba(0, 0, 0, 0.58)';
    ctx.fillRect(margin, h - barHeight - margin, w - (margin * 2), barHeight);

    ctx.fillStyle = '#FFFFFF';
    ctx.font = `600 ${fontSize}px Arial, sans-serif`;
    ctx.textBaseline = 'middle';
    ctx.fillText(watermarkText, margin + 10, h - (barHeight / 2) - margin);

    return canvas.toDataURL('image/jpeg', 0.88);
}

function takeSnapshot(id) {
    const video = document.getElementById(`video-${id}`);
    const container = document.getElementById(`cam-inputs-${id}`);

    navigator.geolocation.getCurrentPosition(
        (pos) => {
            const lat = pos.coords.latitude;
            const lng = pos.coords.longitude;
            const base64WithWatermark = renderPhotoWithWatermark(video, lat, lng);
            appendCameraInput(container, base64WithWatermark, lat, lng);
            alert(`📸 Foto ber-watermark GPS & Jam berhasil direkam (${lat.toFixed(6)}, ${lng.toFixed(6)})`);
        },
        () => {
            const base64WithWatermark = renderPhotoWithWatermark(video, '', '');
            appendCameraInput(container, base64WithWatermark, '', '');
            alert('📸 Foto dengan cap waktu berhasil diambil (GPS perangkat tidak aktif).');
        },
        { enableHighAccuracy: true, timeout: 8000 }
    );
}

function appendCameraInput(container, base64, lat, lng) {
    container.insertAdjacentHTML('beforeend', `
        <input type="hidden" name="camera_photos[]" value="${base64}">
        <input type="hidden" name="camera_lat[]" value="${lat}">
        <input type="hidden" name="camera_lng[]" value="${lng}">
        <div class="mt-1.5 border rounded overflow-hidden bg-white p-1">
            <img src="${base64}" class="w-full h-24 object-cover rounded">
            <div class="text-[10px] text-green-700 font-semibold mt-0.5">✓ Foto ber-watermark GPS & Jam siap disimpan</div>
        </div>
    `);
}