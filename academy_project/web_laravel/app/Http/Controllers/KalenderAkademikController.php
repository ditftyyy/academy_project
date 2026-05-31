<?php

namespace App\Http\Controllers;

use App\Models\MongoDB\Akademik as MongoAkademik;
use Illuminate\Http\Request;

class KalenderAkademikController extends Controller
{
    public function index()
    {
        $akademik = MongoAkademik::aktif()->first();
        $events = [];

        if ($akademik && !empty($akademik->kalender) && is_array($akademik->kalender)) {
            foreach ($akademik->kalender as $k) {
                $color = ($k['status'] ?? '') === 'masuk' ? '#924ACE' : '#68B01A';
                $events[] = [
                    'id'     => $k['id'] ?? uniqid(),
                    'title'  => $k['title'] ?? '',
                    'start'  => $k['start_date'] ?? '',
                    'end'    => $k['end_date'] ?? '',
                    'status' => $k['status'] ?? 'libur',
                    'color'  => $color,
                ];
            }
        }

        return view('pages.akademik.data-kalender-akademik.kalender', [
            'events' => $events,
            'title'  => 'Kalender Akademik'
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'      => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after_or_equal:start_date',
        ]);

        $akademik = MongoAkademik::aktif()->first();
        if (!$akademik) {
            return response()->json(['error' => 'Tidak ada tahun ajaran aktif'], 400);
        }

        // Generate id unik
        $eventId = uniqid();

        $akademik->tambahEvent([
            'id'         => $eventId,
            'title'      => $request->title,
            'start_date' => $request->start_date,
            'end_date'   => $request->end_date,
            'status'     => $request->status ?? 'libur',
        ]);

        return response()->json([
            'id'    => $eventId,
            'start' => $request->start_date,
            'end'   => $request->end_date,
            'title' => $request->title,
            'color' => '#68B01A',
        ]);
    }

    public function update(Request $request, $eventId)
    {
        $akademik = MongoAkademik::aktif()->first();
        if (!$akademik) {
            return response()->json(['error' => 'Tidak ada tahun ajaran aktif'], 404);
        }

        $success = $akademik->updateEvent($eventId, [
            'start_date' => $request->start_date,
            'end_date'   => $request->end_date,
        ]);

        return $success
            ? response()->json('Event updated')
            : response()->json(['error' => 'Event not found'], 404);
    }

    public function destroy($eventId)
    {
        $akademik = MongoAkademik::aktif()->first();
        if (!$akademik) {
            return response()->json(['error' => 'Tidak ada tahun ajaran aktif'], 404);
        }

        $success = $akademik->hapusEvent($eventId);
        return $success
            ? response()->json($eventId)
            : response()->json(['error' => 'Event not found'], 404);
    }
}