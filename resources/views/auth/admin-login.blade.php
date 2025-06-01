@extends('layouts.auth')

@section('content')
<img src="{{ asset('assets/img/LOGO.png') }}" alt="Baliwag Institute of Technology" class="auth-logo">
<h1 class="auth-title">Admin Portal</h1>
<p class="auth-subtitle">Sign in to access admin dashboard</p>

<ul class="nav auth-form nav-tabs">
    <li class="nav-item">
        <a class="nav-link active" href="#">Admin Login</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{ route('applicant.login') }}">Applicant Login</a>
    </li>
</ul>

<form method="POST" action="{{ route('admin.login.submit') }}" class="auth-form">
    @csrf

    <div class="mb-3">
        <label for="email" class="form-label">Email Address</label>
        <div class="input-icon">
            <i class="fas fa-envelope icon"></i>
            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus placeholder="admin@baliwag.unit.site">

            @error('email')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
        </div>
    </div>

    <div class="mb-3">
        <label for="password" class="form-label">Password</label>
        <div class="input-icon">
            <i class="fas fa-lock icon"></i>
            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password" placeholder="**********">

            @error('password')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="form-check">
            <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
            <label class="form-check-label" for="remember">
                {{ __('Remember me') }}
            </label>
        </div>
        <div class="forgot-password">
            <a href="#">Forgot Password?</a>
        </div>
    </div>

    <button type="submit" class="btn-signin">
        Sign in ->
    </button>
</form>
@endsection