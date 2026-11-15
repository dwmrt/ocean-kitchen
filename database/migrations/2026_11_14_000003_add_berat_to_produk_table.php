<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('produk', function (Blueprint $table) {
            // Berat per 1 satuan jual, dalam gram (sudah termasuk kemasan).
            // Dipakai menghitung ongkir: Rp 10.000 per kg.
            // Boleh kosong: produk tanpa berat tidak boleh dipesan dengan metode antar.
            $table->unsignedInteger('berat_gram')->nullable()->after('min_order');
        });
    }

    public function down(): void
    {
        Schema::table('produk', function (Blueprint $table) {
            $table->dropColumn('berat_gram');
        });
    }
};
