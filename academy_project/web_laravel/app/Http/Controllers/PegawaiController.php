<?php

namespace App\Http\Controllers;

use App\Models\MongoDB\User as MongoUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\File;

class PegawaiController extends Controller
{
    /**
     * ============================================
     * CATATAN UNTUK PEMULA:
     * Controller ini untuk mengelola data pegawai
     * (Kepala Sekolah, Wakil, Staff TU, dll).
     * 
     * Di MongoDB, pegawai disimpan di collection
     * 'users' dengan role 'pegawai' atau 'admin'.
     * Data spesifik disimpan di field 'profile'.
     * ============================================
     */

    /**
     * Halaman daftar pegawai
     * 
     * Route: GET /data-pegawai
     */
    public function index()
    {
        // Ambil semua user dengan role pegawai/staff
        $pegawais = MongoUser::whereIn('role', ['admin', 'pegawai', 'staff'])
            ->get();
        
        return view('pages.datapegawai.pegawai', [
            'user' => $pegawais,
            'title' => 'Data Pegawai'
        ]);
    }

    /**
     * Halaman tambah pegawai
     * 
     * Route: GET /data-pegawai-add
     */
    public function create()
    {
        return view('pages.datapegawai.tambah', [
            'title' => 'Tambah Data Pegawai'
        ]);
    }

    /**
     * Simpan pegawai baru
     * 
     * Route: POST /data-pegawai
     * 
     * CARA KERJA:
     * 1. Validasi input (harus diisi, tidak boleh sama)
     * 2. Cek apakah jabatan Kepsek/Waka sudah ada
     * 3. Upload foto jika ada
     * 4. Simpan ke MongoDB collection 'users'
     */
    public function store(Request $request)
    {
        // Pesan error custom
        $messages = [
            'regex' => ':attribute harus diisi dengan huruf saja',
            'unique' => 'Data ini sudah digunakan',
        ];

        // Validasi input
        $this->validate($request, [
            'nama' => 'regex:/^[a-zA-Z\s]+$/',
            'nip' => 'required|unique:users,username',  // username = NIP
            'notelp' => 'required|unique:users,profile.no_telp',
        ], $messages);

        // Cek: Kepala Sekolah tidak boleh duplikat
        if ($request->jabatan == 'Kepala Sekolah') {
            $kepsek = MongoUser::where('profile.jabatan', 'Kepala Sekolah')->first();
            if ($kepsek) {
                return redirect('/data-pegawai-add')
                    ->with('toast_error', 'Data kepala sekolah sudah ada');
            }
        }

        // Cek: Wakil Kepala Sekolah tidak boleh duplikat
        if ($request->jabatan == 'waka') {
            $waka = MongoUser::where('profile.jabatan', 'waka')->first();
            if ($waka) {
                return redirect('/data-pegawai-add')
                    ->with('toast_error', 'Data wakil kepala sekolah sudah ada');
            }
        }

        // Upload foto
        $fileFoto = 'default_img.png';
        if ($request->hasFile('foto')) {
            $file = $request->file('foto');
            $fileFoto = time() . "_" . $file->getClientOriginalName();
            
            // Pindahkan file ke folder public
            $file->move(public_path('assets/img/pegawai'), $fileFoto);
        }

        // Simpan ke MongoDB
        MongoUser::create([
            'username' => $request->nip,
            'email' => $request->nip . '@school.staff.com',
            'password' => Hash::make($request->nip),
            'role' => 'pegawai',
            'deleted' => false,
            'is_online' => false,
            'profile' => [
                'nip' => $request->nip,
                'nama_lengkap' => $request->nama,
                'jenis_kelamin' => $request->jeniskelamin,
                'agama' => $request->agama,
                'no_telp' => $request->notelp,
                'tempat_lahir' => $request->tempatlahir,
                'tanggal_lahir' => $request->tgllahir,
                'foto' => $fileFoto,
                'alamat' => $request->alamat,
                'jabatan' => $request->jabatan,
            ],
        ]);

        return redirect('/data-pegawai')
            ->with('toast_success', 'Data Pegawai Berhasil di Tambahkan');
    }

    /**
     * Halaman edit pegawai
     * 
     * Route: GET /data-pegawai-edit/{id}
     */
    public function edit($id)
    {
        $pegawai = MongoUser::findOrFail($id);
        
        return view('pages.datapegawai.edit', [
            'user' => $pegawai,
            'title' => 'Edit Data Pegawai'
        ]);
    }

    /**
     * Update data pegawai
     * 
     * Route: PUT /data-pegawai-update/{id}
     * 
     * CARA KERJA:
     * 1. Cari pegawai by ID
     * 2. Update data di field 'profile'
     * 3. Jika ada foto baru, upload & hapus foto lama
     */
    public function update(Request $request, $id)
    {
        $pegawai = MongoUser::findOrFail($id);
        
        // Data yang akan diupdate
        $profile = $pegawai->profile ?? [];
        $profile['nip'] = $request->nip;
        $profile['nama_lengkap'] = $request->nama;
        $profile['jenis_kelamin'] = $request->jeniskelamin;
        $profile['agama'] = $request->agama;
        $profile['no_telp'] = $request->notelp;
        $profile['tempat_lahir'] = $request->tempatlahir;
        $profile['tanggal_lahir'] = $request->tgllahir;
        $profile['alamat'] = $request->alamat;
        $profile['jabatan'] = $request->jabatan;
        
        // Update password juga (pakai NIP baru)
        $updateData = [
            'username' => $request->nip,
            'password' => Hash::make($request->nip),
            'profile' => $profile,
        ];
        
        // Jika ada foto baru
        if ($request->hasFile('foto')) {
            // Hapus foto lama
            $fotoLama = public_path('assets/img/pegawai/' . ($profile['foto'] ?? ''));
            if ($profile['foto'] && File::exists($fotoLama)) {
                File::delete($fotoLama);
            }
            
            // Upload foto baru
            $file = $request->file('foto');
            $fileFoto = time() . "_" . $file->getClientOriginalName();
            $file->move(public_path('assets/img/pegawai'), $fileFoto);
            
            $profile['foto'] = $fileFoto;
            $updateData['profile'] = $profile;
        }
        
        $pegawai->update($updateData);
        
        return redirect('/data-pegawai')
            ->with('toast_success', 'Data Pegawai Berhasil di Ubah');
    }

    /**
     * Hapus pegawai
     * 
     * Route: DELETE /data-pegawai-hapus/{id}
     */
    public function destroy($id)
    {
        $pegawai = MongoUser::findOrFail($id);
        
        // Hapus foto dari storage
        $foto = $pegawai->profile['foto'] ?? '';
        $pathFoto = public_path('assets/img/pegawai/' . $foto);
        
        if ($foto && File::exists($pathFoto)) {
            File::delete($pathFoto);
        }
        
        // Hapus dari database
        $pegawai->delete();
        
        return redirect('/data-pegawai')
            ->with('toast_success', 'Data Pegawai Berhasil di Hapus');
    }

    /**
     * Lihat daftar pegawai (versi ringkas)
     */
    public function lihat()
    {
        $pegawais = MongoUser::whereIn('role', ['admin', 'pegawai', 'staff'])->get();
        
        return view('pages.datapegawai.lihat', [
            'user' => $pegawais,
            'title' => 'Lihat Data Pegawai'
        ]);
    }
}