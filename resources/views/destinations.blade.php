@extends('layouts.app') <!-- Pastikan layout utama ada -->

@section('content')
<div class="search-container">
    <form method="get" id="searchForm">
        <h1>Explore Destination Around You</h1>
        <select name="searchType" id="searchType">
            <option value="" disabled selected>Pilih Kategori Wisata</option>
            <option value="Wisata Alam">Wisata Alam</option>
            <option value="WisBudaya">Wisata Budaya</option>
            <option value="WisBuatan">Wisata Buatan</option>
        </select>
        <button type="submit">Search</button>
    </form>
</div>

@php
    $grouped = $destinations->groupBy('jenis__wisata');
@endphp

@foreach (['Wisata Alam', 'Wisata Budaya', 'Wisata Buatan'] as $jenis)
    <section id="ex__destination" class="ex__destination">
        <div class="destination__title">
            <h3>{{ $jenis }}</h3>
        </div>

        <div class="card__desti">
            @foreach ($grouped[$jenis] ?? [] as $item)
                @php
                    $foto_tmpt = json_decode($item->foto__tmpt, true);
                    $img = !empty($foto_tmpt) ? $foto_tmpt[0] : null;
                @endphp
                <div class="destination__card" data-destination-id="{{ $item->id__tmpt }}">
                    <div class="card">
                        @if ($img)
                            <a href="{{ url('destination-detail', ['id' => $item->id__tmpt]) }}">
                                <img src="{{ asset('images/data__wisata/' . $img) }}" class="loc__img">
                            </a>
                        @endif
                        <div class="card__content">
                            <h2>{{ $item->nama__tmpt }}</h2>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </section>
@endforeach
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.destination__card').forEach(function(card) {
            card.addEventListener('click', function() {
                var destinationId = this.dataset.destinationId;
                window.location.href = '/destination-detail?id=' + destinationId;
            });
        });

        document.getElementById("searchForm").addEventListener("submit", function(e) {
            e.preventDefault();
            var selectedPage = document.getElementById("searchType").value;
            let url = {
                'Wisata Alam': '/wisata/alam',
                'WisBudaya': '/wisata/budaya',
                'WisBuatan': '/wisata/buatan'
            }[selectedPage] ?? '/destination';

            window.location.href = url;
        });
    });
</script>
@endpush
