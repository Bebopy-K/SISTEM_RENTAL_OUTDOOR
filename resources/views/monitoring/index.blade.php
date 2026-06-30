@extends('layouts.app')

@section('title', 'Monitoring Realtime Sistem')

@section('content')
<div class="container-fluid py-4">

    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h3 fw-bold">
                <i class="fas fa-heartbeat text-primary me-2"></i> Monitoring Realtime Sistem
            </h1>
            <p class="text-muted">Status keseluruhan sistem persewaan alat outdoor multi-cabang.</p>
            <hr>
        </div>
    </div>

    {{-- 1. STATISTIK CEPAT --}}
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-primary text-white h-100">
                <div class="card-body">
                    <h6 class="card-title"><i class="fas fa-users me-1"></i> Total User</h6>
                    <h2 class="fw-bold mb-0">{{ $totalUsers ?? 0 }}</h2>
                    <small>Superadmin: {{ $totalSuperadmin ?? 0 }} | Manager: {{ $totalManager ?? 0 }} | Staff: {{ $totalStaff ?? 0 }}</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-success text-white h-100">
                <div class="card-body">
                    <h6 class="card-title"><i class="fas fa-user-check me-1"></i> User Aktif (15 menit)</h6>
                    <h2 class="fw-bold mb-0">{{ $activeUsers ?? 0 }}</h2>
                    <small>Terakhir update: {{ now()->format('H:i:s') }}</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-info text-white h-100">
                <div class="card-body">
                    <h6 class="card-title"><i class="fas fa-exchange-alt me-1"></i> Transaksi Hari Ini</h6>
                    <h2 class="fw-bold mb-0">{{ $transaksiHariIni ?? 0 }}</h2>
                    <small>Pendapatan: Rp {{ number_format($pendapatanHariIni ?? 0, 0, ',', '.') }}</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-warning text-dark h-100">
                <div class="card-body">
                    <h6 class="card-title"><i class="fas fa-database me-1"></i> Database MySQL</h6>
                    <h2 class="fw-bold mb-0">{{ $mysqlSize ?? 0 }} MB</h2>
                    <small>Status: <span class="badge bg-{{ ($mysqlStatus ?? 'Disconnected') == 'Connected' ? 'success' : 'danger' }}">{{ $mysqlStatus ?? 'Disconnected' }}</span></small>
                </div>
            </div>
        </div>
    </div>

    {{-- 2. STATUS KONEKSI DATABASE --}}
    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light">
                    <i class="fas fa-database me-1"></i> MySQL (OLTP)
                </div>
                <div class="card-body">
                    <p><strong>Status:</strong> <span class="badge bg-{{ ($mysqlStatus ?? 'Disconnected') == 'Connected' ? 'success' : 'danger' }}">{{ $mysqlStatus ?? 'Disconnected' }}</span></p>
                    <p><strong>Versi:</strong> {{ $mysqlInfo ?? '-' }}</p>
                    <p><strong>Database:</strong> {{ env('DB_DATABASE', 'oltp_rental') }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light">
                    <i class="fas fa-database me-1"></i> PostgreSQL (Data Warehouse)
                </div>
                <div class="card-body">
                    <p><strong>Status:</strong> <span class="badge bg-{{ ($pgsqlStatus ?? 'Disconnected') == 'Connected' ? 'success' : 'danger' }}">{{ $pgsqlStatus ?? 'Disconnected' }}</span></p>
                    <p><strong>Versi:</strong> {{ $pgsqlInfo ?? '-' }}</p>
                    <p><strong>Database:</strong> {{ env('DB_DWH_DATABASE', 'dw_outdoor') }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- 3. GRAFIK --}}
    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light">
                    <i class="fas fa-chart-bar me-1"></i> Transaksi 7 Hari Terakhir
                </div>
                <div class="card-body">
                    <canvas id="chartTransaksi" height="200"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light">
                    <i class="fas fa-store me-1"></i> Top 5 Cabang (Total Transaksi)
                </div>
                <div class="card-body">
                    <canvas id="chartCabang" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- 4. AUDIT LOG TERBARU --}}
    <div class="row g-3 mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <span><i class="fas fa-history me-1"></i> Audit Log Terbaru</span>
                    <a href="{{ route('audit.logs') }}" class="btn btn-sm btn-primary">Lihat Semua</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover table-striped">
                            <thead>
                                <tr>
                                    <th>Waktu</th>
                                    <th>User</th>
                                    <th>Role</th>
                                    <th>Aksi</th>
                                    <th>IP</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentLogs ?? [] as $log)
                                <tr>
                                    <td>{{ $log->created_at ? $log->created_at->format('d/m/Y H:i:s') : '-' }}</td>
                                    <td>{{ $log->username ?? 'Guest' }}</td>
                                    <td>
                                        <span class="badge bg-{{ match($log->role) {
                                            'superadmin' => 'danger',
                                            'manager' => 'primary',
                                            'staff' => 'secondary',
                                            default => 'light'
                                        } }}">
                                            {{ $log->role ?? '-' }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ match($log->action) {
                                            'login' => 'success',
                                            'login_failed' => 'danger',
                                            'logout' => 'warning',
                                            default => 'secondary'
                                        } }}">
                                            {{ $log->action }}
                                        </span>
                                    </td>
                                    <td><code>{{ $log->ip_address }}</code></td>
                                </tr>
                                @empty
                                <tr><td colspan="5" class="text-center text-muted">Belum ada log.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- 5. INFO SISTEM --}}
    <div class="row g-3">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light">
                    <i class="fas fa-info-circle me-1"></i> Informasi Sistem
                </div>
                <div class="card-body">
                    <p><strong>PHP Versi:</strong> {{ $phpVersion ?? phpversion() }}</p>
                    <p><strong>Laravel Versi:</strong> {{ $laravelVersion ?? app()->version() }}</p>
                    <p><strong>Waktu Server:</strong> {{ $serverTime ?? now()->format('Y-m-d H:i:s') }}</p>
                    <p><strong>Total Cabang:</strong> {{ $totalCabang ?? 0 }}</p>
                    <p><strong>Total Produk:</strong> {{ $totalProduk ?? 0 }}</p>
                    <p><strong>Total Transaksi (Semua):</strong> {{ $totalTransaksi ?? 0 }}</p>
                    @if(isset($lastEtl) && $lastEtl)
                    <p><strong>ETL Terakhir:</strong> {{ $lastEtl->finished_at ? $lastEtl->finished_at->format('d M Y H:i:s') : '-' }}</p>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light">
                    <i class="fas fa-chart-pie me-1"></i> Statistik User
                </div>
                <div class="card-body">
                    <div style="height:220px;">
                        <canvas id="chartUser"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. Transaksi 7 hari
    new Chart(document.getElementById('chartTransaksi'), {
        type: 'bar',
        data: {
            labels: @json($chartLabels ?? []),
            datasets: [{
                label: 'Jumlah Transaksi',
                data: @json($chartData ?? []),
                backgroundColor: 'rgba(54, 162, 235, 0.5)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
        }
    });

    // 2. Top 5 Cabang
    new Chart(document.getElementById('chartCabang'), {
        type: 'bar',
        data: {
            labels: @json($cabangLabels ?? []),
            datasets: [{
                label: 'Total Transaksi',
                data: @json($cabangData ?? []),
                backgroundColor: 'rgba(255, 99, 132, 0.5)',
                borderColor: 'rgba(255, 99, 132, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
        }
    });

    // 3. Pie User
    new Chart(document.getElementById('chartUser'), {
        type: 'pie',
        data: {
            labels: ['Superadmin', 'Manager', 'Staff'],
            datasets: [{
                data: [
                    {{ $totalSuperadmin ?? 0 }},
                    {{ $totalManager ?? 0 }},
                    {{ $totalStaff ?? 0 }}
                ],
                backgroundColor: ['#dc3545', '#0d6efd', '#6c757d']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom' }
            }
        }
    });
});
</script>
@endpush