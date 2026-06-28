<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Sistem Persewaan Outdoor')</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- Custom CSS (opsional) -->
    <style>
        .navbar-brand i { margin-right: 8px; }
        .card-header { font-weight: bold; }
        footer { margin-top: 50px; text-align: center; padding: 20px; background: #f8f9fa; }
        .dropdown-menu { background-color: #343a40; }
        .dropdown-item { color: #fff; }
        .dropdown-item:hover { background-color: #495057; color: #fff; }
    </style>
    @stack('styles')
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="{{ route('dashboard') }}">
                <i class="fas fa-campground"></i> Sewa Outdoor
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    {{-- MENU UNTUK USER YANG SUDAH LOGIN --}}
                    @auth
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('dashboard') }}"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('transaksi.index') }}"><i class="fas fa-exchange-alt"></i> Transaksi</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('transaksi.create') }}"><i class="fas fa-plus-circle"></i> Tambah Transaksi</a>
                        </li>

                        {{-- Laporan OLAP hanya untuk Superadmin & Manager (tidak untuk Staff) --}}
                        @if(Auth::user()->role !== 'staff')
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('olap') }}"><i class="fas fa-chart-line"></i> Laporan OLAP</a>
                        </li>
                        @endif

                        {{-- MASTER DATA (HANYA UNTUK SUPERADMIN) --}}
                        @if(Auth::user()->role === 'superadmin')
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="masterDataDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-cogs"></i> Master Data
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="masterDataDropdown">
                                <li>
                                    <a class="dropdown-item" href="{{ route('cabang.index') }}">
                                        <i class="fas fa-store me-2"></i> Cabang
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('produk.index') }}">
                                        <i class="fas fa-box me-2"></i> Produk
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('users.index') }}">
                                        <i class="fas fa-users me-2"></i> Manajemen User
                                    </a>
                                </li>
                            </ul>
                        </li>
                        @endif

                        <li class="nav-item">
                            <form action="{{ route('logout') }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-link nav-link" style="display: inline; cursor: pointer;">
                                    <i class="fas fa-sign-out-alt"></i> Logout
                                </button>
                            </form>
                        </li>
                    @endauth

                    {{-- MENU UNTUK USER YANG BELUM LOGIN --}}
                    @guest
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}"><i class="fas fa-sign-in-alt"></i> Login</a>
                        </li>
                    @endguest
                </ul>
            </div>
        </div>
    </nav>

    <main class="py-4">
        <div class="container">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @yield('content')
        </div>
    </main>

    <footer>
        <div class="container">
            <p class="text-muted">&copy; {{ date('Y') }} Sistem Persewaan Alat Outdoor - Data Warehouse & OLAP</p>
        </div>
    </footer>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>