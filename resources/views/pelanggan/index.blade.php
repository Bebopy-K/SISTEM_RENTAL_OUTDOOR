@extends('layouts.app')

@section('content')
<div class="container-fluid py-4" style="background-color: #f8f9fa; min-height: 100vh;">

    <div class="row align-items-center mb-4">
        <div class="col-md-6 mb-3 mb-md-0">
            <h1 class="h3 mb-1 text-dark fw-bold">Database Pelanggan</h1>
            <p class="text-muted mb-0">Direktori data identitas penyewa alat outdoor terpusat nasional.</p>
        </div>
        <div class="col-md-6 d-flex justify-content-md-end align-items-center gap-2">
            <form action="{{ route('pelanggan.index') }}" method="GET" class="d-flex">
                <input type="text" name="search" class="form-control form-control-sm me-2 border-grey" placeholder="Cari nama / nomor HP..." value="{{ $search }}">
                <button type="submit" class="btn btn-light btn-sm border shadow-sm px-3">Cari</button>
            </form>
            <a href="{{ route('pelanggan.create') }}" class="btn btn-primary shadow-sm px-3 py-2 text-nowrap">
                <i class="fas fa-user-plus me-1"></i> Registrasi Member
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success border-0 shadow-sm d-flex align-items-center" role="alert">
        <i class="fas fa-check-circle me-2 fs-5"></i>
        <div>{{ session('success') }}</div>
    </div>
    @endif

    <div class="card border-0 shadow-sm bg-white overflow-hidden">
        <div class="card-header bg-white py-3 border-bottom-0">
            <h6 class="m-0 fw-bold text-secondary">
                <i class="fas fa-users text-muted me-2"></i>Daftar Identitas Penyewa Terverifikasi
            </h6>
        </div>
        
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light text-secondary text-uppercase fs-7" style="letter-spacing: 0.5px;">
                    <tr>
                        <th class="ps-4 py-3" width="5%">No</th>
                        <th width="20%">No. Identitas (NIK)</th>
                        <th width="25%">Nama Lengkap</th>
                        <th width="15%">Kontak / No. HP</th>
                        <th width="35%">Alamat Domisili</th>
                    </tr>
                </thead>
                <tbody class="border-top-0">
                    @forelse($dataPelanggan as $index => $pelanggan)
                    <tr>
                        <td class="ps-4 text-muted fw-bold">{{ $dataPelanggan->firstItem() + $index }}</td>
                        <td>
                            <span class="badge bg-light text-dark border font-mono px-2 py-1.5 fs-7">
                                <i class="far fa-id-card text-muted me-1"></i> {{ $pelanggan->nik }}
                            </span>
                        </td>
                        <td>
                            <div class="fw-bold text-dark">{{ $pelanggan->nama }}</div>
                            <small class="text-muted fs-7">Status: Member Aktif</small>
                        </td>
                        <td>
                            <a href="https://wa.me/{{ $pelanggan->no_hp }}" target="_blank" class="text-decoration-none text-success fw-bold small">
                                <i class="fab fa-whatsapp me-1"></i> {{ $pelanggan->no_hp }}
                            </a>
                        </td>
                        <td class="text-secondary text-truncate" style="max-width: 250px;">
                            {{ $pelanggan->alamat }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted">
                            <i class="fas fa-users-slash fa-3x mb-3 text-light"></i>
                            <p class="mb-0">Tidak ditemukan data pelanggan yang cocok.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($dataPelanggan->hasPages())
        <div class="card-footer bg-white py-3 border-top d-flex justify-content-between align-items-center">
            <div class="small text-muted">
                Menampilkan {{ $dataPelanggan->firstItem() }} - {{ $dataPelanggan->lastItem() }} dari {{ $dataPelanggan->total() }} pelanggan.
            </div>
            <div>
                {{ $dataPelanggan->appends(request()->query())->links('pagination::bootstrap-5') }}
            </div>
        </div>
        @endif
    </div>
</div>
@endsection