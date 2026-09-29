<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Edit Produk</title>

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

        .header h1 {
            margin: 0;
        }

        .container {
            padding: 30px 40px;
        }

        .card {
            background-color: white;
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
            border: 1px solid #ccc;
            border-radius: 5px;
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

        <h1>Edit Produk</h1>

    </div>


    <div class="container">

        <div class="card">

            <form action="{{ route('products.update', $product->id) }}"
                  method="POST">

                @csrf

                @method('PUT')


                <div class="form-group">

                    <label>Nama Produk</label>

                    <input type="text"
                           name="name"
                           value="{{ $product->name }}"
                           required>

                </div>


                <div class="form-group">

                    <label>Kategori</label>

                    <select name="category_id" required>

                        @foreach($categories as $category)

                            <option value="{{ $category->id }}"
                                {{ $product->category_id == $category->id ? 'selected' : '' }}>

                                {{ $category->name }}

                            </option>

                        @endforeach

                    </select>

                </div>


                <div class="form-group">

                    <label>SKU</label>

                    <input type="text"
                           name="sku"
                           value="{{ $product->sku }}"
                           required>

                </div>


                <div class="form-group">

                    <label>Harga</label>

                    <input type="number"
                           name="price"
                           value="{{ $product->price }}"
                           required>

                </div>


                <div class="form-group">

                    <label>Stok</label>

                    <input type="number"
                           name="stock"
                           value="{{ $product->stock }}"
                           required>

                </div>


                <a href="{{ route('products.index') }}"
                   class="btn btn-secondary">

                    Kembali

                </a>


                <button type="submit"
                        class="btn btn-primary">

                    Update

                </button>

            </form>

        </div>

    </div>

</body>

</html>