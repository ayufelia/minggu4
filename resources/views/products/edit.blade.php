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
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
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

        .current-image {
            margin-bottom: 12px;
        }

        .current-image img {
            width: 150px;
            height: 150px;
            object-fit: cover;
            border-radius: 8px;
            border: 1px solid #ddd;
        }

        .btn {
            display: inline-block;
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

        .error {
            color: red;
            font-size: 14px;
            margin-top: 5px;
        }
    </style>
</head>

<body>

    <div class="header">
        <h1>Edit Produk</h1>
    </div>

    <div class="container">
        <div class="card">

            @if ($errors->any())
                <div class="error">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('products.update', $product->id) }}"
                  method="POST"
                  enctype="multipart/form-data">

                @csrf
                @method('PUT')

                <div class="form-group">
                    <label for="name">Nama Produk</label>
                    <input type="text"
                           id="name"
                           name="name"
                           value="{{ old('name', $product->name) }}"
                           required>
                </div>

                <div class="form-group">
                    <label for="category_id">Kategori</label>
                    <select name="category_id" id="category_id" required>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}"
                                {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="sku">SKU</label>
                    <input type="text"
                           id="sku"
                           name="sku"
                           value="{{ old('sku', $product->sku) }}"
                           required>
                </div>

                <div class="form-group">
                    <label for="price">Harga</label>
                    <input type="number"
                           id="price"
                           name="price"
                           value="{{ old('price', $product->price) }}"
                           min="0"
                           required>
                </div>

                <div class="form-group">
                    <label for="stock">Stok</label>
                    <input type="number"
                           id="stock"
                           name="stock"
                           value="{{ old('stock', $product->stock) }}"
                           min="0"
                           required>
                </div>

                <div class="form-group">
                    <label>Gambar Produk Saat Ini</label>

                    @if (!empty($product->image))
                        <div class="current-image">
                            <img src="{{ asset('storage/' . $product->image) }}"
                                 alt="{{ $product->name }}"
                                 onerror="this.style.display='none'; document.getElementById('image-error').style.display='block';">

                            <p id="image-error" style="display:none; color:red;">
                                Gambar tidak ditemukan. Periksa lokasi file gambar.
                            </p>
                        </div>
                    @else
                        <p>Belum ada gambar produk.</p>
                    @endif
                </div>

                <div class="form-group">
                    <label for="image">Ganti Gambar (Opsional)</label>
                    <input type="file"
                           id="image"
                           name="image"
                           accept="image/jpeg,image/png,image/webp">
                    <small>Kosongkan jika tidak ingin mengganti gambar.</small>
                </div>

                <a href="{{ route('products.index') }}"
                   class="btn btn-secondary">
                    Kembali
                </a>

                <button type="submit" class="btn btn-primary">
                    Update
                </button>

            </form>
        </div>
    </div>

</body>
</html>