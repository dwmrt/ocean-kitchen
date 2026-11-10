<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bahan_baku', function (Blueprint $table) {
            $table->id('id_bahan');
            $table->string('nama_bahan');
            $table->string('satuan', 20);
            // harga_bahan = harga untuk sejumlah "jumlah" satuan
            // contoh: jumlah 1000 gram, harga_bahan 14000 => Rp 14 per gram
            $table->decimal('jumlah', 12, 3);
            $table->decimal('harga_bahan', 12, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bahan_baku');
    }
};
