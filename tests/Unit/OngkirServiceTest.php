<?php

namespace Tests\Unit;

use App\Services\OngkirService;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class OngkirServiceTest extends TestCase
{
    public static function kasusJarak(): array
    {
        return [
            'sangat dekat (1 km)' => [1, 10000],
            '5 km' => [5, 10000],
            '10 km' => [10, 10000],
            '11 km' => [11, 10000],
            '14 km' => [14, 10000],
            '14,9 km' => [14.9, 10000],
            '15 km' => [15, 20000],
            '20 km' => [20, 20000],
            '24 km' => [24, 20000],
            '24,9 km' => [24.9, 20000],
            '25 km' => [25, 30000],
            '34 km' => [34, 30000],
            '35 km' => [35, 40000],
        ];
    }

    #[DataProvider('kasusJarak')]
    public function test_ongkir_sesuai_aturan_penjual(float $jarakKm, int $diharapkan): void
    {
        $this->assertSame($diharapkan, (new OngkirService())->hitung($jarakKm));
    }

    public function test_jarak_negatif_ditolak(): void
    {
        $this->expectException(InvalidArgumentException::class);

        (new OngkirService())->hitung(-1);
    }
}
