@extends('layouts.app')

@section('content')
<div class="container-fluid py-4" style="background-color: #f8f9fa; min-height: 100vh;">

    <div class="row align-items-center mb-4">
        <div class="col-md-6 mb-3 mb-md-0">
            <h1 class="h3 mb-1 text-dark fw-bold">Inventaris Alat Outdoor</h1>
            <p class="text-muted mb-0">Manajemen logistik ketersediaan dan kondisi unit barang per cabang.</p>
        </div>
        <div class="col-md-6 d-flex justify-content-md-end align-items-center">
            <a href="{{ route('alat.create') }}" class="btn btn-primary shadow-sm px-3 py-2">
                <i class="fas fa-plus-circle me-1"></i> Registrasi Alat Baru
            </a>
        </div>
    </div>

    @if($user->role === 'superadmin')
    <div class="card border-0 shadow-sm mb-4 bg-white p-3">
        <form action="{{ route('alat.index') }}" method="GET" class="row g-2 align-items-center">
            <div class="col-auto">
                <label for="id_cabang" class="text-secondary fw-bold small mb-0">
                    <i class="fas fa-warehouse text-primary me-1"></i> Lokasi Gudang Cabang:
                </label>
            </div>
            <div class="col-md-4">
                <select name="id_cabang" id="id_cabang" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="all" {{ $selectedBranch == 'all' ? 'selected' : '' }}>-- Semua Cabang (Nasional) --</option>
                    @foreach($daftarCabang as $cabang)
                        <option value="{{ $cabang->id }}" {{ $selectedBranch == $cabang->id ? 'selected' : '' }}>{{ $cabang->nama_kota }}</option>
                    @endforeach
                </select>
            </div>
        </form>
    </div>
    @endif

    @if(session('success'))
    <div class="alert alert-success border-0 shadow-sm d-flex align-items-center" role="alert">
        <i class="fas fa-check-circle me-2 fs-5"></i>
        <div>{{ session('success') }}</div>
    </div>
    @endif

    <div class="card border-0 shadow-sm bg-white overflow-hidden">
        <div class="card-header bg-white py-3 border-bottom-0 d-flex justify-content-between align-items-center">
            <h6 class="m-0 fw-bold text-secondary">
                <i class="fas fa-boxes text-muted me-2"></i>Status Kuantitas Logistik Lapangan
            </h6>
        </div>
        
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light text-secondary text-uppercase fs-7" style="letter-spacing: 0.5px;">
                    <tr>
                        <th class="ps-4 py-3" width="5%">No</th>
                        <th width="15%">Cabang Pemilik</th>
                        <th width="25%">Nama Peralatan</th>
                        <th width="15%">Kategori</th>
                        <th width="15%">Tarif / Hari</th>
                        <th width="15%" class="text-center">Stok (Ready / Total)</th>
                        <th width="10%">Kondisi</th>
                    </tr>
                </thead>
                <tbody class="border-top-0">
                    @forelse($dataAlat as $index => $alat)
                    <tr>
                        <td class="ps-4 text-muted fw-bold">{{ $dataAlat->firstItem() + $index }}</td>
                        <td><span class="fw-bold text-dark">{{ $alat->cabang->nama_kota ?? 'Pusat' }}</span></td>
                        <td>
                            <div class="fw-bold text-primary">{{ $alat->nama_alat }}</div>
                            <small class="text-muted fs-7">ID Alat: #ALT-0{{ $alat->id }}</small>
                        </td>
                        <td><span class="badge bg-light text-secondary border">{{ $alat->kategori }}</span></td>
                        <td class="fw-bold text-dark">Rp {{ number_format($alat->harga_sewa_per_hari, 0, ',', '.') }}</td>
                        <td class="text-center">
                            <span class="fw-bold text-success">{{ $alat->stok_tersedia }}</span> 
                            <span class="text-muted">/</span> 
                            <span class="text-secondary fw-bold">{{ $alat->stok_total }} Unit</span>
                        </td>
                        <td>
                            @if($alat->kondisi === 'Bagus')
                                <span class="badge bg-success-light text-success border border-success border-opacity-20 px-2 py-1" style="background-color: #e8f5e9;">Bagus</span>
                            @else
                                <span class="badge bg-danger-light text-danger border border-danger border-opacity-20 px-2 py-1" style="background-color: #ffebee;">Rusak</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="fas fa-box-open fa-3x mb-3 text-light"></i>
                            <p class="mb-0">Belum ada item alat outdoor yang didaftarkan di cabang ini.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($dataAlat->hasPages())
        <div class="card-footer bg-white py-3 border-top d-flex justify-content-between align-items-center">
            <div class="small text-muted">
                Menampilkan {{ $dataAlat->firstItem() }} - {{ $dataAlat->lastItem() }} dari {{ $dataAlat->total() }} unit alat.
            </div>
            <div>
                {{ $dataAlat->appends(request()->query())->links('pagination::bootstrap-5') }}
            </div>
        </div>
        @endif
    </div>
</div>
@endsection