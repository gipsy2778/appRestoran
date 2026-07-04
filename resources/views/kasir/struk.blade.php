<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Struk - {{ $transaksi->kode_transaksi }}</title>
    <style>
        body { font-family: monospace; max-width: 300px; margin: 0 auto; padding: 20px; }
        .center { text-align: center; }
        .divider { border-top: 1px dashed #000; margin: 8px 0; }
        .row { display: flex; justify-content: space-between; }
        .total { font-weight: bold; font-size: 1.1em; }
    </style>
</head>
<body>
    <div class="center">
        <strong>AYAM GORENG CIPANAS INDAH</strong><br>
        Cipendawa, Cianjur<br>
        <small>{{ \Carbon\Carbon::parse($transaksi->created_at)->format('d/m/Y H:i') }}</small>
    </div>
    <div class="divider"></div>
    <div class="row"><span>No</span><span>{{ $transaksi->kode_transaksi }}</span></div>
    <div class="row"><span>Kasir</span><span>{{ $transaksi->kasir->nama }}</span></div>
    <div class="divider"></div>

    @foreach($transaksi->detail as $d)
    <div>{{ $d->nama_menu }}</div>
    <div class="row">
        <span>{{ $d->qty }} x Rp {{ number_format($d->harga, 0, ',', '.') }}</span>
        <span>Rp {{ number_format($d->subtotal, 0, ',', '.') }}</span>
    </div>
    @endforeach

    <div class="divider"></div>
    <div class="row total">
        <span>TOTAL</span>
        <span>Rp {{ number_format($transaksi->total_harga, 0, ',', '.') }}</span>
    </div>
    <div class="divider"></div>
    <div class="center"><small>Terima kasih!</small></div>

    <br>
    <div style="text-align:center;">
        <button onclick="window.print()" style="padding: 8px 20px; cursor:pointer;">🖨️ Cetak</button>
        <a href="{{ route('kasir.transaksi.index') }}" style="display:block; margin-top:8px; text-align:center;">← Kembali</a>
    </div>
</body>
</html>