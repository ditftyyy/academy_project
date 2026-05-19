<?php

namespace App\Http\Controllers;

use App\Models\MongoDB\User as MongoUser;
use App\Models\MongoDB\Kelas as MongoKelas;
use App\Models\MongoDB\Mapel as MongoMapel;
use App\Models\MongoDB\Akademik as MongoAkademik;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;

class RaportController extends Controller
{
    /**
     * ============================================
     * CATATAN UNTUK PEMULA:
     * Controller ini mengelola RAPORT siswa.
     * 
     * Di MongoDB, nilai disimpan di field
     * 'academic_records' dalam dokumen user siswa.
     * 
     * academic_records adalah ARRAY yang berisi:
     * [
     *   {
     *     "tahun_ajaran": "2025/2026",
     *     "semester": "ganjil",
     *     "nilai": [
     *       { "mapel": "Matematika", "nilai_akademik": 85 },
     *       { "mapel": "Bahasa Inggris", "nilai_akademik": 78 }
     *     ],
     *     "absensi": { "sakit": 2, "izin": 1, "tanpa_keterangan": 0 }
     *   }
     * ]
     * ============================================
     */

    /**
     * Halaman daftar angkatan untuk raport
     */
    public function index()
    {
        // Ambil angkatan unik dari data siswa
        $angkatans = MongoUser::siswaAktif()
            ->get()
            ->pluck('siswa_data.angkatan.nama')
            ->unique()
            ->filter()
            ->values();
        
        return view('pages.akademik.data-raport.raport-admin', [
            'angkatans' => $angkatans,
            'title' => 'Raport'
        ]);
    }

    /**
     * Tampilkan siswa berdasarkan angkatan
     */
    public function showRaportAngkatan($angkatanNama)
    {
        $siswas = MongoUser::where('role', 'siswa')
            ->where('siswa_data.angkatan.nama', $angkatanNama)
            ->get();
        
        return view('pages.akademik.data-raport.siswa', [
            'siswas' => $siswas,
            'title' => 'Raport ' . $angkatanNama
        ]);
    }

    /**
     * Form input raport per siswa
     * 
     * CARA KERJA:
     * 1. Cari siswa by ID
     * 2. Ambil academic_records untuk semester yang dimaksud
     * 3. Tampilkan form input
     */
    public function input($siswaId, $semester)
    {
        $siswa = MongoUser::findOrFail($siswaId);
        
        // Cari record akademik untuk semester ini
        $recordAkademik = null;
        foreach ($siswa->academic_records ?? [] as $record) {
            if (($record['semester'] ?? '') === $semester) {
                $recordAkademik = $record;
                break;
            }
        }
        
        // Jika belum ada record, buat kosong
        if (!$recordAkademik) {
            $recordAkademik = [
                'semester' => $semester,
                'nilai' => [],
                'absensi' => ['sakit' => 0, 'izin' => 0, 'tanpa_keterangan' => 0],
            ];
        }
        
        $mapels = MongoMapel::all();
        $kelas = MongoKelas::all();
        
        return view('dataraport.input', [
            'siswa' => $siswa,
            'raport' => $recordAkademik['nilai'] ?? [],
            'mapel' => $mapels,
            'raport_ket' => $recordAkademik['absensi']['sakit'] ?? 0,
            'raport_ket2' => $recordAkademik['absensi']['izin'] ?? 0,
            'raport_ket3' => $recordAkademik['absensi']['tanpa_keterangan'] ?? 0,
            'semester' => $semester,
            'kelas' => $kelas,
            'title' => 'Input Raport - ' . $siswa->nama_lengkap
        ]);
    }

    /**
     * Tambah nilai ke raport
     */
    public function tambahnilai(Request $request)
    {
        $siswa = MongoUser::findOrFail($request->siswa_id);
        
        // Konversi nilai ke huruf
        $nilaiPth = $request->nilai_pth;
        $nilaiKtr = $request->nilai_ktr;
        $nilaiHurufPth = $this->konversiHuruf($nilaiPth);
        $nilaiHurufKtr = $this->konversiHuruf($nilaiKtr);
        
        // Hitung rata-rata
        $rataRata = ($nilaiPth + $nilaiKtr) / 2;
        $nilaiHurufRata = $this->konversiHuruf($rataRata);
        
        // Update academic_records
        $academicRecords = $siswa->academic_records ?? [];
        $semester = $request->semester;
        $found = false;
        
        foreach ($academicRecords as &$record) {
            if (($record['semester'] ?? '') === $semester) {
                $record['nilai'][] = [
                    'mapel' => $request->mapel_id,
                    'nilai_pengetahuan' => $nilaiPth,
                    'nilai_keterampilan' => $nilaiKtr,
                    'nilai_akademik' => round($rataRata, 2),
                    'nilai_huruf' => $nilaiHurufRata,
                    'guru' => $request->guru_id ?? '',
                ];
                
                // Update absensi jika ada
                if ($request->has('sakit')) {
                    $record['absensi'] = [
                        'sakit' => $request->sakit ?? 0,
                        'izin' => $request->izin ?? 0,
                        'tanpa_keterangan' => $request->tanpa_ket ?? 0,
                    ];
                }
                
                $found = true;
                break;
            }
        }
        
        if (!$found) {
            $academicRecords[] = [
                'tahun_ajaran' => MongoAkademik::aktif()->first()->tahun_ajaran ?? '',
                'semester' => $semester,
                'jenis_nilai' => 'raport',
                'nilai' => [[
                    'mapel' => $request->mapel_id,
                    'nilai_pengetahuan' => $nilaiPth,
                    'nilai_keterampilan' => $nilaiKtr,
                    'nilai_akademik' => round($rataRata, 2),
                    'nilai_huruf' => $nilaiHurufRata,
                    'guru' => $request->guru_id ?? '',
                ]],
                'absensi' => [
                    'sakit' => $request->sakit ?? 0,
                    'izin' => $request->izin ?? 0,
                    'tanpa_keterangan' => $request->tanpa_ket ?? 0,
                ],
            ];
        }
        
        $siswa->update(['academic_records' => $academicRecords]);
        
        return redirect()->back()
            ->with('toast_success', 'Nilai berhasil ditambahkan');
    }

