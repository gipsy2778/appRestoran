@php $role = auth()->user()->role; @endphp

@if($role === 'manager')
    <div class="sidebar-heading">Master Data</div>
    <a href="{{ route('manager.bahan-baku.index') }}" class="{{ request()->routeIs('manager.bahan-baku.*') ? 'active' : '' }}">
        <i class="bi bi-basket me-2"></i> Bahan Baku
    </a>
    <a href="{{ route('manager.menu.index') }}" class="{{ request()->routeIs('manager.menu.*') ? 'active' : '' }}">
        <i class="bi bi-menu-button-wide me-2"></i> Menu
    </a>
    <a href="{{ route('manager.pengguna.index') }}" class="{{ request()->routeIs('manager.pengguna.*') ? 'active' : '' }}">
        <i class="bi bi-people me-2"></i> Pengguna
    </a>

    <div class="sidebar-heading">Stok</div>
    <a href="{{ route('manager.batch.index') }}" class="{{ request()->routeIs('manager.batch.*') ? 'active' : '' }}">
        <i class="bi bi-box-seam me-2"></i> Batch & Stok Masuk
    </a>
    <a href="{{ route('manager.food-wastage.index') }}" class="{{ request()->routeIs('manager.food-wastage.*') ? 'active' : '' }}">
        <i class="bi bi-trash me-2"></i> Food Wastage
    </a>

    <div class="sidebar-heading">Monitoring</div>
    <a href="{{ route('manager.notifikasi.index') }}" class="{{ request()->routeIs('manager.notifikasi.*') ? 'active' : '' }}">
        <i class="bi bi-bell me-2"></i> Notifikasi
    </a>
    <a href="{{ route('manager.laporan.index') }}" class="{{ request()->routeIs('manager.laporan.*') ? 'active' : '' }}">
        <i class="bi bi-bar-chart me-2"></i> Laporan
    </a>
    <a href="{{ route('manager.qrcode') }}" class="{{ request()->routeIs('manager.qrcode') ? 'active' : '' }}">
        <i class="bi bi-qr-code me-2"></i> QR Code Menu
    </a>

@elseif($role === 'kasir')
    <div class="sidebar-heading">Transaksi</div>
    <a href="{{ route('kasir.transaksi.index') }}" class="{{ request()->routeIs('kasir.transaksi.*') ? 'active' : '' }}">
        <i class="bi bi-receipt me-2"></i> Transaksi
    </a>

    <div class="sidebar-heading">Informasi</div>
    <a href="{{ route('kasir.menu.index') }}" class="{{ request()->routeIs('kasir.menu.*') ? 'active' : '' }}">
        <i class="bi bi-menu-button-wide me-2"></i> Menu
    </a>
@endif