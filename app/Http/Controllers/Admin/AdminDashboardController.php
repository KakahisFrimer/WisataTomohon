<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TourismPlace;
use App\Models\TourismPlaceDetail;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $places = TourismPlace::whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->get([
                'id__tmpt',
                'name',
                'address',
                'category',
                'contact',
                'foto__tmpt',
                'longitude',
                'latitude',
                'description'
            ]);

        $categoryCounts = TourismPlace::select('category', DB::raw('count(*) as total'))
            ->groupBy('category')
            ->pluck('total', 'category');

        $totalWisata = TourismPlace::count();

        $latestPlaces = TourismPlace::orderByDesc('id__tmpt')->take(5)->get([
            'id__tmpt',
            'name',
            'foto__tmpt',
            'category',
            'address'
        ]);

    $upcomingEvents = Event::whereDate('tanggal_event', '>=', now()->toDateString())
        ->orderBy('tanggal_event', 'asc')
        ->take(5)
        ->get();


        $notifications = TourismPlace::where(function($q){
            $q->whereNull('foto__tmpt')->orWhere('foto__tmpt','');
        })->orWhere(function($q){
            $q->whereNull('description')->orWhere('description','');
        })->take(10)->get([
            'id__tmpt','name','foto__tmpt','description','category'
        ]);

        return view('admin.dashboard', compact(
            'places',
            'categoryCounts',
            'totalWisata',
            'latestPlaces',
            'upcomingEvents',
            'notifications'
        ));
    }
}
