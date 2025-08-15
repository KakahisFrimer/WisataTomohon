<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tbl__events', function (Blueprint $table) {
            $table->increments('id__event'); // Primary key auto increment INT
            $table->integer('wisata_id'); // INT biasa, bukan unsigned
            $table->string('nama_event');
            $table->date('tanggal_event');
            $table->timestamps();

            // Foreign key ke tbl__wisata
            $table->foreign('wisata_id')
                  ->references('id__tmpt')
                  ->on('tbl__wisata')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tbl__events');
    }
};
