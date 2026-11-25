// =========================================================================
// MASTER DATA JENIS INFRASTRUKTUR PER BIDANG KEMENTERIAN PU (5 BIDANG)
// =========================================================================
const INFRA_CATEGORIES = {
    BM: [
        "Efektivitas Drainase", "Gerbang Tol", "Inpres Jalan Daerah", "Jalan Daerah",
        "Jalan Nasional", "Jalan Perbatasan Kalimantan", "Jalan Tol Konstruksi",
        "Jalan Tol Operasi", "Jembatan Gantung", "Jembatan Khusus", "Jembatan Nasional",
        "Jembatan Tol", "Lereng", "Overpass Tol", "Rest Area", "Simpang Susun Tol",
        "Underpass Tol"
    ],
    CK: [
        "BPB", "IPAL", "IPLT", "PKS", "PLBN", "Rumah Sakit", "SPAM", "TPA"
    ],
    PS: [
        "Gedung SPPG", "Infrastruktur Penanganan Pasca Bencana", "Madrasah",
        "Perguruan Tinggi", "Perguruan Tinggi Keagamaan", "Pondok Pesantren",
        "Prasarana Cagar Budaya", "Sarana Olahraga", "Sarana Prasarana Kesehatan",
        "Sarana Prasarana Perekonomian", "Sarana Prasarana Peribadatan",
        "Sarana Prasarana Strategis Lainnya", "Sekolah", "Sekolah Rakyat"
    ],
    SDA: [
        "Bendung", "Bendungan Konstruksi", "Bendungan Operasi", "Bendungan Rencana",
        "Daerah Irigasi Permukaan", "Danau", "Embung", "Intake Sungai",
        "Kebutuhan Air", "Ketersediaan Air", "Mata Air", "Neraca Air",
        "PAH/ ABSAH", "Pengaman Pantai", "Pengendali Sedimen", "Pompa Banjir",
        "Pos Curah Hujan", "Pos Duga Air", "Pos Klimatologi", "Situ", "Sumur"
    ],
    SETJEN: [
        "Aset BIM PU", "Aset Tanah PU"
    ]
};

// Render Custom Dropdown Anti-Kepotong & Bisa Dicari (Searchable)
function renderCustomJenisDropdown(uid, kategori, selectedValue = '') {
    const list = INFRA_CATEGORIES[kategori] || [];
    const allItems = (selectedValue && !list.includes(selectedValue))
        ? [selectedValue, ...list]
        : list;

    const optionsHtml = allItems.map(item => `
        <button type="button"
                onclick="selectCustomJenis('${uid}', '${item.replace(/'/g, "\\'")}')"
                data-label="${item.toLowerCase()}"
                class="jenis-option-${uid} w-full text-left px-3 py-2 text-xs hover:bg-emerald-50 hover:text-emerald-900 border-b border-gray-100 last:border-0 whitespace-normal leading-snug transition ${item === selectedValue ? 'bg-emerald-100 font-bold text-emerald-900' : 'text-gray-700'}">
            ${item}
        </button>
    `).join('');

    return `
        <div class="relative w-full" id="jenis-wrapper-${uid}">
            <input type="hidden" name="jenis_infrastruktur" id="jenis-input-${uid}" value="${selectedValue}" required>
            
            <button type="button"
                    onclick="toggleCustomJenisMenu('${uid}')"
                    class="w-full flex items-center justify-between gap-2 border border-gray-300 rounded-lg px-2.5 py-1.5 bg-white text-left text-xs hover:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-200">
                <span id="jenis-label-${uid}" class="whitespace-normal leading-snug ${selectedValue ? 'text-gray-900 font-medium' : 'text-gray-400'}">
                    ${selectedValue || '-- Pilih Jenis Infrastruktur --'}
                </span>
                <span class="text-gray-400 shrink-0 text-[10px]">▼</span>
            </button>

            <div id="jenis-menu-${uid}"
                 class="hidden mt-1 w-full bg-white border border-gray-300 rounded-xl shadow-xl overflow-hidden z-50">
                <div class="p-1.5 bg-gray-50 border-b border-gray-200">
                    <input type="text"
                           id="jenis-search-${uid}"
                           oninput="filterCustomJenis('${uid}', this.value)"
                           placeholder="🔍 Ketik untuk mencari..."
                           class="w-full border border-gray-300 rounded-md px-2 py-1 text-[11px] focus:outline-none focus:border-emerald-500">
                </div>
                <div class="max-h-44 overflow-y-auto divide-y divide-gray-50">
                    ${optionsHtml}
                </div>
            </div>
        </div>
    `;
}

function toggleCustomJenisMenu(uid) {
    const menu = document.getElementById(`jenis-menu-${uid}`);
    if (!menu) return;
    const isHidden = menu.classList.contains('hidden');

    // Tutup semua menu dropdown jenis lain yang terbuka
    document.querySelectorAll('[id^="jenis-menu-"]').forEach(el => el.classList.add('hidden'));

    if (isHidden) {
        menu.classList.remove('hidden');
        const searchInput = document.getElementById(`jenis-search-${uid}`);
        if (searchInput) {
            searchInput.value = '';
            filterCustomJenis(uid, '');
            setTimeout(() => searchInput.focus(), 50);
        }
    }
}

function filterCustomJenis(uid, keyword) {
    const term = keyword.toLowerCase().trim();
    document.querySelectorAll(`.jenis-option-${uid}`).forEach(btn => {
        const label = btn.getAttribute('data-label') || '';
        btn.style.display = label.includes(term) ? 'block' : 'none';
    });
}

function selectCustomJenis(uid, value) {
    const input = document.getElementById(`jenis-input-${uid}`);
    const label = document.getElementById(`jenis-label-${uid}`);
    const menu = document.getElementById(`jenis-menu-${uid}`);

    if (input) input.value = value;
    if (label) {
        label.textContent = value;
        label.classList.remove('text-gray-400');
        label.classList.add('text-gray-900', 'font-medium');
    }
    if (menu) menu.classList.add('hidden');
}

// Helper untuk memperbarui container dropdown di Modal Tambah Titik Manual
function updateManualJenisDropdown() {
    const katEl = document.getElementById('manual_kategori');
    const container = document.getElementById('manual_jenis_container');
    if (!katEl || !container) return;
    container.innerHTML = renderCustomJenisDropdown('manual', katEl.value, '');
}