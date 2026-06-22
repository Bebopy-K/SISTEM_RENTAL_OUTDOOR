@extends('layouts.app')

@section('content')
<div class="container-fluid d-flex align-items-center justify-content-center" style="background-color: #f8f9fa; min-height: 100vh;">
    <div class="row justify-content-center w-100">
        <div class="col-md-5 col-lg-4">
            <div class="card border-0 shadow-sm bg-white p-4 rounded-3">
                <div class="text-center mb-4">
                    <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-2" style="width: 60px; height: 60px;">
                        <i class="fas fa-campground fa-2x"></i>
                    </div>
                    <h4 class="fw-bold text-dark mb-1">Outdoor Rental System</h4>
                    <p class="text-muted small">Silakan masuk untuk mengelola operasional cabang</p>
                </div>

                @if($errors->any())
                <div class="alert alert-danger border-0 shadow-sm d-flex align-items-center mb-3 py-2" role="alert">
                    <i class="fas fa-exclamation-circle me-2 small"></i>
                    <div class="small fw-bold">{{ $errors->first() }}</div>
                </div>
                @endif

                {{-- FORM LOGIN REGULER --}}
                <form method="POST" action="{{ route('login.proses') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label text-secondary fw-bold small">Username</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted border-end-0">
                                <i class="fas fa-user small"></i>
                            </span>
                            <input type="text" name="username" class="form-control border-start-0 ps-0" placeholder="Masukkan username Anda" required autofocus value="{{ old('username') }}">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label text-secondary fw-bold small">Password</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted border-end-0">
                                <i class="fas fa-lock small"></i>
                            </span>
                            <input type="password" name="password" class="form-control border-start-0 ps-0" placeholder="••••••••" required>
                        </div>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary py-2 shadow-sm fw-bold">
                            <i class="fas fa-sign-in-alt me-1"></i> Masuk ke Sistem
                        </button>
                    </div>
                </form>

                {{-- PEMISAH / ATAU --}}
                <div class="text-center mt-3">
                    <div class="position-relative my-2">
                        <hr class="border-muted">
                        <span class="position-absolute top-50 start-50 translate-middle bg-white px-2 text-muted small">atau</span>
                    </div>

                    {{-- TOMBOL LOGIN DENGAN GOOGLE --}}
                    <a href="{{ route('login.google') }}" class="btn btn-outline-danger w-100 py-2 shadow-sm fw-bold">
                        <i class="fab fa-google me-1"></i> Login dengan Google
                    </a>
                </div>

                {{-- LINK LUPA PASSWORD --}}
                <div class="text-center mt-3">
                    <a href="{{ route('password.request') }}" class="small text-muted text-decoration-none">Lupa Password?</a>
                </div>
            </div>

            <div class="text-center mt-3">
                <p class="text-muted small">Sistem Informasi Ekosistem Multi-Cabang v2.0</p>
            </div>
        </div>
    </div>
</div>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
@endsection