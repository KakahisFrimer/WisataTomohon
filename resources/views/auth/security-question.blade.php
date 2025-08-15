@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="{{ asset('css/style.css') }}">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="register-page">
    <div class="reg-box">
        <h2 class="auth-title">Security Question</h2>

        @if (session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        <form method="POST" action="{{ route('verify.answer') }}" id="securityQuestionForm">
            @csrf

            <div class="input-group" style=" text-align: center;"   >
                <label >Questions you selected during registration:</label>
            </div>

            <div class="input-group">
                <label for="security_question_id">Select Security Question</label>
                <select id="security_question_id" name="security_question_id" required>
                    <option value="">Select Security Question</option>
                    @foreach($questions as $id => $question)
                        <option value="{{ $id }}">{{ $question }}</option>
                    @endforeach
                </select>
            </div>

            <div class="input-group">
                <label for="answer">Your Answer</label>
                <input type="text" id="answer" name="answer" required>
            </div>

            <button type="submit" class="auth-button">Verify Answer</button>
        </form>
    </div>
</div>

@if (session('error'))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        Swal.fire({
            icon: 'error',
            title: 'Wrong Answer',
            text: '{{ session('error') }}',
        });
    });
</script>
@endif

<script>
    document.addEventListener('DOMContentLoaded', function() {
        feather.replace();
    });
</script>
@endsection