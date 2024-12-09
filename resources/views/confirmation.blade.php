<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Konfirmasi Reservasi</title>
</head>
<body>
    <h1>Konfirmasi Reservasi</h1>
    @method('POST')
    @if ($status === 'success')
        <p style="color: green;">{{ $message }}</p>
    @elseif ($status === 'error')
        <p style="color: red;">{{ $message }}</p>
    @else
        <p>Memverifikasi...</p>
    @endif
</body>
</html>
