@extends('layouts.app')

@section('title', 'Menu')

@section('content')
<h4 class="fw-bold mb-4">Daftar Menu</h4>

<div class="card shadow-sm">
    <div class="card-body">
        <table class="table table-hover align-middle">
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>Nama Menu</th>
                    <th>Harga</th>
                    <th>Bisa Disajikan</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($menu as $m)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $m->nama_menu }}</td>
                    <td>Rp {{ number_format($m->harga, 0, ',', '.') }}</td>
                    <td>
                        @if($m->maks_porsi > 0)
                            <span class="fw-bold">{{ $m->maks_porsi }} porsi</span>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>
                    <td>
                        @if($m->resepDetail->count() === 0)
                            <span class="badge bg-secondary">Belum ada resep</span>
                        @elseif($m->maks_porsi > 10)
                            <span class="badge bg-success">Tersedia</span>
                        @elseif($m->maks_porsi > 0)
                            <span class="badge bg-warning text-dark">Stok Terbatas</span>
                        @else
                            <span class="badge bg-danger">Habis</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center text-muted">Belum ada menu</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection