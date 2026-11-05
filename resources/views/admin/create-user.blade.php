@extends('layouts.admin')

@section('dashboard')
<link rel="stylesheet" href="{{ asset('css/admin-style.css') }}">
<title>Tambah Data User</title>

<header class="main-header">
    <h1>Tambah Data User</h1>
</header>

<main class="content add__page">

    @if ($errors->any())
    <div class="alert-error">
        <ul>
           @foreach ($errors->all() as $error)
                <li>
                    {{ str_replace('The password field confirmation does not match.', 'Konfirmasi password tidak sesuai.', $error) }}
                </li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('admin.user.store') }}" method="POST">
        @csrf

        <label>Nama</label>
        <input type="text" name="name" value="{{ old('name') }}"required>

        <label>Email / Phone</label>
        <input type="text" name="email_or_phone" value="{{ old('email_or_phone') }}" required>


        <label>Password</label>
        <div style="position: relative;">
            <input type="password" id="password" name="password" required>
            <span class="toggle-password" onclick="togglePassword()">
                <i id="password-icon" data-feather="eye"></i>
            </span>
        </div>

        <label>Konfirmasi Password</label>
        <div style="position: relative;">
            <input type="password" id="password_confirmation" name="password_confirmation" required>
            <span class="toggle-password" onclick="togglePasswordConfirm()">
                <i id="password-confirm-icon" data-feather="eye"></i>
            </span>
        </div>

        <div class="submit__btn">
            <button type="submit" class="btn primary">Simpan</button>
            <button type="button" class="btn secondary" onclick="window.location='{{ route('admin.user.index') }}'">Batal</button>
        </div>
    </form>
</main>

@if(session('success'))
<div id="successModal" class="add__modal-overlay">
    <div class="modal-box">
        <h2>{{ session('success') }}</h2>
        <div class="modal-actions">
            <a href="{{ route('admin.user.create') }}" class="btn primary">+ Tambah Data</a>
            <a href="{{ route('admin.user.index') }}" class="btn secondary">Selesai</a>
        </div>
    </div>
</div>
@endif

<style>
        .toggle-password i {
        width: 10px;   
        height: 10px;  
    }

    .toggle-password {
        display: flex;
        align-items: center;   
        justify-content: center;
        position: absolute;
        right: 10px;
        top: 50%;              
        transform: translateY(-50%);
        cursor: pointer;
        color: #1f1f1f;
    }

    .alert-error{
    background: #fde8e8;
    color: #dc2626;
    border: 1px solid #fca5a5;
    padding: 12px 16px;
    border-radius: 8px;
    margin-bottom: 20px;
    }

    .alert-error ul{
        margin: 0;
        padding-left: 18px;
    }

    .text-danger{
    color: #dc2626;
    font-size: 0.85rem;
    display: block;
    margin-top: 5px;
    }
</style>

<script>
    feather.replace();

    function togglePassword() {
        const passwordInput = document.getElementById('password');
        const passwordIcon = document.getElementById('password-icon');
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';

        passwordInput.setAttribute('type', type);

        if (type === 'text') {
            passwordIcon.setAttribute('data-feather', 'eye-off');
        } else {
            passwordIcon.setAttribute('data-feather', 'eye');
        }

        feather.replace();
    }

    function togglePasswordConfirm() {
        const passwordInput = document.getElementById('password_confirmation');
        const passwordIcon = document.getElementById('password-confirm-icon');
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';

        passwordInput.setAttribute('type', type);

        if (type === 'text') {
            passwordIcon.setAttribute('data-feather', 'eye-off');
        } else {
            passwordIcon.setAttribute('data-feather', 'eye');
        }

        feather.replace();
    }
</script>

@endsection
