<?php

namespace App\Http\Controllers;

use App\Models\MongoDB\Inventaris as MongoInventaris;
use Illuminate\Http\Request;

class PeminjamanBarangController extends Controller
{
    /**
     * ============================================
     * CATATAN UNTUK PEMULA:
     * Controller ini mengelola peminjaman BARANG
     * (seperti proyektor, laptop, dll).
     * 
     * Data peminjaman disimpan sebagai array
     * 'riwayat_peminjaman' di dalam dokumen barang
     * di collection 'inventaris'.
     * ============================================
     */

    /**
     * Halaman daftar peminjaman barang
     * 
     * Route: GET /peminjaman-barang
     * 
     * Tampilkan:
     * - Peminjaman yang masih aktif (hari ini)
     * - Semua peminjaman yang belum dikembalikan
     * - Daftar barang yang tersedia
     */
    public function index()
    {
        // Ambil semua barang
        $semuaBarang = MongoInventaris::all();
        
        // Kumpulkan peminjaman yang masih aktif
        $peminjamanAktif = [];
        $hariIni = now()->format('Y-m-d');
        
        foreach ($semuaBarang as $barang) {
            foreach ($barang->riwayat_peminjaman ?? [] as $index => $peminjaman) {
                // Jika status masih 'dipinjam' dan belum dikembalikan
                if (($peminjaman['status'] ?? '') === 'dipinjam') {
                    $peminjamanAktif[] = array_merge($peminjaman, [
                        'barang_id' => $barang->_id,
                        'nama_barang' => $barang->nama_barang,
                        'ruang' => $barang->ruang['nama'] ?? '-',
                        'peminjaman_index' => $index,
                    ]);
                }
            }
        }
        
        // Urutkan: terbaru di atas
        $peminjamanAktif = collect($peminjamanAktif)
            ->sortByDesc('created_at')
            ->values();
        
        // Peminjaman hari ini
        $hariIniList = collect($peminjamanAktif)
            ->filter(function($p) use ($hariIni) {
                return ($p['tanggal_pinjam'] ?? '') === $hariIni;
            });
        
        return view('pages.humas.data-peminjaman-barang.index', [
            'hariini' => $hariIniList,
            'peminjaman' => $peminjamanAktif,
            'barang' => $semuaBarang,
            'title' => 'Data Peminjaman Barang'
        ]);
    }

    /**
     * Simpan peminjaman baru
     * 
     * Route: POST /peminjaman-barang
     * 
     * CARA KERJA:
     * 1. Validasi input
     * 2. Upload surat peminjaman
     * 3. Cari barang di collection inventaris
     * 4. Catat peminjaman ke riwayat
     * 5. Kurangi stok barang
     */
    public function store(Request $request)
    {
        // Validasi
        $validated = $request->validate([
            'barang_id' => 'required',
            'jumlah' => 'required|integer|min:1',
            'nama_peminjam' => 'required|string',
            'tanggal_peminjaman' => 'required|date',
            'tanggal_pengembalian' => 'required|date|after:tanggal_peminjaman',
            'surat' => 'required|file|mimes:pdf,doc,docx|max:2048',
        ]);

        // Upload surat
        $file = $request->file('surat');
        $fileName = time() . '_' . $file->getClientOriginalName();
        $file->storeAs('public/surat', $fileName);

        // Cari barang
        $barang = MongoInventaris::findOrFail($request->barang_id);
        
        // Cek stok cukup
        if (($barang->jumlah_baik ?? 0) < $request->jumlah) {
            return redirect()->back()
                ->with('toast_error', 'Stok barang tidak mencukupi!');
        }

        // Catat peminjaman (otomatis kurangi stok)
        $barang->catatPeminjaman([
            'nama_peminjam' => $request->nama_peminjam,
            'jumlah' => $request->jumlah,
            'tanggal_pinjam' => $request->tanggal_peminjaman,
            'tanggal_kembali' => $request->tanggal_pengembalian,
            'surat' => $fileName,
        ]);

        return redirect()->route('peminjamanBarang.index')
            ->with('toast_success', 'Data Peminjaman Berhasil di Tambahkan');
    }

