<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Upload Bukti Bayar</title>
</head>
<body>
    <h2>Upload Bukti Bayar</h2>

    @if (session('success'))
        <p style="color: green">{{ session('success') }}</p>
    @endif

    <form method="POST" action="{{ route('pembayaran.upload', $pembayaran->id_pembayaran) }}" enctype="multipart/form-data">
        @csrf
        <input type="file" name="bukti_bayar" required>
        <button type="submit">Upload</button>
    </form>
</body>
</html>