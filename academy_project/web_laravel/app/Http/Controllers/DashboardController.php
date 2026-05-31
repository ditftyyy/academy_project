<?php

namespace App\Http\Controllers;

use App\Models\MongoDB\User as MongoUser;
use App\Models\MongoDB\Kelas as MongoKelas;
use App\Models\MongoDB\Akademik as MongoAkademik;
use App\Models\MongoDB\Pengumuman as MongoPengumuman;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Halaman dashboard utama
     * Semua logika akses data (jadwal, absensi) dipindahkan ke model User
     * agar controller tetap bersih dan tidak ada json_decode().
     */
    public function index()
    {
        if (!Auth::check()) {
            return redirect('/login');
        }

        $user = Auth::user();
        $role = $user->current_role ?? $user->role;
        $datas = [];

        // ========== 1. KALENDER AKADEMIK ==========
        $events = [];
        $akademikAktif = MongoAkademik::aktif()->first();
        if ($akademikAktif && isset($akademikAktif->kalender)) {
            foreach ($akademikAktif->kalender as $k) {
                $color = ($k['status'] ?? '') === 'masuk' ? '#924ACE' : '#68B01A';
                $events[] = [
                    'id'     => $k['id'] ?? uniqid(),
                    'title'  => $k['title'] ?? '',
                    'start'  => $k['start_date'] ?? '',
                    'end'    => $k['end_date'] ?? '',
                    'status' => $k['status'] ?? 'masuk',
                    'color'  => $color,
                ];
            }
        }
        $datas['events'] = $events;

        // ========== 2. DATA PER ROLE ==========
        if (in_array($role, ['admin', 'kepsek', 'root'])) {
            // --- DASHBOARD ADMIN ---
            $datas['total_user']  = MongoUser::where('deleted', false)->where('role', '!=', 'root')->count();
            $datas['total_guru']  = MongoUser::where('role', 'guru')->where('deleted', false)->count();
            $datas['total_siswa'] = MongoUser::where('role', 'siswa')->where('deleted', false)->count();
            $datas['total_kelas'] = MongoKelas::where('deleted', false)->count();

            // Pastikan tahun ajaran aktif ada
            $currentMonth = date('m');
            $semester = $currentMonth >= '07' ? 'ganjil' : 'genap';
            $currentYear = now()->year;
            $akademikAktif = MongoAkademik::where('tahun_ajaran', 'like', $currentYear . '%')
                                          ->where('semester', $semester)
                                          ->first();
            if (!$akademikAktif) {
                MongoAkademik::query()->update(['selected' => false]);
                MongoAkademik::create([
                    'tahun_ajaran' => $currentYear . '/' . ($currentYear + 1),
                    'semester'     => $semester,
                    'selected'     => true,
                    'kalender'     => [],
                    'konfigurasi'  => [],
                ]);
            }
        } 
        elseif ($role === 'guru') {
            // --- DASHBOARD GURU ---
            $datas['guru_data'] = [
                'nama'       => $user->nama_lengkap,
                'nip'        => $user->guru_data['nip'] ?? '-',
                'kelas_wali' => $user->guru_data['kelas_wali']['nama'] ?? 'Belum ditentukan',
            ];
            $datas['jadwal_hari_ini'] = $user->getJadwalHariIni();   // method aman dari model
            $datas['rekap_absensi']   = $user->rekap_absensi;        // accessor dari model
        } 
        elseif ($role === 'siswa') {
            // --- DASHBOARD SISWA ---
            $datas['siswa_data'] = [
                'nama'  => $user->nama_lengkap,
                'nis'   => $user->siswa_data['nis'] ?? '-',
                'kelas' => $user->siswa_data['kelas']['nama'] ?? '-',
            ];
            $datas['jadwal_hari_ini'] = $user->getJadwalHariIni();
            $datas['rekap_absensi']   = $user->rekap_absensi;
            $datas['nilai_terbaru']   = $user->rata_rata_nilai;
        }

        // ========== 3. PENGUMUMAN ==========
        if (in_array($role, ['admin', 'kepsek', 'root'])) {
            $pengumumans = MongoPengumuman::byType('pengumuman')
                                          ->orderBy('created_at', 'desc')
                                          ->get();
        } else {
            $pengumumans = MongoPengumuman::where('type', 'pengumuman')
                                          ->where(function ($q) use ($role) {
                                              $q->where('role', $role)->orWhere('role', 'semua');
                                          })
                                          ->orderBy('created_at', 'desc')
                                          ->get();
        }
        $datas['pengumumans'] = $pengumumans;
        $datas['rolePengumuman'] = config('app.DB_user_roles', ['admin', 'guru', 'siswa']);

        // ========== 4. PESAN TAMU ==========
        $tamuPesans = MongoPengumuman::byType('tamu')
                                     ->where('data_tambahan.tujuan_username', $user->username)
                                     ->where('data_tambahan.status', 'menunggu')
                                     ->orderByDesc('created_at')
                                     ->get();
        $datas['tamu_pesans'] = $tamuPesans;

        return view('pages.dashboard.dashboard', $datas)->with('title', 'Dashboard');
    }

    /**
     * Terima pesan tamu
     */
    public function terimaPesan($id)
    {
        $pesan = MongoPengumuman::findOrFail($id);
        $dataTambahan = $pesan->data_tambahan ?? [];
        $dataTambahan['status'] = 'pesan_telah_diterima';
        $pesan->data_tambahan = $dataTambahan;
        $pesan->save();
        return redirect()->back()->with('success', 'Pesan diterima.');
    }

    /**
     * Hapus/selesai pesan tamu
     */
    public function hapusPesan($id)
    {
        $pesan = MongoPengumuman::findOrFail($id);
        $dataTambahan = $pesan->data_tambahan ?? [];
        $dataTambahan['status'] = 'pesan_telah_selesai';
        $pesan->data_tambahan = $dataTambahan;
        $pesan->save();
        return redirect()->back()->with('success', 'Pesan dihapus.');
    }
}