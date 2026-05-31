<?php

namespace App\Http\Controllers;

use App\Models\MongoDB\User as MongoUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\UsersExport;
use App\Import\UsersImport;

class UserController extends Controller
{
    public function index()
    {
        // Hanya tampilkan user yang belum dihapus (deleted = false)
        $users = MongoUser::where('deleted', false)->get();
        return view('pages.administrasi.data-user.index', [
            'users' => $users,
            'title' => 'User Management'
        ]);
    }

    /**
     * Update role user (hanya untuk role admin, guru, siswa)
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'roles' => 'required|array',
            'roles.*' => 'string|in:admin,guru,siswa', // hanya role yang diperbolehkan
        ]);

        $user = MongoUser::findOrFail($id);

        // Cek jika user adalah root, tidak boleh diubah dari sini
        $currentRoles = explode(',', $user->role);
        if (in_array('root', $currentRoles)) {
            return back()->with('toast_error', 'Role root tidak dapat diubah melalui form ini.');
        }

        // Gabungkan role yang dipilih
        $newRoles = $request->roles;
        $newRoles = array_unique($newRoles);
        if (empty($newRoles)) {
            $newRoles = ['siswa'];
        }

        $newRoleString = implode(',', $newRoles);

        // Update current_role
        $currentRole = $user->current_role ?? $user->role;
        if (!in_array($currentRole, $newRoles)) {
            $user->current_role = $newRoles[0];
        }

        $user->role = $newRoleString;
        $user->save();

        return back()->with('toast_success', "Role user {$user->username} berhasil diperbarui.");
    }

    /**
     * Reset password user menjadi sama dengan username
     */
    public function reset(Request $request, $id)
    {
        $user = MongoUser::findOrFail($id);
        $user->password = Hash::make($user->username);
        $user->save();

        return redirect()->route('user_management')
            ->with('toast_success', "Password user {$user->username} berhasil direset.");
    }

    /**
 * Hapus user secara permanen (hard delete)
 */
public function destroy($id)
{
    $user = MongoUser::findOrFail($id);
    
    // Cegah penghapusan root
    $roles = explode(',', $user->role);
    if (in_array('root', $roles)) {
        return redirect()->route('user_management')
            ->with('toast_error', 'User root tidak dapat dihapus.');
    }
    
    // Hapus file foto jika user adalah guru atau siswa
    if ($user->role == 'guru') {
        $foto = $user->guru_data['foto'] ?? $user->profile['foto'] ?? null;
        if ($foto && $foto != 'default_img.png') {
            $path = public_path('storage/guru/img/' . $foto);
            if (File::exists($path)) File::delete($path);
        }
        // hapus signature jika ada
        $signature = $user->guru_data['signature'] ?? null;
        if ($signature && $signature != 'default_signature.png') {
            $pathSig = public_path('storage/guru/signatures/' . $signature);
            if (File::exists($pathSig)) File::delete($pathSig);
        }
    } elseif ($user->role == 'siswa') {
        $foto = $user->siswa_data['foto'] ?? $user->profile['foto'] ?? null;
        if ($foto && $foto != 'default_img.png') {
            $path = public_path('storage/murid/img/' . $foto);
            if (File::exists($path)) File::delete($path);
        }
    }
    
    // Hard delete
    $user->delete();
    
    return redirect()->route('user_management')
        ->with('toast_success', "User {$user->username} berhasil dihapus permanen.");
}

    /**
     * Hapus user (soft delete)
     */
    // public function destroy($id)
    // {
    //     $user = MongoUser::findOrFail($id);
        
    //     // Cegah penghapusan user root
    //     $roles = explode(',', $user->role);
    //     if (in_array('root', $roles)) {
    //         return redirect()->route('user_management')
    //             ->with('toast_error', 'User root tidak dapat dihapus.');
    //     }
        
    //     $user->deleted = true;
    //     $user->save();
        
    //     return redirect()->route('user_management')
    //         ->with('toast_success', "User {$user->username} berhasil dihapus.");
    // }

    public function export()
    {
        return Excel::download(new UsersExport, 'users.xlsx');
    }

    public function showImportForm()
    {
        return view('pages.administrasi.data-user.import_form', ['title' => 'Import User']);
    }

    public function import(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|file|mimes:xlsx,xls',
        ]);

        Excel::import(new UsersImport(), $request->file('excel_file'));

        return redirect()->back()->with('success', 'Data user berhasil diimport.');
    }
}