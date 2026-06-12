<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DestinationController extends Controller
{
    public function index(Request $request)
{
    // Ambil semua kategori unik
    $categories = DB::table('tbl__wisata')
        ->select('category')
        ->distinct()
        ->pluck('category');

    // Ambil filter dari request
    $categoryFilter = $request->input('category'); 
    $search = $request->input('search');

    // Query dasar (semua destinasi)
    $query = DB::table('tbl__wisata')
        ->select('id__tmpt', 'name', 'address', 'category', 'foto__tmpt')
        ->orderBy('id__tmpt');

    // Jika kategori ada & bukan 'all', filter sesuai kategori
    if (!empty($categoryFilter) && $categoryFilter !== 'all') {
        $query->where('category', $categoryFilter);
    }

    // Jika ada pencarian
    if (!empty($search)) {
        $query->where(function($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('address', 'like', "%{$search}%")
              ->orWhere('category', 'like', "%{$search}%");
        });
    }

    // Ambil hasil paginasi
    $destinations = $query->paginate(9);

    // Pertahankan parameter search dan category
    $destinations->appends($request->only(['category', 'search']));

    // Kirim ke view
    return view('destinations', compact('destinations', 'categories', 'categoryFilter'));
}

}
