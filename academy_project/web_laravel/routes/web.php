<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AbsensiController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EditPasswordController;
use App\Http\Controllers\GuruController;
use App\Http\Controllers\InputNilaiController;
use App\Http\Controllers\InventarisController;
use App\Http\Controllers\JadwalController;
use App\Http\Controllers\JadwalMengajarController;
use App\Http\Controllers\KalenderAkademikController;
use App\Http\Controllers\KelasController;
use App\Http\Controllers\KerjaSamaController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\MapelController;
use App\Http\Controllers\NilaiMoodleController;
use App\Http\Controllers\PegawaiController;
use App\Http\Controllers\PeminjamanBarangController;
use App\Http\Controllers\PeminjamanController;
use App\Http\Controllers\PengumumanController;
use App\Http\Controllers\RaportController;
use App\Http\Controllers\RuangController;
use App\Http\Controllers\SiswaController;
use App\Http\Controllers\StudentDataController;
use App\Http\Controllers\TamuController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserMoodleApiController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AIController;
use App\Http\Controllers\Api\AuthController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->get('/user', [AuthController::class, 'user']);

// -------- HALAMAN AWAL --------
Route::get('/', function () {
    if (Auth::check()) return redirect('/dashboard');
    return redirect('/login');
});

// ==========================================
// ✅ ROUTE API PUBLIK (TANPA MIDDLEWARE)
// ==========================================
Route::get('/api/get-username-by-role/{role}', [TamuController::class, 'getUsernamesByRole']);

// -------- GUEST (BELUM LOGIN) --------
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'login'])->name('login');
    Route::post('/login', [LoginController::class, 'authenticating']);
    Route::get('/daftar-tamu', [TamuController::class, 'daftar'])->name('daftar-tamu');
    Route::post('/kirim-tamu', [TamuController::class, 'store'])->name('kirim-tamu');
});

