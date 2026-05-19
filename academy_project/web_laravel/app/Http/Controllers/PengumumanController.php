<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MongoDB\Pengumuman as MongoPengumuman;

class PengumumanController extends Controller
{
    /**
     * ============================================
     * CATATAN UNTUK PEMULA:
     * Controller ini mengelola pengumuman yang
     * muncul di dashboard user.
     * 
     * Di MongoDB, pengumuman disimpan di
     * collection 'pengumuman' dengan field 'role'
     * untuk menentukan siapa yang bisa melihat.
     * ============================================
     */

    /**
     * Halaman buat pengumuman
     */
    public function create()
    {
        return view('pengumumans.create', [
            'title' => 'Buat Pengumuman'
        ]);
    }

    /**
     * Simpan pengumuman baru
     * 
     * CARA KERJA:
     * 1. Cek roles yang dipilih (admin/guru/siswa)
     * 2. Untuk setiap role, buat pengumuman terpisah
     *    (atau buat 1 dengan role 'semua')
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'roles' => 'required|array',
        ]);

        // Jika memilih banyak role, buat pengumuman untuk masing-masing
        // Atau gabung jadi satu dengan role 'semua'
        $roles = $request->input('roles', []);
        
        if (count($roles) === 1) {
            // Buat 1 pengumuman untuk role tertentu
            MongoPengumuman::buat(
                $request->title,
                $request->message,
                $roles[0]
            );
        } else {
            // Buat pengumuman untuk semua role yang dipilih
            foreach ($roles as $role) {
                MongoPengumuman::buat(
                    $request->title,
                    $request->message,
                    $role
                );
            }
        }

        return redirect()->route('dashboard')
            ->with('success', 'Pengumuman berhasil disimpan');
    }

    /**
     * Update pengumuman
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        $pengumuman = MongoPengumuman::findOrFail($id);
        
        $pengumuman->update([
            'title' => $request->title,
            'message' => $request->message,
            'role' => $request->role ?? $pengumuman->role,
        ]);

        return redirect()->back()
            ->with('success', 'Pengumuman berhasil diperbarui.');
    }

    /**
     * Hapus pengumuman
     */
    public function destroy($id)
    {
        $pengumuman = MongoPengumuman::findOrFail($id);
        $pengumuman->delete();

        return redirect()->back()
            ->with('success', 'Pengumuman berhasil dihapus.');
    }
}