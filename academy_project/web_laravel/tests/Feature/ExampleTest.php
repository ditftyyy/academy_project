<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * Test halaman login publik.
     */
    public function test_login_page(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
    }

    /**
     * Test bahwa /dashboard mengarahkan ke /login jika belum autentikasi.
     */
    public function test_dashboard_redirects_to_login_when_unauthenticated(): void
    {
        $response = $this->get('/dashboard');
        $response->assertRedirect('/login');
    }

    /**
     * Test API get_kelas (tanpa auth) harus mengarahkan ke login.
     */
    public function test_get_kelas_api(): void
    {
        $response = $this->get('/get_kelas');
        $response->assertRedirect('/login');
    }

    /**
     * Test API get_guru (tanpa auth) harus mengarahkan ke login.
     */
    public function test_get_guru_api(): void
    {
        $response = $this->get('/get_guru');
        $response->assertRedirect('/login');
    }

    /**
     * Test halaman daftar-tamu (publik).
     */
    public function test_daftar_tamu_page(): void
    {
        $response = $this->get('/daftar-tamu');
        $response->assertStatus(200);
    }

    /**
     * Test bahwa route admin (e.g., /administrasi/guru) mengarahkan ke login jika belum auth.
     */
    public function test_admin_route_redirects_to_login(): void
    {
        $response = $this->get('/administrasi/guru');
        $response->assertRedirect('/login');
    }
}