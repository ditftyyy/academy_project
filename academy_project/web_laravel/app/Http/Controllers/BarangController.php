<?php

namespace App\Http\Controllers;

use App\Models\MongoDB\Inventaris as MongoInventaris;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BarangController extends Controller
{
    /**
     * ============================================
     * CATATAN UNTUK PEMULA:
     * Controller ini mengelola DATA BARANG
     * (meja, kursi, proyektor, dll).
     * 
     * Di MongoDB, barang disimpan di collection
     * 'inventaris'. Setiap dokumen menyimpan:
     * - nama_barang
     * - jenis (meubel/elektronik/atk)
     * - jumlah (seluruh, baik, rusak)
     * - ruang (lokasi barang)
     * - riwayat_peminjaman
     * ============================================
     */

    /**
     * Halaman daftar semua barang
     * 
     * Route: GET /barang
     */
    public function index()
    {
        $daftarBarang = MongoInventaris::orderBy('nama_barang', 'asc')->get();
        
        return view('pages.sarana.data-barang.barang', [
            'daftarBarang' => $daftarBarang,
            'title' => 'Daftar Barang'
        ]);
    }

    /**
     * Halaman tambah barang
     * 
     * Route: GET /barang/tambah
     */
    public function create()
    {
        return view('pages.sarana.data-barang.tambah-barang', [
            'jenis_barang' => ['meubel', 'elektronik', 'atk'],
            'title' => 'Tambah Barang'
        ]);
    }

    /**
     * Simpan barang baru
     * 
     * Route: POST /barang
     * 
     * CARA KERJA:
     * 1. Validasi input
     * 2. Upload gambar jika ada
     * 3. Simpan ke collection inventaris
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'nama_barang' => 'required|string|max:255',
            'tahun_pengadaan' => 'required|date',
            'jenis' => 'required|string',
            'jumlah_seluruh_barang' => 'required|integer|min:1',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10240',
        ]);

        // Upload gambar
        $fileName = null;
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $fileName = uniqid() . '.' . $file->getClientOriginalExtension();
            $file->storeAs('public/image', $fileName);
        }

        // Simpan ke MongoDB
        MongoInventaris::create([
            'nama_barang' => $data['nama_barang'],
            'jenis' => $data['jenis'],
            'tahun_pengadaan' => $data['tahun_pengadaan'],
            'image' => $fileName,
            'jumlah_seluruh' => $data['jumlah_seluruh_barang'],
            'jumlah_baik' => $data['jumlah_seluruh_barang'],  // Awalnya semua baik
            'jumlah_rusak' => 0,
            'ruang' => null,  // Belum ditempatkan
            'riwayat_peminjaman' => [],
        ]);

        return redirect()->route('barang_main')
            ->with('success', 'Data daftar barang berhasil ditambahkan.');
    }

    /**
     * Halaman edit barang
     * 
     * Route: GET /barang/{id}/edit
     */
    public function edit($id)
    {
        $barang = MongoInventaris::findOrFail($id);
        
        return view('pages.sarana.data-barang.edit-barang', [
            'barang' => $barang,
            'jenis_barang' => ['meubel', 'elektronik', 'atk'],
            'title' => 'Update Barang'
        ]);
    }

    /**
     * Update data barang
     * 
     * Route: PUT /barang/{id}
     * 
     * CARA KERJA:
     * 1. Validasi input
     * 2. Upload gambar baru (jika ada)
     * 3. Hapus gambar lama
     * 4. Update data barang
     */
    public function update(Request $request, $id)
    {
        $barang = MongoInventaris::findOrFail($id);
        
        $request->validate([
            'nama_barang' => 'required|string|max:255',
            'tahun_pengadaan' => 'required|date',
            'jenis' => 'required|string',
            'jumlah_seluruh_barang' => 'required|integer|min:1',
            'image' => 'nullable|image|file|max:10240',
        ]);

        $updateData = [
            'nama_barang' => $request->input('nama_barang'),
            'tahun_pengadaan' => $request->input('tahun_pengadaan'),
            'jenis' => $request->input('jenis'),
            'jumlah_seluruh' => $request->input('jumlah_seluruh_barang'),
        ];

        // Update gambar jika ada
        if ($request->hasFile('image')) {
            // Hapus gambar lama
            if ($barang->image) {
                Storage::delete('public/image/' . $barang->image);
            }
            
            // Upload gambar baru
            $file = $request->file('image');
            $fileName = uniqid() . '.' . $file->getClientOriginalExtension();
            $file->storeAs('public/image/', $fileName);
            
            $updateData['image'] = $fileName;
        }

        // Hitung ulang jumlah baik (jika jumlah berubah)
        $selisih = $request->jumlah_seluruh_barang - $barang->jumlah_seluruh;
        if ($selisih != 0) {
            $updateData['jumlah_baik'] = ($barang->jumlah_baik ?? 0) + $selisih;
            if ($updateData['jumlah_baik'] < 0) {
                $updateData['jumlah_baik'] = 0;
            }
        }

        $barang->update($updateData);

        return redirect()->route('barang_main')
            ->with('success', 'Data barang berhasil diperbarui.');
    }

    /**
     * Hapus barang
     * 
     * Route: DELETE /barang/{id}
     */
    public function destroy($id)
    {
        $barang = MongoInventaris::findOrFail($id);
        
        // Hapus gambar dari storage
        if ($barang->image) {
            Storage::delete('public/image/' . $barang->image);
        }
        
        // Hapus dari database
        $barang->delete();

        return redirect()->route('barang_main')
            ->with('success', 'Data barang berhasil dihapus.');
    }
}