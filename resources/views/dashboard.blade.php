<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Dashboard POS Toko</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #f4f6f9;
            color: #333;
        }

        /* HEADER */
        .header {
            background-color: #343a40;
            color: white;
            padding: 25px 40px;
        }

        .header h1 {
            margin: 0;
            font-size: 28px;
        }

        .header p {
            margin: 8px 0 0;
            color: #ddd;
        }

        /* CONTAINER */
        .container {
            padding: 30px 40px;
        }

        /* CARD */
        .cards {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }

        .card {
            background-color: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        }

        .card h3 {
            margin: 0;
            font-size: 18px;
            color: #555;
        }

        .number {
            font-size: 35px;
            font-weight: bold;
            margin-top: 10px;
            color: #343a40;
        }

        /* SECTION */
        .section {
            background-color: white;
            padding: 25px;
            margin-bottom: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        }

        .section h2 {
            margin-top: 0;
            margin-bottom: 20px;
            color: #343a40;
        }

        /* TABLE */
        .table-container {
            width: 100%;
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            background-color: #343a40;
            color: white;
            padding: 12px;
            text-align: left;
        }

        td {
            padding: 12px;
            border-bottom: 1px solid #ddd;
        }

        tr:hover {
            background-color: #f5f5f5;
        }

        /* BADGE */
        .badge {
            display: inline-block;
            background-color: #007bff;
            color: white;
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 13px;
        }

        /* FOOTER */
        .footer {
            text-align: center;
            padding: 20px;
            color: #777;
        }

        /* RESPONSIVE */
        @media (max-width: 768px) {

            .cards {
                grid-template-columns: 1fr;
            }

            .container {
                padding: 20px;
            }

            .header {
                padding: 20px;
            }
        }
    </style>

</head>

<body>

    <!-- HEADER -->
    <div class="header">

        <h1>Dashboard POS Toko</h1>

        <p>
            Sistem Informasi Penjualan Toko
        </p>

    </div>


    <div class="container">

        <!-- ========================= -->
        <!-- CARD RINGKASAN -->
        <!-- ========================= -->

        <div class="cards">

            <div class="card">

                <h3>Total Kategori</h3>

                <div class="number">
                    {{ $categories->count() }}
                </div>

            </div>


            <div class="card">

                <h3>Total Produk</h3>

                <div class="number">
                    {{ $products->count() }}
                </div>

            </div>


            <div class="card">

                <h3>Total Supplier</h3>

                <div class="number">
                    {{ $suppliers->count() }}
                </div>

            </div>

        </div>


        <!-- ========================= -->
        <!-- DATA KATEGORI -->
        <!-- ========================= -->

        <div class="section">

            <h2>Data Kategori</h2>

            <div class="table-container">

                <table>

                    <thead>

                        <tr>

                            <th>No</th>
                            <th>ID</th>
                            <th>Nama Kategori</th>
                            <th>Slug</th>

                        </tr>

                    </thead>


                    <tbody>

                        @foreach ($categories as $index => $category)

                        <tr>

                            <td>
                                {{ $index + 1 }}
                            </td>

                            <td>
                                {{ $category->id }}
                            </td>

                            <td>
                                {{ $category->name }}
                            </td>

                            <td>
                                {{ $category->slug }}
                            </td>

                        </tr>

                        @endforeach

                    </tbody>

                </table>

            </div>

        </div>


        <!-- ========================= -->
        <!-- DATA PRODUK -->
        <!-- ========================= -->

        <div class="section">

            <h2>Data Produk</h2>

            <div class="table-container">

                <table>

                    <thead>

                        <tr>

                            <th>No</th>
                            <th>ID</th>
                            <th>Kategori</th>
                            <th>Nama Produk</th>
                            <th>SKU</th>
                            <th>Harga</th>
                            <th>Stok</th>

                        </tr>

                    </thead>


                    <tbody>

                        @foreach ($products as $index => $product)

                        <tr>

                            <td>
                                {{ $index + 1 }}
                            </td>

                            <td>
                                {{ $product->id }}
                            </td>

                            <td>

                                <span class="badge">
                                    {{ $product->category_name }}
                                </span>

                            </td>

                            <td>
                                {{ $product->name }}
                            </td>

                            <td>
                                {{ $product->sku }}
                            </td>

                            <td>
                                Rp {{ number_format($product->price, 0, ',', '.') }}
                            </td>

                            <td>
                                {{ $product->stock }}
                            </td>

                        </tr>

                        @endforeach

                    </tbody>

                </table>

            </div>

        </div>


        <!-- ========================= -->
        <!-- DATA SUPPLIER -->
        <!-- ========================= -->

        <div class="section">

            <h2>Data Supplier</h2>

            <div class="table-container">

                <table>

                    <thead>

                        <tr>

                            <th>No</th>
                            <th>ID</th>
                            <th>Nama Supplier</th>
                            <th>Telepon</th>
                            <th>Alamat</th>

                        </tr>

                    </thead>


                    <tbody>

                        @foreach ($suppliers as $index => $supplier)

                        <tr>

                            <td>
                                {{ $index + 1 }}
                            </td>

                            <td>
                                {{ $supplier->id }}
                            </td>

                            <td>
                                {{ $supplier->name }}
                            </td>

                            <td>
                                {{ $supplier->phone }}
                            </td>

                            <td>
                                {{ $supplier->address }}
                            </td>

                        </tr>

                        @endforeach

                    </tbody>

                </table>

            </div>

        </div>

    </div>


    <!-- FOOTER -->

    <div class="footer">

        &copy; {{ date('Y') }} POS Toko

    </div>

</body>

</html>