<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Produk extends Model
{
    public const STATUS_AKTIF = 'aktif';
    public const STATUS_NONAKTIF = 'nonaktif';

    protected $table = 'produk';
    protected $primaryKey = 'id_produk';

    protected $fillable = [
        'nama_produk', 'kategori', 'deskripsi', 'foto',
        'harga_jual', 'hpp', 'status',
    ];

    protected function casts(): array
    {
        return [
            'harga_jual' => 'decimal:2',
            'hpp' => 'decimal:2',
        ];
    }

    public function scopeAktif($query)
    {
        return $query->where('status', self::STATUS_AKTIF);
    }

    public function komposisi()
    {
        return $this->hasMany(KomposisiProduk::class, 'id_produk', 'id_produk');
    }

    public function bahanBaku()
    {
        return $this->belongsToMany(BahanBaku::class, 'komposisi_produk', 'id_produk', 'id_bahan')
            ->withPivot('id_komposisi', 'jumlah_pakai')
            ->withTimestamps();
    }

    public function detailPesanan()
    {
        return $this->hasMany(DetailPesanan::class, 'id_produk', 'id_produk');
    }
}
