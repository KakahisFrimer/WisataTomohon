<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TourismPlace;
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

    // Kirim data ke view
    return view('admin.dashboard', [
        'totalWisata' => $totalWisata,
        'categories'  => $categories,
        'places'      => $places
    ]);
}

}
