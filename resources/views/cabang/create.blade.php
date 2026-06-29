@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 fw-bold">Tambah Cabang</h1>
            <p class="text-muted">Isi data cabang baru.</p>
        </div>
        <a href="{{ route('cabang.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <form method="POST" action="{{ route('cabang.store') }}">
                @csrf

                <div class="mb-3">
                    <label for="kode_cabang" class="form-label fw-bold">Kode Cabang <span class="text-danger">*</span></label>
                    <input type="text" name="kode_cabang" id="kode_cabang" class="form-control @error('kode_cabang') is-invalid @enderror" value="{{ old('kode_cabang') }}" placeholder="Contoh: CBG_PLU" required>
                    @error('kode_cabang')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="nama_kota" class="form-label fw-bold">Nama Kota <span class="text-danger">*</span></label>
                    <input type="text" name="nama_kota" id="nama_kota" class="form-control @error('nama_kota') is-invalid @enderror" value="{{ old('nama_kota') }}" placeholder="Contoh: Palu" required>
                    @error('nama_kota')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mt-3">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Simpan Cabang
                    </button>
                    <a href="{{ route('cabang.index') }}" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection