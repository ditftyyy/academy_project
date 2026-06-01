<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Student;
use Illuminate\Support\Facades\Storage;

class StudentsTableSeeder extends Seeder
{
    public function run()
    {
        // Pastikan file CSV ada di storage/app/public/students.csv
        $csvFile = storage_path('app/public/students.csv');
        
        if (!file_exists($csvFile)) {
            $this->command->error("File CSV tidak ditemukan di: $csvFile");
            return;
        }

        $file = fopen($csvFile, 'r');
        if (!$file) {
            $this->command->error("Gagal membuka file CSV");
            return;
        }

        // Baca header (kolom pertama)
        $header = fgetcsv($file);
        
        // Mapping kolom berdasarkan urutan di file CSV
        // Kolom: gender, race/ethnicity, parental level of education, lunch, test preparation course, math score, reading score, writing score
        $genderIdx = 0;
        $raceIdx = 1;
        $mathIdx = 5;
        $readingIdx = 6;
        $writingIdx = 7;

        $count = 0;
        while (($row = fgetcsv($file)) !== false) {
            // Bersihkan data (hilangkan spasi, konversi ke integer)
            $math = (int) trim($row[$mathIdx]);
            $reading = (int) trim($row[$readingIdx]);
            $writing = (int) trim($row[$writingIdx]);
            
            // Buat nama unik (gunakan nomor urut agar lebih rapi)
            $count++;
            $name = 'Siswa ' . $count;
            
            Student::create([
                'name'            => $name,
                'gender'          => trim($row[$genderIdx]),
                'race_ethnicity'  => trim($row[$raceIdx]),
                'math_score'      => $math,
                'reading_score'   => $reading,
                'writing_score'   => $writing,
                'cluster'         => null, // akan diisi setelah analisis AI
            ]);
        }
        fclose($file);
        
        $this->command->info("Berhasil mengimpor $count siswa dari CSV.");
    }
}