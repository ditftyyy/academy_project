<?php

namespace App\Http\Controllers;

use App\Models\MongoDB\Ruang as MongoRuang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PeminjamanController extends Controller
{
    public function index()
    {
        $ruangs = MongoRuang::all();
        $peminjamanAktif = [];
        $hariIni = now()->format('Y-m-d');

        foreach ($ruangs as $ruang) {
            foreach ($ruang->peminjaman ?? [] as $index => $p) {
                if (($p['status'] ?? 'dipinjam') !== 'dikembalikan') {
                    $peminjamanAktif[] = array_merge($p, [
                        'ruang_id' => $ruang->_id,
                        'nama_ruang' => $ruang->nama_ruang,
                        'peminjaman_index' => $index,
                    ]);
                }
            }
        }

        $peminjamanAktif = collect($peminjamanAktif)->sortByDesc('created_at');
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

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ruang' => 'required',
            'nama_peminjam' => 'required|string',
            'tgl_peminjaman' => 'required|date',
            'tgl_pengembalian' => 'required|date|after_or_equal:tgl_peminjaman',
            'surat' => 'required|file|mimes:pdf,doc,docx|max:2048',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $ruang = MongoRuang::findOrFail($request->ruang);

        foreach ($ruang->peminjaman ?? [] as $p) {
            if (($p['status'] ?? 'dipinjam') !== 'dikembalikan') {
                $pinjamMulai = $p['tanggal_pinjam'] ?? '';
                $pinjamSelesai = $p['tanggal_kembali'] ?? '';
                if ($request->tgl_peminjaman <= $pinjamSelesai && 
                    $request->tgl_pengembalian >= $pinjamMulai) {
                    return back()->with('toast_error', 'Ruangan sudah terpinjam pada rentang waktu tersebut');
                }
            }
        }

        // Upload surat ke public/uploads/surat (tanpa symlink)
        $destinationPath = public_path('uploads/surat');
        if (!file_exists($destinationPath)) {
            mkdir($destinationPath, 0777, true);
        }
        $file = $request->file('surat');
        $fileName = time() . '_' . $file->getClientOriginalName();
        $file->move($destinationPath, $fileName);
        $suratPath = 'uploads/surat/' . $fileName;

        $peminjaman = $ruang->peminjaman ?? [];
        $peminjaman[] = [
            '_id' => (string) new \MongoDB\BSON\ObjectId(),
            'nama_peminjam' => $request->nama_peminjam,
            'tanggal_pinjam' => $request->tgl_peminjaman,
            'tanggal_kembali' => $request->tgl_pengembalian,
            'surat' => $suratPath,
            'status' => 'dipinjam',
            'status_pengajuan' => null,
            'created_at' => now()->toDateTimeString(),
        ];
        $ruang->update(['peminjaman' => $peminjaman]);

        return redirect('/data-peminjaman')->with('toast_success', 'Peminjaman berhasil ditambahkan');
    }

    public function update(Request $request, $ruangId, $peminjamanId)
    {
        $ruang = MongoRuang::findOrFail($ruangId);
        $peminjaman = $ruang->peminjaman ?? [];

        foreach ($peminjaman as &$p) {
            if (($p['_id'] ?? '') === $peminjamanId) {
                $p['nama_peminjam'] = $request->nama_peminjam ?? $p['nama_peminjam'];
                $p['tanggal_kembali'] = $request->tgl_pengembalian ?? $p['tanggal_kembali'];
                if ($request->hasFile('surat')) {
                    $destinationPath = public_path('uploads/surat');
                    if (!file_exists($destinationPath)) {
                        mkdir($destinationPath, 0777, true);
                    }
                    $file = $request->file('surat');
                    $fileName = time() . '_' . $file->getClientOriginalName();
                    $file->move($destinationPath, $fileName);
                    $p['surat'] = 'uploads/surat/' . $fileName;
                }
                break;
            }
        }

        $ruang->update(['peminjaman' => $peminjaman]);
        return redirect('/data-peminjaman')->with('toast_success', 'Peminjaman diperbarui');
    }

    public function approve($id)
    {
        $ruangs = MongoRuang::all();
        foreach ($ruangs as $ruang) {
            $peminjaman = $ruang->peminjaman ?? [];
            foreach ($peminjaman as &$p) {
                if (($p['_id'] ?? '') === $id) {
                    $p['status_pengajuan'] = true;
                    $ruang->update(['peminjaman' => $peminjaman]);
                    return back()->with('toast_success', 'Peminjaman disetujui');
                }
            }
        }
        return back()->with('toast_error', 'Data tidak ditemukan');
    }

    public function decline($id)
    {
        $ruangs = MongoRuang::all();
        foreach ($ruangs as $ruang) {
            $peminjaman = $ruang->peminjaman ?? [];
            foreach ($peminjaman as &$p) {
                if (($p['_id'] ?? '') === $id) {
                    $p['status_pengajuan'] = false;
                    $ruang->update(['peminjaman' => $peminjaman]);
                    return back()->with('toast_success', 'Peminjaman ditolak');
                }
            }
        }
        return back()->with('toast_error', 'Data tidak ditemukan');
    }

    public function complete($ruangId, $peminjamanId)
    {
        $ruang = MongoRuang::findOrFail($ruangId);
        $peminjaman = $ruang->peminjaman ?? [];
        foreach ($peminjaman as &$p) {
            if (($p['_id'] ?? '') === $peminjamanId) {
                $p['status'] = 'dikembalikan';
                $p['tanggal_kembali_aktual'] = now()->toDateTimeString();
                break;
            }
        }
        $ruang->update(['peminjaman' => $peminjaman]);
        return back()->with('toast_success', 'Peminjaman telah dikembalikan');
    }

    public function destroy($ruangId, $peminjamanId)
    {
        $ruang = MongoRuang::findOrFail($ruangId);
        $peminjaman = $ruang->peminjaman ?? [];
        $peminjaman = array_filter($peminjaman, function($p) use ($peminjamanId) {
            return ($p['_id'] ?? '') !== $peminjamanId;
        });
        $ruang->update(['peminjaman' => array_values($peminjaman)]);
        return redirect('/data-peminjaman')->with('toast_success', 'Peminjaman dihapus');
    }
}