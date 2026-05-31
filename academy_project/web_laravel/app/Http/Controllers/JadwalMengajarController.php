<?php

namespace App\Http\Controllers;

use App\Models\MongoDB\User as MongoUser;
use App\Models\MongoDB\Kelas as MongoKelas;
use App\Models\MongoDB\Ruang as MongoRuang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Carbon\Carbon;

class JadwalMengajarController extends Controller
{
    /**
     * Halaman daftar guru (untuk admin atur jadwal mengajar)
     */
    public function index()
    {
        $guru_list = MongoUser::guruAktif()->get();
        return view('pages.akademik.data-jadwal-mengajar.jadwal', [
            'guru_list' => $guru_list,
            'title' => 'Jadwal Mengajar Guru'
        ]);
    }

    /**
     * Halaman atur jadwal mengajar untuk satu guru
     */
    public function atur($guruId)
    {
        $guru = MongoUser::findOrFail($guruId);
        $kelas_list = MongoKelas::kelasAktif()->get();
        $ruangs = MongoRuang::all();

        // Ambil jadwal dari field schedule guru (struktur: array of objects dengan 'jadwal' per hari)
        $schedule = $guru->schedule ?? [];
        // Jika kosong, inisialisasi dengan 6 hari
        if (empty($schedule)) {
            $schedule = [
                'jadwal' => [
                    ['hari' => 'senin', 'mata_pelajaran' => []],
                    ['hari' => 'selasa', 'mata_pelajaran' => []],
                    ['hari' => 'rabu', 'mata_pelajaran' => []],
                    ['hari' => 'kamis', 'mata_pelajaran' => []],
                    ['hari' => 'jumat', 'mata_pelajaran' => []],
                    ['hari' => 'sabtu', 'mata_pelajaran' => []],
                ]
            ];
        }

        // Gabungkan semua mata pelajaran dari semua hari menjadi satu list untuk ditampilkan di tabel
        $allJadwal = [];
        foreach ($schedule['jadwal'] as $j) {
            $hari = $j['hari'];
            foreach ($j['mata_pelajaran'] ?? [] as $mp) {
                $allJadwal[] = array_merge($mp, ['hari' => $hari]);
            }
        }

        return view('pages.akademik.data-jadwal-mengajar.atur', [
            'guru' => $guru,
            'jadwal' => $allJadwal,
            'kelas_list' => $kelas_list,
            'ruangs' => $ruangs,
            'title' => 'Atur Jadwal Mengajar'
        ]);
    }

    /**
     * Tambah jadwal mengajar untuk guru
     */
    public function insert(Request $request)
    {
        $this->validate($request, [
            'guru_id' => 'required',
            'hari' => 'required',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
            'kelas_id' => 'required',
            'ruang_id' => 'required',
        ]);

        $guru = MongoUser::findOrFail($request->guru_id);
        $kelas = MongoKelas::findOrFail($request->kelas_id);
        $ruang = MongoRuang::findOrFail($request->ruang_id);

        $dataBaru = [
            'jam_mulai' => $request->jam_mulai,
            'jam_selesai' => $request->jam_selesai,
            'kelas' => $kelas->nama_kelas,
            'kelas_id' => $kelas->_id,
            'ruang' => $ruang->nama_ruang,
            'ruang_id' => $ruang->_id,
            'keterangan' => $request->keterangan ?? '',
        ];

        $schedule = $guru->schedule ?? ['jadwal' => []];
        $found = false;
        foreach ($schedule['jadwal'] as &$j) {
            if ($j['hari'] === $request->hari) {
                $j['mata_pelajaran'][] = $dataBaru;
                // urutkan
                usort($j['mata_pelajaran'], fn($a,$b) => strcmp($a['jam_mulai'], $b['jam_mulai']));
                $found = true;
                break;
            }
        }
        if (!$found) {
            $schedule['jadwal'][] = [
                'hari' => $request->hari,
                'mata_pelajaran' => [$dataBaru]
            ];
        }
        $guru->schedule = $schedule;
        $guru->save();

        return redirect()->back()->with('toast_success', 'Jadwal mengajar ditambahkan.');
    }

