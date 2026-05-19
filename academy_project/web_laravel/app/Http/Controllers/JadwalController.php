<?php

namespace App\Http\Controllers;

use App\Models\MongoDB\Kelas as MongoKelas;
use App\Models\MongoDB\User as MongoUser;
use App\Models\MongoDB\Mapel as MongoMapel;
use App\Models\MongoDB\Ruang as MongoRuang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Carbon\Carbon;

class JadwalController extends Controller
{
    /**
     * Halaman jadwal admin
     */
    public function showJadwalAdmin()
    {
        $kelas = MongoKelas::kelasAktif()->get();
        
        return view('pages.akademik.data-jadwal.jadwal', [
            'daftar_kelas' => $kelas,
            'title' => 'Data Jadwal'
        ]);
    }

    /**
     * Halaman jadwal siswa (berdasarkan kelas yang login)
     */
    public function showJadwalSiswa()
    {
        $user = auth()->user();
        $kelasId = $user->siswa_data['kelas']['id'] ?? null;
        
        if (!$kelasId) {
            return back()->with('toast_error', 'Data kelas tidak ditemukan');
        }
        
        $kelas = MongoKelas::find($kelasId);
        $jadwals = $kelas->jadwal ?? [];
        
        return view('pages.data-jadwal.jadwal-kelas', [
            'jadwals' => $jadwals,
            'title' => 'Jadwal ' . ($user->siswa_data['kelas']['nama'] ?? '')
        ]);
    }

    /**
     * Kelola jadwal per kelas
     */
    public function jadwalKelas(Request $request, $kelasId)
    {
        $kelas = MongoKelas::findOrFail($kelasId);
        
        // Update status hari (masuk/libur)
        if ($request->has('hari') && $request->has('status')) {
            $jadwal = $kelas->jadwal ?? [];
            
            foreach ($jadwal as &$j) {
                if (($j['hari'] ?? '') === $request->hari) {
                    $j['status'] = $request->status;
                    break;
                }
            }
            
            $kelas->update(['jadwal' => $jadwal]);
        }
        
        return view('pages.akademik.data-jadwal.jadwal-kelas', [
            'kelas' => $kelas,
            'jadwals' => $kelas->jadwal ?? [],
            'mapels' => MongoMapel::all(),
            'gurus' => MongoUser::guruAktif()->get(),
            'ruangs' => MongoRuang::all(),
            'title' => 'Jadwal ' . $kelas->nama_kelas
        ]);
    }

    /**
     * Tambah mata pelajaran ke jadwal
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'hari' => 'required|string',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
            'id_mapel' => 'required',
            'id_guru' => 'required',
            'id_ruang' => 'required',
        ]);

        $kelas = MongoKelas::findOrFail($request->id_kelas);
        
        // Validasi durasi minimal 45 menit
        $jamMulai = Carbon::createFromFormat('H:i', $request->jam_mulai);
        $jamSelesai = Carbon::createFromFormat('H:i', $request->jam_selesai);
        
        if ($jamSelesai->diffInMinutes($jamMulai) < 45) {
            return redirect()->back()
                ->with('toast_error', 'Durasi pelajaran minimal 45 menit.');
        }
        
        // Cek bentrok jadwal
        $jadwals = $kelas->jadwal ?? [];
        foreach ($jadwals as $j) {
            if (($j['hari'] ?? '') !== $request->hari) continue;
            
            foreach ($j['mata_pelajaran'] ?? [] as $mp) {
                $mpMulai = Carbon::createFromFormat('H:i', $mp['jam_mulai'] ?? '00:00');
                $mpSelesai = Carbon::createFromFormat('H:i', $mp['jam_selesai'] ?? '00:00');
                
                if (
                    ($jamMulai >= $mpMulai && $jamMulai < $mpSelesai) ||
                    ($jamSelesai > $mpMulai && $jamSelesai <= $mpSelesai)
                ) {
                    return redirect()->back()
                        ->with('toast_error', 'Jadwal bentrok dengan ' . ($mp['mapel'] ?? 'mata pelajaran lain'));
                }
            }
        }
        
        // Ambil nama mapel, guru, ruang
        $mapel = MongoMapel::find($request->id_mapel);
        $guru = MongoUser::find($request->id_guru);
        $ruang = MongoRuang::find($request->id_ruang);
        
        $mataPelajaranBaru = [
            'jam_mulai' => $request->jam_mulai,
            'jam_selesai' => $request->jam_selesai,
            'mapel' => $mapel->nama_mapel ?? '',
            'guru' => $guru->guru_data['nama'] ?? ($guru->profile['nama_lengkap'] ?? ''),
            'ruang' => $ruang->nama_ruang ?? '',
        ];
        
        // Tambahkan ke jadwal
        $jadwals = $kelas->jadwal ?? [];
        $found = false;
        
        foreach ($jadwals as &$j) {
            if (($j['hari'] ?? '') === $request->hari) {
                $j['mata_pelajaran'][] = $mataPelajaranBaru;
                // Urutkan berdasarkan jam mulai
                usort($j['mata_pelajaran'], function($a, $b) {
                    return strcmp($a['jam_mulai'] ?? '', $b['jam_mulai'] ?? '');
                });
                $found = true;
                break;
            }
        }
        
        if (!$found) {
            $jadwals[] = [
                'hari' => $request->hari,
                'status' => 'masuk',
                'mata_pelajaran' => [$mataPelajaranBaru],
            ];
        }
        
        $kelas->update(['jadwal' => $jadwals]);
        
        return redirect()->back()
            ->with('toast_success', 'Data berhasil ditambahkan!');
    }

    /**
     * Update mata pelajaran di jadwal
     */
    public function update(Request $request, $kelasId, $hari, $index)
    {
        $kelas = MongoKelas::findOrFail($kelasId);
        
        $this->validate($request, [
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i',
            'mapel' => 'required',
            'guru' => 'required',
            'ruang' => 'required',
        ]);
        
        $jadwals = $kelas->jadwal ?? [];
        
        foreach ($jadwals as &$j) {
            if (($j['hari'] ?? '') === $hari) {
                if (isset($j['mata_pelajaran'][$index])) {
                    $j['mata_pelajaran'][$index] = [
                        'jam_mulai' => $request->jam_mulai,
                        'jam_selesai' => $request->jam_selesai,
                        'mapel' => $request->mapel,
                        'guru' => $request->guru,
                        'ruang' => $request->ruang,
                    ];
                }
                break;
            }
        }
        
        $kelas->update(['jadwal' => $jadwals]);
        
        return redirect()->back()
            ->with('toast_success', 'Data berhasil diubah!');
    }

