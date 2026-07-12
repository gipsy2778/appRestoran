@extends('layouts.app')

@section('title', 'Bahan Baku')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Bahan Baku</h4>
    <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#modalTambah">
        <i class="bi bi-plus-lg me-1"></i> Tambah
    </button>
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
                    <th>Stok Minimum</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($bahanBaku as $bahan)
                <tr>
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
                    <td colspan="6" class="text-center text-muted">Belum ada data bahan baku</td>
                </tr>
                @endforelse
            </tbody>
        </table>
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