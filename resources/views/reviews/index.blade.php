@extends('layouts.app')

@section('content')
@php
    // fallback agar tidak error kalau variabel tidak ada (safety)
    $totalReviews = $totalReviews ?? ($reviews->count() ?? 0);
    $averageRating = $averageRating ?? ($reviews->avg('rating') ? round($reviews->avg('rating'),1) : 0);
    $ratingCounts  = $ratingCounts ?? [
        5 => ($reviews->where('rating',5)->count() ?? 0),
        4 => ($reviews->where('rating',4)->count() ?? 0),
        3 => ($reviews->where('rating',3)->count() ?? 0),
        2 => ($reviews->where('rating',2)->count() ?? 0),
        1 => ($reviews->where('rating',1)->count() ?? 0),
    ];
@endphp

<div class="reviews-wrap">
    <!-- Header -->
    <header class="reviews-header">
        <h1 class="reviews-title">Reviews</h1>
        <div class="reviews-filter">
            <select class="filter-select" name="period" onchange="filterReviewsByPeriod(this.value)">
                <option value="" {{ request('period') == '' ? 'selected' : '' }}>All Time</option>
                <option value="30days" {{ request('period') == '30days' ? 'selected' : '' }}>Last 30 Days</option>
                <option value="90days" {{ request('period') == '90days' ? 'selected' : '' }}>Last 90 Days</option>
                <option value="1year" {{ request('period') == '1year' ? 'selected' : '' }}>Last 1 Year</option>
            </select>
        </div>
    </header>

    <!-- Top Stats -->
    <section class="reviews-stats">
        <div class="stat-box stat-total" style="align-items: center;">
            <div class="stat-label">Total Reviews</div>
            <div class="stat-value">{{ $totalReviews }}</div>
            <div class="stat-sub">Total reviews on this place</div>
        </div>

        <div class="stat-box stat-chart">
            <div class="stat-label" style="display : flex; justify-content: center;">Rating Distribution</div>
            <div class="mini-distribution">
                @foreach([5,4,3,2,1] as $s)
                    @php
                        $count = $ratingCounts[$s] ?? 0;
                        $percent = $totalReviews > 0 ? round(($count / $totalReviews) * 100, 1) : 0;
                    @endphp
                    <div class="dist-row">
                        <div class="dist-left">
                            <span class="dist-star">{{ $s }}</span>
                            <i data-feather="star" class="star small selected"></i>
                        </div>
                        <div class="dist-bar">
                            <div class="dist-progress" style="width: {{ $percent }}%"></div>
                        </div>
                        <div class="dist-count">{{ $count }}</div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="stat-box stat-average" style="align-items: center;">
            <div class="stat-label">Average Rating</div>
            <div class="stat-value-row">
                <div class="stat-value big">{{ $averageRating }}</div>
                <div class="stat-stars">
                    @for ($i = 1; $i <= 5; $i++)
                        <i data-feather="star" class="star {{ $i <= round($averageRating) ? 'selected' : '' }}"></i>
                    @endfor
                </div>
            </div>
            <div class="stat-sub">Average rating on this place</div>
        </div>
    </section>

    <!-- Separator -->
    <hr class="divider">

    <!-- Reviews List -->
    <section class="reviews-list">
        @forelse($reviews as $review)
            <article class="review-card">
                <div class="review-left">
                    <div class="avatar">
                        @php
                            $uname = optional($review->user)->name ?? 'U';
                            $initial = strtoupper(substr(trim($uname), 0, 1));
                        @endphp
                        <div class="avatar-circle">{{ $initial }}</div>
                    </div>

                    <div class="user-meta">
                        <div class="user-name">{{ $review->user->name ?? 'Anonymous' }}</div>
                        <div class="user-total">Total Review: {{ optional($review->user)->reviews()->count() ?? 0 }}</div>
                    </div>
                </div>

                <div class="review-main">
                    <div class="review-top">
                        <div class="review-rating">
                            @for ($i = 1; $i <= 5; $i++)
                                <i data-feather="star" class="star {{ $i <= $review->rating ? 'selected' : '' }}"></i>
                            @endfor
                        </div>
                        <div class="review-date">{{ $review->created_at->format('d-m-Y') }}</div>
                    </div>

                    <div class="review-body">
                        <p class="review-text">{{ $review->feedback }}</p>

                        @if($review->image)
                            <div class="review-image">
                                <img src="{{ asset('storage/' . $review->image) }}" alt="Review image" />
                            </div>
                        @endif
                    </div>
                </div>
            </article>
        @empty
            <p class="no-reviews">Belum ada review untuk tempat ini.</p>
        @endforelse
    </section>

    <!-- Pagination -->
    <div class="reviews-pagination">
        {{ $reviews->links('pagination::bootstrap-4') }}
    </div>
</div>

<style>
.reviews-wrap {
    max-width: 1100px;
    margin: 28px auto;
    padding: 18px;
    font-family: system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial;
    color: #1f1f1f;
}

.reviews-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap; /* biar title & filter bisa turun ke bawah di layar kecil */
    gap: 12px;
    margin-bottom: 18px;
}
.reviews-title {
    font-size: 28px;
    margin: 0;
    font-weight: 700;
}

/* FILTER STYLE */
.reviews-filter .filter-select {
    width: auto;
    min-width: 160px;
    max-width: 100%;
    padding: 6px 10px;
    font-size: 14px;
    border-radius: 6px;
    display: inline-block;
}

.reviews-filter .filter-select:focus {
    outline: none;
}

