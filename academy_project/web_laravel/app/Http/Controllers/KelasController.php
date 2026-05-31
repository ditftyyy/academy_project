<?php

namespace App\Http\Controllers;

use App\Models\MongoDB\Kelas as MongoKelas;
use App\Models\MongoDB\User as MongoUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
        
        // Guru yang belum jadi wali kelas (tidak ada field wali_kelas di guru_data? kita cek dengan not exists di kelas)
        // Ambil semua guru aktif
        $semuaGuru = MongoUser::guruAktif()->get();
        // Filter guru yang sudah menjadi wali kelas
        $waliKelasIds = MongoKelas::kelasAktif()
            ->whereNotNull('wali_kelas.id')
            ->pluck('wali_kelas.id')
            ->toArray();
        
        $guruTersedia = $semuaGuru->filter(function($g) use ($waliKelasIds) {
            return !in_array($g->_id, $waliKelasIds);
        });
        
        return view('pages.sarana.data-kelas.kelas', [
            'daftar_kelas' => $kelas,
            'guruTersedia' => $guruTersedia,
            'list_guru' => $semuaGuru,
            'title' => 'Data Kelas'
        ]);
    }

    /**
     * Tambah kelas baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_kelas' => 'required|string',
            'id_guru' => 'nullable|exists:users,_id',
        ]);
        
        $namaKelas = strtoupper(trim($request->nama_kelas));
        
        // Cek duplikasi (case-insensitive) – manual query
        $exists = MongoKelas::whereRaw([
            '$expr' => [
                '$eq' => [
                    ['$toLower' => '$nama_kelas'],
                    strtolower($namaKelas)
                ]
            ]
        ])->first();
        
        if ($exists) {
            if ($exists->deleted) {
                // Aktifkan kembali kelas yang sudah di-soft delete
                $exists->update(['deleted' => false]);
                return redirect()->route('kelas_main')
                    ->with('toast_success', 'Kelas yang telah dihapus berhasil diaktifkan kembali!');
            } else {
                return redirect()->back()
                    ->with('toast_error', 'Kelas dengan nama tersebut sudah ada!');
            }
        }
        
        // Ambil data wali kelas
        $waliKelas = null;
        if ($request->filled('id_guru')) {
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
            'nama_kelas' => $namaKelas,
            'tingkat' => explode(' ', $namaKelas)[0] ?? '',
            'jurusan' => explode(' ', $namaKelas)[1] ?? '',
            'wali_kelas' => $waliKelas,
            'jadwal' => $jadwal,
            'siswa_ids' => [],
            'deleted' => false,
        ]);
        
        return redirect()->route('kelas_main')
            ->with('toast_success', 'Data kelas berhasil ditambahkan!');
    }

    /**
     * Update data kelas
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_kelas' => 'required|string',
            'id_guru' => 'nullable|exists:users,_id',
        ]);
        
        $kelas = MongoKelas::findOrFail($id);
        $namaKelas = strtoupper(trim($request->nama_kelas));
        
        // Cek duplikasi (kecuali dirinya sendiri)
        $exists = MongoKelas::whereRaw([
            '$expr' => [
                '$eq' => [
                    ['$toLower' => '$nama_kelas'],
                    strtolower($namaKelas)
                ]
            ]
        ])->first();
        
        if ($exists && $exists->_id != $id) {
            return redirect()->back()
                ->with('toast_error', 'Kelas dengan nama tersebut sudah ada!');
        }
        
        // Update wali kelas
        $waliKelas = null;
        if ($request->filled('id_guru')) {
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
            'nama_kelas' => $namaKelas,
            'wali_kelas' => $waliKelas,
            // tingkat dan jurusan bisa diupdate otomatis dari nama
            'tingkat' => explode(' ', $namaKelas)[0] ?? $kelas->tingkat,
            'jurusan' => explode(' ', $namaKelas)[1] ?? $kelas->jurusan,
        ]);
        
        return redirect()->route('kelas_main')
            ->with('toast_success', 'Data kelas berhasil diubah!');
    }

    /**
     * Soft delete kelas (set deleted = true)
     */
    public function destroy($id)
    {
        $kelas = MongoKelas::findOrFail($id);
        $kelas->update([
            'deleted' => true,
            'wali_kelas' => null,
        ]);
        
        return redirect()->route('kelas_main')
            ->with('toast_success', 'Data kelas berhasil dihapus (soft delete)!');
    }
}