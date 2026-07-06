@extends('layouts.app')

@section('title', 'Menu')

@section('content')
<h4 class="fw-bold mb-4">Daftar Menu</h4>

<div class="row g-3">
    @forelse($menu as $m)
    <div class="col-md-4">
        <div class="card shadow-sm h-100">
            <div class="card-body">
                <h5 class="fw-bold">{{ $m->nama_menu }}</h5>
                <div class="text-danger fw-bold fs-5">Rp {{ number_format($m->harga, 0, ',', '.') }}</div>
                @if($m->resepDetail->count() > 0)
                <hr>
                <small class="text-muted">Bahan:</small>
                <ul class="mb-0 ps-3">
                    @foreach($m->resepDetail as $r)
                    <li><small>{{ $r->bahanBaku->nama_bahan }} — {{ $r->jumlah }} {{ $r->bahanBaku->satuan }}</small></li>
                    @endforeach
                </ul>
                @endif
            </div>
        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="alert alert-info">Belum ada menu tersedia.</div>
    </div>
    @endforelse
</div>
@endsection