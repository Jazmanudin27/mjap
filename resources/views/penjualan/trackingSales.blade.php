@extends('layouts.template')
@section('titlepage', 'Tracking Salesman')
@section('contents')

    <style>
        #map {
            height: 58vh;
            width: 100%;
        }

        .leaflet-popup-content-wrapper {
            font-size: 14px;
        }
    </style>

    <div class="container-fluid p-0">
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm border-0 rounded-4">
                    <div
                        class="card-header bg-primary text-white rounded-top-4 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-bold"><i class="fa fa-map-marker-alt me-2"></i> Tracking Salesman</h5>
                    </div>
                    <div class="card-body mt-3">
                        <form method="GET" action="{{ route('trackingSales') }}" class="mb-4">
                            <div class="row g-2">
                                <div class="col-md-3">
                                    @php
                                        $salesList = DB::table('hrd_karyawan')
                                            ->where('status', '1')
                                            ->where('id_jabatan', '1')
                                            ->select('nik', 'nama_lengkap')
                                            ->get();
                                    @endphp
                                    <select name="kode_sales" id="kode_sales" class="form-select2 form-select-sm">
                                        <option value="">-- Semua Sales --</option>
                                        @foreach ($salesList as $s)
                                            <option value="{{ $s->nik }}"
                                                {{ request('kode_sales') == $s->nik ? 'selected' : '' }}>
                                                {{ $s->nik }} - {{ $s->nama_lengkap }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <input type="date" name="tanggal" id="tanggal" class="form-control form-control-sm"
                                        value="{{ Date('Y-m-d') }}">
                                </div>
                                <div class="col-md-1 d-grid">
                                    <button class="btn btn-primary btn-sm" id="btn-filter">
                                        <i class="fa fa-search"></i>
                                    </button>
                                </div>
                            </div>
                        </form>
                        <div id="map"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Leaflet -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <script>
        function dateIndo2(dateString) {
            const options = {
                year: 'numeric',
                month: 'long',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            };
            const date = new Date(dateString);
            return new Intl.DateTimeFormat('id-ID', options).format(date);
        }

        function getColorForSales(salesNameOrId) {
            const colors = [
                'red', 'blue', 'green', 'orange', 'purple', 'darkred',
                'lightblue', 'darkgreen', 'pink', 'cadetblue',
                'darkpurple', 'beige', 'darkblue', 'lightgreen', 'gray'
            ];

            let input = salesNameOrId || 'unknown';
            let hash = 0;
            for (let i = 0; i < input.length; i++) {
                hash += input.charCodeAt(i);
            }

            // Pastikan warna selalu ada
            const index = Math.abs(hash) % colors.length;
            return colors[index];
        }

        function createCustomIcon(color) {
            if (!color) {
                console.error('Warna tidak valid!');
                color = 'red';
            }

            return L.icon({
                iconUrl: `https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png`,
                shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/images/marker-shadow.png ',
                iconSize: [25, 41],
                iconAnchor: [12, 41],
                popupAnchor: [1, -34],
                shadowSize: [41, 41]
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
            const map = L.map('map').setView([-7.293305025418249, 108.21139388280555], 10);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap contributors'
            }).addTo(map);

            let markers = [];

            const logs = @json($logs);

            function addMarkers(data) {
                data.forEach(log => {
                    if (log.latitude && log.longitude) {
                        const lat = parseFloat(log.latitude);
                        const lng = parseFloat(log.longitude);
                        const color = getColorForSales(log.nama_lengkap || log.kode_sales);
                        const customIcon = createCustomIcon(color);
                        const marker = L.marker([lat, lng], {
                            icon: customIcon
                        }).addTo(map);

                        // Hitung durasi kunjungan
                        let duration = '<span class="text-danger">Belum Checkout</span>';
                        if (log.checkout) {
                            const start = new Date(log.checkin);
                            const end = new Date(log.checkout);
                            const diffMins = Math.round((end - start) / 60000);
                            duration = `${diffMins} menit`;
                        }

                        const fotoUrl = log.foto_pelanggan ?
                            `storage/pelanggan/${log.foto_pelanggan}` :
                            'https://upload.wikimedia.org/wikipedia/commons/thumb/6/65/No-Image-Placeholder.svg/1665px-No-Image-Placeholder.svg.png';

                        marker.bindPopup(`
                        <div style="max-width: 250px;">
                            <div class="text-center mb-2">
                                <img src="${fotoUrl}" alt="Foto Pelanggan" class="rounded" style="width: 100px; height: 100px; object-fit: cover;">
                            </div>
                            <strong>Sales:</strong> ${log.nama_lengkap || log.kode_sales}<br>
                            <strong>Nama Pelanggan:</strong> ${log.nama_pelanggan || '-'}<br>
                            <strong>Alamat:</strong> ${log.alamat_toko || '-'}<br>
                            <strong>Wilayah:</strong> ${log.nama_wilayah || '-'}<br>
                            <strong>Check-in:</strong> ${dateIndo2(log.checkin)}<br>
                            <strong>Durasi:</strong> ${duration}<br>
                            <strong>Catatan:</strong> ${log.catatan || '-'}
                        </div>
                    `);

                        markers.push(marker);
                    }
                });
            }

            function applyFilter() {
                const kodeSales = document.getElementById('kode_sales').value.trim();
                const kodePelanggan = document.getElementById('kode_pelanggan').value.trim();
                const tanggal = document.getElementById('tanggal').value.trim();

                markers.forEach(marker => map.removeLayer(marker));
                markers = [];

                const filtered = logs.filter(log => {
                    return (!kodeSales || (log.nama_lengkap || '').toLowerCase().includes(kodeSales
                            .toLowerCase()) || log.kode_sales.includes(kodeSales)) &&
                        (!kodePelanggan || log.kode_pelanggan.includes(kodePelanggan)) &&
                        (!tanggal || log.tanggal === tanggal);
                });

                addMarkers(filtered);
            }

            document.getElementById('btn-filter').addEventListener('click', applyFilter);

            // Tampilkan semua marker awal
            addMarkers(logs);
        });
    </script>

@endsection
