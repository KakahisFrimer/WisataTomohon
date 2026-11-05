@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="{{ asset('css/style.css') }}">
<script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>

<style>
   
    .input-group select {
        width: 100%;
        padding: 12px;
        border: none;
        border-bottom: 2px solid #fdfdfd;
        font-size: 16px;
        background-color: transparent;
        transition: border-color 0.3s;
        margin-top: 5px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24' fill='none' stroke='%23fdfdfd' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 12px center;
        background-size: 16px;
    }
    
    .input-group select:focus {
        border-color: #710eff;
        outline: none;
        box-shadow: 0 0 0 3px rgba(113, 14, 255, 0.15);
    }
    
    .input-group select.input-error {
        border-color: #ff3860;
    }
    
    .input-group select option {
        padding: 10px;
        font-size: 15px;
        background-color: #2d2d2d;
        color: #fff;
    }
    
    /* Media Queries */
    @media (max-width: 768px) {
        .input-group select {
            padding: 10px 36px 10px 10px;
            font-size: 15px;
            background-size: 14px;
            background-position: right 10px center;
        }
    }
    
    @media (max-width: 480px) {
        .input-group select {
            padding: 8px 32px 8px 8px;
            font-size: 14px;
            background-size: 12px;
            background-position: right 8px center;
        }
    }
    
    .error-message {
        color: #ff3860;
        font-size: 14px;
        margin-top: 5px;
        display: block;
    }
</style>

<div class="register-page">
    <div class="reg-box">
        <h2 class="auth-title">CREATE AN ACCOUNT</h2>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('register') }}" id="registerForm">
            @csrf

            <div class="input-group">
                <label for="name">Full name</label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" 
                       class="@error('name') input-error @enderror" required
                       oninput="validateName(this)">
                <span class="error-message" id="name-error"></span>
            </div>

            <div class="input-group">
                <label for="email_or_phone">Email/Phone number</label>
                <input type="text" id="email_or_phone" name="email_or_phone" 
                       value="{{ old('email_or_phone') }}" 
                       class="@error('email_or_phone') input-error @enderror" required
                       oninput="validateEmailOrPhone(this)">
                <span class="error-message" id="email_or_phone-error"></span>
            </div>

            
            <div class="input-group">
                <label for="security_question_id">Security Question</label>
                <select id="security_question_id" name="security_question_id" 
                        class="@error('security_question_id') input-error @enderror" required>
                    <option value="">Select a security question</option>
                    @foreach(config('security_questions.questions') as $id => $question)
                        <option value="{{ $id }}" {{ old('security_question_id') == $id ? 'selected' : '' }}>
                            {{ $question }}
                        </option>
                    @endforeach
                </select>
                @error('security_question_id')
                    <span class="error-message">{{ $message }}</span>
                @enderror
                <span class="error-message" id="security_question_id-error"></span>
            </div>

            <div class="input-group">
                <label for="security_answer">Your Answer</label>
                <input type="text" id="security_answer" name="security_answer" 
                    value="{{ old('security_answer') }}" 
                    class="@error('security_answer') input-error @enderror" required
                    oninput="validateSecurityAnswer(this)">
                @error('security_answer')
                    <span class="error-message">{{ $message }}</span>
                @enderror
                <span class="error-message" id="security_answer-error"></span>
            </div>

            <div class="input-group">
                <label for="password">Password</label>
                <div style="position: relative;">
                    <input type="password" id="password" name="password" 
                        class="@error('password') input-error @enderror" required
                        oninput="checkPasswordLength(); checkPasswordMatch()">
                    <span class="toggle-password" onclick="togglePassword('password')" 
                        style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); cursor: pointer;">
                        <i id="password-icon" data-feather="eye"></i>
                    </span>
                </div>
                <span class="error-message" id="password-length-error"></span>
                @error('password')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <div class="input-group">
                <label for="password_confirmation">Confirm password</label>
                <div style="position: relative;">
                    <input type="password" id="password_confirmation" name="password_confirmation" 
                        class="@error('password_confirmation') input-error @enderror" required
                        oninput="checkPasswordMatch()">
                    <span class="toggle-password" onclick="togglePassword('password_confirmation')" 
                        style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); cursor: pointer;">
                        <i id="password_confirmation-icon" data-feather="eye"></i>
                    </span>
                </div>
                <span class="error-message" id="password-match-error"></span>
            </div>

            <button type="submit" class="auth-button">Register</button>

            <div class="reg-footer">
                Already have an account?<a href="{{ route('login') }}">Login here</a>
            </div>
        </form>

    </div>
</div>