/* Stats Layout */
.reviews-stats {
    display: grid;
    grid-template-columns: 1fr 1fr 360px;
    gap: 16px;
    align-items: stretch;
}

.stat-box {
    background: #ffffff;
    border: 1px solid #e6e6e6;
    padding: 16px;
    border-radius: 12px;
    box-shadow: 0 6px 18px rgba(16,24,40,0.04);
    display: flex;
    flex-direction: column;
    justify-content: center;
}

.stat-label { color: #6b7280; font-size: 13px; margin-bottom: 8px; font-weight: 600; }
.stat-value { font-size: 36px; font-weight: 800; }
.stat-value.big { font-size: 36px; font-weight: 800; }
.stat-value-row { display:flex; align-items:center; gap:12px; flex-wrap: wrap; }
.stat-stars { display:flex; align-items:center; gap:6px; flex-wrap: wrap; }
.stat-sub { color:#9ca3af; font-size:13px; margin-top:8px; }

.mini-distribution { margin-top:6px; }
.dist-row { display:flex; align-items:center; gap:10px; margin-bottom:8px; }
.dist-left { width:56px; display:flex; align-items:center; gap:6px; }
.dist-star { font-size:13px; color:#374151; width:18px; text-align:right; display:inline-block; }
.dist-bar { flex:1; background:#f3f4f6; height:10px; border-radius:999px; overflow:hidden; }
.dist-progress { height:100%; background:#fbbf24; border-radius:999px; transition: width 0.3s ease-in-out; }
.dist-count { width:40px; text-align:right; font-size:13px; color:#374151; }

.divider { border: none; border-top: 1px solid #eef2f7; margin: 18px 0; }

.reviews-list { margin-top: 8px; display:flex; flex-direction:column; gap:14px; }
.review-card {
    display:flex;
    gap:16px;
    border: 1px solid #e6e6e6;
    padding:14px;
    border-radius:12px;
    background:#fff;
    box-shadow: 0 6px 18px rgba(16,24,40,0.03);
    transition: all 0.2s ease-in-out;
}
.review-left { width:220px; display:flex; gap:12px; align-items:flex-start; }
.avatar-circle {
    width:56px; height:56px; border-radius:50%;
    background:#f3f4f6; display:flex; align-items:center; justify-content:center;
    font-weight:700; color:#111827; font-size:20px;
    border: 1px solid #e5e7eb;
}
.user-meta { display:flex; flex-direction:column; gap:6px; }
.user-name { font-weight:700; font-size:15px; color:#111827; }
.user-total { font-size:12px; color:#6b7280; }

.review-main { flex:1; display:flex; flex-direction:column; gap:10px; }
.review-top { display:flex; justify-content:space-between; align-items:center; gap:12px; flex-wrap: wrap; }
.review-rating { display:flex; gap:6px; align-items:center; flex-wrap: wrap; }
.review-date { font-size:12px; color:#6b7280; }

.review-body { margin-top:4px; }
.review-text { margin:0 0 8px 0; color:#374151; line-height:1.5; white-space:pre-wrap; }
.review-image img { max-width:220px; border-radius:8px; border:1px solid #e6e6e6; }

.no-reviews { color:#6b7280; }

.star { width:18px; height:18px; stroke:#cfcfcf; fill:none; }
.star.selected { stroke:#f59e0b; fill:#f59e0b; }

.review-card:hover, .stat-box:hover {
    box-shadow: 0 8px 20px rgba(16,24,40,0.06);
    transform: translateY(-2px);
}

/* Pagination Style */
.reviews-pagination {
    margin-top: 18px;
    display: flex;
    justify-content: center;
}
.reviews-pagination nav ul {
    display: flex;
    gap: 6px;
    flex-wrap: wrap;
}
.reviews-pagination nav ul li a,
.reviews-pagination nav ul li span {
    padding: 6px 12px;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    font-size: 14px;
    background: #fff;
    color: #374151;
    transition: all 0.2s ease-in-out;
}
.reviews-pagination nav ul li a:hover {
    background: #f3f4f6;
}
.reviews-pagination nav ul li.active span {
    background: #6366f1;
    border-color: #6366f1;
    color: white;
}

/* ✅ RESPONSIVE OPTIMIZATION */
@media (max-width: 980px) {
    .reviews-stats { grid-template-columns: 1fr; }
    .review-left { width:160px; }
    .reviews-wrap { padding:12px; }
    .reviews-title { font-size: 22px; }
    .review-text {font-size : 12px;}
}

@media (max-width: 768px) {
    .review-card {
        flex-direction: column;
        align-items: flex-start;
    }
    .review-left { width:100%; justify-content: flex-start; }
    .review-main { width: 100%; }
    .review-image img { max-width: 100%; }
    .review-text {font-size : 12px;}
}

@media (max-width: 480px) {
    .reviews-title { font-size: 20px; }
    .reviews-filter .filter-select { width: 100%; }
    .stat-value { font-size: 28px; }
    .review-top { flex-direction: column; align-items: flex-start; gap: 4px; }
    .review-card { padding: 12px; }
    .avatar-circle { width:48px; height:48px; font-size:16px; }
    .review-text {font-size : 12px;}
}
</style>


<script>
document.addEventListener('DOMContentLoaded', function(){
    if (typeof feather !== 'undefined') feather.replace();
});
function filterReviewsByPeriod(period) {
    let url = new URL(window.location.href);
    if (period) {
        url.searchParams.set('period', period);
    } else {
        url.searchParams.delete('period');
    }
    window.location.href = url.toString();
}
</script>
@endsection
