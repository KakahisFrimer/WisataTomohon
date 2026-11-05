@extends('layouts.app')

@section('content')

<section class="landing__page" id="home">
    <main class="container">
        <h2>WELCOME TO</h2>
        <h1>TOMOHON CITY</h1>
        <p>Explore the Beuaty of Tomohon's Nature and Culture</p>
        <a href="#near__destination"><button>Start Explore</button></a>
    </main> 
</section>

<div class="category__floating">
    <div class="category__selector">
        <select id="categoryFilter" class="category-dropdown">
            <option value="" disabled {{ empty($selectedCategory) ? 'selected' : '' }}>Choose Destination Category</option>            
            <option value="all" {{ !isset($selectedCategory) || $selectedCategory == 'all' ? 'selected' : '' }}>All Category</option>
            <option value="Wisata Buatan" {{ $selectedCategory == 'Wisata Buatan' ? 'selected' : '' }}>Wisata Buatan</option>
            <option value="Wisata Alam" {{ $selectedCategory == 'Wisata Alam' ? 'selected' : '' }}>Wisata Alam</option>
            <option value="Wisata Budaya" {{ $selectedCategory == 'Wisata Budaya' ? 'selected' : '' }}>Wisata Budaya</option>
            <option value="Kuliner" {{ $selectedCategory == 'Kuliner' ? 'selected' : '' }}>Kuliner</option>
            <option value="Akomodasi" {{ $selectedCategory == 'Akomodasi' ? 'selected' : '' }}>Akomodasi</option>

        </select>
        <span class="dropdown-icon">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#1f1f1f" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="6 9 12 15 18 9"></polyline>
            </svg>
        </span>

    </div>
</div>


<section id="near__destination" class="ex__destination">
    <div class="destination__title">
        <h3>Nearest Destination</h3>
        <a href="{{ route ('all__des') }}"><button>View All</button></a>
    </div>

    <div class="card__desti">
        @php
            $nearestList = isset($nearestDestinations) ? $nearestDestinations : $destinations;
        @endphp

        @forelse($nearestList as $item)
            @php
                $photos = json_decode($item->foto__tmpt, true) ?? [];
                $photoPath = !empty($photos) ? 'storage/' . $photos[0] : 'storage/wisata/default-16x9.png';

                $detail = $item->details->first();
                $jamBuka = $detail->jam__buka ?? null;
                $jamTutup = $detail->jam__tutup ?? null;

                $harga = $detail->harga__tiket ?? '-';
                $avgRating = $item->average_rating ?? $item->reviews()->avg('rating') ?? 0;
                $statusBuka = $item->status_buka ?? '-';
            @endphp

            <div class="destination__card">
                <a href="{{ route('wisata.show', $item->slug) }}" onclick="event.preventDefault(); redirectWithLocation('{{ route('wisata.show', $item->slug) }}');" class="android-card">
                    <div class="card__image-wrapper">
                        <img src="{{ asset($photoPath) }}" class="loc__img" alt="{{ $item->name }}" loading="lazy">

                        {{-- Floating Rating Chip (Android Material Glassmorphism) --}}
                        <div class="badge-rating-android">
                            <svg viewBox="0 0 24 24" class="star-icon">
                                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                            </svg>
                            <span>{{ number_format($avgRating, 1) }}</span>
                        </div>

                        {{-- Floating Status Badge (Buka / Tutup) --}}
                        @if($statusBuka === 'Buka')
                            <div class="badge-status-android buka">
                                <span class="dot-indicator"></span>Buka
                            </div>
                        @elseif($statusBuka === 'Tutup')
                            <div class="badge-status-android tutup">
                                Tutup
                            </div>
                        @endif
                    </div>

                    <div class="card__content">
                        <span class="card-category-pill">{{ $item->category ?? 'Wisata' }}</span>
                        <h2 class="place-name">{{ $item->name }}</h2>

                        <div class="card-meta-row">
                            <div class="meta-distance">
                                <svg class="meta-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                                    <circle cx="12" cy="10" r="3"></circle>
                                </svg>
                                <span>
                                    @if(isset($item->distance) && $item->distance !== null)
                                        {{ number_format($item->distance, 2, ',', '.') }} km
                                    @else
                                        Tomohon
                                    @endif
                                </span>
                            </div>
                            <div class="meta-price">
                                <span class="price-label">Tiket</span>
                                <span class="price-value">
                                    @if(is_numeric($harga))
                                        Rp {{ number_format($harga, 0, ',', '.') }}
                                    @elseif($harga === '-' || empty($harga))
                                        Gratis
                                    @else
                                        {{ $harga }}
                                    @endif
                                </span>
                            </div>
                        </div>

                        <div class="card-action-bar">
                            <span>Lihat Detail</span>
                            <svg class="action-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                <polyline points="9 18 15 12 9 6"></polyline>
                            </svg>
                        </div>
                    </div>
                </a>
            </div>
        @empty
            <p class="empty__state">Tidak ada destinasi terdekat.</p>
        @endforelse
    </div>
