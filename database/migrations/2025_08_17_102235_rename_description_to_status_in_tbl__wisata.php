<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('tbl__wisata', function (Blueprint $table) {
            $table->renameColumn('description', 'status');
        });
    }

    public function down(): void
    {
        Schema::table('tbl__wisata', function (Blueprint $table) {
            $table->renameColumn('status', 'description');
        });
    }
};
