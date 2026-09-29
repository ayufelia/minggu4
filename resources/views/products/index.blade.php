<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Data Produk</title>

    <style>
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
        }

        .container {
            padding: 30px 40px;
        }

        .section {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }

        .top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .btn {
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

        .btn-warning {
            background-color: #ffc107;
            color: black;
        }

        .btn-danger {
            background-color: #dc3545;
        }

        .btn-secondary {
            background-color: #6c757d;
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

        .alert {
            padding: 12px;
            background-color: #d4edda;
            color: #155724;
            margin-bottom: 20px;
            border-radius: 5px;
        }
    </style>

</head>

<body>

    <div class="header">
        <h1>Data Produk</h1>
    </div>

    <div class="container">

        <div class="section">

            <div class="top">

                <h2>Daftar Produk</h2>

                <a href="{{ route('products.create') }}"
                   class="btn btn-primary">
                    + Tambah Produk
                </a>

            </div>

            @if(session('success'))
                <div class="alert">
                    {{ session('success') }}
                </div>
            @endif

            <table>

                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Produk</th>
                        <th>Kategori</th>
                        <th>SKU</th>
                        <th>Harga</th>
                        <th>Stok</th>
                        <th>Aksi</th>
                    </tr>
                </thead>

                <tbody>

                    @forelse($products as $product)

                    <tr>

                        <td>{{ $loop->iteration }}</td>

                        <td>{{ $product->name }}</td>

                        <td>{{ $product->category_name }}</td>

                        <td>{{ $product->sku }}</td>

                        <td>
                            Rp {{ number_format($product->price, 0, ',', '.') }}
                        </td>

                        <td>{{ $product->stock }}</td>

                        <td>

                            <a href="{{ route('products.edit', $product->id) }}"
                               class="btn btn-warning">
                                Edit
                            </a>

                            <form action="{{ route('products.destroy', $product->id) }}"
                                  method="POST"
                                  style="display:inline;">

                                @csrf
                                @method('DELETE')

                                <button type="submit"
                                        class="btn btn-danger"
                                        onclick="return confirm('Yakin ingin menghapus produk ini?')">
                                    Hapus
                                </button>

                            </form>

                        </td>

                    </tr>

                    @empty

                    <tr>
                        <td colspan="7" style="text-align:center;">
                            Belum ada data produk.
                        </td>
                    </tr>

                    @endforelse

                </tbody>

            </table>

            <br>

            <a href="/"
               class="btn btn-secondary">
                Kembali ke Dashboard
            </a>

        </div>

    </div>

</body>

</html>