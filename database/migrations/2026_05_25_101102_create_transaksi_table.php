<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('transaksi', function (Blueprint $table) {
            $table->id('id_transaksi');
            $table->date('tanggal');
            $table->foreignId('produk_id')->constrained('produk', 'id_produk');
            $table->integer('jumlah');
            $table->integer('durasi');
            $table->foreignId('cabang_id')->constrained('cabang', 'id_cabang');
            $table->foreignId('user_id')->constrained('users', 'id_user');
            $table->integer('total_harga');
            $table->integer('denda')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaksi');
    }
};
