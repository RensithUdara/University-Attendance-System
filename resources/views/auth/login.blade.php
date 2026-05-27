@extends('layouts.app')

@section('title', 'Login')

@section('content')
<div class="auth-form-container">
    <form method="POST" action="{{ route('login') }}" class="auth-form" id="loginForm">
        @csrf

        <div class="form-group floating-input">
            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" 
                   name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
            <label for="email" class="form-label">{{ __('Email Address') }}</label>
            <div class="input-icon">
                <i class="fas fa-envelope"></i>
            </div>
            @error('email')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        <div class="form-group floating-input">
            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" 
                   name="password" required autocomplete="current-password">
            <label for="password" class="form-label">{{ __('Password') }}</label>
            <div class="input-icon">
                <i class="fas fa-lock"></i>
            </div>
            <div class="password-toggle">
                <i class="fas fa-eye" id="togglePassword"></i>
            </div>
            @error('password')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        <div class="form-group d-flex justify-content-between align-items-center">
            <div class="form-check remember-me">
                <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                <label class="form-check-label" for="remember">
                    {{ __('Remember Me') }}
                </label>
            </div>
            
            @if (Route::has('password.request'))
                <a class="forgot-password" href="{{ route('password.request') }}">
                    {{ __('Forgot Password?') }}
                </a>
            @endif
        </div>

        <div class="form-group">
            <button type="submit" class="btn btn-primary auth-btn w-100" id="loginButton">
                <span class="btn-text">{{ __('Login') }}</span>
                <div class="btn-loader">
                    <i class="fas fa-spinner fa-spin"></i>
                </div>
            </button>
        </div>
    </form>
</div>
@endsection