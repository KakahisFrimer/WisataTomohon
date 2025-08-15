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
        // Ambil semua tempat wisata yang memiliki koordinat
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

        // Hitung jumlah per kategori (category)
        $categoryCounts = TourismPlace::select('category', DB::raw('count(*) as total'))
            ->groupBy('category')
            ->pluck('total', 'category');

        // total wisata
        $totalWisata = TourismPlace::count();

        // 5 terbaru (by id__tmpt atau created_at jika ada) — sesuaikan
        $latestPlaces = TourismPlace::orderByDesc('id__tmpt')->take(5)->get([
            'id__tmpt',
            'name',
            'foto__tmpt',
            'category',
            'address'
        ]);

        // events coming soon (future) — memakai 'tanggal_event'
    $upcomingEvents = Event::whereDate('tanggal_event', '>=', now()->toDateString())
        ->orderBy('tanggal_event', 'asc')
        ->take(5)
        ->get();


        // notifications: tempat tanpa foto atau tanpa deskripsi
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
