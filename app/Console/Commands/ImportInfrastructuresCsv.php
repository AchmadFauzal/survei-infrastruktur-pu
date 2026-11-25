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
            return 1;
        }

        $file = fopen($path, 'r');
        if ($file === false) {
            $this->error("File tidak dapat dibuka: $path");
            return 1;
        }

        $firstLine = fgets($file);
        if ($firstLine === false) {
            fclose($file);
            $this->error("File CSV kosong.");
            return 1;
        }

        $delimiter = substr_count($firstLine, ';') > substr_count($firstLine, ',') ? ';' : ',';
        rewind($file);

        $headers = fgetcsv($file, 0, $delimiter);
        if ($headers === false) {
            fclose($file);
            $this->error("Header CSV tidak ditemukan.");
            return 1;
        }

        $headers[0] = preg_replace('/^\xEF\xBB\xBF/', '', $headers[0]);
        $headers = array_map(
            fn ($header) => strtolower(trim((string) $header)),
            $headers
        );

        $count = 0;
        while (($values = fgetcsv($file, 0, $delimiter)) !== false) {
            if (count($values) !== count($headers)) {
                continue;
            }

            $row = array_change_key_case(array_combine($headers, $values), CASE_LOWER);

            $nama = trim((string) ($row['nama_infrastruktur'] ?? $row['nama'] ?? ''));
            $latitude = $row['latitude'] ?? $row['lat'] ?? null;
            $longitude = $row['longitude'] ?? $row['lon'] ?? $row['lng'] ?? null;

            if ($nama === '' || !is_numeric(str_replace(',', '.', (string) $latitude)) || !is_numeric(str_replace(',', '.', (string) $longitude))) {
                continue;
            }

            $rawKategori = strtoupper(trim((string) ($row['kategori'] ?? $row['unor'] ?? '')));
            $kategori = in_array($rawKategori, ['BM', 'CK', 'SDA', 'PS', 'SETJEN'], true)
                ? $rawKategori
                : 'CK';

            Infrastructure::create([
                'kode_infrastruktur' => trim((string) ($row['kode_infrastruktur'] ?? $row['id'] ?? '')) ?: 'IMP-' . uniqid(),
                'nama_infrastruktur' => $nama,
                'kategori' => $kategori,
                'jenis_infrastruktur' => trim((string) ($row['jenis_infrastruktur'] ?? $row['jenis'] ?? '')),
                'kota_kabupaten' => trim((string) ($row['kota_kabupaten'] ?? $row['kota'] ?? '-')),
                'provinsi' => trim((string) ($row['provinsi'] ?? '-')),
                'latitude' => (float) str_replace(',', '.', (string) $latitude),
                'longitude' => (float) str_replace(',', '.', (string) $longitude),
                'status_survey' => $this->parseBoolean($row['status_survey'] ?? $row['status_survei'] ?? false),
                'status_lapor_big' => $this->parseBoolean($row['status_lapor_big'] ?? false),
                'is_coordinates_close' => $this->parseNullableBoolean($row['is_coordinates_close'] ?? null),
                'yolo_detection' => $this->parseNullableBoolean($row['yolo_detection'] ?? null),
                'best_score' => is_numeric($row['best_score'] ?? null) ? (float) str_replace(',', '.', (string) $row['best_score']) : null,
            ]);
            $count++;
        }

        fclose($file);
        $this->info("Berhasil mengimpor $count data infrastruktur!");
        return 0;
    }

    private function parseBoolean(mixed $value): bool
    {
        if (is_bool($value)) {
            return $value;
        }

        $normalized = strtolower(trim((string) $value));
        return in_array($normalized, ['1', 'true', 'ya', 'y', 'yes', 'sudah'], true);
    }

    private function parseNullableBoolean(mixed $value): ?bool
    {
        if ($value === null || $value === '') {
            return null;
        }

        return $this->parseBoolean($value);
    }
}