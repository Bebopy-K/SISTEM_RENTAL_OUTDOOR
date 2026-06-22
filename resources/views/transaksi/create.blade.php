@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="mb-4">
        <a href="{{ route('transaksi.index') }}" class="btn btn-light btn-sm text-secondary border shadow-sm mb-2">
            <i class="fas fa-arrow-left me-1"></i> Kembali ke Riwayat
        </a>
        <h1 class="h3 mb-1 text-dark fw-bold">Tambah Transaksi Baru (Multi-Item)</h1>
        <p class="text-muted mb-0">Pencatatan data kasir beberapa barang sekaligus ke dalam ekosistem database transaksi.</p>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm bg-white p-4 rounded-3">
                <form action="{{ route('transaksi.store') }}" method="POST">
                    @csrf
                    
                    <div id="container-item">
                        <div class="item-baris border-bottom pb-4 mb-4">
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label text-secondary fw-bold small">Cari & Pilih Produk Alat Outdoor</label>
                                    <div class="input-group mb-2">
                                        <span class="input-group-text bg-light text-muted"><i class="fas fa-search"></i></span>
                                        <input type="text" class="form-control form-control-sm pencarian-produk" placeholder="Ketik kode atau nama produk untuk menyaring... (Contoh: TND01 / Tenda)">
                                    </div>
                                    <select name="produk_id[]" class="form-select select-produk" required onchange="hitungMutiItem()">
                                        <option value="" data-harga="0">-- Pilih Produk --</option>
                                        @foreach($produks as $p)
                                            <option value="{{ $p->id_produk }}" data-harga="{{ $p->harga_sewa }}">
                                                {{ $p->kode_produk }} - {{ $p->nama_produk }} (Rp {{ number_format($p->harga_sewa, 0, ',', '.') }}/hari)
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-5">
                                    <label class="form-label text-secondary fw-bold small">Jumlah (Unit)</label>
                                    <input type="number" name="jumlah[]" class="form-control input-jumlah" min="1" value="1" required oninput="hitungMutiItem()">
                                </div>

                                <div class="col-md-5">
                                    <label class="form-label text-secondary fw-bold small">Durasi (Hari)</label>
                                    <input type="number" name="durasi[]" class="form-control input-durasi" min="1" value="1" required oninput="hitungMutiItem()">
                                </div>

                                <div class="col-md-2 d-flex align-items-end justify-content-center">
                                    <button type="button" class="btn btn-outline-danger btn-sm w-100 pemotong-baris" onclick="hapusBaris(this)" disabled>
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <button type="button" class="btn btn-outline-primary btn-sm fw-bold mb-4" onclick="tambahBaris()">
                        <i class="fas fa-plus me-1"></i> Tambah Item Barang Lain
                    </button>

                    <div class="d-flex justify-content-end gap-2 pt-3 border-top">
                        <a href="{{ route('transaksi.index') }}" class="btn btn-light border px-4">Batal</a>
                        <button type="submit" class="btn btn-success px-4 shadow-sm fw-bold">
                            <i class="fas fa-save me-1"></i> Simpan Catatan Transaksi
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm p-4 bg-white rounded-3 sticky-top" style="top: 20px;">
                <h6 class="fw-bold text-dark mb-3"><i class="fas fa-shopping-cart text-primary me-2"></i>Ringkasan Nota</h6>
                <div class="p-3 rounded text-center mb-3" style="background-color: #f8f9fa;">
                    <span class="text-secondary small fw-bold d-block text-uppercase">Total Biaya Masuk:</span>
                    <h3 id="total-grand" class="fw-bold text-success font-monospace mt-1 mb-0">Rp 0</h3>
                </div>
                <p class="small text-muted mb-0">Sistem otomatis memecah data menjadi baris transaksi terpisah di database sesuai arsitektur OLTP Anda.</p>
            </div>
        </div>
    </div>
</div>

<script>
// Fungsi Efek Live Search untuk Dropdown
document.addEventListener('input', function(e) {
    if(e.target && e.target.classList.contains('pencarian-produk')) {
        const kataKunci = e.target.value.toLowerCase();
        const selectElement = e.target.closest('.item-baris').querySelector('.select-produk');
        const options = selectElement.options;

        let focusSet = false;
        for (let i = 0; i < options.length; i++) {
            const teksOption = options[i].text.toLowerCase();
            if (teksOption.includes(kataKunci) || options[i].value === "") {
                options[i].style.display = "";
                if(!focusSet && options[i].value !== "") {
                    // Berikan auto-select ringan pada item pertama yang cocok
                    focusSet = true;
                }
            } else {
                options[i].style.display = "none";
            }
        }
    }
});

function tambahBaris() {
    const container = document.getElementById('container-item');
    const barisBaru = container.querySelector('.item-baris').cloneNode(true);
    
    // Reset nilai inputan di baris baru
    barisBaru.querySelector('.pencarian-produk').value = "";
    barisBaru.querySelector('.select-produk').value = "";
    barisBaru.querySelector('.input-jumlah').value = 1;
    barisBaru.querySelector('.input-durasi').value = 1;
    
    // Tampilkan kembali semua opsi produk pada baris kloningan
    const options = barisBaru.querySelector('.select-produk').options;
    for(let i=0; i<options.length; i++) { options[i].style.display = ""; }

    barisBaru.querySelector('.pemotong-baris').disabled = false;
    container.appendChild(barisBaru);
    hitungMutiItem();
}

function hapusBaris(tombol) {
    tombol.closest('.item-baris').remove();
    hitungMutiItem();
}

function hitungMutiItem() {
    let grandTotal = 0;
    const semuaBaris = document.querySelectorAll('.item-baris');

    semuaBaris.forEach(baris => {
        const select = baris.querySelector('.select-produk');
        const qty = baris.querySelector('.input-jumlah').value;
        const days = baris.querySelector('.input-durasi').value;
        
        const harga = parseFloat(select.options[select.selectedIndex].getAttribute('data-harga')) || 0;
        grandTotal += (parseInt(qty) * parseInt(days) * harga);
    });

    document.getElementById('total-grand').innerText = "Rp " + grandTotal.toLocaleString('id-ID');
}

document.addEventListener("DOMContentLoaded", function() {
    hitungMutiItem();
});
</script>
@endsection