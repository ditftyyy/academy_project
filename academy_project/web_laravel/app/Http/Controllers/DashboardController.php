<?php

namespace App\Http\Controllers;

use App\Models\MongoDB\User as MongoUser;
use App\Models\MongoDB\Kelas as MongoKelas;
use App\Models\MongoDB\Akademik as MongoAkademik;
use App\Models\MongoDB\Pengumuman as MongoPengumuman;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        if (!Auth::check()) return redirect('/login');

        $user  = Auth::user();
        $role  = $user->current_role ?? $user->role;
        $datas = [];

        // Kalender akademik
        $events        = [];
        $akademikAktif = MongoAkademik::aktif()->first();
        if ($akademikAktif && isset($akademikAktif->kalender)) {
            foreach ($akademikAktif->kalender as $k) {
                $color    = ($k['status'] ?? '') === 'masuk' ? '#924ACE' : '#68B01A';
                $events[] = [
                    'id'     => $k['id']         ?? uniqid(),
                    'title'  => $k['title']      ?? '',
                    'start'  => $k['start_date'] ?? '',
                    'end'    => $k['end_date']   ?? '',
                    'status' => $k['status']     ?? 'masuk',
                    'color'  => $color,
                ];
            }
        }
        $datas['events'] = $events;

        if (in_array($role, ['admin', 'kepsek', 'root'])) {
            $datas['total_user']  = MongoUser::where('deleted', false)->where('role', '!=', 'root')->count();
            $datas['total_guru']  = MongoUser::where('role', 'guru')->where('deleted', false)->count();
            $datas['total_siswa'] = MongoUser::where('role', 'siswa')->where('deleted', false)->count();
            $datas['total_kelas'] = MongoKelas::where('deleted', false)->count();

            $currentMonth  = date('m');
            $semester      = $currentMonth >= '07' ? 'ganjil' : 'genap';
            $currentYear   = now()->year;
            $akademikAktif = MongoAkademik::where('tahun_ajaran', 'like', $currentYear . '%')
                                          ->where('semester', $semester)->first();
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

        } elseif ($role === 'guru') {
            // Wali kelas
            $kelasWali = 'Bukan wali kelas';
            $kelasList = MongoKelas::kelasAktif()->get();
            foreach ($kelasList as $kelas) {
                if (isset($kelas->wali_kelas['id']) && (string) $kelas->wali_kelas['id'] === (string) $user->_id) {
                    $kelasWali = $kelas->nama_kelas;
                    break;
                }
            }

            $datas['guru_data'] = [
                'nama'       => $user->nama_lengkap,
                'nip'        => $user->guru_data['nip'] ?? '-',
                'kelas_wali' => $kelasWali,
            ];

            // Jadwal hari ini: baca dari kelas->jadwal, filter guru_id
            $userId        = (string) $user->_id;
            $guruNamaLogin = trim($user->guru_data['nama'] ?? $user->nama_lengkap ?? '');
            $hariIni       = strtolower(Carbon::now()->locale('id')->dayName);
            $jadwalHariIni = [];

            foreach ($kelasList as $kelas) {
                $jadwalKelas = $kelas->jadwal;
                if (empty($jadwalKelas) || !is_array($jadwalKelas)) continue;

                foreach ($jadwalKelas as $hariEntry) {
                    if (is_object($hariEntry)) $hariEntry = (array) $hariEntry;
                    if (strtolower(trim($hariEntry['hari'] ?? '')) !== $hariIni) continue;
                    if (($hariEntry['status'] ?? 'masuk') === 'libur') continue;

                    foreach ($hariEntry['mata_pelajaran'] ?? [] as $mp) {
                        if (is_object($mp)) $mp = (array) $mp;
                        $guruIdDB   = (string) ($mp['guru_id'] ?? '');
                        $guruNamaDB = trim((string) ($mp['guru'] ?? ''));
                        $cocok = ($guruIdDB !== '' && $guruIdDB === $userId)
                            || (!empty($guruNamaDB) && strcasecmp($guruNamaDB, $guruNamaLogin) === 0);
                        if (!$cocok) continue;
                        $jadwalHariIni[] = [
                            'jam_mulai'   => $mp['jam_mulai']   ?? '',
                            'jam_selesai' => $mp['jam_selesai'] ?? '',
                            'mapel'       => $mp['mapel']       ?? '-',
                            'kelas'       => $kelas->nama_kelas ?? '-',
                            'ruang'       => $mp['ruang']       ?? '-',
                        ];
                    }
                }
            }
            usort($jadwalHariIni, fn($a, $b) => strcmp($a['jam_mulai'], $b['jam_mulai']));

            $datas['jadwal_hari_ini'] = $jadwalHariIni;
            $datas['rekap_absensi']   = $user->rekap_absensi;

        } elseif ($role === 'siswa') {
            $datas['siswa_data'] = [
                'nama'  => $user->nama_lengkap,
                'nis'   => $user->siswa_data['nis']           ?? '-',
                'kelas' => $user->siswa_data['kelas']['nama'] ?? '-',
            ];
            $datas['jadwal_hari_ini'] = $user->getJadwalHariIni();
            $datas['rekap_absensi']   = $user->rekap_absensi;
            $datas['nilai_terbaru']   = $user->rata_rata_nilai;
        }

        // Pengumuman
        if (in_array($role, ['admin', 'kepsek', 'root'])) {
            $pengumumans = MongoPengumuman::byType('pengumuman')->orderBy('created_at', 'desc')->get();
        } else {
            $pengumumans = MongoPengumuman::where('type', 'pengumuman')
                ->where(function ($q) use ($role) {
                    $q->where('role', $role)->orWhere('role', 'semua');
                })->orderBy('created_at', 'desc')->get();
        }
        $datas['pengumumans']    = $pengumumans;
        $datas['rolePengumuman'] = config('app.DB_user_roles', ['admin', 'guru', 'siswa']);

        // Pesan tamu
        $tamuPesans = MongoPengumuman::byType('tamu')
            ->where('data_tambahan.tujuan_username', $user->username)
            ->where('data_tambahan.status', 'menunggu')
            ->orderByDesc('created_at')->get();
        $datas['tamu_pesans'] = $tamuPesans;

        return view('pages.dashboard.dashboard', $datas)->with('title', 'Dashboard');
    }

    public function terimaPesan($id)
    {
        $pesan = MongoPengumuman::findOrFail($id);
        $dt    = $pesan->data_tambahan ?? [];
        $dt['status']       = 'pesan_telah_diterima';
        $pesan->data_tambahan = $dt;
        $pesan->save();
        return redirect()->back()->with('success', 'Pesan diterima.');
    }

    public function hapusPesan($id)
    {
        $pesan = MongoPengumuman::findOrFail($id);
        $dt    = $pesan->data_tambahan ?? [];
        $dt['status']       = 'pesan_telah_selesai';
        $pesan->data_tambahan = $dt;
        $pesan->save();
        return redirect()->back()->with('success', 'Pesan dihapus.');
    }
}