    /**
     * Simpan raport (update status)
     */
    public function store(Request $request)
    {
        $siswa = MongoUser::findOrFail($request->siswa_id);
        
        // Update status kenaikan kelas di academic_records
        $academicRecords = $siswa->academic_records ?? [];
        $semester = $request->semester;
        
        foreach ($academicRecords as &$record) {
            if (($record['semester'] ?? '') === $semester) {
                $record['status'] = $request->status ?? 'selesai';
                $record['catatan'] = $request->catatan ?? '';
                break;
            }
        }
        
        // Update kelas siswa jika naik kelas
        if ($request->kelassiswa && $request->status === 'naik_kelas') {
            $siswaData = $siswa->siswa_data ?? [];
            $siswaData['kelas']['id'] = $request->kelassiswa;
            
            $siswa->update([
                'academic_records' => $academicRecords,
                'siswa_data' => $siswaData,
            ]);
        } else {
            $siswa->update(['academic_records' => $academicRecords]);
        }
        
        return redirect('/data-cetak-raport/' . $semester . '/' . $siswa->_id)
            ->with('toast_success', 'Raport berhasil disimpan');
    }

    /**
     * Hapus nilai dari raport
     */
    public function destroy($siswaId, $mapelIndex, $semester)
    {
        $siswa = MongoUser::findOrFail($siswaId);
        $academicRecords = $siswa->academic_records ?? [];
        
        foreach ($academicRecords as &$record) {
            if (($record['semester'] ?? '') === $semester) {
                if (isset($record['nilai'][$mapelIndex])) {
                    array_splice($record['nilai'], $mapelIndex, 1);
                }
                break;
            }
        }
        
        $siswa->update(['academic_records' => $academicRecords]);
        
        return redirect()->back()
            ->with('toast_success', 'Nilai berhasil dihapus');
    }

    /**
     * Cetak raport PDF
     */
    public function cetak($semester, $siswaId)
    {
        $siswa = MongoUser::findOrFail($siswaId);
        
        // Cari record akademik
        $recordAkademik = null;
        foreach ($siswa->academic_records ?? [] as $record) {
            if (($record['semester'] ?? '') === $semester) {
                $recordAkademik = $record;
                break;
            }
        }
        
        if (!$recordAkademik) {
            return redirect()->back()
                ->with('toast_error', 'Data raport belum ada');
        }
        
        // Ambil data wali kelas
        $waliKelas = null;
        if (isset($siswa->siswa_data['kelas']['id'])) {
            $kelas = MongoKelas::find($siswa->siswa_data['kelas']['id']);
            $waliKelas = $kelas->wali_kelas ?? null;
        }
        
        // Ambil data kepala sekolah
        $kepsek = MongoUser::where('profile.jabatan', 'Kepala Sekolah')->first();
        
        $tanggalSekarang = Carbon::now()
            ->setTimezone('Asia/Jakarta')
            ->translatedFormat('d F Y');
        
        $pdf = App::make('dompdf.wrapper');
        $pdf->loadView('dataraport.cetak', [
            'siswa' => $siswa,
            'raport' => $recordAkademik['nilai'] ?? [],
            'raport_ket' => $recordAkademik['absensi']['sakit'] ?? 0,
            'raport_ket2' => $recordAkademik['absensi']['izin'] ?? 0,
            'raport_ket3' => $recordAkademik['absensi']['tanpa_keterangan'] ?? 0,
            'kepsek' => $kepsek,
            'tanggal' => $tanggalSekarang,
            'walikelas' => $waliKelas,
            'semester' => $semester,
            'status' => $recordAkademik['status'] ?? '',
        ]);
        
        return $pdf->stream('Raport - ' . $siswa->nama_lengkap . '.pdf');
    }

    /**
     * Helper: Konversi nilai angka ke huruf
     */
    private function konversiHuruf($nilai)
    {
        if ($nilai >= 85 && $nilai <= 100) return 'A';
        if ($nilai >= 70 && $nilai < 85) return 'B';
        if ($nilai >= 55 && $nilai < 70) return 'C';
        if ($nilai >= 40 && $nilai < 55) return 'D';
        return 'E';
    }
}