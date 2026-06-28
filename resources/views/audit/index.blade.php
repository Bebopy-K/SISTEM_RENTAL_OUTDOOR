@extends('layouts.app')

@section('title', 'Audit Log')

@section('content')
<div class="container py-4">
    <h1 class="h3 fw-bold">Audit Log</h1>
    <p class="text-muted">Catatan semua aktivitas user di sistem.</p>

    {{-- Filter --}}
    <div class="card shadow-sm mb-4 p-3">
        <form method="GET" class="row g-3">
            <div class="col-md-3">
                <label>Aksi</label>
                <select name="action" class="form-select">
                    <option value="">Semua</option>
                    @foreach($actions as $act)
                        <option value="{{ $act }}" {{ request('action')==$act ? 'selected' : '' }}>{{ $act }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label>User</label>
                <select name="user_id" class="form-select">
                    <option value="">Semua</option>
                    @foreach($users as $u)
                        <option value="{{ $u->id_user }}" {{ request('user_id')==$u->id_user ? 'selected' : '' }}>{{ $u->username }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label>Dari</label>
                <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
            </div>
            <div class="col-md-2">
                <label>Sampai</label>
                <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button class="btn btn-primary w-100">Filter</button>
            </div>
        </form>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>Waktu</th>
                            <th>User</th>
                            <th>Role</th>
                            <th>Aksi</th>
                            <th>Deskripsi</th>
                            <th>IP</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs as $log)
                        <tr>
                            <td>{{ $log->created_at }}</td>
                            <td>{{ $log->username ?? 'Guest' }}</td>
                            <td>{{ $log->role ?? '-' }}</td>
                            <td><span class="badge bg-secondary">{{ $log->action }}</span></td>
                            <td>{{ $log->description }}</td>
                            <td>{{ $log->ip_address }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="text-center">Belum ada log.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $logs->appends(request()->query())->links() }}
        </div>
    </div>
</div>
@endsection