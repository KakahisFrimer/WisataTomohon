@extends('layouts.admin')

@section('dashboard')
<link rel="stylesheet" href="{{ asset('css/admin-style.css') }}">
<title>Edit Data Wisata</title>

<header class="main-header">
    <h1>Edit Data Wisata</h1>
</header>

<main class="content add__page">
    <form action="{{ route('admin.wisata.update', $wisata->id__tmpt) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <label>Nama Tempat</label>
        <input type="text" name="name" value="{{ old('name', $wisata->name) }}" required>

        <label>Alamat</label>
        <textarea name="address" required>{{ old('address', $wisata->address) }}</textarea>

        <label>Jenis Wisata</label>
        <select name="category" required>
            <option value="Wisata Alam" {{ $wisata->category == 'Wisata Alam' ? 'selected' : '' }}>Wisata Alam</option>
            <option value="Wisata Buatan" {{ $wisata->category == 'Wisata Buatan' ? 'selected' : '' }}>Wisata Buatan</option>
            <option value="Wisata Budaya" {{ $wisata->category == 'Wisata Budaya' ? 'selected' : '' }}>Wisata Budaya</option>
            <option value="Kuliner" {{ $wisata->category == 'Kuliner' ? 'selected' : '' }}>Kuliner</option>
            <option value="Akomodasi" {{ $wisata->category == 'Akomodasi' ? 'selected' : '' }}>Akomodasi</option>
        </select>

        <label>Kontak</label>
        <input type="text" name="contact" value="{{ old('contact', $wisata->contact ?? '-') }}">

        <label>Status</label>
        <select name="status" required>
            <option value="Beroperasi" {{ $wisata->status == 'Beroperasi' ? 'selected' : '' }}>Beroperasi</option>
            <option value="Berhenti beroperasi" {{ $wisata->status == 'Berhenti beroperasi' ? 'selected' : '' }}>Berhenti beroperasi</option>
        </select>

        <label>Foto Lama</label>
        <div id="preview" style="margin:10px 0; display:flex; gap:20px; flex-wrap:wrap;">
            @if($wisata->foto__tmpt)
                @foreach(json_decode($wisata->foto__tmpt, true) as $foto)
            <div style="position:relative; display:inline-block; text-align:center;">
                <img src="{{ asset('storage/'.$foto) }}" height="100" style="border-radius:8px; display:block; margin:auto;">

                <input type="checkbox" name="hapus_foto[]" value="{{ $foto }}" id="hapus-{{ $loop->index }}" style="display:none;">

                <label for="hapus-{{ $loop->index }}" class="trash-icon" 
                    style="cursor:pointer; display:block; margin-top:5px; font-size:12px; position:relative;">
                    <i data-feather="trash-2" style="width:15px; height:15px;"></i>
                    <span class="tooltip-text" 
                        style="visibility:hidden; opacity:0; transition:opacity 0.2s; 
                               position:absolute; bottom:125%; left:50%; transform:translateX(-50%);
                               background:#333; color:#fff; padding:3px 6px; border-radius:4px; font-size:12px; white-space:nowrap;">
                        Klik untuk hapus foto
                    </span>
                </label>
            </div>
        @endforeach
    @else
        <span>Tidak ada foto</span>
    @endif
</div>

        <label>Upload Foto Baru (opsional)</label>
        <input type="file" name="foto__tmpt[]" multiple accept="image/*" onchange="previewImages(event)">
        <div id="preview" style="margin-top:10px; display:flex; gap:10px; flex-wrap:wrap;"></div>


        <label>Lokasi</label>
        <div id="map" style="height:300px; margin-bottom:10px;"></div>
        <input type="text" name="latitude" id="latitude" value="{{ old('latitude', $wisata->latitude) }}" required>
        <input type="text" name="longitude" id="longitude" value="{{ old('longitude', $wisata->longitude) }}" required>


        <div id="detail-wrapper">
            @foreach($wisata->details as $detail)
                <div class="detail-item">
                    <label>Hari Buka</label>
                    <input type="text" name="hari__buka[]" value="{{ $detail->hari__buka }}">
                    <label>Jam Buka</label>
                    <input type="time" name="jam__buka[]" value="{{ $detail->jam__buka }}">
                    <label>Jam Tutup</label>
                    <input type="time" name="jam__tutup[]" value="{{ $detail->jam__tutup }}">
                    <label>Harga Tiket</label>
                    <input type="number" name="harga__tiket[]" value="{{ $detail->harga__tiket }}">
                </div>
            @endforeach
        </div>


        <div class="submit__btn">
            <button type="submit" class="btn primary">Update</button>
            <button type="button" onclick="window.location='{{ route('admin.wisata.index') }}'" class="btn secondary">Batal</button>
        </div>
    </form>

</main>

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
<div id="popup" class="popup">
    <div class="popup-content">
        <h2>{{ session('success') }}</h2>
        <div style="margin-top: 20px; display: flex; gap: 10px; justify-content: center;">
            <a href="{{ route('admin.wisata.index') }}" class="btn">Selesai</a>
        </div>
    </div>
</div>

<script>
    function closePopup() {
        document.getElementById('popup').style.display = 'none';
    }
    // otomatis tampil saat ada session success
    document.getElementById('popup').style.display = 'flex';
</script>

<style>
    .popup {
        position: fixed;
        top: 0; left: 0;
        width: 100%; height: 100%;
        background: rgba(0, 0, 0, 0.6);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 9999;
    }
    .popup-content {
        background: #fff;
        padding: 20px 30px;
        border-radius: 12px;
        text-align: center;
        box-shadow: 0 5px 15px rgba(0,0,0,0.3);
        animation: fadeIn 0.3s ease;
    }
    @keyframes fadeIn {
        from {opacity: 0; transform: scale(0.9);}
        to {opacity: 1; transform: scale(1);}
    }
</style>
@endif
<script>
document.addEventListener("DOMContentLoaded", function() {
    // Feather icons render
    if (typeof feather !== "undefined") {
        feather.replace();
    }

    // Ambil semua checkbox hapus
    document.querySelectorAll('input[name="hapus_foto[]"]').forEach(function(checkbox) {
        let label = document.querySelector('label[for="'+checkbox.id+'"]');
        let icon = label.querySelector("svg"); 
        let tooltip = label.querySelector(".tooltip-text");

        label.addEventListener("click", function() {
            setTimeout(() => {
                if (checkbox.checked) {
                    icon.style.stroke = "#d9534f"; 
                } else {
                    icon.style.stroke = ""; 
                }
            }, 10);
        });

        label.addEventListener("mouseenter", function() {
            tooltip.style.visibility = "visible";
            tooltip.style.opacity = "1";
        });
        label.addEventListener("mouseleave", function() {
            tooltip.style.visibility = "hidden";
            tooltip.style.opacity = "0";
        });
    });
});
</script>



@endsection
