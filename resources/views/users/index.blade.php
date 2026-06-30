@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 fw-bold">Manajemen User</h1>
            <p class="text-muted">Kelola akun superadmin, manager, dan staff cabang.</p>
        </div>
        <a href="{{ route('users.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Tambah User
        </a>
    </div>

    {{-- NOTIFIKASI UNTUK MANAGER --}}
    @if(Auth::user()->role === 'manager')
        <div class="alert alert-info mb-3">
            <i class="fas fa-info-circle me-1"></i> 
            Anda hanya dapat mengelola staff di cabang <strong>{{ Auth::user()->cabang->nama_kota ?? 'Anda' }}</strong>.
        </div>
    @endif

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Cabang</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                        <tr>
                            <td>#{{ $user->id_user }}</td>
                            <td>
                                @if(Auth::user()->role === 'superadmin')
                                    <strong>{{ $user->username }}</strong>
                                @else
                                    <strong>{{ \App\Helpers\DataMaskingHelper::maskUsername($user->username) }}</strong>
                                    <span class="text-muted d-block" style="font-size:10px;">(masked)</span>
                                @endif
                            </td>
                            <td>
                                @if(Auth::user()->role === 'superadmin')
                                    {{ $user->email ?? '-' }}
                                @else
                                    {{ \App\Helpers\DataMaskingHelper::maskEmail($user->email ?? '-') }}
                                    <span class="text-muted d-block" style="font-size:10px;">(masked)</span>
                                @endif
                            </td>
                            <td>
                                @php
                                    $badgeColor = match($user->role) {
                                        'superadmin' => 'danger',
                                        'manager' => 'primary',
                                        'staff' => 'secondary',
                                        default => 'light'
                                    };
                                @endphp
                                <span class="badge bg-{{ $badgeColor }}">
                                    {{ ucfirst($user->role) }}
                                </span>
                            </td>
                            <td>{{ $user->cabang->nama_kota ?? '-' }}</td>
                            <td class="text-center">
                                <a href="{{ route('users.edit', $user->id_user) }}" class="btn btn-sm btn-warning">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('users.destroy', $user->id_user) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin hapus user {{ $user->username }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">
                                <i class="fas fa-users fa-2x mb-2 d-block opacity-25"></i>
                                Belum ada user terdaftar.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- INFORMASI DATA MASKING (HANYA UNTUK MANAGER & STAFF) --}}
    @if(Auth::user()->role !== 'superadmin')
    <div class="mt-3 text-muted small">
        <i class="fas fa-shield-alt text-primary me-1"></i>
        Data sensitif (username & email) telah di-masking untuk melindungi privasi.
        <br>Contoh: <strong>superadmin</strong> → <strong>s********n</strong>, 
        <strong>email@domain.com</strong> → <strong>e*********@domain.com</strong>
    </div>
    @endif
</div>
@endsection