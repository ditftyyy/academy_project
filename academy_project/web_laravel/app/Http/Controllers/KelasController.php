<?php

namespace App\Http\Controllers;

use App\Models\MongoDB\Kelas as MongoKelas;
use App\Models\MongoDB\User as MongoUser;
use App\Models\MongoDB\Akademik as MongoAkademik;
use Illuminate\Http\Request;

class KelasController extends Controller
{
    /**
     * Ambil nama kelas (JSON)
     */
    public function getKelas()
    {
        $kelas = MongoKelas::kelasAktif()->pluck('nama_kelas');
        return response()->json($kelas);
    }

    /**
     * Halaman daftar kelas
     */
    public function index()
    {
        $kelas = MongoKelas::kelasAktif()
            ->orderBy('nama_kelas', 'asc')
            ->get();
        
        // Guru yang belum jadi wali kelas
        $guruTersedia = MongoUser::guruAktif()
            ->where('guru_data.kelas_wali', null)
            ->get();
        
        return view('pages.sarana.data-kelas.kelas', [
            'daftar_kelas' => $kelas,
            'guruTersedia' => $guruTersedia,
            'list_guru' => MongoUser::guruAktif()->get(),
            'title' => 'Data Kelas'
        ]);
    }

    /**
     * Tambah kelas baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_kelas' => 'required|unique:kelas,nama_kelas',
            'id_guru' => 'nullable',
        ]);
        
        // Cek kelas yang sudah di-soft delete
        $existingKelas = MongoKelas::where('nama_kelas', strtoupper($request->nama_kelas))
            ->where('deleted', true)
            ->first();
        
        if ($existingKelas) {
            $existingKelas->update(['deleted' => false]);
            return redirect()->route('kelas_main')
                ->with('toast_success', 'Kelas terhapus telah aktif kembali!');
        }
        
        // Ambil data wali kelas
        $waliKelas = null;
        if ($request->id_guru) {
            $guru = MongoUser::find($request->id_guru);
            if ($guru) {
                $waliKelas = [
                    'id' => $guru->_id,
                    'nip' => $guru->guru_data['nip'] ?? '',
                    'nama' => $guru->guru_data['nama'] ?? $guru->nama_lengkap,
                ];
            }
        }
        
        // Generate jadwal kosong untuk 6 hari
        $jadwal = [];
        foreach (['senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu'] as $hari) {
            $jadwal[] = [
                'hari' => $hari,
                'status' => 'libur',
                'mata_pelajaran' => [],
            ];
        }
        
        MongoKelas::create([
            'nama_kelas' => strtoupper($request->nama_kelas),
            'tingkat' => explode(' ', $request->nama_kelas)[0],
            'jurusan' => explode(' ', $request->nama_kelas)[1] ?? '',
            'wali_kelas' => $waliKelas,
            'jadwal' => $jadwal,
            'siswa_ids' => [],
            'deleted' => false,
        ]);
        
        return redirect()->route('kelas_main')
            ->with('toast_success', 'Data berhasil ditambahkan!');
    }

    /**
     * Update data kelas
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_kelas' => 'required',
            'id_guru' => 'nullable',
        ]);
        
        $kelas = MongoKelas::findOrFail($id);
        
        // Update wali kelas
        $waliKelas = null;
        if ($request->id_guru) {
            $guru = MongoUser::find($request->id_guru);
            if ($guru) {
                $waliKelas = [
                    'id' => $guru->_id,
                    'nip' => $guru->guru_data['nip'] ?? '',
                    'nama' => $guru->guru_data['nama'] ?? $guru->nama_lengkap,
                ];
            }
        }
        
        $kelas->update([
            'nama_kelas' => strtoupper($request->nama_kelas),
            'wali_kelas' => $waliKelas,
        ]);
        
        return redirect()->route('kelas_main')
            ->with('toast_success', 'Data berhasil diubah!');
    }

    /**
     * Soft delete kelas
     */
    public function destroy($id)
    {
        $kelas = MongoKelas::findOrFail($id);
        $kelas->update([
            'deleted' => true,
            'wali_kelas' => null,
        ]);
        
        return redirect()->route('kelas_main')
            ->with('toast_success', 'Data berhasil dihapus!');
    }
}