    /**
     * Update jadwal mengajar
     */
    public function update(Request $request, $guruId, $hari, $index)
    {
        $this->validate($request, [
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
            'kelas_id' => 'required',
            'ruang_id' => 'required',
        ]);

        $guru = MongoUser::findOrFail($guruId);
        $kelas = MongoKelas::findOrFail($request->kelas_id);
        $ruang = MongoRuang::findOrFail($request->ruang_id);

        $dataUpdate = [
            'jam_mulai' => $request->jam_mulai,
            'jam_selesai' => $request->jam_selesai,
            'kelas' => $kelas->nama_kelas,
            'kelas_id' => $kelas->_id,
            'ruang' => $ruang->nama_ruang,
            'ruang_id' => $ruang->_id,
            'keterangan' => $request->keterangan ?? '',
        ];

        $schedule = $guru->schedule ?? ['jadwal' => []];
        foreach ($schedule['jadwal'] as &$j) {
            if ($j['hari'] === $hari) {
                if (isset($j['mata_pelajaran'][$index])) {
                    $j['mata_pelajaran'][$index] = $dataUpdate;
                    usort($j['mata_pelajaran'], fn($a,$b) => strcmp($a['jam_mulai'], $b['jam_mulai']));
                }
                break;
            }
        }
        $guru->schedule = $schedule;
        $guru->save();

        return redirect()->back()->with('toast_success', 'Jadwal mengajar diupdate.');
    }

    /**
     * Hapus jadwal mengajar
     */
    public function destroy($guruId, $hari, $index)
    {
        $guru = MongoUser::findOrFail($guruId);
        $schedule = $guru->schedule ?? ['jadwal' => []];
        foreach ($schedule['jadwal'] as &$j) {
            if ($j['hari'] === $hari) {
                if (isset($j['mata_pelajaran'][$index])) {
                    array_splice($j['mata_pelajaran'], $index, 1);
                }
                break;
            }
        }
        $guru->schedule = $schedule;
        $guru->save();

        return redirect()->back()->with('toast_success', 'Jadwal mengajar dihapus.');
    }

    /**
     * Cetak PDF jadwal mengajar guru
     */
    public function cetak($guruId)
    {
        $guru = MongoUser::findOrFail($guruId);
        $schedule = $guru->schedule ?? ['jadwal' => []];
        $hariOrder = ['senin','selasa','rabu','kamis','jumat','sabtu'];
        $jadwal = collect($schedule['jadwal'])
            ->sortBy(fn($j) => array_search($j['hari'], $hariOrder))
            ->values()
            ->toArray();

        $pdf = App::make('dompdf.wrapper');
        $pdf->loadView('pages.akademik.data-jadwal-mengajar.cetak', [
            'guru' => $guru,
            'jadwal' => $jadwal,
        ]);
        return $pdf->stream('jadwal-mengajar-' . ($guru->guru_data['nama'] ?? $guru->nama_lengkap) . '.pdf');
    }

    /**
     * Halaman lihat jadwal mengajar untuk guru yang login
     */
    public function jadwalguru()
    {
        $user = auth()->user();
        if (!$user || $user->role !== 'guru') {
            return back()->with('toast_error', 'Akses ditolak.');
        }
        $schedule = $user->schedule ?? ['jadwal' => []];
        $hariIni = strtolower(Carbon::now()->locale('id')->dayName);
        $allJadwal = [];
        foreach ($schedule['jadwal'] as $j) {
            if ($j['hari'] === $hariIni) {
                $allJadwal = $j['mata_pelajaran'] ?? [];
                break;
            }
        }
        return view('pages.akademik.data-jadwal-guru.jadwalguru', [
            'all_jadwal' => $allJadwal,
            'hari_ini' => $hariIni,
            'title' => 'Jadwal Mengajar Saya'
        ]);
    }
}