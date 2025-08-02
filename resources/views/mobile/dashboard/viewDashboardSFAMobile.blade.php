@extends('mobile.layout')
@section('title', 'Dashboard')
@section('header', 'Dashboard Mobile')
@section('content')

    <!-- Include AOS CSS -->
    <link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .card:hover {
            transform: translateY(-5px);
            transition: all 0.3s ease;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        }

        .summary-icon {
            font-size: 24px;
        }

        .bg-gradient-blue {
            background: linear-gradient(135deg, #4e73df, #224abe);
        }

        .bg-gradient-purple {
            background: linear-gradient(135deg, #6f42c1, #4e2a85);
        }

        .bg-gradient-green {
            background: linear-gradient(135deg, #1cc88a, #17a673);
        }

        .bg-gradient-orange {
            background: linear-gradient(135deg, #fd7e14, #dc6805);
        }
    </style>

    <div class="header-wrapper">
        <div class="container">
            <div class="position-relative">
                {{-- Logo --}}
                <div class="text-start">
                    <img src="{{ asset('assets/img/DIS.png') }}" alt="Logo" height="60">
                </div>
                <a href="{{ route('mobile.logout') }}" class="position-absolute top-0 end-0 me-2 mt-2 text-decoration-none"
                    title="Keluar">
                    <div class="btn btn-sm btn-danger border rounded-circle p-2 shadow-sm d-flex justify-content-center align-items-center"
                        style="width: 40px; height: 40px;">
                        <i class="bi bi-box-arrow-right text-white fs-5"></i>
                    </div>
                </a>
            </div>
            <h5 class="mb-3 fw-bold">Selamat datang, {{ Auth::user()->name }}</h5>
            <div class="row g-2">
                {{-- Penjualan Hari Ini --}}
                <div class="col-6">
                    <div
                        class="card border-0 rounded-4 p-2 shadow-sm h-100 d-flex align-items-center flex-row gap-1 bg-gradient-blue">
                        <div class="summary-icon text-blue">
                            <i class="bi bi-cart-check-fill"></i>
                        </div>
                        <div>
                            <small class="text-white">Penj. Hari Ini</small><br>
                            <div class="fw-semibold text-white" style="font-size: 12px;">
                                Rp{{ number_format($penjualanHariIni ?? 0, 0, ',', '.') }}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-6">
                    <div
                        class="card border-0 rounded-4 p-2 shadow-sm h-100 d-flex align-items-center flex-row gap-1 bg-gradient-purple">
                        <div class="summary-icon text-purple">
                            <i class="bi bi-box-seam-fill"></i>
                        </div>
                        <div>
                            <small class="text-white">Penj. Bulan Ini</small><br>
                            <div class="fw-semibold text-white" style="font-size: 12px;">
                                Rp{{ number_format($penjualanBulanIni ?? 0, 0, ',', '.') }}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-6">
                    <div
                        class="card border-0 rounded-4 p-2 shadow-sm h-100 d-flex align-items-center flex-row gap-1 bg-gradient-green">
                        <div class="summary-icon text-green">
                            <i class="bi bi-wallet-fill"></i>
                        </div>
                        <div>
                            <small class="text-white">Tagihan Hari Ini</small><br>
                            <div class="fw-semibold text-white" style="font-size: 12px;">
                                Rp{{ number_format($pembayaranHariIni ?? 0, 0, ',', '.') }}
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Pembayaran Bulan Ini --}}
                <div class="col-6">
                    <div
                        class="card border-0 rounded-4 p-2 shadow-sm h-100 d-flex align-items-center flex-row gap-1 bg-gradient-orange">
                        <div class="summary-icon text-orange">
                            <i class="bi bi-graph-up-arrow"></i>
                        </div>
                        <div>
                            <small class="text-white">Tagihan Bulan Ini</small><br>
                            <div class="fw-semibold text-white" style="font-size: 12px;">
                                Rp{{ number_format($pembayaranBulanIni ?? 0, 0, ',', '.') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            {{-- <div class="menu-card mt-3">
                <div class="menu-row">
                    <div class="menu-item">
                        <div class="icon-circle bg-primary text-white"><i class="bi bi-calendar-check"></i></div>
                        <small>Kunjungan</small>
                    </div>
                    <div class="menu-item">
                        <div class="icon-circle bg-success text-white"><i class="bi bi-cart-plus"></i></div>
                        <small>Order</small>
                    </div>
                    <div class="menu-item">
                        <div class="icon-circle bg-warning text-white"><i class="bi bi-cash-stack"></i></div>
                        <small>Pembayaran</small>
                    </div>
                    <div class="menu-item">
                        <div class="icon-circle bg-danger text-white"><i class="bi bi-receipt"></i></div>
                        <small>Faktur</small>
                    </div>
                </div>

                <div class="menu-row">
                    <div class="menu-item">
                        <div class="icon-circle bg-info text-white"><i class="bi bi-box-seam"></i></div>
                        <small>Stok Barang</small>
                    </div>
                    <div class="menu-item">
                        <div class="icon-circle bg-secondary text-white"><i class="bi bi-bar-chart-line"></i></div>
                        <small>Laporan</small>
                    </div>
                    <div class="menu-item">
                        <div class="icon-circle bg-dark text-white"><i class="bi bi-geo-alt"></i></div>
                        <small>Rute</small>
                    </div>
                    <div class="menu-item">
                        <div class="icon-circle bg-success text-white"><i class="bi bi-person-badge"></i></div>
                        <small>Pelanggan</small>
                    </div>
                </div>

                <div class="menu-row">
                    <div class="menu-item">
                        <div class="icon-circle bg-primary text-white"><i class="bi bi-megaphone"></i></div>
                        <small>Promo</small>
                    </div>
                    <div class="menu-item">
                        <div class="icon-circle bg-warning text-white"><i class="bi bi-camera-fill"></i></div>
                        <small>Bukti Foto</small>
                    </div>
                    <div class="menu-item">
                        <div class="icon-circle bg-info text-white"><i class="bi bi-journal-text"></i></div>
                        <small>Aktivitas</small>
                    </div>
                    <div class="menu-item">
                        <div class="icon-circle bg-danger text-white"><i class="bi bi-gear"></i></div>
                        <small>Pengaturan</small>
                    </div>
                </div>
            </div> --}}
        </div>

        <div class="card mt-3 mx-3 rounded-4 shadow-sm">
            <div class="card-body px-2 pt-3">
                <ul class="nav nav-pills nav-justified mb-3 rounded-3 overflow-hidden bg-light" id="summaryTab"
                    role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active small" id="target-tab" data-bs-toggle="pill" data-bs-target="#target"
                            type="button" role="tab">🎯 Target</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link small" id="history-tab" data-bs-toggle="pill" data-bs-target="#history"
                            type="button" role="tab">📅 History</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link small" id="activity-tab" data-bs-toggle="pill" data-bs-target="#activity"
                            type="button" role="tab">📋 Activity</button>
                    </li>
                </ul>

                <div class="tab-content" id="summaryTabContent">
                    <!-- Target Tab -->
                    <div class="tab-pane fade show active" id="target" role="tabpanel">
                        <div class="mb-2">
                            <div class="d-flex gap-2 mb-2">
                                <select id="filterBulan" class="form-select form-select-sm">
                                    @foreach (range(1, 12) as $b)
                                        <option value="{{ $b }}" {{ now()->month == $b ? 'selected' : '' }}>
                                            {{ \Carbon\Carbon::create()->month($b)->translatedFormat('F') }}
                                        </option>
                                    @endforeach
                                </select>
                                <select id="filterTahun" class="form-select form-select-sm">
                                    @foreach (range(now()->year - 2, now()->year + 1) as $t)
                                        <option value="{{ $t }}" {{ now()->year == $t ? 'selected' : '' }}>
                                            {{ $t }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div id="targetResult">
                            <div class="text-center text-muted">Memuat data...</div>
                        </div>
                    </div>

                    <!-- History Tab -->
                    <div class="tab-pane fade" id="history" role="tabpanel">
                        <div class="mb-3 d-flex gap-2">
                            <input type="date" id="tanggalDari" class="form-control form-control-sm"
                                value="{{ now()->format('Y-m-d') }}">
                            <input type="date" id="tanggalSampai" class="form-control form-control-sm"
                                value="{{ now()->format('Y-m-d') }}">
                        </div>

                        <div id="historyResult">

                        </div>
                    </div>

                    <!-- Activity Tab -->
                    <div class="tab-pane fade" id="activity" role="tabpanel">
                        <ul class="list-group list-group-flush small">
                            @forelse ($activity as $log)
                                @php
                                    $checkin = \Carbon\Carbon::parse($log->checkin);
                                    $checkout = $log->checkout ? \Carbon\Carbon::parse($log->checkout) : null;
                                    $durasi = $checkout ? $checkin->diff($checkout)->format('%h jam %i menit') : '-';
                                @endphp
                                <li class="list-group-item px-3 py-2">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <div class="fw-semibold">{{ $log->nama_pelanggan ?? '-' }}</div>
                                            <div class="text-muted small">
                                                <i class="bi bi-calendar-check me-1"></i>
                                                {{ $checkin->format('d M Y') }}
                                            </div>
                                            <div class="text-muted small mt-1">
                                                <i class="bi bi-hourglass-split me-1"></i> Durasi:
                                                <span class="fw-semibold text-dark">{{ $durasi }}</span>
                                            </div>
                                        </div>
                                        <div class="text-end">
                                            <div class="badge bg-success-subtle text-success border mb-1">
                                                <i class="bi bi-box-arrow-in-right me-1"></i>
                                                {{ $checkin->format('H:i') }}
                                            </div>
                                            @if ($checkout)
                                                <div class="badge bg-danger-subtle text-danger border">
                                                    <i class="bi bi-box-arrow-left me-1"></i>
                                                    {{ $checkout->format('H:i') }}
                                                </div>
                                            @else
                                                <div class="badge bg-secondary-subtle text-secondary border mt-1">Belum
                                                    Checkout</div>
                                            @endif
                                        </div>
                                    </div>
                                </li>
                            @empty
                                <li class="list-group-item text-center text-muted py-3">
                                    <i class="bi bi-clock-history fs-4 d-block"></i>
                                    Tidak ada aktivitas check-in.
                                </li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Include AOS & CountUp.js -->
    <script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/countup.js@2.0.8/dist/countUp.min.js"></script>
    <script>
        AOS.init();
        document.addEventListener("DOMContentLoaded", () => {
            const targets = document.querySelectorAll('[data-countup]');
            targets.forEach(el => {
                const value = el.getAttribute('data-countup');
                const counter = new CountUp(el, value);
                if (!counter.error) {
                    counter.start();
                } else {
                    console.error(counter.error);
                }
            });
        });

        function loadTargetDataMobile() {
            let bulan = $('#filterBulan').val();
            let tahun = $('#filterTahun').val();

            $.get('filterTargetMobile', {
                bulan,
                tahun
            }, function(data) {
                let html = '';

                data.forEach(item => {
                    html += `
                <div class="card shadow-sm mb-3">
                    <div class="card-body p-3">
                        <h6 class="fw-bold text-primary mb-2">${item.nama_sales}</h6>
                        ${renderProgress('OA', item.target_oa, item.real_oa, item.persen_oa)}
                        ${renderProgress('EC', item.target_ec, item.real_ec, item.persen_ec)}
                        ${renderProgress('Penjualan', item.target_penjualan, item.real_penjualan, item.persen_penjualan, true)}
                        ${renderProgress('Tagihan', item.target_tagihan, item.real_tagihan, item.persen_tagihan, true)}
                    </div>
                </div>
            `;
                });

                $('#targetResult').html(html);
            });
        }

        function renderProgress(label, target, real, persen, isRupiah = false) {
            const format = val => isRupiah ? `Rp${Number(val).toLocaleString('id-ID')}` : Number(val).toLocaleString(
                'id-ID');

            return `
        <div class="mb-2">
            <div class="d-flex justify-content-between small">
                <div class="text-muted">${label}</div>
                <div class="text-muted">${format(real)} / ${format(target)}</div>
            </div>
            <div class="progress" style="height: 8px;">
                <div class="progress-bar bg-${getColor(persen)}" style="width: ${persen}%"></div>
            </div>
            <div class="text-end text-muted small">${persen}% tercapai</div>
        </div>
    `;
        }

        function getColor(persen) {
            if (persen >= 100) return 'success';
            if (persen >= 80) return 'warning';
            return 'danger';
        }

        function loadHistoryData() {
            let dari = $('#tanggalDari').val();
            let sampai = $('#tanggalSampai').val();

            $.get('filterHistory', {
                dari: dari,
                sampai: sampai
            }, function(html) {
                $('#historyResult').html(html);
            });
        }

        $(document).ready(function() {
            loadTargetDataMobile();
            loadHistoryData();

            $('#tanggalDari, #tanggalSampai').on('change', function() {
                loadHistoryData();
            });
            $('#filterBulan, #filterTahun').on('change', function() {
                loadTargetDataMobile();
            });
        });
    </script>
@endsection
