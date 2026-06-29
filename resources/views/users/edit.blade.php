@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 fw-bold">Edit User: {{ $user->username }}</h1>
            <p class="text-muted">Ubah data akun user.</p>
        </div>
        <a href="{{ route('users.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <form method="POST" action="{{ route('users.update', $user->id_user) }}">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="username" class="form-label fw-bold">Username <span class="text-danger">*</span></label>
                        <input type="text" name="username" id="username" class="form-control @error('username') is-invalid @enderror" value="{{ old('username', $user->username) }}" required>
                        @error('username')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="email" class="form-label fw-bold">Email</label>
                        <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}">
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="password" class="form-label fw-bold">Password Baru</label>
                        <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror">
                        <small class="text-muted">Kosongkan jika tidak ingin mengubah password.</small>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="role" class="form-label fw-bold">Role <span class="text-danger">*</span></label>
                        <select name="role" id="role" class="form-select @error('role') is-invalid @enderror" required>
                            <option value="superadmin" {{ old('role', $user->role) == 'superadmin' ? 'selected' : '' }}>Superadmin</option>
                            <option value="manager" {{ old('role', $user->role) == 'manager' ? 'selected' : '' }}>Manager</option>
                            <option value="staff" {{ old('role', $user->role) == 'staff' ? 'selected' : '' }}>Staff</option>
                        </select>
                        @error('role')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="cabang_id" class="form-label fw-bold">Cabang</label>
                        <select name="cabang_id" id="cabang_id" class="form-select @error('cabang_id') is-invalid @enderror">
                            <option value="">-- Tidak Terikat Cabang --</option>
                            @foreach($cabangs as $cabang)
                                <option value="{{ $cabang->id_cabang }}" {{ old('cabang_id', $user->cabang_id) == $cabang->id_cabang ? 'selected' : '' }}>
                                    {{ $cabang->nama_kota }}
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted">Khusus superadmin, biarkan kosong.</small>
                        @error('cabang_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 mt-3">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Update User
                        </button>
                        <a href="{{ route('users.index') }}" class="btn btn-secondary">Batal</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection