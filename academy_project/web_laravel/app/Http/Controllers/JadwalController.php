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
     * Halaman daftar kelas untuk admin mengatur jadwal
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
     * Halaman atur jadwal per kelas
     */
    public function jadwalKelas(Request $request, $kelasId)
    {
        $kelas = MongoKelas::findOrFail($kelasId);

        // Proses liburkan / masuk jika ada request POST dari form di card header
        if ($request->has('hari') && $request->has('status')) {
            $kelas->updateStatusHari($request->hari, $request->status);
            return redirect()->back()->with('toast_success', 'Status hari berhasil diubah.');
        }

        // Urutkan jadwal berdasarkan urutan hari (senin s/d sabtu)
        $hariOrder = ['senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu'];
        $jadwals = collect($kelas->jadwal ?? [])
            ->sortBy(function($j) use ($hariOrder) {
                return array_search(strtolower($j['hari'] ?? ''), $hariOrder);
            })
            ->values()
            ->toArray();

        return view('pages.akademik.data-jadwal.jadwal-kelas', [
            'kelas' => $kelas,
            'jadwals' => $jadwals,
            'mapels' => MongoMapel::all(),
            'gurus' => MongoUser::guruAktif()->get(),
            'ruangs' => MongoRuang::all(),
            'title' => 'Atur Jadwal - ' . $kelas->nama_kelas
        ]);
    }

    /**
     * Tambah mata pelajaran ke jadwal (via modal)
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'id_kelas' => 'required',
            'hari' => 'required|string',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
            'id_mapel' => 'required',
            'id_guru' => 'required',
            'id_ruang' => 'required',
        ]);

        $kelas = MongoKelas::findOrFail($request->id_kelas);

        // Cek durasi minimal 45 menit
        $jamMulai = Carbon::createFromFormat('H:i', $request->jam_mulai);
        $jamSelesai = Carbon::createFromFormat('H:i', $request->jam_selesai);
        if ($jamSelesai->diffInMinutes($jamMulai) < 45) {
            return redirect()->back()->with('toast_error', 'Durasi pelajaran minimal 45 menit.');
        }

        // Cek bentrok jadwal di hari yang sama
        $jadwalHari = collect($kelas->jadwal ?? [])->firstWhere('hari', $request->hari);
        if ($jadwalHari && isset($jadwalHari['mata_pelajaran'])) {
            foreach ($jadwalHari['mata_pelajaran'] as $mp) {
                $mpMulai = Carbon::createFromFormat('H:i', $mp['jam_mulai']);
                $mpSelesai = Carbon::createFromFormat('H:i', $mp['jam_selesai']);
                if ($jamMulai < $mpSelesai && $jamSelesai > $mpMulai) {
                    return redirect()->back()->with('toast_error', 'Jadwal bentrok dengan ' . ($mp['mapel'] ?? 'pelajaran lain'));
                }
            }
        }

        // Ambil data nama
        $mapel = MongoMapel::find($request->id_mapel);
        $guru = MongoUser::find($request->id_guru);
        $ruang = MongoRuang::find($request->id_ruang);

        $dataBaru = [
            'jam_mulai' => $request->jam_mulai,
            'jam_selesai' => $request->jam_selesai,
            'mapel' => $mapel->nama_mapel ?? '',
            'guru' => $guru->guru_data['nama'] ?? $guru->nama_lengkap,
            'ruang' => $ruang->nama_ruang ?? '',
            'mapel_id' => $request->id_mapel,
            'guru_id' => $request->id_guru,
            'ruang_id' => $request->id_ruang,
        ];

        $kelas->tambahMataPelajaran($request->hari, $dataBaru);

        return redirect()->back()->with('toast_success', 'Jadwal berhasil ditambahkan.');
    }

    /**
     * Update mata pelajaran (perbaikan)
     */
    public function update(Request $request)
    {
        $request->validate([
            'kelas_id' => 'required',
            'hari' => 'required|string',
            'index' => 'required|integer',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
            'mapel_id' => 'required',
            'guru_id' => 'required',
            'ruang_id' => 'required',
        ]);

        $kelas = MongoKelas::findOrFail($request->kelas_id);
        $hari = $request->hari;
        $index = $request->index;

        // Ambil data nama
        $mapel = MongoMapel::find($request->mapel_id);
        $guru = MongoUser::find($request->guru_id);
        $ruang = MongoRuang::find($request->ruang_id);

        if (!$mapel || !$guru || !$ruang) {
            return redirect()->back()->with('toast_error', 'Data mapel, guru, atau ruang tidak valid.');
        }

        $dataUpdate = [
            'jam_mulai' => $request->jam_mulai,
            'jam_selesai' => $request->jam_selesai,
            'mapel' => $mapel->nama_mapel,
            'guru' => $guru->guru_data['nama'] ?? $guru->nama_lengkap,
            'ruang' => $ruang->nama_ruang,
            'mapel_id' => $request->mapel_id,
            'guru_id' => $request->guru_id,
            'ruang_id' => $request->ruang_id,
        ];

        // Panggil method model
        $kelas->updateMataPelajaran($hari, $index, $dataUpdate);

        return redirect()->back()->with('toast_success', 'Jadwal berhasil diupdate.');
    }

    /**
     * Hapus mata pelajaran
     */
    public function destroy($kelasId, $hari, $index)
    {
        $kelas = MongoKelas::findOrFail($kelasId);
        $kelas->hapusMataPelajaran($hari, $index);
        return redirect()->back()->with('toast_success', 'Jadwal berhasil dihapus.');
    }

    /**
     * Cetak jadwal PDF
     */
    public function cetak($kelasId)
    {
        $kelas = MongoKelas::findOrFail($kelasId);
        $hariOrder = ['senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu'];
        $jadwals = collect($kelas->jadwal ?? [])
            ->sortBy(function($j) use ($hariOrder) {
                return array_search(strtolower($j['hari'] ?? ''), $hariOrder);
            })
            ->values()
            ->toArray();

        $pdf = App::make('dompdf.wrapper');
        $pdf->loadView('pages.akademik.data-jadwal.cetak', [
            'jadwal' => $jadwals,
            'kelas' => $kelas,
        ]);
        return $pdf->stream('jadwal-kelas-' . $kelas->nama_kelas . '.pdf');
    }

    /**
     * Halaman cek jadwal untuk siswa (sudah ada di jadwalsiswa)
     */
    public function jadwalsiswa($kelasId)
    {
        $kelas = MongoKelas::findOrFail($kelasId);
        $hariIni = strtolower(Carbon::now()->locale('id')->dayName);
        return view('pages.akademik.data-jadwal-siswa.jadwalsiswa', [
            'jadwals' => $kelas->jadwal ?? [],
            'kelas' => $kelas,
            'hari_ini' => $hariIni,
            'title' => 'Jadwal Pelajaran'
        ]);
    }
}