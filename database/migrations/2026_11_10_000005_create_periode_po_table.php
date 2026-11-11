<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('periode_po', function (Blueprint $table) {
            $table->id('id_po');
            $table->string('nama_periode');
            $table->date('tanggal_buka');
            $table->date('tanggal_tutup');
            $table->date('tanggal_terima');
            // aktif / ditutup (status "dibuka" dihitung di server dari tanggal + status)
            $table->string('status', 20)->default('aktif');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('periode_po');
    }
};
