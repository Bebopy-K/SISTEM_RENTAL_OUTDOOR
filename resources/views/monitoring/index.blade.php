@extends('layouts.app')

@section('title', 'Monitoring Realtime Sistem')

@section('content')
<div class="container-fluid py-4">

    {{-- HEADER --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex align-items-center">
                <div class="bg-primary text-white rounded-circle p-3 me-3 shadow-sm">
                    <i class="fas fa-heartbeat fa-2x"></i>
                </div>
                <div>
                    <h1 class="h2 fw-bold mb-1">Monitoring Realtime Sistem</h1>
                    <p class="text-muted mb-0">Status keseluruhan sistem persewaan alat outdoor multi-cabang</p>
                </div>
            </div>
            <hr class="mt-3">
        </div>
    </div>

    {{-- 1. STATISTIK CEPAT --}}
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-gradient-primary text-white h-100">
                <div class="card-body d-flex flex-column">
                    <div class="d-flex justify-content-between align-items-start">
                        <h6 class="card-title text-white-50 text-uppercase fw-bold small mb-0"><i class="fas fa-users me-1"></i>Total User</h6>
                        <span class="badge bg-white text-primary rounded-pill px-3 py-2">Hari Ini</span>
                    </div>
                    <h2 class="fw-bold mb-2 mt-2 display-6">{{ $totalUsers ?? 0 }}</h2>
                    <div class="mt-auto">
                        <small class="text-white-70">
                            <span class="badge bg-danger bg-opacity-25 text-white me-1">Superadmin {{ $totalSuperadmin ?? 0 }}</span>
                            <span class="badge bg-info bg-opacity-25 text-white me-1">Manager {{ $totalManager ?? 0 }}</span>
                            <span class="badge bg-secondary bg-opacity-25 text-white">Staff {{ $totalStaff ?? 0 }}</span>
                        </small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-gradient-success text-white h-100">
                <div class="card-body d-flex flex-column">
                    <div class="d-flex justify-content-between align-items-start">
                        <h6 class="card-title text-white-50 text-uppercase fw-bold small mb-0"><i class="fas fa-user-check me-1"></i>User Aktif</h6>
                        <span class="badge bg-white text-success rounded-pill px-3 py-2">15 Menit</span>
                    </div>
                    <h2 class="fw-bold mb-2 mt-2 display-6">{{ $activeUsers ?? 0 }}</h2>
                    <div class="mt-auto">
                        <small class="text-white-70">Terakhir update: <strong>{{ now()->format('H:i:s') }}</strong></small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-gradient-info text-white h-100">
                <div class="card-body d-flex flex-column">
                    <div class="d-flex justify-content-between align-items-start">
                        <h6 class="card-title text-white-50 text-uppercase fw-bold small mb-0"><i class="fas fa-exchange-alt me-1"></i>Transaksi Hari Ini</h6>
                        <span class="badge bg-white text-info rounded-pill px-3 py-2">Harian</span>
                    </div>
                    <h2 class="fw-bold mb-2 mt-2 display-6">{{ $transaksiHariIni ?? 0 }}</h2>
                    <div class="mt-auto">
                        <small class="text-white-70">Pendapatan: <strong>Rp {{ number_format($pendapatanHariIni ?? 0, 0, ',', '.') }}</strong></small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-gradient-warning text-dark h-100">
                <div class="card-body d-flex flex-column">
                    <div class="d-flex justify-content-between align-items-start">
                        <h6 class="card-title text-dark-50 text-uppercase fw-bold small mb-0"><i class="fas fa-database me-1"></i>Database MySQL</h6>
                        <span class="badge bg-dark text-warning rounded-pill px-3 py-2">{{ $mysqlSize ?? 0 }} MB</span>
                    </div>
                    <h2 class="fw-bold mb-2 mt-2 display-6">{{ $mysqlSize ?? 0 }} MB</h2>
                    <div class="mt-auto">
                        <small>Status: 
                            <span class="badge bg-{{ ($mysqlStatus ?? 'Disconnected') == 'Connected' ? 'success' : 'danger' }} rounded-pill px-3 py-2">
                                {{ $mysqlStatus ?? 'Disconnected' }}
                            </span>
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- 2. STATUS KONEKSI DATABASE --}}
    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-light border-0 d-flex align-items-center">
                    <div class="bg-primary bg-opacity-10 text-primary rounded-circle p-2 me-2">
                        <i class="fas fa-database"></i>
                    </div>
                    <h5 class="fw-bold mb-0">MySQL (OLTP)</h5>
                </div>
                <div class="card-body">
                    <div class="row g-0">
                        <div class="col-4 text-muted">Status</div>
                        <div class="col-8">
                            <span class="badge bg-{{ ($mysqlStatus ?? 'Disconnected') == 'Connected' ? 'success' : 'danger' }} rounded-pill px-3 py-2">
                                {{ $mysqlStatus ?? 'Disconnected' }}
                            </span>
                        </div>
                    </div>
                    <div class="row g-0 mt-2">
                        <div class="col-4 text-muted">Versi</div>
                        <div class="col-8"><code>{{ $mysqlInfo ?? '-' }}</code></div>
                    </div>
                    <div class="row g-0 mt-2">
                        <div class="col-4 text-muted">Database</div>
                        <div class="col-8"><strong>{{ env('DB_DATABASE', 'oltp_rental') }}</strong></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-light border-0 d-flex align-items-center">
                    <div class="bg-success bg-opacity-10 text-success rounded-circle p-2 me-2">
                        <i class="fas fa-database"></i>
                    </div>
                    <h5 class="fw-bold mb-0">PostgreSQL (Data Warehouse)</h5>
                </div>
                <div class="card-body">
                    <div class="row g-0">
                        <div class="col-4 text-muted">Status</div>
                        <div class="col-8">
                            <span class="badge bg-{{ ($pgsqlStatus ?? 'Disconnected') == 'Connected' ? 'success' : 'danger' }} rounded-pill px-3 py-2">
                                {{ $pgsqlStatus ?? 'Disconnected' }}
                            </span>
                        </div>
                    </div>
                    <div class="row g-0 mt-2">
                        <div class="col-4 text-muted">Versi</div>
                        <div class="col-8"><code>{{ $pgsqlInfo ?? '-' }}</code></div>
                    </div>
                    <div class="row g-0 mt-2">
                        <div class="col-4 text-muted">Database</div>
                        <div class="col-8"><strong>{{ env('DB_DWH_DATABASE', 'dw_outdoor') }}</strong></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- 3. GRAFIK --}}
    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light border-0 d-flex align-items-center">
                    <div class="bg-primary bg-opacity-10 text-primary rounded-circle p-2 me-2">
                        <i class="fas fa-chart-bar"></i>
                    </div>
                    <h5 class="fw-bold mb-0">Transaksi 7 Hari Terakhir</h5>
                </div>
                <div class="card-body">
                    <canvas id="chartTransaksi" height="200"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light border-0 d-flex align-items-center">
                    <div class="bg-danger bg-opacity-10 text-danger rounded-circle p-2 me-2">
                        <i class="fas fa-store"></i>
                    </div>
                    <h5 class="fw-bold mb-0">Top 5 Cabang (Total Transaksi)</h5>
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
                <div class="card-header bg-light border-0 d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        <div class="bg-warning bg-opacity-10 text-warning rounded-circle p-2 me-2">
                            <i class="fas fa-history"></i>
                        </div>
                        <h5 class="fw-bold mb-0">Audit Log Terbaru</h5>
                    </div>
                    <a href="{{ route('audit.logs') }}" class="btn btn-sm btn-primary rounded-pill px-3">
                        <i class="fas fa-arrow-right me-1"></i> Lihat Semua
                    </a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3">Waktu</th>
                                    <th>User</th>
                                    <th>Role</th>
                                    <th>Aksi</th>
                                    <th>IP</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentLogs ?? [] as $log)
                                <tr>
                                    <td class="ps-3 text-nowrap">
                                        <span class="badge bg-light text-muted fw-normal me-1">
                                            <i class="far fa-clock"></i>
                                        </span>
                                        {{ $log->created_at ? $log->created_at->format('d/m/Y H:i:s') : '-' }}
                                    </td>
                                    <td>
                                        <span class="fw-semibold">{{ $log->username ?? 'Guest' }}</span>
                                    </td>
                                    <td>
                                        @php
                                            $roleBadge = match($log->role) {
                                                'superadmin' => 'danger',
                                                'manager' => 'primary',
                                                'staff' => 'secondary',
                                                default => 'light'
                                            };
                                        @endphp
                                        <span class="badge bg-{{ $roleBadge }}-subtle text-{{ $roleBadge }} rounded-pill px-3 py-2">
                                            {{ $log->role ?? '-' }}
                                        </span>
                                    </td>
                                    <td>
                                        @php
                                            $actionBadge = match($log->action) {
                                                'login' => 'success',
                                                'login_failed' => 'danger',
                                                'logout' => 'warning',
                                                default => 'secondary'
                                            };
                                        @endphp
                                        <span class="badge bg-{{ $actionBadge }}-subtle text-{{ $actionBadge }} rounded-pill px-3 py-2">
                                            {{ $log->action }}
                                        </span>
                                    </td>
                                    <td><code class="bg-light px-2 py-1 rounded">{{ $log->ip_address }}</code></td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">
                                        <i class="fas fa-inbox fa-2x d-block mb-2 opacity-25"></i>
                                        Belum ada log.
                                    </td>
                                </tr>
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
                <div class="card-header bg-light border-0 d-flex align-items-center">
                    <div class="bg-info bg-opacity-10 text-info rounded-circle p-2 me-2">
                        <i class="fas fa-info-circle"></i>
                    </div>
                    <h5 class="fw-bold mb-0">Informasi Sistem</h5>
                </div>
                <div class="card-body">
                    <div class="row g-0 border-bottom pb-2 mb-2">
                        <div class="col-5 text-muted">PHP Versi</div>
                        <div class="col-7"><strong>{{ $phpVersion ?? phpversion() }}</strong></div>
                    </div>
                    <div class="row g-0 border-bottom pb-2 mb-2">
                        <div class="col-5 text-muted">Laravel Versi</div>
                        <div class="col-7"><strong>{{ $laravelVersion ?? app()->version() }}</strong></div>
                    </div>
                    <div class="row g-0 border-bottom pb-2 mb-2">
                        <div class="col-5 text-muted">Waktu Server</div>
                        <div class="col-7"><strong>{{ $serverTime ?? now()->format('Y-m-d H:i:s') }}</strong></div>
                    </div>
                    <div class="row g-0 border-bottom pb-2 mb-2">
                        <div class="col-5 text-muted">Total Cabang</div>
                        <div class="col-7"><strong>{{ $totalCabang ?? 0 }}</strong></div>
                    </div>
                    <div class="row g-0 border-bottom pb-2 mb-2">
                        <div class="col-5 text-muted">Total Produk</div>
                        <div class="col-7"><strong>{{ $totalProduk ?? 0 }}</strong></div>
                    </div>
                    <div class="row g-0 border-bottom pb-2 mb-2">
                        <div class="col-5 text-muted">Total Transaksi</div>
                        <div class="col-7"><strong>{{ $totalTransaksi ?? 0 }}</strong></div>
                    </div>
                    @if(isset($lastEtl) && $lastEtl)
                    <div class="row g-0">
                        <div class="col-5 text-muted">ETL Terakhir</div>
                        <div class="col-7"><strong>{{ $lastEtl->finished_at ? $lastEtl->finished_at->format('d M Y H:i:s') : '-' }}</strong></div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light border-0 d-flex align-items-center">
                    <div class="bg-success bg-opacity-10 text-success rounded-circle p-2 me-2">
                        <i class="fas fa-chart-pie"></i>
                    </div>
                    <h5 class="fw-bold mb-0">Statistik User</h5>
                </div>
                <div class="card-body d-flex justify-content-center">
                    <div style="height:220px; width:100%; max-width:300px;">
                        <canvas id="chartUser"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- CSS tambahan untuk badge subtle --}}
