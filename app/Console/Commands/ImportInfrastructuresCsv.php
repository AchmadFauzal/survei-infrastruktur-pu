<?php

namespace App\Console\Commands;

use App\Models\Infrastructure;
use Illuminate\Console\Command;

class ImportInfrastructuresCsv extends Command
{
    protected $signature = 'import:csv {filename}';
    protected $description = 'Import data infrastruktur dari file CSV ke database';

    public function handle()
    {
        $path = storage_path('app/' . $this->argument('filename'));
        if (!file_exists($path)) {
            $this->error("File tidak ditemukan di: $path");
            return;
        }

        $file = fopen($path, 'r');
        $header = fgetcsv($file); // Lewati baris header pertama
        // Sesuaikan pemisah koma (,) atau titik koma (;) jika perlu

        $count = 0;
        while (($row = fgetcsv($file)) !== false) {
            // Sesuaikan indeks kolom [0], [1], dst dengan urutan kolom di CSV Anda
            Infrastructure::create([
                'nama_objek'    => $row[0] ?? 'Tanpa Nama',
                'kategori'      => in_array(strtoupper($row[1] ?? ''), ['BM', 'CK', 'SDA', 'PS']) ? strtoupper($row[1]) : 'SDA',
                'kota'          => $row[2] ?? '-',
                'provinsi'      => $row[3] ?? 'Jawa Timur',
                'latitude'      => (float) str_replace(',', '.', $row[4] ?? 0),
                'longitude'     => (float) str_replace(',', '.', $row[5] ?? 0),
                'status_survei' => 'belum',
            ]);
            $count++;
        }

        fclose($file);
        $this->info("Berhasil mengimpor $count data infrastruktur!");
    }
}