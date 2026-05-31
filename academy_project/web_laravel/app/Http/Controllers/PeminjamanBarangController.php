<?php

namespace App\Http\Controllers;

use App\Models\MongoDB\Inventaris as MongoInventaris;
use Illuminate\Http\Request;
use MongoDB\BSON\ObjectId;

class PeminjamanBarangController extends Controller
{
    public function index()
    {
        $semuaBarang = MongoInventaris::all();
        $peminjamanAktif = [];
        $hariIni = now()->format('Y-m-d');

        foreach ($semuaBarang as $barang) {
            foreach ($barang->riwayat_peminjaman ?? [] as $index => $peminjaman) {
                if (($peminjaman['status'] ?? '') === 'dipinjam') {
                    $peminjamanAktif[] = array_merge($peminjaman, [
                        'barang_id' => $barang->_id,
                        'nama_barang' => $barang->nama_barang,
                        'peminjaman_index' => $index,
                    ]);
                }
            }
        }

        $peminjamanAktif = collect($peminjamanAktif)->sortByDesc('created_at')->values();
        $hariIniList = $peminjamanAktif->filter(function($p) use ($hariIni) {
            return ($p['tanggal_pinjam'] ?? '') === $hariIni;
        });

        return view('pages.humas.data-peminjaman-barang.index', [
            'hariini' => $hariIniList,
            'peminjaman' => $peminjamanAktif,
            'barang' => $semuaBarang,
            'title' => 'Data Peminjaman Barang'
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'barang_id' => 'required|exists:inventaris,_id',
            'jumlah' => 'required|integer|min:1',
            'nama_peminjam' => 'required|string',
            'tanggal_peminjaman' => 'required|date',
            'tanggal_pengembalian' => 'required|date|after:tanggal_peminjaman',
            'surat' => 'required|file|mimes:pdf,doc,docx|max:2048',
        ]);

        $barang = MongoInventaris::findOrFail($request->barang_id);
        
        $stokSekarang = (int) $barang->jumlah_baik;
        $jumlahPinjam = (int) $request->jumlah;
        
        if ($stokSekarang < $jumlahPinjam) {
            return back()->with('toast_error', 'Stok tidak mencukupi!');
        }

        // Upload surat
        $dest = public_path('uploads/surat');
        if (!file_exists($dest)) mkdir($dest, 0777, true);
        $file = $request->file('surat');
        $fileName = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $file->getClientOriginalName());
        $file->move($dest, $fileName);
        $suratPath = 'uploads/surat/' . $fileName;

        $riwayat = $barang->riwayat_peminjaman ?? [];
        $riwayat[] = [
            '_id' => (string) new ObjectId(),
            'nama_peminjam' => $request->nama_peminjam,
            'jumlah' => $jumlahPinjam,
            'tanggal_pinjam' => $request->tanggal_peminjaman,
            'tanggal_kembali' => $request->tanggal_pengembalian,
            'surat' => $suratPath,
            'status' => 'dipinjam',
            'status_pengajuan' => null,
            'created_at' => now()->toDateTimeString(),
        ];

        $barang->jumlah_baik = $stokSekarang - $jumlahPinjam;
        $barang->riwayat_peminjaman = $riwayat;
        $barang->save();

