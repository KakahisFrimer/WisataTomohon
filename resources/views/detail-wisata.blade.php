@extends('layouts.app')

@section('content')

@php
    if (!isset($place)) {
        echo '<section class="ex__destination" style="padding:2rem 7%;"><p>Data wisata tidak ditemukan.</p></section>';
        return;
    }

    try { 
        $photos = json_decode($place->foto__tmpt, true) ?: []; 
    } catch (\Throwable $e) { 
        $photos = []; 
    }

    $photoList = count($photos) 
        ? array_map(fn($p) => asset('storage/' . ltrim($p, '/')), $photos) 
        : [asset('storage/wisata/default-16x9.png')];

    $detail = $place->details->first() ?? null;
    $jamBuka = $detail->jam__buka ?? null;
    $jamTutup = $detail->jam__tutup ?? null;
    $harga = $detail->harga__tiket ?? '-';
    $hariBuka = $detail->hari__buka ?? '-';
    $status_operasi = $place->status ?? '-';
    $latitude = $place->latitude ?? null;
    $longitude = $place->longitude ?? null;
    $formattedPrice = is_numeric($harga) ? 'Rp ' . number_format($harga, 0, ',', '.') : $harga;

    // Hitung jarak
    $distance = null;
    if (!empty($latitude) && !empty($longitude) && request()->has(['latitude', 'longitude'])) {
        $earthRadius = 6371; // km
        $user_latitude = deg2rad(request('latitude'));
        $user_longitude = deg2rad(request('longitude'));
        $lat2 = deg2rad($latitude);
        $lon2 = deg2rad($longitude);

        $dlat = $lat2 - $user_latitude;
        $dlon = $lon2 - $user_longitude;

        $a = sin($dlat / 2) ** 2 + cos($user_latitude) * cos($lat2) * sin($dlon / 2) ** 2;
        $c = 2 * asin(sqrt($a));

        $distance = round($earthRadius * $c, 2);
    }
@endphp

<section class="ex__destination" 
    style="padding:0; height:100vh; display:grid;overflow:hidden;">
    
    <div class="mobile-toggle-buttons">
        <button id="toggle-left-panel" class="mobile-toggle-btn" aria-label="Toggle left panel">
            <i data-feather="info" style="width:20px;height:20px;"></i>
        </button>
        <button id="toggle-right-panel" class="mobile-toggle-btn" aria-label="Toggle right panel">
            <i data-feather="map-pin" style="width:20px;height:20px;"></i>
        </button>
    </div>
    
    <!-- Left Panel -->
    <aside id="detail-panel" class="detail-panel">
        <button class="panel-close-btn" id="close-left-panel" aria-label="Close panel">
            <i data-feather="x" style="width:20px;height:20px;"></i>
        </button>
        
        <!-- Main Info Card -->
        <div class="panel-card">
            <h2 class="place-title">{{ $place->name }}</h2>
            
            <p class="place-category">{{ $place->category ?? '-' }}</p>

            <div class="photo-info">
                <img src="{{ $photoList[0] }}" class="main-photo" alt="Foto utama {{ $place->name }}">
                <div class="place-meta">
                    <p class="place-address">
                        <i data-feather="map-pin" style="width:15px;height:15px;"></i> {{ $place->address ?? '-' }}
                    </p>
                    <p class="place-price">
                        <i data-feather="tag" style="width:15px;height:15px;"></i> {{ $formattedPrice }}
                    </p>
                    <p class="place-schedule">
                        <i data-feather="calendar" style="width:15px;height:15px;"></i> {{ $hariBuka }}
                    </p>
                    <p class="place-contact">
                        <i data-feather="phone" style="width:15px;height:15px;"></i> {{ $place->contact ?? '-' }}
                    </p>
                    
                </div>
            </div>

            <div class="info-badges">
                <span>
                    <i data-feather="clock" style="width:15px;height:15px;"></i>
                    {{ $jamBuka && $jamTutup 
                        ? \Carbon\Carbon::parse($jamBuka)->format('H:i') . ' - ' . \Carbon\Carbon::parse($jamTutup)->format('H:i') 
                        : '-' }}
                </span>
                <span class="badge-status {{ strtolower($status_operasi)==='beroperasi'?'beroperasi':'berhenti' }}">
                    {{ $status_operasi }}
                </span>
            </div>

            <!-- Action Buttons -->
            <div class="action-buttons">
                @auth
                    @if(Auth::user()->role === 'user')
                        @if($userReview)
                            <button class="read__more" id="openReviewModal">
                                <i data-feather="edit-3" style="width:15px;height:15px;"></i> Edit Review
                            </button>
                        @else
                            <button class="read__more" id="openReviewModal">
                                <i data-feather="edit-3" style="width:15px;height:15px;"></i> Write Review
                            </button>
                        @endif

                        {{-- Modal tetap di-include --}}
                        @include('components.review-modal', ['userReview' => $userReview])

                    @else
                        <button class="read__more" disabled>User Only</button>
                    @endif
                @else
                    <a href="{{ route('login') }}" class="read__more">
                        <i data-feather="log-in" style="width:15px;height:15px;"></i> Login To Review
                    </a>
                @endauth
            </div>
            <div class="action-buttons">
                <a href="{{ route('reviews.index', $place->slug) }}" class="read__more">
                    View Review
                </a>
            </div>
        </div>

        <!-- Gallery -->
        <div class="panel-card gallery-card">
            <div id="tabPhotos" class="tab-content">
                <!-- Letakkan di dalam section, setelah gallery-grid -->
                    <div class="gallery-grid">
                        @foreach($photoList as $p)
                            @if($loop->index < 2)
                                <img src="{{ $p }}" class="gallery-photo" alt="Foto {{ $loop->iteration }}" data-index="{{ $loop->index }}">
                            @elseif($loop->index == 2)
                                <div class="gallery-photo-container">
                                    <img src="{{ $p }}" class="gallery-photo" alt="Foto {{ $loop->iteration }}" data-index="{{ $loop->index }}">
                                    @if(count($photoList) > 3)
                                        <div class="photo-overlay" data-index="{{ $loop->index }}">
                                            <span class="overlay-text">+{{ count($photoList) - 3 }}</span>
                                        </div>
                                    @endif
                                </div>
                            @endif
                        @endforeach
                    </div>

                    <!-- Tambahkan elemen hidden untuk menyimpan semua URL foto -->
                    <div id="allPhotosData" data-photos='@json($photoList)' style="display: none;"></div>
            </div>
        </div>
    </aside>

    <!-- Map -->
    <div id="map-wrapper" class="map-wrapper">
        <div id="map">
            <div id="route-instruction-card">
                <span id="route-distance">--</span>
                <span id="route-text">Fetching Route...</span>
            </div>
        </div>
    </div>

    <!-- Right Panel -->
    <aside id="right-panel" class="right-panel">
        <button class="panel-close-btn" id="close-right-panel" aria-label="Close panel">
            <i data-feather="x" style="width:20px;height:20px;"></i>
        </button>
        
        <div class="right-content">
            <h3>Destination Around</h3>

            @php
    $currentCategory = strtolower($place->category ?? '');

    $tourismCategories = [
        'wisata alam',
        'wisata buatan',
        'wisata budaya'
    ];

    if (in_array($currentCategory, $tourismCategories)) {

        $topTabs = [
            ['key' => 'akomodasi', 'label' => 'Accommodation'],
            ['key' => 'kuliner', 'label' => 'Culinary'],
        ];

    } elseif ($currentCategory === 'akomodasi') {

        $topTabs = [
            ['key' => 'kuliner', 'label' => 'Culinary'],
            ['key' => 'tourism', 'label' => 'Tourism'],
        ];

    } elseif ($currentCategory === 'kuliner') {

        $topTabs = [
            ['key' => 'akomodasi', 'label' => 'Accommodation'],
            ['key' => 'tourism', 'label' => 'Tourism'],
        ];

    } else {

        $topTabs = [
            ['key' => 'akomodasi', 'label' => 'Accommodation'],
            ['key' => 'kuliner', 'label' => 'Culinary'],
        ];
    }
