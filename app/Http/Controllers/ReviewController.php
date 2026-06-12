<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Review;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Models\TourismPlace;

class ReviewController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'wisata_id'   => 'required|exists:tbl__wisata,id__tmpt',
            'rating'      => 'required|integer|min:1|max:5',
            'review_text' => 'nullable|string|max:500',
            'review_image'=> 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        // Ambil review lama jika ada
        $review = Review::where('wisata_id', $request->wisata_id)
                        ->where('user_id', Auth::id())
                        ->first();

        // Validasi tambahan: wajib gambar jika rating <=3 dan tidak ada review lama
        $validator->after(function ($validator) use ($request, $review) {
            if ($request->rating <= 3 && !$request->hasFile('review_image') && (!$review || !$review->image)) {
                $validator->errors()->add('review_image', 'Gambar wajib diunggah jika rating 3 bintang atau kurang.');
            }
        });

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $imagePath = $review?->image ?? null;
        if ($request->hasFile('review_image')) {
            if ($imagePath) {
                Storage::disk('public')->delete($imagePath);
            }
            $imagePath = $request->file('review_image')->store('reviews', 'public');
        }

        Review::updateOrCreate(
            [
                'wisata_id' => $request->wisata_id,
                'user_id'   => Auth::id(),
            ],
            [
                'rating'   => $request->rating,
                'feedback' => $request->review_text,
                'image'    => $imagePath,
            ]
        );

        return back()->with('success', $review ? 'Review berhasil diupdate!' : 'Review berhasil dikirim!');
    }

    public function index($wisata_id)
{
    $place = TourismPlace::findOrFail($wisata_id);

    $selectedPeriod = request('period', '');

    // Ambil query reviews
    $reviewsQuery = Review::where('wisata_id', $wisata_id)->with('user');

    // Filter berdasarkan periode (jika ada request period)
    if ($selectedPeriod !== '') {
        if ($selectedPeriod === '30days') {
            $reviewsQuery->where('created_at', '>=', now()->subDays(30));
        } elseif ($selectedPeriod === '90days') {
            $reviewsQuery->where('created_at', '>=', now()->subDays(90));
        } elseif ($selectedPeriod === '1year') {
            $reviewsQuery->where('created_at', '>=', now()->subYear());
        }
    }

    // Gunakan paginate supaya bisa pakai links()
    $reviews = $reviewsQuery->latest()->paginate(10);

    $totalReviews = $reviews->total();
    $averageRating = $totalReviews > 0 ? round($reviews->avg('rating'), 1) : 0;

    $ratingCounts = [
        5 => Review::where('wisata_id', $wisata_id)->where('rating', 5)->count(),
        4 => Review::where('wisata_id', $wisata_id)->where('rating', 4)->count(),
        3 => Review::where('wisata_id', $wisata_id)->where('rating', 3)->count(),
        2 => Review::where('wisata_id', $wisata_id)->where('rating', 2)->count(),
        1 => Review::where('wisata_id', $wisata_id)->where('rating', 1)->count(),
    ];

    return view('reviews.index', compact(
        'place',
        'reviews',
        'totalReviews',
        'averageRating',
        'ratingCounts',
        'selectedPeriod'
    ));
}

}
