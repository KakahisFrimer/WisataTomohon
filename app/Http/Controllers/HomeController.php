<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function index()
    {
        $destinations = DB::table('tbl__wisata')
                          ->orderByDesc('id__tmpt')
                          ->limit(4)
                          ->get();

        $accommodations = DB::table('tbl__wisata')
                            ->orderByDesc('id__tmpt')
                            ->limit(4)
                            ->get();

        $culinary = DB::table('tbl__wisata')
                            ->orderByDesc('id__tmpt')
                            ->limit(4)
                            ->get();

        return view('home', compact('destinations', 'accommodations', 'culinary'));
    }
}
