<?php

namespace App\Http\Controllers;

use App\Models\MongoDB\Pengumuman as MongoPengumuman;
use App\Models\MongoDB\User as MongoUser;
use Illuminate\Http\Request;

class TamuController extends Controller
{
    /**
     * ============================================
     * CATATAN UNTUK PEMULA:
     * Controller ini mengelola BUKU TAMU.
     * 
     * Di MongoDB, data tamu disimpan di
     * collection 'pengumuman' dengan type='tamu'.
     * 
     * Field 'data_tambahan' menyimpan detail tamu
     * seperti nama, alamat, tujuan, status.
     * ============================================
     */

    /**
     * Ambil username berdasarkan role (untuk dropdown tujuan)
     */
    public function getUsernamesByRole($role)
{
    // Cari user berdasarkan role
    $users = MongoUser::where('role', $role)->get();
    
    // Format data untuk dropdown
    $formattedUsers = $users->map(function ($user) {
        // Ambil nama dari guru_data, siswa_data, atau profile
        $nama = $user->guru_data['nama'] 
            ?? $user->siswa_data['nama'] 
            ?? $user->profile['nama_lengkap'] 
            ?? $user->username;
        
        return [
            'nama' => $nama,
            'username' => $user->username,
        ];
    });
    
    return response()->json($formattedUsers);
}

    /**
     * Halaman daftar tamu (untuk admin)
     */
    public function create()
    {
        $userRoles = MongoUser::select('role')
            ->distinct()
            ->where('role', '!=', 'root,admin')
            ->get()
            ->pluck('role');
        
        return view('pages.humas.tamu', [
            'title' => 'Daftar Tamu',
            'tamu' => MongoPengumuman::byType('tamu')->get(),
            'userRoles' => $userRoles,
        ]);
    }

    /**
     * Simpan tamu baru (dari admin)
     */
    public function kirim(Request $request)
{
    // Catat tamu tanpa tujuan (isi default)
    MongoPengumuman::catatTamu([
        'nama' => $request->namaTamu,
        'alamat' => $request->alamatTamu,
        'tujuan' => 'Umum',               // Nilai default
        'keterangan' => $request->keteranganTamu,
    ]);
    
    // (Opsional) Jika ingin tetap menyimpan data tambahan, bisa diabaikan
    // karena tidak ada user tujuan yang dipilih.
    
    return redirect('/data-tamu')
        ->with('toast_success', 'Data tamu berhasil disimpan');
}

    /**
     * Halaman data tamu
     */
    public function index()
    {
        return view('pages.humas.data-tamu', [
            'title' => 'Data Tamu',
            'tamus' => MongoPengumuman::byType('tamu')
                ->orderBy('created_at', 'desc')
                ->get(),
        ]);
    }

    /**
     * Halaman edit tamu
     */
    public function edit($id)
    {
        $tamu = MongoPengumuman::findOrFail($id);
        
        $userRoles = MongoUser::select('role')
            ->distinct()
            ->where('role', '!=', 'root,admin')
            ->get()
            ->pluck('role');
        
        return view('pages.humas.tamu-edit', [
            'tamu' => $tamu,
            'userRoles' => $userRoles,
            'title' => 'Update Data Tamu'
        ]);
    }

    /**
     * Update data tamu
     */
    public function update(Request $request, $id)
{
    $tamu = MongoPengumuman::findOrFail($id);
    
    $dataTambahan = $tamu->data_tambahan ?? [];
    $dataTambahan['nama_tamu'] = $request->namaTamu;
    $dataTambahan['alamat'] = $request->alamatTamu;
    // Hapus atau beri default untuk 'tujuan' jika diperlukan
    $dataTambahan['tujuan'] = $request->Opsi ?? $dataTambahan['tujuan'] ?? 'Umum';
    $dataTambahan['keterangan'] = $request->keteranganTamu;
    
    // Tidak perlu mengupdate user tujuan karena field tidak ada
    
    $tamu->update([
        'message' => $request->keteranganTamu ?? '',
        'data_tambahan' => $dataTambahan,
    ]);

    return redirect('/data-tamu')
        ->with('toast_success', 'Data tamu berhasil diupdate');
}

    /**
     * Hapus data tamu
     */
    public function delete($id)
    {
        $tamu = MongoPengumuman::findOrFail($id);
        $tamu->delete();

        return redirect('/data-tamu')
            ->with('toast_success', 'Data Tamu Berhasil di Hapus');
    }

    /**
     * Halaman daftar tamu (untuk user biasa/resepsionis)
     */
    public function daftar()
    {
        $userRoles = MongoUser::select('role')
            ->distinct()
            ->where('role', '!=', 'root,admin')
            ->get()
            ->pluck('role');
        
        return view('pages.humas.daftar-tamu', [
            'userRoles' => $userRoles,
            'tamu' => MongoPengumuman::byType('tamu')->get(),
            'title' => 'Buku Tamu'
        ]);
    }

    /**
     * Simpan tamu (dari user biasa/resepsionis)
     */
    public function store(Request $request)
{
    // Catat tamu tanpa memerlukan tujuan (isi default)
    MongoPengumuman::catatTamu([
        'nama' => $request->namaTamu,
        'alamat' => $request->alamatTamu,
        'tujuan' => 'Umum',               // nilai default (bisa diisi 'Tanpa tujuan')
        'keterangan' => $request->keteranganTamu,
    ]);

    // (Opsional) Tidak perlu mengupdate user tujuan karena tidak ada

    return redirect('/login')
        ->with('toast_success', 'Data tamu berhasil disimpan');
}

    /**
 * Update status tamu (diterima/selesai)
 */
public function updateStatus(Request $request, $id)
{
    $tamu = MongoPengumuman::findOrFail($id);
    $tamu->updateStatusTamu($request->status);
    return back()->with('toast_success', 'Status tamu berhasil diupdate');
}
}