@extends('layouts.app')

@section('title', 'Backup & Recovery')

@section('content')
<div class="container py-4">

    @php
        if (!isset($stats)) {
            $stats = ['total' => 0, 'full' => 0, 'incremental' => 0, 'differential' => 0, 'success' => 0, 'failed' => 0];
        }
    @endphp

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 fw-bold"><i class="fas fa-database text-primary me-2"></i>Backup & Recovery</h1>
            <p class="text-muted">Kelola backup database MySQL (OLTP) dan PostgreSQL (Data Warehouse).</p>
        </div>
        <a href="{{ route('backup.drp') }}" class="btn btn-outline-info">
            <i class="fas fa-shield-alt"></i> Disaster Recovery Plan
        </a>
    </div>

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
    @if(session('info'))
        <div class="alert alert-info alert-dismissible fade show" role="alert">
            {{ session('info') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- STATISTIK --}}
    <div class="row g-3 mb-4">
        <div class="col-md-2">
            <div class="card shadow-sm border-0 bg-primary text-white">
                <div class="card-body">
                    <h6 class="card-title">Total Backup</h6>
                    <h2 class="fw-bold">{{ $stats['total'] }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card shadow-sm border-0 bg-success text-white">
                <div class="card-body">
                    <h6 class="card-title">Full</h6>
                    <h2 class="fw-bold">{{ $stats['full'] }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card shadow-sm border-0 bg-info text-white">
                <div class="card-body">
                    <h6 class="card-title">Incremental</h6>
                    <h2 class="fw-bold">{{ $stats['incremental'] }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card shadow-sm border-0 bg-warning text-dark">
                <div class="card-body">
                    <h6 class="card-title">Differential</h6>
                    <h2 class="fw-bold">{{ $stats['differential'] }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card shadow-sm border-0 bg-success text-white">
                <div class="card-body">
                    <h6 class="card-title">Berhasil</h6>
                    <h2 class="fw-bold">{{ $stats['success'] }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card shadow-sm border-0 bg-danger text-white">
                <div class="card-body">
                    <h6 class="card-title">Gagal</h6>
                    <h2 class="fw-bold">{{ $stats['failed'] }}</h2>
                </div>
            </div>
        </div>
    </div>

    {{-- TOMBOL ACTION --}}
    <div class="row g-2 mb-4">
        <div class="col-md-3">
            <form method="POST" action="{{ route('backup.full') }}">
                @csrf
                <button type="submit" class="btn btn-primary w-100" onclick="this.disabled=true; this.innerHTML='<i class=\'fas fa-spinner fa-spin\'></i> Proses...'">
                    <i class="fas fa-database"></i> Full Backup
                </button>
            </form>
        </div>
        <div class="col-md-3">
            <form method="POST" action="{{ route('backup.incremental') }}">
                @csrf
                <button type="submit" class="btn btn-info w-100 text-white" onclick="this.disabled=true; this.innerHTML='<i class=\'fas fa-spinner fa-spin\'></i> Proses...'">
                    <i class="fas fa-plus-circle"></i> Incremental Backup
                </button>
            </form>
        </div>
        <div class="col-md-3">
            <form method="POST" action="{{ route('backup.differential') }}">
                @csrf
                <button type="submit" class="btn btn-warning w-100 text-dark" onclick="this.disabled=true; this.innerHTML='<i class=\'fas fa-spinner fa-spin\'></i> Proses...'">
                    <i class="fas fa-database"></i> Differential Backup
                </button>
            </form>
        </div>
        <div class="col-md-3">
            <a href="{{ route('backup.index') }}" class="btn btn-secondary w-100">
                <i class="fas fa-sync"></i> Refresh
            </a>
        </div>
    </div>

    {{-- TABEL BACKUP --}}
    <div class="card shadow-sm">
        <div class="card-header bg-light">
            <i class="fas fa-list"></i> Riwayat Backup
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Nama</th>
                            <th>Tipe</th>
                            <th>Database</th>
                            <th>Ukuran</th>
                            <th>Status</th>
                            <th>Waktu</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($backups as $b)
                        <tr>
                            <td>#{{ $b->id }}</td>
                            <td>{{ $b->name }}</td>
                            <td>
                                <span class="badge bg-{{ $b->type == 'full' ? 'primary' : ($b->type == 'incremental' ? 'info' : 'warning') }}">
                                    {{ ucfirst($b->type) }}
                                </span>
                            </td>
                            <td>{{ $b->database }}</td>
                            <td>{{ $b->size ?? '-' }}</td>
                            <td>
                                <span class="badge bg-{{ $b->status == 'success' ? 'success' : ($b->status == 'failed' ? 'danger' : 'warning') }}">
                                    {{ ucfirst($b->status) }}
                                </span>
                            </td>
                            <td>{{ $b->completed_at ? $b->completed_at->format('d/m/Y H:i') : '-' }}</td>
                            <td>
                                @if($b->status == 'success' && $b->filename)
                                    <a href="{{ route('backup.download', $b->id) }}" class="btn btn-sm btn-success" title="Download">
                                        <i class="fas fa-download"></i>
                                    </a>
                                    <a href="{{ route('backup.restore', $b->id) }}" class="btn btn-sm btn-warning" title="Restore" onclick="return confirm('Yakin restore backup ini? Data saat ini akan ditimpa.')">
                                        <i class="fas fa-undo"></i>
                                    </a>
                                @endif
                                <form action="{{ route('backup.destroy', $b->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin hapus backup ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">
                                <i class="fas fa-database fa-2x mb-2 d-block opacity-25"></i>
                                Belum ada backup yang dibuat.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $backups->links() }}
        </div>
    </div>
</div>
@endsection