</section>

<section id="ex__destination" class="ex__destination">
    <div class="destination__title">
        <h3>Popular Destination</h3>
        <a href="{{ route ('all__des') }}"><button>View All</button></a>
    </div>

    <div class="card__desti">
        @forelse($destinations as $item)
            @php
                $photos = json_decode($item->foto__tmpt, true) ?? [];
                $photoPath = !empty($photos) ? 'storage/' . $photos[0] : 'storage/wisata/default-16x9.webp';

                $detail = $item->details->first();
                $jamBuka = $detail->jam__buka ?? null;
                $jamTutup = $detail->jam__tutup ?? null;

                $harga = $detail->harga__tiket ?? '-';
                $avgRating = $item->average_rating ?? $item->reviews()->avg('rating') ?? 0;
                $statusBuka = $item->status_buka ?? '-';
            @endphp

            <div class="destination__card">
                <a href="{{ route('wisata.show', $item->slug) }}" onclick="event.preventDefault(); redirectWithLocation('{{ route('wisata.show', $item->slug) }}');" class="android-card">
                    <div class="card__image-wrapper">
                        <img src="{{ asset($photoPath) }}" class="loc__img" alt="{{ $item->name }}" loading="lazy">

                        {{-- Floating Rating Chip (Android Material Glassmorphism) --}}
                        <div class="badge-rating-android">
                            <svg viewBox="0 0 24 24" class="star-icon">
                                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                            </svg>
                            <span>{{ number_format($avgRating, 1) }}</span>
                        </div>

                        {{-- Floating Status Badge (Buka / Tutup) --}}
                        @if($statusBuka === 'Buka')
                            <div class="badge-status-android buka">
                                <span class="dot-indicator"></span>Buka
                            </div>
                        @elseif($statusBuka === 'Tutup')
                            <div class="badge-status-android tutup">
                                Tutup
                            </div>
                        @endif
                    </div>

                    <div class="card__content">
                        <span class="card-category-pill">{{ $item->category ?? 'Wisata' }}</span>
                        <h2 class="place-name">{{ $item->name }}</h2>

                        <div class="card-meta-row">
                            <div class="meta-distance">
                                <svg class="meta-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                                    <circle cx="12" cy="10" r="3"></circle>
                                </svg>
                                <span>
                                    @if(isset($item->distance) && $item->distance !== null)
                                        {{ number_format($item->distance, 2, ',', '.') }} km
                                    @else
                                        Tomohon
                                    @endif
                                </span>
                            </div>
                            <div class="meta-price">
                                <span class="price-label">Tiket</span>
                                <span class="price-value">
                                    @if(is_numeric($harga))
                                        Rp {{ number_format($harga, 0, ',', '.') }}
                                    @elseif($harga === '-' || empty($harga))
                                        Gratis
                                    @else
                                        {{ $harga }}
                                    @endif
                                </span>
                            </div>
                        </div>

                        <div class="card-action-bar">
                            <span>Lihat Detail</span>
                            <svg class="action-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                <polyline points="9 18 15 12 9 6"></polyline>
                            </svg>
                        </div>
                    </div>
                </a>
            </div>
        @empty
            <p class="empty__state">Belum ada destinasi populer.</p>
        @endforelse
    </div>
</section>

