<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') — Cipanas Indah</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .sidebar {
            min-height: 100vh;
            background-color: #212529;
            width: 240px;
            position: fixed;
            top: 0;
            left: 0;
            padding-top: 60px;
        }
        .sidebar a {
            color: #adb5bd;
            text-decoration: none;
            display: block;
            padding: 10px 20px;
            font-size: 14px;
        }
        .sidebar a:hover, .sidebar a.active {
            color: white;
            background-color: #e63946;
        }
        .sidebar .sidebar-heading {
            color: #6c757d;
            font-size: 11px;
            text-transform: uppercase;
            padding: 16px 20px 4px;
            letter-spacing: 1px;
        }
        .navbar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 100;
            background-color: #e63946 !important;
        }
        .main-content {
            margin-left: 240px;
            padding: 80px 24px 24px;
        }
    </style>
</head>

{{-- Toast Notifikasi --}}
<div class="position-fixed bottom-0 end-0 p-3" style="z-index: 9999">
    <div id="toast" class="toast align-items-center text-white border-0" role="alert">
        <div class="d-flex">
            <div class="toast-body" id="toast-message"></div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    </div>
</div>

@if(session('success'))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const toast = document.getElementById('toast');
        toast.classList.add('bg-success');
        document.getElementById('toast-message').textContent = "{{ session('success') }}";
        new bootstrap.Toast(toast, { delay: 3000 }).show();
    });
</script>
@endif

@if(session('error'))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const toast = document.getElementById('toast');
        toast.classList.add('bg-danger');
        document.getElementById('toast-message').textContent = "{{ session('error') }}";
        new bootstrap.Toast(toast, { delay: 3000 }).show();
    });
</script>
@endif

@if($errors->any())
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const toast = document.getElementById('toast');
        toast.classList.add('bg-danger');
        document.getElementById('toast-message').textContent = "{{ $errors->first() }}";
        new bootstrap.Toast(toast, { delay: 4000 }).show();
    });
</script>
@endif

<body>

    {{-- Navbar --}}
    <nav class="navbar navbar-dark px-3">
        <span class="navbar-brand fw-bold">Ayam Goreng Cipanas Indah</span>
        <div class="d-flex align-items-center gap-3">

            {{-- Notifikasi Bell --}}
            @if(auth()->user()->role === 'manager')
            <div class="dropdown">
                <button class="btn btn-sm btn-outline-light position-relative" id="notifBtn" data-bs-toggle="dropdown">
                    <i class="bi bi-bell"></i>
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-warning text-dark" id="notif-count" style="display:none"></span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end" style="width:320px; max-height:400px; overflow-y:auto;" id="notif-list">
                    <li class="dropdown-item text-muted text-center">Memuat...</li>
                </ul>
            </div>
            @endif

            <span class="text-white">{{ auth()->user()->nama }}</span>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn btn-sm btn-outline-light">Keluar</button>
            </form>
        </div>
    </nav>

    {{-- Sidebar --}}
    <div class="sidebar">
        @include('layouts.sidebar')
    </div>

    {{-- Konten --}}
    <div class="main-content">
        @yield('content')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    {{-- Polling Notifikasi --}}
    @if(auth()->user()->role === 'manager')
    <script>
        function loadNotifikasi() {
            fetch('/notifikasi/fetch')
                .then(res => res.json())
                .then(data => {
                    const count = data.length;
                    const badge = document.getElementById('notif-count');
                    const list = document.getElementById('notif-list');

                    if (count > 0) {
                        badge.style.display = 'inline';
                        badge.textContent = count;
                    } else {
                        badge.style.display = 'none';
                    }

                    if (count === 0) {
                        list.innerHTML = '<li class="dropdown-item text-muted text-center">Tidak ada notifikasi baru</li>';
                    } else {
                        list.innerHTML = data.map(n => `
                            <li>
                                <a class="dropdown-item" href="/notifikasi/${n.id}/baca">
                                    <div class="fw-bold">${n.judul}</div>
                                    <small class="text-muted">${n.pesan}</small>
                                </a>
                            </li>
                        `).join('<li><hr class="dropdown-divider"></li>');
                    }
                });
        }

        loadNotifikasi();
        setInterval(loadNotifikasi, 30000);
    </script>
    @endif

    @stack('scripts')
</body>
</html>