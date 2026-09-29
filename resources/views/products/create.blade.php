<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Tambah Produk</title>

    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #f4f6f9;
        }

        .header {
            background-color: #343a40;
            color: white;
            padding: 25px 40px;
        }

        .container {
            padding: 30px 40px;
        }

        .card {
            background: white;
            padding: 25px;
            border-radius: 10px;
            max-width: 700px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }

        .form-group {
            margin-bottom: 18px;
        }

        label {
            display: block;
            margin-bottom: 7px;
            font-weight: bold;
        }

        input,
        select {
            width: 100%;
            padding: 10px;
            box-sizing: border-box;
        }

        .btn {
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            cursor: pointer;
        }

        .btn-primary {
            background-color: #007bff;
            color: white;
        }

        .btn-secondary {
            background-color: #6c757d;
            color: white;
        }
    </style>

</head>

<body>

    <div class="header">
        <h1>Tambah Produk</h1>
    </div>

    <div class="container">

        <div class="card">

            <form action="{{ route('products.store') }}" method="POST">

                @csrf

                <div class="form-group">

                    <label>Nama Produk</label>

                    <input type="text"
                           name="name"
                           required>

                </div>

                <div class="form-group">

                    <label>Kategori</label>

                    <select name="category_id" required>

                        <option value="">
                            -- Pilih Kategori --
                        </option>

                        @foreach($categories as $category)

                            <option value="{{ $category->id }}">
                                {{ $category->name }}
                            </option>

                        @endforeach

                    </select>

                </div>

                <div class="form-group">

                    <label>SKU</label>

                    <input type="text"
                           name="sku"
                           required>

                </div>

                <div class="form-group">

                    <label>Harga</label>

                    <input type="number"
                           name="price"
                           required>

                </div>

                <div class="form-group">

                    <label>Stok</label>

                    <input type="number"
                           name="stock"
                           required>

                </div>

                <a href="{{ route('products.index') }}"
                   class="btn btn-secondary">
                    Kembali
                </a>

                <button type="submit"
                        class="btn btn-primary">
                    Simpan
                </button>

            </form>

        </div>

    </div>

</body>

</html>