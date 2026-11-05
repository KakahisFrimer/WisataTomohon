<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Review;
use App\Models\TourismPlace;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ReviewController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'wisata_id'    => 'required|exists:tbl__wisata,id__tmpt',
            'rating'       => 'required|integer|min:1|max:5',
            'review_text'  => 'nullable|string|max:500',
            'review_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
        ]);

        $review = Review::where('wisata_id', $request->wisata_id)
            ->where('user_id', Auth::id())
            ->first();

        $validator->after(function ($validator) use ($request, $review) {
            if (
                $request->rating <= 3 &&
                !$request->hasFile('review_image') &&
                (!$review || !$review->image)
            ) {
                $validator->errors()->add(
                    'review_image',
                    'Gambar wajib diunggah jika rating 3 bintang atau kurang.'
                );
            }
        });

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $imagePath = $review?->image ?? null;

        if ($request->hasFile('review_image')) {

            // hapus foto lama
            if ($imagePath && $imagePath !== 'reviews/default.webp') {

                $oldFile = $_SERVER['DOCUMENT_ROOT'] . '/storage/' . $imagePath;

                if (file_exists($oldFile)) {
                    unlink($oldFile);
                }
            }

            $file = $request->file('review_image');

            $filename = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();

            $destination = $_SERVER['DOCUMENT_ROOT'] . '/storage/reviews';

            if (!file_exists($destination)) {
                mkdir($destination, 0755, true);
            }

            $file->move($destination, $filename);

            $imagePath = 'reviews/' . $filename;
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

        return back()->with(
            'success',
            $review ? 'Review berhasil diupdate!' : 'Review berhasil dikirim!'
        );
    }

    public function index($slug)
    {
        $place = TourismPlace::where('slug', $slug)
            ->firstOrFail();

        $wisata_id = $place->id__tmpt;

        $selectedPeriod = request('period', '');

        $reviewsQuery = Review::where('wisata_id', $wisata_id)
            ->with('user');

        if ($selectedPeriod !== '') {

            if ($selectedPeriod === '30days') {
                $reviewsQuery->where(
                    'created_at',
                    '>=',
                    now()->subDays(30)
                );
            }

            elseif ($selectedPeriod === '90days') {
                $reviewsQuery->where(
                    'created_at',
                    '>=',
                    now()->subDays(90)
                );
            }

            elseif ($selectedPeriod === '1year') {
                $reviewsQuery->where(
                    'created_at',
                    '>=',
                    now()->subYear()
                );
            }
        }

        $reviews = $reviewsQuery
            ->latest()
            ->paginate(10);

        $totalReviews = Review::where('wisata_id', $wisata_id)->count();

        $averageRating = Review::where('wisata_id', $wisata_id)
            ->avg('rating');

        $averageRating = $averageRating
            ? round($averageRating, 1)
            : 0;

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