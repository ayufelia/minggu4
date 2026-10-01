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

        .print-button {
            display: block;
            width: 80mm;
            margin: 15px auto 0;
            padding: 10px;
            border: none;
            background: #198754;
            color: white;
            cursor: pointer;
            font-weight: bold;
        }

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

            .print-button {
                display: none;
            }
        }
    </style>

</head>

<body>

<div class="receipt">

    <div class="store-name">
        TOKO RETAIL MAKMUR
    </div>

    <div class="store-info">
        Jl. Contoh No. 123 • Jember
        <br>
        Telp. 0812-xxxx-xxxx
    </div>


    <div class="line"></div>


    <div class="transaction-info">

        <div>
            No. Transaksi :
            {{ $transaction->transaction_number ?? 'TRX-001' }}
        </div>

        <div>
            Tanggal :
            {{ isset($transaction->created_at)
                ? date('d/m/Y H:i', strtotime($transaction->created_at))
                : date('d/m/Y H:i') }}
        </div>

        <div>
            Kasir :
            {{ $transaction->cashier_name ?? 'Kasir' }}
        </div>

    </div>


    <div class="line"></div>


    {{-- BARANG --}}
    @if(isset($details))

        @foreach($details as $item)

            <div class="item">

                <div class="item-name">
                    {{ $item->product_name }}
                </div>

                <div class="item-detail">

                    <span>
                        {{ $item->qty }}
                        x
                        {{ number_format($item->price, 0, ',', '.') }}
                    </span>

                    <span>
                        Rp
                        {{ number_format($item->subtotal, 0, ',', '.') }}
                    </span>

                </div>

            </div>

        @endforeach

    @endif


    <div class="line"></div>


    {{-- RINGKASAN --}}
    <div class="summary">

        <div class="summary-row">

            <span>
                Subtotal
            </span>

            <span>
                Rp
                {{ number_format($transaction->subtotal ?? 0, 0, ',', '.') }}
            </span>

        </div>


        <div class="summary-row">

            <span>
                Diskon
            </span>

            <span>
                Rp
                {{ number_format($transaction->discount ?? 0, 0, ',', '.') }}
            </span>

        </div>


        <div class="summary-row">

            <span>
                PPN
            </span>

            <span>
                Rp
                {{ number_format($transaction->tax ?? 0, 0, ',', '.') }}
            </span>

        </div>


        <div class="summary-row">

            <span>
                Biaya Lain
            </span>

            <span>
                Rp
                {{ number_format($transaction->other_fee ?? 0, 0, ',', '.') }}
            </span>

        </div>


        <div class="line"></div>


        <div class="summary-row grand-total">

            <span>
                TOTAL
            </span>

            <span>
                Rp
                {{ number_format($transaction->grand_total ?? 0, 0, ',', '.') }}
            </span>

        </div>


        <div class="summary-row">

            <span>
                Bayar
            </span>

            <span>
                Rp
                {{ number_format($transaction->paid_amount ?? 0, 0, ',', '.') }}
            </span>

        </div>


        <div class="summary-row">

            <span>
                Kembali
            </span>

            <span>
                Rp
                {{ number_format($transaction->change_amount ?? 0, 0, ',', '.') }}
            </span>

        </div>


        <div class="summary-row">

            <span>
                Pembayaran
            </span>

            <span>
                {{ $transaction->payment_method ?? 'Tunai' }}
            </span>

        </div>

    </div>


    <div class="line"></div>


    <div class="footer">

        Terima kasih telah berbelanja

        <br>

        di TOKO RETAIL MAKMUR

    </div>

</div>


<button
    class="print-button"
    onclick="window.print()"
>
    CETAK STRUK
</button>


<script>

    window.onload = function () {

        window.print();

    };

</script>

</body>

</html>