<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="google" content="notranslate">
    <title>@yield('title', 'Mobile App')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#0d6efd">
    <link rel="apple-touch-icon" href="/assets/img/DIS.png">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    {{-- Bootstrap --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    {{-- Bootstrap Icons --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    {{-- Animate CSS --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        body {
            background-color: #f4f6f9;
            font-family: 'Segoe UI', sans-serif;
            margin: 0;
            padding-bottom: 70px;
        }

        .header-wrapper {
            position: relative;
        }

        .mobile-header {
            background-color: #0d6efd;
            padding: 1.5rem 1rem 3.5rem;
            border-bottom-left-radius: 20px;
            border-bottom-right-radius: 20px;
            position: relative;
            z-index: 1;
        }

        .bg-gradient-light {
            background: linear-gradient(to bottom right, #f0f4ff, #ffffff);
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
        }

        .bottom-nav {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background-color: #ffffff;
            border-top: 1px solid #dee2e6;
            display: flex;
            justify-content: space-around;
            padding: 8px 0;
            box-shadow: 0 -2px 6px rgba(0, 0, 0, 0.05);
            z-index: 10;
        }

        .bottom-nav a {
            text-align: center;
            color: #6c757d;
            font-size: 12px;
            text-decoration: none;
            flex: 1;
        }

        .bottom-nav a.active,
        .bottom-nav a:hover {
            color: #0d6efd;
        }

        .bottom-nav i {
            font-size: 20px;
            display: block;
        }



        .app-header {
            padding: 1rem;
            background-color: #fff;
            border-bottom: 1px solid #ccc;
        }

        .program-card {
            border: 1px solid #e0e0e0;
            border-radius: 10px;
            padding: 1rem;
            margin-bottom: 1rem;
            background-color: #fff;
        }

        .icon-box {
            width: 48px;
            height: 48px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #e3f2fd;
            font-size: 20px;
            color: #1976d2;
        }

        .service-icon {
            width: 64px;
            text-align: center;
            margin-bottom: 1rem;
        }

        .service-icon i {
            font-size: 24px;
            display: block;
            margin-bottom: 4px;
            color: #2e7d32;
        }

        .nav-bottom {
            border-top: 1px solid #ccc;
            background-color: #fff;
            padding: 0.5rem 0;
            position: fixed;
            bottom: 0;
            width: 100%;
        }

        .nav-bottom .nav-link {
            color: #757575;
        }

        .nav-bottom .nav-link.active {
            color: #2e7d32;
        }

        .service-icon {
            margin-bottom: 20px;
        }

        .menu-card {
            padding: 1rem;
            border-radius: 1.5rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            background-color: #ffffff;
        }

        .service-icon {
            padding: 8px 4px;
        }

        .icon-circle {
            width: 55px;
            height: 55px;
            background: #f1f3f5;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            margin: 0 auto;
            font-size: 22px;
            color: #0d6efd;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
            transition: 0.2s;
        }

        .menu-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 1rem;
        }

        .icon-circle:hover {
            transform: scale(1.1);
            background-color: #e9f2ff;
        }

        .menu-item {
            flex: 0 0 22%;
            text-align: center;
        }

        .menu-item small {
            display: block;
            margin-top: 6px;
            font-size: 12px;
            line-height: 1.2;
            color: #333;
            min-height: 32px;
            /* jaga agar semua teks tinggi sama */
            text-wrap: balance;
            /* agar distribusi kata lebih merata */
            overflow: hidden;
        }

        .program-card {
            background-color: #ffffff;
            border-radius: 1rem;
            padding: 1rem;
            margin-bottom: 1rem;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.05);
            transition: 0.3s ease;
        }

        .program-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
        }

        .circle-icon {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            color: #fff;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
        }

        .summary-icon {
            width: 35px;
            height: 35px;
            background-color: #ffffff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
        }

        .summary-icon.text-blue {
            color: #1565c0;
        }

        .summary-icon.text-purple {
            color: #512da8;
        }

        .summary-icon.text-green {
            color: #2e7d32;
        }

        .summary-icon.text-orange {
            color: #ef6c00;
        }

        .nav-pills .nav-link.small {
            font-size: 12px;
            padding: 4px 6px;
            border-radius: 0;
            transition: background-color 0.2s ease;
        }

        .nav-pills .nav-link.active {
            background-color: #0051ff !important;
            color: #fff !important;
            font-weight: 600;
        }

        /* Gradient themes */
        .bg-gradient-blue {
            background: linear-gradient(to bottom right, #1565c0, #42a5f5);
        }

        .bg-gradient-purple {
            background: linear-gradient(to bottom right, #512da8, #9575cd);
        }

        .bg-gradient-green {
            background: linear-gradient(to bottom right, #2e7d32, #81c784);
        }

        .bg-gradient-orange {
            background: linear-gradient(to bottom right, #ef6c00, #ffb74d);
        }

        .history-item {
            font-family: 'Inter', sans-serif;
            border-radius: 12px;
            transition: 0.2s ease;
            background-color: #f9f9f9;
        }

        .history-item:hover {
            background-color: #eef7ff;
        }

        .history-faktur {
            font-weight: 600;
            font-size: 14px;
            color: #2c3e50;
        }

        .history-pelanggan {
            font-size: 13px;
            color: #6c757d;
        }

        .history-tanggal {
            font-size: 12px;
            color: #a0a0a0;
        }

        .history-total {
            background: #e3f2fd;
            color: #1565c0;
            border: 1px solid #bbdefb;
            font-weight: 600;
            font-size: 13px;
            padding: 4px 8px;
            border-radius: 8px;
        }

        .bottom-nav .badge {
            font-size: 10px;
            padding: 4px 6px;
            z-index: 10;
            left: 75% !important;
        }

        .bottom-nav i {
            font-size: 1.3rem;
        }
    </style>
</head>

<body>

    <main class="content">
        @yield('content')
    </main>

    @php
        $user = Auth::user();
        $userRole = $user->role;
        $userNik = $user->nik;
        $userTeam = $user->team;

        $pendingKreditQuery = DB::table('pengajuan_limit_kredit')
            ->join('hrd_karyawan', 'hrd_karyawan.nik', '=', 'pengajuan_limit_kredit.nik')
            ->where('pengajuan_limit_kredit.status', 'diajukan');

        if ($userRole === 'sales') {
            $pendingKreditQuery->where('pengajuan_limit_kredit.nik', $userNik);
        } elseif ($userRole === 'spv sales') {
            $pendingKreditQuery->where('hrd_karyawan.divisi', $userTeam);
        }

        $pendingKredit = $pendingKreditQuery->distinct('pengajuan_limit_kredit.id')->count('pengajuan_limit_kredit.id');
        $pendingFakturQuery = DB::table('pengajuan_limit_faktur')
            ->join('hrd_karyawan', 'hrd_karyawan.nik', '=', 'pengajuan_limit_faktur.nik')
            ->where('pengajuan_limit_faktur.status', 'diajukan');

        if ($userRole === 'sales') {
            $pendingFakturQuery->where('pengajuan_limit_faktur.nik', $userNik);
        } elseif ($userRole === 'spv sales') {
            $pendingFakturQuery->where('hrd_karyawan.divisi', $userTeam);
        }

        $pendingFaktur = $pendingFakturQuery->distinct('pengajuan_limit_faktur.id')->count('pengajuan_limit_faktur.id');
    @endphp
    <nav class="bottom-nav">
        <a href="{{ route('viewDashboardSFAMobile') }}"
            class="{{ request()->routeIs('viewDashboardSFAMobile') ? 'active' : '' }}">
            <i class="bi bi-house-door-fill"></i>
            <span>Beranda</span>
        </a>
        <a href="{{ route('viewPelangganMobile') }}"
            class="{{ request()->routeIs('viewPelangganMobile') ? 'active' : '' }}">
            <i class="bi bi-people-fill"></i>
            <span>Pelanggan</span>
        </a>
        <a href="{{ route('limitKreditMobile') }}"
            class="{{ request()->routeIs('limitKreditMobile') ? 'active' : '' }}">
            <div class="position-relative d-flex justify-content-center">
                <i class="bi bi-file-earmark-check-fill"></i>
                @if ($pendingKredit > 0)
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                        {{ $pendingKredit }}
                    </span>
                @endif
            </div>
            <span>Limit Kredit</span>
        </a>
        <a href="{{ route('limitFakturMobile') }}"
            class="{{ request()->routeIs('limitFakturMobile') ? 'active' : '' }}">
            <div class="position-relative d-flex justify-content-center">
                <i class="bi bi-receipt-cutoff"></i>
                @if ($pendingFaktur > 0)
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                        {{ $pendingFaktur }}
                    </span>
                @endif
            </div>
            <span>Faktur</span>
        </a>
        <a href="{{ route('profileMobile', Auth::user()->nik) }}"
            class="{{ request()->routeIs('profileMobile') ? 'active' : '' }}">
            <i class="bi bi-person-circle"></i>
            <span>Profil</span>
        </a>
    </nav>
    <script type="module" src="/service-worker.js"></script>

</body>

</html>
