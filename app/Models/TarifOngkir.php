<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TarifOngkir extends Model
{
    protected $table = 'tarif_ongkir';
    protected $primaryKey = 'id_tarif';

    protected $fillable = ['wilayah', 'biaya'];

    protected function casts(): array
    {
        return [
            'biaya' => 'decimal:2',
        ];
    }

    public function pesanan()
    {
        return $this->hasMany(Pesanan::class, 'id_tarif', 'id_tarif');
    }
}
