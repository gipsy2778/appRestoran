@extends('layouts.app')

@section('title', 'Transaksi Baru')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Transaksi Baru</h4>
    <a href="{{ route('kasir.transaksi.index') }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left me-1"></i> Kembali
    </a>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header fw-bold bg-dark text-white">Pilih Menu</div>
            <div class="card-body">
                <form action="{{ route('kasir.transaksi.store') }}" method="POST" id="formTransaksi">
                    @csrf
                    <div class="row g-3">
                        @foreach($menu as $m)
                        <div class="col-md-6">
                            <div class="card border h-100">
                                <div class="card-body">
                                    <div class="form-check mb-2">
                                        <input class="form-check-input menu-check" type="checkbox"
                                            name="menu[]"
                                            value="{{ $m->id }}"
                                            id="menu{{ $m->id }}"
                                            data-harga="{{ $m->harga }}"
                                            data-nama="{{ $m->nama_menu }}">
                                        <label class="form-check-label fw-bold" for="menu{{ $m->id }}">
                                            {{ $m->nama_menu }}
                                        </label>
                                    </div>
                                    <div class="text-danger fw-bold mb-2">
                                        Rp {{ number_format($m->harga, 0, ',', '.') }}
                                    </div>
                                    <div class="input-group input-group-sm qty-input" style="display:none;">
                                        <button type="button" class="btn btn-outline-secondary btn-minus">-</button>
                                        <input type="number" name="qty[]" class="form-control text-center qty-field"
                                            value="1" min="1">
                                        <button type="button" class="btn btn-outline-secondary btn-plus">+</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Ringkasan --}}
    <div class="col-md-4">
        <div class="card shadow-sm sticky-top" style="top: 80px;">
            <div class="card-header fw-bold bg-dark text-white">Ringkasan Pesanan</div>
            <div class="card-body">
                <div id="ringkasan">
                    <p class="text-muted text-center">Belum ada menu dipilih</p>
                </div>
                <hr>
                <div class="d-flex justify-content-between fw-bold fs-5">
                    <span>Total</span>
                    <span class="text-danger" id="totalHarga">Rp 0</span>
                </div>
                <button type="submit" form="formTransaksi" class="btn btn-danger w-100 mt-3 fw-bold">
                    <i class="bi bi-check-circle me-1"></i> Konfirmasi & Simpan
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function updateRingkasan() {
        const checks = document.querySelectorAll('.menu-check:checked');
        let total = 0;
        let html = '';

        checks.forEach(function(check) {
            const qty = check.closest('.card-body').querySelector('.qty-field').value;
            const harga = parseInt(check.dataset.harga);
            const subtotal = harga * parseInt(qty);
            total += subtotal;
            html += `<div class="d-flex justify-content-between mb-1">
                <span>${check.dataset.nama} x${qty}</span>
                <span>Rp ${subtotal.toLocaleString('id-ID')}</span>
            </div>`;
        });

        document.getElementById('ringkasan').innerHTML = html || '<p class="text-muted text-center">Belum ada menu dipilih</p>';
        document.getElementById('totalHarga').textContent = 'Rp ' + total.toLocaleString('id-ID');
    }

    document.querySelectorAll('.menu-check').forEach(function(check) {
        check.addEventListener('change', function() {
            const qtyInput = this.closest('.card-body').querySelector('.qty-input');
            qtyInput.style.display = this.checked ? 'flex' : 'none';
            updateRingkasan();
        });
    });

    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('btn-minus')) {
            const input = e.target.nextElementSibling;
            if (parseInt(input.value) > 1) {
                input.value = parseInt(input.value) - 1;
                updateRingkasan();
            }
        }
        if (e.target.classList.contains('btn-plus')) {
            const input = e.target.previousElementSibling;
            input.value = parseInt(input.value) + 1;
            updateRingkasan();
        }
        if (e.target.classList.contains('qty-field')) {
            updateRingkasan();
        }
    });
</script>
@endpush