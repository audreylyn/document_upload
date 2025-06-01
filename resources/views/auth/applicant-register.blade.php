@extends('layouts.auth')

@section('content')
<img src="{{ asset('assets/img/LOGO.png') }}" alt="Baliwag Institute of Technology" class="auth-logo">
<h1 class="auth-title">Create Account</h1>
<p class="auth-subtitle">Sign up to get started</p>

<div class="container-register">
    <ul class="nav nav-tabs nav-justified">
        <li class="nav-item">
            <a class="nav-link" href="{{ route('applicant.login') }}">Login</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('admin.login') }}">Admin Login</a>
        </li>
    </ul>

    <form method="POST" action="{{ route('applicant.register.submit') }}" class="auth-form">
        @csrf

        <div class="row form-row">
            <div class="col-md-6 mb-3">
                <label for="name" class="form-label">Full Name</label>
                <div class="input-icon">
                    <i class="fas fa-user icon"></i>
                    <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus placeholder="John Doe">

                    @error('name')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>
            </div>

            <div class="col-md-6 mb-3">
                <label for="email" class="form-label">Email Address</label>
                <div class="input-icon">
                    <i class="fas fa-envelope icon"></i>
                    <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" placeholder="johndoe@baliwag.unit.site">

                    @error('email')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>
            </div>
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <div class="input-icon">
                <i class="fas fa-lock icon"></i>
                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password" placeholder="**********">

                @error('password')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>
        </div>

        <div class="mb-3">
            <label for="password-confirm" class="form-label">Confirm Password</label>
            <div class="input-icon">
                <i class="fas fa-lock icon"></i>
                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password" placeholder="**********">
            </div>
        </div>

        <button type="submit" class="btn-signin">
            Register ->
        </button>

        <div class="signup-link">
            Already have an account? <a href="{{ route('applicant.login') }}">Sign in</a>
        </div>
    </form>
    @endsection