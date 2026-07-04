@extends('layouts.app')

@section('title', 'Batch & Stok Masuk')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Batch & Stok Masuk</h4>
    <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#modalTambah">
        <i class="bi bi-plus-lg me-1"></i> Input Stok Masuk
    </button>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <table class="table table-hover align-middle">
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>Kode Batch</th>
                    <th>Bahan</th>
                    <th>Qty Awal</th>
                    <th>Qty Sisa</th>
                    <th>Tgl Masuk</th>
                    <th>Tgl Expired</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($batch as $b)
                <tr class="{{ Carbon\Carbon::parse($b->tanggal_expired)->diffInDays(now(), false) >= 0 ? 'table-danger' : (Carbon\Carbon::parse($b->tanggal_expired)->diffInDays(now(), false) >= -3 ? 'table-warning' : '') }}">
                    <td>{{ $loop->iteration }}</td>
                    <td><span class="badge bg-secondary">{{ $b->kode_batch }}</span></td>
                    <td>{{ $b->bahanBaku->nama_bahan }}</td>
                    <td>{{ $b->qty_awal }} {{ $b->bahanBaku->satuan }}</td>
                    <td>{{ $b->qty_sisa }} {{ $b->bahanBaku->satuan }}</td>
                    <td>{{ \Carbon\Carbon::parse($b->tanggal_masuk)->format('d/m/Y') }}</td>
                    <td>{{ \Carbon\Carbon::parse($b->tanggal_expired)->format('d/m/Y') }}</td>
                    <td>
                        @if($b->status === 'aktif')
                            <span class="badge bg-success">Aktif</span>
                        @else
                            <span class="badge bg-secondary">Habis</span>
                        @endif
                    </td>
                    <td>
                        <form action="{{ route('manager.batch.destroy', $b->id) }}" method="POST" class="d-inline"
                            onsubmit="return confirm('Hapus batch ini?')">
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
                    <td colspan="9" class="text-center text-muted">Belum ada data batch</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <small class="text-muted">
            <span class="badge bg-warning text-dark">Kuning</span> = mendekati kedaluwarsa (≤3 hari) &nbsp;
            <span class="badge bg-danger">Merah</span> = sudah kedaluwarsa
        </small>
    </div>
</div>

{{-- Modal Tambah --}}
<div class="modal fade" id="modalTambah" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Input Stok Masuk</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('manager.batch.store') }}" method="POST">
                @csrf
                <div class="modal-body">
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
                        <input type="number" name="qty_awal" class="form-control" step="0.01" min="0.01" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tanggal Masuk</label>
                        <input type="date" name="tanggal_masuk" class="form-control"
                            value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tanggal Kedaluwarsa</label>
                        <div class="btn-group w-100 mb-2" role="group">
                            <input type="radio" class="btn-check" name="mode_expired" id="modeTanggal" value="tanggal" checked>
                            <label class="btn btn-outline-secondary" for="modeTanggal">Pilih Tanggal</label>

                            <input type="radio" class="btn-check" name="mode_expired" id="modeHari" value="hari">
                            <label class="btn btn-outline-secondary" for="modeHari">Hitung dari Hari</label>
                        </div>

                        <div id="inputTanggal">
                            <input type="date" name="tanggal_expired" id="tanggalExpired" class="form-control">
                        </div>

                        <div id="inputHari" style="display:none;">
                            <div class="input-group">
                                <input type="number" id="jumlahHari" class="form-control" min="1" placeholder="Contoh: 7">
                                <span class="input-group-text">hari dari tanggal masuk</span>
                            </div>
                        </div>
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
    // Toggle mode expired
    document.querySelectorAll('input[name="mode_expired"]').forEach(function(radio) {
        radio.addEventListener('change', function() {
            if (this.value === 'tanggal') {
                document.getElementById('inputTanggal').style.display = 'block';
                document.getElementById('inputHari').style.display = 'none';
                document.getElementById('tanggalExpired').required = true;
                document.getElementById('jumlahHari').required = false;
            } else {
                document.getElementById('inputTanggal').style.display = 'none';
                document.getElementById('inputHari').style.display = 'block';
                document.getElementById('tanggalExpired').required = false;
                document.getElementById('jumlahHari').required = true;
            }
        });
    });

    // Hitung tanggal expired dari jumlah hari + tanggal masuk
    document.getElementById('jumlahHari').addEventListener('input', function() {
        const tanggalMasuk = document.querySelector('input[name="tanggal_masuk"]').value;
        if (tanggalMasuk && this.value) {
            const date = new Date(tanggalMasuk);
            date.setDate(date.getDate() + parseInt(this.value));
            const hasil = date.toISOString().split('T')[0];
            document.getElementById('tanggalExpired').value = hasil;
        }
    });

    // Kalau tanggal masuk berubah, recalculate jika mode hari aktif
    document.querySelector('input[name="tanggal_masuk"]').addEventListener('change', function() {
        const hari = document.getElementById('jumlahHari').value;
        const modeHari = document.getElementById('modeHari').checked;
        if (modeHari && hari) {
            const date = new Date(this.value);
            date.setDate(date.getDate() + parseInt(hari));
            document.getElementById('tanggalExpired').value = date.toISOString().split('T')[0];
        }
    });
</script>
@endpush