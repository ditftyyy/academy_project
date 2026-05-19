<?php

namespace App\Http\Controllers;

use App\Models\MongoDB\Inventaris as MongoInventaris;
use App\Models\MongoDB\Ruang as MongoRuang;
use Illuminate\Http\Request;

class InventarisController extends Controller
{
    /**
     * Halaman daftar inventaris
     */
    public function index()
    {
        $ruangs = MongoRuang::all();
        
        return view('pages.sarana.data-inventaris.inventaris', [
            'ruangs' => $ruangs,
            'title' => 'Daftar Inventaris'
        ]);
    }

    /**
     * Kelola barang per ruangan
     */
    public function aturBarang($ruangId)
    {
        $ruang = MongoRuang::findOrFail($ruangId);
        $inventaris = MongoInventaris::where('ruang.id', $ruangId)->get();
        
        return view('pages.sarana.data-inventaris.kelolabarang', [
            'ruangs' => $ruang,
            'inventaris' => $inventaris,
            'title' => 'Daftar Inventaris - ' . $ruang->nama_ruang
        ]);
    }

    /**
     * Tambah barang ke ruangan
     */
    public function store(Request $request, $ruangId)
    {
        $request->validate([
            'nama_barang' => 'required|string|max:255',
            'jumlah_barang' => 'required|integer|min:1',
            'jenis' => 'required|string',
        ]);

        $ruang = MongoRuang::findOrFail($ruangId);

        MongoInventaris::create([
            'nama_barang' => $request->nama_barang,
            'jenis' => $request->jenis,
            'tahun_pengadaan' => $request->tahun_pengadaan ?? now()->format('Y-m-d'),
            'image' => $request->image ?? null,
            'jumlah_seluruh' => $request->jumlah_barang,
            'jumlah_baik' => $request->jumlah_barang,
            'jumlah_rusak' => 0,
            'ruang' => [
                'id' => $ruang->_id,
                'nama' => $ruang->nama_ruang,
            ],
            'riwayat_peminjaman' => [],
        ]);

        return redirect()->route('atur-barang', $ruangId)
            ->with('toast_success', 'Data inventaris berhasil ditambahkan');
    }

    /**
     * Hapus barang dari ruangan
     */
    public function destroy($id)
    {
        try {
            $inventaris = MongoInventaris::findOrFail($id);
            $inventaris->delete();

            return redirect()->back()
                ->with('success', 'Inventaris berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menghapus inventaris: ' . $e->getMessage());
        }
    }

    /**
     * Cari barang
     */
    public function search(Request $request)
    {
        $searchTerm = $request->get('searchTerm');
        
        $barangs = MongoInventaris::where('nama_barang', 'like', "%{$searchTerm}%")
            ->get(['nama_barang', 'jenis', 'jumlah_baik', 'ruang.nama']);
        
        return response()->json($barangs);
    }

    /**
     * Detail barang berdasarkan nama
     */
    public function getDetailByName(Request $request)
    {
        $selectedBarang = $request->input('selectedBarang');
        
        $barang = MongoInventaris::where('nama_barang', $selectedBarang)->first();
        
        if ($barang) {
            return response()->json([
                'barang_id' => $barang->_id,
                'nama_barang' => $barang->nama_barang,
                'tahun_pengadaan' => $barang->tahun_pengadaan,
                'jenis' => $barang->jenis,
            ]);
        }
        
        return response()->json(['error' => 'Barang tidak ditemukan'], 404);
    }

    /**
     * Ambil semua barang
     */
    public function getAllBarang()
    {
        $barangs = MongoInventaris::all();
        return response()->json($barangs);
    }
}