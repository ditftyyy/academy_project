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
    public function index()
    {
        $guru_list = MongoUser::guruAktif()->get();
        return view('pages.akademik.data-jadwal-mengajar.jadwal', [
            'guru_list' => $guru_list,
            'title'     => 'Jadwal Mengajar Guru'
        ]);
    }

    public function atur($guruId)
    {
        $guru       = MongoUser::findOrFail($guruId);
        $kelas_list = MongoKelas::kelasAktif()->get();
        $ruangs     = MongoRuang::all();

        $schedule    = $guru->schedule ?? [];
        $jadwalArray = $schedule['jadwal'] ?? [];
        if (empty($jadwalArray)) {
            $jadwalArray = [
                ['hari' => 'senin',  'mata_pelajaran' => []],
                ['hari' => 'selasa', 'mata_pelajaran' => []],
                ['hari' => 'rabu',   'mata_pelajaran' => []],
                ['hari' => 'kamis',  'mata_pelajaran' => []],
                ['hari' => 'jumat',  'mata_pelajaran' => []],
                ['hari' => 'sabtu',  'mata_pelajaran' => []],
            ];
        }

        $allJadwal = [];
        foreach ($jadwalArray as $j) {
            foreach ($j['mata_pelajaran'] ?? [] as $mp) {
                $allJadwal[] = array_merge($mp, ['hari' => $j['hari']]);
            }
        }

        return view('pages.akademik.data-jadwal-mengajar.atur', [
            'guru'       => $guru,
            'jadwal'     => $allJadwal,
            'kelas_list' => $kelas_list,
            'ruangs'     => $ruangs,
            'title'      => 'Atur Jadwal Mengajar'
        ]);
    }

    public function insert(Request $request)
    {
        $this->validate($request, [
            'guru_id'     => 'required',
            'hari'        => 'required',
            'jam_mulai'   => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
            'kelas_id'    => 'required',
            'ruang_id'    => 'required',
        ]);

        $guru  = MongoUser::findOrFail($request->guru_id);
        $kelas = MongoKelas::findOrFail($request->kelas_id);
        $ruang = MongoRuang::findOrFail($request->ruang_id);

        $dataBaru = [
            'jam_mulai'   => $request->jam_mulai,
            'jam_selesai' => $request->jam_selesai,
            'kelas'       => $kelas->nama_kelas,
            'kelas_id'    => (string) $kelas->_id,
            'ruang'       => $ruang->nama_ruang,
            'ruang_id'    => (string) $ruang->_id,
            'keterangan'  => $request->keterangan ?? '',
        ];

        $schedule    = $guru->schedule ?? [];
        $jadwalArray = $schedule['jadwal'] ?? [];
        $found       = false;
        foreach ($jadwalArray as &$j) {
            if ($j['hari'] === $request->hari) {
                $j['mata_pelajaran'][] = $dataBaru;
                usort($j['mata_pelajaran'], fn($a, $b) => strcmp($a['jam_mulai'], $b['jam_mulai']));
                $found = true;
                break;
            }
        }
        unset($j);
        if (!$found) {
            $jadwalArray[] = ['hari' => $request->hari, 'mata_pelajaran' => [$dataBaru]];
        }
        $schedule['jadwal'] = $jadwalArray;
        $guru->schedule     = $schedule;
        $guru->save();

        return redirect()->back()->with('toast_success', 'Jadwal mengajar ditambahkan.');
    }

    public function update(Request $request, $guruId, $hari, $index)
    {
        $this->validate($request, [
            'jam_mulai'   => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
            'kelas_id'    => 'required',
            'ruang_id'    => 'required',
        ]);

        $guru  = MongoUser::findOrFail($guruId);
        $kelas = MongoKelas::findOrFail($request->kelas_id);
        $ruang = MongoRuang::findOrFail($request->ruang_id);

        $dataUpdate = [
            'jam_mulai'   => $request->jam_mulai,
            'jam_selesai' => $request->jam_selesai,
            'kelas'       => $kelas->nama_kelas,
            'kelas_id'    => (string) $kelas->_id,
            'ruang'       => $ruang->nama_ruang,
            'ruang_id'    => (string) $ruang->_id,
            'keterangan'  => $request->keterangan ?? '',
        ];

        $schedule    = $guru->schedule ?? [];
        $jadwalArray = $schedule['jadwal'] ?? [];
        foreach ($jadwalArray as &$j) {
            if ($j['hari'] === $hari && isset($j['mata_pelajaran'][$index])) {
                $j['mata_pelajaran'][$index] = $dataUpdate;
                usort($j['mata_pelajaran'], fn($a, $b) => strcmp($a['jam_mulai'], $b['jam_mulai']));
                break;
            }
        }
        unset($j);
        $schedule['jadwal'] = $jadwalArray;
        $guru->schedule     = $schedule;
        $guru->save();

        return redirect()->back()->with('toast_success', 'Jadwal mengajar diupdate.');
    }

    public function destroy($guruId, $hari, $index)
    {
        $guru        = MongoUser::findOrFail($guruId);
        $schedule    = $guru->schedule ?? [];
        $jadwalArray = $schedule['jadwal'] ?? [];
        foreach ($jadwalArray as &$j) {
            if ($j['hari'] === $hari && isset($j['mata_pelajaran'][$index])) {
                array_splice($j['mata_pelajaran'], $index, 1);
                break;
            }
        }
        unset($j);
        $schedule['jadwal'] = $jadwalArray;
        $guru->schedule     = $schedule;
        $guru->save();

        return redirect()->back()->with('toast_success', 'Jadwal mengajar dihapus.');
    }

    public function cetak($guruId)
    {
        $guru        = MongoUser::findOrFail($guruId);
        $schedule    = $guru->schedule ?? [];
        $jadwalArray = $schedule['jadwal'] ?? [];
        $hariOrder   = ['senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu'];
        $jadwal = collect($jadwalArray)
            ->sortBy(fn($j) => array_search($j['hari'] ?? '', $hariOrder))
            ->values()->toArray();

        $pdf = App::make('dompdf.wrapper');
        $pdf->loadView('pages.akademik.data-jadwal-mengajar.cetak', compact('guru', 'jadwal'));
        return $pdf->stream('jadwal-mengajar-' . ($guru->guru_data['nama'] ?? $guru->nama_lengkap) . '.pdf');
    }

    /**
     * Jadwal mengajar guru yang login.
     * Membaca dari kelas->jadwal, filter berdasarkan guru_id atau nama guru.
     * Struktur kelas->jadwal:
     *   [ { hari, status, mata_pelajaran: [ { jam_mulai, jam_selesai, mapel, guru, guru_id, ruang, ... } ] } ]
     */
    public function jadwalguru()
    {
        $user = auth()->user();
        if (!$user || $user->role !== 'guru') {
            return back()->with('toast_error', 'Akses ditolak.');
        }

        $userId        = (string) $user->_id;
        $guruNamaLogin = trim($user->guru_data['nama'] ?? $user->nama_lengkap ?? '');
        $hariIni       = strtolower(Carbon::now()->locale('id')->dayName);
        $hariIndo      = ['senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu'];

        // Inisialisasi struktur per hari
        $jadwalPerHari = [];
        foreach ($hariIndo as $hari) {
            $jadwalPerHari[$hari] = ['status' => 'masuk', 'mata_pelajaran' => []];
        }

        // Loop semua kelas, cari jadwal yang ada guru_id = user ini
        $kelasList = MongoKelas::kelasAktif()->get();

        foreach ($kelasList as $kelas) {
            $jadwalKelas = $kelas->jadwal;
            if (empty($jadwalKelas) || !is_array($jadwalKelas)) {
                continue;
            }

            foreach ($jadwalKelas as $hariEntry) {
                if (is_object($hariEntry)) $hariEntry = (array) $hariEntry;

                $hari = strtolower(trim($hariEntry['hari'] ?? ''));
                if (!in_array($hari, $hariIndo)) continue;

                // Jika hari ini berstatus libur di kelas manapun, tandai libur
                $statusHari = $hariEntry['status'] ?? 'masuk';
                if ($statusHari === 'libur') {
                    // Hanya set libur jika belum ada mata pelajaran tersimpan untuk hari ini
                    if (empty($jadwalPerHari[$hari]['mata_pelajaran'])) {
                        $jadwalPerHari[$hari]['status'] = 'libur';
                    }
                    continue;
                }

                $mataPelajaran = $hariEntry['mata_pelajaran'] ?? [];
                if (!is_array($mataPelajaran)) continue;

                foreach ($mataPelajaran as $mp) {
                    if (is_object($mp)) $mp = (array) $mp;

                    $guruIdDB   = (string) ($mp['guru_id'] ?? '');
                    $guruNamaDB = trim((string) ($mp['guru'] ?? ''));

                    // Cocokkan guru_id (utama) atau nama (fallback)
                    $cocok = ($guruIdDB !== '' && $guruIdDB === $userId)
                        || (!empty($guruNamaDB) && !empty($guruNamaLogin)
                            && strcasecmp($guruNamaDB, $guruNamaLogin) === 0);

                    if (!$cocok) continue;

                    // Pastikan hari tidak libur sebelum menambah
                    $jadwalPerHari[$hari]['status'] = 'masuk';
                    $jadwalPerHari[$hari]['mata_pelajaran'][] = [
                        'jam_mulai'   => $mp['jam_mulai']   ?? '',
                        'jam_selesai' => $mp['jam_selesai'] ?? '',
                        'mapel'       => $mp['mapel']       ?? '-',
                        'kelas'       => $kelas->nama_kelas ?? '-',
                        'ruang'       => $mp['ruang']       ?? '-',
                        'keterangan'  => $mp['keterangan']  ?? '',
                    ];
                }
            }
        }

        // Urutkan per hari berdasarkan jam_mulai
        foreach ($jadwalPerHari as &$data) {
            usort($data['mata_pelajaran'], fn($a, $b) => strcmp($a['jam_mulai'], $b['jam_mulai']));
        }
        unset($data);

        return view('pages.akademik.data-jadwal-guru.jadwalguru', [
            'jadwalPerHari' => $jadwalPerHari,
            'hariIni'       => $hariIni,
        ]);
    }
}