    /**
     * Update peminjaman
     */
    public function update(Request $request)
    {
        $request->validate([
            'id' => 'required',
            'nama_peminjam' => 'sometimes|string',
            'tanggal_pengembalian' => 'sometimes|date',
        ]);

        // Cari barang yang memiliki peminjaman ini
        $barang = MongoInventaris::where('riwayat_peminjaman.id', $request->id)
            ->first();
        
        if ($barang) {
            $riwayat = $barang->riwayat_peminjaman ?? [];
            
            foreach ($riwayat as &$p) {
                if (($p['id'] ?? '') === $request->id) {
                    if ($request->has('nama_peminjam')) {
                        $p['nama_peminjam'] = $request->nama_peminjam;
                    }
                    if ($request->has('tanggal_pengembalian')) {
                        $p['tanggal_kembali'] = $request->tanggal_pengembalian;
                    }
                    break;
                }
            }
            
            $barang->update(['riwayat_peminjaman' => $riwayat]);
        }

        return redirect()->route('peminjamanBarang.index')
            ->with('toast_success', 'Data berhasil diupdate');
    }

    /**
     * Konfirmasi pengembalian
     * 
     * CARA KERJA:
     * 1. Cari barang & peminjaman
     * 2. Ubah status jadi 'dikembalikan'
     * 3. Kembalikan stok
     */
    public function confirm($id)
    {
        // Cari barang yang punya peminjaman ini
        $barang = MongoInventaris::where('riwayat_peminjaman._id', $id)
            ->first();
        
        if ($barang) {
            $riwayat = $barang->riwayat_peminjaman ?? [];
            
            foreach ($riwayat as &$p) {
                if (($p['_id'] ?? '') === $id) {
                    // Toggle status
                    if (($p['status'] ?? '') === 'dipinjam') {
                        $p['status'] = 'dikembalikan';
                        $p['tanggal_kembali_aktual'] = now()->toDateTimeString();
                        
                        // Kembalikan stok
                        $barang->increment('jumlah_baik', $p['jumlah'] ?? 0);
                    }
                    break;
                }
            }
            
            $barang->update(['riwayat_peminjaman' => $riwayat]);
        }

        return back()->with('toast_success', 'Status berhasil diubah');
    }

    /**
     * Hapus peminjaman
     */
    public function destroy($id)
    {
        $barang = MongoInventaris::where('riwayat_peminjaman._id', $id)
            ->first();
        
        if ($barang) {
            $riwayat = $barang->riwayat_peminjaman ?? [];
            
            $riwayat = array_filter($riwayat, function($p) use ($id) {
                return ($p['_id'] ?? '') !== $id;
            });
            
            $barang->update(['riwayat_peminjaman' => array_values($riwayat)]);
        }

        return redirect()->route('peminjamanBarang.index')
            ->with('toast_success', 'Data berhasil dihapus');
    }

    /**
     * Riwayat peminjaman (sudah selesai)
     */
    public function history()
    {
        $semuaBarang = MongoInventaris::all();
        $history = [];
        
        foreach ($semuaBarang as $barang) {
            foreach ($barang->riwayat_peminjaman ?? [] as $peminjaman) {
                if (($peminjaman['status'] ?? '') === 'dikembalikan') {
                    $history[] = array_merge($peminjaman, [
                        'nama_barang' => $barang->nama_barang,
                    ]);
                }
            }
        }
        
        return view('pages.humas.data-peminjaman-barang.history', [
            'peminjaman_barang' => collect($history)->sortByDesc('tanggal_kembali_aktual'),
            'title' => 'Riwayat Peminjaman Barang'
        ]);
    }
}