<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservation Summary</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            color: #333;
            background-color: #f9f9f9;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 500px;
            margin: 0 auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .header, .footer {
            text-align: center;
            font-size: 14px;
            color: #888;
        }
        .title {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .details {
            margin: 20px 0;
            line-height: 1.6;
        }
        .details span {
            display: block;
            font-weight: bold;
            color: #555;
        }
        .button {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 15px;
            background-color: #616FE8;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">Reservation Summary</div>

        <div class="details">
            <span>From</span>
            <div>{{ $reservation->date->format('l, d F Y') }} - {{ explode('-', $reservation->time_slot)[0] }}</div>

            <span>To</span>
            <div>{{ $reservation->date->format('l, d F Y') }} - {{ explode('-', $reservation->time_slot)[1] }}</div>

            <span>Total Peminjam</span>
            <div>{{ $reservation->detailReservations->count() }} orang</div>

            <span>Email</span>
            <div>
                @foreach($reservation->detailReservations as $detail)
                    {{ $detail->user->email_mhs }}<br>
                @endforeach
            </div>

            <span>No Meja</span>
            <div>{{ $reservation->table->table_number ?? 'N/A' }}</div>

            <span>Booking ID</span>
            <div{{ $reservation->code }}</div>

            <span>Status</span>
            <div>{{ $reservation->status }}</div>

            <span>Purpose</span>
            <div>{{ $reservation->purpose }}</div>
        </div>

        <div class="footer">Thank you for your reservation!</div>
    </div>
</body>
</html>
