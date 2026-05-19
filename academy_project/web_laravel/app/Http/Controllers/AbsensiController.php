<?php

namespace App\Http\Controllers;

use App\Models\MongoDB\User as MongoUser;
use App\Models\MongoDB\Kelas as MongoKelas;
use App\Models\MongoDB\Akademik as MongoAkademik;
use App\Models\MongoDB\Pengumuman as MongoPengumuman;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class AbsensiController extends Controller
{
    /**
     * ============================================
     * CATATAN UNTUK PEMULA:
     * Controller ini mengelola ABSENSI kehadiran
     * siswa dan guru.
     * 
     * Di MongoDB, absensi disimpan sebagai ARRAY
     * 'attendances' di dalam dokumen user.
     * 
     * Contoh struktur attendances:
     * [
     *   {
     *     "tanggal": "2026-04-28",
     *     "status": "masuk",
     *     "role": "siswa",
     *     "file_path": null,
     *     "created_at": "2026-04-28 07:30:00"
     *   }
     * ]
     * 
     * CARA KERJA SIMPAN ABSENSI:
     * 1. Cari user by ID
     * 2. Push data baru ke array attendances
     * 3. User->save() (otomatis update di MongoDB)
     * ============================================
     */

    /**
     * Halaman absensi admin
     * Menampilkan SEMUA absensi siswa dan guru
     * 
     * Route: GET /absensi/admin
     */
    public function showAbsensiAdmin()
    {
        // Ambil SEMUA user yang punya absensi
        $allUsers = MongoUser::where('attendances', 'exists', true)
            ->where('attendances', 'not', [])  // tidak kosong
            ->get();
        
        // Pisahkan siswa dan guru
        $siswaAbsensi = [];
        $guruAbsensi = [];
        
        foreach ($allUsers as $user) {
            foreach ($user->attendances ?? [] as $absen) {
                $data = array_merge($absen, [
                    'user_id' => $user->_id,
                    'nama' => $user->nama_lengkap,
                    'role' => $user->role,
                    'kelas' => $user->siswa_data['kelas']['nama'] ?? '-',
                    'nis' => $user->siswa_data['nis'] ?? '-',
                    'nip' => $user->guru_data['nip'] ?? '-',
                ]);
                
                if ($user->role === 'siswa') {
                    $siswaAbsensi[] = $data;
                } elseif ($user->role === 'guru') {
                    $guruAbsensi[] = $data;
                }
            }
        }
        
        // Urutkan: terbaru di atas
        $siswaAbsensi = collect($siswaAbsensi)->sortByDesc('created_at')->values();
        $guruAbsensi = collect($guruAbsensi)->sortByDesc('created_at')->values();
        
        return view('pages.akademik.absensi.absensi-admin', [
            'siswaAbsensis' => $siswaAbsensi,
            'guruAbsensis' => $guruAbsensi,
            'title' => 'Absensi Admin'
        ]);
    }

    /**
     * Hapus absensi berdasarkan ID user + index absensi
     * 
     * Route: DELETE /absensi/{userId}/{absensiIndex}
     * 
     * CARA KERJA:
     * 1. Cari user by ID
     * 2. Hapus absensi pada index tertentu dari array
     * 3. Simpan kembali
     */
    public function deleteAbsensi($userId, $absensiIndex)
    {
        try {
            $user = MongoUser::findOrFail($userId);
            
            $attendances = $user->attendances ?? [];
            
            // Hapus absensi pada index tertentu
            if (isset($attendances[$absensiIndex])) {
                array_splice($attendances, $absensiIndex, 1);
                $user->attendances = array_values($attendances);
                $user->save();
            }
            
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false, 
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Ambil detail absensi by ID user
     * 
     * Route: GET /absensi/{userId}
     */
    public function getAbsensiById($userId)
    {
        try {
            $user = MongoUser::with('attendances')->findOrFail($userId);
            
            return response()->json([
                'success' => true, 
                'data' => [
                    'user' => [
                        'id' => $user->_id,
                        'nama' => $user->nama_lengkap,
                        'role' => $user->role,
                        'kelas' => $user->siswa_data['kelas']['nama'] ?? null,
                        'nip' => $user->guru_data['nip'] ?? null,
                    ],
                    'attendances' => $user->attendances ?? [],
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error: ' . $e->getMessage());
            return response()->json([
                'success' => false, 
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update absensi (ganti status)
     * 
     * Route: PUT /absensi/{userId}/{absensiIndex}
     * 
     * CARA KERJA:
     * 1. Cari user
     * 2. Update status di array attendances pada index tertentu
     * 3. Simpan
     */
    public function updateAbsensi(Request $request, $userId, $absensiIndex)
    {
        try {
            $user = MongoUser::findOrFail($userId);
            
            $attendances = $user->attendances ?? [];
            
            if (isset($attendances[$absensiIndex])) {
                // Update status
                $attendances[$absensiIndex]['status'] = $request->status_absen ?? $attendances[$absensiIndex]['status'];
                
                // Update file jika ada
                if ($request->has('file_path')) {
                    $attendances[$absensiIndex]['file_path'] = $request->file_path;
                }
                
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
     * Halaman absensi siswa
     * 
     * Route: GET /absensi/siswa
     */
    public function showAbsensiSiswa(Request $request)
    {
        $siswas = MongoUser::where('role', 'siswa')->get();
        
        // Kumpulkan semua absensi siswa
        $allAbsensi = [];
        foreach ($siswas as $siswa) {
            foreach ($siswa->attendances ?? [] as $absen) {
                $allAbsensi[] = array_merge($absen, [
                    'user_id' => $siswa->_id,
                    'nama' => $siswa->nama_lengkap,
                    'kelas' => $siswa->siswa_data['kelas']['nama'] ?? '-',
                ]);
            }
        }
        
        $allAbsensi = collect($allAbsensi)->sortByDesc('created_at')->values();
        
        if ($request->ajax()) {
            return response()->json($allAbsensi);
        }
        
        return view('pages.akademik.absensi.absensi-siswa', [
            'absensis' => $allAbsensi,
            'title' => 'Absensi Siswa'
        ]);
    }

    /**
     * Halaman absensi guru
     * 
     * Route: GET /absensi/guru
     */
    public function showAbsensiGuru(Request $request)
    {
        $gurus = MongoUser::where('role', 'guru')->get();
        
        // Kumpulkan semua absensi guru
        $allAbsensi = [];
        foreach ($gurus as $guru) {
            foreach ($guru->attendances ?? [] as $absen) {
                $allAbsensi[] = array_merge($absen, [
                    'user_id' => $guru->_id,
                    'nama' => $guru->nama_lengkap,
                    'nip' => $guru->guru_data['nip'] ?? '-',
                ]);
            }
        }
        
        $allAbsensi = collect($allAbsensi)->sortByDesc('created_at')->values();
        
        if ($request->ajax()) {
            return response()->json($allAbsensi);
        }
        
        return view('pages.akademik.absensi.absensi-guru', [
            'absensis' => $allAbsensi,
            'title' => 'Absensi Guru'
        ]);
    }

    /**
     * Simpan absensi (dari user yang login)
     * 
     * Route: POST /absensi
     * 
     * CARA KERJA LENGKAP:
     * 1. Validasi input
     * 2. Cek apakah user sudah absen hari ini
     * 3. Jika belum, push absensi baru ke array
     * 4. Upload file PDF jika ada (surat sakit/izin)
     * 5. Simpan user
     */
    public function store(Request $request)
    {
        // Log untuk debugging
        Log::info('Absensi store request data:', $request->all());
        
        // Validasi
        $request->validate([
            'status_absen' => 'required|in:masuk,sakit,izin',
            'role' => 'required',
            'id_user' => 'required',
            'file' => 'nullable|mimes:pdf|max:5120',
        ]);
        
        $userId = $request->input('id_user');
        $today = now()->format('Y-m-d');
        
        // Cari user
        $user = MongoUser::find($userId);
        
        if (!$user) {
            return response()->json(['message' => 'User tidak ditemukan'], 404);
        }
        
        // Cek apakah user sudah absen hari ini
        $sudahAbsen = false;
        foreach ($user->attendances ?? [] as $absen) {
            if (($absen['tanggal'] ?? '') === $today) {
                $sudahAbsen = true;
                break;
            }
        }
        
        if ($sudahAbsen) {
            Log::info('Presensi hari ini sudah ada untuk user ' . $userId);
            return response()->json([
                'message' => 'Anda telah melakukan presensi pada hari ini'
            ], 400);
        }
        
        // Siapkan data absensi baru
        $absensiBaru = [
            'tanggal' => $today,
            'status' => $request->input('status_absen'),
            'role' => $request->input('role'),
            'file_path' => null,
            'created_at' => now()->toDateTimeString(),
        ];
        
        // Push ke array attendances
        $attendances = $user->attendances ?? [];
        $attendances[] = $absensiBaru;
        
        // Handle upload file PDF
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            Log::info('Uploaded file name: ' . $file->getClientOriginalName());
            
            // Simpan file
            $fileName = 'absensi_' . $user->_id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $file->storeAs('absensi_files', $fileName, 'public');
            
            // Update file_path di absensi terbaru
            $lastIndex = count($attendances) - 1;
            $attendances[$lastIndex]['file_path'] = $fileName;
            
            Log::info('File path saved: ' . $fileName);
        }
        
        // Simpan ke user
        $user->attendances = $attendances;
        $user->save();
        
        return response()->json([
            'message' => 'Data absensi berhasil disimpan'
        ], 201);
    }

    /**
     * Simpan absensi (dari admin)
     * Admin bisa mencatatkan absensi untuk siswa/guru lain
     * 
     * Route: POST /absensi/admin
     * 
     * CARA KERJA:
     * 1. Validasi input
     * 2. Cari user berdasarkan nama & role
     * 3. Catat absensi ke user tersebut
     */
    public function storeAdmin(Request $request)
    {
        Log::info('Absensi store admin request data:', $request->all());
        
        try {
            // Validasi
            $request->validate([
                'status_absen' => 'required|in:masuk,sakit,izin',
                'role' => 'required|in:siswa,guru',
                'nama_siswa' => 'required',
                'file' => 'nullable|mimes:pdf|max:5120',
            ]);
            
            $namaUser = $request->input('nama_siswa');
            $selectedRole = $request->input('role');
            
            // Cari user berdasarkan nama dan role
            $user = $this->cariUserByNamaRole($namaUser, $selectedRole);
            
            if (!$user) {
                return response()->json([
                    'message' => 'User tidak ditemukan'
                ], 404);
            }
            
            $today = now()->format('Y-m-d');
            
            // Push absensi baru
            $attendances = $user->attendances ?? [];
            $attendances[] = [
                'tanggal' => $today,
                'status' => $request->input('status_absen'),
                'role' => $selectedRole,
                'file_path' => null,
                'created_at' => now()->toDateTimeString(),
                'created_by' => 'admin',
            ];
            
            // Handle upload file
            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $fileName = 'absensi_' . $user->_id . '_' . time() . '.pdf';
                $file->storeAs('absensi_files', $fileName, 'public');
                
                $lastIndex = count($attendances) - 1;
                $attendances[$lastIndex]['file_path'] = $fileName;
            }
            
            $user->attendances = $attendances;
            $user->save();
            
            return response()->json([
                'message' => 'Data absensi berhasil disimpan'
            ], 201);
            
        } catch (\Exception $e) {
            Log::error('Error in storeAdmin:', ['error' => $e->getMessage()]);
            return response()->json([
                'message' => 'Terjadi kesalahan saat menyimpan data absensi'
            ], 500);
        }
    }

    /**
     * Helper: Cari user berdasarkan nama dan role
     * 
     * Di MongoDB, kita bisa cari langsung di field embedded
     */
    private function cariUserByNamaRole($nama, $role)
    {
        if ($role === 'siswa') {
            return MongoUser::where('role', 'siswa')
                ->where('siswa_data.nama', $nama)
                ->orWhere('profile.nama_lengkap', $nama)
                ->first();
        } elseif ($role === 'guru') {
            return MongoUser::where('role', 'guru')
                ->where('guru_data.nama', $nama)
                ->orWhere('profile.nama_lengkap', $nama)
                ->first();
        }
        
        return null;
    }

    /**
     * Cek dan isi otomatis absensi untuk hari yang terlewat
     * 
     * CARA KERJA:
     * 1. Cek 7 hari ke belakang (kecuali Sabtu-Minggu)
     * 2. Jika ada hari tanpa absensi, isi "tidak masuk"
     * 3. Jika sudah lewat jam 4 sore dan belum absen hari ini, isi "tidak masuk"
     */
    public function checkAndFillAbsentData()
    {
        Log::info('checkAndFillAbsentData dijalankan pada ' . now());
        
        $userId = Auth::id();
        $user = MongoUser::find($userId);
        
        if (!$user) {
            return response()->json(['success' => false], 404);
        }
        
        $dataInserted = false;
        $attendances = $user->attendances ?? [];
        
        // Cek 8 hari ke belakang
        $endDate = now()->subDay();
        $startDate = $endDate->copy()->subDays(8);
        
        while ($startDate <= $endDate) {
            $dayOfWeek = $startDate->dayOfWeek;
            $tanggalCek = $startDate->format('Y-m-d');
            
            // Skip Sabtu (6) dan Minggu (0)
            if ($dayOfWeek != 6 && $dayOfWeek != 0) {
                // Cek apakah sudah ada absensi di tanggal ini
                $sudahAbsen = false;
                foreach ($attendances as $absen) {
                    if (($absen['tanggal'] ?? '') === $tanggalCek) {
                        $sudahAbsen = true;
                        break;
                    }
                }
                
                // Jika belum, isi otomatis "tidak masuk"
                if (!$sudahAbsen) {
                    $attendances[] = [
                        'tanggal' => $tanggalCek,
                        'status' => 'tidak masuk',
                        'role' => $user->role,
                        'created_at' => $tanggalCek . ' 16:00:00',
                        'auto_filled' => true,
                    ];
                    $dataInserted = true;
                }
            }
            
            $startDate->addDay();
        }
        
        // Cek hari ini (jika sudah lewat jam 4 sore)
        $today = now()->format('Y-m-d');
        $dayOfWeekToday = now()->dayOfWeek;
        $sudahAbsenHariIni = false;
        
        foreach ($attendances as $absen) {
            if (($absen['tanggal'] ?? '') === $today) {
                $sudahAbsenHariIni = true;
                break;
            }
        }
        
        $disablePresensi = false;
        
        if (!$sudahAbsenHariIni && now()->format('H:i:s') >= '16:00:00' && $dayOfWeekToday != 6 && $dayOfWeekToday != 0) {
            $attendances[] = [
                'tanggal' => $today,
                'status' => 'tidak masuk',
                'role' => $user->role,
                'created_at' => now()->toDateTimeString(),
                'auto_filled' => true,
            ];
            $dataInserted = true;
            $disablePresensi = true;
        }
        
        // Simpan jika ada perubahan
        if ($dataInserted) {
            $user->attendances = $attendances;
            $user->save();
        }
        
        return response()->json([
            'success' => true,
            'dataInserted' => $dataInserted,
            'disablePresensiOption' => $disablePresensi,
        ]);
    }

    /**
     * Tambah event khusus (weekend/libur) - disimpan di collection pengumuman
     */
    public function tambahEvent(Request $request)
    {
        $data = $request->validate([
            'tanggal' => 'required|date',
            'status' => 'required|in:weekend,libur',
            'keterangan' => 'nullable|string',
        ]);
        
        // Simpan sebagai pengumuman type 'keterangan_absensi'
        MongoPengumuman::create([
            'title' => $data['status'] === 'weekend' ? 'Akhir Pekan' : 'Libur',
            'message' => $data['keterangan'] ?? '',
            'role' => 'semua',
            'type' => 'keterangan_absensi',
            'data_tambahan' => [
                'tanggal' => $data['tanggal'],
                'status' => $data['status'],
                'keterangan' => $data['keterangan'] ?? '',
            ],
            'created_by' => auth()->id(),
        ]);
        
        return redirect()->back()->with('success', 'Event berhasil ditambahkan.');
    }

    /**
     * Ambil daftar tanggal weekend/libur dari database
     */
    public function getEventsFromDatabase()
    {
        Log::info('Fetching events from database...');
        
        try {
            $events = MongoPengumuman::where('type', 'keterangan_absensi')
                ->whereIn('data_tambahan.status', ['weekend', 'libur'])
                ->get();
            
            $weekendDates = $events->pluck('data_tambahan.tanggal')
                ->filter()
                ->values()
                ->toArray();
            
            Log::info('Filtered weekend dates:', $weekendDates);
            
            return response()->json($weekendDates);
        } catch (\Exception $e) {
            Log::error('Error fetching events: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to fetch events'
            ], 500);
        }
    }

    /**
     * Halaman absensi (index)
     */
    public function index()
    {
        $akademiks = MongoAkademik::all();
        
        // Ambil tahun ajaran unik
        $tahunAjaran = $akademiks->pluck('tahun_ajaran')->unique()->values();
        
        return view('pages.akademik.absensi.absensi', [
            'akademiks' => $tahunAjaran,
            'title' => 'Absensi'
        ]);
    }

    /**
     * Halaman absensi per kelas
     */
    public function showKelasAbsensi(Request $request, $tahunAkademik, $kelasNama)
    {
        $tahunAkademik = str_replace('-', '/', $tahunAkademik);
        
        // Cari kelas
        $kelasList = MongoKelas::where('nama_kelas', 'like', $kelasNama . '%')
            ->where('deleted', false)
            ->get();
        
        if (count($kelasList) < 1) {
            abort(404);
        }
        
        // Pilih kelas
        $selectedKelas = $request->selected_kelas ?? $kelasList->first()->nama_kelas;
        $selectedSemester = $request->selected_semester ?? 'ganjil';
        
        // Ambil siswa di kelas tersebut
        $siswas = MongoUser::where('role', 'siswa')
            ->where('siswa_data.kelas.nama', $selectedKelas)
            ->get();
        
        // Kumpulkan absensi
        $absensiList = [];
        foreach ($siswas as $siswa) {
            foreach ($siswa->attendances ?? [] as $absen) {
                $absensiList[] = array_merge($absen, [
                    'user_id' => $siswa->_id,
                    'nama' => $siswa->nama_lengkap,
                ]);
            }
        }
        
        $absensiList = collect($absensiList)->sortByDesc('created_at')->values();
        
        return view('pages.akademik.absensi.absensi-kelas', [
            'kelas_list' => $kelasList,
            'selected_kelas' => $selectedKelas,
            'selected_semester' => $selectedSemester,
            'list_status' => ['tidak masuk', 'masuk', 'sakit', 'izin', 'telat'],
            'absensis' => $absensiList,
            'title' => 'Absensi - ' . $selectedKelas
        ]);
    }

    /**
     * API Update absensi (dari halaman absensi kelas)
     */
    public function apiUpdateAbsensi(Request $request, $userId, $absensiIndex)
    {
        $user = MongoUser::findOrFail($userId);
        
        $attendances = $user->attendances ?? [];
        
        if (isset($attendances[$absensiIndex])) {
            $attendances[$absensiIndex]['status'] = $request->status;
            
            if ($request->status == 'izin' && $request->has('keterangan_izin')) {
                $attendances[$absensiIndex]['keterangan'] = $request->keterangan_izin;
            }
            
            $user->attendances = $attendances;
            $user->save();
        }
        
        return back()->with('toast_success', 'Absensi berhasil diupdate');
    }
}