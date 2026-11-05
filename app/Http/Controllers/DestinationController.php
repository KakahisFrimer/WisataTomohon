<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DestinationController extends Controller
{
    public function index(Request $request)
{
    
    $categories = DB::table('tbl__wisata')
        ->select('category')
        ->distinct()
        ->pluck('category');

    $categoryFilter = $request->input('category'); 
    $search = $request->input('search');

    $query = DB::table('tbl__wisata')
    ->select(
        'id__tmpt',
        'slug',
        'name',
        'address',
        'category',
        'foto__tmpt'
    )
    ->orderBy('slug');

    if (!empty($categoryFilter) && $categoryFilter !== 'all') {
        $query->where('category', $categoryFilter);
    }

    if (!empty($search)) {
        $query->where(function($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('address', 'like', "%{$search}%")
              ->orWhere('category', 'like', "%{$search}%");
        });
    }

    $destinations = $query->paginate(9);

    $destinations->appends($request->only(['category', 'search']));

    return view('destinations', compact('destinations', 'categories', 'categoryFilter'));
}

}
