<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('komposisi_produk', function (Blueprint $table) {
            $table->id('id_komposisi');
            $table->foreignId('id_produk')->constrained('produk', 'id_produk')->cascadeOnDelete();
            // bahan yang sudah dipakai di komposisi tidak boleh dihapus
            $table->foreignId('id_bahan')->constrained('bahan_baku', 'id_bahan')->restrictOnDelete();
            // takaran per 1 unit jual
            $table->decimal('jumlah_pakai', 12, 3);
            $table->timestamps();

            $table->unique(['id_produk', 'id_bahan']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('komposisi_produk');
    }
};
