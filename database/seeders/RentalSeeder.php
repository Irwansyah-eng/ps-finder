<?php

namespace Database\Seeders;

use App\Models\Rental;
use Illuminate\Database\Seeder;

class RentalSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['nama' => 'King’s Playstation Samarinda', 'kecamatan' => 'Samarinda Kota', 'alamat' => 'Jl. Dewi Sartika Gg. Sederhana No.46', 'lat' => -0.4958127, 'lng' => 117.1572858, 'jam' => '09.00–23.00', 'harga' => 10000, 'wa' => '085183104646'],
            ['nama' => 'Galactic Gaming Station', 'kecamatan' => 'Samarinda Utara', 'alamat' => 'Jl. PM. Noor No.88, Temindung Permai', 'lat' => -0.460376, 'lng' => 117.1737278, 'jam' => '10.00–02.00', 'harga' => 10000, 'wa' => null],
            ['nama' => 'Ultra Arena ID', 'kecamatan' => 'Samarinda Ilir', 'alamat' => 'Jl. Urip Sumoharjo No.88, Sidomulyo', 'lat' => -0.4896493, 'lng' => 117.162381, 'jam' => '24 jam', 'harga' => 10000, 'wa' => '085179591368'],
            ['nama' => 'Nusantara PS', 'kecamatan' => 'Samarinda Ulu', 'alamat' => 'Jl. H. Suwandi I, Gn. Kelua', 'lat' => -0.465038, 'lng' => 117.1478652, 'jam' => '24 jam', 'harga' => 8000, 'wa' => '081977728999'],
            ['nama' => 'Pavilion Gaming Hub', 'kecamatan' => 'Sungai Pinang', 'alamat' => 'Jl. Kemakmuran No.17, Sungai Pinang Dalam', 'lat' => -0.4780836, 'lng' => 117.16493, 'jam' => '24 jam', 'harga' => 10000, 'wa' => '082254641568'],
        ];
        foreach ($data as $row) {
            Rental::updateOrCreate(['nama' => $row['nama']], $row);
        }
    }
}
