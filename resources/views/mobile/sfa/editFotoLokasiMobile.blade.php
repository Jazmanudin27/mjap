@extends('mobile.layout')
@section('title', 'Foto Lokasi')
@section('header', 'Ambil Foto & Lokasi Toko')

@section('content')
    <div class="container py-3">
        <form action="{{ route('updateFotoLokasiPelanggan') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="kode_pelanggan" value="{{ $pelanggan->kode_pelanggan }}">
            <input type="hidden" name="latitude" id="latitude">
            <input type="hidden" name="longitude" id="longitude">
            <input type="hidden" name="foto" id="foto">

            {{-- Info Toko --}}
            <div class="mb-3 text-center">
                <h6 class="fw-bold text-dark mb-1">
                    <i class="bi bi-shop me-1 text-primary"></i>
                    {{ $pelanggan->nama_pelanggan }}
                </h6>
                <small class="text-muted d-block">{{ $pelanggan->alamat_pelanggan }}</small>
            </div>

            {{-- Kamera dan Ambil Foto --}}
            <div class="card shadow-sm border-0 rounded-4 mb-3">
                <div class="card-body text-center p-3">
                    <video id="camera" autoplay playsinline muted class="w-100 rounded shadow-sm"
                        style="max-height: 240px; border: 2px dashed #0d6efd;"></video>

                    <canvas id="canvas" class="d-none"></canvas>

                    <button type="button" class="btn btn-outline-primary mt-3" onclick="ambilFoto()">
                        <i class="bi bi-camera-fill me-1"></i> Ambil Foto
                    </button>

                    <div id="previewArea" class="d-none mt-3">
                        <p class="small text-muted">Preview:</p>
                        <img id="previewImg" class="img-thumbnail rounded shadow" style="max-height: 200px;">
                    </div>
                </div>
            </div>

            {{-- Peta Lokasi --}}
            <div class="card shadow-sm border-0 rounded-4 mb-3">
                <div class="card-body p-3">
                    <h6 class="fw-bold mb-2 text-success"><i class="bi bi-geo-alt-fill me-1"></i> Lokasi Toko</h6>
                    <div id="map" style="height: 240px; border-radius: 12px; overflow: hidden;"></div>
                    <div class="small text-muted mt-2">
                        <i class="bi bi-map me-1"></i> <span id="lokasiText">Mengambil lokasi...</span>
                    </div>
                </div>
            </div>

            {{-- Tombol Submit --}}
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-body text-center p-3">
                    <p class="text-muted small mb-2">Pastikan foto dan lokasi terlihat akurat sebelum dikirim.</p>
                    <button type="submit" class="btn btn-success w-100 shadow-sm py-2">
                        <i class="bi bi-cloud-upload-fill me-1"></i> Simpan Foto & Lokasi
                    </button>
                </div>
            </div>
        </form>
    </div>

    {{-- Leaflet.js dan Script Kamera --}}
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

    <script>
        const video = document.getElementById('camera');
        const canvas = document.getElementById('canvas');
        const previewImg = document.getElementById('previewImg');
        const fotoInput = document.getElementById('foto');
        const previewArea = document.getElementById('previewArea');
        const lokasiText = document.getElementById('lokasiText');

        navigator.mediaDevices.getUserMedia({
            video: { facingMode: "environment" }
        }).then(stream => video.srcObject = stream)
            .catch(err => {
                alert('Tidak bisa akses kamera belakang. Mencoba kamera depan...');
                navigator.mediaDevices.getUserMedia({ video: true })
                    .then(stream => video.srcObject = stream)
                    .catch(err => alert('Gagal mengakses kamera: ' + err.message));
            });

        function ambilFoto() {
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            canvas.getContext('2d').drawImage(video, 0, 0);
            let dataURL = canvas.toDataURL('image/jpeg');
            previewImg.src = dataURL;
            fotoInput.value = dataURL;
            previewArea.classList.remove('d-none');
        }

        // Lokasi & Peta
        let map;
        navigator.geolocation.getCurrentPosition(
            function (position) {
                const lat = position.coords.latitude;
                const lng = position.coords.longitude;

                document.getElementById('latitude').value = lat;
                document.getElementById('longitude').value = lng;
                lokasiText.innerText = `${lat.toFixed(5)}, ${lng.toFixed(5)}`;

                // Inisialisasi Peta
                map = L.map('map').setView([lat, lng], 17);

                // Tambahkan tile layer
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '© OpenStreetMap',
                    maxZoom: 19
                }).addTo(map);

                // Tambahkan marker
                L.marker([lat, lng]).addTo(map)
                    .bindPopup('Lokasi Toko')
                    .openPopup();
            },
            function (error) {
                lokasiText.innerText = 'Gagal mendapatkan lokasi';
                alert('Gagal mendapatkan lokasi: ' + error.message);
            }
        );
    </script>
@endsection
