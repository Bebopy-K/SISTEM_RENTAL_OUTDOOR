@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 fw-bold">Edit Cabang: {{ $cabang->nama_kota }}</h1>
            <p class="text-muted">Ubah data cabang.</p>
        </div>
        <a href="{{ route('cabang.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <form method="POST" action="{{ route('cabang.update', $cabang->id_cabang) }}">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="kode_cabang" class="form-label fw-bold">Kode Cabang <span class="text-danger">*</span></label>
                    <input type="text" name="kode_cabang" id="kode_cabang" class="form-control @error('kode_cabang') is-invalid @enderror" value="{{ old('kode_cabang', $cabang->kode_cabang) }}" required>
                    @error('kode_cabang')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="nama_kota" class="form-label fw-bold">Nama Kota <span class="text-danger">*</span></label>
                    <input type="text" name="nama_kota" id="nama_kota" class="form-control @error('nama_kota') is-invalid @enderror" value="{{ old('nama_kota', $cabang->nama_kota) }}" required>
                    @error('nama_kota')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mt-3">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Update Cabang
                    </button>
                    <a href="{{ route('cabang.index') }}" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection