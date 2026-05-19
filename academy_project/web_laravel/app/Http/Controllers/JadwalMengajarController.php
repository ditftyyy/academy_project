<?php

namespace App\Http\Controllers;

use App\Models\MongoDB\User as MongoUser;
use App\Models\MongoDB\Kelas as MongoKelas;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class JadwalMengajarController extends Controller
{
    /**
     * Halaman daftar guru untuk jadwal mengajar
     */
    public function index()
    {
        $gurus = MongoUser::guruAktif()->get();
        
        return view('pages.datajadwalmengajar.jadwal', [
            'guru' => $gurus,
            'title' => 'Jadwal Mengajar'
        ]);
    }

    /**
     * Lihat jadwal mengajar guru yang sedang login
     */
    public function jadwalguru()
    {
        $user = auth()->user();
        
        if (!$user || $user->role !== 'guru') {
            return back()->with('toast_error', 'Anda tidak memiliki data guru yang valid');
        }
        
        $hariIni = strtolower(Carbon::now()->locale('id')->dayName);
        
        // Ambil jadwal mengajar dari schedule guru
        $schedule = $user->schedule ?? [];
        $jadwalHariIni = [];
        
        foreach ($schedule as $s) {
            foreach ($s['jadwal'] ?? [] as $j) {
                if (strtolower($j['hari'] ?? '') === $hariIni) {
                    $jadwalHariIni = array_merge(
                        $jadwalHariIni, 
                        $j['mata_pelajaran'] ?? []
                    );
                }
            }
        }
        
        return view('pages.akademik.data-jadwal-guru.jadwalguru', [
            'all_jadwal' => $jadwalHariIni,
            'all_jadwals' => $schedule,
            'hari_ini' => $hariIni,
            'title' => 'Jadwal Mengajar'
        ]);
    }

    /**
     * Cetak jadwal mengajar PDF
     */
    public function cetak($guruId)
    {
        $guru = MongoUser::findOrFail($guruId);
        
        $hariOrder = ['senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu'];
        $schedule = collect($guru->schedule ?? [])->first();
        
        $pdf = App::make('dompdf.wrapper');
        $pdf->loadView('pages.datajadwalmengajar.cetak_pdf', [
            'guru' => $guru,
            'jadwal' => $schedule['jadwal'] ?? [],
            'hari' => ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu']
        ]);
        
        return $pdf->stream('laporan-jadwal-mengajar-pdf');
    }

    /**
     * Hapus jadwal mengajar
     */
    public function destroy($guruId, $hari, $index)
    {
        $guru = MongoUser::findOrFail($guruId);
        $schedule = $guru->schedule ?? [];
        
        foreach ($schedule as &$s) {
            foreach ($s['jadwal'] ?? [] as &$j) {
                if (($j['hari'] ?? '') === $hari) {
                    if (isset($j['mata_pelajaran'][$index])) {
                        array_splice($j['mata_pelajaran'], $index, 1);
                    }
                    break;
                }
            }
        }
        
        $guru->update(['schedule' => $schedule]);
        
        return redirect()->back()
            ->with('toast_success', 'Data berhasil dihapus!');
    }
}