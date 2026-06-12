@extends('layouts.app')

@section('content')

<!-- Landing Page -->
<section class="landing__page" id="home">
    <main class="container">
        <h3>Welcome To</h3>
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


<!-- Destinasi Terdekat -->
<section id="near__destination" class="ex__destination">
    <div class="destination__title">
        <h3>Nearest Destination</h3>
        <a href="#"><button>View All</button></a>
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

                $now = \Carbon\Carbon::now()->format('H:i');
                $status = ($jamBuka && $jamTutup && $now >= $jamBuka && $now <= $jamTutup) ? 'Buka' : 'Tutup';

                $harga = $detail->harga__tiket ?? '-';
            @endphp

            <div class="destination__card">
                <a href="{{ route('wisata.show', $item->id__tmpt) }}" class="card card-link">
                    <div class="card__image-wrapper">
                        <img src="{{ asset($photoPath) }}" class="loc__img" loading="lazy">

                        @php
                            $avgRating = $item->reviews()->avg('rating');
                        @endphp

                        <span class="badge-rating top-left">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                                <path d="M12 .587l3.668 7.431 8.2 1.193-5.934 5.782 1.401 8.173L12 18.897l-7.335 3.869 1.401-8.173L.132 9.211l8.2-1.193z"/>
                            </svg>
                            {{ number_format($avgRating ?? 0, 2) }}
                        </span>

                        <span class="badge-status top-right {{ strtolower($status) }}">{{ $status }}</span>
                    </div>


                    <div class="card__content">
                        <h2 class="place-name">{{ $item->name }}</h2>
                        <p class="place-category">{{ $item->category ?? '-' }}</p>

                        <div class="info__container">
                            <div class="info-line">
                                <span class="distance-placeholder">
                                    Jarak:
                                    @if(isset($item->distance))
                                        {{ number_format($item->distance, 2, ',', '.') }} km
                                    @else
                                        -
                                    @endif
                                </span>
                            </div>
                            <div class="info-line">
                                <span class="price">
                                    @if(is_numeric($harga))
                                        Rp {{ number_format($harga, 0, ',', '.') }}
                                    @else
                                        {{ $harga }}
                                    @endif
                                </span>
                            </div>
                        </div>

                        <button class="visit-btn" onclick="redirectWithLocation('{{ route('wisata.show', $item->id__tmpt) }}')">
                            Visit
                        </button>
                    </div>
                </a>
            </div>
        @empty
            <p class="empty__state">Tidak ada destinasi terdekat.</p>
        @endforelse
    </div>
</section>

<!-- Populer Destinasi -->
<section id="ex__destination" class="ex__destination">
    <div class="destination__title">
        <h3>Popular Destination</h3>
        <a href="#"><button>View All</button></a>
    </div>

    <div class="card__desti">
        @forelse($destinations as $item)
            @php
                $photos = json_decode($item->foto__tmpt, true) ?? [];
                $photoPath = !empty($photos) ? 'storage/' . $photos[0] : 'storage/wisata/default-16x9.png';

                $detail = $item->details->first();
                $jamBuka = $detail->jam__buka ?? null;
                $jamTutup = $detail->jam__tutup ?? null;

                $now = \Carbon\Carbon::now()->format('H:i');
                $status = ($jamBuka && $jamTutup && $now >= $jamBuka && $now <= $jamTutup) ? 'Buka' : 'Tutup';

                $harga = $detail->harga__tiket ?? '-';
            @endphp

            <div class="destination__card">
                <a href="{{ route('wisata.show', $item->id__tmpt) }}" class="card card-link">
                    <div class="card__image-wrapper">
                        <img src="{{ asset($photoPath) }}" class="loc__img" loading="lazy">

                        @php
                            $avgRating = $item->reviews()->avg('rating');
                        @endphp

                        <span class="badge-rating top-left">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                                <path d="M12 .587l3.668 7.431 8.2 1.193-5.934 5.782 1.401 8.173L12 18.897l-7.335 3.869 1.401-8.173L.132 9.211l8.2-1.193z"/>
                            </svg>
                            {{ number_format($avgRating ?? 0, 2) }}
                        </span>

                        <span class="badge-status top-right {{ strtolower($status) }}">{{ $status }}</span>
                    </div>

                    <div class="card__content">
                        <h2 class="place-name">{{ $item->name }}</h2>
                        <p class="place-category">{{ $item->category ?? '-' }}</p>

                        <div class="info__container">
                            <div class="info-line">
                                <span class="distance-placeholder">
                                    Jarak:
                                    @if(isset($item->distance))
                                        {{ number_format($item->distance, 2, ',', '.') }} km
                                    @else
                                        -
                                    @endif
                                </span>
                            </div>
                            <div class="info-line">
                                <span class="price">
                                    @if(is_numeric($harga))
                                        Rp {{ number_format($harga, 0, ',', '.') }}
                                    @else
                                        {{ $harga }}
                                    @endif
                                </span>
                            </div>
                        </div>

                        <button class="visit-btn" onclick="redirectWithLocation('{{ route('wisata.show', $item->id__tmpt) }}')">
                            Visit
                        </button>
                    </div>
                </a>
            </div>
        @empty
            <p class="empty__state">Belum ada destinasi populer.</p>
        @endforelse
    </div>
</section>

<!-- Event Section -->
<section id="event__section" class="ex__destination">
    <div class="destination__title">
        <h3>Event</h3>
        <a href="#"><button>View All</button></a>
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
    <p>&copy; {{ date('Y') }} WisataTomohon. All rights reserved.</p>
</footer>

<style>
    /* Rating */
    .badge-rating.top-left {
    position: absolute;
    top: 10px;
    left: 10px;
    padding: 2px 7px 2px 6px;
    border-radius: 12px;
    font-size: 0.7rem;
    font-weight: 600;
    background: #fff8db; /* kuning lembut */
    color: #b7791f;
    display: inline-flex;
    align-items: center;
    gap: 3px;
    line-height: 1;
    vertical-align: middle;
}

.badge-rating.top-left svg {
    width: 12px;
    height: 12px;
    fill: #facc15; /* gold fill */
    flex-shrink: 0;
    position: relative;
    top: -0.5px; /* ini buat sejajar dengan angka */
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
    gap: 1.5rem;
    margin-top: 1.5rem;
}

/* Card container */
.event__card {
    display: block;
    background: #fff;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 4px 15px rgba(0,0,0,0.08);
    transition: transform 0.25s ease, box-shadow 0.25s ease;
    text-decoration: none;
    color: inherit;
}

.event__card:hover {
    transform: translateY(-6px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.12);
}

/* Image section */
.event__img__box {
    position: relative;
    width: 100%;
    aspect-ratio: 16 / 9;
    overflow: hidden;
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

</style>

{{-- ✅ Geolocation hanya reload sekali --}}
<script>
document.addEventListener("DOMContentLoaded", function () {

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



