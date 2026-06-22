@extends('layouts.app')

@section('content')
<div class="container-fluid d-flex align-items-center justify-content-center" style="background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%); min-height: 100vh;">
    <div class="row justify-content-center text-center w-100 px-3">
        <div class="col-md-8 col-lg-6 text-white">
            
            <div class="mb-4">
                <div class="bg-white text-primary rounded-circle d-inline-flex align-items-center justify-content-center shadow-lg" style="width: 80px; height: 80px;">
                    <i class="fas fa-campground fa-3x"></i>
                </div>
            </div>

            <h1 class="display-4 fw-bold mb-2" style="letter-spacing: -1px;">Outdoor Rental System</h1>
            <p class="lead text-white-50 mb-5 fs-5">
                Infrastruktur Ekosistem Sistem Informasi Manajemen & Data Warehousing Inventaris Alat Outdoor Terintegrasi 15 Cabang Regional.
            </p>

            <div class="d-flex justify-content-center gap-3">
                @if(Auth::check())
                    <a href="{{ route('dashboard') }}" class="btn btn-light btn-lg px-4 py-3 fw-bold text-primary shadow-sm rounded-3">
                        <i class="fas fa-chart-pie me-2"></i> Masuk ke Dashboard
                    </a>
                @else
                    <a href="{{ url('/login') }}" class="btn btn-light btn-lg px-5 py-3 fw-bold text-primary shadow-lg rounded-3 transition-all">
                        <i class="fas fa-sign-in-alt me-2"></i> Login Staf / Admin
                    </a>
                @endif
            </div>

            <div class="row mt-5 pt-5 border-top border-white border-opacity-10 justify-content-center g-4">
                <div class="col-6 col-md-4">
                    <div class="p-2">
                        <i class="fas fa-warehouse fa-lg text-warning mb-2 d-block"></i>
                        <span class="small fw-bold d-block text-white">Multi-Branch Sync</span>
                        <small class="text-white-50 fs-7">Konsolidasi 15 Wilayah</small>
                    </div>
                </div>
                <div class="col-6 col-md-4">
                    <div class="p-2">
                        <i class="fas fa-shield-alt fa-lg text-info mb-2 d-block"></i>
                        <span class="small fw-bold d-block text-white">RBAC Security</span>
                        <small class="text-white-50 fs-7">Superadmin & Admin</small>
                    </div>
                </div>
                <div class="col-6 col-md-4">
                    <div class="p-2">
                        <i class="fas fa-database fa-lg text-success mb-2 d-block"></i>
                        <span class="small fw-bold d-block text-white">DW Ready</span>
                        <small class="text-white-50 fs-7">Star Schema Compliant</small>
                    </div>
                </div>
            </div>

            <p class="text-white-50 small mt-5 pt-3 fs-7 opacity-50">
                &copy; {{ date('Y') }} Sistem Informasi Ekosistem Multi-Cabang. All Rights Reserved.
            </p>

        </div>
    </div>
</div>

<style>
    .transition-all:hover {
        transform: translateY(-3px);
        box-shadow: 0 .5rem 1.5rem rgba(0,0,0,.15)!important;
    }
</style>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
@endsection