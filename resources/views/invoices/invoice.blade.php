<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Invoice POS Toko</title>

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #333;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .header h1 {
            margin-bottom: 5px;
        }

        .info {
            margin-bottom: 20px;
            line-height: 1.8;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            border: 1px solid #ccc;
            padding: 8px;
        }

        th {
            background-color: #eeeeee;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .total {
            margin-top: 15px;
            text-align: right;
            font-size: 15px;
            font-weight: bold;
        }

        .footer {
            margin-top: 40px;
            text-align: center;
        }
    </style>
</head>
<body>

    <div class="header">
        <h1>POS TOKO</h1>
        <p>INVOICE PEMBELIAN</p>
    </div>

    <div class="info">
        <strong>Nomor Invoice:</strong>
        {{ $invoiceNumber }}<br>

        <strong>Tanggal:</strong>
        {{ $date }}<br>

        <strong>Nama Pelanggan:</strong>
        {{ $customer }}
    </div>

    <table>
        <thead>
            <tr>
                <th>No.</th>
                <th>Nama Produk</th>
                <th>Jumlah</th>
                <th>Harga</th>
                <th>Subtotal</th>
            </tr>
        </thead>

        <tbody>
            @foreach ($items as $index => $item)
                <tr>
                    <td class="text-center">
                        {{ $index + 1 }}
                    </td>

                    <td>{{ $item['name'] }}</td>

                    <td class="text-center">
                        {{ $item['quantity'] }}
                    </td>

                    <td class="text-right">
                        Rp {{ number_format($item['price'], 0, ',', '.') }}
                    </td>

                    <td class="text-right">
                        Rp {{ number_format($item['quantity'] * $item['price'], 0, ',', '.') }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="total">
        Total Pembayaran:
        Rp {{ number_format($total, 0, ',', '.') }}
    </div>

    <div class="footer">
        <p>Terima kasih telah berbelanja!</p>
        <p>POS TOKO</p>
    </div>

</body>
</html>