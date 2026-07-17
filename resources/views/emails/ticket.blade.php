<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>E-Ticket - AmikomEventHub</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            background-color: #4f46e5;
            margin: 0;
            padding: 40px 20px;
            color: #ffffff;
        }

        .container {
            max-width: 450px;
            margin: 0 auto;
            width: 100%;
        }

        .ticket-card {
            background-color: #ffffff;
            color: #0f172a;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
        }

        .ticket-top {
            background-color: #eef2ff;
            padding: 20px;
            text-align: center;
            border-bottom: 2px dashed #c7d2fe;
        }

        .ticket-top h2 {
            font-size: 20px;
            margin: 0;
            color: #4f46e5;
        }

        .ticket-body {
            padding: 18px;
        }

        .label {
            color: #94a3b8;
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
            margin: 0 0 5px 0;
        }

        .value {
            font-weight: bold;
            font-size: 16px;
            margin: 0;
        }

        .qr-container {
            text-align: center;
            padding: 12px;
            background: #f8fafc;
            border-radius: 12px;
            margin-top: 12px;
        }

        .footer {
            text-align: center;
            padding: 12px;
            color: #94a3b8;
            font-size: 12px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="ticket-card">
            <div class="ticket-top">
                <p class="label">Pembayaran Berhasil!</p>
                <h2>E-Ticket Resmi</h2>
            </div>
            <div class="ticket-body">
                <p class="label">Event</p>
                <p class="value">{{ $transaction->event->title ?? '-' }}</p>

                <p class="label">Nama Pembeli</p>
                <p class="value">{{ $transaction->customer_name }}</p>

                <p class="label">Order ID</p>
                <p class="value">{{ $transaction->order_id }}</p>

                <p class="label">Tanggal & Waktu</p>
                <p class="value">{{ \Carbon\Carbon::parse($transaction->event->date ?? now())->format('d M, H:i') }}</p>

                <div class="qr-container">
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data={{ urlencode($transaction->order_id) }}" alt="QR" />
                    <p class="value" style="margin-top:8px;">{{ $transaction->order_id }}</p>
                </div>
            </div>
        </div>
        <div class="footer">Mohon tunjukkan E-Ticket ini saat memasuki area acara. &copy; {{ date('Y') }} AmikomEventHub.</div>
    </div>
</body>

</html>