@endphp

<div class="nearby-tabs top-tabs">
    @foreach($topTabs as $index => $tab)
        <button
            class="nearby-tab {{ $index === 0 ? 'active' : '' }}"
            data-tab="{{ $tab['key'] }}"
        >
            {{ $tab['label'] }}
        </button>
    @endforeach
</div>

            <div class="nearby-list" data-current-category="{{ strtolower($place->category) }}">
                {{-- Akomodasi --}}
                @forelse($akomodasi as $item)
                    <a href="{{ route('wisata.show', $item->slug) }}" class="nearby-item nearby-filter-item" data-category="akomodasi">
                        <img src="{{ $item->photoList->first() }}" alt="{{ $item->name }}">
                        <div class="nearby-info">
                            <h4>{{ $item->name }}</h4>
                            <p class="nearby-distance">{{ $item->distance !== null ? $item->distance . ' km from this location' : '-' }}</p>
                        </div>
                    </a>
                @empty
                    <p class="empty-nearby" data-category="akomodasi">No nearby accommodation.</p>
                @endforelse

                {{-- Kuliner --}}
                @forelse($kuliner as $item)
                    <a href="{{ route('wisata.show', $item->slug) }}" class="nearby-item nearby-filter-item" data-category="kuliner">
                        <img src="{{ $item->photoList->first() }}" alt="{{ $item->name }}">
                        <div class="nearby-info">
                            <h4>{{ $item->name }}</h4>
                            <p class="nearby-distance">{{ $item->distance !== null ? $item->distance . ' km from this location' : '-' }}</p>
                        </div>
                    </a>
                @empty
                    <p class="empty-nearby" data-category="kuliner">No nearby culinary.</p>
                @endforelse

                {{-- Wisata --}}
                {{-- TOURISM TAB --}}
{{-- TOURISM TAB --}}
@if($tourism->count() > 0)

    @foreach($tourism as $item)

        <a href="{{ route('wisata.show', $item->slug) }}"
           class="nearby-item nearby-filter-item tourism-item"
           data-category="tourism">

            <img src="{{ $item->photoList->first() }}" alt="{{ $item->name }}">

            <div class="nearby-info">
                <h4>{{ $item->name }}</h4>

                <p class="nearby-distance">
                    {{ $item->distance !== null ? $item->distance . ' km from this location' : '-' }}
                </p>
            </div>

        </a>

    @endforeach

