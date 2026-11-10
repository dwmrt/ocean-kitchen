<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BahanBaku extends Model
{
    protected $table = 'bahan_baku';
    protected $primaryKey = 'id_bahan';

    protected $fillable = ['nama_bahan', 'satuan', 'jumlah', 'harga_bahan'];

    protected function casts(): array
    {
        return [
            'jumlah' => 'decimal:3',
            'harga_bahan' => 'decimal:2',
        ];
    }

    /**
     * Harga per 1 satuan = harga_bahan / jumlah.
     * Contoh: 14000 / 1000 gram = Rp 14 per gram.
     */
    public function getHargaPerSatuanAttribute(): float
    {
        return (float) $this->jumlah > 0
            ? (float) $this->harga_bahan / (float) $this->jumlah
            : 0.0;
    }

    public function komposisi()
    {
        return $this->hasMany(KomposisiProduk::class, 'id_bahan', 'id_bahan');
    }

    public function produk()
    {
        return $this->belongsToMany(Produk::class, 'komposisi_produk', 'id_bahan', 'id_produk')
            ->withPivot('id_komposisi', 'jumlah_pakai')
            ->withTimestamps();
    }
}
