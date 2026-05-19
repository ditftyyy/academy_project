<?php

namespace App\Http\Controllers;

use App\Models\MongoDB\User as MongoUser;
use App\Models\MongoDB\Kelas as MongoKelas;
use App\Models\MongoDB\Mapel as MongoMapel;
use Illuminate\Http\Request;

class InputNilaiController extends Controller
{
    /**
     * Halaman input nilai untuk guru
     */
    public function index($guruId)
    {
        // Ambil jadwal guru dari MongoDB
        $guru = MongoUser::findOrFail($guruId);
        $schedule = $guru->schedule ?? [];
        
        // Kumpulkan semua mapel unik yang diajar
        $mapelList = [];
        foreach ($schedule as $s) {
            foreach ($s['jadwal'] ?? [] as $j) {
                foreach ($j['mata_pelajaran'] ?? [] as $mp) {
                    $key = ($mp['mapel'] ?? '') . '_' . ($mp['kelas'] ?? '');
                    if (!isset($mapelList[$key])) {
                        $mapelList[$key] = [
                            'mapel' => $mp['mapel'] ?? '',
                            'kelas' => $mp['kelas'] ?? '',
                            'jam_mulai' => $mp['jam_mulai'] ?? '',
                            'jam_selesai' => $mp['jam_selesai'] ?? '',
                        ];
                    }
                }
            }
        }
        
        return view('datainputnilai.nilai', [
            'jadwal' => array_values($mapelList),
            'title' => 'Input Nilai'
        ]);
    }

    /**
     * Halaman atur nilai per kelas
     */
    public function atur($kelasNama, $mapelNama, $smt)
    {
        // Ambil siswa dalam kelas
        $siswas = MongoUser::siswaAktif()
            ->where('siswa_data.kelas.nama', $kelasNama)
            ->get();
        
        return view('datainputnilai.inputnilai', [
            'siswa' => $siswas,
            'mapel' => $mapelNama,
            'kelas' => $kelasNama,
            'semester' => $smt,
            'title' => 'Input Nilai - ' . $kelasNama
        ]);
    }

    /**
     * Form input nilai per siswa
     */
    public function input($siswaId, $mapelNama, $smt)
    {
        $siswa = MongoUser::findOrFail($siswaId);
        
        // Cari nilai yang sudah ada
        $nilaiExist = null;
        foreach ($siswa->academic_records ?? [] as $record) {
            if (($record['semester'] ?? '') === $smt) {
                foreach ($record['nilai'] ?? [] as $n) {
                    if (($n['mapel'] ?? '') === $mapelNama) {
                        $nilaiExist = $n;
                        break 2;
                    }
                }
            }
        }
        
        return view('datainputnilai.inputnilaisiswa', [
            'siswa' => $siswa,
            'mapel' => $mapelNama,
            'nilai' => $nilaiExist,
            'semester' => $smt,
            'title' => 'Input Nilai - ' . $siswa->nama_lengkap
        ]);
    }

    /**
     * Simpan nilai siswa
     */
    public function store(Request $request, $siswaId, $mapelNama, $smt)
    {
        $siswa = MongoUser::findOrFail($siswaId);
        
        // Hitung nilai
        $tugas = [
            $request->input('tugas1', 0),
            $request->input('tugas2', 0),
            $request->input('tugas3', 0),
            $request->input('tugas4', 0),
            $request->input('tugas5', 0),
        ];
        
        $rataTugas = array_sum($tugas) / count($tugas);
        $uts = $request->input('uts', 0);
        $uas = $request->input('uas', 0);
        
        $nilaiAkhir = ($rataTugas * 0.4) + ($uts * 0.3) + ($uas * 0.3);
        $nilaiHuruf = $this->konversiNilaiHuruf($nilaiAkhir);
        
        // Data nilai baru
        $nilaiBaru = [
            'mapel' => $mapelNama,
            'tugas' => $tugas,
            'uts' => $uts,
            'uas' => $uas,
            'nilai_akademik' => round($nilaiAkhir, 2),
            'nilai_huruf' => $nilaiHuruf,
            'guru' => auth()->user()->nama_lengkap ?? 'Unknown',
            'diinput_pada' => now()->toDateTimeString(),
        ];
        
        // Update academic_records
        $academicRecords = $siswa->academic_records ?? [];
        $found = false;
        
        foreach ($academicRecords as &$record) {
            if (($record['semester'] ?? '') === $smt) {
                // Update nilai yang ada atau tambah baru
                $nilaiUpdated = false;
                foreach ($record['nilai'] as &$n) {
                    if (($n['mapel'] ?? '') === $mapelNama) {
                        $n = $nilaiBaru;
                        $nilaiUpdated = true;
                        break;
                    }
                }
                
                if (!$nilaiUpdated) {
                    $record['nilai'][] = $nilaiBaru;
                }
                
                $found = true;
                break;
            }
        }
        
        if (!$found) {
            $academicRecords[] = [
                'tahun_ajaran' => '2025/2026',
                'semester' => $smt,
                'jenis_nilai' => 'harian',
                'nilai' => [$nilaiBaru],
                'absensi' => ['sakit' => 0, 'izin' => 0, 'tanpa_keterangan' => 0],
            ];
        }
        
        $siswa->update(['academic_records' => $academicRecords]);
        
        return redirect()->back()
            ->with('toast_success', 'Data berhasil disimpan!');
    }

    /**
     * Konversi nilai angka ke huruf
     */
    private function konversiNilaiHuruf(float $nilai): string
    {
        if ($nilai >= 85) return 'A';
        if ($nilai >= 70) return 'B';
        if ($nilai >= 55) return 'C';
        if ($nilai >= 40) return 'D';
        return 'E';
    }
}