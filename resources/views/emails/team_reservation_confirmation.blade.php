<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Konfirmasi Reservasi</title>
</head>
<body>
    <h1>Halo, {{ $user->name }}</h1>
    <p>Anda telah diundang untuk bergabung dalam reservasi kelompok.</p>
    <p>Silakan konfirmasi partisipasi Anda dengan mengklik link di bawah ini:</p>
    <a href="{{ $confirmationUrl }}">Konfirmasi Reservasi</a>
    <p>Harap konfirmasi dalam waktu 5 menit untuk menyelesaikan proses reservasi.</p>
</body>
</html>
