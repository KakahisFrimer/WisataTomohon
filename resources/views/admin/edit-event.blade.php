@extends('layouts.admin')

@section('dashboard')
<link rel="stylesheet" href="{{ asset('css/admin-style.css') }}">
<title>Edit Event</title>

<header class="main-header">
    <h1>Edit Event</h1>
</header>

<main class="content add__page">
    <form action="{{ route('admin.events.update', $event->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <label>Nama Event</label>
        <input type="text" name="title" value="{{ old('title', $event->title) }}" required>

        <label>Deskripsi</label>
        <textarea name="description">{{ old('description', $event->description) }}</textarea>

        <label>Kategori</label>
        <select name="category">
            <option value="">-- Pilih Kategori --</option>
            <option value="Festival & Budaya" {{ old('category', $event->category) == 'Festival & Budaya' ? 'selected' : '' }}>Festival & Budaya</option>
            <option value="Musik & Hiburan" {{ old('category', $event->category) == 'Musik & Hiburan' ? 'selected' : '' }}>Musik & Hiburan</option>
            <option value="Kuliner & Pasar Rakyat" {{ old('category', $event->category) == 'Kuliner & Pasar Rakyat' ? 'selected' : '' }}>Kuliner & Pasar Rakyat</option>
            <option value="Olahraga & Komunitas" {{ old('category', $event->category) == 'Olahraga & Komunitas' ? 'selected' : '' }}>Olahraga & Komunitas</option>
            <option value="Edukasi & Religi" {{ old('category', $event->category) == 'Edukasi & Religi' ? 'selected' : '' }}>Edukasi & Religi</option>
        </select>

        <label>Lokasi</label>
        <input type="text" name="location" value="{{ old('location', $event->location) }}">

        <label>Tanggal Mulai</label>
        <input type="date" name="start_date" value="{{ old('start_date', $event->start_date) }}" required>

        <label>Tanggal Selesai</label>
        <input type="date" name="end_date" value="{{ old('end_date', $event->end_date) }}">

        <label>Status</label>
        <select name="status" required>
            <option value="Akan Datang" {{ old('status', $event->status) == 'Akan Datang' ? 'selected' : '' }}>Akan Datang</option>
            <option value="Dimulai" {{ old('status', $event->status) == 'Dimulai' ? 'selected' : '' }}>Dimulai</option>
            <option value="Selesai" {{ old('status', $event->status) == 'Selesai' ? 'selected' : '' }}>Selesai</option>
        </select>

        <label>Foto Event</label>
        @if($event->foto__event)
            <div style="margin:10px 0;">
                <img src="{{ asset('storage/'.$event->foto__event) }}" alt="Foto Event" style="height:100px; border-radius:8px;">
            </div>
        @endif
        <input type="file" name="foto__event" accept="image/*" onchange="previewImages(event)">
        <div id="preview" style="margin:10px 0; display:flex; gap:10px; flex-wrap:wrap;"></div>

        <div class="submit__btn">
            <button type="submit" class="btn primary">Update</button>
            <button type="button" class="btn secondary" onclick="window.location='{{ route('admin.events.index') }}'">Batal</button>
        </div>
    </form>
</main>

@if(session('success'))
<div id="successModal" class="add__modal-overlay">
    <div class="modal-box">
        <h2>{{ session('success') }}</h2>
        <div class="modal-actions">
            <a href="{{ route('admin.events.create') }}" class="btn primary">+ Tambah Event</a>
            <a href="{{ route('admin.events.index') }}" class="btn secondary">Selesai</a>
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

    document.addEventListener("DOMContentLoaded", function () {
        const startInput = document.querySelector('input[name="start_date"]');
        const endInput = document.querySelector('input[name="end_date"]');
        const statusField = document.querySelector('select[name="status"]');
        
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

        updateStatus();
    });
</script>

@endsection
