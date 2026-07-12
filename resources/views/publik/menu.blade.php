<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu — Ayam Goreng Cipanas Indah</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        * { box-sizing: border-box; }
        body {
            background-color: #f5f5f5;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            max-width: 480px;
            margin: 0 auto;
        }

        /* Header */
        .header {
            background: linear-gradient(135deg, #e63946, #c1121f);
            color: white;
            padding: 20px 16px 40px;
            text-align: center;
            position: relative;
        }
        .header h1 { font-size: 1.3rem; font-weight: 700; margin: 0; }
        .header p { font-size: 0.8rem; opacity: 0.85; margin: 4px 0 0; }

        /* Konten */
        .konten {
            padding: 0 12px 24px;
            margin-top: -20px;
        }

        /* Card menu */
        .menu-card {
            background: white;
            border-radius: 14px;
            margin-bottom: 12px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            display: flex;
            align-items: stretch;
        }
        .menu-card .menu-img {
            width: 100px;
            min-height: 100px;
            object-fit: cover;
            flex-shrink: 0;
        }
        .menu-card .menu-img-placeholder {
            width: 100px;
            min-height: 100px;
            background: #f0f0f0;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            color: #bbb;
            font-size: 1.8rem;
        }
        .menu-card .menu-info {
            padding: 12px 14px;
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .menu-card .menu-nama {
            font-weight: 600;
            font-size: 0.95rem;
            color: #1a1a1a;
            margin-bottom: 4px;
        }
        .menu-card .menu-harga {
            font-weight: 700;
            font-size: 1rem;
            color: #e63946;
            margin-bottom: 6px;
        }
        .badge-tersedia {
            background-color: #d4edda;
            color: #155724;
            font-size: 0.7rem;
            padding: 3px 8px;
            border-radius: 20px;
            display: inline-block;
        }
        .badge-terbatas {
            background-color: #fff3cd;
            color: #856404;
            font-size: 0.7rem;
            padding: 3px 8px;
            border-radius: 20px;
            display: inline-block;
        }
        .badge-habis {
            background-color: #f8d7da;
            color: #721c24;
            font-size: 0.7rem;
            padding: 3px 8px;
            border-radius: 20px;
            display: inline-block;
        }
        .badge-unavail {
            background-color: #e2e3e5;
            color: #383d41;
            font-size: 0.7rem;
            padding: 3px 8px;
            border-radius: 20px;
            display: inline-block;
        }

        /* Section title */
        .section-title {
            font-size: 0.75rem;
            font-weight: 600;
            color: #888;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin: 16px 0 10px;
        }

        /* Footer */
        .footer {
            text-align: center;
            padding: 16px;
            color: #aaa;
            font-size: 0.75rem;
            border-top: 1px solid #eee;
            margin-top: 8px;
        }
    </style>
</head>
<body>

<div class="header">
    <h1>Ayam Goreng Cipanas Indah</h1>
    <p><i class="bi bi-geo-alt-fill"></i> Cipendawa, Cianjur</p>
</div>

<div class="konten">
    <div class="section-title">Daftar Menu</div>

    @forelse($menu as $m)
    <div class="menu-card">
        @if($m->gambar)
            <img src="{{ asset('storage/' . $m->gambar) }}" class="menu-img" alt="{{ $m->nama_menu }}">
        @else
            <div class="menu-img-placeholder">
                <i class="bi bi-image"></i>
            </div>
        @endif
        <div class="menu-info">
            <div class="menu-nama">{{ $m->nama_menu }}</div>
            <div class="menu-harga">Rp {{ number_format($m->harga, 0, ',', '.') }}</div>
        </div>
    </div>
    @empty
    <div style="text-align:center; padding:40px 0; color:#aaa;">
        <i class="bi bi-basket" style="font-size:2rem;"></i>
        <p style="margin-top:8px;">Belum ada menu tersedia</p>
    </div>
    @endforelse
</div>

<div class="footer">
    Menu dapat berubah sewaktu-waktu<br>
    <small>{{ now()->format('d/m/Y H:i') }}</small>
</div>

</body>
</html>