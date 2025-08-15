@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="{{ asset('css/style.css') }}">
<div class="login-page">
    <div class="login-box">
        <h2 class="text-center mb-4"><strong>LOGIN</strong></h2>

        {{-- @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif --}}

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <label>Email / Phone number</label>
            <input type="text" name="email_or_phone" value="{{ old('email_or_phone') }}" 
                   class="@error('email_or_phone') input-error @enderror" required>
            @error('email_or_phone')
                <span class="error-message">{{ $message }}</span>
            @enderror

            <label>Password</label>
<div style="position: relative;">
    <input type="password" id="password" name="password" 
           class="@error('password') input-error @enderror" required>
    <span class="toggle-password" onclick="togglePassword()" 
          style="position: absolute; right: 10px; top: 42%; transform: translateY(-50%); cursor: pointer; color :#1f1f1f;">
        <i id="password-icon" data-feather="eye"></i>
    </span>
</div>
            @error('password')
                <span class="error-message">{{ $message }}</span>
            @enderror

            <div class="remember-forgot">
                <div class="remember-wrapper">
                    <input type="checkbox" name="remember" id="remember">
                    <label for="remember">Remember me</label>
                </div>
                <a href="{{ route('password.request') }}" class="forgot-password">Forgot Password?</a>
            </div>


            <button type="submit">Login</button>

            <div class="login-footer">
                <p class="text-center mt-3">Don't have an account? <a href="{{ route('register') }}">Register</a></p>
            </div>
        </form>
    </div>
</div>

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
</script>
@endsection