<?php

namespace App\Http\Controllers;

use App\Models\MongoDB\Inventaris as MongoInventaris;
use Illuminate\Http\Request;

class BarangController extends Controller
{
    public function index()
    {
        // Hanya barang master (belum punya ruang)
        $daftarBarang = MongoInventaris::whereNull('ruang.id')->orderBy('nama_barang')->get();
        return view('pages.sarana.data-barang.barang', compact('daftarBarang'));
    }

    public function create()
    {
        $jenis_barang = ['meubel', 'elektronik', 'atk'];
        return view('pages.sarana.data-barang.tambah-barang', compact('jenis_barang'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama_barang' => 'required|string|max:255',
            'tahun_pengadaan' => 'required|date',
            'jenis' => 'required|string',
            'jumlah_seluruh_barang' => 'required|integer|min:1',
        ]);

        MongoInventaris::create([
            'nama_barang' => $data['nama_barang'],
            'jenis' => $data['jenis'],
            'tahun_pengadaan' => $data['tahun_pengadaan'],
            'jumlah_seluruh' => (int) $data['jumlah_seluruh_barang'],
            'jumlah_baik' => (int) $data['jumlah_seluruh_barang'],
            'jumlah_rusak' => 0,
            'ruang' => null,
            'riwayat_peminjaman' => [],
        ]);

        return redirect()->route('barang_main')->with('success', 'Data master barang berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $barang = MongoInventaris::findOrFail($id);
        $jenis_barang = ['meubel', 'elektronik', 'atk'];
        return view('pages.sarana.data-barang.edit-barang', compact('barang', 'jenis_barang'));
    }

    public function update(Request $request, $id)
    {
        $barang = MongoInventaris::findOrFail($id);
        $request->validate([
            'nama_barang' => 'required|string',
            'tahun_pengadaan' => 'required|date',
            'jenis' => 'required|string',
            'jumlah_seluruh_barang' => 'required|integer|min:1',
        ]);

        $updateData = [
            'nama_barang' => $request->nama_barang,
            'tahun_pengadaan' => $request->tahun_pengadaan,
            'jenis' => $request->jenis,
            'jumlah_seluruh' => (int) $request->jumlah_seluruh_barang,
        ];

        $selisih = $request->jumlah_seluruh_barang - $barang->jumlah_seluruh;
        if ($selisih != 0) {
            $updateData['jumlah_baik'] = max(0, ($barang->jumlah_baik ?? 0) + $selisih);
        }

        $barang->update($updateData);
        return redirect()->route('barang_main')->with('success', 'Data master barang berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $barang = MongoInventaris::findOrFail($id);
        $barang->delete();
        return redirect()->route('barang_main')->with('success', 'Data master barang berhasil dihapus.');
    }
}