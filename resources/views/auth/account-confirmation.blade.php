@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="{{ asset('css/style.css') }}">

<div class="account-confirmation-page">
    <div class="confirmation-box">
        <div class="confirmation-icon">
            <i data-feather="user-check"></i>
        </div>
        
        <h2 class="confirmation-title">Account Confirmation</h2>
        <p class="confirmation-subtitle">Please confirm that this is your account before proceeding</p>
        
        <div class="account-card">
            <div class="account-card-header">
                <div class="account-card-icon">
                    <i data-feather="user"></i>
                </div>
                <h3 class="account-card-title">Account Information</h3>
            </div>
            
            <div class="account-details">
                <div class="account-detail">
                    <div class="detail-icon">
                        <i data-feather="user"></i>
                    </div>
                    <div class="detail-content">
                        <div class="detail-label">Full Name</div>
                        <div class="detail-value">{{ $user->name }}</div>
                    </div>
                </div>
                
                <div class="account-detail">
                    <div class="detail-icon">
                        <i data-feather="mail"></i>
                    </div>
                    <div class="detail-content">
                        <div class="detail-label">Email/Phone</div>
                        <div class="detail-value">{{ $user->email_or_phone }}</div>
                    </div>
                </div>
                
                <div class="account-detail">
                    <div class="detail-icon">
                        <i data-feather="clock"></i>
                    </div>
                    <div class="detail-content">
                        <div class="detail-label">Registered Since</div>
                        <div class="detail-value">{{ $user->created_at->format('M d, Y') }}</div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="confirmation-buttons">
            <a href="{{ route('password.request') }}" class="confirmation-button secondary">
                <i data-feather="arrow-left"></i> No, Go Back
            </a>
            <a href="{{ route('security.question') }}" class="confirmation-button primary">
                Yes, Continue <i data-feather="arrow-right"></i>
            </a>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        feather.replace();
    });
</script>
@endsection