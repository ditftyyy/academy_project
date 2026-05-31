<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Student;

class AIController extends Controller
{
    /**
     * Analisis clustering untuk semua siswa
     */
    public function analyzeClusters()
    {
        // Ambil semua data siswa dari collection 'students'
        $students = Student::select('_id', 'name', 'math_score', 'reading_score', 'writing_score')->get();
        
        if ($students->isEmpty()) {
            return redirect()->back()->with('error', 'Tidak ada data siswa.');
        }
        
        $payload = [
            'students' => $students->map(function ($student) {
                return [
                    'id'            => (string) $student->_id,
                    'name'          => $student->name,
                    'math_score'    => $student->math_score,
                    'reading_score' => $student->reading_score,
                    'writing_score' => $student->writing_score,
                ];
            })->toArray()
        ];
        
        try {
            $response = Http::post('http://127.0.0.1:5000/cluster', $payload);
            
            if ($response->successful()) {
                $result = $response->json();
                
                // Update cluster untuk setiap siswa di MongoDB
                foreach ($result['students'] as $studentData) {
                    Student::where('_id', $studentData['student_id'])
                        ->update(['cluster' => $studentData['cluster']]);
                }
                
                return redirect()->back()->with('ai_result', $result);
            } else {
                return redirect()->back()->with('error', 'AI server error: ' . $response->body());
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal konek ke AI server. Pastikan Flask berjalan di port 5000. Detail: ' . $e->getMessage());
        }
    }

    /**
     * Prediksi untuk satu siswa (tanpa menyimpan ke DB)
     */
    public function predictSingle(Request $request)
    {
        $request->validate([
            'math_score'    => 'required|numeric|min:0|max:100',
            'reading_score' => 'required|numeric|min:0|max:100',
            'writing_score' => 'required|numeric|min:0|max:100',
        ]);

        try {
            $response = Http::post('http://127.0.0.1:5000/predict', [
                'math_score'    => $request->math_score,
                'reading_score' => $request->reading_score,
                'writing_score' => $request->writing_score,
            ]);

            if ($response->successful()) {
                $result = $response->json();
                return redirect()->back()->with('prediction_result', $result['prediction']);
            } else {
                return redirect()->back()->with('error', 'Prediksi gagal: ' . $response->body());
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal konek ke AI server. Pastikan Flask berjalan. Detail: ' . $e->getMessage());
        }
    }
}