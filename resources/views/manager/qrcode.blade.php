@extends('layouts.app')

@section('title', 'QR Code Menu')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">QR Code Menu</h4>
</div>

<div class="card shadow-sm" style="max-width:400px; margin:0 auto;">
    <div class="card-body text-center">
        <p class="text-muted mb-3">Scan QR code ini untuk melihat menu restoran</p>
        <div class="mb-3">
            {!! QrCode::size(250)->generate($url) !!}
        </div>
        <p class="text-muted small mb-3">{{ $url }}</p>
        <a href="{{ $url }}" target="_blank" class="btn btn-outline-danger btn-sm">
            <i class="bi bi-box-arrow-up-right me-1"></i> Buka Halaman Menu
        </a>
    </div>
</div>
@endsection