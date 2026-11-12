<?php

namespace Tests\Feature;

use App\Models\Admin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminLoginTest extends TestCase
{
    use RefreshDatabase;

    private function buatAdmin(): Admin
    {
        return Admin::create([
            'nama' => 'Admin Tes',
            'username' => 'admintes',
            'password' => 'rahasia123',
        ]);
    }

    public function test_halaman_admin_mengalihkan_tamu_ke_login(): void
    {
        $this->get('/admin')->assertRedirect('/admin/login');
    }

    public function test_halaman_login_bisa_dibuka(): void
    {
        $this->get('/admin/login')->assertOk();
    }

    public function test_admin_bisa_login_dengan_data_benar(): void
    {
        $this->buatAdmin();

        $this->post('/admin/login', [
            'username' => 'admintes',
            'password' => 'rahasia123',
        ])->assertRedirect('/admin');

        $this->assertAuthenticated('admin');
    }

    public function test_login_gagal_dengan_password_salah(): void
    {
        $this->buatAdmin();

        $this->from('/admin/login')->post('/admin/login', [
            'username' => 'admintes',
            'password' => 'salah',
        ])->assertRedirect('/admin/login')
          ->assertSessionHasErrors('username');

        $this->assertGuest('admin');
    }

    public function test_admin_bisa_logout(): void
    {
        $admin = $this->buatAdmin();

        $this->actingAs($admin, 'admin')
            ->post('/admin/logout')
            ->assertRedirect('/admin/login');

        $this->assertGuest('admin');
    }
}