@endif


{{-- SIMILAR CATEGORY --}}
@if($nearby->count() > 0)

<div class="similar-category-section">

    <div class="tourism-divider"></div>

    <div class="tourism-title">
        Similar Category
    </div>

    <div class="similar-category-list">

        @foreach($nearby as $item)

    @php
        $avgRating = $item->reviews()->avg('rating');
    @endphp

    <a href="{{ route('wisata.show', $item->slug) }}"
        class="nearby-item similar-rating-card">

        <div class="similar-image-wrapper">

            <img src="{{ $item->photoList->first() }}" alt="{{ $item->name }}">

            {{-- Rating --}}
            <span class="badge-rating top-left">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                    <path d="M12 .587l3.668 7.431 8.2 1.193-5.934 5.782 1.401 8.173L12 18.897l-7.335 3.869 1.401-8.173L.132 9.211l8.2-1.193z"/>
                </svg>

                {{ number_format($avgRating ?? 0, 2) }}
            </span>

        </div>

        <div class="nearby-info">
            <h4>{{ $item->name }}</h4>

            <p class="nearby-distance">
                {{ $item->distance !== null ? $item->distance . ' km from this location' : '-' }}
            </p>
        </div>

    </a>

@endforeach

    </div>

</div>


@endif

</div>
            </div>
        </div>
    </aside>

    <!-- Photo Preview Modal -->
<div id="photoPreviewModal" class="photo-preview-modal hidden">
    <div class="preview-modal-content">
        
        <button id="prevPhoto" class="preview-nav-btn prev-btn" aria-label="Previous photo">
            <i data-feather="chevron-left" style="width:24px;height:24px;"></i>
        </button>
        <button id="nextPhoto" class="preview-nav-btn next-btn" aria-label="Next photo">
            <i data-feather="chevron-right" style="width:24px;height:24px;"></i>
        </button>
        
        <div class="preview-image-container">
            <img id="previewImage" src="" alt="Preview image" class="preview-image">
            <button id="closePreviewModal" class="preview-close-btn" aria-label="Close preview">
            <i data-feather="x" style="width:24px;height:24px;"></i>
        </button>
        </div>
        
        <div class="preview-counter">
            <span id="currentPhotoIndex">1</span> / <span id="totalPhotos">{{ count($photoList) }}</span>
        </div>
    </div>
</div>

</section>

<style>

/* Photo Preview Modal Styles */
/* Gallery Grid dengan Overlay */
.gallery-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(90px, 1fr));
    gap: 6px;
}

.gallery-photo {
    width: 100%;
    height: 80px;
    object-fit: cover;
    border-radius: 6px;
    cursor: pointer;
    transition: transform 0.2s ease;
}

.gallery-photo:hover {
    transform: scale(1.05);
}

/* Container untuk foto ke-3 dengan overlay */
.gallery-photo-container {
    position: relative;
    display: inline-block;
    width: 100%;
}

.gallery-photo-container .gallery-photo {
    filter: brightness(0.7); /* Sedikit gelapkan foto ke-3 */
}

.nearby-image-wrapper{
    position: relative;
    width: 56px;
    height: 56px;
    flex-shrink: 0;
}

.nearby-image-wrapper img{
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 8px;
}

.nearby-rating-badge{
    position: absolute;

    bottom: -6px;
    left: 50%;
    transform: translateX(-50%);

    background: #facc15;
    color: #fff;

    font-size: 10px;
    font-weight: 700;

    padding: 2px 6px;

    border-radius: 999px;

    box-shadow: 0 2px 6px rgba(0,0,0,.15);

    white-space: nowrap;
}

.photo-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.6);
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 6px;
    cursor: pointer;
}

.overlay-text {
    color: white;
    font-size: 18px;
    font-weight: bold;
    text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.8);
}

/* Jika hanya ada 3 foto, tidak perlu overlay */
.gallery-photo-container:has(.gallery-photo:only-child) .gallery-photo {
    filter: brightness(1);
}

.photo-preview-modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.9);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 10000;
}

.photo-preview-modal.hidden {
    display: none;
}

.preview-modal-content {
    position: relative;
    width: 90%;
    max-width: 800px;
    height: 80%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.preview-image-container {
    position: relative;
    display: inline-flex;
    max-width: 100%;
    max-height: 100%;
}

.preview-image {
    max-width: 100%;
    max-height: 100%;
    object-fit: contain;
    border-radius: 8px;
}

/* Tombol close yang menempel di gambar */
.preview-close-btn {
    position: absolute;
    top: 10px;
    right: 10px;
    background: rgba(0, 0, 0, 0.7);
    border: none;
    border-radius: 50%;
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    cursor: pointer;
    z-index: 10001;
    transition: all 0.3s ease;
    backdrop-filter: blur(2px);
}

.preview-close-btn:hover {
    background: rgba(0, 0, 0, 0.9);
    transform: scale(1.1);
}

/* Tombol navigasi kiri/kanan */
.preview-nav-btn {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    background: rgba(255, 255, 255, 0.2);
    border: none;
    border-radius: 50%;
    width: 50px;
    height: 50px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    cursor: pointer;
    z-index: 10001;
    transition: background 0.3s ease;
}

.preview-nav-btn:hover {
    background: rgba(255, 255, 255, 0.3);
}

.prev-btn {
    left: 20px;
}

.next-btn {
    right: 20px;
}

/* Similar Category Rating */
.similar-image-wrapper {
    position: relative;
    width: 56px;
    height: 56px;
    flex-shrink: 0;
}

.similar-image-wrapper img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 8px;
}

