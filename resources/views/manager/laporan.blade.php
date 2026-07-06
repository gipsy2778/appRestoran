@extends('layouts.app')

@section('title', 'Laporan')

@section('content')
<h4 class="fw-bold mb-4">Laporan</h4>

{{-- Tab --}}
<ul class="nav nav-tabs mb-4" id="laporanTab">
    <li class="nav-item">
        <a class="nav-link active" data-bs-toggle="tab" href="#stok">Stok Saat Ini</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" data-bs-toggle="tab" href="#transaksi">Riwayat Transaksi</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" data-bs-toggle="tab" href="#wastage">Riwayat Food Wastage</a>
    </li>
</ul>

<div class="tab-content">

    {{-- Tab Stok --}}
    <div class="tab-pane fade show active" id="stok">
        <div class="card shadow-sm">
            <div class="card-body">
                <table class="table table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>Bahan</th>
                            <th>Jenis</th>
                            <th>Stok Total</th>
                            <th>Satuan</th>
                            <th>Minimum</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($stok as $s)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $s->nama_bahan }}</td>
                            <td>
                                @if($s->jenis === 'mudah_rusak')
                                    <span class="badge bg-danger">Mudah Rusak</span>
                                @else
                                    <span class="badge bg-success">Tahan Lama</span>
                                @endif
                            </td>
                            <td>{{ $s->stok_total }}</td>
                            <td>{{ $s->satuan }}</td>
                            <td>{{ $s->stok_minimum }}</td>
                            <td>
                                @if($s->stok_total <= $s->stok_minimum)
                                    <span class="badge bg-danger">Stok Menipis</span>
                                @else
                                    <span class="badge bg-success">Aman</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted">Belum ada data</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Tab Transaksi --}}
    <div class="tab-pane fade" id="transaksi">
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
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted">Belum ada transaksi</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Tab Food Wastage --}}
    <div class="tab-pane fade" id="wastage">
        <div class="card shadow-sm">
            <div class="card-body">
                <table class="table table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>Batch</th>
                            <th>Bahan</th>
                            <th>Jumlah</th>
                            <th>Alasan</th>
                            <th>Dicatat Oleh</th>
                            <th>Waktu</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($foodWastage as $fw)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td><span class="badge bg-secondary">{{ $fw->batch->kode_batch }}</span></td>
                            <td>{{ $fw->batch->bahanBaku->nama_bahan }}</td>
                            <td>{{ $fw->jumlah }} {{ $fw->batch->bahanBaku->satuan }}</td>
                            <td>{{ $fw->alasan }}</td>
                            <td>{{ $fw->pelapor->nama }}</td>
                            <td>{{ \Carbon\Carbon::parse($fw->created_at)->format('d/m/Y H:i') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted">Belum ada data wastage</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
@endsection