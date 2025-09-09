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
        <input type="text" name="name" required>

        <label>Alamat</label>
        <textarea name="address" required></textarea>

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
        <input type="text" name="latitude" id="latitude" placeholder="Latitude"  required>
        <input type="text" name="longitude" id="longitude" placeholder="Longitude"  required>

        <label>Hari Buka</label>
        <input type="text" name="hari__buka[]" placeholder="(contoh: Senin/Setiap Hari)">
        <label>Jam Buka</label>
        <input type="time" name="jam__buka[]">
        <label>Jam Tutup</label>
        <input type="time" name="jam__tutup[]">
        <label>Harga Tiket</label>
        <input type="number" name="harga__tiket[]" placeholder="Harga Tiket">
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

    // Klik di peta -> update input
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

    // Event listener pakai input (biar realtime saat ngetik)
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
