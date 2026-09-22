<!DOCTYPE html>
<html>
<head>
    <title>Data Suppliers</title>
</head>
<body>

    <h1>Data Suppliers</h1>

    <table border="1" cellpadding="10">
        <tr>
            <th>ID</th>
            <th>Nama Supplier</th>
            <th>Telepon</th>
            <th>Alamat</th>
        </tr>

        @foreach ($suppliers as $supplier)
        <tr>
            <td>{{ $supplier->id }}</td>
            <td>{{ $supplier->name }}</td>
            <td>{{ $supplier->phone }}</td>
            <td>{{ $supplier->address }}</td>
        </tr>
        @endforeach

    </table>

</body>
</html>