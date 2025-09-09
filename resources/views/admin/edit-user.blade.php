@extends('layouts.admin')

@section('dashboard')
<link rel="stylesheet" href="{{ asset('css/admin-style.css') }}">
<title>Edit Data User</title>

<header class="main-header">
    <h1>Edit Data User</h1>
</header>

<main class="content add__page">
    <form action="{{ route('admin.user.update', $user->id) }}" method="POST">
        @csrf
        @method('PUT')

        <label>Nama</label>
        <input type="text" name="name" value="{{ old('name', $user->name) }}" required>

        <label>Email / Phone</label>
        <input type="text" name="email_or_phone" value="{{ old('email_or_phone', $user->email_or_phone) }}" required>

        <label>Password Baru (kosongkan jika tidak diganti)</label>
        <div style="position: relative; width: 100%;">
            <input type="password" id="password" name="password" style="width: 100%; padding-right: 35px;">
            <span class="toggle-password" onclick="togglePassword()">
                <i id="password-icon" data-feather="eye"></i>
            </span>
        </div>

        <label>Konfirmasi Password</label>
        <div style="position: relative; width: 100%;">
            <input type="password" id="password_confirmation" name="password_confirmation" style="width: 100%; padding-right: 35px;">
            <span class="toggle-password" onclick="togglePasswordConfirm()">
                <i id="password-confirm-icon" data-feather="eye"></i>
            </span>
        </div>


        <div class="submit__btn">
            <button type="submit" class="btn primary">Update</button>
            <button type="button" onclick="window.location='{{ route('admin.user.index') }}'" class="btn secondary">Batal</button>
        </div>
    </form>
</main>

@if(session('success'))
<div id="popup" class="popup">
    <div class="popup-content">
        <h2>{{ session('success') }}</h2>
        <div style="margin-top: 20px; display: flex; gap: 10px; justify-content: center;">
            <a href="{{ route('admin.user.index') }}" class="btn">Selesai</a>
        </div>
    </div>
</div>


<script>
    function closePopup() {
        document.getElementById('popup').style.display = 'none';
    }
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

<style>
        .toggle-password {
    position: absolute;
    right: 10px;
    top: 0;
    bottom: 0;
    height: 100%;
    display: flex;
    align-items: center;   /* ratakan vertical */
    justify-content: center;
    cursor: pointer;
    color: #1f1f1f;
}


    .toggle-password i {
        width: 20px;   /* kecilkan icon */
        height: 20px;
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