/* SAMA seperti landing page */
.badge-rating.top-left {
    position: absolute;

    top: 50%;
    left: 50%;

    transform: translate(-50%, -50%);

    padding: 3px 8px 3px 7px;
    border-radius: 14px;

    font-size: 0.7rem;
    font-weight: 600;

    background: rgba(255, 248, 219, 0.95);
    color: #b7791f;

    display: inline-flex;
    align-items: center;
    gap: 3px;

    line-height: 1;
    vertical-align: middle;

    z-index: 3;

    backdrop-filter: blur(4px);
    box-shadow: 0 2px 6px rgba(0,0,0,0.15);
}

.badge-rating.top-left svg {
    width: 12px;
    height: 12px;
    fill: #facc15;
    flex-shrink: 0;
    position: relative;
    top: -0.5px;
}

.preview-counter {
    position: absolute;
    bottom: 50%;
    left: 50%;
    transform: translateX(-50%);
    color: white;
    font-size: 16px;
    background: rgba(0, 0, 0, 0.5);
    padding: 8px 16px;
    border-radius: 20px;
}

/* Gallery photo cursor pointer */
.gallery-photo {
    cursor: pointer;
    transition: transform 0.2s ease;
}

.gallery-photo:hover {
    transform: scale(1.05);
}

/* Responsive styles for preview modal */
@media (max-width: 768px) {
    .preview-modal-content {
        width: 95%;
        height: 70%;
    }
    
    .preview-nav-btn {
        width: 40px;
        height: 40px;
    }
    
    .preview-close-btn {
        width: 35px;
        height: 35px;
        top: 8px;
        right: 8px;
    }
    
    .preview-close-btn i {
        width: 18px !important;
        height: 18px !important;
    }
}

/* Untuk gambar portrait/vertikal */
@media (max-height: 600px) {
    .preview-modal-content {
        height: 90%;
    }
    
    .preview-close-btn {
        top: 5px;
        right: 5px;
    }
}
/* ==== Base Styles (Mobile First) ==== */
.ex__destination {
    position: relative;
    height: 100vh;
    display: grid;
    grid-template-columns: 1fr;
    grid-template-rows: 1fr;
    overflow: hidden;
}

/* Mobile Toggle Buttons */
.mobile-toggle-buttons {
    position: fixed;
    bottom: 20px;
    right: 20px;
    display: flex;
    flex-direction: column;
    gap: 10px;
    z-index: 1000;
}

.mobile-toggle-btn {
    background: #710eff;
    color: white;
    border: none;
    border-radius: 50%;
    width: 50px;
    height: 50px;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 4px 12px rgba(0,0,0,0.3);
    cursor: pointer;
    z-index: 1001;
}

.panel-close-btn {
    position: absolute;
    top: 10px;
    right: 10px;
    background: rgba(255,255,255,0.9);
    border: none;
    border-radius: 50%;
    width: 36px;
    height: 36px;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 2px 6px rgba(0,0,0,0.2);
    cursor: pointer;
    z-index: 10;
}

/* Panel Styles */
.detail-panel, .right-panel {
    position: fixed;
    top: 0;
    bottom: 0;
    width: 100%;
    max-width: 350px;
    background: white;
    padding: 1rem;
    overflow-y: auto;
    z-index: 100;
    transition: transform 0.3s ease;
    box-shadow: 0 0 10px rgba(0,0,0,.08);
}

.detail-panel {
    left: 0;
    transform: translateX(-100%);
    border-right: 1px solid #eee;
}

.right-panel {
    right: 0;
    transform: translateX(100%);
    border-left: 1px solid #eee;
}

.detail-panel.panel-open, .right-panel.panel-open {
    transform: translateX(0);
}

/* Map Wrapper */
.map-wrapper {
    position: relative;
    width: 100%;
    height: 100%;
    z-index: 1;
}

#map {
    height: 100%;
    width: 100%;
}

/* Panel Card */
.panel-card {
    background: var(--primary);
    border-radius: 12px;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
    padding: 1rem;
    margin-bottom: 1rem;
}

.place-title {
    font-size: 1.8rem;
    font-weight: 700;
    margin-bottom: 0.2rem;
    color: var(--txt);
}

.place-category {
    font-size: 0.85rem;
    color: #666;
    margin-bottom: 0.8rem;
}

.photo-info {
    display: flex;
    flex-direction: column;
    gap: 0.8rem;
    align-items: flex-start;
}

.main-photo {
    width: 100%;
    height: 160px;
    object-fit: cover;
    border-radius: 10px;
}

.place-meta p {
    font-size: 0.85rem;
    color: #444;
    margin: 0.2rem 0;
    display: flex;
    align-items: center;
    gap: 4px;
    padding-bottom : 4px;
}

.place-meta i {
    width: 16px;
    height: 16px;
    stroke-width: 1.8;
    color: #710eff;
}

.info-badges {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
    margin-top: 0.8rem;
    font-size: 0.75rem;
    justify-content: space-between;
}

