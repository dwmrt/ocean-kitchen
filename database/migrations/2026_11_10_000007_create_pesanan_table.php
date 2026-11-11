<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pesanan', function (Blueprint $table) {
            $table->id('id_pesanan');
            // Usulan: kode unik agar pelanggan (tanpa login) bisa cek status
            $table->string('kode_pesanan', 30)->unique();

            $table->foreignId('id_po')->constrained('periode_po', 'id_po')->restrictOnDelete();
            // null kalau ambil di toko (ongkir 0)
            $table->foreignId('id_tarif')->nullable()->constrained('tarif_ongkir', 'id_tarif')->nullOnDelete();
            // Usulan: boleh kosong, diisi saat admin pertama kali memverifikasi / mengubah status
            $table->foreignId('id_admin')->nullable()->constrained('admin', 'id_admin')->nullOnDelete();

            $table->string('nama_pelanggan');
            $table->string('no_hp', 20);
            $table->text('alamat')->nullable();           // kosong kalau ambil di toko
            $table->string('metode_terima', 10);          // ambil / antar
            $table->dateTime('jadwal_terima');

            $table->decimal('ongkir', 12, 2)->default(0);
            $table->decimal('total_bayar', 12, 2);

            // belum_dibayar, menunggu_verifikasi, lunas, ditolak
            $table->string('status_bayar', 30)->default('belum_dibayar');
            $table->string('bukti_bayar')->nullable();
            $table->text('alasan_tolak')->nullable();

            // baru, diproses, siap, selesai, dibatalkan
            $table->string('status_pesanan', 30)->default('baru');
            $table->text('alasan_batal')->nullable();

            $table->timestamps();

            $table->index('status_bayar');
            $table->index('status_pesanan');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pesanan');
    }
};
