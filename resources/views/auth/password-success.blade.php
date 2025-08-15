@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="{{ asset('css/style.css') }}">

<div class="register-page">
    <div class="reg-box">
        <div class="success-icon">
            <i data-feather="check-circle"></i>
        </div>
        <h2 class="auth-title">Password Successfully Changed!</h2>
        
        <div class="success-message">
            <p>Your password has been successfully changed. Please login with your new password.</p>
        </div>
        
        <button onclick="redirectToLogin()" class="auth-button">
            Login Now
        </button>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        feather.replace();
    });

    function redirectToLogin() {
        window.location.href = "{{ route('login') }}";
    }
</script>
@endsection