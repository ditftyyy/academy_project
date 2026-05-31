<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;

class StudentDataController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $perPage = $request->input('per_page', 25);
        
        $query = Student::query();
        
        if ($search) {
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('gender', 'like', "%{$search}%")
                  ->orWhere('race_ethnicity', 'like', "%{$search}%");
        }
        
        $students = $query->paginate($perPage);
        
        return view('pages.dataset-students', compact('students', 'search', 'perPage'));
    }
    
    public function exportCsv()
    {
        // Bersihkan semua buffer output
        if (ob_get_level()) ob_end_clean();
        ob_start();
        
        $students = Student::all();
        $filename = 'students_data_' . date('Y-m-d') . '.csv';
        
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: no-cache');
        header('Expires: 0');
        
        $handle = fopen('php://output', 'w');
        fputcsv($handle, ['ID', 'Nama', 'Gender', 'Race/Ethnicity', 'Math Score', 'Reading Score', 'Writing Score', 'Cluster']);
        
        foreach ($students as $s) {
            $clusterName = '';
            if ($s->cluster === 0) $clusterName = 'Berprestasi';
            elseif ($s->cluster === 1) $clusterName = 'Rata-rata';
            elseif ($s->cluster === 2) $clusterName = 'Butuh Bimbingan';
            else $clusterName = '-';
            
            fputcsv($handle, [
                (string) $s->_id,
                $s->name,
                $s->gender ?? '',
                $s->race_ethnicity ?? '',
                $s->math_score,
                $s->reading_score,
                $s->writing_score,
                $clusterName
            ]);
        }
        
        fclose($handle);
        exit;
    }
}