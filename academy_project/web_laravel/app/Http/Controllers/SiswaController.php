<?php

namespace App\Http\Controllers;

use App\Models\MongoDB\User as MongoUser;
use App\Models\MongoDB\Kelas as MongoKelas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\File;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\UsersExportSiswa;

class SiswaController extends Controller
{
    /**
     * ============================================
     * CATATAN UNTUK PEMULA:
     * Controller ini mengelola data SISWA.
     * 
     * Di MongoDB, siswa adalah user dengan
     * role='siswa'. Data spesifik siswa disimpan
     * di field 'siswa_data' (embedded document).
     * 
     * Absensi disimpan di array 'attendances'.
     * Nilai disimpan di array 'academic_records'.
     * ============================================
     */

    /**
     * Update absensi siswa
     */
    public function updateAbsensi(Request $request, $userId)
    {
        try {
            $user = MongoUser::findOrFail($userId);
            
            $user->tambahAbsensi(
                $request->status_absen ?? 'tidak masuk',
                $request->file_path ?? null
            );
            
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
     * Ambil siswa berdasarkan user ID
     */
    public function getSiswaByUser($userId)
    {
        try {
            $siswa = MongoUser::where('_id', $userId)
                ->where('role', 'siswa')
                ->first(['profile', 'siswa_data']);
            
            return response()->json([
                'success' => true, 
                'data' => $siswa
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false, 
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Ambil siswa berdasarkan kelas (untuk absensi)
     */
    public function getSiswaKelasAbsensi(Request $request)
    {
        $kelas = $request->query('kelas');
        
        $siswas = MongoUser::where('role', 'siswa')
            ->where('siswa_data.kelas.nama', $kelas)
            ->get();
        
        return response()->json($siswas);
    }

    /**
     * Ambil nama siswa berdasarkan kelas
     */
    public function getSiswaByKelas(Request $request)
    {
        $selectedKelas = $request->query('kelas');
        
        try {
            $siswaList = MongoUser::where('role', 'siswa')
                ->where('siswa_data.kelas.nama', $selectedKelas)
                ->pluck('siswa_data.nama')
                ->filter()
                ->values();
            
            return response()->json($siswaList);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Gagal mengambil daftar siswa.']);
        }
    }

    /**
     * Halaman daftar siswa
     */
    public function index(Request $request)
    {
        $query = MongoUser::where('role', 'siswa')
            ->whereIn('siswa_data.status', ['bukan pindahan', 'pindahan']);
        
        // Filter status
        if ($request->has('status') && $request->status) {
            $query->where('siswa_data.status', $request->status);
        }
        
        // Filter kelas
        if ($request->has('kelas') && $request->kelas) {
            $query->where('siswa_data.kelas.id', $request->kelas);
        }
        
        $siswas = $query->get();
        $kelas = MongoKelas::kelasAktif()->get();
        
        return view('pages.administrasi.data-siswa.siswa', [
            'siswas' => $siswas,
            'kelas' => $kelas,
            'title' => 'Data Siswa'
        ]);
    }

    /**
     * Halaman tambah siswa
     */
    public function create()
    {
        $kelas = MongoKelas::kelasAktif()->get();
        
        return view('pages.administrasi.data-siswa.tambah', [
            'agamas' => ['islam', 'kristen', 'buddha', 'konghucu', 'hindu'],
            'list_kelas' => $kelas,
            'title' => 'Tambah Siswa'
        ]);
    }

    /**
     * Simpan siswa baru
     * 
     * CARA KERJA:
     * 1. Validasi input
     * 2. Upload foto
     * 3. Buat user dengan role='siswa'
     * 4. Simpan data lengkap di field 'siswa_data'
     */
    public function store(Request $request)
    {
        $messages = [
            'regex' => ':attribute harus diisi dengan huruf saja',
            'unique' => 'Data ini sudah digunakan'
        ];

        $validateData = [
            'nama' => 'regex:/^[a-zA-Z\s]+$/',
            'nik' => 'required|unique:users,siswa_data.nik',
            'nis' => 'required|unique:users,siswa_data.nis',
            'nisn' => 'required|unique:users,siswa_data.nisn',
            'tempat_lahir' => 'required',
            'tanggal_lahir' => 'required|date',
            'jenis_kelamin' => 'required',
            'agama' => 'required',
            'nama_ayah' => 'required',
            'nama_ibu' => 'required',
            'nama_wali' => 'required',
            'kelas' => 'required',
            'no_telp' => 'required',
            'status' => 'required',
            'alamat' => 'required',
            'foto' => 'required',
        ];

        if ($request->status == 'pindahan') {
            $validateData['asal_sekolah'] = 'required';
        }

        $this->validate($request, $validateData, $messages);

        // Upload foto
        $fileFoto = 'default_img.png';
        if ($request->hasFile('foto')) {
            $file = $request->file('foto');
            $fileFoto = time() . "_" . $file->getClientOriginalName();
            $file->move(public_path('storage/murid/img'), $fileFoto);
        }

        // Ambil data kelas
        $kelas = MongoKelas::find($request->kelas);
        
        // Cari angkatan berdasarkan tahun masuk
        $tahunMasuk = now()->year;
        $angkatanNama = 'Angkatan ' . $tahunMasuk;

        // Buat user siswa
        $siswa = MongoUser::create([
            'username' => $request->nis,
            'email' => $request->nis . '@student.sch.id',
            'password' => Hash::make($request->nis),
            'role' => 'siswa',
            'deleted' => false,
            'is_online' => false,
            'profile' => [
                'nama_lengkap' => $request->nama,
                'jenis_kelamin' => $request->jenis_kelamin,
                'agama' => $request->agama,
                'no_telp' => $request->no_telp,
                'alamat' => $request->alamat,
                'foto' => $fileFoto,
            ],
            'siswa_data' => [
                'nis' => $request->nis,
                'nisn' => $request->nisn,
                'nik' => $request->nik,
                'nama' => $request->nama,
                'tempat_lahir' => $request->tempat_lahir,
                'tanggal_lahir' => $request->tanggal_lahir,
                'jenis_kelamin' => $request->jenis_kelamin,
                'agama' => $request->agama,
                'no_telp' => $request->no_telp,
                'alamat' => $request->alamat,
                'foto' => $fileFoto,
                'status' => $request->status == 'pindahan' ? 'mutasi' : 'bukan pindahan',
                'orang_tua' => [
                    'nama_ayah' => $request->nama_ayah,
                    'nama_ibu' => $request->nama_ibu,
                    'nama_wali' => $request->nama_wali,
                ],
                'kelas' => $kelas ? [
                    'id' => $kelas->_id,
                    'nama' => $kelas->nama_kelas,
                ] : null,
                'angkatan' => [
                    'nama' => $angkatanNama,
                    'tahun_masuk' => $tahunMasuk,
                ],
                'asal_sekolah' => $request->asal_sekolah ?? null,
            ],
            'academic_records' => [],
            'attendances' => [],
            'schedule' => [],
        ]);

        // Jika pindahan, tambahkan detail
        if ($request->status == 'pindahan') {
            $siswaData = $siswa->siswa_data;
            $siswaData['sekolah_asal'] = $request->asal_sekolah;
            $siswaData['tanggal_masuk'] = now()->format('Y-m-d');
            $siswa->update(['siswa_data' => $siswaData]);
        }

        // Tambahkan siswa ke daftar kelas
        if ($kelas) {
            $kelas->tambahSiswa($siswa->_id);
        }

        return redirect()->route('siswa_main')
            ->with('toast_success', 'Data Siswa Berhasil di Tambahkan');
    }

    /**
     * Halaman edit siswa
     */
    public function edit($id)
    {
        $siswa = MongoUser::findOrFail($id);
        $kelas = MongoKelas::kelasAktif()->get();
        
        return view('pages.administrasi.data-siswa.edit', [
            'siswa' => $siswa,
            'kelas_list' => $kelas,
            'status_siswa' => ['lulus', 'belum lulus', 'mutasi', 'keluar'],
            'title' => 'Edit Data Siswa'
        ]);
    }

    /**
     * Update data siswa
     */
    public function update(Request $request, $id)
    {
        $siswa = MongoUser::findOrFail($id);
        
        $messages = [
            'regex' => ':attribute harus diisi dengan huruf saja',
            'unique' => 'Data ini sudah digunakan'
        ];

        $validateData = [
            'nama' => 'regex:/^[a-zA-Z\s]+$/',
            'nik' => 'required',
            'nis' => 'required',
            'nisn' => 'required',
            'tempat_lahir' => 'required',
            'tanggal_lahir' => 'required|date',
            'jenis_kelamin' => 'required',
            'agama' => 'required',
            'nama_ayah' => 'required',
            'nama_ibu' => 'required',
            'nama_wali' => 'required',
            'kelas' => 'required',
            'no_telp' => 'required',
            'alamat' => 'required',
        ];

        $this->validate($request, $validateData, $messages);

        // Update siswa_data
        $siswaData = $siswa->siswa_data ?? [];
        $siswaData['nis'] = $request->nis;
        $siswaData['nisn'] = $request->nisn;
        $siswaData['nik'] = $request->nik;
        $siswaData['nama'] = $request->nama;
        $siswaData['tempat_lahir'] = $request->tempat_lahir;
        $siswaData['tanggal_lahir'] = $request->tanggal_lahir;
        $siswaData['jenis_kelamin'] = $request->jenis_kelamin;
        $siswaData['agama'] = $request->agama;
        $siswaData['no_telp'] = $request->no_telp;
        $siswaData['alamat'] = $request->alamat;
        $siswaData['status'] = $request->status;
        $siswaData['sekolah_asal'] = $request->asal_sekolah;
        $siswaData['orang_tua'] = [
            'nama_ayah' => $request->nama_ayah,
            'nama_ibu' => $request->nama_ibu,
            'nama_wali' => $request->nama_wali,
        ];

        // Update kelas
        if ($request->kelas) {
            $kelas = MongoKelas::find($request->kelas);
            if ($kelas) {
                $siswaData['kelas'] = [
                    'id' => $kelas->_id,
                    'nama' => $kelas->nama_kelas,
                ];
                
                // Update daftar siswa di kelas
                $kelas->tambahSiswa($id);
            }
        }

        // Update profile
        $profile = $siswa->profile ?? [];
        $profile['nama_lengkap'] = $request->nama;
        $profile['alamat'] = $request->alamat;

        $updateData = [
            'profile' => $profile,
            'siswa_data' => $siswaData,
        ];

        // Upload foto baru jika ada
        if ($request->hasFile('foto')) {
            // Hapus foto lama
            $fotoLama = public_path('storage/murid/img/' . ($siswaData['foto'] ?? ''));
            if ($siswaData['foto'] && File::exists($fotoLama)) {
                File::delete($fotoLama);
            }
            
            // Upload foto baru
            $file = $request->file('foto');
            $fileFoto = time() . "_" . $file->getClientOriginalName();
            $file->move(public_path('storage/murid/img/'), $fileFoto);
            
            $profile['foto'] = $fileFoto;
            $siswaData['foto'] = $fileFoto;
            $updateData['profile'] = $profile;
            $updateData['siswa_data'] = $siswaData;
        }

        $siswa->update($updateData);

        return redirect()->route('siswa_main')
            ->with('toast_success', 'Data Siswa Berhasil di Ubah');
    }

    /**
     * Halaman siswa keluar/lulus
     */
    public function out_page(Request $request)
    {
        $query = MongoUser::where('role', 'siswa')
            ->whereIn('siswa_data.status', ['keluar', 'lulus']);
        
        if ($request->has('nama') && $request->nama) {
            $query->where('profile.nama_lengkap', 'like', '%' . $request->nama . '%');
        }
        
        if ($request->has('status') && $request->status) {
            $query->where('siswa_data.status', $request->status);
        }
        
        $siswas = $query->get();
        
        return view('pages.administrasi.data-siswa.keluar', [
            'siswas' => $siswas,
            'title' => 'Siswa Keluar'
        ]);
    }

    /**
     * Proses siswa keluar/lulus
     */
    public function out(Request $request, $id)
    {
        $siswa = MongoUser::findOrFail($id);
        
        $siswaData = $siswa->siswa_data ?? [];
        $siswaData['status'] = $request->status;
        $siswaData['tanggal_keluar'] = now()->format('Y-m-d');
        
        $siswa->update(['siswa_data' => $siswaData]);

        return redirect()->route('siswa_out')
            ->with('toast_success', 'Status Siswa Berhasil di Ubah');
    }

    /**
     * Hapus siswa
     */
    public function destroy($id)
    {
        $siswa = MongoUser::findOrFail($id);
        
        // Hapus foto
        $foto = $siswa->siswa_data['foto'] ?? '';
        $pathFoto = public_path('storage/murid/img/' . $foto);
        
        if ($foto && File::exists($pathFoto)) {
            File::delete($pathFoto);
        }
        
        $siswa->delete();

        return redirect()->route('siswa_out')
            ->with('toast_success', 'Data Siswa Berhasil di Hapus');
    }

    /**
     * Export siswa ke Excel
     */
    public function export()
    {
        return Excel::download(new UsersExportSiswa, 'usersSiswa.xlsx');
    }
}