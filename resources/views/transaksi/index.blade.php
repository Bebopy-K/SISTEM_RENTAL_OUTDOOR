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

    {{-- FILTER PENCARIAN & SORTING --}}
    <div class="card border-0 shadow-sm bg-white rounded-3 p-3 mb-4">
        <form method="GET" action="{{ route('transaksi.index') }}" class="row g-2 align-items-end">
            <div class="col-md-4">
                <label class="form-label small text-secondary fw-bold">Cari Transaksi</label>
                <input type="text" name="search" class="form-control form-control-sm" placeholder="ID Transaksi atau Nama Produk" value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label small text-secondary fw-bold">Urutkan Berdasarkan</label>
                <select name="sort_by" class="form-select form-select-sm">
                    <option value="id_transaksi" {{ request('sort_by') == 'id_transaksi' ? 'selected' : '' }}>ID Transaksi</option>
                    <option value="tanggal" {{ request('sort_by') == 'tanggal' ? 'selected' : '' }}>Tanggal</option>
                    <option value="total_harga" {{ request('sort_by') == 'total_harga' ? 'selected' : '' }}>Total Harga</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small text-secondary fw-bold">Urutan</label>
                <select name="sort_order" class="form-select form-select-sm">
                    <option value="desc" {{ request('sort_order') == 'desc' ? 'selected' : '' }}>Terbaru / Terbesar</option>
                    <option value="asc" {{ request('sort_order') == 'asc' ? 'selected' : '' }}>Terlama / Terkecil</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary btn-sm w-100 fw-bold">
                    <i class="fas fa-filter me-1"></i> Terapkan
                </button>
            </div>
            <div class="col-md-2">
                <a href="{{ route('transaksi.index') }}" class="btn btn-secondary btn-sm w-100 fw-bold">
                    <i class="fas fa-undo me-1"></i> Reset
                </a>
            </div>
        </form>
    </div>

    {{-- TABEL TRANSAKSI --}}
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
                            <td class="font-monospace fw-bold text-secondary ps-4">#{{ $t->id_transaksi }}</td>
                            <td class="text-nowrap">
                                <i class="far fa-calendar-alt text-muted me-1"></i>
                                {{ \Carbon\Carbon::parse($t->tanggal)->format('d M Y') }}
                            </td>
                            <td class="fw-bold text-navy">
                                {{ $t->produk->nama_produk ?? 'Produk Tidak Ditemukan' }}
                            </td>
                            <td class="text-center font-monospace">{{ $t->jumlah }} Unit</td>
                            <td class="text-center font-monospace text-secondary">{{ $t->durasi }} Hari</td>
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
                                    <form action="{{ route('transaksi.destroy', $t->id_transaksi) }}" method="POST" onsubmit="return confirm('Yakin hapus transaksi #{{ $t->id_transaksi }}?')" class="d-inline">
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
                                <p class="small text-muted mb-0">Gunakan tombol di atas untuk menginput transaksi pertama.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- PAGINATION CUSTOM (Hanya menampilkan beberapa halaman di sekitar halaman aktif) --}}
        <div class="card-footer bg-white border-0 d-flex flex-column flex-md-row justify-content-between align-items-center py-3 px-4">
            <div class="small text-muted mb-2 mb-md-0">
                Menampilkan 
                <strong>{{ $transaksis->firstItem() ?? 0 }}</strong> 
                sampai 
                <strong>{{ $transaksis->lastItem() ?? 0 }}</strong> 
                dari 
                <strong>{{ $transaksis->total() }}</strong> 
                transaksi
            </div>
            <div>
                @if ($transaksis->hasPages())
                    <nav>
                        <ul class="pagination pagination-sm mb-0">
                            {{-- Previous Page Link --}}
                            @if ($transaksis->onFirstPage())
                                <li class="page-item disabled"><span class="page-link">&laquo;</span></li>
                            @else
                                <li class="page-item">
                                    <a class="page-link" href="{{ $transaksis->previousPageUrl() }}" rel="prev">&laquo;</a>
                                </li>
                            @endif

                            @php
                                // Logika untuk menampilkan hanya beberapa halaman di sekitar halaman aktif
                                $currentPage = $transaksis->currentPage();
                                $lastPage = $transaksis->lastPage();
                                $side = 2; // Jumlah halaman di kiri dan kanan
                                $start = max(1, $currentPage - $side);
                                $end = min($lastPage, $currentPage + $side);
                            @endphp

                            {{-- Link ke halaman pertama --}}
                            @if ($start > 1)
                                <li class="page-item">
                                    <a class="page-link" href="{{ $transaksis->url(1) }}">1</a>
                                </li>
                                @if ($start > 2)
                                    <li class="page-item disabled"><span class="page-link">...</span></li>
                                @endif
                            @endif

                            {{-- Halaman di sekitar halaman aktif --}}
                            @for ($page = $start; $page <= $end; $page++)
                                @if ($page == $currentPage)
                                    <li class="page-item active" aria-current="page"><span class="page-link">{{ $page }}</span></li>
                                @else
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $transaksis->url($page) }}">{{ $page }}</a>
                                    </li>
                                @endif
                            @endfor

                            {{-- Link ke halaman terakhir --}}
                            @if ($end < $lastPage)
                                @if ($end < $lastPage - 1)
                                    <li class="page-item disabled"><span class="page-link">...</span></li>
                                @endif
                                <li class="page-item">
                                    <a class="page-link" href="{{ $transaksis->url($lastPage) }}">{{ $lastPage }}</a>
                                </li>
                            @endif

                            {{-- Next Page Link --}}
                            @if ($transaksis->hasMorePages())
                                <li class="page-item">
                                    <a class="page-link" href="{{ $transaksis->nextPageUrl() }}" rel="next">&raquo;</a>
                                </li>
                            @else
                                <li class="page-item disabled"><span class="page-link">&raquo;</span></li>
                            @endif
                        </ul>
                    </nav>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection