<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\RequestException;

class NilaiMoodleController extends Controller
{
    /**
     * ============================================
     * CATATAN UNTUK PEMULA:
     * Controller ini mengambil data dari API Moodle
     * (Learning Management System eksternal).
     *
     * URL Moodle & token diambil dari file .env
     * agar mudah diubah tanpa edit kode.
     *
     * Jika Moodle tidak bisa dihubungi, halaman
     * tetap bisa diakses dengan pesan error.
     * ============================================
     */

    /**
     * Mengambil daftar course dari Moodle.
     */
    public function getMoodleCourses()
    {
        // Ambil konfigurasi dari .env
        $moodleUrl = env('MOODLE_URL', 'http://localhost/moodle');
        $token = env('MOODLE_TOKEN', '77e020ae6f8d716e42ab406a4a10861c');

        $apiUrl = rtrim($moodleUrl, '/') . '/webservice/rest/server.php';

        try {
            $client = new Client([
                'verify' => false,
                'timeout' => 5,
                'connect_timeout' => 3,
            ]);

            $response = $client->request('GET', $apiUrl, [
                'query' => [
                    'wstoken' => $token,
                    'moodlewsrestformat' => 'json',
                    'wsfunction' => 'core_course_get_courses',
                ],
            ]);

            $data = json_decode($response->getBody(), true);

            // Filter course (ID >= 2)
            $startId = 2;
            $filteredCourses = array_filter($data, function ($course) use ($startId) {
                return $course['id'] >= $startId;
            });

            return view('pages.akademik.data-nilai-moodle.course-moodle', [
                'courses' => $filteredCourses,
                'title' => 'Data Course Moodle',
                'moodleConnected' => true,
            ]);
        } catch (ConnectException $e) {
            return view('pages.akademik.data-nilai-moodle.course-moodle', [
                'courses' => [],
                'title' => 'Data Course Moodle',
                'moodleConnected' => false,
                'errorMessage' => '⚠️ Tidak dapat terhubung ke server Moodle di ' . $moodleUrl,
            ]);
        } catch (ClientException $e) {
            return view('pages.akademik.data-nilai-moodle.course-moodle', [
                'courses' => [],
                'title' => 'Data Course Moodle',
                'moodleConnected' => false,
                'errorMessage' => '⚠️ Endpoint Moodle tidak ditemukan (404). Periksa kembali URL: ' . $apiUrl,
            ]);
        } catch (RequestException $e) {
            return view('pages.akademik.data-nilai-moodle.course-moodle', [
                'courses' => [],
                'title' => 'Data Course Moodle',
                'moodleConnected' => false,
                'errorMessage' => '⚠️ Error dari Moodle: ' . $e->getMessage(),
            ]);
        }
    }

    /**
     * Mengambil nilai (grade) dari course tertentu.
     */
    public function getGradeItems($courseId, Request $request)
    {
        $moodleUrl = env('MOODLE_URL', 'http://localhost/moodle');
        $token = env('MOODLE_TOKEN', '77e020ae6f8d716e42ab406a4a10861c');

        $apiUrl = rtrim($moodleUrl, '/') . '/webservice/rest/server.php';

        try {
            $client = new Client([
                'verify' => false,
                'timeout' => 5,
                'connect_timeout' => 3,
            ]);

            $response = $client->request('POST', $apiUrl, [
                'form_params' => [
                    'wstoken' => $token,
                    'moodlewsrestformat' => 'json',
                    'wsfunction' => 'gradereport_user_get_grade_items',
                    'courseid' => $courseId,
                ],
            ]);

            $gradeItems = json_decode($response->getBody(), true);

            // Filter berdasarkan pencarian nama
            $searchQuery = $request->input('search');
            if (!empty($searchQuery)) {
                $gradeItems['usergrades'] = array_filter(
                    $gradeItems['usergrades'],
                    function ($grade) use ($searchQuery) {
                        return stripos($grade['userfullname'], $searchQuery) !== false;
                    }
                );
            }

            return view('pages.akademik.data-nilai-moodle.course-moodle-nilai', [
                'gradeItems' => $gradeItems,
                'courseId' => $courseId,
                'title' => 'Detail Nilai',
                'moodleConnected' => true,
            ]);
        } catch (ConnectException $e) {
            return view('pages.akademik.data-nilai-moodle.course-moodle-nilai', [
                'gradeItems' => [],
                'courseId' => $courseId,
                'title' => 'Detail Nilai',
                'moodleConnected' => false,
                'errorMessage' => '⚠️ Tidak dapat terhubung ke server Moodle.',
            ]);
        } catch (ClientException $e) {
            return view('pages.akademik.data-nilai-moodle.course-moodle-nilai', [
                'gradeItems' => [],
                'courseId' => $courseId,
                'title' => 'Detail Nilai',
                'moodleConnected' => false,
                'errorMessage' => '⚠️ Data tidak ditemukan (404). Mungkin courseId salah atau API tidak tersedia.',
            ]);
        } catch (RequestException $e) {
            return view('pages.akademik.data-nilai-moodle.course-moodle-nilai', [
                'gradeItems' => [],
                'courseId' => $courseId,
                'title' => 'Detail Nilai',
                'moodleConnected' => false,
                'errorMessage' => '⚠️ Error: ' . $e->getMessage(),
            ]);
        }
    }
}