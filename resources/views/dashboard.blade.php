@extends('layouts.app')

@section('content')
<div class="container py-4">

    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h1 class="h3 mb-1 text-dark fw-bold">Dashboard Analitika</h1>
            <p class="text-muted mb-0">Konsolidasi ringkasan data operasional dan finansial ekosistem multi-cabang.</p>
        </div>

        @if($daftarCabang)
        <div class="bg-white p-2 rounded shadow-sm border">
            <form method="GET" action="{{ route('dashboard') }}" class="d-flex align-items-center gap-2 mb-0">
                <label for="id_cabang" class="small text-secondary fw-bold text-nowrap mb-0"><i class="fas fa-filter text-primary me-1"></i> Wilayah:</label>
                <select name="id_cabang" id="id_cabang" class="form-select form-select-sm border-0 bg-light" onchange="this.form.submit()">
                    <option value="all" {{ $selectedBranch == 'all' || !$selectedBranch ? 'selected' : '' }}>Semua Cabang Nasional</option>
                    @foreach($daftarCabang as $cab)
                        <option value="{{ $cab->id_cabang }}" {{ $selectedBranch == $cab->id_cabang ? 'selected' : '' }}>
                            {{ $cab->nama_kota }}
                        </option>
                    @endforeach
                </select>
            </form>
        </div>
        @endif
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-6 col-lg-4">
            <div class="card border-0 shadow-sm rounded-3 bg-white p-3 h-100">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-secondary small fw-bold text-uppercase font-monospace d-block mb-1">Total Finansial (Fact Value)</span>
                        <h3 class="fw-bold text-success font-monospace mb-0">Rp {{ number_format($akumulasiFinansial, 0, ',', '.') }}</h3>
                    </div>
                    <div class="bg-success-light text-success p-3 rounded-circle" style="background-color: #e8f5e9;">
                        <i class="fas fa-wallet fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-4">
            <div class="card border-0 shadow-sm rounded-3 bg-white p-3 h-100">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-secondary small fw-bold text-uppercase font-monospace d-block mb-1">Total Volume Transaksi</span>
                        <h3 class="fw-bold text-dark font-monospace mb-0">{{ $totalTransaksiCount }} Kali</h3>
                    </div>
                    <div class="bg-primary-light text-primary p-3 rounded-circle" style="background-color: #e3f2fd;">
                        <i class="fas fa-file-invoice-dollar fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-12 col-lg-4">
            <div class="card border-0 shadow-sm rounded-3 bg-white p-3 h-100">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-secondary small fw-bold text-uppercase font-monospace d-block mb-1">Status Hak Akses Wilayah</span>
                        <h5 class="fw-bold text-secondary mb-0 text-capitalize">
                            <i class="fas fa-map-marker-alt text-danger me-1"></i>
                            @if($user->role === 'admin_cabang')
                                Cabang ID: {{ $user->cabang_id }}
                            @else
                                Pemantauan Nasional
                            @endif
                        </h5>
                    </div>
                    <div class="bg-warning-light text-warning p-3 rounded-circle" style="background-color: #fffde7;">
                        <i class="fas fa-shield-alt fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($user->role === 'superadmin')
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-3 bg-white p-4">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <div>
                        <h5 class="fw-bold text-dark mb-1">
                            <i class="fas fa-sync-alt text-primary me-2"></i>Sinkronisasi Data Warehouse (ETL)
                        </h5>
                        <p class="text-muted small mb-0">
                            Sinkronkan transaksi baru dari <strong>MySQL (OLTP)</strong> ke <strong>PostgreSQL (Data Warehouse)</strong>.
                            <br>Hanya transaksi dengan status <span class="badge bg-warning text-dark">belum disinkron</span> yang akan diproses.
                        </p>
                    </div>
                    <div class="flex-shrink-0">
                        <button type="button" id="btn-sync-etl" class="btn btn-success px-4 py-2 shadow-sm" onclick="jalankanEtlSync()">
                            <i class="fas fa-upload me-1"></i> Jalankan ETL Sekarang
                        </button>
                    </div>
                </div>

                <div id="etl-output-container" class="mt-3 d-none">
                    <hr>
                    <strong class="small text-secondary">Output ETL:</strong>
                    <pre id="etl-output-text" class="bg-light p-2 border rounded mt-1" style="max-height:200px; overflow-y:auto; font-size:13px;"></pre>
                </div>
            </div>
        </div>
    </div>
    @endif

    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-3 bg-white p-4">
                <h5 class="fw-bold text-dark mb-4"><i class="fas fa-chart-line text-primary me-2"></i>Tren Pendapatan Bulanan (Data Warehouse Ready)</h5>

                @if(count($chartLabels) > 0)
                    <div style="height: 300px; width: 100%;">
                        <canvas id="trenPendapatanChart"></canvas>
                    </div>
                @else
                    <div class="text-center py-5 text-muted">
                        <i class="fas fa-chart-bar fa-3x opacity-20 mb-3 d-block"></i>
                        Belum ada riwayat transaksi bulanan untuk membuat visualisasi grafik.
                    </div>
                @endif
            </div>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // --- 1. INISIALISASI GRAFIK CHART.JS ---
        @if(count($chartLabels) > 0)
        const ctx = document.getElementById('trenPendapatanChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: {!! json_encode($chartLabels) !!},
                datasets: [{
                    label: 'Pendapatan Kotor (Rp)',
                    data: {!! json_encode($chartData) !!},
                    borderColor: '#0d6efd',
                    backgroundColor: 'rgba(13, 110, 253, 0.05)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.3
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
                        ticks: {
                            callback: function(value) {
                                return 'Rp ' + value.toLocaleString('id-ID');
                            }
                        }
                    }
                }
            }
        });
        @endif
    });

    // --- 2. AJAX HANDLER UNTUK SINKRONISASI ETL ---
    function jalankanEtlSync() {
        const tombol = document.getElementById('btn-sync-etl');
        const outputContainer = document.getElementById('etl-output-container');
        const outputText = document.getElementById('etl-output-text');

        if (!tombol) return;

        // Kunci tombol & ganti teks menjadi loading spinner
        tombol.disabled = true;
        tombol.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Memproses Sinkronisasi...';

        // Tembak REST API Laravel via Fetch di background
        fetch("{{ route('etl.sync') }}", {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": "{{ csrf_token() }}",
                "Content-Type": "application/json",
                "Accept": "application/json"
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Terjadi kesalahan pada server (Status HTTP: ' + response.status + ').');
            }
            return response.json();
        })
        .then(data => {
            if (data.status === 'success') {
                // Tampilkan log output terminal dari Controller ke element <pre>
                outputContainer.classList.remove('d-none');
                outputText.innerText = data.output || 'Sinkronisasi selesai tanpa log pesan.';
                outputText.className = "bg-light text-success p-2 border rounded mt-1";

                alert(data.message || 'Sinkronisasi berhasil diselesaikan!');

                // Beri jeda 1.5 detik agar user sempat membaca log, lalu reload dashboard
                setTimeout(() => {
                    window.location.reload();
                }, 1500);
            } else {
                alert('Gagal melakukan sinkronisasi: ' + data.message);
                resetTombolEtl(tombol);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Proses gagal atau terputus (Request Timeout / Server Error 500).');
            resetTombolEtl(tombol);
        });
    }

    function resetTombolEtl(tombol) {
        tombol.disabled = false;
        tombol.innerHTML = '<i class="fas fa-upload me-1"></i> Jalankan ETL Sekarang';
    }
</script>
@endsection
