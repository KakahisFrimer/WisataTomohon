@extends('layouts.admin')

@section('dashboard')
<link rel="stylesheet" href="{{ asset('css/admin-style.css') }}">
<title>Tambah Data Wisata</title>

<header class="main-header">
    <h1>Tambah Data Wisata</h1>
</header>

<main class="content add__page">
    <form action="{{ route('admin.wisata.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <label>Nama Tempat</label>
        <input type="text" name="name" value="{{ old('name') }}" required>
        @error('name')
            <small class="error-text">{{ $message }}</small>
        @enderror

        <label>Alamat</label>
        <textarea name="address" required>{{ old('address') }}</textarea>
        @error('address')
            <small class="error-text">{{ $message }}</small>
        @enderror       

        <label>Jenis Wisata</label>
        <select name="category" required>
            <option value="Wisata Alam">Wisata Alam</option>
            <option value="Wisata Buatan">Wisata Buatan</option>
            <option value="Wisata Budaya">Wisata Budaya</option>
            <option value="Kuliner">Kuliner</option>
            <option value="Akomodasi">Akomodasi</option>
        </select>

        <label>Kontak</label>
        <input type="text" name="contact">

        <label>Status</label>
        <select name="status" required>
            <option value="Beroperasi">Beroperasi</option>
            <option value="Berhenti beroperasi">Berhenti beroperasi</option>
        </select>

        <label>Foto (bisa pilih banyak)</label>
        <input type="file" name="foto__tmpt[]" multiple accept="image/*" onchange="previewImages(event)">

        <div id="preview" style="margin:10px 0; display:flex; gap:10px; flex-wrap:wrap;"></div>

        <label>Lokasi</label>
        <div id="map" style="height:300px; margin-bottom:10px;"></div>
        <input type="text" name="latitude" id="latitude" placeholder="Latitude" value="{{ old('latitude') }}" required>
        @error('latitude')
            <small class="error-text">{{ $message }}</small>
        @enderror
        <input type="text" name="longitude" id="longitude" placeholder="Longitude" value="{{ old('longitude') }}" required>
        @error('longitude')
            <small class="error-text">{{ $message }}</small>
        @enderror

        <label>Hari Buka</label>
        <input type="text" name="hari__buka[]" value="{{ old('hari__buka.0') }}" placeholder="(contoh: Senin/Setiap Hari)" required>
        @error('hari__buka.0')
            <small class="error-text">{{ $message }}</small>
        @enderror
        <label>Jam Buka</label>
        <input type="time" name="jam__buka[]" required >
        @error('jam__buka.0')
            <small class="error-text">{{ $message }}</small>
        @enderror
        <label>Jam Tutup</label>
        <input type="time" name="jam__tutup[]" required>
        @error('jam__tutup.0')
            <small class="error-text">{{ $message }}</small>
        @enderror
        <label>Harga Tiket</label>
        <input type="number" name="harga__tiket[]" value="{{ old('harga__tiket.0') }}" placeholder="Harga Tiket" required>
        @error('harga__tiket.0')
            <small class="error-text">{{ $message }}</small>
        @enderror
        <div class="submit__btn">
            <button type="submit" class="btn primary">Simpan</button>
            <button type="button" class="btn secondary" onclick="window.location='{{ route('admin.wisata.index') }}'">Batal</button></div>
    </form>
</main>

{{-- Leaflet JS --}}
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css"/>
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

<script>
function previewImages(event) {
    let preview = document.getElementById('preview');
    preview.innerHTML = '';
    for (let file of event.target.files) {
        let reader = new FileReader();
        reader.onload = e => {
            let img = document.createElement('img');
            img.src = e.target.result;
            img.style.height = '80px';
            img.style.borderRadius = '8px';
            preview.appendChild(img);
        };
        reader.readAsDataURL(file);
    }
}


// Leaflet Map
document.addEventListener("DOMContentLoaded", function() {
    let map = L.map('map').setView([-1.265, 116.831], 5);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19
    }).addTo(map);

    let marker;

   
    map.on('click', function(e) {
        if (marker) map.removeLayer(marker);
        marker = L.marker(e.latlng).addTo(map);
        document.getElementById("latitude").value = e.latlng.lat;
        document.getElementById("longitude").value = e.latlng.lng;
    });

   // Ketik manual -> update marker
    function updateMarker() {
        let lat = parseFloat(document.getElementById("latitude").value);
        let lng = parseFloat(document.getElementById("longitude").value);
        if (!isNaN(lat) && !isNaN(lng)) {
            if (marker) map.removeLayer(marker);
            marker = L.marker([lat, lng]).addTo(map);
            map.setView([lat, lng], 13);
        }
    }

    
    document.getElementById("latitude").addEventListener("input", updateMarker);
    document.getElementById("longitude").addEventListener("input", updateMarker);

});
</script>

@if(session('success'))
<div id="successModal" class="add__modal-overlay">
    <div class="modal-box">
        <h2>{{ session('success') }}</h2>
        <div class="modal-actions">
            <a href="{{ route('admin.wisata.create') }}" class="btn primary">+ Tambah Data</a>
            <a href="{{ route('admin.wisata.index') }}" class="btn secondary">Selesai</a>
        </div>
    </div>
</div>
@endif

@endsection
