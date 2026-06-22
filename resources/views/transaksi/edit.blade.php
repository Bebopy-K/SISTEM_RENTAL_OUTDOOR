@extends('layouts.app')

@section('content')
<div class="container py-4">
    
    <div class="mb-4">
        <a href="{{ route('transaksi.index') }}" class="btn btn-light btn-sm text-secondary border shadow-sm mb-2">
            <i class="fas fa-arrow-left me-1"></i> Batal / Kembali
        </a>
        <h1 class="h3 mb-1 text-dark fw-bold">Ubah Data Transaksi #{{ $transaksi->id_transaksi }}</h1>
        <p class="text-muted mb-0">Lakukan penyesuaian kuantitas, durasi, produk, atau input denda keterlambatan pengembalian.</p>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm bg-white p-4 rounded-3">
                <form method="POST" action="{{ route('transaksi.update', $transaksi->id_transaksi) }}">
                    @csrf
                    @method('PUT')
                    
                    <div class="row g-3">
                        <div class="col-12" id="box-edit-produk">
                            <label class="form-label text-secondary fw-bold small">Cari & Ubah Produk Alat Outdoor</label>
                            <div class="input-group mb-2">
                                <span class="input-group-text bg-light text-muted"><i class="fas fa-search"></i></span>
                                <input type="text" id="cari-produk-edit" class="form-control form-control-sm" placeholder="Ketik kode atau nama produk untuk menyaring... (Contoh: TND / CAM)">
                            </div>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted"><i class="fas fa-box"></i></span>
                                <select name="produk_id" id="produk_id" class="form-select" required onchange="hitungUlangEdit()">
                                    @foreach($produks as $p)
                                        <option value="{{ $p->id_produk }}" data-harga="{{ $p->harga_sewa }}" {{ $transaksi->produk_id == $p->id_produk ? 'selected' : '' }}>
                                            {{ $p->kode_produk }} - {{ $p->nama_produk }} (Rp {{ number_format($p->harga_sewa, 0, ',', '.') }}/hari)
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label text-secondary fw-bold small">Jumlah (Unit)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted"><i class="fas fa-layer-group"></i></span>
                                <input type="number" name="jumlah" id="jumlah" class="form-control" min="1" value="{{ $transaksi->jumlah }}" required oninput="hitungUlangEdit()">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label text-secondary fw-bold small">Durasi Sewa (Hari)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted"><i class="fas fa-clock"></i></span>
                                <input type="number" name="durasi" id="durasi" class="form-control" min="1" value="{{ $transaksi->durasi }}" required oninput="hitungUlangEdit()">
                            </div>
                        </div>

                        <div class="col-12">
                            <label class="form-label text-secondary fw-bold small text-danger">Denda Keterlambatan (Rp)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-danger fw-bold">Rp</span>
                                <input type="number" name="denda" id="denda" class="form-control text-danger fw-bold" min="0" value="{{ $transaksi->denda }}" required oninput="hitungUlangEdit()">
                            </div>
                            <div class="form-text small text-muted">Masukkan angka 0 jika unit kembali tepat waktu tanpa denda.</div>
                        </div>

                        <div class="col-12 mt-4">
                            <div class="p-3 rounded border" style="background-color: #f8f9fa;">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <span class="text-secondary small fw-bold d-block">Subtotal Sewa Baru:</span>
                                        <span id="text_subtotal" class="fw-bold text-dark font-monospace">Rp 0</span>
                                    </div>
                                    <div class="text-end">
                                        <span class="text-secondary small fw-bold d-block">Total Keseluruhan Finansial:</span>
                                        <span id="text_grand_total" class="fw-bold text-primary h4 font-monospace">Rp 0</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4 opacity-10">

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('transaksi.index') }}" class="btn btn-light border px-4">Batal</a>
                        <button type="submit" class="btn btn-primary px-4 shadow-sm fw-bold">
                            <i class="fas fa-save me-1"></i> Perbarui Catatan Transaksi
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm p-4 bg-white rounded-3">
                <h6 class="fw-bold text-dark mb-3">
                    <i class="fas fa-info-circle text-warning me-2"></i>Aturan Mutasi OLTP
                </h6>
                <p class="small text-muted mb-0">
                    Menghitung ulang <code class="text-danger">total_harga</code> secara otomatis di backend untuk menjaga kebersihan data sebelum diekstraksi ke skema bintang Data Warehouse Anda.
                </p>
            </div>
        </div>
    </div>
</div>

<script>
// Fitur Penyaringan Live Search untuk halaman Edit
document.getElementById('cari-produk-edit').addEventListener('input', function(e) {
    const kataKunci = e.target.value.toLowerCase();
    const selectElement = document.getElementById('produk_id');
    const options = selectElement.options;

    for (let i = 0; i < options.length; i++) {
        const teksOption = options[i].text.toLowerCase();
        if (teksOption.includes(kataKunci)) {
            options[i].style.display = "";
        } else {
            options[i].style.display = "none";
        }
    }
});

// Fitur Kalkulator Otomatis Subtotal & Grand Total
function hitungUlangEdit() {
    const produk = document.getElementById('produk_id');
    const jumlah = document.getElementById('jumlah').value;
    const durasi = document.getElementById('durasi').value;
    const dendaInput = document.getElementById('denda').value;

    if(!produk.value || !jumlah || !durasi) return;

    const hargaPerHari = parseFloat(produk.options[produk.selectedIndex].getAttribute('data-harga')) || 0;
    const subtotal = parseInt(jumlah) * parseInt(durasi) * hargaPerHari;
    const denda = dendaInput ? parseInt(dendaInput) : 0;
    const totalSemua = subtotal + denda;

    document.getElementById('text_subtotal').innerText = "Rp " + subtotal.toLocaleString('id-ID');
    document.getElementById('text_grand_total').innerText = "Rp " + totalSemua.toLocaleString('id-ID');
}

// Jalankan kalkulator pertama kali saat halaman dimuat (Mengunci nilai awal dari DB)
document.addEventListener("DOMContentLoaded", function() {
    hitungUlangEdit();
});
</script>
@endsection