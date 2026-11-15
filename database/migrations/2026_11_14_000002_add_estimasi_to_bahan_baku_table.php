<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bahan_baku', function (Blueprint $table) {
            // true = harga atau ukuran kemasan masih perkiraan, belum dikonfirmasi pemilik.
            // Dipakai HPP untuk menampilkan peringatan "HPP masih perkiraan".
            $table->boolean('harga_estimasi')->default(false)->after('harga_bahan');
        });
    }

    public function down(): void
    {
        Schema::table('bahan_baku', function (Blueprint $table) {
            $table->dropColumn('harga_estimasi');
        });
    }
};