.info-badges span {
    display: flex;
    align-items: center;
    gap: 4px;
    background: #f3eafd;
    color: var(--sec);
    padding: 4px 8px;
    border-radius: 6px;
    font-weight: 600;
}

.badge-status.beroperasi {
    background: #e8f8ef;
    color: #0f9d58;
}

.badge-status.berhenti {
    background: #fdecea;
    color: #d93025;
}

.action-buttons {
    display: flex;
    margin-top: 0.8rem;
}

.action-buttons .read__more {
    flex: 1;
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 6px;
    padding: 8px;
    background: #710eff;
    color: var(--primary);
    font-size: 0.85rem;
    border-radius: 8px;
    text-decoration: none;
    transition: background 0.3s ease;
}

.action-buttons .read__more:hover {
    background: #5a0ccc;
}

.gallery-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(90px, 1fr));
    gap: 6px;
}

.gallery-photo {
    width: 100%;
    height: 80px;
    object-fit: cover;
    border-radius: 6px;
}

.tourism-title {
    width: 100%;
    background: #710eff;
    color: white;
    text-align: center;
    padding: 10px;
    border-radius: 10px;
    font-size: 14px;
    font-weight: 600;
}

/* Nearby Tabs & List */
.top-tabs {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 8px;
}

.tourism-section {
    margin-top: 18px;
}

.tourism-divider {
    width: 100%;
    height: 1px;
    background: #ececec;
    margin-bottom: 14px;
}

.tourism-tab {
    width: 100%;
}

.nearby-tabs {
    display: flex;
    gap: 8px;
    margin-bottom: 10px;
    padding-bottom: 5px;
}

.nearby-tabs-wrapper {
    display: flex;
    flex-direction: column;
    gap: 10px;
    margin-bottom: 14px;
}

.top-tabs {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 8px;
}

.bottom-tabs {
    display: flex;
}

.tourism-tab {
    width: 100%;
}

/* sedikit lebih modern */
.nearby-tab {
    border: 1px solid #ececec;
}

.nearby-tab:hover {
    transform: translateY(-1px);
}

.nearby-tab {
    flex: 1;
    background: #fdfdfd;
    border: none;
    padding: 8px 10px;
    cursor: pointer;
    border-radius: 8px;
    font-size: 12px;
    font-weight: 500;
    transition: all 0.2s ease-in-out;
    white-space: nowrap;
}

.nearby-tab.active {
    background: #710eff;
    color: #fdfdfd;
    box-shadow: 0 2px 6px rgba(79, 70, 229, 0.4);
}

.nearby-list {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.nearby-item {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 8px;
    border: 1px solid #e5e7eb;
    border-radius: 10px;
    background: #fdfdfd;
    transition: all 0.2s ease-in-out;
    text-decoration: none;
    color: inherit;
}

.nearby-item:hover {
    background: #fdfdfd;
    border-color: #d1d5db;
    transform: translateY(-2px);
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}

.nearby-item img {
    width: 56px;
    height: 56px;
    object-fit: cover;
    border-radius: 8px;
    flex-shrink: 0;
}

.nearby-info {
    flex: 1;
    display: flex;
    flex-direction: column;
    justify-content: center;
    gap: 2px;
    overflow: hidden;
}

.nearby-info h4 {
    font-size: 15px;
    font-weight: 600;
    color: #111827;
    margin: 0;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.nearby-distance {
    font-size: 13px;
    color: #6b7280;
    margin: 0;
}

.empty-nearby {
    font-size: 14px;
    color: #9ca3af;
    text-align: center;
    padding: 10px 0;
}

.tourism-scroll-area {
    max-height: 300px;
    overflow-y: auto;
    display: flex;
    flex-direction: column;
    gap: 10px;
    padding-right: 4px;
}

/* scrollbar modern */
.tourism-scroll-area::-webkit-scrollbar {
    width: 6px;
}

.tourism-scroll-area::-webkit-scrollbar-thumb {
    background: #c4b5fd;
    border-radius: 10px;
}

.tourism-scroll-area::-webkit-scrollbar-track {
    background: transparent;
}

/* Route Instruction Card */

@media (max-width: 768px) {

    #route-instruction-card {
        bottom: 155px !important; 
        width: calc(100% - 40px)!important;
        max-width: 320px!important;
        min-width: auto!important;
        font-size: 13px!important;
        padding: 10px 14px!important;
    }

    #route-distance {
        font-size: 16px;
    }

}
#route-instruction-card {
    position: absolute;
    bottom: 100px;
    left: 50%;
    transform: translateX(-50%);
    background: #fdfdfd;
    color: #710eff;
    padding: 12px 16px;
    border-radius: 14px;
    display: flex;
    align-items: center;
    gap: 8px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.3);
    font-family: sans-serif;
    min-width: 260px;
    text-align: center;
    font-size: 14px;
    z-index: 99999;
    justify-content: center;
}

#route-distance {
    font-size: 18px;
    font-weight: 600;
    margin-right: 8px;
}

.nav-text-container {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 8px;
}

.nav-text-label {
    font-size: 14px;
    font-weight: 500;
    color: #710eff;
    flex: 1;
}

.nav-text-icon svg {
    width: 20px;
    height: 20px;
    stroke: #710eff;
    flex-shrink: 0;
}

