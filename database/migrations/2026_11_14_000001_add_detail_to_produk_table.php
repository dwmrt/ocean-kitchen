<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('produk', function (Blueprint $table) {
            // Satuan jual: Loyang, Toples, Pcs, Botol, dst.
            $table->string('satuan', 20)->default('Pcs')->after('harga_jual');
            // Ukuran atau isi, contoh: "16cm", "250gr", "250ml"
            $table->string('ukuran', 50)->nullable()->after('satuan');
            // Jumlah minimal pesan dalam satuan di atas (donat toping: 6, bomboloni: 3)
            $table->unsignedInteger('min_order')->default(1)->after('ukuran');
            // Teks bebas dari pemilik toko, contoh: "2 Jam"
            $table->string('lama_pengerjaan', 30)->nullable()->after('min_order');
            $table->string('daya_tahan', 60)->nullable()->after('lama_pengerjaan');
        });
    }

    public function down(): void
    {
        Schema::table('produk', function (Blueprint $table) {
            $table->dropColumn(['satuan', 'ukuran', 'min_order', 'lama_pengerjaan', 'daya_tahan']);
        });
    }
};
