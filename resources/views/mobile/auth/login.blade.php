<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Login Mobile - MJAP</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#0d6efd">
    <link rel="apple-touch-icon" href="/assets/img/DIS.png">
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', sans-serif;
            margin: 0;
            padding: 0;
            height: 100vh;
            background: linear-gradient(-45deg, #0d6efd, #1cc88a, #4e73df, #66b2ff);
            background-size: 400% 400%;
            animation: gradientBG 15s ease infinite;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        @keyframes gradientBG {
            0% {
                background-position: 0% 50%;
            }

            50% {
                background-position: 100% 50%;
            }

            100% {
                background-position: 0% 50%;
            }
        }

        .login-container {
            width: 100%;
            max-width: 380px;
            margin: 0 20px;
            background: rgba(255, 255, 255, 0.15);
            border-radius: 16px;
            padding: 30px 25px;
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.2);
            animation: fadeInUp 1s ease;
        }

        @keyframes fadeInUp {
            from {
                transform: translateY(30px);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .logo {
            display: block;
            margin: 0 auto 20px;
            width: 70px;
            height: 70px;
            object-fit: contain;
            animation: logoPop 1s ease;
        }

        @keyframes logoPop {
            from {
                transform: scale(0.8);
                opacity: 0;
            }

            to {
                transform: scale(1);
                opacity: 1;
            }
        }

        h2 {
            text-align: center;
            margin-bottom: 25px;
            color: #fff;
        }

        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 12px 14px;
            margin-bottom: 14px;
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 10px;
            font-size: 15px;
            background: rgba(255, 255, 255, 0.15);
            color: #fff;
            backdrop-filter: blur(5px);
            transition: 0.3s;
        }

        input::placeholder {
            color: rgba(255, 255, 255, 0.6);
        }

        input:focus {
            border-color: #1cc88a;
            outline: none;
            background: rgba(255, 255, 255, 0.25);
        }

        button {
            width: 100%;
            padding: 12px;
            background-color: #1cc88a;
            border: none;
            border-radius: 10px;
            color: white;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        button:hover {
            background-color: #17a673;
            box-shadow: 0 4px 12px rgba(23, 166, 115, 0.4);
        }

        .error {
            color: #d8000c;
            background-color: rgba(255, 0, 0, 0.1);
            padding: 10px 14px;
            border-radius: 6px;
            font-size: 14px;
            margin-bottom: 15px;
            text-align: center;
        }

        @media (max-width: 480px) {
            .login-container {
                padding: 25px 20px;
            }

            .logo {
                width: 60px;
                height: 60px;
            }
        }
    </style>
</head>

<body>
    <div class="login-container">
        {{-- Logo --}}
        {{-- <img src="{{ asset('assets/img/logo.png') }}" alt="Logo" class="logo"> --}}

        <h2>Login Mobile</h2>

        @if ($errors->any())
            <div class="error">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ url('mobile/login') }}">
            @csrf
            <input type="text" name="email" placeholder="Username" required autofocus>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Masuk</button>
        </form>
    </div>
</body>

</html>
