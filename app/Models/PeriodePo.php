<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PeriodePo extends Model
{
    public const STATUS_AKTIF = 'aktif';
    public const STATUS_DITUTUP = 'ditutup';

    protected $table = 'periode_po';
    protected $primaryKey = 'id_po';

    protected $fillable = [
        'nama_periode', 'tanggal_buka', 'tanggal_tutup', 'tanggal_terima', 'status',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_buka' => 'date',
            'tanggal_tutup' => 'date',
            'tanggal_terima' => 'date',
        ];
    }

    public function scopeAktif($query)
    {
        return $query->where('status', self::STATUS_AKTIF);
    }

    /**
     * Periode yang sedang dibuka: status aktif dan hari ini berada di antara
     * tanggal buka dan tanggal tutup (tanggal tutup ikut dihitung sampai akhir hari).
     */
    public function scopeDibuka($query)
    {
        $hariIni = today()->toDateString();

        return $query->aktif()
            ->whereDate('tanggal_buka', '<=', $hariIni)
            ->whereDate('tanggal_tutup', '>=', $hariIni);
    }

    /** Dihitung di server, bukan disimpan. */
    public function getDibukaAttribute(): bool
    {
        $hariIni = today();

        return $this->status === self::STATUS_AKTIF
            && $hariIni->gte($this->tanggal_buka)
            && $hariIni->lte($this->tanggal_tutup);
    }

    /**
     * Periode yang ditampilkan di banner: yang sedang dibuka,
     * kalau tidak ada maka periode aktif terdekat yang akan datang.
     */
    public static function terpilih(): ?self
    {
        return static::dibuka()->orderBy('tanggal_buka')->first()
            ?? static::aktif()
                ->whereDate('tanggal_buka', '>', today()->toDateString())
                ->orderBy('tanggal_buka')
                ->first();
    }

    public function pesanan()
    {
        return $this->hasMany(Pesanan::class, 'id_po', 'id_po');
    }
}
