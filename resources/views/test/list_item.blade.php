<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>List Item Produksi</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        .item-card { border:1px solid #ccc; padding:16px; margin-bottom:16px; cursor:pointer; }
        .item-card:hover { background: #f0f0f0; }
    </style>
</head>
<body>
    <h2>Daftar Item Produksi</h2>
    <div id="list-item">
        @foreach($itemProduksi as $item)
            <a href="{{ route('pesanan.detail', $item->id_item_produksi) }}" style="text-decoration:none;color:inherit;">
                <div class="item-card">
                    <strong>{{ $item->nama_item }}</strong><br>
                    Kategori: {{ $item->kategoriUsaha->nama_kategori ?? '-' }}<br>
                    Status: {{ $item->status_aktif }}
                </div>
            </a>
        @endforeach
    </div>
</body>
</html>