<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ongkir sekarang dihitung per jarak (Rp 10.000 per 10 km), bukan per berat,
     * jadi kolom berat_gram tidak diperlukan lagi.
     */
    public function up(): void
    {
        Schema::table('produk', function (Blueprint $table) {
            $table->dropColumn('berat_gram');
        });
    }

    public function down(): void
    {
        Schema::table('produk', function (Blueprint $table) {
            $table->unsignedInteger('berat_gram')->nullable()->after('min_order');
        });
    }
};
