<?php

namespace App\Http\Controllers;

use App\Models\MongoDB\Ruang as MongoRuang;
use Illuminate\Http\Request;

class RuangController extends Controller
{
    /**
     * ============================================
     * CATATAN UNTUK PEMULA:
     * Controller ini mengelola data RUANGAN
     * (kelas, lab, aula, dll).
     * 
     * Di MongoDB, data ruangan disimpan di
     * collection 'ruang' dengan dokumen sederhana.
     * ============================================
     */

    /**
     * Halaman daftar ruangan
     */
    public function index()
    {
        $ruangs = MongoRuang::all();
        
        return view('pages.sarana.data-ruang.ruang', [
            'ruangs' => $ruangs,
            'title' => 'Daftar Ruangan'
        ]);
    }

    /**
     * Tambah ruangan baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_ruang' => 'required|unique:ruang,nama_ruang',
            'luas' => 'required|integer|min:1',
            'lokasi' => 'required|string',
        ]);

        MongoRuang::create([
            'nama_ruang' => strtoupper($request->nama_ruang),
            'luas' => $request->luas,
            'lokasi' => ucfirst($request->lokasi),
        ]);

        return redirect()->route('ruang_main')
            ->with('toast_success', 'Data Ruang Berhasil di Tambahkan');
    }

    /**
     * Update data ruangan
     */
    public function update(Request $request)
    {
        $ruang = MongoRuang::findOrFail($request->id_ruang);
        
        $validatedCondition = [
            'nama_ruang' => 'required',
            'lokasi' => 'required',
            'luas' => 'required|integer|min:1',
        ];

        // Jika nama berubah, cek unique
        if ($ruang->nama_ruang != $request->nama_ruang) {
            $validatedCondition['nama_ruang'] = 'required|unique:ruang,nama_ruang';
        }

        $request->validate($validatedCondition);

        $ruang->update([
            'nama_ruang' => strtoupper($request->nama_ruang),
            'luas' => $request->luas,
            'lokasi' => ucfirst($request->lokasi),
        ]);

        return redirect()->route('ruang_main')
            ->with('toast_success', 'Data Ruang Berhasil di Ubah');
    }

    /**
     * Hapus ruangan
     */
    public function destroy($id)
    {
        $ruang = MongoRuang::findOrFail($id);
        $ruang->delete();

        return redirect()->route('ruang_main')
            ->with('toast_success', 'Data Ruang Berhasil di Hapus');
    }
}