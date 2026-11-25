@extends('surveyor.layout')

@section('title', 'Riwayat & Pelaporan Survei')
@section('page_title', '📋 Riwayat & Pelaporan Hasil Survei')
@section('page_subtitle', 'Kelola hasil survei lapangan, unduh dokumentasi foto, dan buat Berita Acara Excel.')

@section('header_actions')
    <a href="{{ route('surveyor.assignments') }}"
       class="bg-amber-500 hover:bg-amber-600 text-slate-950 px-3.5 py-2 rounded-xl text-xs font-bold shadow-sm transition flex items-center gap-1.5">
        🧭 Penugasan & Buat Rute
        @if($stats['tugas_aktif'] > 0)
            <span class="bg-slate-950 text-amber-300 px-1.5 py-0.5 rounded-full text-[10px]">{{ $stats['tugas_aktif'] }}</span>
        @endif
    </a>
@endsection

@push('styles')
    <style>#signature-canvas { touch-action: none; cursor: crosshair; }</style>
@endpush

@section('content')
    <!-- Kartu Statistik Ringkas -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <a href="{{ route('surveyor.assignments') }}" class="bg-amber-50 hover:bg-amber-100/70 p-4 rounded-2xl shadow-sm border border-amber-300 transition block">
            <p class="text-[11px] font-bold text-amber-800 uppercase">Tugas Aktif Anda →</p>
            <p class="text-2xl font-extrabold text-amber-900 mt-1">{{ $stats['tugas_aktif'] }} <span class="text-xs font-normal">lokasi</span></p>
        </a>
        <div class="bg-white p-4 rounded-2xl shadow-sm border-l-4 border-green-600">
            <p class="text-[11px] font-bold text-gray-500 uppercase">Selesai Saya Survei</p>
            <p class="text-2xl font-extrabold text-green-700 mt-1">{{ $stats['saya_survei'] }} <span class="text-xs font-normal text-gray-500">titik</span></p>
        </div>
        <div class="bg-white p-4 rounded-2xl shadow-sm border-l-4 border-blue-600">
            <p class="text-[11px] font-bold text-gray-500 uppercase">Dokumentasi Foto</p>
            <p class="text-2xl font-extrabold text-blue-700 mt-1">{{ $stats['total_foto'] }} <span class="text-xs font-normal text-gray-500">foto</span></p>
        </div>
        <div class="bg-white p-4 rounded-2xl shadow-sm border-l-4 border-slate-500">
            <p class="text-[11px] font-bold text-gray-500 uppercase">Sisa Target Peta</p>
            <p class="text-2xl font-extrabold text-slate-700 mt-1">{{ $stats['sisa_wilayah'] }} <span class="text-xs font-normal text-gray-500">titik</span></p>
        </div>
    </div>

    <!-- Seksi Pelaporan Hasil Survei & Berita Acara -->
    <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-200 space-y-5">
        <div class="flex flex-wrap justify-between items-center gap-3 border-b pb-4">
            <div>
                <h2 class="font-bold text-base text-gray-900">Daftar Infrastruktur yang Telah Anda Survei</h2>
                <p class="text-xs text-gray-500">Centang titik yang ingin dimasukkan ke dalam Berita Acara (.xlsx), unduh foto satuan, atau unduh arsip .ZIP.</p>
            </div>
            <span id="selected-counter-badge" class="px-3 py-1.5 rounded-full text-xs font-bold bg-blue-100 text-blue-800">
                0 titik dipilih
            </span>
        </div>

        <!-- Filter Tabel -->
        <form method="GET" action="{{ route('surveyor.dashboard') }}" class="flex flex-wrap gap-3 bg-gray-50 p-3 rounded-xl border">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama objek, jenis, atau kota..."
                   class="flex-1 min-w-[200px] border border-gray-300 rounded-lg px-4 py-2 text-xs bg-white">
            <select name="kategori" class="border border-gray-300 rounded-lg px-4 py-2 text-xs bg-white">
                <option value="">Semua Kategori</option>
                @foreach(['BM' => 'Bina Marga', 'CK' => 'Cipta Karya', 'SDA' => 'Sumber Daya Air', 'PS' => 'Prasarana Strategis'] as $kode => $nama)
                    <option value="{{ $kode }}" {{ request('kategori') == $kode ? 'selected' : '' }}>{{ $kode }} - {{ $nama }}</option>
                @endforeach
            </select>
            <button type="submit" class="bg-slate-800 hover:bg-slate-900 text-white px-5 py-2 rounded-lg text-xs font-semibold">
                Filter Data
            </button>
            <a href="{{ route('surveyor.dashboard') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg text-xs font-semibold">
                Reset
            </a>
        </form>

        <!-- Tabel Hasil Survei -->
        <div class="rounded-xl border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead class="bg-gray-100 border-b border-gray-200 uppercase text-gray-600">
                        <tr>
                            <th class="py-3.5 px-4 w-10 text-center">
                                <input type="checkbox" id="select-all-checkbox" checked class="w-4 h-4 accent-blue-600 rounded cursor-pointer">
                            </th>
                            <th class="py-3.5 px-4">Objek Infrastruktur</th>
                            <th class="py-3.5 px-4">Koordinat Aktual Foto</th>
                            <th class="py-3.5 px-4">Waktu Survei</th>
                            <th class="py-3.5 px-4">Dokumentasi Foto</th>
                            <th class="py-3.5 px-4 text-right">Aksi Unduh</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        @forelse($mySurveys as $item)
                            <tr class="hover:bg-blue-50/40 transition">
                                <td class="py-3 px-4 text-center">
                                    <input type="checkbox" value="{{ $item->id }}" checked class="survey-item-checkbox w-4 h-4 accent-blue-600 rounded cursor-pointer">
                                </td>
                                <td class="py-3 px-4">
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="px-2 py-0.5 text-[9px] font-bold rounded-full bg-slate-200 text-slate-800">{{ $item->kategori }}</span>
                                        <span class="px-2 py-0.5 text-[9px] font-bold rounded-full bg-green-100 text-green-800">✓ Selesai</span>
                                    </div>
                                    <p class="font-bold text-gray-900 text-sm">{{ $item->nama_infrastruktur }}</p>
                                    <p class="text-gray-600 text-[11px]">{{ $item->jenis_infrastruktur ?? 'Jenis belum diisi' }}</p>
                                    <p class="text-gray-700 text-[11px]">Nama hasil survei: {{ $item->nama_hasil_survey ?? '-' }}</p>
                                    <p class="text-gray-400 text-[10px]">{{ $item->kota_kabupaten }}, {{ $item->provinsi }}</p>
                                    @if($item->alasan_verifikasi)
                                        <p class="text-gray-600 italic mt-1 text-[11px] bg-gray-50 p-1.5 rounded border">"{{ $item->alasan_verifikasi }}"</p>
                                    @endif
                                </td>
                                <td class="py-3 px-4 font-mono text-[11px]">
                                    @if($item->photos->whereNotNull('photo_latitude')->count() > 0)
                                        @php $p = $item->photos->whereNotNull('photo_latitude')->first(); @endphp
                                        {{ number_format($p->photo_latitude, 6) }},<br>
                                        {{ number_format($p->photo_longitude, 6) }}
                                    @else
                                        <span class="text-gray-400 italic text-[10px]">Upload tanpa GPS</span>
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-gray-600 text-[11px]">
                                    {{ $item->surveyed_at ? $item->surveyed_at->format('d M Y, H:i') . ' WIB' : '-' }}
                                </td>
                                <td class="py-3 px-4">
                                    @if($item->photos->count() > 0)
                                        <div class="flex flex-wrap items-center gap-1.5">
                                            @foreach($item->photos as $idx => $photo)
                                                <div class="relative group border border-gray-200 rounded-lg p-0.5 bg-white shadow-sm">
                                                    <a href="/storage/{{ $photo->file_path }}" target="_blank" title="Lihat ukuran penuh">
                                                        <img src="/storage/{{ $photo->file_path }}"
                                                             class="w-11 h-11 rounded object-cover {{ $idx === 0 ? 'ring-2 ring-green-500' : '' }}">
                                                    </a>
                                                    @if($idx === 0)
                                                        <span class="absolute -top-1.5 -left-1.5 bg-green-600 text-white text-[8px] font-bold px-1 rounded">BAP</span>
                                                    @endif
                                                    <a href="{{ route('survey.photo.download', $photo->id) }}"
                                                       title="Unduh Foto #{{ $idx + 1 }}"
                                                       class="absolute -bottom-1.5 -right-1.5 bg-blue-600 hover:bg-blue-700 text-white text-[9px] font-bold px-1.5 py-0.5 rounded shadow">
                                                        ⬇
                                                    </a>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <span class="text-gray-400 italic text-[11px]">Tidak ada foto</span>
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-right">
                                    @if($item->photos->count() > 0)
                                        <a href="{{ route('survey.photos.download_zip', $item->id) }}"
                                           class="inline-flex items-center gap-1 bg-slate-800 hover:bg-slate-900 text-white text-[11px] font-semibold px-3 py-2 rounded-lg shadow-sm transition">
                                            📦 Unduh ZIP ({{ $item->photos->count() }})
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-10 text-center text-gray-500">
                                    Belum ada data survei yang selesai. Silakan buka menu <strong>Penugasan & Rute</strong> untuk mulai survei.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Panel Tanda Tangan & Export BAP -->
        <div class="bg-gray-50 p-5 rounded-2xl border border-gray-200 grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="md:col-span-2 space-y-3">
                <h4 class="font-bold text-sm text-gray-800">✍️ Buat Berita Acara Serah Terima Hasil Survei (BAP)</h4>
                <p class="text-xs text-gray-600">
                    Centang baris infrastruktur pada tabel di atas, lengkapi identitas wilayah, lalu bubuhkan tanda tangan digital sebelum mengunduh file Excel (.xlsx).
                </p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-1">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Nama Surveyor (Pihak Kesatu)</label>
                        <input type="text" id="ba_surveyor_name" value="{{ auth()->user()->name }}"
                               class="w-full border border-gray-300 rounded-lg px-3.5 py-2 text-xs bg-white">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Wilayah Kewenangan / Lokasi</label>
                        <input type="text" id="ba_lokasi" placeholder="Contoh: Kab. Banyuwangi, Jawa Timur"
                               class="w-full border border-gray-300 rounded-lg px-3.5 py-2 text-xs bg-white">
                    </div>
                </div>
            </div>

            <div class="bg-white p-4 rounded-xl border border-gray-200 space-y-2">
                <label class="block text-xs font-semibold text-gray-700">Tanda Tangan Digital</label>
                <div class="border-2 border-dashed border-gray-300 rounded-lg bg-gray-50 relative h-28">
                    <canvas id="signature-canvas" class="absolute inset-0 w-full h-full rounded-lg"></canvas>
                </div>
                <div class="flex justify-between items-center text-[11px]">
                    <span id="ttd-status" class="text-gray-400">Belum ada tanda tangan</span>
                    <button type="button" id="clear-signature-btn" class="text-red-600 hover:underline font-semibold">Hapus TTD</button>
                </div>
                <button type="button" id="btn-export-bap" onclick="exportBeritaAcaraExcel()"
                        class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-2.5 px-4 rounded-xl shadow text-xs transition flex items-center justify-center gap-2">
                    📥 Download Berita Acara (.xlsx)
                </button>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/exceljs/4.4.0/exceljs.min.js"></script>