<section id="event__section" class="ex__destination">
    <div class="destination__title">
        <h3>Event</h3>
        <a href="{{ route ('all__events') }}"><button>View All</button></a>
    </div>

    <div class="event__card__wrapper">
        @forelse($events as $event)
        @php
            $eventId = $event->id ?? null;
            $eventUrl = (Route::has('event.show') && $eventId)
                ? route('event.show', $eventId)
                : url('/event/' . ($eventId ?? ''));

            $photosEvent = json_decode($event->foto__event, true) ?? [];
            $photoPathEvent = !empty($photosEvent)
                ? 'storage/' . $photosEvent[0]
                : 'storage/event/default-16x9.png';

            $startDate = \Carbon\Carbon::parse($event->start_date)->format('j M Y');
            $endDate = \Carbon\Carbon::parse($event->end_date)->format('j M Y');
        @endphp

        <a href="{{ $eventUrl }}" class="event__card">
            <div class="event__img__box">
                <img src="{{ asset($photoPathEvent) }}" alt="{{ $event->title }}" loading="lazy">
                <span class="event__badge {{ strtolower($event->status) }}">
                    {{ $event->status }}
                </span>
            </div>
            <div class="event__info">
                <h2 class="event__title">{{ $event->title }}</h2>

                <div class="event__date__box">
                    <span>{{ $startDate }} - {{ $endDate }}</span>
                </div>
            </div>
        </a>
        @empty
        <p class="empty__state">Belum ada event yang ditampilkan.</p>
        @endforelse
    </div>
</section>




{{-- <section class="cta__section">
    <div class="cta__content">
        <h2>Plan Your Journey Now!</h2>
        <p>Discover Tomohon and start your adventure today</p>
        <a href="#contact"><button>Contact Us</button></a>
    </div>
</section>


<section id="contact" class="ex__destination">
    <div class="destination__title">
        <h3>Contact</h3>
    </div>
    <div class="contact__info">
        <p><i data-feather="map-pin"></i> Tomohon City</p>
        <p><i data-feather="mail"></i> info@wisatatomohon.com</p>
        <p><i data-feather="phone"></i> +62 812 3456 7890</p>
    </div>
</section> --}}

<!-- Footer -->
<footer class="footer">
    <p>&copy; {{ date('Y') }} WisataTomohon. All rights reserved. | Developed by
@fri_costan</p>
</footer>

<style>
/* ========================================================
   MODERN ANDROID MATERIAL 3 CARD STYLES
   ======================================================== */

.card__desti {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 1.25rem;
    color: var(--txt);
    margin-top: 1rem;
}

@media (max-width: 1024px) {
    .card__desti {
        grid-template-columns: repeat(2, 1fr);
        gap: 1rem;
    }
}

@media (max-width: 580px) {
    .card__desti {
        grid-template-columns: 1fr;
        gap: 1.15rem;
    }
}

/* Card Root Item */
.destination__card {
    display: flex;
    flex-direction: column;
}

.android-card {
    display: flex;
    flex-direction: column;
    height: 100%;
    background: #ffffff;
    border-radius: 20px;
    overflow: hidden;
    text-decoration: none;
    color: #1f1f1f;
    border: 1px solid rgba(0, 0, 0, 0.07);
    box-shadow: 0 4px 18px rgba(0, 0, 0, 0.05);
    transition: transform 0.25s cubic-bezier(0.4, 0, 0.2, 1), box-shadow 0.25s cubic-bezier(0.4, 0, 0.2, 1), border-color 0.25s ease;
    position: relative;
    cursor: pointer;
    -webkit-tap-highlight-color: transparent;
}

.android-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 12px 28px rgba(113, 14, 255, 0.12);
    border-color: rgba(113, 14, 255, 0.25);
}

.android-card:active {
    transform: scale(0.985);
}

/* Image Wrapper */
.card__image-wrapper {
    position: relative;
    width: 100%;
    aspect-ratio: 16 / 10;
    overflow: hidden;
    background: #eef2f6;
}

.loc__img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
    transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}

.android-card:hover .loc__img {
    transform: scale(1.06);
}

/* Floating Rating Chip (Frosted Glass Pill) */
.badge-rating-android {
    position: absolute;
    top: 12px;
    left: 12px;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 5px 11px;
    background: rgba(255, 255, 255, 0.94);
    backdrop-filter: blur(8px);
    -webkit-backdrop-filter: blur(8px);
    border-radius: 30px;
    font-size: 0.8rem;
    font-weight: 700;
    color: #1f1f1f;
    box-shadow: 0 3px 10px rgba(0, 0, 0, 0.12);
    z-index: 10;
}

.badge-rating-android .star-icon {
    width: 13px;
    height: 13px;
    fill: #f59e0b;
    color: #f59e0b;
}

