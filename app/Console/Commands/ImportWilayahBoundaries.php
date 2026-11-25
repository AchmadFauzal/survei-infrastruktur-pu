<?php

namespace App\Console\Commands;

use App\Models\WilayahBoundary;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class ImportWilayahBoundaries extends Command
{
    protected $signature = 'wilayah:import {file?}';
    protected $description = 'Download & parse SQL wilayah_boundaries (cahyadsn) menjadi GeoJSON ke database';

    public function handle()
    {
        $fileArg = $this->argument('file');

        // Jika argumen file lokal diberikan dan ada, parse langsung dari file lokal
        if ($fileArg) {
            $path = base_path($fileArg);
            if (!file_exists($path)) {
                $this->error("File lokal tidak ditemukan: {$path}");
                return 1;
            }
            $sqlContent = file_get_contents($path);
            $count = $this->parseAndSaveSql($sqlContent);
            $this->info("Selesai! Total {$count} batas wilayah berhasil disimpan.");
            return 0;
        }

        // Jika tanpa argumen file, ambil daftar file SQL langsung dari GitHub cahyadsn/wilayah_boundaries
        $this->info('Mengambil daftar file batas wilayah dari GitHub cahyadsn/wilayah_boundaries...');

        $treeRes = Http::withHeaders(['User-Agent' => 'Laravel-WebGIS-PU'])
            ->get('https://api.github.com/repos/cahyadsn/wilayah_boundaries/git/trees/main?recursive=1');

        if (!$treeRes->successful()) {
            $this->error('Gagal terhubung ke GitHub API. Status: ' . $treeRes->status());
            return 1;
        }

        $tree = $treeRes->json('tree', []);

        // Ambil file SQL level Provinsi & Kabupaten/Kota (berada di folder db/prov/ dan db/kab/)
        $sqlFiles = collect($tree)->filter(function ($item) {
            return $item['type'] === 'blob'
                && str_ends_with($item['path'], '.sql')
                && (str_contains($item['path'], 'prov') || str_contains($item['path'], 'kab'));
        })->pluck('path');

        if ($sqlFiles->isEmpty()) {
            // Fallback: ambil semua file .sql yang ukurannya wajar (< 15MB)
            $sqlFiles = collect($tree)->filter(function ($item) {
                return $item['type'] === 'blob' && str_ends_with($item['path'], '.sql');
            })->pluck('path');
        }

        $totalImported = 0;

        foreach ($sqlFiles as $repoPath) {
            $this->line("Mengunduh & memproses: {$repoPath} ...");
            $rawUrl = "https://raw.githubusercontent.com/cahyadsn/wilayah_boundaries/main/{$repoPath}";
            $res = Http::timeout(60)->get($rawUrl);

            if ($res->successful()) {
                $saved = $this->parseAndSaveSql($res->body());
                $totalImported += $saved;
                $this->info("  ✓ +{$saved} wilayah disimpan (Total: {$totalImported})");
            }
        }

        $this->info("Selesai! Total {$totalImported} batas wilayah Provinsi & Kab/Kota berhasil disimpan ke database.");
        return 0;
    }

    private function parseAndSaveSql(string $sqlContent): int
    {
        $count = 0;
        $lines = preg_split("/\r\n|\n|\r/", $sqlContent);

        foreach ($lines as $line) {
            if (preg_match("/\('([0-9\.]+)',\s*'([^']+)',\s*([\-0-9\.]+|NULL),\s*([\-0-9\.]+|NULL),\s*'(\[.*\])'\)/", $line, $m)) {
                $kode = $m[1];
                $nama = str_replace("''", "'", $m[2]);
                $lat  = $m[3] === 'NULL' ? null : (float) $m[3];
                $lng  = $m[4] === 'NULL' ? null : (float) $m[4];
                $rawCoords = json_decode($m[5], true);

                if (!$rawCoords) continue;

                // Level 1 = Provinsi (tanpa titik), Level 2 = Kab/Kota (1 titik)
                $level = substr_count($kode, '.') + 1;
                if ($level > 2) continue;

                $geojson = $this->convertToGeoJson($rawCoords);

                WilayahBoundary::updateOrCreate(
                    ['kode' => $kode],
                    [
                        'nama'    => $nama,
                        'level'   => $level,
                        'lat'     => $lat,
                        'lng'     => $lng,
                        'geojson' => $geojson,
                    ]
                );

                $count++;
            }
        }

        return $count;
    }

    private function convertToGeoJson(array $coords): array
    {
        $depth = $this->arrayDepth($coords);

        if ($depth === 3) {
            return [
                'type'        => 'Polygon',
                'coordinates' => $this->swapLatLngRecursive($coords, 3),
            ];
        }

        return [
            'type'        => 'MultiPolygon',
            'coordinates' => $this->swapLatLngRecursive($coords, 4),
        ];
    }

    private function swapLatLngRecursive(array $arr, int $depth): array
    {
        if ($depth === 1) {
            return [(float) $arr[1], (float) $arr[0]];
        }
        return array_map(fn($item) => $this->swapLatLngRecursive($item, $depth - 1), $arr);
    }

    private function arrayDepth(array $array): int
    {
        $maxDepth = 1;
        foreach ($array as $value) {
            if (is_array($value)) {
                $depth = $this->arrayDepth($value) + 1;
                if ($depth > $maxDepth) $maxDepth = $depth;
            }
        }
        return $maxDepth;
    }
}