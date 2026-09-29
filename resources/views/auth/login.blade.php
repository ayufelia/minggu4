<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Login POS Toko</title>

    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #f4f6f9;
        }

        .container {
            width: 100%;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .login-box {
            width: 350px;
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        h2 {
            text-align: center;
            margin-bottom: 25px;
        }

        .form-group {
            margin-bottom: 18px;
        }

        label {
            display: block;
            margin-bottom: 7px;
            font-weight: bold;
        }

        input {
            width: 100%;
            padding: 10px;
            box-sizing: border-box;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .btn {
            width: 100%;
            padding: 11px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .btn:hover {
            background-color: #0056b3;
        }

        .error {
            background-color: #f8d7da;
            color: #721c24;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
        }
    </style>

</head>

<body>

    <div class="container">

        <div class="login-box">

            <h2>Login POS Toko</h2>

            @if($errors->any())

                <div class="error">
                    {{ $errors->first() }}
                </div>

            @endif

            <form action="{{ route('login.proses') }}" method="POST">

                @csrf

                <div class="form-group">

                    <label>Email</label>

                    <input type="email"
                           name="email"
                           value="{{ old('email') }}"
                           required>

                </div>

                <div class="form-group">

                    <label>Password</label>

                    <input type="password"
                           name="password"
                           required>

                </div>

                <button type="submit" class="btn">
                    Login
                </button>

            </form>

        </div>

    </div>

</body>

</html>