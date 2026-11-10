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

    protected $table = 'pesanan';
    protected $primaryKey = 'id_pesanan';

    protected $fillable = [
        'kode_pesanan', 'id_po', 'id_tarif', 'id_admin',
        'nama_pelanggan', 'no_hp', 'alamat', 'metode_terima', 'jadwal_terima',
        'ongkir', 'total_bayar',
        'status_bayar', 'bukti_bayar', 'alasan_tolak',
        'status_pesanan', 'alasan_batal',
    ];

    protected function casts(): array
    {
        return [
            'jadwal_terima' => 'datetime',
            'ongkir' => 'decimal:2',
            'total_bayar' => 'decimal:2',
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
