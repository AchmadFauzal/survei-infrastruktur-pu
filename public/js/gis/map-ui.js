// =========================================================================
// KONTROL ANTARMUKA PETA: SEARCHBAR, BASEMAP SWITCHER & POPOVER
// =========================================================================

function toggleBasemapPopover(e) {
    if (e) e.stopPropagation();
    const el = document.getElementById('basemap-popover');
    if (!el) return;
    el.style.display = el.style.display === 'none' ? 'block' : 'none';
}

document.addEventListener('mousedown', function (e) {
    if (!e.target.closest('#basemap-popover') && !e.target.closest('.btn-basemap')) {
        const bp = document.getElementById('basemap-popover');
        if (bp) bp.style.display = 'none';
    }
    if (!e.target.closest('.search-wrapper')) {
        const sr = document.getElementById('map-search-results');
        if (sr) sr.style.display = 'none';
    }
});

const CUSTOM_BASEMAPS = {
    osm: L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '© OpenStreetMap'
    }),
    satellite: L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
        maxZoom: 19,
        attribution: '© Esri'
    }),
    topo: L.tileLayer('https://{s}.tile.opentopomap.org/{z}/{x}/{y}.png', {
        maxZoom: 17,
        attribution: '© OpenTopoMap'
    }),
    light: L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
        maxZoom: 19,
        attribution: '© CARTO'
    })
};

function switchCustomBasemap(key) {
    const targetMap = window.map;
    if (!targetMap || !CUSTOM_BASEMAPS[key]) return;

    const tileLayersToRemove = [];
    targetMap.eachLayer(function (layer) {
        if (layer instanceof L.TileLayer) tileLayersToRemove.push(layer);
    });
    tileLayersToRemove.forEach(layer => targetMap.removeLayer(layer));

    CUSTOM_BASEMAPS[key].addTo(targetMap).bringToBack();

    const checkIcon = '<svg width="14" height="14" fill="none" stroke="#002966" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>';
    document.querySelectorAll('.basemap-opt').forEach(btn => {
        const isSelected = btn.dataset.basemap === key;
        const label = btn.querySelector('span').innerText;
        btn.style.background = isSelected ? '#f0f9ff' : 'transparent';
        btn.style.color = isSelected ? '#002966' : '#334155';
        btn.style.fontWeight = isSelected ? '700' : '600';
        btn.innerHTML = isSelected ? `<span>${label}</span>${checkIcon}` : `<span>${label}</span>`;
    });

    document.getElementById('basemap-popover').style.display = 'none';
}

// Searchbar Infrastruktur Real-Time
const searchInput = document.getElementById('map-search-input');
const searchResults = document.getElementById('map-search-results');
const clearBtn = document.getElementById('btn-clear-search');

searchInput?.addEventListener('input', function () {
    const q = this.value.trim().toLowerCase();
    clearBtn.style.display = q.length > 0 ? 'inline-flex' : 'none';

    if (q.length < 2) {
        searchResults.style.display = 'none';
        return;
    }

    const list = (window.GIS_CONFIG?.infrastructures || []).filter(item =>
        (item.nama_infrastruktur || '').toLowerCase().includes(q) ||
        (item.kota_kabupaten || '').toLowerCase().includes(q) ||
        (item.provinsi || '').toLowerCase().includes(q) ||
        (item.kategori || '').toLowerCase().includes(q)
    ).slice(0, 8);

    if (list.length === 0) {
        searchResults.innerHTML = `<div style="padding:12px; font-size:12px; color:#94a3b8; text-align:center;">Infrastruktur tidak ditemukan</div>`;
    } else {
        searchResults.innerHTML = list.map(item => `
            <div onmousedown="focusInfraOnMap(${Number(item.latitude)}, ${Number(item.longitude)})"
                 style="width:100%; text-align:left; border-bottom:1px solid #f1f5f9; padding:10px 14px; cursor:pointer; display:flex; justify-content:space-between; align-items:flex-start; gap:8px; transition:background 0.15s;"
                 onmouseover="this.style.background='#f0f9ff'" onmouseout="this.style.background='transparent'">
                <div>
                    <div style="font-size:12px; font-weight:700; color:#1e293b;">${item.nama_infrastruktur}</div>
                    <div style="font-size:11px; color:#64748b;">${item.kota_kabupaten || '-'}, ${item.provinsi || '-'}</div>
                </div>
                <span style="flex-shrink:0; font-size:10px; font-weight:700; padding:2px 7px; border-radius:6px; background:${item.status_survei === 'sudah' ? '#dcfce7' : '#fee2e2'}; color:${item.status_survei === 'sudah' ? '#15803d' : '#b91c1c'};">
                    ${item.kategori || 'PU'}
                </span>
            </div>
        `).join('');
    }
    searchResults.style.display = 'block';
});

function focusInfraOnMap(lat, lng) {
    const targetMap = window.map;
    if (!targetMap || isNaN(lat) || isNaN(lng)) return;

    searchResults.style.display = 'none';
    targetMap.flyTo([lat, lng], 16, { duration: 1.2 });

    targetMap.once('moveend', function () {
        targetMap.eachLayer(function (layer) {
            if (layer instanceof L.Marker || layer instanceof L.CircleMarker) {
                const pos = layer.getLatLng();
                if (Math.abs(pos.lat - lat) < 0.0001 && Math.abs(pos.lng - lng) < 0.0001) {
                    layer.openPopup();
                }
            }
        });
    });
}

function clearMapSearch() {
    searchInput.value = '';
    clearBtn.style.display = 'none';
    searchResults.style.display = 'none';
}