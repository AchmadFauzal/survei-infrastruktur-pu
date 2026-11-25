// =========================================================================
// LAYER POLIGON BATAS WILAYAH (DARI DATABASE GEOJSON LOKAL) & POINT-IN-POLYGON
// =========================================================================
let wilayahGeoJsonData = null;
let provinsiMapByKode = {}; // { '33': 'Jawa Tengah', '32': 'Jawa Barat', ... }

const batasWilayahLayer = L.geoJSON(null, {
    style: function () {
        return {
            color: '#fbbf24',      // Garis batas kuning keemasan
            weight: 1.5,
            opacity: 0.9,
            fillColor: '#3b82f6',
            fillOpacity: 0.05
        };
    },
    onEachFeature: function (feature, layer) {
        const props = feature.properties;
        const namaProv = provinsiMapByKode[props.kode_prov] || '';
        layer.bindTooltip(
            `<strong>${props.nama}</strong>${namaProv ? `<br><span class="text-[10px]">Prov. ${namaProv}</span>` : ''}`,
            { sticky: true, direction: 'top' }
        );
    }
});

async function loadWilayahBoundaries(mapInstance) {
    try {
        const [resProv, resKab] = await Promise.all([
            fetch(window.GIS_CONFIG.routes.wilayahProv),
            fetch(window.GIS_CONFIG.routes.wilayahKab)
        ]);

        const provGeo = await resProv.json();
        (provGeo.features || []).forEach(f => {
            provinsiMapByKode[f.properties.kode] = f.properties.nama;
        });

        wilayahGeoJsonData = await resKab.json();
        batasWilayahLayer.addData(wilayahGeoJsonData);
        batasWilayahLayer.addTo(mapInstance);
    } catch (e) {
        console.warn('Gagal memuat batas wilayah:', e);
    }
}

function isPointInRing(point, ring) {
    const x = point[0], y = point[1];
    let inside = false;
    for (let i = 0, j = ring.length - 1; i < ring.length; j = i++) {
        const xi = ring[i][0], yi = ring[i][1];
        const xj = ring[j][0], yj = ring[j][1];
        const intersect = ((yi > y) !== (yj > y)) && (x < (xj - xi) * (y - yi) / (yj - yi + 1e-12) + xi);
        if (intersect) inside = !inside;
    }
    return inside;
}

function findRegionFromLocalGeoJson(lat, lng) {
    if (!wilayahGeoJsonData || !wilayahGeoJsonData.features) return null;
    const pt = [parseFloat(lng), parseFloat(lat)];

    for (const feature of wilayahGeoJsonData.features) {
        const geom = feature.geometry;
        if (!geom) continue;

        let matched = false;
        if (geom.type === 'Polygon') {
            matched = isPointInRing(pt, geom.coordinates[0]);
        } else if (geom.type === 'MultiPolygon') {
            matched = geom.coordinates.some(poly => isPointInRing(pt, poly[0]));
        }

        if (matched) {
            const props = feature.properties;
            return {
                kota: props.nama,
                provinsi: provinsiMapByKode[props.kode_prov] || ''
            };
        }
    }
    return null;
}

function autoDetectRegion(lat, lng) {
    const kotaInput = document.getElementById('manual_kota');
    const provInput = document.getElementById('manual_provinsi');
    const statusEl = document.getElementById('geocode-status');

    const localMatch = findRegionFromLocalGeoJson(lat, lng);

    if (localMatch) {
        kotaInput.value = localMatch.kota;
        provInput.value = localMatch.provinsi;
        statusEl.textContent = '✓ Terdeteksi dari Batas Wilayah Lokal';
        statusEl.className = 'text-[10px] font-bold text-emerald-700';
    } else {
        kotaInput.value = '';
        provInput.value = '';
        statusEl.textContent = '⚠️ Di luar poligon daratan, silakan isi manual';
        statusEl.className = 'text-[10px] font-semibold text-amber-600';
    }
}