@extends('layouts.app')

@section('title', 'Batch & Stok Masuk')

<style>
    table tbody tr.row-expired td { background-color: #e74c3c !important; color: #fff !important; }
    table tbody tr.row-warning td { background-color: #f0ad4e !important; color: #000 !important; }
</style>

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Batch & Stok Masuk</h4>
    <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#modalTambah">
        <i class="bi bi-plus-lg me-1"></i> Input Stok Masuk
    </button>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <table class="table align-middle">
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>Kode Batch</th>
                    <th>Bahan</th>
                    <th>Qty Awal</th>
                    <th>Qty Sisa</th>
                    <th>Harga Beli</th>
                    <th>Tgl Masuk</th>
                    <th>Tgl Expired</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($batch as $b)
                @php
                    $hariTersisa = (int) \Carbon\Carbon::now()->diffInDays(\Carbon\Carbon::parse($b->tanggal_expired), false);
                    if ($hariTersisa < 0) {
                        $rowClass = 'row-expired';
                    } elseif ($hariTersisa <= 3) {
                        $rowClass = 'row-warning';
                    } else {
                        $rowClass = '';
                    }
                @endphp
                <tr class="{{ $rowClass }}">
                    <td>{{ $loop->iteration }}</td>
                    <td><span class="badge bg-secondary">{{ $b->kode_batch }}</span></td>
                    <td>{{ $b->bahanBaku->nama_bahan }}</td>
                    <td>{{ formatAngka($b->qty_awal) }} {{ $b->bahanBaku->satuan }}</td>
                    <td>{{ formatAngka($b->qty_sisa) }} {{ $b->bahanBaku->satuan }}</td>
                    <td>Rp {{ number_format($b->harga_beli, 0, ',', '.') }} / {{ $b->bahanBaku->satuan }}</td>
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
            <span class="badge" style="background-color:#f39c12; color:#000;">Kuning</span> = mendekati kedaluwarsa (≤3 hari) &nbsp;
            <span class="badge" style="background-color:#c0392b;">Merah</span> = sudah kedaluwarsa
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
                        <input type="number" name="qty_awal" id="qtyAwal" class="form-control" step="0.01" min="0.01" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Cara Input Harga</label>
                        <div class="btn-group w-100 mb-2" role="group">
                            <input type="radio" class="btn-check" name="mode_harga" id="modeHargaTotal" value="total" checked>
                            <label class="btn btn-outline-secondary" for="modeHargaTotal">Harga Total</label>
                            <input type="radio" class="btn-check" name="mode_harga" id="modeHargaSatuan" value="satuan">
                            <label class="btn btn-outline-secondary" for="modeHargaSatuan">Harga Satuan</label>
                        </div>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" name="harga_input" id="hargaInput" class="form-control"
                                step="0.01" min="0" placeholder="Contoh: 350000" required>
                        </div>
                        <small class="text-muted" id="hargaHelpText">
                            Masukkan total harga yang dibayar untuk seluruh jumlah bahan pada batch ini (sesuai nota belanja). Sistem otomatis membagi rata ke harga per satuan.
                        </small>
                        <div class="mt-1 fw-semibold text-primary small" id="hargaPreview" style="display:none;"></div>
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
    const modeHargaRadios = document.querySelectorAll('input[name="mode_harga"]');
    const hargaInput = document.getElementById('hargaInput');
    const qtyAwal = document.getElementById('qtyAwal');
    const hargaHelpText = document.getElementById('hargaHelpText');
    const hargaPreview = document.getElementById('hargaPreview');

    function updateHargaMode() {
        const mode = document.querySelector('input[name="mode_harga"]:checked').value;
        if (mode === 'total') {
            hargaInput.placeholder = 'Contoh: 350000';
            hargaHelpText.textContent = 'Masukkan total harga yang dibayar untuk seluruh jumlah bahan pada batch ini (sesuai nota belanja). Sistem otomatis membagi rata ke harga per satuan.';
        } else {
            hargaInput.placeholder = 'Contoh: 35000';
            hargaHelpText.textContent = 'Masukkan harga beli per satuan bahan (mis. per kg/liter/pcs) untuk batch ini.';
        }
        updatePreview();
    }

    function updatePreview() {
        const mode = document.querySelector('input[name="mode_harga"]:checked').value;
        const harga = parseFloat(hargaInput.value);
        const qty = parseFloat(qtyAwal.value);

        if (mode === 'total' && harga > 0 && qty > 0) {
            const perSatuan = harga / qty;
            hargaPreview.style.display = 'block';
            hargaPreview.textContent = '≈ Rp ' + perSatuan.toLocaleString('id-ID', {maximumFractionDigits: 2}) + ' / satuan';
        } else {
            hargaPreview.style.display = 'none';
        }
    }

    modeHargaRadios.forEach(function(radio) {
        radio.addEventListener('change', updateHargaMode);
    });
    hargaInput.addEventListener('input', updatePreview);
    qtyAwal.addEventListener('input', updatePreview);

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

    document.getElementById('jumlahHari').addEventListener('input', function() {
        const tanggalMasuk = document.querySelector('input[name="tanggal_masuk"]').value;
        if (tanggalMasuk && this.value) {
            const date = new Date(tanggalMasuk);
            date.setDate(date.getDate() + parseInt(this.value));
            document.getElementById('tanggalExpired').value = date.toISOString().split('T')[0];
        }
    });

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