<?php

namespace App\Http\Controllers;

use App\Models\MongoDB\Inventaris as MongoInventaris;
use App\Models\MongoDB\Ruang as MongoRuang;
use Illuminate\Http\Request;

class InventarisController extends Controller
{
    public function index()
    {
        $ruangs = MongoRuang::all();
        return view('pages.sarana.data-inventaris.inventaris', compact('ruangs'));
    }

    public function aturBarang($ruangId)
    {
        $ruang = MongoRuang::findOrFail($ruangId);
        // Ambil inventaris ruang (yang memiliki field ruang.id = $ruangId)
        // Perhatikan: karena data lama mungkin ruang masih string, kita tidak bisa mengambilnya.
        // Tapi kita hanya akan menampilkan data yang sudah disimpan melalui aplikasi (array).
        $inventaris = MongoInventaris::where('ruang.id', (string) $ruang->_id)->get();
        // Barang master (belum punya ruang) – cek ruang null atau tidak ada field ruang
        $masterBarang = MongoInventaris::whereNull('ruang.id')->orWhere('ruang', null)->get();

        return view('pages.sarana.data-inventaris.kelolabarang', compact('ruang', 'inventaris', 'masterBarang'));
    }

    public function store(Request $request, $ruangId)
    {
        $request->validate([
            'barang_id'     => 'required|exists:inventaris,_id',
            'jumlah_barang' => 'required|integer|min:1',
        ]);

        $barangMaster = MongoInventaris::findOrFail($request->barang_id);
        $ruang = MongoRuang::findOrFail($ruangId);

        // Cek apakah barang sudah ada di ruang ini (hanya cek data yang sudah disimpan dengan format array)
        $exists = MongoInventaris::where('ruang.id', (string) $ruang->_id)
            ->where('nama_barang', $barangMaster->nama_barang)
            ->first();
        if ($exists) {
            return redirect()->back()->with('toast_error', 'Barang sudah ada di ruang ini.');
        }

        // Simpan inventaris ruang (pastikan ruang disimpan sebagai array, bukan string)
        $inventarisBaru = MongoInventaris::create([
            'nama_barang'      => $barangMaster->nama_barang,
            'jenis'            => $barangMaster->jenis,
            'tahun_pengadaan'  => $barangMaster->tahun_pengadaan,
            'image'            => null,
            'jumlah_seluruh'   => (int) $request->jumlah_barang,
            'jumlah_baik'      => (int) $request->jumlah_barang,
            'jumlah_rusak'     => 0,
            'ruang'            => [   // array biasa, bukan string
                'id'   => (string) $ruang->_id,
                'nama' => $ruang->nama_ruang,
            ],
            'riwayat_peminjaman' => [],
        ]);

        return redirect()->route('atur-barang', $ruangId)
               ->with('toast_success', 'Barang berhasil ditambahkan ke ruang ini.');
    }

    public function destroy($id)
    {
        $inventaris = MongoInventaris::findOrFail($id);
        $inventaris->delete();
        return back()->with('toast_success', 'Barang dihapus dari ruang ini.');
    }
}