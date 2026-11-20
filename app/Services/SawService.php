<?php

namespace App\Services;

use App\Models\Rating;
use App\Models\Rental;
use Illuminate\Support\Facades\DB;

/**
 * Simple Additive Weighting (SAW).
 * Semua kriteria diubah ke skala 1-5 (benefit): r = x / max(x), V = sum(w * r) / sum(w).
 * Sumber data: Google Sheets bila SHEET_URL diisi, selain itu tabel ratings di database.
 * Kriteria harga dari Sheets memakai rata-rata harga sewa isian responden (cost):
 * skor = 5 * harga_termurah / harga_rental, jadi yang termurah mendapat 5.
 */
class SawService
{
    public const CRITERIA = [
        'harga' => 'Harga',
        'nyaman' => 'Kenyamanan',
        'konsol' => 'Konsol & stik',
        'game' => 'Koleksi game',
        'fasilitas' => 'Fasilitas',
        'bersih' => 'Kebersihan',
        'layanan' => 'Pelayanan',
        'lokasi' => 'Lokasi',
    ];

    private function fromDatabase(): array
    {
        $select = ['rental_id', DB::raw('COUNT(*) AS n'), DB::raw('AVG(puas) AS puas')];
        foreach (array_keys(self::CRITERIA) as $k) {
            $select[] = DB::raw("AVG($k) AS $k");
        }
        $out = [];
        foreach (Rating::query()->select($select)->groupBy('rental_id')->get() as $a) {
            $x = [];
            foreach (array_keys(self::CRITERIA) as $k) {
                $x[$k] = (float) $a->$k;
            }
            $out[$a->rental_id] = ['n' => (int) $a->n, 'puas' => round((float) $a->puas, 1), 'x' => $x];
        }

        return $out;
    }

    private function fromSheets($rentals): array
    {
        $agg = app(GoogleSheetService::class)->aggregate($rentals);
        $harga = array_filter(array_column($agg, 'harga_rp'), fn ($v) => $v);
        $min = $harga ? min($harga) : null;
        foreach ($agg as $id => $a) {
            $agg[$id]['x']['harga'] = ($min && $a['harga_rp']) ? 5 * $min / $a['harga_rp'] : null;
        }

        return $agg;
    }

    public function rank(array $weights = []): array
    {
        // Bobot 0-5, default 3 (sama rata)
        $w = [];
        foreach (self::CRITERIA as $k => $label) {
            $w[$k] = max(0, min(5, (float) ($weights[$k] ?? 3)));
        }
        $sumW = array_sum($w) ?: 1;

        $rentals = Rental::orderBy('id')->get();
        $agg = GoogleSheetService::url() ? $this->fromSheets($rentals) : $this->fromDatabase();

        $items = $rentals->map(function (Rental $r) use ($agg) {
            $a = $agg[$r->id] ?? null;
            $x = [];
            foreach (array_keys(self::CRITERIA) as $k) {
                $x[$k] = isset($a['x'][$k]) ? round((float) $a['x'][$k], 2) : null;
            }

            return $r->only(['id', 'nama', 'kecamatan', 'alamat', 'lat', 'lng', 'jam', 'harga', 'wa']) + [
                'n' => $a['n'] ?? 0,
                'puas' => $a['puas'] ?? null,
                'x' => $x,
                'v' => null,
            ];
        })->all();

        // Normalisasi dan skor preferensi
        $max = [];
        foreach (array_keys(self::CRITERIA) as $k) {
            $max[$k] = max(array_merge([0], array_map(fn ($i) => $i['x'][$k] ?? 0, $items))) ?: 1;
        }
        foreach ($items as &$i) {
            if ($i['n'] > 0) {
                $s = 0;
                foreach ($w as $k => $wk) {
                    $s += $wk * (($i['x'][$k] ?? 0) / $max[$k]);
                }
                $i['v'] = round($s / $sumW, 3);
            }
        }
        unset($i);

        usort($items, fn ($a, $b) => ($b['v'] ?? -1) <=> ($a['v'] ?? -1));

        return $items;
    }
}
