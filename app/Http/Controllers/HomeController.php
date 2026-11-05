<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\TourismPlace;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
        public function index(Request $request)
    {
        if (Auth::check() && Auth::user()->role === 'admin') {
        return redirect()->route('admin.dashboard');
    }

 
        $selectedCategory = $request->input('category', '');

        $user_latitude = $request->input('lat', 1.3240);
        $user_longitude = $request->input('lng', 124.8380);

        
$destinationsQuery = TourismPlace::with(['details', 'reviews'])
    ->withAvg('reviews', 'rating');

if (!empty($selectedCategory)) {
    $destinationsQuery->where('category', $selectedCategory);
}

$destinations = $destinationsQuery
    ->orderByDesc('reviews_avg_rating')
    ->limit(4)
    ->get()
    ->map(function ($place) use ($user_latitude, $user_longitude) {
        $photos = json_decode($place->foto__tmpt, true);
        $place->first_photo = $photos[0] ?? 'wisata/default-16x9.webp';

        $today = strtolower(Carbon::now()->format('l'));
        $detail = $place->details->firstWhere('hari__buka', $today);

        if ($detail) {
            $now = Carbon::now()->format('H:i');
            $place->status_buka = ($now >= $detail->jam__buka && $now <= $detail->jam__tutup) ? 'Buka' : 'Tutup';
            $place->harga_tiket = $detail->harga__tiket ?? '-';
        } else {
            $place->status_buka = '-';
            $place->harga_tiket = '-';
        }

        if (!empty($place->latitude) && !empty($place->longitude)) {
            $earthRadius = 6371;
            $lat1 = deg2rad($user_latitude);
            $lon1 = deg2rad($user_longitude);
            $lat2 = deg2rad($place->latitude);
            $lon2 = deg2rad($place->longitude);
            $dlat = $lat2 - $lat1;
            $dlon = $lon2 - $lon1;
            $a = sin($dlat / 2) ** 2 + cos($lat1) * cos($lat2) * sin($dlon / 2) ** 2;
            $c = 2 * asin(sqrt($a));
            $place->distance = round($earthRadius * $c, 2);
        } else {
            $place->distance = null;
        }

        $place->average_rating = round($place->reviews_avg_rating ?? 0, 2);

        return $place;
    });


$nearestQuery = TourismPlace::with(['details', 'reviews']);

if (!empty($selectedCategory)) {
    $nearestQuery->where('category', $selectedCategory);
}

$nearestDestinations = $nearestQuery
    ->get()
    ->map(function ($place) use ($user_latitude, $user_longitude) {
        $photos = json_decode($place->foto__tmpt, true);
        $place->first_photo = $photos[0] ?? 'wisata/default-16x9.jpg';

        if (!empty($place->latitude) && !empty($place->longitude)) {
            $earthRadius = 6371;
            $lat1 = deg2rad($user_latitude);
            $lon1 = deg2rad($user_longitude);
            $lat2 = deg2rad($place->latitude);
            $lon2 = deg2rad($place->longitude);
            $dlat = $lat2 - $lat1;
            $dlon = $lon2 - $lon1;
            $a = sin($dlat / 2) ** 2 + cos($lat1) * cos($lat2) * sin($dlon / 2) ** 2;
            $c = 2 * asin(sqrt($a));
            $place->distance = round($earthRadius * $c, 2);
        } else {
            $place->distance = null;
        }

        return $place;
    })
    ->filter(fn($p) => $p->distance !== null)
    ->sortBy('distance')
    ->take(4)
    ->values();


        $accommodations = TourismPlace::where('category', 'accommodation')
            ->orderByDesc('id__tmpt')
            ->limit(4)
            ->get()
            ->map(function ($place) {
                $photos = json_decode($place->foto__tmpt, true);
                $place->first_photo = $photos[0] ?? 'wisata/default-16x9.jpg';
                return $place;
            });

        $culinary = TourismPlace::where('category', 'culinary')
            ->orderByDesc('id__tmpt')
            ->limit(4)
            ->get()
            ->map(function ($place) {
                $photos = json_decode($place->foto__tmpt, true);
                $place->first_photo = $photos[0] ?? 'wisata/default-16x9.jpg';
                return $place;
            });


$today = Carbon::today();

$events = Event::orderByRaw("
    CASE
        WHEN start_date <= ? AND (end_date IS NULL OR end_date >= ?) THEN 1
        WHEN start_date > ? THEN 2
        ELSE 3
    END
", [$today, $today, $today])
    ->orderByDesc('start_date')
    ->limit(4)
    ->get()
    ->map(function ($event) use ($today) {
        $photosEvent = json_decode($event->foto__event, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($photosEvent)) {
            $event->first_photo = 'event/' . ($photosEvent[0] ?? 'default-16x9.jpg');
        } else {
            $event->first_photo = $event->foto__event
                ? 'event/' . $event->foto__event
                : 'event/default-16x9.jpg';
        }

        if ($event->end_date && $event->end_date < $today->toDateString()) {
            $event->status = 'Selesai';
        } elseif ($event->start_date > $today->toDateString()) {
            $event->status = 'Akan Datang';
        } else {
            $event->status = 'Dimulai';
        }

        return $event;
    });




        return view('home', compact(
            'destinations',
            'accommodations',
            'culinary',
            'events',
            'nearestDestinations',
            'selectedCategory'
        ));
    }
}