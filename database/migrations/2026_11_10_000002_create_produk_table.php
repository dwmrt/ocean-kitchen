<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('produk', function (Blueprint $table) {
            $table->id('id_produk');
            $table->string('nama_produk');
            // Usulan: kolom kategori untuk tab Cakes, Cookies, Brownies, Donuts, Pie
            $table->string('kategori', 50)->default('Lainnya');
            $table->text('deskripsi')->nullable();
            $table->string('foto')->nullable();
            $table->decimal('harga_jual', 12, 2)->default(0);
            // hpp boleh kosong: produk tanpa komposisi tampil "HPP belum bisa dihitung", bukan Rp 0
            $table->decimal('hpp', 12, 2)->nullable();
            // aktif / nonaktif (produk yang pernah dipesan tidak dihapus, cukup dinonaktifkan)
            $table->string('status', 20)->default('aktif');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('produk');
    }
};
