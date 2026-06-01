<?php

namespace App\Http\Controllers;

use App\Models\MongoDB\Pengumuman as MongoPengumuman;
use App\Models\MongoDB\User as MongoUser;
use Illuminate\Http\Request;

class TamuController extends Controller
{
    /**
     * Ambil username berdasarkan role (untuk dropdown tujuan)
     */
    public function getUsernamesByRole($role)
    {
        $users = MongoUser::where('role', $role)->get();
        $formattedUsers = $users->map(function ($user) {
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
        MongoPengumuman::catatTamu([
            'nama' => $request->namaTamu,
            'alamat' => $request->alamatTamu,
            'tujuan' => 'Umum',
            'keterangan' => $request->keteranganTamu,
        ]);
        
        return redirect('/data-tamu')->with('toast_success', 'Data tamu berhasil disimpan');
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
        $dataTambahan['tujuan'] = $request->Opsi ?? $dataTambahan['tujuan'] ?? 'Umum';
        $dataTambahan['keterangan'] = $request->keteranganTamu;
        
        $tamu->update([
            'message' => $request->keteranganTamu ?? '',
            'data_tambahan' => $dataTambahan,
        ]);

        return redirect('/data-tamu')->with('toast_success', 'Data tamu berhasil diupdate');
    }

    /**
     * Hapus data tamu (method DELETE)
     */
    public function delete($id)
    {
        $tamu = MongoPengumuman::findOrFail($id);
        $tamu->delete();

        return redirect('/data-tamu')->with('toast_success', 'Data Tamu Berhasil di Hapus');
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
        MongoPengumuman::catatTamu([
            'nama' => $request->namaTamu,
            'alamat' => $request->alamatTamu,
            'tujuan' => 'Umum',
            'keterangan' => $request->keteranganTamu,
        ]);

        return redirect('/login')->with('toast_success', 'Data tamu berhasil disimpan');
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