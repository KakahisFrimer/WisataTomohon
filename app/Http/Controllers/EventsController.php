<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class EventsController extends Controller
{
    /**
     * Menampilkan semua event (halaman utama events.blade.php)
     */
    public function index(Request $request)
{
    $search = $request->input('search');
    $categoryFilter = $request->input('category');

    $query = Event::query();

    if ($search) {
        $query->where(function($q) use ($search) {
            $q->where('title', 'LIKE', "%{$search}%")
              ->orWhere('location', 'LIKE', "%{$search}%")
              ->orWhere('category', 'LIKE', "%{$search}%");
        });
    }

    if ($categoryFilter && $categoryFilter !== 'all') {
        $query->where('category', $categoryFilter);
    }

    // Ambil semua kategori unik dari tabel event
    $categories = Event::select('category')->distinct()->pluck('category')->filter()->values();

    // Paginasi biar bisa pakai ->links()
    $events = $query->orderBy('start_date', 'desc')->paginate(9);

    foreach ($events as $event) {
        $photosEvent = json_decode($event->foto__event, true);
            if (is_array($photosEvent) && !empty($photosEvent[0])) {
                $event->first_photo = 'storage/event/' . $photosEvent[0];
            } elseif (!empty($event->foto__event)) {
                $event->first_photo = 'storage/event/' . $event->foto__event;
            } else {
                $event->first_photo = 'storage/event/default-16x9.png';
            }




        $today = now()->toDateString();
        if ($event->end_date && $event->end_date < $today) {
            $event->status = 'Selesai';
        } elseif ($event->start_date > $today) {
            $event->status = 'Akan Datang';
        } else {
            $event->status = 'Dimulai';
        }
    }

    return view('events', compact('events', 'search', 'categories'));
}


    /**
     * Menampilkan detail event (halaman event-detail.blade.php)
     */
    public function show($id)
    {
        $event = Event::findOrFail($id);

        // Ambil foto pertama (kalau array JSON)
        $photosEvent = json_decode($event->foto__event, true);
        $event->first_photo = !empty($photosEvent[0])
            ? 'event/' . $photosEvent[0]
            : 'event/default-16x9.png';

        // Tentukan status event
        $today = now()->toDateString();
        if ($event->end_date && $event->end_date < $today) {
            $event->status = 'Selesai';
        } elseif ($event->start_date > $today) {
            $event->status = 'Akan Datang';
        } else {
            $event->status = 'Dimulai';
        }

        return view('event-detail', compact('event'));
    }
}
