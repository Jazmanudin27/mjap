@extends('layouts.template')
@section('titlepage', 'Peta Pelanggan')
@section('contents')

    <style>
        #map {
            height: 58vh;
            width: 100%;
            border-radius: 0.75rem;
        }

        .leaflet-popup-content-wrapper {
            font-size: 13px;
        }

        .popup-content {
            text-align: center;
        }

        .popup-content img {
            width: 150px;
            height: 150px;
            object-fit: cover;
            border-radius: 15%;
            margin-bottom: 8px;
            border: 3px solid #fff;
            box-shadow: 0 0 8px rgba(0, 0, 0, 0.5);
        }

        .popup-content strong {
            display: block;
            font-size: 14px;
            margin-bottom: 4px;
        }
    </style>

    <div class="container-fluid p-0">
        <div class="row">
            <div class="col-12">
                <div class="card shadow-lg border-0 rounded-4">
                    <div
                        class="card-header bg-primary text-white rounded-top-4 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-bold"><i class="fa fa-map-marker-alt me-2"></i> Peta Pelanggan</h5>
                    </div>
                    <div class="card-body mt-3">
                        <div class="row g-2 mb-4">
                            <div class="col-md-6">
                                <select id="kode_pelanggan" name="kode_pelanggan" class="form-select form-select-sm"
                                    tabindex="1"></select>
                            </div>
                            <div class="col-md-2">
                                <select name="status" id="status" class="form-select2 form-select-sm">
                                    <option value="">Semua Status</option>
                                    <option value="1">Aktif</option>
                                    <option value="0">Nonaktif</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select name="wilayah" id="wilayah" class="form-select2 form-select-sm">
                                    <option value="">Semua Wilayah</option>
                                    @foreach ($wilayah as $w)
                                        <option value="{{ $w->kode_wilayah }}">{{ $w->nama_wilayah }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-1 d-grid">
                                <button class="btn btn-primary btn-sm" id="btn-filter">
                                    <i class="fa fa-search"></i>
                                </button>
                            </div>
                        </div>
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
        $(document).ready(function() {
            $('#kode_pelanggan').select2({
                placeholder: 'Cari pelanggan…',
                dropdownParent: $('#kode_pelanggan').parent(),
                ajax: {
                    url: "{{ route('getPelanggan') }}",
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            kode_pelanggan: params.term
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: data.results
                        };
                    },
                    cache: true
                }
            });
        });

        document.addEventListener('DOMContentLoaded', function() {
            const map = L.map('map').setView([-7.293305025418249, 108.21139388280555], 13);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap contributors'
            }).addTo(map);

            let markers = [];

            const customers = @json($customers);

            function getPhoto(photoUrl) {
                return photoUrl ? `{{ url('storage/pelanggan') }}/${photoUrl}` :
                    '{{ asset('images/default-user.png') }}';
            }

            function addMarkers(customers) {
                customers.forEach(customer => {
                    if (customer.latitude && customer.longitude) {
                        const lat = parseFloat(customer.latitude);
                        const lng = parseFloat(customer.longitude);

                        const popupContent = `
                            <div class="popup-content">
                                <img src="${getPhoto(customer.foto)}" alt="${customer.nama_pelanggan}">
                                <strong>${customer.nama_pelanggan}</strong>
                                ${customer.alamat_pelanggan}<br>
                                Wilayah: ${customer.nama_wilayah || '-'}<br>
                            </div>
                        `;

                        const marker = L.marker([lat, lng]).addTo(map).bindPopup(popupContent);
                        marker.on('click', function() {
                            map.setView([lat, lng], 13);
                        });
                        markers.push(marker);
                    }
                });
            }

            addMarkers(customers);

            function applyFilter() {
                const kode = document.getElementById('kode_pelanggan').value.trim();
                const status = document.getElementById('status').value;
                const wilayah = document.getElementById('wilayah').value;

                markers.forEach(marker => map.removeLayer(marker));
                markers = [];

                const filtered = customers.filter(cust => {
                    const matchKode = !kode || cust.kode_pelanggan.includes(kode);
                    const matchStatus = !status || cust.status == status;
                    const matchWilayah = !wilayah || cust.kode_wilayah == wilayah;
                    return matchKode && matchStatus && matchWilayah;
                });

                addMarkers(filtered);
            }

            document.getElementById('btn-filter').addEventListener('click', applyFilter);
        });
    </script>

@endsection
