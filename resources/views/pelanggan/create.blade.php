@extends('layouts.app')

@section('content')
<div class="container-fluid py-4" style="background-color: #f8f9fa; min-height: 100vh;">
    
    <div class="mb-4">
        <a href="{{ route('pelanggan.index') }}" class="btn btn-light btn-sm text-secondary border shadow-sm mb-2">
            <i class="fas fa-arrow-left me-1"></i> Kembali ke Direktori
        </a>
        <h1 class="h3 mb-1 text-dark fw-bold">Registrasi Member Pelanggan</h1>
        <p class="text-muted mb-0">Daftarkan identitas kartu fisik penyewa baru ke server data terpusat.</p>
    </div>

    <div class="row">
        <div class="col-lg-8 mb-4">
            <div class="card border-0 shadow-sm bg-white p-4">
                <form action="{{ route('pelanggan.store') }}" method="POST">
                    @csrf
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="nik" class="form-label text-secondary fw-bold small">No. KTP / NIK Identitas</label>
                            <input type="number" name="nik" id="nik" class="form-control @error('nik') is-invalid @enderror" placeholder="Masukkan 16 digit NIK" required>
                            @error('nik')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="nama" class="form-label text-secondary fw-bold small">Nama Lengkap Sesuai KTP</label>
                            <input type="text" name="nama" id="nama" class="form-control @error('nama') is-invalid @enderror" placeholder="Nama lengkap pelanggan" required>
                            @error('nama')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-12">
                            <label for="no_hp" class="form-label text-secondary fw-bold small">No. HP / WhatsApp Aktif</label>
                            <input type="text" name="no_hp" id="no_hp" class="form-control @error('no_hp') is-invalid @enderror" placeholder="Contoh: 082345xxxxxx" required>
                            <small class="text-muted">Gunakan nomor yang aktif untuk mempermudah penagihan denda keterlambatan via WhatsApp.</small>
                            @error('no_hp')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-12">
                            <label for="alamat" class="form-label text-secondary fw-bold small">Alamat Domisili Sekarang</label>
                            <textarea name="alamat" id="alamat" rows="3" class="form-control @error('alamat') is-invalid @enderror" placeholder="Alamat lengkap tempat tinggal sekarang..." required></textarea>
                            @error('alamat')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <hr class="my-4 opacity-10">

                    <div class="d-flex justify-content-end gap-2">
                        <button type="reset" class="btn btn-light border px-4">Reset</button>
                        <button type="submit" class="btn btn-success px-4 shadow-sm">
                            <i class="fas fa-user-check me-1"></i> Aktivasi Akun Member
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection