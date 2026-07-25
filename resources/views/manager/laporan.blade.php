@extends('layouts.app')

@section('title', 'Laporan')

@section('content')
<h4 class="fw-bold mb-4">Laporan</h4>

{{-- Filter Periode --}}
<div class="card shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('manager.laporan.index') }}" class="row g-2 align-items-end">
            <div class="col-auto">
                <label class="form-label mb-1">Periode</label>
                <select name="periode" id="periodeSelect" class="form-select" onchange="toggleCustom(this.value)">
                    <option value="hari_ini" {{ $periode === 'hari_ini' ? 'selected' : '' }}>Hari Ini</option>
                    <option value="minggu_ini" {{ $periode === 'minggu_ini' ? 'selected' : '' }}>Minggu Ini</option>
                    <option value="bulan_ini" {{ $periode === 'bulan_ini' ? 'selected' : '' }}>Bulan Ini</option>
                    <option value="semua" {{ $periode === 'semua' ? 'selected' : '' }}>Semua</option>
                    <option value="custom" {{ $periode === 'custom' ? 'selected' : '' }}>Custom</option>
                </select>
            </div>
            <div class="col-auto custom-range" style="{{ $periode === 'custom' ? '' : 'display:none;' }}">
                <label class="form-label mb-1">Dari</label>
                <input type="date" name="dari" class="form-control" value="{{ request('dari') }}">
            </div>
            <div class="col-auto custom-range" style="{{ $periode === 'custom' ? '' : 'display:none;' }}">
                <label class="form-label mb-1">Sampai</label>
                <input type="date" name="sampai" class="form-control" value="{{ request('sampai') }}">
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-danger">
                    <i class="bi bi-filter me-1"></i> Terapkan
                </button>
            </div>
        </form>
        @if($dari && $sampai)
            <small class="text-muted d-block mt-2">
                Menampilkan data: {{ $dari->format('d/m/Y') }} &ndash; {{ $sampai->format('d/m/Y') }}
            </small>
        @else
            <small class="text-muted d-block mt-2">Menampilkan seluruh data yang tercatat</small>
        @endif
    </div>
</div>

{{-- Kartu Ringkasan --}}
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card shadow-sm h-100">
            <div class="card-body">
                <div class="text-muted small mb-1">Total Pendapatan</div>
                <div class="fs-5 fw-bold text-success">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card shadow-sm h-100">
            <div class="card-body">
                <div class="text-muted small mb-1">Total HPP</div>
                <div class="fs-5 fw-bold text-danger">Rp {{ number_format($totalHpp, 0, ',', '.') }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card shadow-sm h-100">
            <div class="card-body">
                <div class="text-muted small mb-1">Estimasi Laba Kotor</div>
                <div class="fs-5 fw-bold {{ $labaKotor >= 0 ? 'text-primary' : 'text-danger' }}">
                    Rp {{ number_format($labaKotor, 0, ',', '.') }}
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card shadow-sm h-100">
            <div class="card-body">
                <div class="text-muted small mb-1">Jumlah Transaksi</div>
                <div class="fs-5 fw-bold">{{ $jumlahTransaksi }}</div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3">
    {{-- Menu Paling Laris --}}
    <div class="col-lg-6">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-white">
                <strong><i class="bi bi-trophy me-1"></i> Menu Paling Laris</strong>
            </div>
            <div class="card-body p-0">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>Menu</th>
                            <th class="text-end">Terjual</th>
                            <th class="text-end">Margin/Porsi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($menuLaris as $m)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $m->nama_menu }}</td>
                            <td class="text-end">{{ formatAngka($m->total_qty) }}</td>
                            <td class="text-end">Rp {{ number_format($m->margin_per_porsi, 0, ',', '.') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted py-3">Belum ada transaksi pada periode ini</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Bahan Paling Sering Dibuang --}}
    <div class="col-lg-6">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-white">
                <strong><i class="bi bi-trash me-1"></i> Bahan Baku Paling Sering Dibuang</strong>
            </div>
            <div class="card-body p-0">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>Bahan</th>
                            <th class="text-end">Terbuang</th>
                            <th class="text-end">Estimasi Kerugian</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($bahanTerbuang as $b)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $b->nama_bahan }}</td>
                            <td class="text-end">{{ formatAngka($b->total_jumlah) }} {{ $b->satuan }}</td>
                            <td class="text-end">Rp {{ number_format($b->total_kerugian, 0, ',', '.') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted py-3">Belum ada food wastage pada periode ini</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function toggleCustom(value) {
        document.querySelectorAll('.custom-range').forEach(function(el) {
            el.style.display = value === 'custom' ? '' : 'none';
        });
    }
</script>
@endpush