<div id="successModal" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Registration Success!</h3>
        </div>
        <div class="modal-body">
            <p>Your account has been successfully created. Please log in to continue.</p>
        </div>
        <div class="modal-footer">
            <button onclick="redirectToLogin()" class="modal-button">Login Now</button>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        feather.replace();
    
        @if(session('success'))
            document.getElementById('successModal').style.display = 'flex';
        @endif
    });

    // Validasi nama
    function validateName(input) {
        const name = input.value;
        const errorElement = document.getElementById('name-error');
        const regex = /^[a-zA-Z\s]*$/; 
        
        if (!regex.test(name)) {
            errorElement.textContent = 'Name should not contain numbers or special characters';
            input.classList.add('input-error');
        } else {
            errorElement.textContent = '';
            input.classList.remove('input-error');
        }
    }

    // Validasi security answer
    function validateSecurityAnswer(input) {
        const answer = input.value;
        const errorElement = document.getElementById('security_answer-error');
        
        if (answer.length < 3) {
            errorElement.textContent = 'Answer must be at least 3 characters';
            input.classList.add('input-error');
        } else {
            errorElement.textContent = '';
            input.classList.remove('input-error');
        }
    }

    // Validasi email/telepon
    function validateEmailOrPhone(input) {
        const value = input.value;
        const errorElement = document.getElementById('email_or_phone-error');
        
        if (value.includes('@')) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            const validDomains = ['gmail.com', 'yahoo.com', 'hotmail.com', 'outlook.com', 'icloud.com'];
            const domain = value.split('@')[1];
            
            if (!emailRegex.test(value)) {
                errorElement.textContent = 'Invalid email format';
                input.classList.add('input-error');
            } else if (!validDomains.some(d => domain.endsWith(d))) {
                errorElement.textContent = 'Email domain not supported';
                input.classList.add('input-error');
            } else {
                errorElement.textContent = '';
                input.classList.remove('input-error');
            }
        } 

        else {
            const phoneRegex = /^[0-9]{10,13}$/;
            
            if (!phoneRegex.test(value)) {
                errorElement.textContent = 'Phone number must be 10-13 digits';
                input.classList.add('input-error');
            } else {
                errorElement.textContent = '';
                input.classList.remove('input-error');
            }
        }
    }

    const passwordInput = document.getElementById('password');

    passwordInput.addEventListener('input', function() {
        const password = this.value;
        const errorElement = document.getElementById('password-length-error');
        const minLength = 6;

        errorElement.textContent = '';
        this.classList.remove('input-error');

        if (password.length > 0 && password.length < minLength) {
            errorElement.textContent = 'Password must be at least 6 characters';
            this.classList.add('input-error');
        }
    });
    
    function checkPasswordMatch() {
        const password = document.getElementById('password').value;
        const confirmPassword = document.getElementById('password_confirmation').value;
        const matchErrorElement = document.getElementById('password-match-error');
        const lengthErrorElement = document.getElementById('password-length-error');
        
        if (password.length >= 6) {
            lengthErrorElement.textContent = '';
            document.getElementById('password').classList.remove('input-error');
        }
        
        if (confirmPassword.length === 0) {
            matchErrorElement.textContent = '';
            document.getElementById('password_confirmation').classList.remove('input-error');
            return;
        }
        
        if (password !== confirmPassword) {
            matchErrorElement.textContent = 'Password and confirmation do not match';
            document.getElementById('password_confirmation').classList.add('input-error');
        } else {
            matchErrorElement.textContent = '';
            document.getElementById('password_confirmation').classList.remove('input-error');
        }
    }

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

    function redirectToLogin() {
        window.location.href = "{{ route('login') }}";
    }

    document.getElementById('registerForm').addEventListener('submit', function(e) {
        let isValid = true;
        const name = document.getElementById('name').value;
        const emailOrPhone = document.getElementById('email_or_phone').value;
        const password = document.getElementById('password').value;
        const confirmPassword = document.getElementById('password_confirmation').value;
        const securityQuestion = document.getElementById('security_question_id').value;
        const securityAnswer = document.getElementById('security_answer').value;
        
        // Reset semua pesan error
        document.querySelectorAll('.error-message').forEach(el => {
            if (el.id !== 'name-error' && el.id !== 'email_or_phone-error') {
                el.textContent = '';
            }
        });
        document.querySelectorAll('.input-error').forEach(el => {
            el.classList.remove('input-error');
        });

        // Validasi nama
        const nameRegex = /^[a-zA-Z\s]*$/;
        if (!nameRegex.test(name)) {
            document.getElementById('name-error').textContent = 'Invalid name format';
            document.getElementById('name').classList.add('input-error');
            isValid = false;
        }
        
        // Validasi email/telepon
        if (emailOrPhone.includes('@')) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(emailOrPhone)) {
                document.getElementById('email_or_phone-error').textContent = 'Invalid email format';
                document.getElementById('email_or_phone').classList.add('input-error');
                isValid = false;
            }
        } else {
            const phoneRegex = /^[0-9]{10,13}$/;
            if (!phoneRegex.test(emailOrPhone)) {
                document.getElementById('email_or_phone-error').textContent = 'Invalid phone number';
                document.getElementById('email_or_phone').classList.add('input-error');
                isValid = false;
            }
        }
        
        // Validasi password match
        if (password !== confirmPassword) {
            document.getElementById('password-match-error').textContent = 'Passwords do not match';
            document.getElementById('password_confirmation').classList.add('input-error');
            isValid = false;
        }
        
        // Validasi security question
        if (!securityQuestion) {
            document.getElementById('security_question_id').classList.add('input-error');
            document.getElementById('security_question_id-error').textContent = 'Please select a security question';
            isValid = false;
        }
        
        // Validasi security answer
        if (!securityAnswer) {
            document.getElementById('security_answer').classList.add('input-error');
            document.getElementById('security_answer-error').textContent = 'Please enter your answer';
            isValid = false;
        } else if (securityAnswer.length < 3) {
            document.getElementById('security_answer').classList.add('input-error');
            document.getElementById('security_answer-error').textContent = 'Answer must be at least 3 characters';
            isValid = false;
        }
        
        if (!isValid) {
            e.preventDefault();
        }
    });
</script>

@endsection