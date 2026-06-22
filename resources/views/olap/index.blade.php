@extends('layouts.app')

@section('title', 'Laporan Analitik (OLAP)')

@section('content')
<div class="row">
    <div class="col-12">
        <h1 class="mb-3">Laporan Analitik (OLAP) - Data Warehouse</h1>
        <p class="text-muted">Data diambil langsung dari PostgreSQL (skema bintang) melalui view <code>v_dashboard_nasional</code>.</p>
        <hr>
    </div>
</div>

{{-- FILTER CABANG (HANYA UNTUK SUPERADMIN) --}}
@if(Auth::user()->role == 'superadmin')
<div class="row mb-4">
    <div class="col-md-4">
        <form method="GET" action="{{ route('olap') }}" class="row g-3 align-items-end">
            <div class="col-auto">
                <label for="cabang" class="col-form-label">Filter Cabang:</label>
            </div>
            <div class="col-auto">
                <select name="cabang" id="cabang" class="form-select">
                    <option value="all" {{ $selectedCabang == 'all' || !$selectedCabang ? 'selected' : '' }}>Semua Cabang</option>
                    @foreach($daftarCabang as $cabang)
                        <option value="{{ $cabang }}" {{ $selectedCabang == $cabang ? 'selected' : '' }}>{{ $cabang }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-primary">Tampilkan</button>
            </div>
        </form>
    </div>
</div>
@endif

{{-- TIGA KARTU METRIK UTAMA --}}
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card text-white bg-primary shadow-sm">
            <div class="card-body">
                <h5 class="card-title"><i class="fas fa-money-bill-wave"></i> Total Pendapatan Bersih</h5>
                <h3 class="card-text">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-white bg-success shadow-sm">
            <div class="card-body">
                <h5 class="card-title"><i class="fas fa-receipt"></i> Total Transaksi</h5>
                <h3 class="card-text">{{ number_format($totalTransaksi, 0, ',', '.') }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-white bg-info shadow-sm">
            <div class="card-body">
                <h5 class="card-title"><i class="fas fa-chart-simple"></i> Rata-rata per Transaksi</h5>
                <h3 class="card-text">Rp {{ number_format($rataPendapatan, 0, ',', '.') }}</h3>
            </div>
        </div>
    </div>
</div>

{{-- GRAFIK PENDAPATAN PER CABANG (HANYA UNTUK SUPERADMIN DENGAN FILTER ALL) --}}
@if($pendapatanPerCabang->count() > 0)
<div class="row mb-4">
    <div class="col-md-12">
        <div class="card shadow-sm">
            <div class="card-header bg-light"><i class="fas fa-store"></i> Pendapatan per Cabang (Semua Cabang)</div>
            <div class="card-body">
                <canvas id="cabangChart" height="300"></canvas>
            </div>
        </div>
    </div>
</div>
@endif

{{-- DUA GRAFIK: PRODUK TERLARIS DAN TREN HARIAN --}}
<div class="row mb-4">
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-header bg-light"><i class="fas fa-tag"></i> 5 Produk Terlaris (Unit)</div>
            <div class="card-body">
                <canvas id="produkChart" height="300"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-header bg-light"><i class="fas fa-chart-line"></i> Tren Pendapatan Harian (30 hari terakhir)</div>
            <div class="card-body">
                <canvas id="trenChart" height="300"></canvas>
            </div>
        </div>
    </div>
</div>

{{-- TABEL TRANSAKSI TERBARU --}}
<div class="row">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-header bg-light"><i class="fas fa-table"></i> Transaksi Terbaru</div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>Tanggal</th>
                                <th>Cabang</th>
                                <th>Produk</th>
                                <th>Jumlah Unit</th>
                                <th>Pendapatan Bersih</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($transaksiTerbaru as $t)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($t->tanggal)->format('d/m/Y') }}</td>
                                <td>{{ $t->nama_kota }}</td>
                                <td>{{ $t->nama_produk }}</td>
                                <td>{{ $t->jumlah_unit }}</td>
                                <td>Rp {{ number_format($t->pendapatan_bersih, 0, ',', '.') }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted">Belum ada data transaksi.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        @if($pendapatanPerCabang->count() > 0)
            // Grafik Pendapatan per Cabang (hanya untuk superadmin)
            const cabangLabels = @json($pendapatanPerCabang->pluck('nama_kota'));
            const cabangData = @json($pendapatanPerCabang->pluck('total'));
            const ctxCabang = document.getElementById('cabangChart').getContext('2d');
            new Chart(ctxCabang, {
                type: 'bar',
                data: {
                    labels: cabangLabels,
                    datasets: [{
                        label: 'Pendapatan (Rp)',
                        data: cabangData,
                        backgroundColor: 'rgba(54, 162, 235, 0.5)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { callback: (val) => 'Rp ' + val.toLocaleString('id-ID') }
                        }
                    }
                }
            });
        @endif

        // Grafik Produk Terlaris
        const produkLabels = @json($produkTerlaris->pluck('nama_produk'));
        const produkData = @json($produkTerlaris->pluck('total_unit'));
        const ctxProduk = document.getElementById('produkChart').getContext('2d');
        new Chart(ctxProduk, {
            type: 'bar',
            data: {
                labels: produkLabels,
                datasets: [{
                    label: 'Jumlah Unit Disewa',
                    data: produkData,
                    backgroundColor: 'rgba(255, 99, 132, 0.5)',
                    borderColor: 'rgba(255, 99, 132, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
            }
        });

        // Grafik Tren Pendapatan Harian
        const trenLabels = @json($trenHarian->pluck('tanggal')->map(function($d) {
            return \Carbon\Carbon::parse($d)->format('d/m');
        }));
        const trenData = @json($trenHarian->pluck('total'));
        const ctxTren = document.getElementById('trenChart').getContext('2d');
        new Chart(ctxTren, {
            type: 'line',
            data: {
                labels: trenLabels,
                datasets: [{
                    label: 'Pendapatan (Rp)',
                    data: trenData,
                    borderColor: 'rgba(75, 192, 192, 1)',
                    backgroundColor: 'rgba(75, 192, 192, 0.1)',
                    fill: true,
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { callback: (val) => 'Rp ' + val.toLocaleString('id-ID') }
                    }
                }
            }
        });
    });
</script>
@endpush