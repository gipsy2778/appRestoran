@extends('layouts.app')

@section('title', 'Transaksi')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Transaksi</h4>
    <a href="{{ route('kasir.transaksi.create') }}" class="btn btn-danger">
        <i class="bi bi-plus-lg me-1"></i> Transaksi Baru
    </a>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <table class="table table-hover align-middle">
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>Kode</th>
                    <th>Kasir</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Waktu</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($transaksi as $t)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td><span class="badge bg-secondary">{{ $t->kode_transaksi }}</span></td>
                    <td>{{ $t->kasir->nama }}</td>
                    <td>Rp {{ number_format($t->total_harga, 0, ',', '.') }}</td>
                    <td>
                        @if($t->status === 'selesai')
                            <span class="badge bg-success">Selesai</span>
                        @else
                            <span class="badge bg-danger">Dibatalkan</span>
                        @endif
                    </td>
                    <td>{{ \Carbon\Carbon::parse($t->created_at)->format('d/m/Y H:i') }}</td>
                    <td>
                        <a href="{{ route('kasir.transaksi.struk', $t->id) }}" class="btn btn-sm btn-secondary">
                            <i class="bi bi-receipt"></i>
                        </a>
                        @if($t->status === 'selesai' && \Carbon\Carbon::parse($t->created_at)->diffInMinutes(now()) <= 30)
                        <form action="{{ route('kasir.transaksi.batal', $t->id) }}" method="POST" class="d-inline"
                            onsubmit="return confirm('Batalkan transaksi ini? Stok akan dikembalikan.')">
                            @csrf
                            <button class="btn btn-sm btn-danger">
                                <i class="bi bi-x-circle"></i>
                            </button>
                        </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center text-muted">Belum ada transaksi</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection