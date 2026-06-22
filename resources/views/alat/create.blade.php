@extends('layouts.app')

@section('content')
<div class="container-fluid py-4" style="background-color: #f8f9fa; min-height: 100vh;">
    
    <div class="mb-4">
        <a href="{{ route('alat.index') }}" class="btn btn-light btn-sm text-secondary border shadow-sm mb-2">
            <i class="fas fa-arrow-left me-1"></i> Kembali ke Gudang
        </a>
        <h1 class="h3 mb-1 text-dark fw-bold">Registrasi Alat Outdoor Baru</h1>
        <p class="text-muted mb-0">Tambahkan aset logistik baru ke dalam sistem inventarisasi cabang.</p>
    </div>

    <div class="row">
        <div class="col-lg-8 mb-4">
            <div class="card border-0 shadow-sm bg-white p-4">
                <form action="{{ route('alat.store') }}" method="POST">
                    @csrf
                    
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label for="nama_alat" class="form-label text-secondary fw-bold small">Nama Peralatan Outdoor</label>
                            <input type="text" name="nama_alat" id="nama_alat" class="form-control @error('nama_alat') is-invalid @enderror" placeholder="Contoh: Tenda Eiger Guardian 4P / Carrier Osprey 60L" required>
                            @error('nama_alat')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="kategori" class="form-label text-secondary fw-bold small">Kategori Alat</label>
                            <select name="kategori" id="kategori" class="form-select @error('kategori') is-invalid @enderror" required>
                                <option value="" selected disabled>-- Pilih Kategori --</option>
                                <option value="Tenda">Tenda (Tent)</option>
                                <option value="Tas / Carrier">Tas / Carrier</option>
                                <option value="Perlengkapan Masak">Perlengkapan Masak (Nesting/Stove)</option>
                                <option value="Sleeping Gear">Sleeping Gear (Bag/Matras)</option>
                                <option value="Aksesoris Lampu">Aksesoris & Penerangan (Headlamp/Lantern)</option>
                            </select>
                            @error('kategori')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="id_cabang" class="form-label text-secondary fw-bold small">Lokasi Gudang Penempatan</label>
                            @if($user->role === 'admin_cabang')
                                <select name="id_cabang" id="id_cabang" class="form-select bg-light" required>
                                    @foreach($daftarCabang as $cabang)
                                        <option value="{{ $cabang->id }}" selected>{{ $cabang->nama_kota }}</option>
                                    @endforeach
                                </select>
                            @else
                                <select name="id_cabang" id="id_cabang" class="form-select @error('id_cabang') is-invalid @enderror" required>
                                    <option value="" selected disabled>-- Pilih Gudang Cabang --</option>
                                    @foreach($daftarCabang as $cabang)
                                        <option value="{{ $cabang->id }}">{{ $cabang->nama_kota }}</option>
                                    @endforeach
                                </select>
                            @endif
                        </div>

                        <div class="col-md-6">
                            <label for="harga_sewa_per_hari" class="form-label text-secondary fw-bold small">Tarif Sewa (Per Hari)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-secondary fw-bold">Rp</span>
                                <input type="number" name="harga_sewa_per_hari" id="harga_sewa_per_hari" class="form-control @error('harga_sewa_per_hari') is-invalid @enderror" placeholder="Contoh: 25000" required>
                            </div>
                            @error('harga_sewa_per_hari')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="stok_total" class="form-label text-secondary fw-bold small">Jumlah Unit Masuk (Stok)</label>
                            <div class="input-group">
                                <input type="number" name="stok_total" id="stok_total" class="form-control @error('stok_total') is-invalid @enderror" placeholder="0" min="1" required>
                                <span class="input-group-text bg-light text-secondary">Unit</span>
                            </div>
                            @error('stok_total')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <hr class="my-4 opacity-10">

                    <div class="d-flex justify-content-end gap-2">
                        <button type="reset" class="btn btn-light border px-4">Reset</button>
                        <button type="submit" class="btn btn-primary px-4 shadow-sm">
                            <i class="fas fa-box me-1"></i> Simpan ke Gudang
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm p-4 bg-white mb-4">
                <h6 class="fw-bold text-secondary mb-3">
                    <i class="fas fa-shield-alt text-warning me-2"></i>Aturan Pengkodean Produk
                </h6>
                <p class="small text-muted mb-2">
                    Setiap unit barang yang baru didaftarkan akan otomatis divalidasi oleh sistem backend dengan kondisi awal <span class="badge bg-success-light text-success" style="background-color: #e8f5e9;">Bagus</span>.
                </p>
                <p class="small text-muted mb-0">
                    Nilai <strong>Stok Tersedia</strong> akan disamakan secara *default* dengan <strong>Stok Total</strong> pada saat pendaftaran pertama kali di database operasional.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection