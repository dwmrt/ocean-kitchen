<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pesanan extends Model
{
    // status_bayar
    public const BAYAR_BELUM = 'belum_dibayar';
    public const BAYAR_MENUNGGU = 'menunggu_verifikasi';
    public const BAYAR_LUNAS = 'lunas';
    public const BAYAR_DITOLAK = 'ditolak';

    // status_pesanan
    public const PESANAN_BARU = 'baru';
    public const PESANAN_DIPROSES = 'diproses';
    public const PESANAN_SIAP = 'siap';           // siap diambil atau diantar
    public const PESANAN_SELESAI = 'selesai';
    public const PESANAN_DIBATALKAN = 'dibatalkan';

    // metode_terima
    public const METODE_AMBIL = 'ambil';
    public const METODE_ANTAR = 'antar';

    // metode_bayar
    public const METODE_BAYAR_NON_TUNAI = 'non_tunai';   // QRIS atau transfer bank
    public const METODE_BAYAR_COD = 'cod';               // bayar di tempat

    protected $table = 'pesanan';
    protected $primaryKey = 'id_pesanan';

    protected $fillable = [
        'kode_pesanan', 'id_po', 'id_tarif', 'id_admin',
        'nama_pelanggan', 'no_hp', 'alamat', 'metode_terima', 'jadwal_terima',
        'ongkir', 'total_bayar', 'metode_bayar', 'batas_bayar',
        'jarak_km', 'latitude', 'longitude',
        'status_bayar', 'bukti_bayar', 'alasan_tolak',
        'status_pesanan', 'alasan_batal',
    ];

    protected function casts(): array
    {
        return [
            'jadwal_terima' => 'datetime',
            'batas_bayar' => 'datetime',
            'ongkir' => 'decimal:2',
            'total_bayar' => 'decimal:2',
            'jarak_km' => 'decimal:2',
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
        ];
    }

    public function periode()
    {
        return $this->belongsTo(PeriodePo::class, 'id_po', 'id_po');
    }

    public function tarif()
    {
        return $this->belongsTo(TarifOngkir::class, 'id_tarif', 'id_tarif');
    }

    public function admin()
    {
        return $this->belongsTo(Admin::class, 'id_admin', 'id_admin');
    }

    public function detail()
    {
        return $this->hasMany(DetailPesanan::class, 'id_pesanan', 'id_pesanan');
    }
}