/* Floating Status Chip (Buka / Tutup) */
.badge-status-android {
    position: absolute;
    top: 12px;
    right: 12px;
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 5px 11px;
    border-radius: 30px;
    font-size: 0.75rem;
    font-weight: 700;
    backdrop-filter: blur(8px);
    -webkit-backdrop-filter: blur(8px);
    box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
    z-index: 10;
}

.badge-status-android.buka {
    background: rgba(240, 253, 244, 0.95);
    color: #15803d;
    border: 1px solid rgba(34, 197, 94, 0.35);
}

.badge-status-android.tutup {
    background: rgba(254, 242, 242, 0.95);
    color: #b91c1c;
    border: 1px solid rgba(239, 68, 68, 0.35);
}

.badge-status-android .dot-indicator {
    width: 7px;
    height: 7px;
    border-radius: 50%;
    background: #22c55e;
    animation: pulse-dot 1.8s infinite;
}

@keyframes pulse-dot {
    0% { transform: scale(0.95); opacity: 0.8; }
    50% { transform: scale(1.3); opacity: 1; }
    100% { transform: scale(0.95); opacity: 0.8; }
}

/* Card Content Area */
.card__content {
    padding: 14px 16px 16px;
    display: flex;
    flex-direction: column;
    flex-grow: 1;
    background: #ffffff;
}

.card-category-pill {
    align-self: flex-start;
    font-size: 0.72rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: #710eff;
    background: #f3e8ff;
    padding: 3px 8px;
    border-radius: 6px;
    margin-bottom: 6px;
}

.place-name {
    font-size: 1.05rem;
    font-weight: 700;
    color: #111827;
    margin: 0 0 10px 0;
    line-height: 1.35;
    display: -webkit-box;
    -webkit-line-clamp: 1;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* Meta Info Row */
.card-meta-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-top: auto;
    padding-top: 10px;
    border-top: 1px dashed rgba(0, 0, 0, 0.08);
    gap: 8px;
}

.meta-distance {
    display: flex;
    align-items: center;
    gap: 5px;
    font-size: 0.82rem;
    font-weight: 600;
    color: #4b5563;
}

.meta-distance .meta-icon {
    width: 14px;
    height: 14px;
    color: #710eff;
    stroke: #710eff;
}

.meta-price {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
}

.meta-price .price-label {
    font-size: 0.68rem;
    color: #9ca3af;
    text-transform: uppercase;
    font-weight: 500;
    line-height: 1;
}

.meta-price .price-value {
    font-size: 0.92rem;
    font-weight: 700;
    color: #1f1f1f;
    line-height: 1.2;
}

