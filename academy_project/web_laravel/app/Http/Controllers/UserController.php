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
    /**
     * ============================================
     * CATATAN UNTUK PEMULA:
     * Controller ini untuk ADMIN mengelola
     * user management (role, password).
     * ============================================
     */

    /**
     * Halaman manajemen user
     */
    public function index()
    {
        $users = MongoUser::all();
        
        return view('pages.administrasi.data-user.index', [
            'users' => $users,
            'title' => 'User Management'
        ]);
    }

    /**
     * Update role user
     * 
     * CARA KERJA:
     * 1. Validasi roles yang dikirim
     * 2. Cek apakah user punya role 'root'
     * 3. Gabungkan roles baru
     * 4. Update current_role jika berubah
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'roles' => 'required|array',
        ]);

        $user = MongoUser::findOrFail($id);
        
        // Cek: hanya super admin yang bisa ubah root
        $roleDariDB = explode(',', $user->role);
        
        if (in_array('root', $roleDariDB) && $user->_id != auth()->user()->_id) {
            return back()->with('toast_error', "Anda tidak memiliki akses untuk mengubah data ini");
        }
        
        // Filter role yang valid
        $availableRoles = config('app.DB_user_roles', ['admin', 'guru', 'siswa', 'pegawai', 'tamu']);
        $roleToSubmit = [];
        
        foreach ($request->roles as $role) {
            if (in_array($role, $availableRoles)) {
                $roleToSubmit[] = $role;
            }
        }
        
        // Pertahankan role 'root' jika sebelumnya ada
        if (in_array('root', $roleDariDB) && !in_array('root', $roleToSubmit)) {
            array_unshift($roleToSubmit, 'root');
            if (!in_array('admin', $roleToSubmit)) {
                $roleToSubmit[] = 'admin';
            }
        }
        
        if (count($roleToSubmit) <= 0) {
            return back()->with('toast_error', "Role yang dimasukkan tidak ada yang valid");
        }
        
        // Update
        $updateData = [
            'role' => implode(',', $roleToSubmit),
        ];
        
        // Update current_role jika tidak termasuk dalam role baru
        if (!in_array($user->current_role, $roleToSubmit)) {
            $updateData['current_role'] = $roleToSubmit[0];
        }
        
        $user->update($updateData);

        return back()->with('toast_success', "User: {$user->username} berhasil diperbarui");
    }

    /**
     * Reset password user
     */
    public function reset(Request $request, $id)
    {
        $user = MongoUser::findOrFail($id);
        
        $user->update([
            'password' => Hash::make($user->username),
        ]);

        return redirect()->route('user_management')
            ->with('toast_success', 'Password Berhasil di Reset');
    }

    /**
     * Export user ke Excel
     */
    public function export()
    {
        return Excel::download(new UsersExport, 'users.xlsx');
    }

    /**
     * Halaman import user
     */
    public function showImportForm()
    {
        return view('pages.administrasi.data-user.import_form', [
            'title' => 'Import User'
        ]);
    }

    /**
     * Import user dari Excel
     */
    public function import(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|file|mimes:xlsx,xls',
        ]);
        
        $file = $request->file('excel_file');
        
        Excel::import(new UsersImport(), $file);

        return redirect()->back()
            ->with('success', 'Data imported successfully.');
    }
}