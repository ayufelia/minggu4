
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
            padding: 30px;
        }

        .container {
            max-width: 700px;
            margin: auto;
            padding: 30px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        h2 {
            margin-top: 0;
            color: #333;
        }

        .form-group {
            margin-bottom: 18px;
        }

        label {
            display: block;
            margin-bottom: 7px;
            font-weight: bold;
        }

        input, select {
            width: 100%;
            padding: 11px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
            font-size: 14px;
        }

        .btn {
            display: inline-block;
            padding: 11px 18px;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            cursor: pointer;
            font-size: 14px;
        }

        .btn-primary {
            background: #007bff;
            color: white;
        }

        .btn-secondary {
            background: #6c757d;
            color: white;
        }

        .error {
            color: red;
            margin-top: 5px;
            font-size: 13px;
        }

        .alert {
            background: #f8d7da;
            color: #721c24;
            padding: 12px;
            border-radius: 5px;
            margin-bottom: 18px;
        }

        .buttons {
            display: flex;
            gap: 10px;
            margin-top: 25px;
        }

        small {
            display: block;
            margin-top: 5px;
            color: #666;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Tambah Produk</h2>

    @if ($errors->any())
        <div class="alert">
            <strong>Terjadi kesalahan:</strong>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('products.store') }}"
          method="POST"
          enctype="multipart/form-data">
        @csrf

        <div class="form-group">
            <label for="name">Nama Produk</label>
            <input type="text" id="name" name="name"
                   value="{{ old('name') }}"
                   placeholder="Masukkan nama produk" required>
            @error('name')
                <div class="error">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="category_id">Kategori</label>
            <select id="category_id" name="category_id" required>
                <option value="">-- Pilih Kategori --</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}"
                        {{ old('category_id') == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
            @error('category_id')
                <div class="error">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="sku">SKU</label>
            <input type="text" id="sku" name="sku"
                   value="{{ old('sku') }}"
                   placeholder="Masukkan kode SKU" required>
            @error('sku')
                <div class="error">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="price">Harga</label>
            <input type="number" id="price" name="price"
                   value="{{ old('price') }}"
                   placeholder="Masukkan harga produk"
                   min="0" step="0.01" required>
            @error('price')
                <div class="error">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="stock">Stok</label>
            <input type="number" id="stock" name="stock"
                   value="{{ old('stock') }}"
                   placeholder="Masukkan jumlah stok"
                   min="0" required>
            @error('stock')
                <div class="error">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="image">Gambar Produk</label>
            <input type="file" id="image" name="image"
                   accept=".jpg,.jpeg,.png,.webp">
            <small>Format JPG, JPEG, PNG, atau WEBP. Maksimal 2 MB.</small>
            @error('image')
                <div class="error">{{ $message }}</div>
            @enderror
        </div>

        <div class="buttons">
            <a href="{{ route('products.index') }}"
               class="btn btn-secondary">Kembali</a>

            <button type="submit" class="btn btn-primary">
                Simpan Produk
            </button>
        </div>
    </form>
</div>

</body>
</html>