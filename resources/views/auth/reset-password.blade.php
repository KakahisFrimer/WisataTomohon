@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="{{ asset('css/style.css') }}">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="register-page">
    <div class="reg-box">
        <h2 class="auth-title">Reset Password</h2>

        <form method="POST" action="{{ route('password.update') }}" id="resetPasswordForm">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">

            <div class="input-group">
                <label for="password">New Password</label>
                <div style="position: relative;">
                    <input type="password" id="password" name="password" required
                           oninput="checkPasswordLength()">
                    <span class="toggle-password" onclick="togglePassword('password')" 
                          style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); cursor: pointer;">
                        <i id="password-icon" data-feather="eye"></i>
                    </span>
                </div>
                <span class="error-message" id="password-length-error"></span>
            </div>

            <div class="input-group">
                <label for="password-confirm">Confirm New Password</label>
                <div style="position: relative;">
                    <input type="password" id="password-confirm" name="password_confirmation" required
                           oninput="checkPasswordMatch()">
                    <span class="toggle-password" onclick="togglePassword('password-confirm')" 
                          style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); cursor: pointer;">
                        <i id="password-confirm-icon" data-feather="eye"></i>
                    </span>
                </div>
                <span class="error-message" id="password-match-error"></span>
            </div>

            <button type="submit" class="auth-button">Reset Password</button>
        </form>
    </div>
</div>

<div id="successModal" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Password changed successfully!</h3>
        </div>
        <div class="modal-body">
            <p>Your password has been successfully changed. Please login with your new password.</p>
        </div>
        <div class="modal-footer">
            <button onclick="redirectToLogin()" class="modal-button">Login Now</button>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        feather.replace();
        
        @if (session('success'))
            document.getElementById('successModal').style.display = 'flex';
        @endif
    });

    function togglePassword(fieldId) {
        const passwordInput = document.getElementById(fieldId);
        const passwordIcon = document.getElementById(`${fieldId}-icon`);
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        
        passwordInput.setAttribute('type', type);
        
        if (type === 'text') {
            passwordIcon.setAttribute('data-feather', 'eye-off');
        } else {
            passwordIcon.setAttribute('data-feather', 'eye');
        }
        
        feather.replace();
    }

    function checkPasswordLength() {
        const password = document.getElementById('password').value;
        const errorElement = document.getElementById('password-length-error');
        const minLength = 6;

        if (password.length > 0 && password.length < minLength) {
            errorElement.textContent = 'Password must be at least 6 characters';
            errorElement.style.display = 'block';
        } else {
            errorElement.textContent = '';
            errorElement.style.display = 'none';
        }
    }

    function checkPasswordMatch() {
        const password = document.getElementById('password').value;
        const confirmPassword = document.getElementById('password-confirm').value;
        const errorElement = document.getElementById('password-match-error');

        if (confirmPassword.length === 0) {
            errorElement.textContent = '';
            errorElement.style.display = 'none';
            return;
        }

        if (password !== confirmPassword) {
            errorElement.textContent = 'Passwords do not match';
            errorElement.style.display = 'block';
        } else {
            errorElement.textContent = '';
            errorElement.style.display = 'none';
        }
    }

    function redirectToLogin() {
        window.location.href = "{{ route('login') }}";
    }


    document.getElementById('resetPasswordForm').addEventListener('submit', function(e) {
        const password = document.getElementById('password').value;
        const confirmPassword = document.getElementById('password-confirm').value;
        

        if (password.length < 6) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Password Too Short',
                text: 'Password must be at least 6 characters long',
            });
            return;
        }
        

        if (password !== confirmPassword) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Passwords Do Not Match',
                text: 'Please make sure both passwords match',
            });
            return;
        }
    });
</script>

@if (session('error'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: '{{ session('error') }}',
            });
        });
    </script>
@endif

@if ($errors->any())
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Cek jika ada error terkait password (misal: password baru sama dengan yang lama)
            @if ($errors->has('password'))
                Swal.fire({
                    icon: 'error',
                    title: 'Invalid Password',
                    text: '{{ $errors->first('password') }}',
                });
            @endif
        });
    </script>
@endif

@endsection