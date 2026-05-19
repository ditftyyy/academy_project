<?php

namespace App\Http\Controllers;

use App\Models\MongoDB\Akademik as MongoAkademik;
use Illuminate\Http\Request;

class KalenderAkademikController extends Controller
{
    /**
     * Halaman kalender akademik
     */
    public function index(Request $request)
    {
        $akademik = MongoAkademik::aktif()->first();
        $events = [];
        
        if ($akademik && isset($akademik->kalender)) {
            foreach ($akademik->kalender as $k) {
                $color = ($k['status'] ?? '') === 'masuk' ? '#924ACE' : '#68B01A';
                
                $events[] = [
                    'id' => $k['id'] ?? uniqid(),
                    'title' => $k['title'] ?? '',
                    'start' => $k['start_date'] ?? '',
                    'end' => $k['end_date'] ?? '',
                    'status' => $k['status'] ?? 'masuk',
                    'color' => $color,
                ];
            }
        }
        
        return view('pages.akademik.data-kalender-akademik.kalender', [
            'events' => $events,
            'title' => 'Kalender Akademik'
        ]);
    }

    /**
     * Tambah event ke kalender
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
        ]);
        
        $akademik = MongoAkademik::aktif()->first();
        
        if (!$akademik) {
            return response()->json(['error' => 'Tidak ada tahun ajaran aktif'], 400);
        }
        
        $akademik->tambahEvent([
            'title' => $request->title,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'status' => $request->status ?? 'libur',
        ]);
        
        return response()->json([
            'id' => uniqid(),
            'start' => $request->start_date,
            'end' => $request->end_date,
            'title' => $request->title,
            'color' => '#68B01A',
        ]);
    }

    /**
     * Update event kalender
     */
    public function update(Request $request, $eventId)
    {
        $akademik = MongoAkademik::aktif()->first();
        
        if (!$akademik) {
            return response()->json(['error' => 'Tidak ada tahun ajaran aktif'], 404);
        }
        
        $kalender = $akademik->kalender ?? [];
        
        foreach ($kalender as &$k) {
            if (($k['id'] ?? '') === $eventId) {
                $k['start_date'] = $request->start_date;
                $k['end_date'] = $request->end_date;
                break;
            }
        }
        
        $akademik->update(['kalender' => $kalender]);
        
        return response()->json('Event updated');
    }

    /**
     * Hapus event kalender
     */
    public function destroy($eventId)
    {
        $akademik = MongoAkademik::aktif()->first();
        
        if (!$akademik) {
            return response()->json(['error' => 'Tidak ada tahun ajaran aktif'], 404);
        }
        
        $kalender = $akademik->kalender ?? [];
        
        $kalender = array_filter($kalender, function($k) use ($eventId) {
            return ($k['id'] ?? '') !== $eventId;
        });
        
        $akademik->update(['kalender' => array_values($kalender)]);
        
        return response()->json($eventId);
    }
}