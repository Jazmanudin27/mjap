@extends('mobile.presensi.layout')
@section('title', 'Dashboard')
@section('header', 'E-Presensi')
@section('content')
    <style>
        /* Container utama */
        .camera-wrapper {
            position: relative;
            width: 100%;
            max-width: 360px;
            margin: 1rem auto 0 auto;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 10px rgb(0 0 0 / 0.1);
            background: #000;
            aspect-ratio: 4 / 3;
        }

        video {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        /* Peta */
        #map {
            width: 100%;
            max-width: 360px;
            height: 180px;
            margin: 1rem auto 0 auto;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgb(0 0 0 / 0.1);
        }

        /* Tombol */
        .btn-group {
            display: flex;
            max-width: 360px;
            margin: 1rem auto;
            gap: 10px;
        }

        .btn-scan {
            flex: 1;
            font-size: 1.1rem;
            padding: 1rem 0;
            border: none;
            border-radius: 10px;
            color: white;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 8px;
            box-shadow: 0 4px 12px rgb(0 0 0 / 0.12);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .btn-scan:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgb(0 0 0 / 0.2);
        }

        .btn-masuk {
            background-color: #28a745;
        }

        .btn-pulang {
            background-color: #dc3545;
        }

        .cards {
            margin-left: 20px;
            margin-right: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
    </style>

    <nav class="navbar navbar-expand-lg navbar-dark bg-primary px-3 py-3 shadow-sm"
        style="border-bottom-left-radius: 25px; border-bottom-right-radius: 15px;">
        <div class="container-fluid justify-content-center">
            <img src="{{ asset('assets/img/PresenTech.jpg') }}" alt="Avatar" class="img-fluid rounded"
                style="max-width: 60%;">
        </div>
    </nav>
    <div class="cards">
        <div class="camera-wrapper">
            <video id="video" autoplay playsinline muted></video>
        </div>

        <div id="map"></div>

        <div class="btn-group">
            <button class="btn-scan btn-masuk" id="btnMasuk">
                <i class="bi bi-fingerprint"></i> Scan Masuk
            </button>
            <button class="btn-scan btn-pulang" id="btnPulang">
                <i class="bi bi-fingerprint"></i> Scan Pulang
            </button>
        </div>
    </div>
    <script>
        const video = document.getElementById('video');
        navigator.mediaDevices.getUserMedia({
                video: {
                    facingMode: 'user'
                }
            })
            .then(stream => video.srcObject = stream)
            .catch(err => alert("Tidak bisa akses kamera: " + err));

        const map = L.map('map').setView([-7.2931265, 108.2115288], 16);
        L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        let marker = null;
        navigator.geolocation.getCurrentPosition(pos => {
            const lat = pos.coords.latitude;
            const lng = pos.coords.longitude;
            map.setView([lat, lng], 16);
            if (marker) {
                marker.setLatLng([lat, lng]);
            } else {
                marker = L.marker([lat, lng]).addTo(map).bindPopup('Lokasi kamu').openPopup();
            }
        }, err => alert("Gagal mendapat lokasi: " + err.message));

        function captureSelfie() {
            const canvas = document.createElement('canvas');
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            const ctx = canvas.getContext('2d');
            ctx.drawImage(video, 0, 0);
            return canvas.toDataURL('image/jpeg');
        }

        function sendScan(type) {
            const selfie = captureSelfie();
            const nik = '{{ auth()->user()->nik ?? '' }}';

            navigator.geolocation.getCurrentPosition(pos => {
                const data = {
                    type: type,
                    nik: nik,
                    lat: pos.coords.latitude,
                    lng: pos.coords.longitude,
                    selfie: selfie,
                    _token: '{{ csrf_token() }}'
                };

                $.ajax({
                    url: '{{ route('storePresensi') }}',
                    method: 'POST',
                    data: data,
                    success: function(res) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Presensi Berhasil',
                            text: res.message || 'Berhasil melakukan presensi!',
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            window.location.href = "{{ route('viewDashboardPresensi') }}";
                        });
                    },
                    error: function(err) {
                        console.error(err);
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: err.responseJSON?.message || "Gagal menyimpan presensi!",
                        });
                    }
                });
            }, err => alert("Gagal ambil lokasi: " + err.message));
        }

        // EVENT LISTENER
        document.getElementById('btnMasuk').addEventListener('click', () => sendScan('Masuk'));
        document.getElementById('btnPulang').addEventListener('click', () => sendScan('Pulang'));
    </script>
@endsection