    /**
     * Hapus mata pelajaran dari jadwal
     */
    public function destroy($kelasId, $hari, $index)
    {
        $kelas = MongoKelas::findOrFail($kelasId);
        $jadwals = $kelas->jadwal ?? [];
        
        foreach ($jadwals as &$j) {
            if (($j['hari'] ?? '') === $hari) {
                if (isset($j['mata_pelajaran'][$index])) {
                    array_splice($j['mata_pelajaran'], $index, 1);
                }
                break;
            }
        }
        
        $kelas->update(['jadwal' => $jadwals]);
        
        return redirect()->back()
            ->with('toast_success', 'Data berhasil dihapus!');
    }

    /**
     * Cetak jadwal PDF
     */
    public function cetak($kelasId)
    {
        $kelas = MongoKelas::findOrFail($kelasId);
        $hariOrder = ['senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu'];
        
        // Urutkan jadwal berdasarkan hari
        $jadwals = collect($kelas->jadwal ?? [])
            ->sortBy(function($j) use ($hariOrder) {
                return array_search(strtolower($j['hari'] ?? ''), $hariOrder) ?: 99;
            })
            ->values()
            ->toArray();
        
        $pdf = App::make('dompdf.wrapper');
        $pdf->loadView('pages.data-jadwal.cetak', [
            'jadwal' => $jadwals,
            'kelas' => $kelas,
            'hari' => ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu']
        ]);
        
        return $pdf->stream('laporan-jadwal-pelajaran-pdf');
    }

    /**
     * Lihat jadwal siswa
     */
    public function jadwalsiswa($kelasId)
    {
        if ($kelasId == 'null' || !$kelasId) {
            return back()->with('toast_warning', 'Fitur ini masih dikerjakan');
        }
        
        $kelas = MongoKelas::find($kelasId);
        
        if (!$kelas) {
            return back()->with('toast_error', 'Kelas tidak ditemukan');
        }
        
        $hariIni = strtolower(Carbon::now()->locale('id')->dayName);
        
        // Ambil jadwal hari ini
        $jadwalHariIni = [];
        foreach ($kelas->jadwal ?? [] as $j) {
            if (strtolower($j['hari'] ?? '') === $hariIni) {
                $jadwalHariIni = $j['mata_pelajaran'] ?? [];
                break;
            }
        }
        
        return view('pages.akademik.data-jadwal-siswa.jadwalsiswa', [
            'jadwals' => $kelas->jadwal ?? [],
            'jadwal_hari_ini' => $jadwalHariIni,
            'kelas' => $kelas,
            'hari_ini' => $hariIni,
            'title' => 'Jadwal Pelajaran'
        ]);
    }
}