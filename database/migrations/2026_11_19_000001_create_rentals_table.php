<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('rentals', function (Blueprint $t) {
            $t->id();
            $t->string('nama');
            $t->string('kecamatan');
            $t->string('alamat');
            $t->decimal('lat', 10, 7);
            $t->decimal('lng', 10, 7);
            $t->string('jam');
            $t->unsignedInteger('harga');
            $t->string('wa')->nullable();
            $t->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rentals');
    }
};
