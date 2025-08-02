@extends('mobile.layout')
@section('title', 'Kunjungan')
@section('header', 'Data Pelanggan')

@section('content')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    @php
        $belumLunasList = DB::table('penjualan')
            ->leftJoin(
                DB::raw(
                    '(SELECT no_faktur, SUM(jumlah) AS total_bayar FROM penjualan_pembayaran GROUP BY no_faktur) AS pbyr',
                ),
                'penjualan.no_faktur',
                '=',
                'pbyr.no_faktur',
            )
            ->leftJoin(
                DB::raw(
                    '(SELECT no_faktur, SUM(jumlah) AS total_giro FROM penjualan_pembayaran_giro GROUP BY no_faktur) AS gr',
                ),
                'penjualan.no_faktur',
                '=',
                'gr.no_faktur',
            )
            ->leftJoin(
                DB::raw(
                    '(SELECT no_faktur, SUM(jumlah) AS total_transfer FROM penjualan_pembayaran_transfer GROUP BY no_faktur) AS tf',
                ),
                'penjualan.no_faktur',
                '=',
                'tf.no_faktur',
            )
            ->select(
                'penjualan.no_faktur',
                'penjualan.tanggal',
                'penjualan.grand_total',
                DB::raw(
                    'COALESCE(pbyr.total_bayar, 0) + COALESCE(gr.total_giro, 0) + COALESCE(tf.total_transfer, 0) AS total_dibayar',
                ),
                DB::raw(
                    'penjualan.grand_total - (COALESCE(pbyr.total_bayar, 0) + COALESCE(gr.total_giro, 0) + COALESCE(tf.total_transfer, 0)) AS sisa',
                ),
            )
            ->where('penjualan.kode_pelanggan', $pelanggan->kode_pelanggan)
            ->where('penjualan.batal', 0)
            ->having('sisa', '>', 0)
            ->orderBy('penjualan.tanggal', 'desc')
            ->get();
    @endphp
    <div class="container py-3">
        <div class="d-flex gap-2 mb-3">
            <a href="{{ route('editPelangganMobile', $pelanggan->kode_pelanggan) }}" class="btn btn-sm btn-success rounded-3">
                <i class="bi bi-pencil-square"></i>
            </a>
            <a href="{{ route('editFotoLokasiMobile', $pelanggan->kode_pelanggan) }}"
                class="btn btn-sm btn-primary rounded-3">
                <i class="bi bi-camera"></i>
            </a>
            <a href="{{ route('createPengajuanLimitMobile',$pelanggan->kode_pelanggan) }}" class="btn btn-sm btn-primary rounded-3"><i
                    class="bi bi-currency-dollar"></i></a>
            <a href="{{ route('createPengajuanFakturMobile',$pelanggan->kode_pelanggan) }}" class="btn btn-sm btn-primary rounded-3"><i class="bi bi-receipt"></i></a>
        </div>

        <div class="card shadow-sm border-0 rounded-4 mb-3 text-white" style="background: #0059ff; background-size: cover;">
            <div class="card-body rounded-4"
                style="background-image: url('{{ $pelanggan->foto ? asset('storage/pelanggan/' . $pelanggan->foto) : '' }}'); background-size: cover; background-position: center;">
                <div class="bg-dark bg-opacity-50 p-3 rounded-4">
                    <div class="fw-bold fs-6">{{ $pelanggan->kode_pelanggan }}</div>
                    <div class="fw-bold fs-5">{{ $pelanggan->nama_pelanggan }}</div>
                    <div>{{ $pelanggan->alamat_toko }}</div>
                    <div class="mt-2">Limit Pelanggan: {{ number_format($pelanggan->limit_pelanggan) }}</div>
                    <div>Jumlah Faktur Max: {{ number_format($pelanggan->max_faktur) }}</div>
                    <div class="mt-2">
                        <div class="fw-semibold text-white">Faktur Belum Lunas:</div>
                        @if ($belumLunasList->isEmpty())
                            <div class="text-white">Tidak ada</div>
                        @else
                            <ul class="list-unstyled mb-0">
                                @foreach ($belumLunasList as $faktur)
                                    <li>
                                        <a href="{{ route('detailPenjualanMobile', $faktur->no_faktur) }}"
                                            class="text-white text-decoration-none">
                                            <span class="badge bg-warning text-dark">{{ $faktur->no_faktur }}</span>
                                            <small>Sisa: Rp {{ number_format($faktur->sisa, 0, ',', '.') }}</small>
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div id="map" class="rounded-4 shadow-sm mb-3" style="height: 220px;"></div>
        @if (!$kunjunganAktif)
            {{-- Jika belum check-in --}}
            <form method="POST" action="{{ route('checkin') }}">
                @csrf
                <input type="hidden" name="kode_pelanggan" value="{{ $pelanggan->kode_pelanggan }}">
                <input type="hidden" name="latitude" id="latitude">
                <input type="hidden" name="longitude" id="longitude">
                <button type="submit" class="btn btn-sm btn-success w-100 shadow-sm">
                    <i class="bi bi-fingerprint me-2"></i>Check-In
                </button>
            </form>
        @else
            {{-- Jika sudah check-in --}}
            <div class="alert alert-primary d-flex align-items-center rounded-3 shadow-sm mt-3" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i> Sudah Check-In: {{ $kunjunganAktif->checkin }}
            </div>

            @if ($kunjunganAktif->checkout)
                <div class="alert alert-danger d-flex align-items-center rounded-3 shadow-sm mt-3" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i> Sudah Check-Out: {{ $kunjunganAktif->checkout }}
                </div>
            @else
                <form method="POST" action="{{ route('checkout') }}" class="mt-2">
                    @csrf
                    <input type="hidden" name="kunjungan_id" value="{{ $kunjunganAktif->id }}">
                    <button type="submit" class="btn btn-sm btn-danger w-100 rounded-3 shadow-sm">
                        <i class="bi bi-box-arrow-left me-1"></i> Check-Out
                    </button>
                </form>
            @endif

            {{-- Tampilkan tombol input penjualan & retur selama masih dalam kunjungan --}}
            <div class="row text-center mt-3 mb-2">
                <div class="col-6">
                    <a href="{{ route('createPenjualanMobile', ['id' => $pelanggan->kode_pelanggan]) }}"
                        class="btn btn-sm btn-lg btn-warning w-100 shadow-sm rounded-4">
                        <i class="bi bi-cart-plus me-2"></i>Input Penjualan
                    </a>
                </div>
                <div class="col-6">
                    <a href="{{ route('createReturMobile', ['id' => $pelanggan->kode_pelanggan]) }}"
                        class="btn btn-sm btn-lg btn-info text-white w-100 shadow-sm rounded-4">
                        <i class="bi bi-arrow-counterclockwise me-2"></i>Input Retur
                    </a>
                </div>
            </div>
        @endif
        <ul class="nav nav-tabs nav-fill mt-4" role="tablist">
            <li class="nav-item" role="presentation">
                <a class="nav-link active" data-bs-toggle="tab" href="#penjualan" role="tab">Penjualan</a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link" data-bs-toggle="tab" href="#retur" role="tab">Retur</a>
            </li>
        </ul>

        <div class="tab-content mt-2">
            <div class="tab-pane fade show active" id="penjualan" role="tabpanel">
                <div class="list-group list-group-flush small">
                    @forelse ($penjualanList as $jual)
                        <a href="{{ route('detailPenjualanMobile', $jual->no_faktur) }}" class="text-decoration-none">
                            <div class="card border-0 shadow-sm rounded-4 mb-3">
                                <div class="card-body p-3">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <div class="fw-bold text-primary mb-1">
                                                <i class="bi bi-receipt-cutoff me-1"></i> {{ $jual->no_faktur }}
                                            </div>
                                            <div class="text-muted small mb-1">
                                                <i class="bi bi-calendar-event me-1"></i>
                                                {{ \Carbon\Carbon::parse($jual->tanggal)->format('d M Y') }}
                                            </div>
                                            <div class="text-muted small">
                                                <i class="bi bi-cash-coin me-1"></i>
                                                Total: <span class="fw-semibold text-dark">Rp
                                                    {{ number_format($jual->total, 0, ',', '.') }}</span>
                                            </div>
                                            <div class="mt-1">
                                                @php
                                                    $lunas = floatval($jual->total_bayar) >= floatval($jual->total);
                                                @endphp
                                                <span class="badge {{ $lunas ? 'bg-primary' : 'bg-danger' }}">
                                                    <i
                                                        class="bi {{ $lunas ? 'bi-check-circle' : 'bi-x-circle' }} me-1"></i>
                                                    {{ $lunas ? 'Lunas' : 'Belum Lunas' }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="text-end">
                                            <span
                                                class="badge rounded-pill px-3 py-2 {{ $jual->jenis_transaksi === 'T' ? 'bg-success' : 'bg-warning text-dark' }}">
                                                <i
                                                    class="bi {{ $jual->jenis_transaksi === 'T' ? 'bi-cash' : 'bi-credit-card' }} me-1"></i>
                                                {{ $jual->jenis_transaksi === 'T' ? 'Tunai' : 'Kredit' }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </a>
                    @empty
                        <div class="text-center text-muted py-4">
                            <i class="bi bi-info-circle me-2"></i> Data penjualan belum tersedia.
                        </div>
                    @endforelse
                </div>
            </div>
            <div class="tab-pane fade" id="retur" role="tabpanel">
                <div class="list-group list-group-flush small">
                    @forelse ($returList as $retur)
                        <a href="{{ route('detailReturMobile', $retur->no_retur) }}" class="text-decoration-none">
                            <div class="card border-0 shadow-sm rounded-4 mb-3">
                                <div class="card-body p-3">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <div class="fw-bold text-danger mb-1">
                                                <i class="bi bi-arrow-counterclockwise me-1"></i> {{ $retur->no_retur }}
                                            </div>
                                            <div class="text-muted small mb-1">
                                                <i class="bi bi-calendar-event me-1"></i>
                                                {{ \Carbon\Carbon::parse($retur->tanggal)->format('d M Y') }}
                                            </div>
                                            <div class="text-muted small">
                                                <i class="bi bi-receipt me-1"></i>
                                                Faktur: <span class="fw-semibold text-dark">{{ $retur->no_faktur }}</span>
                                            </div>
                                            <div class="text-muted small mt-1">
                                                <i class="bi bi-cash-stack me-1"></i>
                                                Total Retur: <span class="fw-semibold text-dark">Rp
                                                    {{ number_format($retur->total, 0, ',', '.') }}</span>
                                            </div>
                                        </div>
                                        <div class="text-end">
                                            <span
                                                class="badge rounded-pill px-3 py-2
                                                                                                                                    {{ $retur->jenis_retur === 'GB' ? 'bg-info text-dark' : 'bg-secondary' }}">
                                                <i
                                                    class="bi {{ $retur->jenis_retur === 'GB' ? 'bi-arrow-repeat' : 'bi-receipt' }} me-1"></i>
                                                {{ $retur->jenis_retur === 'GB' ? 'Ganti Barang' : 'Potong Faktur' }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </a>
                    @empty
                        <div class="text-center text-muted py-4">
                            <i class="bi bi-info-circle me-2"></i> Data retur belum tersedia.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function(position) {
                    const lat = position.coords.latitude;
                    const long = position.coords.longitude;
                    const latInput = document.getElementById('latitude');
                    const longInput = document.getElementById('longitude');

                    if (latInput && longInput) {
                        latInput.value = lat;
                        longInput.value = long;
                    }

                    const map = L.map('map').setView([lat, long], 16);
                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        attribution: '&copy; OpenStreetMap contributors'
                    }).addTo(map);

                    L.marker([lat, long]).addTo(map)
                        .bindPopup("Lokasi Anda")
                        .openPopup();
                }, function(error) {
                    console.error("Gagal mendapatkan lokasi:", error.message);
                });
            } else {
                console.warn("Geolocation tidak didukung oleh browser.");
            }
        });
    </script>
@endsection
