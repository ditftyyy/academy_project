<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;

class UserMoodleApiController extends Controller
{
    /**
     * ============================================
     * CATATAN UNTUK PEMULA:
     * Controller ini mengambil data user dari
     * API Moodle (LMS eksternal).
     * 
     * URL dan token diambil dari file .env
     * agar mudah diubah tanpa edit kode.
     * ============================================
     */

    /**
     * Fetch user data dari API Moodle
     * 
     * CARA KERJA:
     * 1. Ambil URL & token dari .env
     * 2. Cek apakah Moodle bisa dijangkau
     * 3. Kirim request POST ke API Moodle
     * 4. Tampilkan data di view
     * 5. Jika gagal, tampilkan pesan error
     */
    public function fetchApi(Request $request)
    {
        // ============================================
        // 1. AMBIL KONFIGURASI DARI .env
        // ============================================
        $moodleUrl = env('MOODLE_URL', 'http://localhost/moodle');
        $token = env('MOODLE_TOKEN', '77e020ae6f8d716e42ab406a4a10861c');
        
        // Buat URL API lengkap
        $apiUrl = rtrim($moodleUrl, '/') . '/webservice/rest/server.php';
        
        // Parameter API
        $params = [
            'wstoken' => $token,
            'moodlewsrestformat' => 'json',
            'wsfunction' => 'core_user_get_users',
            'criteria' => [
                [
                    'key' => '',
                    'value' => '',
                ],
            ],
        ];

        try {
            // ============================================
            // 2. BUAT HTTP CLIENT DENGAN TIMEOUT
            // ============================================
            $client = new Client([
                'verify' => false,      // Nonaktifkan SSL (development only)
                'timeout' => 5,         // Timeout 5 detik
                'connect_timeout' => 3, // Timeout koneksi 3 detik
            ]);

            // ============================================
            // 3. KIRIM REQUEST KE MOODLE
            // ============================================
            $response = $client->post($apiUrl, [
                'form_params' => $params,
            ]);

            // ============================================
            // 4. PARSE RESPONSE
            // ============================================
            $data = json_decode($response->getBody(), true);

            // ============================================
            // 5. TAMPILKAN KE VIEW
            // ============================================
            return view('pages.administrasi.data-user-moodle.index', [
                'data' => $data,
                'title' => 'Data User Moodle',
                'moodleConnected' => true,
            ]);

        } catch (ConnectException $e) {
            // ============================================
            // 6. JIKA TIDAK BISA KONEK (MOODLE MATI)
            // ============================================
            return view('pages.administrasi.data-user-moodle.index', [
                'data' => null,
                'title' => 'Data User Moodle',
                'moodleConnected' => false,
                'errorMessage' => '⚠️ Tidak dapat terhubung ke server Moodle. Pastikan Moodle sudah berjalan di ' . $moodleUrl,
            ]);

        } catch (RequestException $e) {
            // ============================================
            // 7. JIKA ADA ERROR LAIN (TOKEN SALAH, DLL)
            // ============================================
            return view('pages.administrasi.data-user-moodle.index', [
                'data' => null,
                'title' => 'Data User Moodle',
                'moodleConnected' => false,
                'errorMessage' => '⚠️ Error dari Moodle: ' . $e->getMessage(),
            ]);
        }
    }
}