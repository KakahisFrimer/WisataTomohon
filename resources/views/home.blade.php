@extends('layouts.app')

@section('content')

<!-- Landing Page -->
<section class="landing__page" id="home">
    <main class="container">
        <h3>Welcome To</h3>
        <h1>TOMOHON CITY</h1>
        <p>Explore the Beuaty of Tomohon's Nature and Culture</p>
        <a href="#ex__destination"><button>Start Explore</button></a>
    </main>
</section>

<!-- Destinations -->
<section id="ex__destination" class="ex__destination">
    <div class="destination__title">
        <h3>Destinations</h3>
        <a href="#"><button>View All</button></a>
    </div>

    <div class="card__desti">
        @foreach($destinations as $item)
        <div class="destination__card">
            <div class="card">
                @if(!empty(json_decode($item->foto__tmpt)))
                    <img src="{{ asset('images/data__wisata/' . json_decode($item->foto__tmpt)[0]) }}" class="loc__img">
                @endif
                <div class="card__content">
                    <h2>{{ $item->name }}</h2>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</section>

<!-- Culinary -->
<section id="culinary" class="ex__destination">
    <div class="destination__title">
        <h3>Culinary</h3>
        <a href="#"><button>View All</button></a>
    </div>

    <div class="card__desti">
        @foreach($culinary as $item)
        <div class="destination__card">
            <div class="card">
                @if(!empty(json_decode($item->foto__tmpt)))
                    <img src="{{ asset('images/data__wisata/' . json_decode($item->foto__tmpt)[0]) }}" class="loc__img">
                @endif
                <div class="card__content">
                    <h2>{{ $item->name }}</h2>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</section>

<!-- Accommodations -->
<section id="accommodation" class="ex__destination">
    <div class="destination__title">
        <h3>Accommodations</h3>
        <a href="#"><button>View All</button></a>
    </div>

    <div class="card__desti">
        @foreach($accommodations as $item)
        <div class="destination__card">
            <div class="card">
                @if(!empty(json_decode($item->foto__tmpt)))
                    <img src="{{ asset('images/data__wisata/' . json_decode($item->foto__tmpt)[0]) }}" class="loc__img">
                @endif
                <div class="card__content">
                    <h2>{{ $item->name }}</h2>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</section>

@endsection
