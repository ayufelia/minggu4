<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Kasir Retail POS</title>

    <link rel="stylesheet" href="{{ asset('css/kasir.css') }}">
</head>

<body>

<div class="kasir-container">

    {{-- ================= HEADER ================= --}}
    <header class="kasir-header">

        <div class="store-info">

            <div class="store-name">
                TOKO RETAIL MAKMUR
            </div>

            <div class="store-address">
                Jl. Contoh No. 123 • Jember • Telp. 0812-xxxx-xxxx
            </div>

        </div>

        <div class="transaksi-info">

            <div class="transaction-label">
                No. Transaksi
            </div>

            <div
                class="transaction-number"
                id="transactionNumber"
            >
                TRX-20260911-001
            </div>

            <div id="currentDate"></div>

        </div>

    </header>


    {{-- ================= MAIN ================= --}}
    <main class="kasir-content">

        {{-- ================= BAGIAN KIRI ================= --}}
        <section class="kasir-left">

            {{-- ================= TAMBAH BARANG ================= --}}
            <div class="card">

                <div class="card-header">
                    Tambah Barang
                </div>

                <div class="card-body">

                    <div class="search-area">

                        <div class="search-input">

                            <input
                                type="text"
                                id="searchProduct"
                                placeholder="Cari nama barang / kode / barcode..."
                                autocomplete="off"
                            >

                            <div
                                class="product-results"
                                id="productResults"
                            ></div>

                        </div>

                        <button
                            type="button"
                            class="btn btn-primary"
                            onclick="searchProduct()"
                        >
                            Cari
                        </button>

                    </div>


                    {{-- ================= CUSTOMER ================= --}}
                    <div class="customer-area">

                        <div class="form-group">

                            <label for="customer">
                                Pelanggan
                            </label>

                            <select
                                id="customer"
                                class="form-control"
                            >
                                <option value="Umum">
                                    Umum
                                </option>

                                <option value="Pelanggan Member">
                                    Pelanggan Member
                                </option>

                                <option value="Member VIP">
                                    Member VIP
                                </option>
                            </select>

                        </div>


                        <div class="form-group">

                            <label for="memberNumber">
                                No. Member / HP
                            </label>

                            <input
                                type="text"
                                id="memberNumber"
                                class="form-control"
                                placeholder="Opsional"
                            >

                        </div>

                    </div>

                </div>

            </div>


            {{-- ================= KERANJANG ================= --}}
            <div
                class="card"
                style="margin-top: 15px;"
            >

                <div class="card-header">

                    <div class="cart-header">

                        <span>
                            Keranjang Belanja
                        </span>

                        <span id="itemCount">
                            0 Item
                        </span>

                    </div>

                </div>


                <div class="table-wrapper">

                    <table>

                        <thead>

                            <tr>

                                <th width="40">
                                    No
                                </th>

                                <th>
                                    Barang
                                </th>

                                <th width="100">
                                    Harga
                                </th>

                                <th
                                    width="120"
                                    class="text-center"
                                >
                                    Qty
                                </th>

                                <th
                                    width="120"
                                    class="text-right"
                                >
                                    Subtotal
                                </th>

                                <th width="40">
                                </th>

                            </tr>

                        </thead>


                        <tbody id="cartBody">

                            <tr id="emptyRow">

                                <td
                                    colspan="6"
                                    class="empty-cart"
                                >
                                    Keranjang masih kosong.
                                    <br>
                                    Silakan cari atau scan barang.
                                </td>

                            </tr>

                        </tbody>

                    </table>

                </div>

            </div>

        </section>


        {{-- ================= BAGIAN KANAN ================= --}}
        <aside class="kasir-right">

            {{-- ================= RINGKASAN PEMBAYARAN ================= --}}
            <div class="card">

                <div class="card-header">
                    Ringkasan Pembayaran
                </div>


                <div class="card-body">

                    <div class="payment-summary">

                        {{-- TOTAL ITEM --}}
                        <div class="summary-row">

                            <span>
                                Total Item
                            </span>

                            <strong id="totalQty">
                                0
                            </strong>

                        </div>


                        {{-- SUBTOTAL --}}
                        <div class="summary-row">

                            <span>
                                Subtotal
                            </span>

                            <strong id="subtotal">
                                Rp 0
                            </strong>

                        </div>


                        {{-- DISKON PERSEN --}}
                        <div class="summary-row">

                            <span>
                                Diskon (%)
                            </span>

                            <input
                                type="number"
                                id="discountPercent"
                                value="0"
                                min="0"
                                max="100"
                                onchange="calculateTotal()"
                            >

                        </div>


                        {{-- DISKON NOMINAL --}}
                        <div class="summary-row">

                            <span>
                                Diskon (Rp)
                            </span>

                            <input
                                type="number"
                                id="discountAmount"
                                value="0"
                                min="0"
                                onchange="calculateTotal()"
                            >

                        </div>


                        {{-- PAJAK --}}
                        <div class="summary-row">

                            <span>
                                Pajak / PPN
                            </span>

                            <input
                                type="number"
                                id="tax"
                                value="0"
                                min="0"
                                onchange="calculateTotal()"
                            >

                        </div>


                        {{-- BIAYA LAIN --}}
                        <div class="summary-row">

                            <span>
                                Biaya Lain
                            </span>

                            <input
                                type="number"
                                id="otherFee"
                                value="0"
                                min="0"
                                onchange="calculateTotal()"
                            >

                        </div>


                        {{-- TOTAL AKHIR --}}
                        <div class="total-box">

                            <div class="total-label">
                                TOTAL AKHIR
                            </div>

                            <div
                                class="total-value"
                                id="grandTotal"
                            >
                                Rp 0
                            </div>

                        </div>


                        {{-- ================= PAYMENT ================= --}}
                        <div class="payment-box">

                            <div class="payment-label">
                                Uang Dibayar
                            </div>


                            <input
                                type="number"
                                id="payment"
                                class="payment-input"
                                placeholder="0"
                                min="0"
                                oninput="calculateChange()"
                            >


                            {{-- METODE PEMBAYARAN --}}
                            <div
                                class="payment-label"
                                style="margin-top: 15px;"
                            >
                                Metode Pembayaran
                            </div>


                            <div class="payment-method">

                                <button
                                    type="button"
                                    class="active"
                                    onclick="selectPayment(this)"
                                >
                                    Tunai
                                </button>

                                <button
                                    type="button"
                                    onclick="selectPayment(this)"
                                >
                                    QRIS
                                </button>

                                <button
                                    type="button"
                                    onclick="selectPayment(this)"
                                >
                                    Debit
                                </button>

                                <button
                                    type="button"
                                    onclick="selectPayment(this)"
                                >
                                    Kredit
                                </button>

                                <button
                                    type="button"
                                    onclick="selectPayment(this)"
                                >
                                    E-Wallet
                                </button>

                                <button
                                    type="button"
                                    onclick="selectPayment(this)"
                                >
                                    Transfer
                                </button>

                            </div>


                            {{-- KEMBALIAN --}}
                            <div
                                class="change-box"
                                id="changeBox"
                            >

                                <div class="change-label">
                                    KEMBALIAN
                                </div>

                                <div
                                    class="change-value"
                                    id="change"
                                >
                                    Rp 0
                                </div>

                            </div>

                        </div>


                        {{-- ================= ACTION ================= --}}
                        <div class="action-area">

                            <button
                                type="button"
                                class="btn btn-warning"
                                onclick="holdTransaction()"
                            >
                                Tahan
                            </button>


                            <button
                                type="button"
                                class="btn btn-danger"
                                onclick="cancelTransaction()"
                            >
                                Batal
                            </button>


                            <button
                                type="button"
                                class="btn btn-success btn-pay"
                                onclick="processPayment()"
                            >
                                BAYAR & CETAK
                            </button>

                        </div>

                    </div>

                </div>

            </div>

        </aside>

    </main>

</div>


<script src="{{ asset('js/kasir.js') }}"></script>

</body>

</html>