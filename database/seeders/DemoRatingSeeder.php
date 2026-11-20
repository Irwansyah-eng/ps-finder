<?php

namespace Database\Seeders;

use App\Models\Rating;
use App\Models\Rental;
use App\Services\SawService;
use Illuminate\Database\Seeder;

/**
 * HANYA UNTUK UJI COBA: mengisi nilai acak (bukan hasil survei asli).
 * Jalankan: php artisan db:seed --class=DemoRatingSeeder
 * Hapus datanya sebelum presentasi: php artisan migrate:fresh --seed
 */
class DemoRatingSeeder extends Seeder
{
    public function run(): void
    {
        foreach (Rental::all() as $rental) {
            for ($i = 0; $i < 12; $i++) {
                $row = ['rental_id' => $rental->id, 'puas' => rand(2, 5)];
                foreach (array_keys(SawService::CRITERIA) as $k) {
                    $row[$k] = rand(2, 5);
                }
                Rating::create($row);
            }
        }
    }
}