        return redirect()->route('peminjamanBarang.index')->with('toast_success', 'Peminjaman berhasil ditambahkan');
    }

    /**
     * Cari inventaris yang memiliki riwayat peminjaman dengan _id tertentu
     */
    private function findInventarisByPeminjamanId($peminjamanId)
    {
        // Gunakan elemMatch untuk pencarian lebih akurat
        return MongoInventaris::where('riwayat_peminjaman', 'elemMatch', ['_id' => $peminjamanId])->first();
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_peminjam' => 'required|string',
            'tanggal_pengembalian' => 'required|date',
            'surat' => 'nullable|file|mimes:pdf,doc,docx|max:2048',
        ]);

        $barang = $this->findInventarisByPeminjamanId($id);
        if (!$barang) {
            return back()->with('toast_error', 'Data peminjaman tidak ditemukan');
        }

        $riwayat = $barang->riwayat_peminjaman;
        $updated = false;
        foreach ($riwayat as &$p) {
            if (($p['_id'] ?? '') === $id) {
                $p['nama_peminjam'] = $request->nama_peminjam;
                $p['tanggal_kembali'] = $request->tanggal_pengembalian;
                if ($request->hasFile('surat')) {
                    $dest = public_path('uploads/surat');
                    if (!file_exists($dest)) mkdir($dest, 0777, true);
                    $file = $request->file('surat');
                    $fileName = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $file->getClientOriginalName());
                    $file->move($dest, $fileName);
                    $p['surat'] = 'uploads/surat/' . $fileName;
                }
                $updated = true;
                break;
            }
        }

        if (!$updated) {
            return back()->with('toast_error', 'Data riwayat tidak ditemukan');
        }

        $barang->riwayat_peminjaman = $riwayat;
        $barang->save();

        return redirect()->route('peminjamanBarang.index')->with('toast_success', 'Data peminjaman berhasil diperbarui');
    }

    public function approve($id)
    {
        $barang = $this->findInventarisByPeminjamanId($id);
        if ($barang) {
            $riwayat = $barang->riwayat_peminjaman;
            foreach ($riwayat as &$p) {
                if (($p['_id'] ?? '') === $id && is_null($p['status_pengajuan'] ?? null)) {
                    $p['status_pengajuan'] = true;
                    break;
                }
            }
            $barang->riwayat_peminjaman = $riwayat;
            $barang->save();
        }
        return redirect()->route('peminjamanBarang.index')->with('toast_success', 'Peminjaman disetujui');
    }

    public function decline($id)
    {
        $barang = $this->findInventarisByPeminjamanId($id);
        if ($barang) {
            $riwayat = $barang->riwayat_peminjaman;
            foreach ($riwayat as &$p) {
                if (($p['_id'] ?? '') === $id && is_null($p['status_pengajuan'] ?? null)) {
                    $p['status_pengajuan'] = false;
                    // Kembalikan stok
                    $stokSekarang = (int) $barang->jumlah_baik;
                    $jumlahPinjam = (int) ($p['jumlah'] ?? 0);
                    $barang->jumlah_baik = $stokSekarang + $jumlahPinjam;
                    break;
                }
            }
            $barang->riwayat_peminjaman = $riwayat;
            $barang->save();
        }
        return redirect()->route('peminjamanBarang.index')->with('toast_success', 'Peminjaman ditolak');
    }

    public function confirm($id)
    {
        $barang = $this->findInventarisByPeminjamanId($id);
        if ($barang) {
            $riwayat = $barang->riwayat_peminjaman;
            foreach ($riwayat as &$p) {
                if (($p['_id'] ?? '') === $id && ($p['status'] ?? '') === 'dipinjam') {
                    $p['status'] = 'dikembalikan';
                    $p['tanggal_kembali_aktual'] = now()->toDateTimeString();
                    $stokSekarang = (int) $barang->jumlah_baik;
                    $jumlahPinjam = (int) ($p['jumlah'] ?? 0);
                    $barang->jumlah_baik = $stokSekarang + $jumlahPinjam;
                    break;
                }
            }
            $barang->riwayat_peminjaman = $riwayat;
            $barang->save();
        }
        return redirect()->route('peminjamanBarang.index')->with('toast_success', 'Barang telah dikembalikan');
    }

    public function destroy($id)
    {
        $barang = $this->findInventarisByPeminjamanId($id);
        if ($barang) {
            $riwayat = $barang->riwayat_peminjaman;
            $newRiwayat = [];
            foreach ($riwayat as $p) {
                if (($p['_id'] ?? '') === $id) {
                    // Kembalikan stok jika masih dipinjam
                    if (($p['status'] ?? '') === 'dipinjam') {
                        $stokSekarang = (int) $barang->jumlah_baik;
                        $jumlahPinjam = (int) ($p['jumlah'] ?? 0);
                        $barang->jumlah_baik = $stokSekarang + $jumlahPinjam;
                    }
                    continue;
                }
                $newRiwayat[] = $p;
            }
            $barang->riwayat_peminjaman = array_values($newRiwayat);
            $barang->save();
        }
        return redirect()->route('peminjamanBarang.index')->with('toast_success', 'Data peminjaman dihapus');
    }
}