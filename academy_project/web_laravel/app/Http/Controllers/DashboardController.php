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
     */
    public function index()
    {
        if (!Auth::check()) {
            return redirect('/login');
        }
        
        $user = Auth::user();
        $role = $user->current_role ?? $user->role;
        $datas = [];
        
        // ========== KALENDER AKADEMIK ==========
        $events = [];
        $akademikAktif = MongoAkademik::aktif()->first();
        
        if ($akademikAktif && isset($akademikAktif->kalender)) {
            foreach ($akademikAktif->kalender as $k) {
                $color = ($k['status'] ?? '') === 'masuk' ? '#924ACE' : '#68B01A';
                $events[] = [
                    'id' => $k['id'] ?? uniqid(),
                    'title' => $k['title'] ?? '',
                    'start' => $k['start_date'] ?? '',
                    'end' => $k['end_date'] ?? '',
                    'status' => $k['status'] ?? 'masuk',
                    'color' => $color,
                ];
            }
        }
        $datas['events'] = $events;
        
        // ========== DATA SPESIFIK PER ROLE ==========
        
        if (in_array($role, ['admin', 'kepsek', 'root'])) {
            // ===== DASHBOARD ADMIN =====
            
            // Statistik - TAMBAHKAN $teknisi
            $datas['teknisi'] = 0;  // Bisa diganti dengan count dari MongoDB jika ada role teknisi
            $datas['total_guru'] = MongoUser::where('role', 'guru')->where('deleted', false)->count();
            $datas['total_siswa'] = MongoUser::where('role', 'siswa')
                ->whereIn('siswa_data.status', ['bukan pindahan', 'pindahan', 'mutasi'])
                ->count();
            $datas['total_kelas'] = MongoKelas::where('deleted', false)->count();
            
            // Cek tahun ajaran aktif
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
                    'semester' => $semester,
                    'selected' => true,
                    'kalender' => [],
                    'konfigurasi' => [],
                ]);
            }
            
        } elseif ($role === 'guru') {
            // ===== DASHBOARD GURU =====
            
            $guruData = [
                'nama' => $user->guru_data['nama'] ?? $user->nama_lengkap,
                'nip' => $user->guru_data['nip'] ?? '-',
                'kelas_wali' => $user->guru_data['kelas_wali']['nama'] ?? 'Belum ditentukan',
            ];
            
            // Jadwal hari ini
            $hariIni = strtolower(now()->locale('id')->dayName);
            $jadwalHariIni = [];
            
            foreach ($user->schedule ?? [] as $s) {
                foreach ($s['jadwal'] ?? [] as $j) {
                    if (strtolower($j['hari'] ?? '') === $hariIni) {
                        foreach ($j['mata_pelajaran'] ?? [] as $mp) {
                            $jadwalHariIni[] = $mp;
                        }
                    }
                }
            }
            
            // Rekap absensi bulan ini
            $bulanIni = now()->format('Y-m');
            $rekapAbsensi = ['masuk' => 0, 'sakit' => 0, 'izin' => 0, 'tidak_masuk' => 0];
            
            foreach ($user->attendances ?? [] as $absen) {
                if (str_starts_with($absen['tanggal'] ?? '', $bulanIni)) {
                    $status = $absen['status'] ?? '';
                    if (isset($rekapAbsensi[$status])) {
                        $rekapAbsensi[$status]++;
                    }
                }
            }
            
            $datas['guru_data'] = $guruData;
            $datas['jadwal_hari_ini'] = $jadwalHariIni;
            $datas['rekap_absensi'] = $rekapAbsensi;
            
        } elseif ($role === 'siswa') {
            // ===== DASHBOARD SISWA =====
            
            $siswaData = [
                'nama' => $user->siswa_data['nama'] ?? $user->nama_lengkap,
                'nis' => $user->siswa_data['nis'] ?? '-',
                'kelas' => $user->siswa_data['kelas']['nama'] ?? '-',
            ];
            
            $hariIni = strtolower(now()->locale('id')->dayName);
            $jadwalHariIni = [];
            
            foreach ($user->schedule ?? [] as $s) {
                foreach ($s['jadwal'] ?? [] as $j) {
                    if (strtolower($j['hari'] ?? '') === $hariIni) {
                        foreach ($j['mata_pelajaran'] ?? [] as $mp) {
                            $jadwalHariIni[] = $mp;
                        }
                    }
                }
            }
            
            $bulanIni = now()->format('Y-m');
            $rekapAbsensi = ['masuk' => 0, 'sakit' => 0, 'izin' => 0, 'tidak_masuk' => 0];
            
            foreach ($user->attendances ?? [] as $absen) {
                if (str_starts_with($absen['tanggal'] ?? '', $bulanIni)) {
                    $status = $absen['status'] ?? '';
                    if (isset($rekapAbsensi[$status])) {
                        $rekapAbsensi[$status]++;
                    }
                }
            }
            
            $nilaiTerbaru = [];
            $lastRecord = collect($user->academic_records ?? [])->last();
            if ($lastRecord) {
                $nilaiTerbaru = $lastRecord['nilai'] ?? [];
            }
            
            $datas['siswa_data'] = $siswaData;
            $datas['jadwal_hari_ini'] = $jadwalHariIni;
            $datas['rekap_absensi'] = $rekapAbsensi;
            $datas['nilai_terbaru'] = $nilaiTerbaru;
        }
        
        // ========== PENGUMUMAN ==========
        
        if (in_array($role, ['admin', 'kepsek', 'root'])) {
            $pengumumans = MongoPengumuman::byType('pengumuman')
                ->orderBy('created_at', 'desc')
                ->get();
        } else {
            $pengumumans = MongoPengumuman::where('type', 'pengumuman')
                ->where(function($query) use ($role) {
                    $query->where('role', $role)
                          ->orWhere('role', 'semua');
                })
                ->orderBy('created_at', 'desc')
                ->get();
        }
        
        $datas['pengumumans'] = $pengumumans;
        
        // TAMBAHKAN: Daftar role untuk dropdown pengumuman
        $datas['rolePengumuman'] = config('app.DB_user_roles', ['admin', 'guru', 'siswa']);
        
        // ========== PESAN TAMU ==========
        $tamuPesans = MongoPengumuman::byType('tamu')
            ->where('data_tambahan.tujuan_username', $user->username)
            ->where('data_tambahan.status', 'menunggu')
            ->orderByDesc('created_at')
            ->get();
        
        $datas['tamu_pesans'] = $tamuPesans;
        
        return view('pages.dashboard.dashboard', $datas)
            ->with('title', 'Dashboard');
    }

    /**
     * Terima pesan tamu
     */
    public function terimaPesan($id)
    {
        $tamuPesan = MongoPengumuman::findOrFail($id);
        
        // Update data_tambahan.status
        $dataTambahan = $tamuPesan->data_tambahan ?? [];
        $dataTambahan['status'] = 'pesan_telah_diterima';
        $tamuPesan->data_tambahan = $dataTambahan;
        $tamuPesan->save();

        return redirect()->back()->with('success', 'Pesan diterima.');
    }

    /**
     * Hapus/Selesai pesan tamu
     */
    public function hapusPesan($id)
    {
        $tamuPesan = MongoPengumuman::findOrFail($id);
        
        $dataTambahan = $tamuPesan->data_tambahan ?? [];
        $dataTambahan['status'] = 'pesan_telah_selesai';
        $tamuPesan->data_tambahan = $dataTambahan;
        $tamuPesan->save();

        return redirect()->back()->with('success', 'Pesan dihapus.');
    }
}