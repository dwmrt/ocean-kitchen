<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KomposisiProduk extends Model
{
    protected $table = 'komposisi_produk';
    protected $primaryKey = 'id_komposisi';

    protected $fillable = ['id_produk', 'id_bahan', 'jumlah_pakai'];

    protected function casts(): array
    {
        return [
            'jumlah_pakai' => 'decimal:3',
        ];
    }

    public function produk()
    {
        return $this->belongsTo(Produk::class, 'id_produk', 'id_produk');
    }

    public function bahan()
    {
        return $this->belongsTo(BahanBaku::class, 'id_bahan', 'id_bahan');
    }
}
