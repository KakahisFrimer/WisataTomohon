@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="{{ asset('css/style.css') }}">

<div class="register-page">
    <div class="reg-box">
        <h2 class="auth-title">Forgot Password</h2>

        @if (session('status'))
            <div class="alert alert-success">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}">
            @csrf

            <div class="input-group">
                <label for="email_or_phone">Email/Phone number</label>
                <input type="text" id="email_or_phone" name="email_or_phone" 
                       value="{{ old('email_or_phone') }}" required>
                @error('email_or_phone')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <button type="submit" class="auth-button">Continue</button>
        </form>
    </div>
</div>
@endsection