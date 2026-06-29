@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 fw-bold">Manajemen Produk</h1>
            <p class="text-muted">Kelola daftar produk alat outdoor yang tersedia untuk disewa.</p>
        </div>
        <a href="{{ route('produk.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Tambah Produk
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

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th>Kode</th>
                            <th>Nama Produk</th>
                            <th>Harga Sewa (Rp)</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($produks as $produk)
                        <tr>
                            <td><strong>{{ $produk->kode_produk }}</strong></td>
                            <td>{{ $produk->nama_produk }}</td>
                            <td>Rp {{ number_format($produk->harga_sewa, 0, ',', '.') }}</td>
                            <td>
                                <a href="{{ route('produk.edit', $produk->id_produk) }}" class="btn btn-sm btn-warning">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('produk.destroy', $produk->id_produk) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin hapus produk ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-4 text-muted">
                                <i class="fas fa-box-open fa-2x mb-2 d-block opacity-25"></i>
                                Belum ada produk terdaftar.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection