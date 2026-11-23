<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pesanan', function (Blueprint $table) {
            // non_tunai = QRIS / transfer bank (ada unggah bukti), cod = bayar di tempat
            $table->string('metode_bayar', 20)->default('non_tunai')->after('total_bayar');
            // Batas bayar non-tunai (24 jam setelah pesanan dibuat). Kosong untuk COD.
            $table->dateTime('batas_bayar')->nullable()->after('metode_bayar');

            // Disiapkan untuk ongkir otomatis lewat titik peta (boleh kosong).
            $table->decimal('jarak_km', 6, 2)->nullable()->after('batas_bayar');
            $table->decimal('latitude', 10, 7)->nullable()->after('jarak_km');
            $table->decimal('longitude', 10, 7)->nullable()->after('latitude');

            $table->index('batas_bayar');
        });
    }

    public function down(): void
    {
        Schema::table('pesanan', function (Blueprint $table) {
            $table->dropIndex(['batas_bayar']);
            $table->dropColumn(['metode_bayar', 'batas_bayar', 'jarak_km', 'latitude', 'longitude']);
        });
    }
};
