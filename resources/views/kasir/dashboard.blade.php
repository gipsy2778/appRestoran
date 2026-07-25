@extends('layouts.app')

@section('title', 'Dashboard Kasir')

@section('content')
<h4 class="fw-bold mb-1">Dashboard</h4>
<p class="text-muted mb-4">Selamat datang, {{ auth()->user()->nama }} 👋</p>

<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card shadow-sm h-100">
            <div class="card-body d-flex align-items-center">
                <div class="rounded-circle bg-success bg-opacity-10 text-success d-flex align-items-center justify-content-center me-3"
                    style="width:48px;height:48px;">
                    <i class="bi bi-receipt fs-5"></i>
                </div>
                <div>
                    <div class="text-muted small">Transaksi Saya Hari Ini</div>
                    <div class="fs-5 fw-bold">{{ $jumlahTransaksiSaya }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card shadow-sm h-100">
            <div class="card-body d-flex align-items-center">
                <div class="rounded-circle bg-warning bg-opacity-10 text-warning d-flex align-items-center justify-content-center me-3"
                    style="width:48px;height:48px;">
                    <i class="bi bi-cash-stack fs-5"></i>
                </div>
                <div>
                    <div class="text-muted small">Pendapatan Saya Hari Ini</div>
                    <div class="fs-6 fw-bold">Rp {{ number_format($pendapatanSaya, 0, ',', '.') }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card shadow-sm h-100 bg-danger text-white">
            <div class="card-body d-flex flex-column justify-content-center align-items-start">
                <div class="mb-2">Mulai transaksi baru</div>
                <a href="{{ route('kasir.transaksi.create') }}" class="btn btn-light btn-sm">
                    <i class="bi bi-plus-lg me-1"></i> Buat Transaksi
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
