<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <title>Login - MJAP</title>
    <meta content="Login Sistem MJAP" name="description">
    <meta content="MJAP Login" name="keywords">

    <link href="{{ asset('assets/img/favicon.png') }}" rel="icon">
    <link href="{{ asset('assets/img/apple-touch-icon.png') }}" rel="apple-touch-icon">

    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="{{ asset('assets/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/vendor/bootstrap-icons/bootstrap-icons.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/style.css') }}" rel="stylesheet">

    <style>
        body {
            font-family: 'Nunito', sans-serif;
            height: 100vh;
            background: linear-gradient(-45deg, #0d6efd, #66b2ff, #1cc88a, #4e73df);
            background-size: 400% 400%;
            animation: gradientBG 15s ease infinite;
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
            background: rgba(255, 255, 255, 0.2);
            border-radius: 1rem;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            border: 1px solid rgba(255, 255, 255, 0.18);
            animation: slideFadeIn 1s ease;
        }

        @keyframes slideFadeIn {
            0% {
                opacity: 0;
                transform: translateY(30px);
            }

            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .form-control {
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: #1cc88a;
            box-shadow: 0 0 10px rgba(28, 200, 138, 0.4);
        }

        .btn-primary {
            background-color: #1cc88a;
            border: none;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background-color: #17a673;
            box-shadow: 0 4px 15px rgba(23, 166, 115, 0.4);
        }

        .logo-text {
            font-size: 1.8rem;
            font-weight: 700;
            color: #fff;
            margin-left: 0.5rem;
        }

        .card-title {
            color: #fff;
        }

        label {
            color: #fff;
        }

        .form-check-label {
            color: #f1f1f1;
        }

        .text-white-50 {
            color: rgba(255, 255, 255, 0.6);
        }

        .text-white-50 a {
            color: #fff;
            text-decoration: underline;
        }
    </style>
</head>

<body>

    <main>
        <div class="container">

            <section class="section register min-vh-100 d-flex flex-column align-items-center justify-content-center py-4">
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-lg-4 col-md-6 d-flex flex-column align-items-center justify-content-center">

                            <div class="d-flex justify-content-center py-3">
                                <a href="#" class="d-flex align-items-center">
                                    <img src="{{ asset('assets/img/logo.png') }}" alt="" width="40">
                                    <span class="logo-text">MJAP</span>
                                </a>
                            </div>

                            <div class="card login-card p-4">

                                <div class="card-body">

                                    <div class="pb-3 text-center">
                                        <h5 class="card-title fs-4">Login ke Akun Anda</h5>
                                        <p class="small">Masukkan username dan password</p>
                                    </div>

                                    @include('auth._message')

                                    <form class="row g-3" action="" method="post">
                                        {{ csrf_field() }}
                                        <div class="col-12">
                                            <label for="yourEmail" class="form-label">Username</label>
                                            <input type="text" name="email" class="form-control" id="yourEmail" required>
                                        </div>

                                        <div class="col-12">
                                            <label for="yourPassword" class="form-label">Password</label>
                                            <input type="password" name="password" class="form-control" id="yourPassword" required>
                                        </div>

                                        <div class="col-12">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="remember" id="rememberMe">
                                                <label class="form-check-label" for="rememberMe">Ingat saya</label>
                                            </div>
                                        </div>

                                        <div class="col-12">
                                            <button class="btn btn-primary w-100" type="submit">Login</button>
                                        </div>

                                        <div class="col-12">
                                            <p class="small text-center mb-0">Belum punya akun? <a href="pages-register.html">Daftar sekarang</a></p>
                                        </div>
                                    </form>

                                </div>
                            </div>

                            <div class="mt-4 text-white-50 small text-center">
                                &copy; {{ date('Y') }} MJAP. All rights reserved.
                            </div>

                        </div>
                    </div>
                </div>
            </section>

        </div>
    </main>

    <a href="#" class="back-to-top d-flex align-items-center justify-content-center">
        <i class="bi bi-arrow-up-short"></i>
    </a>

    <script src="{{ asset('assets/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/js/main.js') }}"></script>

</body>

</html>