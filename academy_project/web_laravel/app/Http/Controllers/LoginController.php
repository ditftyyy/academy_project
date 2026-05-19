<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\MongoDB\User as MongoUser;

class LoginController extends Controller
{
    /**
     * Halaman login
     */
    public function login()
    {
        return view('pages.auth.login');
    }

    /**
     * Proses autentikasi
     */
    public function authenticating(Request $request)
    {
        $credentials = $request->validate([
            'username' => ['required'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            
            // Set current_role jika belum ada
            if (!$user->current_role) {
                $roles = array_filter(
                    explode(',', $user->role), 
                    fn($r) => $r !== 'root'
                );
                $user->update([
                    'current_role' => $roles[0] ?? $user->role,
                    'is_online' => true,
                    'last_online' => now(),
                ]);
            } else {
                $user->update([
                    'is_online' => true,
                    'last_online' => now(),
                ]);
            }
            
            $request->session()->regenerate();
            
            return redirect()->intended('/dashboard');
        }

        return back()
            ->with('toast_error', 'Username atau Password salah!')
            ->withInput();
    }

    /**
     * Ganti role user
     */
    public function setRole(Request $request)
    {
        $role = $request->role;
        $user = Auth::user();
        
        $availableRoles = explode(',', $user->role);
        
        if (!in_array($role, $availableRoles)) {
            return back()->with('toast_error', 'Gagal mengubah role.');
        }
        
        $user->update(['current_role' => $role]);
        
        return redirect()->route('dashboard')
            ->with('toast_success', 'Kamu sekarang ' . ucfirst($role));
    }

    /**
     * Logout
     */
    public function logout(Request $request)
    {
        $user = Auth::user();
        
        if ($user) {
            $user->update(['is_online' => false]);
        }
        
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect('/login');
    }
}