<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') — Cipanas Indah</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,500;9..144,600;9..144,700&family=Plus+Jakarta+Sans:wght@400;500;600;700&family=IBM+Plex+Mono:wght@500&display=swap" rel="stylesheet">
    <style>
        :root {
            --char: #241c1a;
            --char-soft: #322723;
            --ember: #d64933;
            --ember-dark: #b83a27;
            --golden: #e8a33d;
            --cream: #faf6f1;
            --ink: #2a211e;
            --herb: #6b8f52;
            --herb-bg: #eef3e9;
            --ember-bg: #fdeae6;
            --golden-bg: #fdf1de;
            --line: #e8ddd3;
        }

        * { box-sizing: border-box; }

        body {
            background-color: var(--cream);
            color: var(--ink);
            font-family: 'Plus Jakarta Sans', -apple-system, sans-serif;
        }

        h1, h2, h3, h4, h5, h6 {
            font-family: 'Fraunces', Georgia, serif;
            font-weight: 600;
            color: var(--ink);
            letter-spacing: -0.01em;
        }

        code, .font-mono, .badge.bg-secondary {
            font-family: 'IBM Plex Mono', monospace !important;
            font-weight: 500;
        }

        a { color: var(--ember); }
        a:hover { color: var(--ember-dark); }

        /* ===== Sidebar ===== */
        .sidebar {
            min-height: 100vh;
            background-color: var(--char);
            width: 250px;
            position: fixed;
            top: 0;
            left: 0;
            padding-top: 0;
            z-index: 200;
            transition: transform 0.25s ease;
        }

        .sidebar-brand {
            padding: 22px 20px 18px;
            border-bottom: 1px dashed rgba(255,255,255,0.18);
            position: relative;
        }

        .sidebar-brand .brand-name {
            font-family: 'Fraunces', serif;
            font-weight: 700;
            font-size: 17px;
            color: #fff;
            line-height: 1.25;
            margin: 0;
        }

        .sidebar-brand .brand-tag {
            font-size: 11px;
            color: var(--golden);
            text-transform: uppercase;
            letter-spacing: 1.2px;
            font-weight: 600;
        }

        /* Perforated ticket-tear motif under the brand block */
        .sidebar-brand::after {
            content: "";
            position: absolute;
            bottom: -5px;
            left: 0;
            right: 0;
            height: 10px;
            background-image: radial-gradient(circle at 10px 0, var(--cream) 5px, transparent 5.5px);
            background-size: 20px 10px;
            background-repeat: repeat-x;
            opacity: 0.12;
        }

        .sidebar-nav { padding-top: 14px; }

        .sidebar a {
            color: #c9beb8;
            text-decoration: none;
            display: flex;
            align-items: center;
            padding: 10px 20px 10px 17px;
            font-size: 14px;
            font-weight: 500;
            border-left: 3px solid transparent;
            transition: background-color 0.15s ease, color 0.15s ease, border-color 0.15s ease;
        }

        .sidebar a:hover {
            color: #fff;
            background-color: rgba(255,255,255,0.05);
        }

        .sidebar a.active {
            color: #fff;
            background-color: rgba(214,73,51,0.18);
            border-left-color: var(--ember);
        }

        .sidebar .sidebar-heading {
            color: #8a7d76;
            font-size: 10.5px;
            text-transform: uppercase;
            padding: 18px 20px 6px;
            letter-spacing: 1.3px;
            font-weight: 600;
        }

        .sidebar-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.4);
            z-index: 150;
        }

        /* ===== Topbar ===== */
        .navbar {
            position: fixed;
            top: 0;
            left: 250px;
            right: 0;
            z-index: 100;
            background-color: #fff !important;
            border-bottom: 1px solid var(--line);
            padding: 14px 24px;
        }

        .navbar-brand {
            font-family: 'Fraunces', serif;
            font-weight: 600;
            color: var(--ink) !important;
            font-size: 16px;
        }

        .navbar .btn-outline-light {
            border-color: var(--line);
            color: var(--ink);
        }
        .navbar .btn-outline-light:hover {
            background-color: var(--ember);
            border-color: var(--ember);
            color: #fff;
        }
        .navbar .text-white { color: var(--ink) !important; font-weight: 500; }

        #sidebarToggle {
            display: none;
            border: none;
            background: none;
            font-size: 22px;
            color: var(--ink);
            margin-right: 12px;
        }

        /* ===== Main content ===== */
        .main-content {
            margin-left: 250px;
            padding: 92px 26px 26px;
        }

        /* ===== Cards ===== */
        .card {
            border: 1px solid var(--line);
            border-radius: 14px;
        }
        .card-header {
            background-color: #fff;
            border-bottom: 1px dashed var(--line);
            border-radius: 14px 14px 0 0 !important;
            font-family: 'Fraunces', serif;
        }
        .shadow-sm {
            box-shadow: 0 2px 10px rgba(42,33,30,0.06) !important;
        }

        /* ===== Buttons ===== */
        .btn-danger {
            background-color: var(--ember);
            border-color: var(--ember);
        }
        .btn-danger:hover, .btn-danger:focus {
            background-color: var(--ember-dark);
            border-color: var(--ember-dark);
        }
        .btn-outline-secondary { border-color: var(--line); color: var(--ink); }
        .btn { border-radius: 8px; font-weight: 500; }

        /* ===== Tables ===== */
        .table-dark {
            --bs-table-bg: var(--char);
            --bs-table-color: #f2ece7;
        }
        .table thead th {
            font-size: 12.5px;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            font-weight: 600;
        }

        /* ===== Semantic tints (used for status/stock states) ===== */
        .bg-success.bg-opacity-10 { background-color: var(--herb-bg) !important; }
        .text-success { color: var(--herb) !important; }
        .bg-danger.bg-opacity-10 { background-color: var(--ember-bg) !important; }
        .text-danger { color: var(--ember) !important; }
        .bg-warning.bg-opacity-10 { background-color: var(--golden-bg) !important; }
        .text-warning { color: #b8790f !important; }
        .bg-primary.bg-opacity-10 { background-color: #eef0fb !important; }

        @media (max-width: 991.98px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.show { transform: translateX(0); }
            .sidebar.show ~ .sidebar-overlay { display: block; }
            .navbar { left: 0; }
            .main-content { margin-left: 0; }
            #sidebarToggle { display: inline-block; }
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
    <nav class="navbar navbar-light px-3">
        <div class="d-flex align-items-center">
            <button id="sidebarToggle" aria-label="Buka menu">
                <i class="bi bi-list"></i>
            </button>
            <span class="navbar-brand fw-bold mb-0">Ayam Goreng Cipanas Indah</span>
        </div>
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
    <div class="sidebar" id="appSidebar">
        @include('layouts.sidebar')
    </div>
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    {{-- Konten --}}
    <div class="main-content">
        @yield('content')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        const sidebarEl = document.getElementById('appSidebar');
        const overlayEl = document.getElementById('sidebarOverlay');
        const toggleBtn = document.getElementById('sidebarToggle');

        function closeSidebar() {
            sidebarEl.classList.remove('show');
            overlayEl.style.display = 'none';
        }

        toggleBtn?.addEventListener('click', function() {
            sidebarEl.classList.toggle('show');
            overlayEl.style.display = sidebarEl.classList.contains('show') ? 'block' : 'none';
        });
        overlayEl?.addEventListener('click', closeSidebar);
    </script>

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