/* Tablet Styles */
@media (max-width: 768px) {
    .detail-panel,
    .right-panel {
        top: 50px; 
    }

    .place-title {
    font-size: 3rem;
    font-weight: 700;
    margin-bottom: 0.2rem;
    color: var(--txt);
}

.similar-category-list {
    max-height: 300px;
    overflow-y: auto;
    padding-right: 4px;

    display: flex;
    flex-direction: column;
    gap: 10px;
}

/* scrollbar */
.similar-category-list::-webkit-scrollbar {
    width: 6px;
}

.similar-category-list::-webkit-scrollbar-thumb {
    background: #c7c7c7;
    border-radius: 10px;
}

.place-category {
    font-size: 1.854rem;
    color: #666;
    margin-bottom: 0.8rem;
}

.place-meta p {
    font-size: 1.146rem;
    color: #444;
    margin: 0.2rem 0;
    display: flex;
    align-items: center;
    gap: 5px;
}

.info-badges {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
    margin-top: 0.8rem;
    font-size: 1.3rem;
    justify-content: space-around;
}

.action-buttons .read__more {
    font-size: 1.4rem;
}

.ex__destination h3 {
    font-size: clamp(3rem, 3vw, 2.2rem);
    font-weight: 700;
    color: var(--txt);
    margin-bottom: 0.8rem;
}
}


@media (min-width: 768px) {
    .ex__destination {
        grid-template-columns: 350px 1fr;
        grid-template-rows: 1fr;
    }

    .detail-panel.panel-open, .right-panel.panel-open {
    transform: translateX(0);
}
    
    .detail-panel, .right-panel {
        position: relative;
        transform: translateX(0);
        max-width: 100%;
        height: 100%;
    }
    
    .detail-panel {
        grid-column: 1;
    }
    
    .map-wrapper {
        grid-column: 2;
    }
    
    .right-panel {
        display: none;
    }
    
    .mobile-toggle-buttons {
        display: none;
    }
    
    .panel-close-btn {
        display: none;
    }
}

/* Desktop Styles */
@media (min-width: 1024px) {
    .ex__destination {
        grid-template-columns: 350px 1fr 300px;
    }
    
    .right-panel {
        display: block;
        grid-column: 3;
    }
    
    .place-title {
        font-size: 2.3rem;
    }
}

/* Large Desktop */
@media (min-width: 1200px) {
    .detail-panel {
        max-width: 380px;
    }
    
    .right-panel {
        max-width: 320px;
    }
}

/* Modal Responsive */
@media (max-width: 768px) {
    #reviewModal .modal-content {
        width: 90%;
        margin: 10% auto;
    }
}
</style>

