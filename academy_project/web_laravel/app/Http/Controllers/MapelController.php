<?php

namespace App\Http\Controllers;

use App\Models\MongoDB\Mapel as MongoMapel;
use Illuminate\Http\Request;

class MapelController extends Controller
{
    /**
     * Halaman daftar mapel
     */
    public function index()
    {
        $mapels = MongoMapel::all();
        
        return view('pages.akademik.data-mapel.mapel', [
            'mapels' => $mapels,
            'title' => 'Mata Pelajaran'
        ]);
    }

    /**
     * Tambah mapel baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_mapel' => 'required|unique:mata_pelajaran,nama_mapel',
        ]);
        
        MongoMapel::create([
            'nama_mapel' => $request->nama_mapel,
            'guru_pengajar_ids' => [],
        ]);
        
        return redirect()->route('mapel_main')
            ->with('toast_success', 'Data berhasil ditambahkan!');
    }

    /**
     * Update mapel
     */
    public function update(Request $request, $id)
    {
        $mapel = MongoMapel::findOrFail($id);
        
        $request->validate([
            'nama_mapel' => 'required|unique:mata_pelajaran,nama_mapel,' . $id,
        ]);
        
        $mapel->update(['nama_mapel' => $request->nama_mapel]);
        
        return redirect()->route('mapel_main')
            ->with('toast_success', 'Data berhasil diubah!');
    }

    /**
     * Hapus mapel
     */
    public function destroy($id)
    {
        $mapel = MongoMapel::findOrFail($id);
        $mapel->delete();
        
        return redirect()->route('mapel_main')
            ->with('toast_success', 'Data berhasil dihapus!');
    }
}