<script>
    const mySurveysData = @json($mySurveys);

    // 1. Checkbox Pilihan Berita Acara
    const selectAllCheckbox = document.getElementById('select-all-checkbox');
    const itemCheckboxes = document.querySelectorAll('.survey-item-checkbox');
    const counterBadge = document.getElementById('selected-counter-badge');

    function updateSelectedCounter() {
        const checkedCount = document.querySelectorAll('.survey-item-checkbox:checked').length;
        const totalItems = itemCheckboxes.length;
        counterBadge.textContent = `${checkedCount} dari ${totalItems} titik dipilih`;

        if (totalItems > 0) {
            selectAllCheckbox.checked = (checkedCount === totalItems);
            selectAllCheckbox.indeterminate = (checkedCount > 0 && checkedCount < totalItems);
        }
    }

    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', (e) => {
            itemCheckboxes.forEach(cb => { cb.checked = e.target.checked; });
            updateSelectedCounter();
        });
    }
    itemCheckboxes.forEach(cb => cb.addEventListener('change', updateSelectedCounter));
    updateSelectedCounter();

    // 2. Canvas Tanda Tangan Digital
    const canvas = document.getElementById('signature-canvas');
    const ctx = canvas.getContext('2d');
    let drawing = false;
    let hasInk = false;
    const ttdStatus = document.getElementById('ttd-status');

    function resizeCanvas() {
        const container = canvas.parentElement;
        const ratio = window.devicePixelRatio || 1;
        canvas.width = container.clientWidth * ratio;
        canvas.height = container.clientHeight * ratio;
        ctx.scale(ratio, ratio);
        ctx.lineWidth = 2.5;
        ctx.lineCap = 'round';
        ctx.strokeStyle = '#111827';
    }
    resizeCanvas();

    function getPos(e) {
        const rect = canvas.getBoundingClientRect();
        const clientX = e.touches ? e.touches[0].clientX : e.clientX;
        const clientY = e.touches ? e.touches[0].clientY : e.clientY;
        return { x: clientX - rect.left, y: clientY - rect.top };
    }

    canvas.addEventListener('pointerdown', (e) => {
        drawing = true;
        const pos = getPos(e);
        ctx.beginPath();
        ctx.moveTo(pos.x, pos.y);
    });

    canvas.addEventListener('pointermove', (e) => {
        if (!drawing) return;
        e.preventDefault();
        const pos = getPos(e);
        ctx.lineTo(pos.x, pos.y);
        ctx.stroke();
        hasInk = true;
        ttdStatus.textContent = '✓ Tanda tangan terekam';
        ttdStatus.style.color = '#059669';
    });

    window.addEventListener('pointerup', () => { drawing = false; });

    document.getElementById('clear-signature-btn').addEventListener('click', () => {
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        hasInk = false;
        ttdStatus.textContent = 'Belum ada tanda tangan';
        ttdStatus.style.color = '#9ca3af';
    });

    // 3. Export Excel Berita Acara
    async function fetchImageAsBase64(url) {
        try {
            const response = await fetch(url);
            if (!response.ok) return null;
            const blob = await response.blob();
            const ext = blob.type.includes('png') ? 'png' : 'jpeg';
            return await new Promise((resolve) => {
                const reader = new FileReader();
                reader.onloadend = () => resolve({ base64: reader.result, extension: ext });
                reader.onerror = () => resolve(null);
                reader.readAsDataURL(blob);
            });
        } catch (err) {
            return null;
        }
    }

    async function exportBeritaAcaraExcel() {
        const selectedIds = Array.from(document.querySelectorAll('.survey-item-checkbox:checked')).map(cb => Number(cb.value));
        const selectedSurveys = mySurveysData.filter(item => selectedIds.includes(Number(item.id)));

        if (selectedSurveys.length === 0) {
            alert('⚠️ Silakan centang minimal 1 data survei untuk dimasukkan ke Berita Acara.');
            return;
        }
        if (!hasInk) {
            alert('⚠️ Silakan bubuhkan tanda tangan digital Anda terlebih dahulu.');
            return;
        }

        const btnExport = document.getElementById('btn-export-bap');
        const originalText = btnExport.innerHTML;
        btnExport.disabled = true;
        btnExport.innerHTML = '⏳ Menyiapkan File Excel...';

        try {
            const surveyorName = document.getElementById('ba_surveyor_name').value.trim() || 'Surveyor PU';
            const lokasi = document.getElementById('ba_lokasi').value.trim() || 'Wilayah Survei PU';
            const tanggal = new Date().toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });
            const signatureBase64 = canvas.toDataURL('image/png');

            const wb = new ExcelJS.Workbook();
            const ws = wb.addWorksheet('Berita Acara');
            ws.views = [{ showGridLines: false }];

            const colWidths = [5, 18, 25, 22, 13, 13, 13, 28, 22];
            colWidths.forEach((w, i) => { ws.getColumn(i + 1).width = w; });

            const thinBorder = { style: 'thin', color: { argb: 'FF000000' } };
            const allBorder = { top: thinBorder, left: thinBorder, bottom: thinBorder, right: thinBorder };
            const headerFill = { type: 'pattern', pattern: 'solid', fgColor: { argb: 'FFEEEEEE' } };

            let r = 1;
            function mergedText(row, c1, c2, text, opts = {}) {
                ws.mergeCells(row, c1, row, c2);
                const cell = ws.getCell(row, c1);
                cell.value = text;
                cell.font = { name: 'Arial', size: opts.size || 11, bold: !!opts.bold };
                cell.alignment = { horizontal: opts.align || 'left', vertical: 'middle', wrapText: !!opts.wrap };
                return cell;
            }

            mergedText(r, 1, 9, 'KEMENTERIAN PEKERJAAN UMUM', { size: 13, bold: true, align: 'center' }); r++;
            mergedText(r, 1, 9, 'SEKRETARIAT JENDERAL', { size: 12, bold: true, align: 'center' }); r++;
            mergedText(r, 1, 9, 'PUSAT DATA DAN TEKNOLOGI INFORMASI', { size: 11, bold: true, align: 'center' }); r++;
            mergedText(r, 1, 9, 'Jalan Pattimura Nomor 20, Kebayoran Baru, Jakarta 12110, Telepon (021) 7232366', { size: 9, align: 'center' }); r++;
            ws.mergeCells(r, 1, r, 9);
            for (let c = 1; c <= 9; c++) ws.getCell(r, c).border = { bottom: { style: 'medium' } };
            r += 2;

            mergedText(r, 1, 9, 'BERITA ACARA KEGIATAN SURVEI DAN MONITORING INFRASTRUKTUR PU', { size: 12, bold: true, align: 'center' }); r += 2;
            mergedText(r, 1, 9, 'Telah dilaksanakan kegiatan survei lapangan dan akuisisi data koordinat serta dokumentasi infrastruktur Kementerian Pekerjaan Umum pada:', { size: 10, wrap: true });
            ws.getRow(r).height = 30;
            r++;

            mergedText(r, 1, 2, 'Hari/Tanggal', { size: 10 });
            mergedText(r, 3, 9, `: ${tanggal}`, { size: 10 }); r++;
            mergedText(r, 1, 2, 'Lokasi/Wilayah', { size: 10 });
            mergedText(r, 3, 9, `: ${lokasi}`, { size: 10 }); r += 2;

            const headerRow = r;
            const headers = [
                ['No', 1, 1], ['Kategori / Jenis', 2, 2], ['Nama Infrastruktur', 3, 3],
                ['Kota / Provinsi', 4, 4], ['Koordinat', 5, 6],
                ['Status', 7, 7], ['Catatan Kondisi', 8, 8], ['Dokumentasi Foto', 9, 9]
            ];
            headers.forEach(([text, c1, c2]) => {
                if (c1 !== c2) ws.mergeCells(headerRow, c1, headerRow, c2);
                const cell = ws.getCell(headerRow, c1);
                cell.value = text;
                cell.font = { name: 'Arial', size: 10, bold: true };
                cell.alignment = { horizontal: 'center', vertical: 'middle', wrapText: true };
                cell.fill = headerFill;
                cell.border = allBorder;
            });
            r++;
            const subRow = r;
            [1, 2, 3, 4, 7, 8, 9].forEach(c => ws.mergeCells(headerRow, c, subRow, c));
            ['Longitude (X)', 'Latitude (Y)'].forEach((text, i) => {
                const cell = ws.getCell(subRow, 5 + i);
                cell.value = text;
                cell.font = { name: 'Arial', size: 9, bold: true };
                cell.alignment = { horizontal: 'center', vertical: 'middle' };
                cell.fill = headerFill;
                cell.border = allBorder;
            });
            r++;

            for (let idx = 0; idx < selectedSurveys.length; idx++) {
                const d = selectedSurveys[idx];
                const lat = d.latitude !== null ? Number(d.latitude).toFixed(6) : '-';
                const lon = d.longitude !== null ? Number(d.longitude).toFixed(6) : '-';
                const firstPhoto = d.photos && d.photos.length > 0 ? d.photos[0] : null;

                ws.getCell(r, 1).value = idx + 1;
                ws.getCell(r, 2).value = `${d.kategori} - ${d.jenis_infrastruktur || '-'}`;
                ws.getCell(r, 3).value = d.nama_infrastruktur;
                ws.getCell(r, 4).value = `${d.kota_kabupaten}, ${d.provinsi}`;
                ws.getCell(r, 5).value = lon;
                ws.getCell(r, 6).value = lat;
                ws.getCell(r, 7).value = 'Sudah Survei';
                ws.getCell(r, 8).value = d.alasan_verifikasi || '-';
                ws.getCell(r, 9).value = firstPhoto ? '' : 'Tidak ada foto';
                ws.getRow(r).height = firstPhoto ? 90 : 30;

                for (let c = 1; c <= 9; c++) {
                    const cell = ws.getCell(r, c);
                    cell.font = { name: 'Arial', size: 10 };
                    cell.border = allBorder;
                    cell.alignment = [1, 5, 6, 7, 9].includes(c)
                        ? { horizontal: 'center', vertical: 'middle', wrapText: true }
                        : { horizontal: 'left', vertical: 'middle', wrapText: true };
                }

                if (firstPhoto) {
                    const imgData = await fetchImageAsBase64(`/storage/${firstPhoto.file_path}`);
                    if (imgData) {
                        const imageId = wb.addImage({ base64: imgData.base64, extension: imgData.extension });
                        ws.addImage(imageId, { tl: { col: 8.12, row: r - 0.95 }, ext: { width: 115, height: 90 } });
                    }
                }
                r++;
            }

            r += 2;
            mergedText(r, 1, 9, 'Demikian Berita Acara ini dibuat dengan sebenar-benarnya untuk dipergunakan sebagaimana mestinya.', { size: 10 });
            r += 2;

            const sigStartRow = r;
            ws.getRow(sigStartRow).height = 20;
            ws.getRow(sigStartRow + 1).height = 35;
            ws.getRow(sigStartRow + 2).height = 20;

            const sigImageId = wb.addImage({ base64: signatureBase64, extension: 'png' });
            ws.addImage(sigImageId, { tl: { col: 1.6, row: sigStartRow - 1 }, ext: { width: 150, height: 65 } });

            r += 3;
            mergedText(r, 1, 4, `( ${surveyorName} )`, { size: 10, bold: true, align: 'center' });
            mergedText(r, 7, 9, '( _____________________ )', { size: 10, align: 'center' });
            r++;
            mergedText(r, 1, 4, 'Surveyor / Pelaksana Lapangan', { size: 9, align: 'center' });
            mergedText(r, 7, 9, 'Mengetahui / Pejabat Pembuat Komitmen', { size: 9, align: 'center' });

            const buffer = await wb.xlsx.writeBuffer();
            const blob = new Blob([buffer], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' });
            const link = document.createElement('a');
            link.href = URL.createObjectURL(blob);
            link.download = `Berita_Acara_Survei_${surveyorName.replace(/\s+/g, '_')}_${new Date().toISOString().slice(0, 10)}.xlsx`;
            link.click();
        } finally {
            btnExport.disabled = false;
            btnExport.innerHTML = originalText;
        }
    }
</script>
@endpush