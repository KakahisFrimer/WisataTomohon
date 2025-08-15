<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DestinationController extends Controller
{
    public function index()
    {
        $destinations = DB::table('tbl__wisata')->orderBy('id__tmpt')->get();

        return view('destinations', compact('destinations'));
    }
}
