@extends('layouts.app')

@section('title', 'Food Wastage')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Food Wastage</h4>
    <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#modalTambah">
        <i class="bi bi-plus-lg me-1"></i> Catat Wastage
    </button>
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
            <tbody>
                @forelse($foodWastage as $fw)
                <tr>
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
                    <td colspan="7" class="text-center text-muted">Belum ada data food wastage</td>
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
                <h5 class="modal-title">Catat Food Wastage</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('manager.food-wastage.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Batch</label>
                        <select name="batch_id" class="form-select" required>
                            <option value="">-- Pilih Batch --</option>
                            @foreach($batch as $b)
                                <option value="{{ $b->id }}">
                                    {{ $b->kode_batch }} — {{ $b->bahanBaku->nama_bahan }}
                                    (sisa: {{ formatAngka($b->qty_sisa) }} {{ $b->bahanBaku->satuan }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Jumlah</label>
                        <input type="number" name="jumlah" class="form-control" step="0.01" min="0.01" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Alasan</label>
                        <select name="alasan" class="form-select" required>
                            <option value="">-- Pilih Alasan --</option>
                            <option value="Busuk">Busuk</option>
                            <option value="Kedaluwarsa">Kedaluwarsa</option>
                            <option value="Rusak fisik">Rusak fisik</option>
                            <option value="Terkontaminasi">Terkontaminasi</option>
                            <option value="Lainnya">Lainnya</option>
                        </select>
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