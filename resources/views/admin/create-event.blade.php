@extends('layouts.admin')

@section('dashboard')
<link rel="stylesheet" href="{{ asset('css/admin-style.css') }}">
<title>Tambah Event</title>

<header class="main-header">
    <h1>Tambah Event</h1>
</header>

<main class="content add__page">
    <form action="{{ route('admin.events.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <label>Nama Event</label>
        <input type="text" name="title" value="{{ old('title') }}" required>

        <label>Deskripsi</label>
        <textarea name="description">{{ old('description') }}</textarea>

        <label>Kategori</label>
        <select name="category">
            <option value="">-- Pilih Kategori --</option>
            <option value="Festival & Budaya" {{ old('category') == 'Festival & Budaya' ? 'selected' : '' }}>Festival & Budaya</option>
            <option value="Musik & Hiburan" {{ old('category') == 'Musik & Hiburan' ? 'selected' : '' }}>Musik & Hiburan</option>
            <option value="Kuliner & Pasar Rakyat" {{ old('category') == 'Kuliner & Pasar Rakyat' ? 'selected' : '' }}>Kuliner & Pasar Rakyat</option>
            <option value="Olahraga & Komunitas" {{ old('category') == 'Olahraga & Komunitas' ? 'selected' : '' }}>Olahraga & Komunitas</option>
            <option value="Edukasi & Religi" {{ old('category') == 'Edukasi & Religi' ? 'selected' : '' }}>Edukasi & Religi</option>
        </select>


        <label>Lokasi</label>
        <input type="text" name="location" value="{{ old('location') }}">

        <label>Tanggal Mulai</label>
        <input type="date" name="start_date" value="{{ old('start_date') }}" required>

        <label>Tanggal Selesai</label>
        <input type="date" name="end_date" value="{{ old('end_date') }}">

        <label>Status</label>
        <select name="status" required>
            <option value="Akan Datang">Akan Datang</option>
            <option value="Dimulai">Dimulai</option>
            <option value="Selesai">Selesai</option>
        </select>


        <label>Foto Event (opsional)</label>
        <input type="file" name="foto__event[]" multiple accept="image/*" onchange="previewImages(event)">
        <div id="preview" style="margin:10px 0; display:flex; gap:10px; flex-wrap:wrap;"></div>

        <div class="submit__btn">
            <button type="submit" class="btn primary">Simpan</button>
            <button type="button" class="btn secondary" onclick="window.location='{{ route('admin.events.index') }}'">Batal</button>
        </div>
    </form>
</main>

@if(session('success'))
<div id="successModal" class="add__modal-overlay">
    <div class="modal-box">
        <h2>{{ session('success') }}</h2>
        <div class="modal-actions">
            <a href="{{ route('admin.events.index') }}" class="btn primary">Selesai</a>
        </div>
    </div>
</div>
@endif

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
// === Auto set status berdasarkan start_date ===
    document.addEventListener("DOMContentLoaded", function () {
    const startInput = document.querySelector('input[name="start_date"]');
    const endInput = document.querySelector('input[name="end_date"]');
    const statusField = document.querySelector('input[name="status"], select[name="status"]');
    
    function updateStatus() {
        const today = new Date().toISOString().split("T")[0];
        const start = startInput.value;
        const end = endInput.value || start;

        if (!start) return;

        if (end < today) {
            statusField.value = "Selesai";
        } else if (start > today) {
            statusField.value = "Akan Datang";
        } else if (start <= today && end >= today) {
            statusField.value = "Dimulai";
        } else {
            statusField.value = "Akan Datang";
        }
    }

    startInput.addEventListener("change", updateStatus);
    endInput.addEventListener("change", updateStatus);

    // Trigger awal saat form dibuka
    updateStatus();
});
</script>

@endsection
