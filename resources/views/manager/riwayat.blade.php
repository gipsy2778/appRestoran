@extends('layouts.app')

@section('title', 'Riwayat')

@section('content')
<h4 class="fw-bold mb-4">Riwayat</h4>

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
        <div class="card shadow-sm mb-3">
            <div class="card-body">
                <div class="row g-2 align-items-end">
                    <div class="col-md-5">
                        <label class="form-label small text-muted mb-1">Cari</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                            <input type="text" id="searchStok" class="form-control" placeholder="Nama bahan...">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small text-muted mb-1">Jenis</label>
                        <div class="btn-group w-100" role="group">
                            <input type="radio" class="btn-check" name="filterJenisStok" id="jenisStokSemua" value="semua" checked>
                            <label class="btn btn-outline-secondary btn-sm" for="jenisStokSemua">Semua</label>
                            <input type="radio" class="btn-check" name="filterJenisStok" id="jenisStokMudahRusak" value="mudah_rusak">
                            <label class="btn btn-outline-secondary btn-sm" for="jenisStokMudahRusak">Mudah Rusak</label>
                            <input type="radio" class="btn-check" name="filterJenisStok" id="jenisStokTahanLama" value="tahan_lama">
                            <label class="btn btn-outline-secondary btn-sm" for="jenisStokTahanLama">Tahan Lama</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small text-muted mb-1">Status</label>
                        <div class="btn-group w-100" role="group">
                            <input type="radio" class="btn-check" name="filterStatusStok" id="statusStokSemua" value="semua" checked>
                            <label class="btn btn-outline-secondary btn-sm" for="statusStokSemua">Semua</label>
                            <input type="radio" class="btn-check" name="filterStatusStok" id="statusStokAman" value="aman">
                            <label class="btn btn-outline-secondary btn-sm" for="statusStokAman">Aman</label>
                            <input type="radio" class="btn-check" name="filterStatusStok" id="statusStokMenipis" value="menipis">
                            <label class="btn btn-outline-secondary btn-sm" for="statusStokMenipis">Menipis</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

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
                    <tbody id="stokTableBody">
                        @forelse($stok as $s)
                        @php $stokMenipis = $s->stok_total <= $s->stok_minimum; @endphp
                        <tr data-nama="{{ strtolower($s->nama_bahan) }}"
                            data-jenis="{{ $s->jenis }}"
                            data-status="{{ $stokMenipis ? 'menipis' : 'aman' }}">
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $s->nama_bahan }}</td>
                            <td>
                                @if($s->jenis === 'mudah_rusak')
                                    <span class="badge bg-danger">Mudah Rusak</span>
                                @else
                                    <span class="badge bg-success">Tahan Lama</span>
                                @endif
                            </td>
                            <td>{{ formatAngka($s->stok_total) }}</td>
                            <td>{{ $s->satuan }}</td>
                            <td>{{ formatAngka($s->stok_minimum) }}</td>
                            <td>
                                @if($stokMenipis)
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
                <div id="noResultStok" class="text-center text-muted py-3" style="display:none;">
                    <i class="bi bi-inbox me-1"></i> Tidak ada bahan yang cocok dengan filter ini
                </div>
            </div>
        </div>
    </div>

    {{-- Tab Transaksi --}}
    <div class="tab-pane fade" id="transaksi">
        <div class="card shadow-sm mb-3">
            <div class="card-body">
                <div class="row g-2 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label small text-muted mb-1">Cari</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                            <input type="text" id="searchTransaksi" class="form-control" placeholder="Kode transaksi atau nama kasir...">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small text-muted mb-1">Status</label>
                        <div class="btn-group w-100" role="group">
                            <input type="radio" class="btn-check" name="filterStatusTransaksi" id="statusTrxSemua" value="semua" checked>
                            <label class="btn btn-outline-secondary btn-sm" for="statusTrxSemua">Semua</label>
                            <input type="radio" class="btn-check" name="filterStatusTransaksi" id="statusTrxSelesai" value="selesai">
                            <label class="btn btn-outline-secondary btn-sm" for="statusTrxSelesai">Selesai</label>
                            <input type="radio" class="btn-check" name="filterStatusTransaksi" id="statusTrxDibatalkan" value="dibatalkan">
                            <label class="btn btn-outline-secondary btn-sm" for="statusTrxDibatalkan">Dibatalkan</label>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small text-muted mb-1">Dari</label>
                        <input type="date" id="tglDariTransaksi" class="form-control">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small text-muted mb-1">Sampai</label>
                        <input type="date" id="tglSampaiTransaksi" class="form-control">
                    </div>
                    <div class="col-md-1">
                        <button class="btn btn-outline-secondary w-100" onclick="resetFilterTransaksi()" title="Reset filter">
                            <i class="bi bi-x-lg"></i>
                        </button>
                    </div>
                </div>
            </div>
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
                        </tr>
                    </thead>
                    <tbody id="transaksiTableBody">
                        @forelse($transaksi as $t)
                        <tr data-kode="{{ strtolower($t->kode_transaksi) }}"
                            data-kasir="{{ strtolower($t->kasir->nama) }}"
                            data-status="{{ $t->status }}"
                            data-tanggal="{{ \Carbon\Carbon::parse($t->created_at)->format('Y-m-d') }}">
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
                <div id="noResultTransaksi" class="text-center text-muted py-3" style="display:none;">
                    <i class="bi bi-inbox me-1"></i> Tidak ada transaksi yang cocok dengan filter ini
                </div>
            </div>
        </div>
    </div>

    {{-- Tab Food Wastage --}}
    <div class="tab-pane fade" id="wastage">
        <div class="card shadow-sm mb-3">
            <div class="card-body">
                <div class="row g-2 align-items-end">
                    <div class="col-md-5">
                        <label class="form-label small text-muted mb-1">Cari</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                            <input type="text" id="searchWastage" class="form-control" placeholder="Bahan, kode batch, atau alasan...">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small text-muted mb-1">Dari</label>
                        <input type="date" id="tglDariWastage" class="form-control">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small text-muted mb-1">Sampai</label>
                        <input type="date" id="tglSampaiWastage" class="form-control">
                    </div>
                    <div class="col-md-1">
                        <button class="btn btn-outline-secondary w-100" onclick="resetFilterWastage()" title="Reset filter">
                            <i class="bi bi-x-lg"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

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
                    <tbody id="wastageTableBody">
                        @forelse($foodWastage as $fw)
                        <tr data-bahan="{{ strtolower($fw->batch->bahanBaku->nama_bahan) }}"
                            data-kode="{{ strtolower($fw->batch->kode_batch) }}"
                            data-alasan="{{ strtolower($fw->alasan) }}"
                            data-tanggal="{{ \Carbon\Carbon::parse($fw->created_at)->format('Y-m-d') }}">
                            <td>{{ $loop->iteration }}</td>
                            <td><span class="badge bg-secondary">{{ $fw->batch->kode_batch }}</span></td>
                            <td>{{ $fw->batch->bahanBaku->nama_bahan }}</td>
                            <td>{{ formatAngka($fw->jumlah) }} {{ $fw->batch->bahanBaku->satuan }}</td>
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
                <div id="noResultWastage" class="text-center text-muted py-3" style="display:none;">
                    <i class="bi bi-inbox me-1"></i> Tidak ada data yang cocok dengan filter ini
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    // ===== Tab Stok =====
    function applyStokFilter() {
        const keyword = document.getElementById('searchStok').value.toLowerCase().trim();
        const jenisFilter = document.querySelector('input[name="filterJenisStok"]:checked').value;
        const statusFilter = document.querySelector('input[name="filterStatusStok"]:checked').value;

        const rows = document.querySelectorAll('#stokTableBody tr[data-nama]');
        let visible = 0;

        rows.forEach(function(row) {
            let show = true;
            if (keyword && !row.getAttribute('data-nama').includes(keyword)) show = false;
            if (jenisFilter !== 'semua' && row.getAttribute('data-jenis') !== jenisFilter) show = false;
            if (statusFilter !== 'semua' && row.getAttribute('data-status') !== statusFilter) show = false;

            row.style.display = show ? '' : 'none';
            if (show) visible++;
        });

        document.getElementById('noResultStok').style.display = (visible === 0 && rows.length > 0) ? '' : 'none';
    }

    document.getElementById('searchStok').addEventListener('input', applyStokFilter);
    document.querySelectorAll('input[name="filterJenisStok"], input[name="filterStatusStok"]').forEach(function(el) {
        el.addEventListener('change', applyStokFilter);
    });

    // ===== Tab Transaksi =====
    function applyTransaksiFilter() {
        const keyword = document.getElementById('searchTransaksi').value.toLowerCase().trim();
        const statusFilter = document.querySelector('input[name="filterStatusTransaksi"]:checked').value;
        const dari = document.getElementById('tglDariTransaksi').value;
        const sampai = document.getElementById('tglSampaiTransaksi').value;

        const rows = document.querySelectorAll('#transaksiTableBody tr[data-kode]');
        let visible = 0;

        rows.forEach(function(row) {
            let show = true;
            const kode = row.getAttribute('data-kode');
            const kasir = row.getAttribute('data-kasir');
            const tanggal = row.getAttribute('data-tanggal');

            if (keyword && !kode.includes(keyword) && !kasir.includes(keyword)) show = false;
            if (statusFilter !== 'semua' && row.getAttribute('data-status') !== statusFilter) show = false;
            if (dari && tanggal < dari) show = false;
            if (sampai && tanggal > sampai) show = false;

            row.style.display = show ? '' : 'none';
            if (show) visible++;
        });

        document.getElementById('noResultTransaksi').style.display = (visible === 0 && rows.length > 0) ? '' : 'none';
    }

    function resetFilterTransaksi() {
        document.getElementById('searchTransaksi').value = '';
        document.getElementById('statusTrxSemua').checked = true;
        document.getElementById('tglDariTransaksi').value = '';
        document.getElementById('tglSampaiTransaksi').value = '';
        applyTransaksiFilter();
    }

    document.getElementById('searchTransaksi').addEventListener('input', applyTransaksiFilter);
    document.getElementById('tglDariTransaksi').addEventListener('change', applyTransaksiFilter);
    document.getElementById('tglSampaiTransaksi').addEventListener('change', applyTransaksiFilter);
    document.querySelectorAll('input[name="filterStatusTransaksi"]').forEach(function(el) {
        el.addEventListener('change', applyTransaksiFilter);
    });

    // ===== Tab Food Wastage =====
    function applyWastageFilter() {
        const keyword = document.getElementById('searchWastage').value.toLowerCase().trim();
        const dari = document.getElementById('tglDariWastage').value;
        const sampai = document.getElementById('tglSampaiWastage').value;

        const rows = document.querySelectorAll('#wastageTableBody tr[data-bahan]');
        let visible = 0;

        rows.forEach(function(row) {
            let show = true;
            const bahan = row.getAttribute('data-bahan');
            const kode = row.getAttribute('data-kode');
            const alasan = row.getAttribute('data-alasan');
            const tanggal = row.getAttribute('data-tanggal');

            if (keyword && !bahan.includes(keyword) && !kode.includes(keyword) && !alasan.includes(keyword)) show = false;
            if (dari && tanggal < dari) show = false;
            if (sampai && tanggal > sampai) show = false;

            row.style.display = show ? '' : 'none';
            if (show) visible++;
        });

        document.getElementById('noResultWastage').style.display = (visible === 0 && rows.length > 0) ? '' : 'none';
    }

    function resetFilterWastage() {
        document.getElementById('searchWastage').value = '';
        document.getElementById('tglDariWastage').value = '';
        document.getElementById('tglSampaiWastage').value = '';
        applyWastageFilter();
    }

    document.getElementById('searchWastage').addEventListener('input', applyWastageFilter);
    document.getElementById('tglDariWastage').addEventListener('change', applyWastageFilter);
    document.getElementById('tglSampaiWastage').addEventListener('change', applyWastageFilter);
</script>
@endpush
