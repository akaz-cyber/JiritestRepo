<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\User;

class LoginTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    use RefreshDatabase;
    protected function setUp(): void
    {
        parent::setUp();

        // Persiapan: Membuat user dummy untuk menguji skenario login gagal (A02)
        User::factory()->create([
            'email' => 'user.asli@example.com',
            'password' => bcrypt('password123'),
        ]);
    }

    /**
     * ID: A01
     * Deskripsi: Form login dikosongkan lalu klik login
     * Harapan: Notifikasi untuk mengisi form yang masih kosong
     */
    public function test_a01_login_dengan_form_kosong()
    {
        // Simulasi user melakukan POST request ke route login tanpa mengisi data
        $response = $this->post('/login', [
            'email' => '',
            'password' => '',
        ]);

        // Ekspektasi: Terdapat error validasi pada kolom email dan password
        $response->assertSessionHasErrors(['email', 'password']);

        // Pastikan user tidak dalam status login
        $this->assertGuest();
    }

    /**
     * ID: A02
     * Deskripsi: Mengisi form login email atau password dengan salah
     * Harapan: Login ditolak dengan pesan username atau password salah
     */
    public function test_a02_login_dengan_data_salah()
    {
        // Simulasi user melakukan POST request dengan kredensial yang salah
        $response = $this->post('/login', [
            'email' => 'email.ngasal@example.com',
            'password' => 'passwordsalah',
        ]);

        // Ekspektasi: Login ditolak. Di Laravel default, pesan error kredensial
        // dikembalikan ke session 'email'
        $response->assertSessionHasErrors('email');

        // Pastikan user gagal masuk
        $this->assertGuest();
    }

    /**
     * ID: A03
     * Deskripsi: Mengisi form login tapi salah satu data tidak diisi (misal password)
     * Harapan: Form memberikan notifikasi bahwa salah satu kolom belum diisi
     */
    public function test_a03_login_dengan_sebagian_data_kosong()
    {
        // Simulasi user mengisi email tapi lupa mengisi password
        $response = $this->post('/login', [
            'email' => 'user.asli@example.com',
            'password' => '', // Sengaja dikosongkan
        ]);

        // Ekspektasi: Terdapat error validasi spesifik pada kolom password
        $response->assertSessionHasErrors('password');

        // Memastikan tidak ada error pada kolom email karena sudah diisi dengan benar
        $response->assertSessionDoesntHaveErrors('email');

        $this->assertGuest();
    }
}
