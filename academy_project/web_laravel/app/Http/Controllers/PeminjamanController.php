<?php

namespace App\Http\Controllers;

use App\Models\MongoDB\Inventaris as MongoInventaris;
use App\Models\MongoDB\Ruang as MongoRuang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PeminjamanController extends Controller
{
    /**
     * ============================================
     * CATATAN UNTUK PEMULA:
     * Controller ini mengelola peminjaman RUANGAN
     * (seperti aula, lab, ruang meeting).
     * 
     * Berbeda dengan PeminjamanBarangController
     * yang mengelola peminjaman BARANG.
     * 
     * Di MongoDB, peminjaman ruangan disimpan
     * sebagai 'riwayat_peminjaman' di dokumen
     * ruangan di collection 'inventaris'.
     * Tapi untuk kemudahan, kita buat RUANGAN
     * juga bisa dipinjam melalui collection 'ruang'.
     * ============================================
     */

    /**
     * Halaman daftar peminjaman ruangan
     */
    public function index()
    {
        // Ambil semua ruangan
        $ruangs = MongoRuang::all();
        
        // Kumpulkan peminjaman aktif
        $peminjamanAktif = [];
        $hariIni = now()->format('Y-m-d');
        
        foreach ($ruangs as $ruang) {
            foreach ($ruang->peminjaman ?? [] as $index => $peminjaman) {
                if (($peminjaman['status'] ?? '') === 'dipinjam') {
                    $peminjamanAktif[] = array_merge($peminjaman, [
                        'ruang_id' => $ruang->_id,
                        'nama_ruang' => $ruang->nama_ruang,
                        'peminjaman_index' => $index,
                    ]);
                }
            }
        }
        
        $peminjamanAktif = collect($peminjamanAktif)
            ->sortByDesc('created_at');
        
        // Peminjaman hari ini
        $hariIniList = $peminjamanAktif->filter(function($p) use ($hariIni) {
            return ($p['tanggal_pinjam'] ?? '') <= $hariIni && 
                   ($p['tanggal_kembali'] ?? '') >= $hariIni;
        });

        return view('pages.humas.peminjaman-ruang.peminjaman', [
            'hariini' => $hariIniList,
            'peminjaman' => $peminjamanAktif,
            'ruang' => $ruangs,
            'title' => 'Data Peminjaman Ruangan'
        ]);
    }

    /**
     * Simpan peminjaman ruangan baru
     * 
     * CARA KERJA:
     * 1. Validasi input
     * 2. Cek apakah ruangan sudah dipinjam di rentang waktu yang sama
     * 3. Upload surat peminjaman
     * 4. Catat peminjaman ke ruangan
     */
    public function store(Request $request)
    {
        // Validasi
        $validator = Validator::make($request->all(), [
            'ruang' => 'required',
            'nama_peminjam' => 'required|string',
            'tgl_peminjaman' => 'required|date',
            'tgl_pengembalian' => 'required|date|after_or_equal:tgl_peminjaman',
            'surat' => 'required|file|mimes:pdf,doc,docx|max:2048',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Cek ketersediaan ruangan
        $ruang = MongoRuang::findOrFail($request->ruang);
        
        foreach ($ruang->peminjaman ?? [] as $peminjaman) {
            if (($peminjaman['status'] ?? '') === 'dipinjam') {
                $pinjamMulai = $peminjaman['tanggal_pinjam'] ?? '';
                $pinjamSelesai = $peminjaman['tanggal_kembali'] ?? '';
                
                // Cek bentrok tanggal
                if (
                    $request->tgl_peminjaman <= $pinjamSelesai && 
                    $request->tgl_pengembalian >= $pinjamMulai
                ) {
                    return redirect()->back()
                        ->with('toast_error', 'Ruangan sudah terpinjam pada rentang waktu tersebut');
                }
            }
        }

        // Upload surat
        $file = $request->file('surat');
        $fileName = time() . '_' . $file->getClientOriginalName();
        $file->storeAs('public/surat', $fileName);

        // Catat peminjaman ke ruangan
        $peminjaman = $ruang->peminjaman ?? [];
        $peminjaman[] = [
            '_id' => (string) new \MongoDB\BSON\ObjectId(),
            'nama_peminjam' => $request->nama_peminjam,
            'tanggal_pinjam' => $request->tgl_peminjaman,
            'tanggal_kembali' => $request->tgl_pengembalian,
            'surat' => $fileName,
            'status' => 'dipinjam',
            'created_at' => now()->toDateTimeString(),
        ];
        
        $ruang->update(['peminjaman' => $peminjaman]);

        return redirect('/data-peminjaman')
            ->with('toast_success', 'Data Peminjaman Berhasil di Tambahkan');
    }

    /**
     * Update peminjaman ruangan
     */
    public function update(Request $request, $ruangId, $peminjamanId)
    {
        $ruang = MongoRuang::findOrFail($ruangId);
        $peminjaman = $ruang->peminjaman ?? [];
        
        foreach ($peminjaman as &$p) {
            if (($p['_id'] ?? '') === $peminjamanId) {
                $p['nama_peminjam'] = $request->nama_peminjam ?? $p['nama_peminjam'];
                $p['tanggal_kembali'] = $request->tgl_pengembalian ?? $p['tanggal_kembali'];
                
                // Upload surat baru jika ada
                if ($request->hasFile('surat')) {
                    $file = $request->file('surat');
                    $fileName = time() . '_' . $file->getClientOriginalName();
                    $file->storeAs('public/surat', $fileName);
                    $p['surat'] = $fileName;
                }
                break;
            }
        }
        
        $ruang->update(['peminjaman' => $peminjaman]);

        return redirect('/data-peminjaman')
            ->with('toast_success', 'Data berhasil diubah');
    }

    /**
     * Konfirmasi pengembalian ruangan
     */
    public function confirm($ruangId, $peminjamanId)
    {
        $ruang = MongoRuang::findOrFail($ruangId);
        $peminjaman = $ruang->peminjaman ?? [];
        
        foreach ($peminjaman as &$p) {
            if (($p['_id'] ?? '') === $peminjamanId) {
                $p['status'] = $p['status'] === 'dipinjam' ? 'dikembalikan' : 'dipinjam';
                
                if ($p['status'] === 'dikembalikan') {
                    $p['tanggal_kembali_aktual'] = now()->toDateTimeString();
                }
                break;
            }
        }
        
        $ruang->update(['peminjaman' => $peminjaman]);

        return back()->with('toast_success', 'Status berhasil diubah');
    }

    /**
     * Hapus peminjaman
     */
    public function destroy($ruangId, $peminjamanId)
    {
        $ruang = MongoRuang::findOrFail($ruangId);
        $peminjaman = $ruang->peminjaman ?? [];
        
        $peminjaman = array_filter($peminjaman, function($p) use ($peminjamanId) {
            return ($p['_id'] ?? '') !== $peminjamanId;
        });
        
        $ruang->update(['peminjaman' => array_values($peminjaman)]);

        return redirect('/data-peminjaman')
            ->with('toast_success', 'Data berhasil dihapus');
    }

    /**
     * Riwayat peminjaman ruangan
     */
    public function history()
    {
        $ruangs = MongoRuang::all();
        $history = [];
        
        foreach ($ruangs as $ruang) {
            foreach ($ruang->peminjaman ?? [] as $p) {
                if (($p['status'] ?? '') === 'dikembalikan') {
                    $history[] = array_merge($p, [
                        'nama_ruang' => $ruang->nama_ruang,
                    ]);
                }
            }
        }
        
        return view('pages.humas.peminjaman-ruang.history', [
            'peminjaman' => collect($history)->sortByDesc('tanggal_kembali_aktual'),
            'title' => 'Riwayat Peminjaman Ruangan'
        ]);
    }
}