@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">Verifikasi Dua Langkah (2FA)</div>
                <div class="card-body">
                    <p>Kode verifikasi telah dikirim ke email Anda (cek juga log laravel).</p>

                    @if(session('status'))
                        <div class="alert alert-success">{{ session('status') }}</div>
                    @endif

                    <form method="POST" action="{{ route('2fa.verify.submit') }}">
                        @csrf
                        <div class="mb-3">
                            <label>Kode Verifikasi</label>
                            <input type="text" name="code" class="form-control" placeholder="6 digit kode" required autofocus>
                        </div>
                        <button type="submit" class="btn btn-primary">Verifikasi</button>
                    </form>

                    <hr>
                    <form method="POST" action="{{ route('2fa.resend') }}">
                        @csrf
                        <button type="submit" class="btn btn-link">Kirim ulang kode</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection