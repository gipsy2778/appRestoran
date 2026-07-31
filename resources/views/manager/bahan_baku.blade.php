@extends('layouts.app')

@section('title', 'Bahan Baku')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Bahan Baku</h4>
    <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#modalTambah">
        <i class="bi bi-plus-lg me-1"></i> Tambah
    </button>
</div>

<div class="card shadow-sm mb-3">
    <div class="card-body">
        <div class="row g-2 align-items-end">
            <div class="col-md-4">
                <label class="form-label small text-muted mb-1">Cari</label>
                <div class="input-group">
                    <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                    <input type="text" id="searchBahan" class="form-control" placeholder="Nama bahan...">
                </div>
            </div>
            <div class="col-md-3">
                <label class="form-label small text-muted mb-1">Satuan</label>
                <select id="filterSatuan" class="form-select">
                    <option value="">Semua Satuan</option>
                    @foreach($bahanBaku->pluck('satuan')->unique()->sort() as $satuan)
                        <option value="{{ strtolower($satuan) }}">{{ ucfirst($satuan) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small text-muted mb-1">Jenis</label>
                <div class="btn-group w-100" role="group">
                    <input type="radio" class="btn-check" name="filterJenis" id="jenisSemua" value="semua" checked>
                    <label class="btn btn-outline-secondary btn-sm" for="jenisSemua">Semua</label>

                    <input type="radio" class="btn-check" name="filterJenis" id="jenisMudahRusak" value="mudah_rusak">
                    <label class="btn btn-outline-secondary btn-sm" for="jenisMudahRusak">Mudah Rusak</label>

                    <input type="radio" class="btn-check" name="filterJenis" id="jenisTahanLama" value="tahan_lama">
                    <label class="btn btn-outline-secondary btn-sm" for="jenisTahanLama">Tahan Lama</label>
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-check mt-2">
                    <input class="form-check-input" type="checkbox" id="filterKritis">
                    <label class="form-check-label small" for="filterKritis">
                        Stok kritis saja
                    </label>
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
                    <th>Nama Bahan</th>
                    <th>Jenis</th>
                    <th>Satuan</th>
                    <th>Stok Saat Ini</th>
                    <th>Stok Minimum</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody id="bahanTableBody">
                @forelse($bahanBaku as $bahan)
                @php $kritis = $bahan->stok_total <= $bahan->stok_minimum; @endphp
                <tr data-nama="{{ strtolower($bahan->nama_bahan) }}"
                    data-satuan="{{ strtolower($bahan->satuan) }}"
                    data-jenis="{{ $bahan->jenis }}"
                    data-kritis="{{ $kritis ? '1' : '0' }}">
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $bahan->nama_bahan }}</td>
                    <td>
                        @if($bahan->jenis === 'mudah_rusak')
                            <span class="badge bg-danger">Mudah Rusak</span>
                        @else
                            <span class="badge bg-success">Tahan Lama</span>
                        @endif
                    </td>
                    <td>{{ $bahan->satuan }}</td>
                    <td>
                        <span class="{{ $kritis ? 'text-danger fw-semibold' : '' }}">
                            {{ formatAngka($bahan->stok_total) }} {{ $bahan->satuan }}
                        </span>
                        @if($kritis)
                            <i class="bi bi-exclamation-triangle-fill text-danger ms-1" title="Stok kritis"></i>
                        @endif
                    </td>
                    <td>{{ formatAngka($bahan->stok_minimum) }} {{ $bahan->satuan }}</td>
                    <td>
                        <button class="btn btn-sm btn-warning"
                            data-bs-toggle="modal"
                            data-bs-target="#modalEdit"
                            data-id="{{ $bahan->id }}"
                            data-nama="{{ $bahan->nama_bahan }}"
                            data-jenis="{{ $bahan->jenis }}"
                            data-satuan="{{ $bahan->satuan }}"
                            data-minimum="{{ $bahan->stok_minimum }}">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <form action="{{ route('manager.bahan-baku.destroy', $bahan->id) }}" method="POST" class="d-inline"
                            onsubmit="return confirm('Hapus bahan ini?')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-danger">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center text-muted">Belum ada data bahan baku</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div id="noResultBahan" class="text-center text-muted py-3" style="display:none;">
            <i class="bi bi-inbox me-1"></i> Tidak ada bahan yang cocok dengan filter ini
        </div>
    </div>
