<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $username = env('SEED_ADMIN_USERNAME', 'admin');
        $password = env('SEED_ADMIN_PASSWORD');

        if ($password === null) {
            if (! app()->environment('local')) {
                throw new \RuntimeException(
                    'Isi SEED_ADMIN_PASSWORD di .env sebelum menjalankan seeder di luar lingkungan lokal.'
                );
            }
            $password = 'admin12345';
        }

        Admin::updateOrCreate(
            ['username' => $username],
            ['nama' => 'Admin Ocean Kitchen', 'password' => $password]
        );
    }
}
