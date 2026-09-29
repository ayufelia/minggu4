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

        .container {
            padding: 30px 40px;
        }

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

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .section-header h2 {
            margin: 0;
        }

        .btn {
            display: inline-block;
            padding: 10px 15px;
            border-radius: 5px;
            text-decoration: none;
            color: white;
            border: none;
            cursor: pointer;
        }

        .btn-primary {
            background-color: #007bff;
        }

        .btn-primary:hover {
            background-color: #0056b3;
        }

        .btn-danger {
            background-color: #dc3545;
        }

        .btn-danger:hover {
            background-color: #bd2130;
        }

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

        .badge {
            display: inline-block;
            background-color: #007bff;
            color: white;
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 13px;
        }

        .footer {
            text-align: center;
            padding: 20px;
            color: #777;
        }

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

            .section-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }

        }

    </style>

</head>

<body>

    <!-- Header -->

    <div class="header">

        <div style="display: flex; justify-content: space-between; align-items: center;">

            <div>

                <h1>Dashboard POS Toko</h1>

                <p>
                    Sistem Informasi Penjualan Toko
                </p>

            </div>

            <form action="{{ route('logout') }}" method="POST">

                @csrf

                <button type="submit"
                        class="btn btn-danger">

                    Logout

                </button>

            </form>

        </div>

    </div>


    <div class="container">


        <!-- Card Ringkasan -->

        <div class="cards">

            <div class="card">

                <h3>
                    Total Kategori
                </h3>

                <div class="number">

                    {{ $categories->count() }}

                </div>

            </div>


            <div class="card">

                <h3>
                    Total Produk
                </h3>

                <div class="number">

                    {{ $products->count() }}

                </div>

            </div>


            <div class="card">

                <h3>
                    Total Supplier
                </h3>

                <div class="number">

                    {{ $suppliers->count() }}

                </div>

            </div>

        </div>


        <!-- Data Kategori -->

        <div class="section">

            <h2>
                Data Kategori
            </h2>

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

                        @forelse ($categories as $index => $category)

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

                        @empty

                            <tr>

                                <td colspan="4"
                                    style="text-align: center;">

                                    Belum ada data kategori.

                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>


        <!-- Data Produk -->

        <div class="section">

            <div class="section-header">

                <h2>
                    Data Produk
                </h2>

                <a href="{{ route('products.index') }}"
                   class="btn btn-primary">

                    Kelola Produk

                </a>

            </div>


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

                        @forelse ($products as $index => $product)

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

                                    Rp
                                    {{ number_format($product->price, 0, ',', '.') }}

                                </td>

                                <td>
                                    {{ $product->stock }}
                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="7"
                                    style="text-align: center;">

                                    Belum ada data produk.

                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>


        <!-- Data Supplier -->

        <div class="section">

            <h2>
                Data Supplier
            </h2>

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

                        @forelse ($suppliers as $index => $supplier)

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

                        @empty

                            <tr>

                                <td colspan="5"
                                    style="text-align: center;">

                                    Belum ada data supplier.

                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>

    </div>


    <!-- Footer -->

    <div class="footer">

        &copy; {{ date('Y') }} POS Toko

    </div>

</body>

</html>