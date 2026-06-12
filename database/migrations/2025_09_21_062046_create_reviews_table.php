<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('wisata_id'); // relasi ke tbl__wisata
            $table->unsignedBigInteger('user_id');   // relasi ke users
            $table->tinyInteger('rating');           // 1 - 5 bintang
            $table->text('feedback')->nullable();    // feedback dari user
            $table->string('image')->nullable();     // gambar opsional
            $table->timestamps();

            // Foreign key
            $table->foreign('wisata_id')->references('id__tmpt')->on('tbl__wisata')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
