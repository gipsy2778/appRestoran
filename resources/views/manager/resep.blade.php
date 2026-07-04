@extends('layouts.app')

@section('title', 'Resep')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Resep</h4>
    <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#modalTambah">
        <i class="bi bi-plus-lg me-1"></i> Tambah
    </button>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif

@forelse($menu as $m)
<div class="card shadow-sm mb-3">
    <div class="card-header fw-bold bg-dark text-white">
        {{ $m->nama_menu }}
        <span class="fw-normal text-warning ms-2">Rp {{ number_format($m->harga, 0, ',', '.') }}</span>
    </div>
    <div class="card-body p-0">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-secondary">
                <tr>
                    <th>Bahan</th>
                    <th>Jumlah</th>
                    <th>Satuan</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($m->resepDetail as $detail)
                <tr>
                    <td>{{ $detail->bahanBaku->nama_bahan }}</td>
                    <td>{{ $detail->jumlah }}</td>
                    <td>{{ $detail->bahanBaku->satuan }}</td>
                    <td>
                        <form action="{{ route('manager.resep.destroy', $detail->id) }}" method="POST" class="d-inline"
                            onsubmit="return confirm('Hapus bahan ini dari resep?')">
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
                    <td colspan="4" class="text-center text-muted">Belum ada bahan di resep ini</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@empty
<div class="alert alert-info">Belum ada menu. Tambahkan menu terlebih dahulu.</div>
@endforelse

{{-- Modal Tambah --}}
<div class="modal fade" id="modalTambah" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Resep</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('manager.resep.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Menu</label>
                        <select name="menu_id" class="form-select" required>
                            <option value="">-- Pilih Menu --</option>
                            @foreach($menu as $m)
                                <option value="{{ $m->id }}">{{ $m->nama_menu }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Bahan Baku</label>
                        <select name="bahan_id" class="form-select" required>
                            <option value="">-- Pilih Bahan --</option>
                            @foreach($bahanBaku as $bahan)
                                <option value="{{ $bahan->id }}">{{ $bahan->nama_bahan }} ({{ $bahan->satuan }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Jumlah</label>
                        <input type="number" name="jumlah" class="form-control" step="0.01" min="0" required>
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