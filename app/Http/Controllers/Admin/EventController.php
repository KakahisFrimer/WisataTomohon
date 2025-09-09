<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Event;
use Illuminate\Support\Facades\Storage;

class EventController extends Controller
{
    public function index(Request $request)
    {
        // Data untuk kalender (semua event dalam format JSON)
        $allEvents = Event::select('id', 'title', 'category', 'start_date', 'end_date')
            ->get()
            ->map(function ($e) {
                return [
                    'id'       => $e->id,
                    'title'    => $e->title,
                    'category' => $e->category,
                    'start'    => \Carbon\Carbon::parse($e->start_date)->format('Y-m-d'),
                    'end'      => $e->end_date 
                        ? \Carbon\Carbon::parse($e->end_date)->format('Y-m-d') 
                        : \Carbon\Carbon::parse($e->start_date)->format('Y-m-d'),
                ];
            });

        // Query untuk tabel data event
        $query = Event::query();

        if ($request->filled('search')) {
            $query->where('title', 'LIKE', "%{$request->search}%");
        }

        if ($request->filled('showCategory') && $request->showCategory !== 'Semua') {
            $query->where('category', $request->showCategory);
        }

        if ($request->filled('filterStatus') && $request->filterStatus !== 'Semua') {
            $query->where('status', $request->filterStatus);
        }

        if ($request->filled('sortBy')) {
            $query->orderBy($request->sortBy, 'asc');
        } else {
            $query->orderBy('start_date', 'desc');
        }

        $events = $query->paginate(10)->withQueryString();

        $upcomingEvents = Event::where('start_date', '>=', now()->toDateString())
        ->orderBy('start_date', 'asc')
        ->limit(5)
        ->get();


        // Kirim dua variabel ke view: $events (tabel) & $allEvents (kalender)
        return view('admin.data-event', compact('events', 'allEvents'));
    }

    public function create()
    {
        return view('admin.create-event');
    }

    public function store(Request $request)
{
    $request->validate([
        'title'       => 'required|string|max:255',
        'start_date'  => 'required|date',
        'end_date'    => 'nullable|date|after_or_equal:start_date',
        'status'      => 'nullable',
        'foto__event.*' => 'nullable|image|mimes:jpg,jpeg,png|max:4096',
    ]);

    $today = now()->toDateString();
    $start = $request->start_date;
    $end   = $request->end_date ?? $request->start_date;

    if ($end < $today) {
        $status = 'Selesai';
    } elseif ($start > $today) {
        $status = 'Akan Datang';
    } elseif ($start <= $today && $end >= $today) {
        $status = 'Dimulai';
    } else {
        $status = 'Akan Datang';
    }

    $fotoPaths = [];
        if ($request->hasFile('foto__event')) {
            foreach ($request->file('foto__event') as $file) {
                $fotoPaths[] = $file->store('event', 'public');
            }
        }

        // misalnya simpan json di DB
        $foto = !empty($fotoPaths) ? json_encode($fotoPaths) : 'event/default-16x9.jpg';


    Event::create([
        'title'       => $request->title,
        'description' => $request->description ?? '-',
        'category'    => $request->category ?? '-',
        'location'    => $request->location ?? '-',
        'start_date'  => $start,
        'end_date'    => $end,
        'status'      => $status,
        'foto__event' => $foto,
    ]);

    return redirect()->route('admin.events.index')->with('success', 'Event berhasil ditambahkan!');
}


    public function edit($id)
    {
        $event = Event::findOrFail($id);
        return view('admin.edit-event', compact('event'));
    }

    public function update(Request $request, $id)
{
    $event = Event::findOrFail($id);

    $request->validate([
        'title'       => 'required|string|max:255',
        'start_date'  => 'required|date',
        'end_date'    => 'nullable|date|after_or_equal:start_date',
        'status'      => 'nullable',
        'foto__event.*' => 'nullable|image|mimes:jpg,jpeg,png|max:4096',
    ]);

    $today = now()->toDateString();
    $start = $request->start_date;
    $end   = $request->end_date ?? $request->start_date;

    if ($end < $today) {
        $status = 'Selesai';
    } elseif ($start > $today) {
        $status = 'Akan Datang';
    } elseif ($start <= $today && $end >= $today) {
        $status = 'Dimulai';
    } else {
        $status = 'Akan Datang';
    }

    $foto = $event->foto__event;
    if ($request->hasFile('foto__event')) {
        if ($event->foto__event && $event->foto__event !== 'event/default-16x9.jpg') {
            Storage::delete('public/' . $event->foto__event);
        }
        $foto = $request->file('foto__event')->store('event', 'public');
    }

    $event->update([
        'title'       => $request->title,
        'description' => $request->description ?? '-',
        'category'    => $request->category ?? '-',
        'location'    => $request->location ?? '-',
        'start_date'  => $start,
        'end_date'    => $end,
        'status'      => $status,
        'foto__event' => $foto,
    ]);

    return redirect()->route('admin.events.index')->with('success', 'Event berhasil diupdate!');
}


    public function destroy($id)
    {
        $event = Event::findOrFail($id);

        if ($event->foto__event && $event->foto__event !== 'event/default-16x9.jpg') {
            Storage::delete('public/' . $event->foto__event);
        }

        $event->delete();

        return redirect()->route('admin.events.index')->with('success', 'Event berhasil dihapus!');
    }

    public function updateStatus(Request $request, $id)
    {
        $event = Event::findOrFail($id);
        $event->update(['status' => $request->status]);

        return back()->with('success', 'Status event berhasil diperbarui!');
    }
}