// -------- SEMUA PENGGUNA YANG SUDAH LOGIN --------
Route::middleware('auth')->group(function () {
    // Logout
    Route::get('/logout', function () {
        Auth::logout();
        return redirect('/')->with('toast_success', 'Anda telah berhasil logout.');
    })->name('logout');

    // Set role
    Route::post('/login/setRole', [LoginController::class, 'setRole'])->name('set_role');

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // ==========================================
    // AI CLUSTERING (ADMIN & GURU)
    // ==========================================
    Route::middleware('userRole:admin,guru')->group(function () {
        Route::post('/ai/analyze', [AIController::class, 'analyzeClusters'])->name('ai.analyze');
        Route::post('/ai/predict', [AIController::class, 'predictSingle'])->name('ai.predict');
    });

    // ==========================================
    // DATASET STUDENTS (ADMIN & GURU)
    // ==========================================
    Route::middleware('userRole:admin,guru')->group(function () {
        Route::get('/dataset-students', [StudentDataController::class, 'index'])->name('dataset.index');
        Route::get('/dataset-students/export-csv', [StudentDataController::class, 'exportCsv'])->name('dataset.export.csv');
    });

    // Terima / hapus pesan tamu
    Route::post('/dashboard/{id}/terima', [DashboardController::class, 'terimaPesan'])->name('dashboard.terimaPesan');
    Route::delete('/dashboard/{id}/hapus', [DashboardController::class, 'hapusPesan'])->name('dashboard.hapusPesan');

    // Ubah password
    Route::get('/option/change-password', [EditPasswordController::class, 'index']);
    Route::post('/option/change-password/{id}', [EditPasswordController::class, 'ubah'])->name('option.change-password');

    // ==========================================
    // AKADEMIK UNTUK GURU & ADMIN
    // ==========================================
    Route::middleware('userRole:admin,guru')->group(function () {
        Route::get('/akademik/jadwal-guru', [JadwalMengajarController::class, 'jadwalguru'])->name('guru.jadwal');
        Route::get('/akademik/jadwal/cetak_pdf/{id_guru}', [JadwalMengajarController::class, 'cetakjadwalguru']);

        // Input nilai
        Route::get('/data-inputnilai/{guruId}', [InputNilaiController::class, 'index']);
        Route::get('/data-nilai-atur/{kelasId}/{mapelId}/{smt}', [InputNilaiController::class, 'atur']);
        Route::get('/data-input-nilai/{kelasId}/{siswaId}/{mapelId}/{smt}', [InputNilaiController::class, 'input']);
        Route::post('/data-input-nilai-siswa/{kelasId}/{siswaId}/{mapelId}/{smt}', [InputNilaiController::class, 'store']);
        Route::get('/data-detail-nilai/{kelasId}/{siswaId}/{mapelId}/{smt}', [InputNilaiController::class, 'detail']);

        // Raport
        Route::get('/data-raport', [RaportController::class, 'index']);
        Route::get('/data-raport-input/{id}/{smt}', [RaportController::class, 'input']);
        Route::post('/tambahnilai', [RaportController::class, 'tambahnilai']);
        Route::get('/data-nilai-hapus/{id}', [RaportController::class, 'destroy']);
        Route::post('/data-raport-insert', [RaportController::class, 'store']);
        Route::get('/data-cetak-raport/{smt}/{id}', [RaportController::class, 'cetak']);

        // Jadwal siswa (admin/guru lihat jadwal siswa)
        Route::get('/data-jadwal/{id}', [JadwalController::class, 'jadwalsiswa']);
        Route::get('/data-jadwalsiswa/cetak_pdf/{id}', [JadwalController::class, 'cetakjadwalsiswa']);

        // Raport siswa
        Route::get('/data-raport-cetak-siswa/{id}/{smt}', [RaportController::class, 'cetakraportsiswa']);

        // Pegawai, guru, jadwal (read-only)
        Route::get('/data-pegawai-lihat', [PegawaiController::class, 'lihat']);
        Route::get('/data-guru-lihat', [GuruController::class, 'lihat']);
        Route::get('/data-jadwalmengajar-guru', [JadwalMengajarController::class, 'lihat']);
        Route::get('/data-jadwalmengajar-cek/{id}', [JadwalMengajarController::class, 'cekjadwal']);
        Route::get('/data-jadwal-cek', [JadwalController::class, 'lihat']);
        Route::get('/data-jadwal-cekjadwal/{id}', [JadwalController::class, 'cekjadwal']);

        // Moodle
        Route::get('/data-nilai-moodle/course-moodle', [NilaiMoodleController::class, 'getMoodleCourses']);
        Route::get('/data-nilai-moodle/course-moodle/nilai-course/{courseId}', [NilaiMoodleController::class, 'getGradeItems'])->name('nilai-course');
        Route::get('/get-grade-items/{courseId}/{search?}', [NilaiMoodleController::class, 'getGradeItems'])->name('get.grade.items');
        Route::get('/testing-api', fn() => view('testing-api'))->name('testing-api');
    });

    // ==========================================
    // ADMIN & WAKASEK (Manajemen Master, Inventaris, Peminjaman, dll)
    // ==========================================
    Route::middleware('userRole:admin,wakasek')->group(function () {
        // Pengumuman
        Route::get('/dashboard/buat-pengumuman', [PengumumanController::class, 'create'])->name('buat-pengumuman');
        Route::post('/dashboard/buat-pengumuman', [PengumumanController::class, 'store']);
        Route::get('/dashboard/hapus-pengumuman/{id}', [PengumumanController::class, 'destroy']);
        Route::put('/dashboard/update-pengumuman/{id}', [PengumumanController::class, 'update'])->name('update-pengumuman');

        // Manajemen User
        Route::get('/administrasi/users', [UserController::class, 'index'])->name('user_management');
        Route::patch('/administrasi/users/{id}', [UserController::class, 'update']);
        Route::put('/administrasi/users/reset/{id}', [UserController::class, 'reset']);
        Route::delete('/administrasi/users/delete/{id}', [UserController::class, 'destroy'])->name('users.destroy');
        Route::get('/administrasi/user/export', [UserController::class, 'export']);
        Route::get('/administrasi/users/import', [UserController::class, 'showImportForm'])->name('users.import.form');
        Route::post('/administrasi/users/import', [UserController::class, 'Import'])->name('users.import');

        // User Moodle
        Route::get('/administrasi/usermoodle', [UserMoodleApiController::class, 'fetchApi']);

        // Barang
        Route::get('/sarana/barang', [BarangController::class, 'index'])->name('barang_main');
        Route::get('/sarana/barang-tambah', [BarangController::class, 'create'])->name('tambah-barang');
        Route::post('/sarana/barang-tambah', [BarangController::class, 'store']);
        Route::get('/sarana/barang-update/{id}', [BarangController::class, 'edit'])->name('update-barang');
        Route::put('/sarana/barang-update/{id}', [BarangController::class, 'update']);
        Route::delete('/sarana/barang-hapus/{id}', [BarangController::class, 'destroy'])->name('hapus-barang');

        // Ruang
        Route::get('/sarana/ruang', [RuangController::class, 'index'])->name('ruang_main');
        Route::post('/sarana/ruang-tambah', [RuangController::class, 'store'])->name('tambah-ruang');
        Route::put('/sarana/ruang-update', [RuangController::class, 'update'])->name('update-ruang');
        Route::delete('/sarana/ruang-hapus/{id}', [RuangController::class, 'destroy'])->name('hapus-ruang');

        // Inventaris
        Route::get('/sarana/inventaris', [InventarisController::class, 'index'])->name('inventaris_main');
        Route::get('atur-barang/{id}', [InventarisController::class, 'aturBarang'])->name('atur-barang');
        Route::post('/store-inventaris/{id}', [InventarisController::class, 'store'])->name('store-inventaris');
        Route::delete('/delete-inventaris/{id}', [InventarisController::class, 'destroy'])->name('delete-inventaris');
        Route::get('/search-barang', [InventarisController::class, 'search'])->name('search-barang');
        Route::get('/get-barang-detail-by-name', [InventarisController::class, 'getDetailByName'])->name('get-barang-detail-by-name');
        Route::get('/get-all-barang', [InventarisController::class, 'getAllBarang'])->name('get-all-barang');

        // Kalender akademik
        Route::get('/akademik/kalender/index', [KalenderAkademikController::class, 'index'])->name('calendar.index');
        Route::post('/akademik/kalender', [KalenderAkademikController::class, 'store'])->name('calendar.store');
        Route::patch('/akademik/kalender/update/{id}', [KalenderAkademikController::class, 'update'])->name('calendar.update');
        Route::delete('/akademik/kalender/destroy/{id}', [KalenderAkademikController::class, 'destroy'])->name('calendar.destroy');

        // Guru (CRUD dengan soft delete / hard delete tergantung method)
        Route::get('/administrasi/guru', [GuruController::class, 'index']);
        Route::get('/administrasi/guru-tambah', [GuruController::class, 'create']);
        Route::post('/administrasi/guru-tambah', [GuruController::class, 'store']);
        Route::get('/administrasi/guru-update/{id}', [GuruController::class, 'edit']);
        Route::put('/administrasi/guru-update/{id}', [GuruController::class, 'update']);
        Route::delete('/administrasi/guru-hapus/{id}', [GuruController::class, 'destroy']);
        Route::get('/userguru/export', [GuruController::class, 'export']);

        // Siswa (CRUD + keluar + hard delete)
        Route::get('/administrasi/siswa', [SiswaController::class, 'index'])->name('siswa_main');
        Route::get('/administrasi/siswa-tambah', [SiswaController::class, 'create']);
        Route::post('/administrasi/siswa-tambah', [SiswaController::class, 'store']);
        Route::get('/administrasi/siswa-update/{id}', [SiswaController::class, 'edit'])->name('siswa_edit');
        Route::put('/administrasi/siswa-update/{id}', [SiswaController::class, 'update']);
        Route::put('/administrasi/siswa-keluar/{id}', [SiswaController::class, 'out']);
        Route::delete('/administrasi/siswa-hapus/{id}', [SiswaController::class, 'destroy'])->name('siswa_hapus');
        Route::get('/usersiswa/export', [SiswaController::class, 'export']);

        // Pegawai
        Route::get('/data-pegawai', [PegawaiController::class, 'index']);
        Route::get('/data-pegawai-add', [PegawaiController::class, 'create']);
        Route::post('/data-pegawai-insert', [PegawaiController::class, 'store']);
        Route::get('/data-pegawai-edit/{id}', [PegawaiController::class, 'edit']);
        Route::put('/data-pegawai-update/{id}', [PegawaiController::class, 'update']);
        Route::delete('/data-pegawai-hapus/{id}', [PegawaiController::class, 'destroy']);

        // Mapel
        Route::get('/akademik/mapel', [MapelController::class, 'index'])->name('mapel_main');
        Route::post('/akademik/mapel-tambah', [MapelController::class, 'store']);
        Route::put('/akademik/mapel-update/{id}', [MapelController::class, 'update']);
        Route::delete('/akademik/mapel-hapus/{id}', [MapelController::class, 'destroy']);

        // Kelas
        Route::get('/sarana/kelas', [KelasController::class, 'index'])->name('kelas_main');
        Route::post('/sarana/kelas-tambah', [KelasController::class, 'store'])->name('tambah_kelas');
        Route::put('/sarana/kelas-update/{id}', [KelasController::class, 'update'])->name('update_kelas');
        Route::delete('/sarana/kelas-hapus/{id}', [KelasController::class, 'destroy'])->name('hapus_kelas');

        // Jadwal Pelajaran
        Route::get('/akademik/jadwal', [JadwalController::class, 'showJadwalAdmin'])->name('jadwal.admin');
        Route::get('/akademik/jadwal-kelas/{kelas}', [JadwalController::class, 'jadwalKelas'])->name('jadwal.kelas');
        Route::post('/akademik/jadwal-kelas/{kelas}', [JadwalController::class, 'jadwalKelas']);
        Route::post('/akademik/jadwal-tambah', [JadwalController::class, 'store'])->name('jadwal.store');
        Route::put('/akademik/jadwal-update', [JadwalController::class, 'update'])->name('jadwal.update');
        Route::delete('/akademik/jadwal-hapus/{kelasId}/{hari}/{index}', [JadwalController::class, 'destroy'])->name('jadwal.destroy');
        Route::get('/akademik/jadwal/cetak_pdf/{id}', [JadwalController::class, 'cetak'])->name('jadwal.cetak');

        // Jadwal Mengajar Guru
        Route::get('/data-jadwalmengajar', [JadwalMengajarController::class, 'index'])->name('jadwal-mengajar.index');
        Route::get('/data-jadwalmengajar-atur/{id}', [JadwalMengajarController::class, 'atur'])->name('jadwal-mengajar.atur');
        Route::post('/data-jadwalmengajar-insert', [JadwalMengajarController::class, 'insert'])->name('jadwal-mengajar.insert');
        Route::put('/data-jadwalmengajar-update/{guruId}/{hari}/{index}', [JadwalMengajarController::class, 'update'])->name('jadwal-mengajar.update');
        Route::delete('/data-jadwalmengajar-hapus/{guruId}/{hari}/{index}', [JadwalMengajarController::class, 'destroy'])->name('jadwal-mengajar.destroy');
        Route::get('/data-jadwalmengajar/cetak_pdf/{id}', [JadwalMengajarController::class, 'cetak'])->name('jadwal-mengajar.cetak');
        Route::get('/data-jadwalmengajar-cek/{id}', [JadwalMengajarController::class, 'cek'])->name('jadwal-mengajar.cek');

        // Raport Admin
        Route::get('/data-raport-admin', [RaportController::class, 'index']);
        Route::get('/akademik/raport-angkatan/{angkatan}', [RaportController::class, 'showRaportAngkatan']);
        Route::get('/akademik/raport-cetak/{id}/{smt}', [RaportController::class, 'cetakraport']);
        Route::post('/akademik/raport-update/{id_siswa}', [RaportController::class, 'update_nilai_raport']);

        // Absensi Admin
        Route::get('/akademik/absensi/admin', [AbsensiController::class, 'showAbsensiAdmin']);
        Route::get('/akademik/absensi/{akademik}/{kelas}', [AbsensiController::class, 'showKelasAbsensi']);
        Route::post('/akademik/absensi/{akademik}/{kelas}', [AbsensiController::class, 'showKelasAbsensi']);
        Route::post('/api/akademik/absensi-update/{userId}/{absensiIndex}', [AbsensiController::class, 'apiUpdateAbsensi'])->name('api.update-absensi');

        // Peminjaman Ruang
        Route::get('/data-peminjaman', [PeminjamanController::class, 'index']);
        Route::get('/data-peminjaman-history', [PeminjamanController::class, 'history']);
        Route::delete('/peminjaman-hapus/{id}', [PeminjamanController::class, 'destroy']);
        Route::post('/peminjaman-tambah', [PeminjamanController::class, 'store']);
        Route::put('/peminjaman-update/{ruangId}/{peminjamanId}', [PeminjamanController::class, 'update']);
        Route::get('/peminjaman-confirm/{ruangId}/{peminjamanId}', [PeminjamanController::class, 'confirm']);
        Route::get('/peminjaman-approve/{id}', [PeminjamanController::class, 'approve']);
        Route::get('/peminjaman-decline/{id}', [PeminjamanController::class, 'decline']);
        Route::post('/peminjaman-tambah', [PeminjamanController::class, 'store']);
Route::put('/peminjaman-update/{ruangId}/{peminjamanId}', [PeminjamanController::class, 'update']);
Route::delete('/peminjaman-hapus/{ruangId}/{peminjamanId}', [PeminjamanController::class, 'destroy']);
Route::get('/peminjaman-approve/{id}', [PeminjamanController::class, 'approve']);
Route::get('/peminjaman-decline/{id}', [PeminjamanController::class, 'decline']);
Route::get('/peminjaman-complete/{ruangId}/{peminjamanId}', [PeminjamanController::class, 'complete']);

        // Peminjaman Barang
//         Route::get('/data-peminjaman-barang', [PeminjamanBarangController::class, 'index'])->name('peminjamanBarang.index');
//         Route::post('/data-peminjaman-barang', [PeminjamanBarangController::class, 'store'])->name('peminjamanBarang.store');
//         Route::put('/data-peminjaman-barang', [PeminjamanBarangController::class, 'update'])->name('peminjamanBarang.update');
//         Route::delete('/data-peminjaman-barang-hapus/{id}', [PeminjamanBarangController::class, 'destroy'])->name('peminjamanBarang.destroy');
//         Route::get('/data-peminjaman-barang-history', [PeminjamanBarangController::class, 'history']);
//         Route::get('/data-peminjaman-barang-confirm/{id}', [PeminjamanBarangController::class, 'confirm']);
//         Route::get('/peminjaman-barang-approve/{id}', [PeminjamanBarangController::class, 'approve'])->name('peminjamanBarang.approve');
// Route::get('/peminjaman-barang-decline/{id}', [PeminjamanBarangController::class, 'decline'])->name('peminjamanBarang.decline');
// Route::get('/peminjaman-barang-confirm/{id}', [PeminjamanBarangController::class, 'confirm'])->name('peminjamanBarang.confirm');
// Route::put('/data-peminjaman-barang-update/{id}', [PeminjamanBarangController::class, 'update'])->name('peminjamanBarang.update');

// Peminjaman Barang
Route::prefix('data-peminjaman-barang')->group(function () {
    Route::get('/', [PeminjamanBarangController::class, 'index'])->name('peminjamanBarang.index');
    Route::post('/', [PeminjamanBarangController::class, 'store'])->name('peminjamanBarang.store');
    Route::put('/update/{id}', [PeminjamanBarangController::class, 'update'])->name('peminjamanBarang.update');
    Route::delete('/hapus/{id}', [PeminjamanBarangController::class, 'destroy'])->name('peminjamanBarang.destroy');
    Route::get('/approve/{id}', [PeminjamanBarangController::class, 'approve'])->name('peminjamanBarang.approve');
    Route::get('/decline/{id}', [PeminjamanBarangController::class, 'decline'])->name('peminjamanBarang.decline');
    Route::get('/confirm/{id}', [PeminjamanBarangController::class, 'confirm'])->name('peminjamanBarang.confirm');
});

        // Tamu
        Route::get('/data-tamu', [TamuController::class, 'index']);
        Route::get('/tamu', [TamuController::class, 'create']);
        Route::post('/tamu', [TamuController::class, 'kirim']);
        Route::get('/tamu-edit/{id}', [TamuController::class, 'edit']);
        Route::put('/tamu-edit/{id}', [TamuController::class, 'update']);
        Route::delete('/tamu-delete/{id}', [TamuController::class, 'delete']);
        Route::post('/tamu-update-status/{id}', [TamuController::class, 'updateStatus'])->name('tamu.updateStatus');

        // Kerjasama MoU
        Route::get('/mou', [KerjaSamaController::class, 'lihat']);
        Route::get('/add-mou', [KerjaSamaController::class, 'create']);
        Route::post('/add-mou', [KerjaSamaController::class, 'store']);
        Route::get('/edit-mou/{id}', [KerjaSamaController::class, 'edit']);
        Route::put('/edit-mou/{id}', [KerjaSamaController::class, 'update']);
        Route::delete('/delete-mou/{id}', [KerjaSamaController::class, 'destroy']);

        // API absensi admin
        Route::get('/api/events-from-database', [AbsensiController::class, 'getEventsFromDatabase']);
        Route::delete('/api/delete-absensi/{userId}/{absensiIndex}', [AbsensiController::class, 'deleteAbsensi']);
        Route::put('/api/update-absensi/{userId}/{absensiIndex}', [AbsensiController::class, 'updateAbsensi']);
        Route::get('/get_kelas', [KelasController::class, 'getKelas']);
        Route::get('/get_siswa', [SiswaController::class, 'getSiswaKelasAbsensi']);
        Route::get('/get_guru', [GuruController::class, 'getGuru']);
        Route::get('/get_gurunames', [GuruController::class, 'getGuruNames']);
        Route::get('/get_siswaadmin', [SiswaController::class, 'getSiswaByKelas']);
        Route::post('/akademik/absensi/postAbsensi', [AbsensiController::class, 'storeAdmin'])->name('absensi.storeAdmin');
        Route::get('/absensi-file/{filename}', function ($filename) {
            $path = storage_path('app/public/absensi_files/' . $filename);
            if (!file_exists($path)) {
                abort(404, 'File tidak ditemukan');
            }
            return response()->file($path);
        })->name('absensi.file');
    });

    // ==========================================
    // SISWA (presensi)
    // ==========================================
    Route::middleware('userRole:siswa')->group(function () {
        Route::get('/akademik/absensi/siswa', [AbsensiController::class, 'showAbsensiSiswa'])->name('absensi.showAbsensiSiswa');
        Route::put('/api/update-absensi-siswa/{userId}/{absensiIndex}', [SiswaController::class, 'updateAbsensi']);
    });

    // ==========================================
    // GURU (presensi)
    // ==========================================
    Route::middleware('userRole:guru')->group(function () {
        Route::get('/akademik/absensi/guru', [AbsensiController::class, 'showAbsensiGuru'])->name('absensi.showAbsensiGuru');
        Route::put('/api/update-absensi-guru/{userId}/{absensiIndex}', [GuruController::class, 'updateAbsensi']);
    });

    // ==========================================
    // SISWA & GURU (presensi, jadwal, raport)
    // ==========================================
    Route::middleware('userRole:siswa,guru')->group(function () {
        Route::post('/akademik/absensi/siswaguruPostAbsensi', [AbsensiController::class, 'store'])->name('absensi.store');
        Route::post('/absensi/checkAndFillAbsentData', [AbsensiController::class, 'checkAndFillAbsentData'])->name('absensi.checkAndFillAbsentData');
    });

    Route::middleware('userRole:siswa,admin')->group(function () {
        Route::get('/akademik/jadwal-siswa/{id}', [JadwalController::class, 'jadwalsiswa']);
    });

    Route::middleware('userRole:siswa,admin,guru')->group(function () {
        Route::get('/akademik/raport-siswa/{jenis_raport}', [RaportController::class, 'show_raport']);
        Route::get('/akademik/jadwal-siswa', [JadwalController::class, 'showJadwalSiswa']);
        Route::get('/akademik/raport/{jenis_nilai}/{siswa}', [RaportController::class, 'show']);
    });

    // ==========================================
    // API UMUM
    // ==========================================
    Route::middleware('userRole:siswa,guru,admin')->group(function () {
        Route::get('/api/absensi/{userId}', [AbsensiController::class, 'getAbsensiById']);
        Route::get('/api/siswa-by-user/{userId}', [SiswaController::class, 'getSiswaByUser']);
        Route::get('/api/guru-by-user/{userId}', [GuruController::class, 'getGuruByUser']);
    });
});