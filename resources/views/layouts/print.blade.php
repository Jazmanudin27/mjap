<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Laporan')</title>
    <style>
        body {
            font-family: Tahoma, sans-serif;
            font-size: 13px;
            background: #fff;
            margin: 20px;
        }

        h2,
        .periode {
            text-align: center;
            margin: 0;
        }

        .periode {
            margin: 5px 0 20px;
            font-size: 12px;
            color: #444;
        }

        table {
            border-collapse: collapse;
            width: 100%;
            margin-bottom: 15px;
        }

        th,
        td {
            border: 1px solid #ccc;
            padding: 6px 8px;
            white-space: nowrap;
        }

        th {
            background-color: #0d6efd;
            color: white;
            font-size: 13px;
        }

        tr:hover td {
            background: #f4faff;
        }

        .text-end {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .fw-bold {
            font-weight: bold;
        }

        .bg-lunas {
            color: green;
            font-weight: bold;
        }

        .bg-belum {
            color: orange;
            font-weight: bold;
        }

        .bg-batal {
            color: red;
            text-decoration: line-through;
            font-weight: bold;
        }

        tfoot td {
            font-weight: bold;
            background: #eaf1f8;
        }

        .small {
            font-size: 11px;
        }
    </style>
</head>

<body>
    <h2>@yield('title')</h2>
    @hasSection('periode')
        <div class="periode">
            @yield('periode')
        </div>
    @endif

    @yield('content')

</body>

</html>
