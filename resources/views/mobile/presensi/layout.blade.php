<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="google" content="notranslate">
    <title>@yield('title', 'Mobile App')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="manifest" href="/manifestPresensi.json">
    <meta name="theme-color" content="#0d6efd">
    <link rel="apple-touch-icon" href="{{ asset('assets/img/MJAP.png') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="{{ asset('assets/css/stylePresensi.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.3/dist/leaflet.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" />

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://unpkg.com/leaflet@1.9.3/dist/leaflet.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</head>

<body>

    @yield('content')
    <div class="bottom-nav">
        <a href="{{ route('viewDashboardPresensi') }}" class="active">
            <i class="bi bi-house-door-fill"></i>
            Home
        </a>
        <a href="#">
            <i class="bi bi-list-ul"></i>
            Menu
        </a>
        <a href="#">
            <i class="bi bi-person-circle"></i>
            Profil
        </a>
    </div>

</body>

</html>
