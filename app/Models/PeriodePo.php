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

    public function pesanan()
    {
        return $this->hasMany(Pesanan::class, 'id_po', 'id_po');
    }
}
