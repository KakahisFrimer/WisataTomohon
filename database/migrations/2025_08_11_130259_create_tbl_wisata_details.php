<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tbl__wisata_details', function (Blueprint $table) {
            $table->id();

            // Sesuaikan tipe data dengan id__tmpt di tbl__wisata (int signed)
            $table->integer('wisata_id');

            $table->foreign('wisata_id')->references('id__tmpt')->on('tbl__wisata')->onDelete('cascade');

            $table->string('hari__buka');
            $table->time('jam__buka');
            $table->time('jam__tutup');
            $table->decimal('harga__tiket', 20);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tbl__wisata_details');
    }
};
