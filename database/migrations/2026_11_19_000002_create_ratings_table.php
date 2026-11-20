<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('ratings', function (Blueprint $t) {
            $t->id();
            $t->foreignId('rental_id')->constrained()->cascadeOnDelete();
            $t->string('kecamatan')->nullable();
            foreach (['harga', 'nyaman', 'konsol', 'game', 'fasilitas', 'bersih', 'layanan', 'lokasi', 'puas'] as $kolom) {
                $t->unsignedTinyInteger($kolom);
            }
            $t->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ratings');
    }
};
