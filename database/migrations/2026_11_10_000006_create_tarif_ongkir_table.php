<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tarif_ongkir', function (Blueprint $table) {
            $table->id('id_tarif');
            $table->string('wilayah');
            $table->decimal('biaya', 12, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tarif_ongkir');
    }
};
