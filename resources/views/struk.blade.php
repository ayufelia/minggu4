<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Struk Transaksi</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 20px;
            background: #f2f2f2;
            font-family: Arial, Helvetica, sans-serif;
            color: #000;
        }

        .receipt {
            width: 80mm;
            margin: 0 auto;
            background: #fff;
            padding: 12px;
        }

        .store-name {
            text-align: center;
            font-size: 18px;
            font-weight: bold;
        }

        .store-info {
            text-align: center;
            font-size: 10px;
            line-height: 1.5;
            margin-top: 3px;
        }

        .line {
            border-top: 1px dashed #000;
            margin: 8px 0;
        }

        .transaction-info {
            font-size: 10px;
            line-height: 1.6;
        }

        .item {
            margin-bottom: 7px;
            font-size: 10px;
        }

        .item-name {
            font-weight: bold;
        }

        .item-detail {
            display: flex;
            justify-content: space-between;
            margin-top: 2px;
        }

        .summary {
            font-size: 10px;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            gap: 10px;
            margin: 4px 0;
        }

        .grand-total {
            font-size: 13px;
            font-weight: bold;
            margin-top: 6px;
        }

        .footer {
            text-align: center;
            font-size: 10px;
            margin-top: 12px;
            line-height: 1.5;
        }

        /* Tombol di bawah struk */
        .button-container {
            width: 80mm;
            margin: 15px auto 0;
        }

        .print-button,
        .invoice-button {
            display: block;
            width: 100%;
            margin-bottom: 10px;
            padding: 10px;
            border: none;
            color: white;
            cursor: pointer;
            font-weight: bold;
            text-align: center;
            text-decoration: none;
            font-size: 14px;
            font-family: Arial, Helvetica, sans-serif;
            border-radius: 4px;
        }

        /* Tombol cetak struk */
        .print-button {
            background: #198754;
        }

        .print-button:hover {
            background: #146c43;
        }

        /* Tombol cetak invoice PDF */
        .invoice-button {
            background: #0d6efd;
        }

        .invoice-button:hover {
            background: #0b5ed7;
        }

        /* Tampilan ketika dicetak */
        @media print {
            body {
                padding: 0;
                background: white;
            }

            .receipt {
                width: 80mm;
                margin: 0;
                padding: 5mm;
            }

            .button-container {
                display: none !important;
            }
        }
    </style>
</head>

<body>

    {{-- STRUK TRANSAKSI --}}
    <div class="receipt">

        {{-- Informasi toko --}}
        <div class="store-name">
            TOKO RETAIL MAKMUR
        </div>

        <div class="store-info">
            Jl. Contoh No. 123 • Jember
            <br>
            Telp. 0812-xxxx-xxxx
        </div>

        <div class="line"></div>

        {{-- Informasi transaksi --}}
        <div class="transaction-info">

            <div>
                No. Transaksi:
                {{ $transaction->transaction_number ?? 'TRX-001' }}
            </div>

            <div>
                Tanggal:
                {{ isset($transaction->created_at)
                    ? date('d/m/Y H:i', strtotime($transaction->created_at))
                    : date('d/m/Y H:i') }}
            </div>

            <div>
                Kasir:
                {{ $transaction->cashier_name ?? 'Kasir' }}
            </div>

        </div>

        <div class="line"></div>

        {{-- Daftar barang yang dibeli --}}
        @if(isset($details) && count($details) > 0)

            @foreach($details as $item)

                <div class="item">

                    <div class="item-name">
                        {{ $item->product_name ?? 'Produk' }}
                    </div>

                    <div class="item-detail">

                        <span>
                            {{ $item->qty ?? 0 }}
                            x
                            {{ number_format($item->price ?? 0, 0, ',', '.') }}
                        </span>

                        <span>
                            Rp {{ number_format($item->subtotal ?? 0, 0, ',', '.') }}
                        </span>

                    </div>

                </div>

            @endforeach

        @else

            <div class="item">
                Tidak ada detail barang.
            </div>

        @endif

        <div class="line"></div>

        {{-- Ringkasan pembayaran --}}
        <div class="summary">

            <div class="summary-row">
                <span>Subtotal</span>
                <span>
                    Rp {{ number_format($transaction->subtotal ?? 0, 0, ',', '.') }}
                </span>
            </div>

            <div class="summary-row">
                <span>Diskon</span>
                <span>
                    Rp {{ number_format($transaction->discount ?? 0, 0, ',', '.') }}
                </span>
            </div>

            <div class="summary-row">
                <span>PPN</span>
                <span>
                    Rp {{ number_format($transaction->tax ?? 0, 0, ',', '.') }}
                </span>
            </div>

            <div class="summary-row">
                <span>Biaya Lain</span>
                <span>
                    Rp {{ number_format($transaction->other_fee ?? 0, 0, ',', '.') }}
                </span>
            </div>

            <div class="line"></div>

            <div class="summary-row grand-total">
                <span>TOTAL</span>
                <span>
                    Rp {{ number_format($transaction->grand_total ?? 0, 0, ',', '.') }}
                </span>
            </div>

            <div class="summary-row">
                <span>Bayar</span>
                <span>
                    Rp {{ number_format($transaction->paid_amount ?? 0, 0, ',', '.') }}
                </span>
            </div>

            <div class="summary-row">
                <span>Kembali</span>
                <span>
                    Rp {{ number_format($transaction->change_amount ?? 0, 0, ',', '.') }}
                </span>
            </div>

            <div class="summary-row">
                <span>Pembayaran</span>
                <span>
                    {{ $transaction->payment_method ?? 'Tunai' }}
                </span>
            </div>

        </div>

        <div class="line"></div>

        {{-- Penutup struk --}}
        <div class="footer">
            Terima kasih telah berbelanja
            <br>
            di TOKO RETAIL MAKMUR
        </div>

    </div>

    {{-- TOMBOL CETAK --}}
    <div class="button-container">

        {{-- Cetak struk menggunakan printer --}}
        <button
            type="button"
            class="print-button"
            onclick="window.print()">
            CETAK STRUK
        </button>

        {{-- Unduh invoice dalam format PDF --}}
        <a
            href="{{ route('kasir.invoice', ['id' => $transaction->id]) }}"
            class="invoice-button">
            CETAK INVOICE PDF
        </a>

    </div>

    {{-- Cetak otomatis ketika halaman dibuka --}}
    <script>
        window.onload = function () {
            window.print();
        };
    </script>

</body>
</html>