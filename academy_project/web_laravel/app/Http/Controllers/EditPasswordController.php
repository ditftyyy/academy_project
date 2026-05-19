<?php

namespace App\Http\Controllers;

use App\Models\MongoDB\User as MongoUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class EditPasswordController extends Controller
{
    /**
     * ============================================
     * CATATAN UNTUK PEMULA:
     * Controller ini untuk MENGUBAH PASSWORD
     * user yang sedang login.
     * 
     * Di MongoDB, password tetap di-hash
     * menggunakan bcrypt (sama seperti MySQL).
     * ============================================
     */

    /**
     * Halaman ubah password
     * 
     * Route: GET /settings/password
     */
    public function index()
    {
        return view('pages.user.settings.password.ubah')
            ->with('title', 'Ubah Password');
    }

    /**
     * Proses ubah password
     * 
     * Route: POST /settings/password
     * 
     * CARA KERJA:
     * 1. Cek password lama (pakai Hash::check)
     * 2. Cek password baru != password lama
     * 3. Cek konfirmasi password baru
     * 4. Update password di MongoDB
     */
    public function ubah(Request $request, $userId = null)
    {
        // Jika userId tidak dikirim, gunakan user yang sedang login
        if ($userId) {
            $user = MongoUser::findOrFail($userId);
        } else {
            $user = Auth::user();
        }
        
        // 1. Cek password lama
        if (!Hash::check($request->old_password, $user->password)) {
            return back()->with('toast_error', "Password lama yang dimasukkan salah!");
        }
        
        // 2. Cek password baru tidak sama dengan password lama
        if (strcmp($request->old_password, $request->new_password) == 0) {
            return back()->with("toast_error", "Password baru tidak boleh sama dengan password lama!");
        }
        
        // 3. Cek konfirmasi password baru
        if ($request->new_password != $request->new_password_confirm) {
            return back()->with('toast_error', "Konfirmasi password baru salah!");
        }
        
        // 4. Update password
        $user->password = Hash::make($request->new_password);
        $user->save();
        
        return back()->with('toast_success', "Password berhasil diubah!");
    }
}