<style>
    .bg-success-subtle { background-color: #d1e7dd; }
    .text-success { color: #0f5132; }
    .bg-danger-subtle { background-color: #f8d7da; }
    .text-danger { color: #842029; }
    .bg-warning-subtle { background-color: #fff3cd; }
    .text-warning { color: #664d03; }
    .bg-primary-subtle { background-color: #cfe2ff; }
    .text-primary { color: #084298; }
    .bg-secondary-subtle { background-color: #e2e3e5; }
    .text-secondary { color: #41464b; }
    .bg-light-subtle { background-color: #f8f9fa; }
    .text-light { color: #6c757d; }

    .bg-gradient-primary {
        background: linear-gradient(135deg, #0d6efd, #0a58ca);
    }
    .bg-gradient-success {
        background: linear-gradient(135deg, #198754, #157347);
    }
    .bg-gradient-info {
        background: linear-gradient(135deg, #0dcaf0, #0aa2c0);
    }
    .bg-gradient-warning {
        background: linear-gradient(135deg, #ffc107, #e0a800);
    }
    .text-white-70 { color: rgba(255,255,255,0.7); }
    .text-dark-50 { color: rgba(0,0,0,0.5); }
    .bg-opacity-25 { opacity: 0.25; }
</style>
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
                backgroundColor: 'rgba(13, 110, 253, 0.6)',
                borderColor: 'rgba(13, 110, 253, 1)',
                borderWidth: 1,
                borderRadius: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { stepSize: 1 }
                }
            }
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
                backgroundColor: 'rgba(220, 53, 69, 0.6)',
                borderColor: 'rgba(220, 53, 69, 1)',
                borderWidth: 1,
                borderRadius: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { stepSize: 1 }
                }
            }
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
                backgroundColor: ['#dc3545', '#0d6efd', '#6c757d'],
                borderWidth: 2,
                borderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 20,
                        usePointStyle: true,
                        pointStyle: 'circle'
                    }
                }
            }
        }
    });
});
</script>
@endpush