@extends('mobile.presensi.layout')
@section('title', 'Dashboard')
@section('header', 'Dashboard Presensi')
@section('content')
    <div class="header">
        <div class="user-info">
            <div class="avatar-wrapper">
                <img src="{{ asset('assets/img/PresenTech.jpg') }}" alt="Avatar">
            </div>
            <marquee>
                <div class="user-role mt-2">Selamat datang, {{ Auth::user()->name }}</div>
            </marquee>
        </div>
        <div class="header-icons">
            <i class="bi bi-bell">
                <span class="notif-dot"></span>
            </i>
        </div>
    </div>


    <div class="content">
        <div class="status-rekap">
            <div class="status-item fade-delay-1">
                <div class="bg-success icon-card hadir">
                    <i class="bi bi-person-check-fill"></i>
                    @if ($hadir > 0)
                        <span class="badge">{{ $hadir }}</span>
                    @endif
                </div>
                <span class="label">Hadir</span>
            </div>
            <div class="status-item fade-delay-2">
                <div class="bg-primary icon-card izin">
                    <i class="bi bi-file-earmark-excel-fill"></i>
                    @if ($izin > 0)
                        <span class="badge">{{ $izin }}</span>
                    @endif
                </div>
                <span class="label">Izin</span>
            </div>
            <div class="status-item fade-delay-3">
                <div class="bg-warning icon-card sakit">
                    <i class="bi bi-hospital-fill"></i> <!-- Ini Rumah Sakit -->
                    @if ($sakit > 0)
                        <span class="badge">{{ $sakit }}</span>
                    @endif
                </div>
                <span class="label">Sakit</span>
            </div>
            <div class="status-item fade-delay-4">
                <div class="bg-danger icon-card telat">
                    <i class="bi bi-alarm-fill"></i>
                    @if ($telat > 0)
                        <span class="badge">{{ $telat }}</span>
                    @endif
                </div>
                <span class="label">Telat</span>
            </div>
        </div>

        <div class="content-padding">
            <div class="time-display-card">
                <div class="current-time" id="current-time">00:00:00</div>
                <div class="current-date" id="current-date">Senin, 29 Juli 2025</div>
            </div>
            <div class="scan-buttons fade-delay-6">
                <button onclick="scanMasuk()" class="btn-scan btn-scan-masuk">
                    @if ($presensi && $presensi->jam_in)
                        <img src="{{ asset('storage/' . $presensi->foto_in) }}" alt="Foto Masuk" class="scan-photo">
                        {{ substr($presensi->jam_in, 11, 8) }}
                    @else
                        <i class="bi bi-fingerprint"></i> Belum Scan
                    @endif
                </button>

                <button onclick="scanPulang()" class="btn-scan btn-scan-keluar">
                    @if ($presensi && $presensi->jam_out)
                        <img src="{{ asset('storage/' . $presensi->foto_out) }}" alt="Foto Pulang" class="scan-photo">
                        {{ substr($presensi->jam_out, 11, 8) }}
                    @else
                        <i class="bi bi-fingerprint"></i> Belum Scan
                    @endif
                </button>
            </div>
        </div>
        <style>
            .features-grid a,
            .features-grid a:hover,
            .features-grid a:active,
            .features-grid a:visited {
                text-decoration: none !important;
                color: inherit !important;
            }
        </style>
        <div class="features-grid">
            <a href="{{ route('riwayatPresensi') }}" class="feature-item fade-delay-7">
                <div class="icon-box bg-danger"><i class="bi bi-clock-history"></i></div>
                <div class="feature-label">Riwayat</div>
            </a>
            <a href="{{ route('suratAbsen') }}" class="feature-item fade-delay-8">
                <div class="icon-box bg-secondary"><i class="bi bi-envelope-fill"></i></div>
                <div class="feature-label">Surat Absen</div>
            </a>
            <a href="#" class="feature-item fade-delay-9">
                <div class="icon-box bg-warning text-dark"><i class="bi bi-wallet2"></i></div>
                <div class="feature-label">Kasbon</div>
            </a>
            <a href="#" class="feature-item fade-delay-10">
                <div class="icon-box bg-info text-dark"><i class="bi bi-cash-stack"></i></div>
                <div class="feature-label">Pinjaman</div>
            </a>
            <a href="#" class="feature-item fade-delay-11">
                <div class="icon-box bg-success"><i class="bi bi-file-earmark-text-fill"></i></div>
                <div class="feature-label">Slip Gaji</div>
            </a>
            <a href="#" class="feature-item fade-delay-12">
                <div class="icon-box bg-primary"><i class="bi bi-person-circle"></i></div>
                <div class="feature-label">Profile</div>
            </a>
            <a href="#" class="feature-item fade-delay-13">
                <div class="icon-box bg-dark"><i class="bi bi-alarm-fill"></i></div>
                <div class="feature-label">Lembur</div>
            </a>
            <a href="#" class="feature-item fade-delay-14">
                <div class="icon-box bg-pink text-white"><i class="bi bi-three-dots"></i></div>
                <div class="feature-label">Lainnya</div>
            </a>
        </div>
    </div>
    <input type="hidden" id="jamIn" value="{{ $presensi->jam_in ?? '' }}">
    <input type="hidden" id="jamOut" value="{{ $presensi->jam_out ?? '' }}">
    <script>
        // Update waktu realtime di kartu waktu
        function updateTime() {
            const now = new Date();
            const time = now.toLocaleTimeString('id-ID', {
                hour12: false
            });
            const date = now.toLocaleDateString('id-ID', {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });

            document.getElementById('current-time').textContent = time;
            document.getElementById('current-date').textContent = date;
        }

        setInterval(updateTime, 1000);
        updateTime();


        function scanMasuk() {
            const jamIn = document.getElementById('jamIn').value;
            if (jamIn) {
                Swal.fire({
                    icon: 'info',
                    title: 'Sudah Scan Masuk',
                    text: 'Anda sudah melakukan scan masuk pada ' + jamIn,
                    confirmButtonText: 'OK'
                });
            } else {
                window.location.href = "{{ route('scanPresensi', ['tipe' => 'masuk']) }}";
            }
        }

        function scanPulang() {
            const jamOut = document.getElementById('jamOut').value;
            if (jamOut) {
                Swal.fire({
                    icon: 'info',
                    title: 'Sudah Scan Pulang',
                    text: 'Anda sudah melakukan scan pulang pada ' + jamOut,
                    confirmButtonText: 'OK'
                });
            } else {
                window.location.href = "{{ route('scanPresensi', ['tipe' => 'pulang']) }}";
            }
        }
    </script>
@endsection