</div>

{{-- Modal Tambah --}}
<div class="modal fade" id="modalTambah" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Bahan Baku</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('manager.bahan-baku.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Bahan</label>
                        <input type="text" name="nama_bahan" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Jenis</label>
                        <select name="jenis" class="form-select" required>
                            <option value="mudah_rusak">Mudah Rusak</option>
                            <option value="tahan_lama">Tahan Lama</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Satuan</label>
                        <select name="satuan" class="form-select" required>
                            <option value="">-- Pilih Satuan --</option>
                            <option value="potong">Potong</option>
                            <option value="kg">Kg</option>
                            <option value="liter">Liter</option>
                            <option value="pcs">Pcs</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Stok Minimum</label>
                        <input type="number" name="stok_minimum" class="form-control" step="0.01" min="0" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Edit --}}
<div class="modal fade" id="modalEdit" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Bahan Baku</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formEdit" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Bahan</label>
                        <input type="text" name="nama_bahan" id="editNama" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Jenis</label>
                        <select name="jenis" id="editJenis" class="form-select" required>
                            <option value="mudah_rusak">Mudah Rusak</option>
                            <option value="tahan_lama">Tahan Lama</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Satuan</label>
                        <select name="satuan" id="editSatuan" class="form-select" required>
                            <option value="">-- Pilih Satuan --</option>
                            <option value="potong">Potong</option>
                            <option value="kg">Kg</option>
                            <option value="liter">Liter</option>
                            <option value="pcs">Pcs</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Stok Minimum</label>
                        <input type="number" name="stok_minimum" id="editMinimum" class="form-control" step="0.01" min="0" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function applyBahanFilter() {
        const keyword = document.getElementById('searchBahan').value.toLowerCase().trim();
        const satuanFilter = document.getElementById('filterSatuan').value;
        const jenisFilter = document.querySelector('input[name="filterJenis"]:checked').value;
        const kritisOnly = document.getElementById('filterKritis').checked;

        const rows = document.querySelectorAll('#bahanTableBody tr[data-nama]');
        let visibleCount = 0;

        rows.forEach(function(row) {
            const nama = row.getAttribute('data-nama');
            const satuan = row.getAttribute('data-satuan');
            const jenis = row.getAttribute('data-jenis');
            const isKritis = row.getAttribute('data-kritis') === '1';

            let show = true;

            if (keyword && !nama.includes(keyword)) show = false;
            if (satuanFilter && satuan !== satuanFilter) show = false;
            if (jenisFilter !== 'semua' && jenis !== jenisFilter) show = false;
            if (kritisOnly && !isKritis) show = false;

            row.style.display = show ? '' : 'none';
            if (show) visibleCount++;
        });

        document.getElementById('noResultBahan').style.display = (visibleCount === 0 && rows.length > 0) ? '' : 'none';
    }

    document.getElementById('searchBahan').addEventListener('input', applyBahanFilter);
    document.getElementById('filterSatuan').addEventListener('change', applyBahanFilter);
    document.getElementById('filterKritis').addEventListener('change', applyBahanFilter);
    document.querySelectorAll('input[name="filterJenis"]').forEach(function(radio) {
        radio.addEventListener('change', applyBahanFilter);
    });

    document.getElementById('modalEdit').addEventListener('show.bs.modal', function(e) {
        const btn = e.relatedTarget;
        document.getElementById('editNama').value = btn.dataset.nama;
        document.getElementById('editJenis').value = btn.dataset.jenis;
        document.getElementById('editSatuan').value = btn.dataset.satuan;
        document.getElementById('editMinimum').value = btn.dataset.minimum;
        document.getElementById('formEdit').action = `/manager/bahan-baku/${btn.dataset.id}`;
    });
</script>
@endpush