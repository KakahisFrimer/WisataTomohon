<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Ubah semua data lama "Aktif" jadi "Beroperasi"
        DB::table('tbl__wisata')
            ->where('status', 'Aktif')
            ->update(['status' => 'Beroperasi']);
    }

    public function down(): void
    {
        // Kembalikan ke "Aktif" kalau rollback
        DB::table('tbl__wisata')
            ->where('status', 'Beroperasi')
            ->update(['status' => 'Aktif']);
    }
};
