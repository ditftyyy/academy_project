<?php

namespace Database\Seeders;

use App\Models\MongoDB\Akademik;
use App\Models\MongoDB\User;
use Illuminate\Database\Seeder;

class AngkatanSeeder extends Seeder
{
    /**
     * ============================================
     * CATATAN UNTUK PEMULA:
     * Seeder ini membuat data TAHUN AJARAN
     * dan ANGKATAN.
     * 
     * Di MongoDB:
     * - Tahun ajaran disimpan di collection 'akademik'
     * - Angkatan disimpan di field 'siswa_data.angkatan'
     *   dalam dokumen user siswa
     * ============================================
     */
    public function run(): void
    {
        $jumlahAngkatan = 4;
        $tahunMulai = now()->year - ($jumlahAngkatan - 1);

        for ($i = 0; $i < $jumlahAngkatan; $i++) {
            $tahunCounter = $tahunMulai;

            // Buat tahun akademik (ganjil & genap)
            foreach (['ganjil', 'genap'] as $semester) {
                $tahunAjaran = $tahunCounter . '/' . ($tahunCounter + 1);

                // Cek apakah sudah ada
                $exists = Akademik::where('tahun_ajaran', $tahunAjaran)
                    ->where('semester', $semester)
                    ->exists();

                if (!$exists) {
                    Akademik::create([
                        'tahun_ajaran' => $tahunAjaran,
                        'semester' => $semester,
                        'selected' => false,
                        'kalender' => [],
                        'konfigurasi' => [],
                    ]);
                }
            }

            $tahunMulai++;
        }

        // Set tahun ajaran aktif berdasarkan bulan sekarang
        $currentMonth = date('m');
        $semester = $currentMonth >= '07' ? 'ganjil' : 'genap';
        $currentYear = now()->year;

        // Nonaktifkan semua
        Akademik::query()->update(['selected' => false]);

        if ($semester === 'ganjil') {
            $tahunAjaranAktif = $currentYear . '/' . ($currentYear + 1);
        } else {
            $tahunAjaranAktif = ($currentYear - 1) . '/' . $currentYear;
        }

        // Aktifkan yang sesuai
        $akademikAktif = Akademik::where('tahun_ajaran', $tahunAjaranAktif)
            ->where('semester', $semester)
            ->first();

        if ($akademikAktif) {
            $akademikAktif->update(['selected' => true]);
            echo "✅ Tahun ajaran aktif: {$tahunAjaranAktif} ({$semester})\n";
        } else {
            // Buat baru jika belum ada
            Akademik::create([
                'tahun_ajaran' => $tahunAjaranAktif,
                'semester' => $semester,
                'selected' => true,
                'kalender' => [],
                'konfigurasi' => [],
            ]);
            echo "✅ Tahun ajaran baru dibuat: {$tahunAjaranAktif} ({$semester})\n";
        }

        echo "✅ Angkatan seeder selesai!\n";
    }
}