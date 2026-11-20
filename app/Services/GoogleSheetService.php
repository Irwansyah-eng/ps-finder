<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

/**
 * Membaca jawaban Google Form dari CSV Google Sheets (Publikasikan ke web > CSV).
 * Kolom dikenali dari kata kunci pada judul pertanyaan (huruf kecil).
 */
class GoogleSheetService
{
    private const COLS = [
        'rental' => ['pernah kamu kunjungi'],
        'harga' => ['harga'],
        'kualitas' => ['kualitas playstation'],
        'stik' => ['stik'],
        'game' => ['variasi'],
        'nyaman' => ['kenyamanan tempat'],
        'fasilitas' => ['fasilitas'],
        'bersih' => ['kebersihan'],
        'layanan' => ['pelayanan'],
        'lokasi' => ['akses'],
    ];

    public static function url(): ?string
    {
        return config('services.sheet_url') ?: (env('SHEET_URL') ?: null);
    }

    private function csv(): ?string
    {
        $url = self::url();
        if (! $url) {
            return null;
        }

        return Cache::remember('sheet_csv', 60, function () use ($url) {
            foreach ([true, false] as $verify) {   // percobaan kedua tanpa verifikasi SSL (masalah umum di Windows)
                try {
                    $res = Http::timeout(10)->withOptions(['verify' => $verify])->get($url);
                    if ($res->ok() && str_contains($res->body(), ',')) {
                        Cache::forever('sheet_csv_last', $res->body());

                        return $res->body();
                    }
                } catch (\Throwable $e) {
                    // coba lagi / pakai salinan terakhir
                }
            }

            return Cache::get('sheet_csv_last');
        });
    }

    private function parse(string $body): array
    {
        $fh = fopen('php://memory', 'r+');
        fwrite($fh, $body);
        rewind($fh);
        $head = fgetcsv($fh);
        if (! $head) {
            return [];
        }
        $head = array_map(fn ($h) => mb_strtolower(trim((string) $h)), $head);

        $idx = [];
        foreach (self::COLS as $k => $keys) {
            $idx[$k] = null;
            foreach ($head as $i => $h) {
                foreach ($keys as $kw) {
                    if (str_contains($h, $kw)) {
                        $idx[$k] = $i;
                        break 2;
                    }
                }
            }
        }
        if ($idx['rental'] === null) {
            return [];
        }

        $rows = [];
        while (($r = fgetcsv($fh)) !== false) {
            $nama = mb_strtolower(trim($r[$idx['rental']] ?? ''));
            if ($nama === '') {
                continue;
            }
            $row = ['rental' => $nama];
            foreach (self::COLS as $k => $_) {
                if ($k === 'rental') {
                    continue;
                }
                $v = $idx[$k] !== null ? trim($r[$idx[$k]] ?? '') : '';
                if ($k === 'harga') {
                    $v = preg_replace('/\D/', '', $v);   // "Rp8.000" -> 8000
                } else {
                    $v = str_replace(',', '.', $v);
                }
                $row[$k] = is_numeric($v) ? (float) $v : null;
            }
            $rows[] = $row;
        }
        fclose($fh);

        return $rows;
    }

    private function cocok(string $teks, string $kunci): bool
    {
        $alias = ['pavilion' => ['pavilion', 'paillion', 'pailion', 'pavillion']];
        foreach ($alias[$kunci] ?? [$kunci] as $a) {
            if (str_contains($teks, $a)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param  iterable<\App\Models\Rental>  $rentals
     * @return array<int, array{n:int, puas:null, harga_rp:?float, x:array<string,?float>}>
     */
    public function aggregate(iterable $rentals): array
    {
        $csv = $this->csv();
        $rows = $csv ? $this->parse($csv) : [];
        $out = [];

        foreach ($rentals as $r) {
            preg_match('/[a-z]+/i', $r->nama, $m);   // kata pertama nama rental: King, Galactic, Ultra, ...
            $kunci = strtolower($m[0] ?? $r->nama);
            $mine = array_values(array_filter($rows, fn ($x) => $this->cocok($x['rental'], $kunci)));
            if (! $mine) {
                continue;
            }

            $avg = function (string ...$cols) use ($mine) {
                $per = [];
                foreach ($mine as $x) {
                    $s = array_values(array_filter(array_map(fn ($c) => $x[$c], $cols), fn ($z) => $z !== null));
                    if ($s) {
                        $per[] = array_sum($s) / count($s);
                    }
                }

                return $per ? array_sum($per) / count($per) : null;
            };

            $out[$r->id] = [
                'n' => count($mine),
                'puas' => null,
                'harga_rp' => $avg('harga'),
                'x' => [
                    'nyaman' => $avg('nyaman'),
                    'konsol' => $avg('kualitas', 'stik'),
                    'game' => $avg('game'),
                    'fasilitas' => $avg('fasilitas'),
                    'bersih' => $avg('bersih'),
                    'layanan' => $avg('layanan'),
                    'lokasi' => $avg('lokasi'),
                ],
            ];
        }

        return $out;
    }
}
