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
     * API: Ambil semua kelas (untuk dropdown admin)
     */
    public function getKelas()
    {
        $kelas = MongoKelas::kelasAktif()->pluck('nama_kelas');
        return response()->json($kelas);
    }

    /**
     * API: Ambil semua nama guru (untuk dropdown)
     */
    public function getGuruNames()
    {
        $gurus = MongoUser::where('role', 'guru')->get();
        $names = [];
        foreach ($gurus as $g) {
            $nama = $g->guru_data['nama'] ?? $g->profile['nama_lengkap'] ?? '';
            if ($nama) $names[] = $nama;
        }
        return response()->json($names);
    }

    /**
     * API: Ambil nama siswa berdasarkan kelas
     */
    public function getSiswaByKelas(Request $request)
    {
        $kelasNama = $request->query('kelas');
        if (!$kelasNama) return response()->json([]);
        
        $siswas = MongoUser::where('role', 'siswa')
            ->where('siswa_data.kelas.nama', $kelasNama)
            ->get();
        
        $names = [];
        foreach ($siswas as $s) {
            $nama = $s->siswa_data['nama'] ?? $s->profile['nama_lengkap'] ?? '';
            if ($nama) $names[] = $nama;
        }
        return response()->json($names);
    }

    /**
     * Halaman absensi admin
     */
    public function showAbsensiAdmin()
    {
        $allUsers = MongoUser::whereRaw(['attendances' => ['$ne' => []]])
            ->where('attendances', 'exists', true)
            ->get();
        
        $siswaAbsensi = [];
        $guruAbsensi = [];
        
        foreach ($allUsers as $user) {
            foreach ($user->attendances ?? [] as $index => $absen) {
                $data = array_merge($absen, [
                    'user_id' => $user->_id,
                    'index'   => $index,
                    'nama'    => $user->nama_lengkap,
                    'role'    => $user->role,
                    'kelas'   => $user->siswa_data['kelas']['nama'] ?? '-',
                    'nis'     => $user->siswa_data['nis'] ?? '-',
                    'nip'     => $user->guru_data['nip'] ?? '-',
                ]);
                
                if ($user->role === 'siswa') {
                    $siswaAbsensi[] = $data;
                } elseif ($user->role === 'guru') {
                    $guruAbsensi[] = $data;
                }
            }
        }
        
        $siswaAbsensi = collect($siswaAbsensi)->sortByDesc('created_at')->values();
        $guruAbsensi  = collect($guruAbsensi)->sortByDesc('created_at')->values();
        
        return view('pages.akademik.absensi.absensi-admin', [
            'siswaAbsensis' => $siswaAbsensi,
            'guruAbsensis'  => $guruAbsensi,
            'title'         => 'Absensi Admin'
        ]);
    }

    /**
     * Hapus absensi
     */
    public function deleteAbsensi($userId, $absensiIndex)
    {
        try {
            $user = MongoUser::findOrFail($userId);
            $attendances = $user->attendances ?? [];
            if (isset($attendances[$absensiIndex])) {
                array_splice($attendances, $absensiIndex, 1);
                $user->attendances = array_values($attendances);
                $user->save();
            }
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Ambil detail absensi
     */
    public function getAbsensiById($userId)
    {
        try {
            $user = MongoUser::findOrFail($userId);
            return response()->json([
                'success' => true,
                'data' => [
                    'user' => [
                        'id'    => $user->_id,
                        'nama'  => $user->nama_lengkap,
                        'role'  => $user->role,
                        'kelas' => $user->siswa_data['kelas']['nama'] ?? null,
                    ],
                    'attendances' => $user->attendances ?? [],
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Update status absensi (via admin)
     */
    public function updateAbsensi(Request $request, $userId, $absensiIndex)
    {
        try {
            $user = MongoUser::findOrFail($userId);
            $attendances = $user->attendances ?? [];
            if (isset($attendances[$absensiIndex])) {
                $attendances[$absensiIndex]['status'] = $request->status_absen;
                $user->attendances = $attendances;
                $user->save();
            }
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false], 500);
        }
    }

    /**
     * Simpan absensi (untuk user yang login)
     */
    public function store(Request $request)
    {
        $request->validate([
            'status_absen' => 'required|in:masuk,sakit,izin',
            'role' => 'required',
            'id_user' => 'required',
            'file' => 'nullable|mimes:pdf|max:5120',
        ]);
        
        $userId = $request->id_user;
        $today = now()->format('Y-m-d');
        $user = MongoUser::find($userId);
        if (!$user) return response()->json(['message' => 'User tidak ditemukan'], 404);
        
        foreach ($user->attendances ?? [] as $absen) {
            if (($absen['tanggal'] ?? '') === $today) {
                return response()->json(['message' => 'Anda telah melakukan presensi hari ini'], 400);
            }
        }
        
        $attendances = $user->attendances ?? [];
        $attendances[] = [
            'tanggal'    => $today,
            'status'     => $request->status_absen,
            'role'       => $request->role,
            'file_path'  => null,
            'created_at' => now()->toDateTimeString(),
        ];
        
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = 'absensi_' . $user->_id . '_' . time() . '.pdf';
            $file->storeAs('absensi_files', $fileName, 'public');
            $lastIndex = count($attendances) - 1;
            $attendances[$lastIndex]['file_path'] = $fileName;
        }
        
        $user->attendances = $attendances;
        $user->save();
        
        return response()->json(['message' => 'Data absensi berhasil disimpan'], 201);
    }

    /**
     * Simpan absensi oleh admin
     */
    public function storeAdmin(Request $request)
    {
        $request->validate([
            'status_absen' => 'required|in:masuk,sakit,izin',
            'role'         => 'required|in:siswa,guru',
            'nama_siswa'   => 'required',
            'file'         => 'nullable|mimes:pdf|max:5120',
        ]);

        Log::info('Upload file attempt', ['hasFile' => $request->hasFile('file'), 'file' => $request->file('file')]);
if ($request->hasFile('file')) {
    $file = $request->file('file');
    Log::info('File details', ['name' => $file->getClientOriginalName(), 'size' => $file->getSize(), 'error' => $file->getError()]);
    // ... simpan file
}
        
        $namaUser = $request->nama_siswa;
        $selectedRole = $request->role;
        $user = $this->cariUserByNamaRole($namaUser, $selectedRole);
        if (!$user) {
            return response()->json(['message' => 'User tidak ditemukan'], 404);
        }
        
        $today = now()->format('Y-m-d');
        $attendances = $user->attendances ?? [];
        $attendances[] = [
            'tanggal'    => $today,
            'status'     => $request->status_absen,
            'role'       => $selectedRole,
            'file_path'  => null,
            'created_at' => now()->toDateTimeString(),
            'created_by' => 'admin',
        ];
        
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = 'absensi_' . $user->_id . '_' . time() . '.pdf';
            $file->storeAs('absensi_files', $fileName, 'public');
            $lastIndex = count($attendances) - 1;
            $attendances[$lastIndex]['file_path'] = $fileName;
        }
        
        $user->attendances = $attendances;
        $user->save();
        
        return response()->json(['message' => 'Data absensi berhasil disimpan'], 201);
    }

    private function cariUserByNamaRole($nama, $role)
    {
        if ($role === 'siswa') {
            return MongoUser::where('role', 'siswa')
                ->where(function($q) use ($nama) {
                    $q->where('siswa_data.nama', $nama)
                      ->orWhere('profile.nama_lengkap', $nama);
                })->first();
        } elseif ($role === 'guru') {
            return MongoUser::where('role', 'guru')
                ->where(function($q) use ($nama) {
                    $q->where('guru_data.nama', $nama)
                      ->orWhere('profile.nama_lengkap', $nama);
                })->first();
        }
        return null;
    }

    /**
     * Halaman absensi siswa (untuk role siswa)
     */
    public function showAbsensiSiswa()
    {
        $siswa = auth()->user();
        $absensis = $siswa->attendances ?? [];
        $absensis = collect($absensis)->sortByDesc('created_at')->values();
        return view('pages.akademik.absensi.absensi-siswa', [
            'absensis' => $absensis,
            'title' => 'Absensi Siswa'
        ]);
    }

    /**
     * Halaman absensi guru (untuk role guru)
     */
    public function showAbsensiGuru()
    {
        $guru = auth()->user();
        $absensis = $guru->attendances ?? [];
        $absensis = collect($absensis)->sortByDesc('created_at')->values();
        return view('pages.akademik.absensi.absensi-guru', [
            'absensis' => $absensis,
            'title' => 'Absensi Guru'
        ]);
    }

    /**
     * Halaman index absensi (pilih tahun akademik)
     */
    public function index()
    {
        $akademiks = MongoAkademik::all();
        $tahunAjaran = $akademiks->pluck('tahun_ajaran')->unique()->values();
        return view('pages.akademik.absensi.absensi', [
            'akademiks' => $tahunAjaran,
            'title' => 'Absensi'
        ]);
    }

    /**
     * Halaman absensi per kelas (untuk admin/guru lihat absensi per kelas)
     */
    public function showKelasAbsensi(Request $request, $tahunAkademik, $kelasNama)
    {
        $tahunAkademik = str_replace('-', '/', $tahunAkademik);
        $kelasList = MongoKelas::where('nama_kelas', 'like', $kelasNama . '%')
            ->where('deleted', false)
            ->get();
        if ($kelasList->isEmpty()) abort(404);
        
        $selectedKelas = $request->selected_kelas ?? $kelasList->first()->nama_kelas;
        $selectedSemester = $request->selected_semester ?? 'ganjil';
        
        $siswas = MongoUser::where('role', 'siswa')
            ->where('siswa_data.kelas.nama', $selectedKelas)
            ->get();
        
        $absensiList = [];
        foreach ($siswas as $siswa) {
            foreach ($siswa->attendances ?? [] as $index => $absen) {
                $absensiList[] = array_merge($absen, [
                    'user_id' => $siswa->_id,
                    'index'   => $index,
                    'nama'    => $siswa->nama_lengkap,
                ]);
            }
        }
        $absensiList = collect($absensiList)->sortByDesc('created_at')->values();
        
        return view('pages.akademik.absensi.absensi-kelas', [
            'kelas_list'       => $kelasList,
            'selected_kelas'   => $selectedKelas,
            'selected_semester'=> $selectedSemester,
            'list_status'      => ['tidak masuk', 'masuk', 'sakit', 'izin', 'telat'],
            'absensis'         => $absensiList,
            'title'            => 'Absensi - ' . $selectedKelas
        ]);
    }

    /**
     * API Update absensi (dari halaman admin dan kelas)
     */
    public function apiUpdateAbsensi(Request $request, $userId, $absensiIndex)
    {
        try {
            $user = MongoUser::findOrFail($userId);
            $attendances = $user->attendances ?? [];
            
            if (isset($attendances[$absensiIndex])) {
                // Update status
                $attendances[$absensiIndex]['status'] = $request->status;
                
                // Jika status izin, simpan keterangan
                if ($request->status == 'izin' && $request->has('keterangan_izin')) {
                    $attendances[$absensiIndex]['keterangan'] = $request->keterangan_izin;
                }
                
                $user->attendances = $attendances;
                $user->save();
                
                return response()->json(['success' => true]);
            }
            return response()->json(['success' => false, 'message' => 'Absensi tidak ditemukan'], 404);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Cek dan isi otomatis absensi untuk hari yang terlewat
     */
    public function checkAndFillAbsentData()
    {
        $userId = Auth::id();
        $user = MongoUser::find($userId);
        if (!$user) return response()->json(['success' => false], 404);
        
        $attendances = $user->attendances ?? [];
        $endDate = now()->subDay();
        $startDate = $endDate->copy()->subDays(8);
        $dataInserted = false;
        
        while ($startDate <= $endDate) {
            $dayOfWeek = $startDate->dayOfWeek;
            $tanggalCek = $startDate->format('Y-m-d');
            if ($dayOfWeek != 6 && $dayOfWeek != 0) {
                $sudahAbsen = false;
                foreach ($attendances as $absen) {
                    if (($absen['tanggal'] ?? '') === $tanggalCek) { $sudahAbsen = true; break; }
                }
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
        
        $today = now()->format('Y-m-d');
        $dayOfWeekToday = now()->dayOfWeek;
        $sudahAbsenHariIni = false;
        foreach ($attendances as $absen) {
            if (($absen['tanggal'] ?? '') === $today) { $sudahAbsenHariIni = true; break; }
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
        try {
            $events = MongoPengumuman::where('type', 'keterangan_absensi')
                ->whereIn('data_tambahan.status', ['weekend', 'libur'])
                ->get();
            $weekendDates = $events->pluck('data_tambahan.tanggal')->filter()->values()->toArray();
            return response()->json($weekendDates);
        } catch (\Exception $e) {
            Log::error('Error fetching events: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to fetch events'], 500);
        }
    }
}