<script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<link rel="stylesheet" href="https://unpkg.com/leaflet-routing-machine/dist/leaflet-routing-machine.css" />
<script src="https://unpkg.com/leaflet-routing-machine/dist/leaflet-routing-machine.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    feather.replace();

    // Panel Toggle Functionality
    const leftPanel = document.getElementById('detail-panel');
    const rightPanel = document.getElementById('right-panel');
    const toggleLeftBtn = document.getElementById('toggle-left-panel');
    const toggleRightBtn = document.getElementById('toggle-right-panel');
    const closeLeftBtn = document.getElementById('close-left-panel');
    const closeRightBtn = document.getElementById('close-right-panel');
    
    // Toggle left panel
    if (toggleLeftBtn) {
        toggleLeftBtn.addEventListener('click', function() {
            leftPanel.classList.toggle('panel-open');
            // Close right panel if open
            if (rightPanel.classList.contains('panel-open')) {
                rightPanel.classList.remove('panel-open');
            }
        });
    }
    
    // Toggle right panel
    if (toggleRightBtn) {
        toggleRightBtn.addEventListener('click', function() {
            rightPanel.classList.toggle('panel-open');
            // Close left panel if open
            if (leftPanel.classList.contains('panel-open')) {
                leftPanel.classList.remove('panel-open');
            }
        });
    }
    
    // Close panel buttons
    if (closeLeftBtn) {
        closeLeftBtn.addEventListener('click', function() {
            leftPanel.classList.remove('panel-open');
        });
    }
    
    if (closeRightBtn) {
        closeRightBtn.addEventListener('click', function() {
            rightPanel.classList.remove('panel-open');
        });
    }
    
    // Close panels when clicking outside on mobile
    document.addEventListener('click', function(event) {
        if (window.innerWidth < 768) {
            if (!leftPanel.contains(event.target) && !toggleLeftBtn.contains(event.target) && leftPanel.classList.contains('panel-open')) {
                leftPanel.classList.remove('panel-open');
            }
            
            if (!rightPanel.contains(event.target) && !toggleRightBtn.contains(event.target) && rightPanel.classList.contains('panel-open')) {
                rightPanel.classList.remove('panel-open');
            }
        }
    });

    // ✅ Handle modal review + navbar restore
    const navbar = document.querySelector('.navbar');
    const reviewModal = document.getElementById('reviewModal');
    const openReviewModal = document.getElementById('openReviewModal');

    function openModal() {
        reviewModal.classList.remove('hidden');

        // ✅ sembunyikan navbar & hilangkan ruangnya
        if (navbar) {
            navbar.style.display = 'none';
        }

        // ✅ paksa reflow layout
        setTimeout(() => window.dispatchEvent(new Event('resize')), 50);
    }

    function closeModal() {
        reviewModal.classList.add('hidden');

        // ✅ kembalikan navbar
        if (navbar) {
            navbar.style.display = '';
        }

        // ✅ paksa reflow ulang agar map & panel geser turun
        setTimeout(() => window.dispatchEvent(new Event('resize')), 50);
    }

    if (openReviewModal) {
        openReviewModal.addEventListener('click', openModal);
    }

    // Kalau ada tombol cancel / close
    if (document.getElementById('cancelReview')) {
        document.getElementById('cancelReview').addEventListener('click', closeModal);
    }

    // ✅ Init map
    if (typeof L !== 'undefined') {
        const map = L.map('map').setView([{{ $latitude ?? 0 }}, {{ $longitude ?? 0 }}], 15);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        let wisataMarker = null;
        let routingControl = null;
        let routeSteps = [];
        let currentStepIndex = 0;

        // Elemen kartu navigasi
        const navCard = document.getElementById('route-instruction-card');
        const navDist = document.getElementById('route-distance');
        const navText = document.getElementById('route-text');

        function updateInstruction(step) {
            if (!step) return;

            navDist.textContent = step.distance < 1000
                ? `${Math.round(step.distance)} m`
                : `${(step.distance / 1000).toFixed(1)} km`;

            // Buat elemen dengan text di kiri & ikon di kanan
            const icon = getTurnIcon(step.type);
            navText.innerHTML = `
                <div class="nav-text-container">
                    <span class="nav-text-label">${step.text}</span>
                    <span class="nav-text-icon">${icon}</span>
                </div>
            `;
            navCard.style.display = 'flex';
        }

        function getTurnIcon(type) {
            switch (type) {
                case 'Head':
                case 'Continue':
                    return feather.icons['arrow-up'].toSvg({ width: 18, height: 18 });
                case 'TurnRight':
                case 'BearRight':
                    return feather.icons['corner-down-right'].toSvg({ width: 18, height: 18 });
                case 'TurnLeft':
                case 'BearLeft':
                    return feather.icons['corner-down-left'].toSvg({ width: 18, height: 18 });
                case 'UTurn':
                    return feather.icons['rotate-ccw'].toSvg({ width: 18, height: 18 });
                case 'Arrive':
                    return feather.icons['map-pin'].toSvg({ width: 18, height: 18 });
                default:
                    return feather.icons['navigation'].toSvg({ width: 18, height: 18 });
            }
        }

        function distance(lat1, lon1, lat2, lon2) {
            const R = 6371e3;
            const φ1 = lat1 * Math.PI / 180;
            const φ2 = lat2 * Math.PI / 180;
            const Δφ = (lat2 - lat1) * Math.PI / 180;
            const Δλ = (lon2 - lon1) * Math.PI / 180;

            const a = Math.sin(Δφ / 2) ** 2 +
                Math.cos(φ1) * Math.cos(φ2) *
                Math.sin(Δλ / 2) ** 2;
            const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));

            return R * c;
        }

        // Marker tempat wisata
        if ({{ $latitude ?? 'null' }} && {{ $longitude ?? 'null' }}) {
            wisataMarker = L.marker([{{ $latitude }}, {{ $longitude }}]).addTo(map)
                .bindPopup(`<b>{{ $place->name }}</b>`);
        }

        // Ambil lokasi user realtime & langsung tampilkan rute
        if (navigator.geolocation) {
            navigator.geolocation.watchPosition(
                function (position) {
                    window.userLat = position.coords.latitude;
                    window.userLng = position.coords.longitude;

                    if (window.userMarker) {
                        window.userMarker.setLatLng([window.userLat, window.userLng]);
                    } else {
                        window.userMarker = L.marker([window.userLat, window.userLng], {
                            icon: L.icon({
                                iconUrl: 'https://cdn-icons-png.flaticon.com/512/64/64113.png',
                                iconSize: [32, 32],
                                iconAnchor: [16, 32]
                            })
                        }).addTo(map).bindPopup("Lokasi Anda");
                    }

                    if (routingControl) {
                        routingControl.setWaypoints([
                            L.latLng(window.userLat, window.userLng),
                            L.latLng({{ $latitude }}, {{ $longitude }})
                        ]);
                    } else {
                        routingControl = L.Routing.control({
                            waypoints: [
                                L.latLng(window.userLat, window.userLng),
                                L.latLng({{ $latitude }}, {{ $longitude }})
                            ],
                            lineOptions: {
                                styles: [{ color: '#4f46e5', weight: 5, opacity: 0.8 }]
                            },
                            show: false,
                            addWaypoints: false,
                            draggableWaypoints: false,
                            fitSelectedRoutes: true,
                            createMarker: () => null
                        }).on('routesfound', function (e) {
                            const route = e.routes[0];
                            routeSteps = route.instructions || [];
                            currentStepIndex = 0;
                            updateInstruction(routeSteps[currentStepIndex]);
                        }).addTo(map);
                    }

                    if (routeSteps.length > 0 && currentStepIndex < routeSteps.length) {
                        const step = routeSteps[currentStepIndex];
                        const stepCoord = step.latLng;
                        const d = distance(window.userLat, window.userLng, stepCoord.lat, stepCoord.lng);

                        if (d < 15) {
                            currentStepIndex++;
                            if (currentStepIndex < routeSteps.length) {
                                updateInstruction(routeSteps[currentStepIndex]);
                            } else {
                                navText.innerHTML = feather.icons['map-pin'].toSvg({ width: 18, height: 18 }) + " Anda telah sampai 🎉";
                                navDist.textContent = "";
                            }
                        }
                    }
                },
                function (error) {
                    console.warn("Gagal mendapatkan lokasi pengguna:", error.message);
                },
                { enableHighAccuracy: true }
            );
        }
    }

  // ✅ Photo Preview Functionality - PERBAIKAN
