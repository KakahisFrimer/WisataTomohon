<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TourismPlace;
use App\Models\Event;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
{
    // Total wisata
    $totalWisata = TourismPlace::count();

    // Hitung jumlah per kategori
    $categories = TourismPlace::select('category', DB::raw('COUNT(*) as total'))
        ->groupBy('category')
        ->get();

    // Data untuk map
    $places = TourismPlace::select(
        'id__tmpt',
        'name',
        'address',
        'category',
        'foto__tmpt',
        'latitude',
        'longitude'
    )->get();

    $upcomingEvents = Event::where('start_date', '>=', now()->toDateString())
        ->orderBy('start_date', 'asc')
        ->limit(10)
        ->get();

    $allEvents = Event::select('id','title','start_date','end_date','category')->get();

    // ✅ Ambil 3 destinasi terbaru
    $latestPlaces = TourismPlace::orderBy('id__tmpt', 'desc')
        ->take(5)
        ->get();

    // Kirim semua data ke view
    return view('admin.dashboard', [
        'totalWisata'    => $totalWisata,
        'categories'     => $categories,
        'places'         => $places,
        'upcomingEvents' => $upcomingEvents,
        'allEvents'      => $allEvents,
        'latestPlaces'   => $latestPlaces,
    ]);
}


}
