@extends('layouts.app')

@section('title', 'Dashboard Manager')

@section('content')
<h4 class="fw-bold mb-1">Dashboard</h4>
<p class="text-muted mb-4">Selamat datang kembali, {{ auth()->user()->nama }} 👋</p>

{{-- Kartu Ringkasan --}}
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card shadow-sm h-100">
            <div class="card-body d-flex align-items-center">
                <div class="rounded-circle bg-danger bg-opacity-10 text-danger d-flex align-items-center justify-content-center me-3"
                    style="width:48px;height:48px;">
                    <i class="bi bi-menu-button-wide fs-5"></i>
                </div>
                <div>
                    <div class="text-muted small">Total Menu</div>
                    <div class="fs-5 fw-bold">{{ $totalMenu }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card shadow-sm h-100">
            <div class="card-body d-flex align-items-center">
                <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center me-3"
                    style="width:48px;height:48px;">
                    <i class="bi bi-basket fs-5"></i>
                </div>
                <div>
                    <div class="text-muted small">Total Bahan Baku</div>
                    <div class="fs-5 fw-bold">{{ $totalBahanBaku }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card shadow-sm h-100">
            <div class="card-body d-flex align-items-center">
                <div class="rounded-circle bg-success bg-opacity-10 text-success d-flex align-items-center justify-content-center me-3"
                    style="width:48px;height:48px;">
                    <i class="bi bi-receipt fs-5"></i>
                </div>
                <div>
                    <div class="text-muted small">Transaksi Hari Ini</div>
                    <div class="fs-5 fw-bold">{{ $jumlahTransaksiHariIni }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card shadow-sm h-100">
            <div class="card-body d-flex align-items-center">
                <div class="rounded-circle bg-warning bg-opacity-10 text-warning d-flex align-items-center justify-content-center me-3"
                    style="width:48px;height:48px;">
                    <i class="bi bi-cash-stack fs-5"></i>
                </div>
                <div>
                    <div class="text-muted small">Pendapatan Hari Ini</div>
                    <div class="fs-6 fw-bold">Rp {{ number_format($pendapatanHariIni, 0, ',', '.') }}</div>
                </div>
            </div>
        </div>
    </div>
</div>

@if($notifikasiBelumDibaca > 0)
<div class="alert alert-warning d-flex align-items-center justify-content-between mb-4">
    <div>
        <i class="bi bi-bell-fill me-2"></i>
        Kamu punya <strong>{{ $notifikasiBelumDibaca }}</strong> notifikasi yang belum dibaca.
    </div>
    <a href="{{ route('manager.notifikasi.index') }}" class="btn btn-sm btn-outline-dark">Lihat Notifikasi</a>
</div>
@endif

<div class="row g-3">
    {{-- Stok Kritis --}}
    <div class="col-lg-6">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <strong><i class="bi bi-exclamation-triangle text-danger me-1"></i> Stok Kritis</strong>
                <a href="{{ route('manager.bahan-baku.index') }}" class="small">Lihat semua</a>
            </div>
            <div class="card-body p-0">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>Bahan</th>
                            <th class="text-end">Stok</th>
                            <th class="text-end">Minimum</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($stokKritis as $s)
                        <tr>
                            <td>{{ $s->nama_bahan }}</td>
                            <td class="text-end text-danger fw-semibold">{{ formatAngka($s->stok_total) }} {{ $s->satuan }}</td>
                            <td class="text-end">{{ formatAngka($s->stok_minimum) }} {{ $s->satuan }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="text-center text-muted py-3">
                                <i class="bi bi-check-circle text-success me-1"></i> Semua stok aman
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Batch Mendekati Kedaluwarsa --}}
    <div class="col-lg-6">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <strong><i class="bi bi-hourglass-split text-warning me-1"></i> Mendekati Kedaluwarsa</strong>
                <a href="{{ route('manager.batch.index') }}" class="small">Lihat semua</a>
            </div>
            <div class="card-body p-0">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>Kode Batch</th>
                            <th>Bahan</th>
                            <th class="text-end">Expired</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($batchMendekatiExpired as $b)
                        <tr>
                            <td><span class="badge bg-secondary">{{ $b->kode_batch }}</span></td>
                            <td>{{ $b->bahanBaku->nama_bahan }}</td>
                            <td class="text-end">{{ \Carbon\Carbon::parse($b->tanggal_expired)->format('d/m/Y') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="text-center text-muted py-3">
                                <i class="bi bi-check-circle text-success me-1"></i> Tidak ada batch yang mendekati kedaluwarsa
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
