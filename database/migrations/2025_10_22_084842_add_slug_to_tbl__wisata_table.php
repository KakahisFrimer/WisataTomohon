<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::table('tbl__wisata', function (Blueprint $table) {
        // jangan pakai unique dulu
        $table->string('slug')->nullable()->after('name');
    });
}

public function down()
{
    Schema::table('tbl__wisata', function (Blueprint $table) {
        $table->dropColumn('slug');
    });
}

};
