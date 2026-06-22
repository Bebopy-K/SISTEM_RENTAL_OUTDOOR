@extends('layouts.app')

@section('content')
<div class="container py-4">

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
            <div class="d-flex align-items-center">
                <i class="fas fa-check-circle me-2 fs-5"></i>
                <div>{{ session('success') }}</div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h1 class="h3 mb-1 text-dark fw-bold">Riwayat Transaksi Finansial</h1>
            <p class="text-muted mb-0">Manajemen pencatatan data operasional penyewaan alat outdoor tingkat cabang.</p>
        </div>
        <div>
            <a href="{{ route('transaksi.create') }}" class="btn btn-primary fw-bold shadow-sm px-4 py-2">
                <i class="fas fa-plus me-1"></i> Tambah Transaksi Baru
            </a>
        </div>
    </div>

    <div class="card border-0 shadow-sm bg-white rounded-3 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-dark text-white font-monospace small">
                    <tr>
                        <th class="py-3 ps-4" style="width: 80px;">ID TX</th>
                        <th class="py-3">Tanggal</th>
                        <th class="py-3">Nama Produk Alat Outdoor</th>
                        <th class="py-3 text-center" style="width: 100px;">Jumlah</th>
                        <th class="py-3 text-center" style="width: 100px;">Durasi</th>
                        <th class="py-3 text-end" style="width: 150px;">Total Harga</th>
                        <th class="py-3 text-end" style="width: 150px;">Nilai Denda</th>
                        <th class="py-3 text-center" style="width: 140px;">Aksi Operasional</th>
                    </tr>
                </thead>
                <tbody class="small text-dark">
                    @forelse($transaksis as $t)
                        <tr>
                            <td class="font-monospace fw-bold text-secondary ps-4">
                                #{{ $t->id_transaksi }}
                            </td>
                            
                            <td class="text-nowrap">
                                <i class="far fa-calendar-alt text-muted me-1"></i>
                                {{ \Carbon\Carbon::parse($t->tanggal)->format('d M Y') }}
                            </td>
                            
                            <td class="fw-bold text-navy">
                                {{ $t->produk->nama_produk ?? 'Produk Tidak Ditemukan' }}
                            </td>
                            
                            <td class="text-center font-monospace">
                                {{ $t->jumlah }} Unit
                            </td>
                            
                            <td class="text-center font-monospace text-secondary">
                                {{ $t->durasi }} Hari
                            </td>
                            
                            <td class="text-end fw-bold font-monospace text-success">
                                Rp {{ number_format($t->total_harga, 0, ',', '.') }}
                            </td>
                            
                            <td class="text-end fw-bold font-monospace text-danger">
                                @if($t->denda > 0)
                                    Rp {{ number_format($t->denda, 0, ',', '.') }}
                                @else
                                    <span class="text-muted opacity-50">-</span>
                                @endif
                            </td>
                            
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-1">
                                    <a href="{{ route('transaksi.edit', $t->id_transaksi) }}" class="btn btn-sm btn-outline-warning p-2" title="Edit Data">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    
                                    <form action="{{ route('transaksi.destroy', $t->id_transaksi) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus catatan transaksi #{{ $t->id_transaksi }} ini?')" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger p-2" title="Hapus Data">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <div class="mb-2 fs-3 opacity-20"><i class="fas fa-folder-open"></i></div>
                                <h6 class="fw-bold mb-1">Belum Ada Transaksi Tercatat</h6>
                                <p class="small text-muted mb-0">Gunakan tombol di atas untuk menginput baris transaksi operasional pertama.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection