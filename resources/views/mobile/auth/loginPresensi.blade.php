<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Login Presensi - MJAP</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', sans-serif;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(120deg, #4e73df, #1cc88a, #66b2ff, #0d6efd);
            background-size: 300% 300%;
            animation: gradientBG 10s ease infinite;
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

        .login-card {
            width: 100%;
            max-width: 360px;
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 30px 25px;
            text-align: center;
            color: #fff;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            animation: fadeInUp 1s ease;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .login-card img {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 20px;
            border: 3px solid rgba(255, 255, 255, 0.6);
            animation: popIn 1s ease;
        }

        @keyframes popIn {
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
            margin-bottom: 25px;
            font-weight: bold;
            font-size: 22px;
        }

        input {
            width: 100%;
            padding: 12px 14px;
            margin-bottom: 15px;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            background: rgba(255, 255, 255, 0.25);
            color: #fff;
            backdrop-filter: blur(5px);
            transition: 0.3s;
        }

        input::placeholder {
            color: rgba(255, 255, 255, 0.6);
        }

        input:focus {
            background: rgba(255, 255, 255, 0.4);
            outline: none;
        }

        button {
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 10px;
            background: #1cc88a;
            color: #fff;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: 0.3s;
        }

        button:hover {
            background: #17a673;
            box-shadow: 0 4px 12px rgba(23, 166, 115, 0.4);
        }

        .error-message {
            background: rgba(255, 0, 0, 0.15);
            padding: 10px;
            border-radius: 8px;
            color: #ff4d4d;
            margin-bottom: 15px;
            font-size: 14px;
        }

        @media(max-width: 480px) {
            .login-card {
                padding: 25px 20px;
            }

            .login-card img {
                width: 65px;
                height: 65px;
            }
        }
    </style>
</head>

<body>

    <div class="login-card">
        {{-- <img src="{{ asset('assets/img/profile-default.png') }}" alt="Foto Karyawan"> --}}
        <h2>Login Presensi</h2>
        @if ($errors->any())
            <div class="error-message">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ url('presensi/authPresensi') }}">
            @csrf
            <input type="text" name="email" placeholder="Username" required autofocus>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Masuk</button>
        </form>
    </div>

</body>

</html>
