@extends('layouts.app')

@section('content')

@if (!$event)
    <section class="ex__destination" style="padding:2rem 7%;">
        <p>Data event tidak ditemukan.</p>
    </section>
@else
<section class="event-detail-container">
    <div class="event-detail-header">
        <div class="event-detail-photo">
           @php
                    $photosEvent = json_decode($event->foto__event, true) ?? [];
                    $photoPathEvent = !empty($photosEvent)
                        ? 'storage/' . $photosEvent[0]
                        : 'storage/event/default-16x9.png';
                    @endphp

                    <img src="{{ asset($photoPathEvent) }}" class="loc__img" loading="lazy">
            <span class="event-status badge-status {{ strtolower($event->status ?? '') }}">
                {{ $event->status ?? '-' }}
            </span>
        </div>
        <h1 class="event-detail-title">{{ $event->title }}</h1>
        <span><p class="event-description">{{ $event->description ?? 'Tidak ada deskripsi.' }}</p></span>
    </div>


    <div class="event-detail-content">
            {{-- ABOUT EVENT --}}
        <div class="event-section">
            <div class="event-category">{{ $event->category ?? '-' }}</div>
        </div>
        {{-- DATE & TIME --}}
        <div class="event-section">
            <h2 class="event-section-title">Event date</h2>
            <div class="event-section-item">
                <i class="fa-regular fa-calendar"></i>
                <span>
                    {{ \Carbon\Carbon::parse($event->start_date)->format('l, F j') }} -
                    {{ \Carbon\Carbon::parse($event->end_date)->format('l, F j') }}
                </span>
            </div>
        </div>

        {{-- LOCATION --}}
        <div class="event-section">
            <h2 class="event-section-title">Location</h2>
            <div class="event-section-item">
                <i class="fa-solid fa-location-dot"></i>
                <div>
                    <p class="event-location-name">{{ $event->location ?? '-' }}</p>
                </div>
            </div>
        </div>

    </div>
</section>
<footer class="footer">
    <p>&copy; {{ date('Y') }} WisataTomohon. All rights reserved.</p>
</footer>
@endif

@endsection

<style>
    :root {
        --primary: #fdfdfd;
        --sec: #710eff;
        --txt: #1f1f1f;
        --muted: #6b6b6b;
        --card-bg: #ffffff;
        --radius: 12px;
        --shadow: 0 6px 18px rgba(34, 34, 34, 0.06);
        --gap: 18px;
    }

    /* page layout */
    .event-detail-container {
        padding: 2rem 7%;
        background: var(--primary);
        color: var(--txt);
    }

    .event-detail-header {
        text-align: left;
        margin-bottom: 1rem;
    }

    .event-detail-photo {
        position: relative;
        display: inline-block;
        width: 100%;
        max-width: 1200px;
    }

    .event-detail-photo .loc__img {
        width: 100%;
        max-height: 480px;
        object-fit: cover;
        border-radius: 10px;
        display: block;
        box-shadow: var(--shadow);
    }

    /* badge-status re-used: keep visual consistent with other badges */
    .badge-status {
        position: absolute;
        top: 1.5rem;
        left: 1.5rem;
        padding: 0.6rem 1rem;
        font-size: 0.9rem;
        background: var(--sec);
        color: #fff;
        border-radius: 8px;
        text-transform: capitalize;
        font-weight: 600;
    }

    .event-detail-title {
        font-size: 2.2rem;
        margin-top: 1rem;
        color: var(--txt);
        font-weight: 700;
    }

    .event-detail-content {
        width: 100%;
        max-width: 900px;
        margin: 0!important;
        padding: 0!important;
        display: flex;
        flex-direction: column;
        gap: 0rem!important;
        align-items: flex-start;
    }

    /* ✅ Hapus tampilan card */
.event-section {
    margin-bottom: 2rem;
    background: transparent; /* Tidak ada latar */
    color: var(--txt);
    padding: 0; /* Hilangkan padding dalam box */
    border-radius: 0;
    box-shadow: none;
    border: none;
}

/* ✅ Buat section title lebih elegan */
.event-section-title {
    font-size: 1.2rem;
    margin-bottom: 0.75rem;
    font-weight: 600;
    color: var(--txt);
}

/* ✅ Item dalam section */
.event-section-item {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin-bottom: 0.5rem;
    color: var(--muted);
    font-size: 0.98rem;
}

/* ✅ Bagian kategori */
.event-category {
    display: inline-block;
    background: var(--sec);
    padding: 6px 12px;
    border-radius: 20px;
    color: #fff;
    margin-bottom: 1rem;
    font-weight: 600;
    font-size: 0.85rem;
}

/* ✅ Deskripsi event */
.event-description {
    font-size: 1rem;
    line-height: 1.7;
    color: var(--txt);
}


    /* responsive */
    @media (max-width: 900px) {
        .event-detail-photo .loc__img {
            max-height: 320px;
        }
        .event-detail-title {
            font-size: 1.6rem;
        }
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
        }

        .event-detail-container {
            flex: 1; 
        }
    }

    @media (max-width: 600px) {
        .event-detail-container { padding: 1.25rem; }
        .event-section-item { font-size: 0.9rem; }
            html, body {
            height: 100%;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
        }

        .event-detail-container {
            flex: 1; 
        }
    }

    .footer {
    background-color: var(--primary);
    color: var(--txt);
    padding: 10px;
    text-align: center;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        feather.replace();

    });
    
    document.addEventListener('livewire:update', function() {
        feather.replace();
    });
</script>