const photoPreviewModal = document.getElementById('photoPreviewModal');
const previewImage = document.getElementById('previewImage');
const closePreviewModal = document.getElementById('closePreviewModal');
const prevPhotoBtn = document.getElementById('prevPhoto');
const nextPhotoBtn = document.getElementById('nextPhoto');
const currentPhotoIndex = document.getElementById('currentPhotoIndex');
const totalPhotos = document.getElementById('totalPhotos');
const galleryPhotos = document.querySelectorAll('.gallery-photo');
const photoOverlay = document.querySelector('.photo-overlay');

// Ambil semua foto dari data attribute
const allPhotosData = document.getElementById('allPhotosData');
const allPhotos = JSON.parse(allPhotosData.getAttribute('data-photos'));

let currentPreviewIndex = 0;

// Update total photos di counter
totalPhotos.textContent = allPhotos.length;

// Open photo preview when gallery photo is clicked
galleryPhotos.forEach((photo) => {
    photo.addEventListener('click', function() {
        // Gunakan data-index dari elemen yang diklik
        currentPreviewIndex = parseInt(this.getAttribute('data-index'));
        updatePreviewImage();
        photoPreviewModal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    });
});

// Handle click on overlay (jika ada)
if (photoOverlay) {
    photoOverlay.addEventListener('click', function() {
        // Mulai dari foto ke-3 (index 2)
        currentPreviewIndex = parseInt(this.getAttribute('data-index'));
        updatePreviewImage();
        photoPreviewModal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    });
}

// Close preview modal
closePreviewModal.addEventListener('click', () => {
    photoPreviewModal.classList.add('hidden');
    document.body.style.overflow = '';
});

// Navigate to previous photo
prevPhotoBtn.addEventListener('click', () => {
    if (currentPreviewIndex > 0) {
        currentPreviewIndex--;
    } else {
        currentPreviewIndex = allPhotos.length - 1; // Loop to last photo
    }
    updatePreviewImage();
});

// Navigate to next photo
nextPhotoBtn.addEventListener('click', () => {
    if (currentPreviewIndex < allPhotos.length - 1) {
        currentPreviewIndex++;
    } else {
        currentPreviewIndex = 0; // Loop to first photo
    }
    updatePreviewImage();
});

// Close modal when clicking outside the image
photoPreviewModal.addEventListener('click', (e) => {
    if (e.target === photoPreviewModal) {
        photoPreviewModal.classList.add('hidden');
        document.body.style.overflow = '';
    }
});

// Keyboard navigation
document.addEventListener('keydown', (e) => {
    if (!photoPreviewModal.classList.contains('hidden')) {
        if (e.key === 'Escape') {
            photoPreviewModal.classList.add('hidden');
            document.body.style.overflow = '';
        } else if (e.key === 'ArrowLeft') {
            prevPhotoBtn.click();
        } else if (e.key === 'ArrowRight') {
            nextPhotoBtn.click();
        }
    }
});

// Update preview image and counter - GUNAKAN allPhotos
function updatePreviewImage() {
    // Gunakan allPhotos array yang berisi semua URL foto
    previewImage.src = allPhotos[currentPreviewIndex];
    currentPhotoIndex.textContent = currentPreviewIndex + 1;
    
    // Update feather icons after modal opens
    setTimeout(() => {
        feather.replace();
    }, 100);
}

    // Nearby tabs functionality
    const tabs = document.querySelectorAll(".nearby-tab");
    const items = document.querySelectorAll(".nearby-item, .empty-nearby");
    const listContainer = document.querySelector(".nearby-list");

function filterCategory(category) {

    document.querySelectorAll(
        '.nearby-filter-item, .empty-nearby'
    ).forEach(item => {

        const cat = item.getAttribute("data-category");

        item.style.display = (cat === category)
            ? ""
            : "none";
    });
}

    // Tentukan kategori default (akomodasi / kuliner / wisata)
    let currentCategory = tabs[0]?.dataset.tab || "akomodasi";

    // Aktifkan tab yang sesuai kategori sekarang
    tabs.forEach(tab => {
        if (tab.dataset.tab === currentCategory) {
            tab.classList.add("active");
        } else {
            tab.classList.remove("active");
        }
    });

    // Filter sesuai kategori aktif
    filterCategory(currentCategory);

    // Event click untuk switch tab
    tabs.forEach(tab => {
        tab.addEventListener("click", function () {
            tabs.forEach(t => t.classList.remove("active"));
            this.classList.add("active");
            filterCategory(this.getAttribute("data-tab"));
        });
    });
});
</script>




@endsection