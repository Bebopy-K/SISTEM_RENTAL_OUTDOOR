@extends('layouts.app')

@section('title', 'Disaster Recovery Plan')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 fw-bold"><i class="fas fa-shield-alt text-danger me-2"></i>Disaster Recovery Plan (DRP)</h1>
            <p class="text-muted">Rencana pemulihan ketika terjadi bencana pada sistem.</p>
        </div>
        <a href="{{ route('backup.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="row g-4">
        {{-- Komponen DRP --}}
        <div class="col-md-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-danger text-white">
                    <i class="fas fa-cogs"></i> Komponen DRP
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <h6><i class="fas fa-server text-primary me-2"></i>Backup Site</h6>
                        <p class="text-muted small">Lokasi penyimpanan backup (cloud / server terpisah). Backup disimpan di <code>storage/app/backups/</code> dan dapat diunduh kapan saja.</p>
                    </div>
                    <div class="mb-3">
                        <h6><i class="fas fa-undo-alt text-success me-2"></i>Recovery Procedure</h6>
                        <p class="text-muted small">Langkah-langkah pemulihan data menggunakan backup yang tersedia. Klik tombol <strong>Restore</strong> pada riwayat backup untuk memulihkan data.</p>
                    </div>
                    <div class="mb-3">
                        <h6><i class="fas fa-users text-warning me-2"></i>Tim Pemulihan</h6>
                        <p class="text-muted small">Superadmin bertanggung jawab penuh atas proses backup dan recovery. Tim IT dapat dihubungi jika diperlukan.</p>
                    </div>
                    <div>
                        <h6><i class="fas fa-file-alt text-info me-2"></i>Dokumentasi</h6>
                        <p class="text-muted small">Dokumentasi backup dan recovery tersedia di halaman ini dan di laporan sistem.</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Diagram DRP --}}
        <div class="col-md-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-primary text-white">
                    <i class="fas fa-sitemap"></i> Diagram DRP
                </div>
                <div class="card-body">
                    <div class="text-center">
                        <div class="p-3 mb-2 bg-light rounded">
                            <h5 class="text-danger"><i class="fas fa-server"></i> Primary Site</h5>
                            <p class="small">Sistem operasional + Data Warehouse</p>
                        </div>
                        <i class="fas fa-arrow-down fa-2x text-muted my-2"></i>
                        <div class="p-3 mb-2 bg-warning rounded">
                            <h5 class="text-dark"><i class="fas fa-exclamation-triangle"></i> Gangguan / Bencana</h5>
                            <p class="small">Kerusakan server, ransomware, kesalahan user</p>
                        </div>
                        <i class="fas fa-arrow-down fa-2x text-muted my-2"></i>
                        <div class="p-3 mb-2 bg-success rounded">
                            <h5 class="text-white"><i class="fas fa-database"></i> Backup Site</h5>
                            <p class="small">Backup tersimpan di <code>storage/app/backups/</code></p>
                        </div>
                        <i class="fas fa-arrow-down fa-2x text-muted my-2"></i>
                        <div class="p-3 mb-2 bg-info rounded">
                            <h5 class="text-white"><i class="fas fa-undo-alt"></i> Recovery Process</h5>
                            <p class="small">Restore data dari backup yang valid</p>
                        </div>
                        <i class="fas fa-arrow-down fa-2x text-muted my-2"></i>
                        <div class="p-3 mb-2 bg-primary rounded">
                            <h5 class="text-white"><i class="fas fa-check-circle"></i> Sistem Normal Kembali</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Contoh Kasus Bencana --}}
    <div class="mt-4">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-light">
                <i class="fas fa-bolt text-warning me-2"></i> Contoh Skenario Bencana & Solusi
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>Jenis Bencana</th>
                                <th>Dampak</th>
                                <th>Solusi Recovery</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><span class="badge bg-danger">Kerusakan Server</span></td>
                                <td>Database tidak dapat diakses</td>
                                <td>Restore dari <strong>Full Backup</strong> terakhir</td>
                            </tr>
                            <tr>
                                <td><span class="badge bg-danger">Serangan Ransomware</span></td>
                                <td>Data terenkripsi / hilang</td>
                                <td>Restore dari <strong>Full Backup</strong> + <strong>Incremental</strong> terbaru</td>
                            </tr>
                            <tr>
                                <td><span class="badge bg-danger">Kesalahan Pengguna</span></td>
                                <td>Data terhapus / berubah salah</td>
                                <td>Restore dari <strong>Differential</strong> atau <strong>Incremental</strong> sebelum kejadian</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection