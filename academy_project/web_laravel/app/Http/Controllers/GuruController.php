<?php

namespace App\Http\Controllers;

use App\Models\MongoDB\User as MongoUser;
use App\Models\MongoDB\Kelas as MongoKelas;
use App\Traits\MongoHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\File;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\UsersExportGuru;

class GuruController extends Controller
{
    use MongoHelper;

    /**
     * Update absensi guru
     */
    public function updateAbsensi(Request $request, $userId)
    {
        try {
            $user = MongoUser::findOrFail($userId);
            
            // Update absensi terbaru
            $attendances = $user->attendances ?? [];
            $lastIndex = count($attendances) - 1;
            
            if ($lastIndex >= 0) {
                $attendances[$lastIndex]['status'] = $request->status_absen;
                $user->attendances = $attendances;
                $user->save();
            }
            
            return response()->json([
                'success' => true, 
                'message' => 'Absensi berhasil diupdate'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false, 
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Ambil guru berdasarkan user ID
     */
    public function getGuruByUser($userId)
    {
        try {
            $guru = MongoUser::where('_id', $userId)
                ->where('role', 'guru')
                ->first(['profile', 'guru_data']);
            
            return response()->json([
                'success' => true, 
                'data' => $guru
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false, 
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Ambil semua guru (JSON)
     */
    public function getGuru()
    {
        $guru = MongoUser::guruAktif()
            ->get(['guru_data.nip', 'guru_data.nama', 'profile'])
            ->map(function($g) {
                return [
                    'id' => $g->_id,
                    'nip' => $g->guru_data['nip'] ?? '',
                    'nama' => $g->guru_data['nama'] ?? $g->nama_lengkap,
                ];
            });
        
        return response()->json($guru);
    }

    /**
     * Ambil nama guru saja
     */
    public function getGuruNames()
    {
        $guruNames = MongoUser::guruAktif()
            ->pluck('guru_data.nama')
            ->filter()
            ->values();
        
        return response()->json($guruNames);
    }

    /**
     * Halaman daftar guru
     */
    public function index()
    {
        $gurus = MongoUser::guruAktif()->get();
        
        return view('pages.administrasi.data-guru.guru', [
            'gurus' => $gurus,
            'title' => 'Data Guru'
        ]);
    }

    /**
     * Halaman tambah guru
     */
    public function create()
    {
        return view('pages.administrasi.data-guru.tambah', [
            'agamas' => ['islam', 'kristen', 'buddha', 'konghucu', 'hindu'],
            'status_gurus' => ['honorer', 'tetap', 'magang'],
            'kelas' => MongoKelas::where('wali_kelas.id', null)->get(),
            'title' => 'Tambah Data Guru'
        ]);
    }

    /**
     * Simpan guru baru
     */
    public function store(Request $request)
    {
        $messages = [
            'regex' => ':attribute harus diisi dengan huruf saja',
            'unique' => 'Data ini sudah digunakan',
            'required' => 'Harap isi kolom',
        ];

        $this->validate($request, [
            'nip' => 'required|unique:users,guru_data.nip',
            'nama' => 'regex:/^[a-zA-Z\s.,]+$/',
            'jenis_kelamin' => 'required',
            'no_telp' => 'required|unique:users,guru_data.no_telp',
            'agama' => 'required',
            'tempat_lahir' => 'required',
            'tanggal_lahir' => 'required|date',
            'alamat' => 'required|array',
            'status' => 'required',
            'signature' => 'required',
            'foto' => 'required',
        ], $messages);

        // Format alamat
        $alamatFix = $this->formatAlamat($request->alamat);

        // Upload foto
        $fileFoto = $this->uploadFile(
            $request->file('foto'), 
            'storage/guru/img/'
        );

        // Handle signature
        $fileSignature = 'default_signature.png';
        if ($request->has('signature')) {
            $signatureData = $request->input('signature');
            $fileSignature = time() . "_{$request->nip}_signature.png";
            $signature = base64_decode(
                preg_replace('#^data:image/\w+;base64,#i', '', $signatureData)
            );
            file_put_contents(
                public_path('storage/guru/signatures/' . $fileSignature), 
                $signature
            );
        }

        // Buat user guru di MongoDB
        $guru = MongoUser::create([
            'username' => $this->generateUsername($request->nip),
            'email' => $this->generateEmail($request->nip, 'guru'),
            'password' => Hash::make($request->nip),
            'role' => 'guru',
            'deleted' => false,
            'is_online' => false,
            'profile' => [
                'nama_lengkap' => $request->nama,
                'jenis_kelamin' => $request->jenis_kelamin,
                'agama' => $request->agama,
                'no_telp' => $request->no_telp,
                'alamat' => $alamatFix,
                'foto' => $fileFoto,
                'signature' => $fileSignature,
            ],
            'guru_data' => [
                'nip' => $request->nip,
                'nama' => $request->nama,
                'no_telp' => $request->no_telp,
                'jenis_kelamin' => $request->jenis_kelamin,
                'agama' => $request->agama,
                'status_pegawai' => $request->status,
                'tempat_lahir' => $request->tempat_lahir,
                'tanggal_lahir' => $request->tanggal_lahir,
                'foto' => $fileFoto,
                'alamat' => $alamatFix,
                'signature' => $fileSignature,
            ],
            'attendances' => [],
            'schedule' => [],
        ]);

        return redirect('/administrasi/guru')
            ->with('toast_success', 'Data Guru Berhasil di Tambahkan');
    }

    /**
     * Halaman edit guru
     */
    public function edit($id)
    {
        $guru = MongoUser::findOrFail($id);
        
        return view('pages.administrasi.data-guru.edit', [
            'agamas' => ['islam', 'kristen', 'buddha', 'konghucu', 'hindu'],
            'status_gurus' => ['honorer', 'tetap', 'magang'],
            'guru' => $guru,
            'title' => 'Update Data Guru'
        ]);
    }

    /**
     * Update data guru
     */
    public function update(Request $request, $id)
    {
        $guru = MongoUser::findOrFail($id);
        
        $messages = [
            'regex' => ':attribute harus diisi dengan huruf saja',
            'unique' => 'Data ini sudah digunakan'
        ];

        $this->validate($request, [
            'nama' => 'regex:/^[a-zA-Z\s.,]+$/',
            'alamat' => 'required|array',
            'status' => 'required',
        ], $messages);

        $alamatFix = $this->formatAlamat($request->alamat);

        // Update profile
        $profile = $guru->profile;
        $profile['nama_lengkap'] = $request->nama;
        $profile['alamat'] = $alamatFix;

        // Update guru_data
        $guruData = $guru->guru_data;
        $guruData['nama'] = $request->nama;
        $guruData['alamat'] = $alamatFix;
        $guruData['status_pegawai'] = $request->status;

        $guru->update([
            'profile' => $profile,
            'guru_data' => $guruData,
        ]);

        return redirect('/administrasi/guru')
            ->with('toast_success', 'Data Guru Berhasil di Ubah');
    }

    /**
     * Hapus guru (soft delete)
     */
    public function destroy($id)
    {
        $guru = MongoUser::findOrFail($id);
        $guru->update(['deleted' => true]);

        return redirect('/administrasi/guru')
            ->with('toast_success', 'Data Guru Berhasil di Hapus');
    }

    /**
     * Export guru ke Excel
     */
    public function export()
    {
        return Excel::download(new UsersExportGuru, 'usersGuru.xlsx');
    }
}