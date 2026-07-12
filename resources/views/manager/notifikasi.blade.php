@extends('layouts.app')

@section('title', 'Notifikasi')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Notifikasi</h4>
    <div class="d-flex gap-2">
        <a href="{{ route('manager.notifikasi.baca-semua') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-check-all me-1"></i> Tandai Semua Dibaca
        </a>
        <a href="{{ route('manager.notifikasi.hapus-semua') }}" class="btn btn-outline-danger btn-sm"
            onclick="return confirm('Hapus semua notifikasi?')">
            <i class="bi bi-trash me-1"></i> Hapus Semua
        </a>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-body p-0">
        @forelse($notifikasi as $n)
        <div class="d-flex align-items-start p-3 border-bottom {{ $n->status === 'belum_dibaca' ? 'bg-light' : '' }}">
            <div class="me-3 mt-1">
                @if($n->tipe === 'stok_minimum')
                    <span class="badge bg-warning text-dark fs-6"><i class="bi bi-exclamation-triangle"></i></span>
                @else
                    <span class="badge bg-danger fs-6"><i class="bi bi-clock"></i></span>
                @endif
            </div>
            <div class="flex-grow-1">
                <div class="fw-bold">{{ $n->judul }}</div>
                <div class="text-muted">{{ $n->pesan }}</div>
                <small class="text-muted">{{ \Carbon\Carbon::parse($n->created_at)->diffForHumans() }}</small>
            </div>
            <div class="d-flex gap-1 ms-2">
                @if($n->status === 'belum_dibaca')
                <a href="{{ route('manager.notifikasi.baca', $n->id) }}" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-check"></i>
                </a>
                @endif
                <a href="{{ route('manager.notifikasi.hapus', $n->id) }}" class="btn btn-sm btn-outline-danger"
                    onclick="return confirm('Hapus notifikasi ini?')">
                    <i class="bi bi-trash"></i>
                </a>
            </div>
        </div>
        @empty
        <div class="text-center text-muted p-4">Tidak ada notifikasi</div>
        @endforelse
    </div>
</div>
@endsection