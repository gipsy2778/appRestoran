@extends('layouts.app')

@section('title', 'Menu')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Menu</h4>
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
                    <th>Foto</th>
                    <th>Nama Menu</th>
                    <th>Harga</th>
                    <th>Jumlah Bahan</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($menu as $m)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>
                        @if($m->gambar)
                            <img src="{{ asset('storage/' . $m->gambar) }}"
                                style="width:50px; height:50px; object-fit:cover; border-radius:6px;">
                        @else
                            <div style="width:50px; height:50px; background:#dee2e6; border-radius:6px; display:flex; align-items:center; justify-content:center;">
                                <i class="bi bi-image text-muted"></i>
                            </div>
                        @endif
                    </td>
                    <td>{{ $m->nama_menu }}</td>
                    <td>Rp {{ number_format($m->harga, 0, ',', '.') }}</td>
                    <td>
                        <span class="badge bg-secondary">{{ $m->resepDetail->count() }} bahan</span>
                    </td>
                    <td>
                        {{-- Tombol Resep --}}
                        <button class="btn btn-sm btn-success btn-resep"
                            data-id="{{ $m->id }}"
                            data-nama="{{ $m->nama_menu }}"
                            data-resep="{{ $m->resepDetail->map(fn($r) => ['id' => $r->id, 'bahan_id' => $r->bahan_id, 'jumlah' => $r->jumlah])->toJson() }}">
                            <i class="bi bi-journal-text"></i> Resep
                        </button>
                        {{-- Tombol Edit --}}
                        <button class="btn btn-sm btn-warning"
                            data-bs-toggle="modal"
                            data-bs-target="#modalEdit"
                            data-id="{{ $m->id }}"
                            data-nama="{{ $m->nama_menu }}"
                            data-harga="{{ $m->harga }}"
                            data-gambar="{{ $m->gambar }}">
                            <i class="bi bi-pencil"></i>
                        </button>
                        {{-- Tombol Hapus --}}
                        <form action="{{ route('manager.menu.destroy', $m->id) }}" method="POST" class="d-inline"
                            onsubmit="return confirm('Hapus menu ini?')">
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
                    <td colspan="6" class="text-center text-muted">Belum ada data menu</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Modal Tambah Menu --}}
<div class="modal fade" id="modalTambah" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Menu</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('manager.menu.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Menu</label>
                        <input type="text" name="nama_menu" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Harga</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" name="harga" class="form-control" min="0" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Foto Menu <small class="text-muted">(opsional)</small></label>
                        <input type="file" name="gambar" class="form-control" accept="image/*">
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

{{-- Modal Edit Menu --}}
<div class="modal fade" id="modalEdit" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Menu</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formEdit" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Menu</label>
                        <input type="text" name="nama_menu" id="editNama" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Harga</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" name="harga" id="editHarga" class="form-control" min="0" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Foto Menu <small class="text-muted">(kosongkan jika tidak diubah)</small></label>
                        <div id="editGambarPreview" class="mb-2"></div>
                        <input type="file" name="gambar" class="form-control" accept="image/*">
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

{{-- Modal Resep --}}
<div class="modal fade" id="modalResep" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Resep — <span id="resepNamaMenu"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formResep" method="POST">
                @csrf
                <input type="hidden" name="menu_id" id="resepMenuId">
                <div class="modal-body">
                    <table class="table align-middle" id="tabelResep">
                        <thead class="table-secondary">
                            <tr>
                                <th>Bahan</th>
                                <th style="width:150px">Jumlah</th>
                                <th style="width:60px"></th>
                            </tr>
                        </thead>
                        <tbody id="resepRows">
                            {{-- Diisi oleh JS --}}
                        </tbody>
                    </table>
                    <button type="button" class="btn btn-sm btn-outline-success" id="btnTambahBahan">
                        <i class="bi bi-plus-lg me-1"></i> Tambah bahan
                    </button>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">Simpan semua</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Data bahan baku untuk JS --}}
<script>
    const daftarBahan = @json($bahanBaku);
</script>
@endsection

@push('scripts')
<script>
    // Modal Edit Menu
    document.getElementById('modalEdit').addEventListener('show.bs.modal', function(e) {
        const btn = e.relatedTarget;
        document.getElementById('editNama').value = btn.dataset.nama;
        document.getElementById('editHarga').value = btn.dataset.harga;
        document.getElementById('formEdit').action = `/manager/menu/${btn.dataset.id}`;

        const preview = document.getElementById('editGambarPreview');
        if (btn.dataset.gambar) {
            preview.innerHTML = `<img src="/storage/${btn.dataset.gambar}" style="height:80px; border-radius:6px; object-fit:cover;">`;
        } else {
            preview.innerHTML = '<small class="text-muted">Belum ada foto</small>';
        }
    });

    // Buat baris bahan baru
    function buatBarisBahan(bahanId = '', jumlah = '') {
        const tr = document.createElement('tr');
        const opsi = daftarBahan.map(b =>
            `<option value="${b.id}" ${b.id == bahanId ? 'selected' : ''}>${b.nama_bahan} (${b.satuan})</option>`
        ).join('');

        tr.innerHTML = `
            <td>
                <select name="bahan_id[]" class="form-select form-select-sm" required>
                    <option value="">-- Pilih Bahan --</option>
                    ${opsi}
                </select>
            </td>
            <td>
                <input type="number" name="jumlah[]" class="form-control form-control-sm"
                    value="${jumlah}" step="0.01" min="0.01" required>
            </td>
            <td>
                <button type="button" class="btn btn-sm btn-outline-danger btn-hapus-baris">
                    <i class="bi bi-trash"></i>
                </button>
            </td>
        `;
        return tr;
    }

    // Tombol tambah bahan
    document.getElementById('btnTambahBahan').addEventListener('click', function() {
        document.getElementById('resepRows').appendChild(buatBarisBahan());
    });

    // Hapus baris (delegasi event)
    document.getElementById('resepRows').addEventListener('click', function(e) {
        if (e.target.closest('.btn-hapus-baris')) {
            e.target.closest('tr').remove();
        }
    });

    // Buka modal resep
    document.querySelectorAll('.btn-resep').forEach(function(btn) {
        btn.addEventListener('click', function() {
            const menuId   = this.dataset.id;
            const namaMenu = this.dataset.nama;
            const resep    = JSON.parse(this.dataset.resep);

            document.getElementById('resepNamaMenu').textContent = namaMenu;
            document.getElementById('resepMenuId').value = menuId;
            document.getElementById('formResep').action = `/manager/resep/${menuId}`;

            const tbody = document.getElementById('resepRows');
            tbody.innerHTML = '';
            if (resep.length > 0) {
                resep.forEach(r => tbody.appendChild(buatBarisBahan(r.bahan_id, r.jumlah)));
            } else {
                tbody.appendChild(buatBarisBahan());
            }

            new bootstrap.Modal(document.getElementById('modalResep')).show();
        });
    });
</script>
@endpush