/* Action CTA Bar */
.card-action-bar {
    margin-top: 12px;
    background: linear-gradient(135deg, #710eff, #8c32ff);
    color: #ffffff;
    border-radius: 12px;
    padding: 10px 14px;
    font-size: 0.83rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    transition: background 0.2s ease, transform 0.2s ease;
    box-shadow: 0 3px 10px rgba(113, 14, 255, 0.22);
}

.android-card:hover .card-action-bar {
    background: linear-gradient(135deg, #5b00dc, #710eff);
    box-shadow: 0 5px 14px rgba(113, 14, 255, 0.35);
}

.card-action-bar .action-icon {
    width: 14px;
    height: 14px;
    transition: transform 0.2s ease;
}

.android-card:hover .card-action-bar .action-icon {
    transform: translateX(3px);
}


    .filter__category {
    background: #fff;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    border-radius: 12px;
    margin-bottom: 2rem;
    display: inline-block;
}

.category-dropdown {
    font-size: 1rem;
    cursor: pointer;
}

.category__floating {
    position: relative;
    width: 100%;
    display: flex;
    justify-content: center;
    transform: translateY(-50%);
    z-index: 20;
}

.category__selector {
    position: relative;
    background: #fff;
    border-radius: 40px;
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
    padding: 0.6rem 1.5rem;
    display: flex;
    align-items: center;
    min-width: 50%;
    max-width: 750px;
}

.category-dropdown {
    appearance: none;
    -webkit-appearance: none;
    -moz-appearance: none;
    border: none;
    outline: none;
    background: transparent;
    width: 100%;
    font-size: 1rem;
    color: #1f1f1f;
    font-weight: 500;
    cursor: pointer;
    padding-right: 25px; /* space for icon */
}

/* Placeholder style */
.category-dropdown:invalid {
    color: #999;
}

/* Mobile */
@media (max-width: 768px) {

    .category__selector {
        width: 85%;
        max-width: 85%;
        border-radius: 18px;
        padding: 0.4rem 1rem;
        height: 50px; /* tambahkan */
        padding: 0 1rem; /* hilangkan padding atas-bawah */
    }

    .category-dropdown {
        font-size: 0.85rem;
        font-weight: 500;
        height : 100%;
    }
}

/* Dropdown icon */
.dropdown-icon {
    position: absolute;
    right: 20px;
    font-size: 0.9rem;
    color: #1f1f1f;
    pointer-events: none;
    transition: transform 0.2s ease;
}

/* Rotate icon when open */
.category__selector:has(select:focus) .dropdown-icon {
    transform: rotate(180deg);
}

/* === Dropdown open animation === */
.category-dropdown option {
    background-color: #fdfdfd;
    color: #1f1f1f;
    font-weight: 500;
    padding: 0.5rem 1rem;
    transition: background 0.2s ease, color 0.2s ease;
}

/* Hover item warna */
.category-dropdown option:hover,
.category-dropdown option:focus {
    background-color: #710eff; /* warna oranye lembut */
    color: #fdfdfd;
}

/* Untuk browser Chrome & Edge agar option hover konsisten */
.category-dropdown::-webkit-scrollbar {
    width: 8px;
}
.category-dropdown::-webkit-scrollbar-thumb {
    background: #fdfdfd;
    border-radius: 4px;
}

/* Tambahkan animasi buka dropdown (trik kecil) */
.category-dropdown:focus {
    animation: fadeInDropdown 0.2s ease-in-out;
}
@keyframes fadeInDropdown {
    0% { opacity: 0; transform: translateY(-5px); }
    100% { opacity: 1; transform: translateY(0); }
}

.event__card__wrapper {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
@media (max-width: 580px) {
    .event__card__wrapper {
        grid-template-columns: 1fr;
        gap: 1.15rem;
    }
}

/* Card container */
.event__card {
    display: block;
    background: #fff;
    border-radius: 20px;
    overflow: hidden;
    border: 1px solid rgba(0, 0, 0, 0.07);
    box-shadow: 0 4px 18px rgba(0, 0, 0, 0.05);
    transition: transform 0.25s cubic-bezier(0.4, 0, 0.2, 1), box-shadow 0.25s cubic-bezier(0.4, 0, 0.2, 1), border-color 0.25s ease;
    text-decoration: none;
    color: inherit;
    -webkit-tap-highlight-color: transparent;
}

.event__card:hover {
    transform: translateY(-5px);
    box-shadow: 0 12px 28px rgba(113, 14, 255, 0.12);
    border-color: rgba(113, 14, 255, 0.25);
}

.event__card:active {
    transform: scale(0.985);
}

/* Image section */
.event__img__box {
    position: relative;
    width: 100%;
    aspect-ratio: 16 / 9;
    overflow: hidden;
    background: #eef2f6;
}

.event__img__box img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
    transition: transform 0.3s ease;
}

.event__card:hover img {
    transform: scale(1.05);
}

/* Status badge */
.event__badge {
    position: absolute;
    top: 12px;
    left: 12px;
    background: #710eff;
    color: #fff;
    font-size: 0.8rem;
    padding: 6px 12px;
    border-radius: 20px;
    font-weight: 600;
    text-transform: capitalize;
}
.event__badge.selesai {
    background: #fde8e8;
    color: #dc2626;
}
.event__badge.berlangsung {
    background: #e6f9ee;
    color: #16a34a;
}
.event__badge.akan {
    background: #fde5be;
    color: #f39c12;
}

/* Info section */
.event__info {
    padding: 1rem;
}

.event__title {
    font-size: 1rem;
    font-weight: 700;
    margin-bottom: 0.25rem;
    color: #222;
}

.event__date {
    font-size: 0.9rem;
    color: #6b6b6b;
}

.event__date__box {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.9rem;
    color: #6b6b6b;
    font-weight: 500;
}

/* =========================
   SCROLL REVEAL
========================= */

.destination__card,
.event__card {
    opacity: 0;
    transform: translateY(25px);
    transition:
        opacity 0.6s ease,
        transform 0.6s ease;
}

.destination__card.show,
.event__card.show {
    opacity: 1;
    transform: translateY(0);
}

/* =========================
   IMAGE LOADING EFFECT
========================= */

.loc__img,
.event__img__box img {
    opacity: 0;
    transition: opacity .5s ease;
}

.loc__img.loaded,
.event__img__box img.loaded {
    opacity: 1;
}

/* =========================
   SKELETON IMAGE
========================= */

.card__image-wrapper,
.event__img__box {
    position: relative;
    overflow: hidden;
}

.card__image-wrapper::before,
.event__img__box::before {
    content: "";
    position: absolute;
    inset: 0;

    background:
    linear-gradient(
        90deg,
        #eeeeee 25%,
        #f8f8f8 50%,
        #eeeeee 75%
    );

    background-size: 200% 100%;

    animation: skeleton-loading 1.3s infinite;
    z-index: 0;
}

.card__image-wrapper img,
.event__img__box img {
    position: relative;
    z-index: 1;
}

.card__image-wrapper.loaded::before,
.event__img__box.loaded::before {
    display: none;
}

@keyframes skeleton-loading {
    0% {
        background-position: 200% 0;
    }
    100% {
        background-position: -200% 0;
    }
}

/* =========================
   PERFORMANCE BOOST
========================= */

.ex__destination {
    content-visibility: auto;
    contain-intrinsic-size: 500px;
}

</style>

{{-- ✅ Geolocation hanya reload sekali --}}
<script>
document.addEventListener("DOMContentLoaded", function () {

    const cards = document.querySelectorAll(
        '.destination__card, .event__card'
    );

    const observer = new IntersectionObserver(
        entries => {
            entries.forEach(entry => {

                if (entry.isIntersecting) {
                    entry.target.classList.add('show');
                }

            });
        },
        {
            threshold: 0.1
        }
    );

    cards.forEach(card => observer.observe(card));

});


/* =========================
   IMAGE LOADING
========================= */

document.addEventListener("DOMContentLoaded", () => {

    const images = document.querySelectorAll(
        '.loc__img, .event__img__box img'
    );

    images.forEach(img => {

        function finishLoading() {

            img.classList.add('loaded');

            const wrapper =
                img.closest('.card__image-wrapper')
                ||
                img.closest('.event__img__box');

            if(wrapper){
                wrapper.classList.add('loaded');
            }
        }

        if (img.complete) {
            finishLoading();
        } else {
            img.addEventListener('load', finishLoading);
        }

    });

    /* === GEOLOCATION: Simpan posisi user di URL (sekali saja) === */
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function (position) {
            const lat = position.coords.latitude;
            const lng = position.coords.longitude;
            localStorage.setItem('user_lat', lat);
            localStorage.setItem('user_lng', lng);
            const params = new URLSearchParams(window.location.search);

            if (!params.has('lat') || !params.has('lng')) {
                params.set('lat', lat);
                params.set('lng', lng);
                window.location.search = params.toString();
            } else {
                // ✅ Sembunyikan parameter dari URL setelah halaman selesai dimuat
                const cleanUrl = window.location.origin + window.location.pathname;
                history.replaceState(null, "", cleanUrl);
            }
        }, function (error) {
            console.warn("Gagal mendapatkan lokasi user:", error.message);
        });
    }

    /* === REDIRECT DETAIL DENGAN KOORDINAT === */
    window.redirectWithLocation = function(url) {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                function (position) {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;
                    window.location.href = `${url}?latitude=${lat}&longitude=${lng}`;
                },
                function () {
                    window.location.href = url; // fallback
                }
            );
        } else {
            window.location.href = url;
        }
    };
});
document.getElementById('categoryFilter').addEventListener('change', function() {
    const selectedCategory = this.value;
    if (!selectedCategory) return;

    // Ambil posisi pengguna dari localStorage (karena geolocation sudah diset di halamanmu)
    const lat = localStorage.getItem('user_lat');
    const lng = localStorage.getItem('user_lng');

    // Redirect dengan kategori dan lokasi
    let url = new URL(window.location.href);
    url.searchParams.set('category', selectedCategory);
    if (lat && lng) {
        url.searchParams.set('lat', lat);
        url.searchParams.set('lng', lng);
    }
    window.location.href = url.toString();
});
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        feather.replace();

    });
    
    document.addEventListener('livewire:update', function() {
        feather.replace();
    });
</script>

@endsection



