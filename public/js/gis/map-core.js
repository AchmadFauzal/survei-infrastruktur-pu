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

// 1. Setup Basemap Layers
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
    zoomControl: false,
    layers: [basemapLayers.osm]
});
window.map = map;

// Muat poligon batas wilayah lokal
loadWilayahBoundaries(map);

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
            html: `<div style="width:26px;height:26px;border-radius:50%;background:#002966;color:#fff;font-size:12px;font-weight:800;display:flex;align-items:center;justify-content:center;border:3px solid #FABD03;box-shadow:0 3px 8px rgba(0,0,0,0.45);">${routeOrder}</div>`,
            iconSize: [26, 26],
            iconAnchor: [13, 13],
            popupAnchor: [0, -13]
        });
    }

    const ringStyle = isMyTask
        ? 'box-shadow: 0 0 0 3.5px #FABD03, 0 3px 8px rgba(0,0,0,0.45);'
        : 'box-shadow: 0 2px 6px rgba(0,0,0,0.4);';
    return L.divIcon({
        className: '',
        html: `<div style="width:18px;height:18px;border-radius:50%;background:${color};border:2.5px solid #fff;${ringStyle}"></div>`,
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
        photosHtml = `<div style="display:grid; grid-template-columns:repeat(3, 1fr); gap:6px; margin-top:8px;">`;
        item.photos.forEach(photo => {
            const gpsTag = photo.photo_latitude
                ? `GPS: ${Number(photo.photo_latitude).toFixed(4)}, ${Number(photo.photo_longitude).toFixed(4)}`
                : 'Upload File';
            photosHtml += `
                <div style="position:relative; border:1px solid #e2e8f0; border-radius:8px; overflow:hidden; background:#f8fafc;">
                    <a href="/storage/${photo.file_path}" target="_blank" style="display:block;">
                        <img src="/storage/${photo.file_path}" style="width:100%; height:56px; object-fit:cover; display:block;" title="${gpsTag}">
                    </a>
                    ${isAuthenticated ? `
                    <form method="POST" action="/survey/photo/${photo.id}" onsubmit="return confirm('Hapus foto ini?')" style="position:absolute; top:3px; right:3px; margin:0;">
                        <input type="hidden" name="_token" value="${csrfToken}">
                        <input type="hidden" name="_method" value="DELETE">
                        <button type="submit" title="Hapus Foto" style="width:18px; height:18px; padding:0 !important; border:none; border-radius:5px; background:rgba(220,38,38,0.92); color:#fff; font-size:11px; font-weight:700; line-height:1; display:flex; align-items:center; justify-content:center; cursor:pointer;">×</button>
                    </form>` : ''}
                </div>
            `;
        });
        photosHtml += `</div>`;
    }

    let popupContent = `
        <div style="font-size:12px;">
            ${routeNum ? `
                <div style="display:inline-flex; align-items:center; gap:5px; background:#002966; color:#FABD03; padding:3px 8px; border-radius:6px; font-weight:700; font-size:10px; margin-bottom:6px; letter-spacing:0.3px;">
                    <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2.4" viewBox="0 0 24 24"><polygon points="3 11 22 2 13 21 11 13 3 11"/></svg>
                    <span>RUTE KUNJUNGAN #${routeNum}</span>
                </div>` : ''}
            ${isMyTask ? `
                <div style="display:inline-flex; align-items:center; gap:5px; background:#fffbeb; color:#92400e; border:1px solid #fcd34d; padding:3px 8px; border-radius:6px; font-weight:700; font-size:10px; margin-bottom:6px;">
                    <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2.4" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 21s-6-5.686-6-10a6 6 0 1112 0c0 4.314-6 10-6 10z"/><circle cx="12" cy="11" r="2"/></svg>
                    <span>TUGAS SURVEI ANDA</span>
                </div>` : ''}

            <div style="display:flex; justify-content:space-between; align-items:flex-start; gap:8px; margin-bottom:6px;">
                <span style="font-weight:800; font-size:13.5px; color:#002966; line-height:1.3; padding-right:20px;">${item.nama_infrastruktur}</span>
            </div>

            <div style="display:flex; flex-wrap:wrap; align-items:center; gap:5px; margin-bottom:8px;">
                <span style="padding:2px 8px; border-radius:6px; font-size:10px; font-weight:700; color:#fff; background:${item.status_survei === 'sudah' ? '#16a34a' : '#dc2626'};">
                    ${item.status_survei === 'sudah' ? 'SUDAH SURVEI' : 'BELUM SURVEI'}
                </span>
                <span style="padding:2px 7px; border-radius:6px; font-size:10px; font-weight:700; background:#f1f5f9; color:#002966; border:1px solid #e2e8f0;">
                    ${item.kategori}
                </span>
            </div>

            <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:10px; padding:8px 10px; space-y:3px; font-size:11px; color:#475569;">
                <div><strong style="color:#1e293b;">Wilayah:</strong> ${item.kota_kabupaten}, ${item.provinsi}</div>
                ${item.nama_hasil_survey ? `<div style="margin-top:2px;"><strong style="color:#1e293b;">Hasil Survei:</strong> ${item.nama_hasil_survey}</div>` : ''}
                <div style="margin-top:2px; font-family:monospace; font-size:10.5px; color:#64748b;">${lat.toFixed(6)}, ${lng.toFixed(6)}</div>
                ${item.surveyor ? `<div style="margin-top:3px; color:#002966; font-weight:600;">Petugas: ${item.surveyor.name}</div>` : ''}
            </div>

            ${photosHtml}

            ${(isAuthenticated && item.photos && item.photos.length > 0) ? `
                <div style="margin-top:8px;">
                    <a href="/survey/${item.id}/download-zip"
                       style="display:flex; align-items:center; justify-content:center; gap:6px; background:#f1f5f9; color:#002966; border:1px solid #cbd5e1; text-decoration:none; font-size:11px; font-weight:700; padding:6px 10px; border-radius:9px;">
                        <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5 5-5M12 15V3"/></svg>
                        <span>Unduh Semua Foto (.ZIP)</span>
                    </a>
                </div>
            ` : ''}
    `;

    if (isAuthenticated) {
        const sisaSlot = 5 - (item.photos ? item.photos.length : 0);
        popupContent += `
            <hr>
            <form method="POST" action="/survey/${item.id}/update" enctype="multipart/form-data">
                <input type="hidden" name="_token" value="${csrfToken}">
                <input type="hidden" name="survey_latitude" id="lat-${item.id}" value="${lat}">
                <input type="hidden" name="survey_longitude" id="lng-${item.id}" value="${lng}">

                <div>
                    <label>Status Survei</label>
                    <select name="status_survei">
                        <option value="belum" ${item.status_survei === 'belum' ? 'selected' : ''}>Belum Survei</option>
                        <option value="sudah" ${item.status_survei === 'sudah' ? 'selected' : ''}>Sudah Survei</option>
                    </select>
                </div>

                <div>
                    <label>Jenis Infrastruktur (${item.kategori})</label>
                    ${renderCustomJenisDropdown(`popup-${item.id}`, item.kategori, item.jenis_infrastruktur || '')}
                </div>

                <div>
                    <label>Nama Hasil Survei</label>
                    <input type="text" name="nama_hasil_survey" maxlength="255"
                           value="${escapeHtmlAttribute(item.nama_hasil_survey)}"
                           placeholder="Contoh: Jembatan Meureudu (Lajur Nasional)">
                </div>

                <div>
                    <label>Catatan / Alasan Verifikasi</label>
                    <textarea name="alasan_verifikasi" rows="2" placeholder="Tulis catatan kondisi lapangan...">${item.alasan_verifikasi || ''}</textarea>
                </div>

                ${sisaSlot > 0 ? `
                <div style="background:#f8fafc; padding:9px; border-radius:10px; border:1px solid #e2e8f0; margin-top:10px;">
                    <label style="margin-top:0 !important;">Dokumentasi Foto (Sisa kuota: ${sisaSlot}/5)</label>
                    <input type="file" name="photos[]" multiple accept="image/*" style="margin-bottom:7px;">

                    <button type="button" onclick="openCamera(${item.id})"
                            style="width:100%; background:#002966; color:#ffffff; border:none; display:flex; align-items:center; justify-content:center; gap:6px;">
                        <svg width="14" height="14" fill="none" stroke="#FABD03" stroke-width="2.2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M23 19a2 2 0 01-2 2H3a2 2 0 01-2-2V8a2 2 0 012-2h4l2-3h6l2 3h4a2 2 0 012 2z"/>
                            <circle cx="12" cy="13" r="4"/>
                        </svg>
                        <span>Buka Kamera + Rekam GPS</span>
                    </button>

                    <div id="cam-box-${item.id}" class="hidden" style="margin-top:8px;">
                        <video id="video-${item.id}" autoplay playsinline style="width:100%; border-radius:8px; background:#000; max-height:140px; display:block;"></video>
                        <button type="button" onclick="takeSnapshot(${item.id})"
                                style="width:100%; margin-top:6px; background:#FABD03; color:#002966; border:none; font-weight:800;">
                            Ambil Jepretan GPS
                        </button>
                    </div>
                    <div id="cam-inputs-${item.id}"></div>
                </div>
                ` : `<div style="font-size:11px; color:#92400e; background:#fffbeb; border:1px solid #fcd34d; padding:7px 9px; border-radius:8px; margin-top:8px;">Kuota maksimal 5 foto telah tercapai.</div>`}

                <button type="submit"
                        style="width:100%; margin-top:10px; background:#059669; color:#ffffff; border:none; display:flex; align-items:center; justify-content:center; gap:6px; box-shadow:0 4px 12px rgba(5,150,105,0.22);">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.4" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span>Simpan Hasil Survei</span>
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
            maxWidth: 340,
            minWidth: 300,
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
    const panel = document.getElementById('floating-route-panel');
    if (panel) panel.style.display = 'none';
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
    if (panel) panel.style.display = 'block';

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
                html: `<div style="width:24px;height:24px;border-radius:50%;background:#002966;color:#FABD03;font-size:11px;font-weight:800;display:flex;align-items:center;justify-content:center;border:3px solid #fff;box-shadow:0 2px 8px rgba(0,0,0,0.45);">S</div>`,
                iconSize: [24, 24],
                iconAnchor: [12, 12]
            });
            L.marker([wp.lat, wp.lng], { icon: startIcon })
                .bindPopup('<strong>Titik Berangkat (Lokasi GPS Anda)</strong>')
                .addTo(routeOverlayLayer);
        }
    });

    if (geometry) {
        L.geoJSON(geometry, { style: { color: '#002966', weight: 5, opacity: 0.85 } }).addTo(routeOverlayLayer);
    } else if (bounds.length > 1) {
        L.polyline(bounds, { color: '#002966', weight: 4, dashArray: '6, 8' }).addTo(routeOverlayLayer);
    }

    if (bounds.length > 0) map.fitBounds(bounds, { padding: [70, 70], maxZoom: 15 });

    if (distMeters !== null && durSeconds !== null) {
        const km = (distMeters / 1000).toFixed(1);
        const mins = Math.round(durSeconds / 60);
        const durStr = mins >= 60 ? `${Math.floor(mins / 60)} jam ${mins % 60} mnt` : `${mins} menit`;
        statsEl.textContent = `${km} km | ± ${durStr}`;
    } else {
        statsEl.textContent = `${targetInfras.length} Titik Tujuan`;
    }

    const listEl = document.getElementById('floating-route-list');
    listEl.innerHTML = orderedWaypoints.map(wp => {
        if (wp.type === 'start') {
            return `<li style="padding:6px 4px; display:flex; align-items:center; gap:8px; color:#002966; font-weight:700; border-bottom:1px solid #f1f5f9;">
                        <span style="padding:2px 6px; border-radius:5px; background:#002966; color:#FABD03; font-size:10px;">START</span>
                        <span>Lokasi GPS Anda</span>
                    </li>`;
        }
        const num = routeOrderMap[wp.id];
        return `<li style="padding:7px 4px; display:flex; align-items:center; justify-content:space-between; gap:8px; border-bottom:1px solid #f1f5f9;">
                    <button type="button" onclick="focusToRoutePoint(${wp.id})" style="background:none; border:none; padding:0; text-align:left; flex:1; cursor:pointer; overflow:hidden;">
                        <span style="display:inline-flex; align-items:center; justify-content:center; width:18px; height:18px; border-radius:99px; background:#002966; color:#fff; font-size:10px; font-weight:700; margin-right:5px;">${num}</span>
                        <strong style="color:#0f172a; font-size:11.5px;">${wp.name}</strong>
                        <span style="color:#64748b; font-size:10px; display:block; padding-left:23px;">${wp.kota}</span>
                    </button>
                    <button type="button" onclick="focusToRoutePoint(${wp.id})"
                            style="background:#FABD03; color:#002966; border:none; padding:4px 8px; border-radius:6px; font-weight:700; font-size:10px; cursor:pointer; flex-shrink:0;">
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

// 4. Mode Tambah Titik Klik Peta & Modal Import (Ikon Pin Terkunci, Tanpa Emoji)
let isAddPointMode = false;
let tempNewMarker = null;

const ICON_PIN_ADD = `
    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.4" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 21s-6-5.686-6-10a6 6 0 1112 0c0 4.314-6 10-6 10z"/>
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v6M9 11h6"/>
    </svg>`;

const ICON_CLOSE_ADD = `
    <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
    </svg>`;

function toggleAddPointMode() {
    isAddPointMode = !isAddPointMode;
    const btn = document.getElementById('btn-toggle-add-point');
    const banner = document.getElementById('add-point-banner');
    const mapContainer = document.getElementById('map');

    if (isAddPointMode) {
        btn.style.background = '#dc2626';
        btn.innerHTML = `${ICON_CLOSE_ADD} <span class="tool-label">Batal Tambah</span>`;
        if (banner) banner.classList.remove('hidden');
        mapContainer.style.cursor = 'crosshair';
    } else {
        btn.style.background = '#059669';
        btn.innerHTML = `${ICON_PIN_ADD} <span class="tool-label">Tambah Titik</span>`;
        if (banner) banner.classList.add('hidden');
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
            alert(`Foto ber-watermark GPS & waktu berhasil direkam (${lat.toFixed(6)}, ${lng.toFixed(6)})`);
        },
        () => {
            const base64WithWatermark = renderPhotoWithWatermark(video, '', '');
            appendCameraInput(container, base64WithWatermark, '', '');
            alert('Foto dengan cap waktu berhasil diambil (GPS perangkat tidak aktif).');
        },
        { enableHighAccuracy: true, timeout: 8000 }
    );
}

function appendCameraInput(container, base64, lat, lng) {
    container.insertAdjacentHTML('beforeend', `
        <input type="hidden" name="camera_photos[]" value="${base64}">
        <input type="hidden" name="camera_lat[]" value="${lat}">
        <input type="hidden" name="camera_lng[]" value="${lng}">
        <div style="margin-top:6px; border:1px solid #cbd5e1; border-radius:8px; overflow:hidden; background:#fff; padding:4px;">
            <img src="${base64}" style="width:100%; height:96px; object-fit:cover; border-radius:6px; display:block;">
            <div style="font-size:10px; color:#15803d; font-weight:700; margin-top:4px; display:flex; align-items:center; gap:4px;">
                <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                <span>Foto ber-watermark GPS & Jam siap disimpan</span>
            </div>
        </div>
    `);
}