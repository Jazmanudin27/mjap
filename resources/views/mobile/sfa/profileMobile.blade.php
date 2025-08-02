@extends('mobile.layout')
@section('title', 'Pelanggan')
@section('header', 'Data Pelanggan')
@section('content')

    <!-- Include Bootstrap JS & SweetAlert2 -->
    <style>
        .employee-card {
            max-width: 400px;
            margin: auto;
            background: linear-gradient(135deg, #007cf0, #00b7ff);
            border-radius: 20px;
            color: white;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
            overflow: hidden;
            font-family: 'Segoe UI', sans-serif;
            position: relative;
        }

        .employee-card::before {
            content: "";
            position: absolute;
            top: -20px;
            right: -20px;
            width: 100px;
            height: 100px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
        }

        .employee-header {
            padding: 20px;
            text-align: center;
            background: rgba(0, 0, 0, 0.1);
        }

        .employee-photo {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border: 3px solid #fff;
            border-radius: 50%;
            margin-bottom: 10px;
        }

        .employee-name {
            font-size: 20px;
            font-weight: bold;
        }

        .employee-nik {
            font-size: 13px;
            opacity: 0.9;
        }

        .employee-body {
            background: white;
            color: #333;
            padding: 20px;
            font-size: 14px;
        }

        .employee-body ul {
            padding-left: 0;
            list-style: none;
        }

        .employee-body li {
            margin-bottom: 8px;
        }

        .employee-status {
            position: absolute;
            top: 15px;
            left: 15px;
            padding: 5px 10px;
            font-size: 12px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 20px;
            backdrop-filter: blur(5px);
        }

        .employee-status.aktif {
            background: rgba(40, 167, 69, 0.8);
            color: #fff;
        }

        .employee-status.nonaktif {
            background: rgba(108, 117, 125, 0.8);
            color: #fff;
        }
    </style>

    <div class="container py-4 position-relative">
        <div class="position-absolute top-0 end-0 mt-4 me-3 d-flex gap-2">
            <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#modalGantiPassword">
                <i class="bi bi-key-fill"></i>
            </button>
            <button class="btn btn-sm btn-info text-white" data-bs-toggle="modal" data-bs-target="#modalUploadFoto">
                <i class="bi bi-upload"></i>
            </button>
            <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#modalEditKaryawan">
                <i class="bi bi-pencil-square"></i>
            </button>
        </div>
        <div class="employee-card mt-5">
            <div class="employee-status {{ $karyawan->status ? 'aktif' : 'nonaktif' }}">
                {{ $karyawan->status ? 'Aktif' : 'Nonaktif' }}
            </div>

            <div class="employee-header">
                <img src="{{ $karyawan->foto_karyawan
                    ? asset('storage/karyawan/' . $karyawan->foto_karyawan)
                    : 'https://www.pngplay.com/wp-content/uploads/12/User-Avatar-Profile-Transparent-Clip-Art-PNG.png' }}"
                    alt="Foto" class="employee-photo" id="foto-preview">
                <div class="employee-name">{{ $karyawan->nama_lengkap }}</div>
                <div class="employee-nik">NIK: {{ $karyawan->nik }}</div>
            </div>

            <div class="employee-body">
                <ul>
                    <li><i class="bi bi-geo-alt me-1"></i> {{ $karyawan->alamat }}</li>
                    <li><i class="bi bi-phone me-1"></i> {{ $karyawan->nomor_telepon }}</li>
                    <li><i class="bi bi-envelope me-1"></i> {{ $karyawan->email }}</li>
                    <li><i class="bi bi-calendar me-1"></i> Lahir:
                        {{ \Carbon\Carbon::parse($karyawan->tanggal_lahir)->format('d M Y') }}</li>
                    <li><i class="bi bi-calendar-check me-1"></i> Masuk:
                        {{ \Carbon\Carbon::parse($karyawan->tgl_masuk)->format('d M Y') }}</li>
                    <li><i class="bi bi-person-vcard me-1"></i> KTP: {{ $karyawan->nomor_ktp }}</li>
                    <li><i class="bi bi-mortarboard me-1"></i> Pendidikan: {{ $karyawan->pendidikan_terakhir }}</li>
                    <li><i class="bi bi-bank me-1"></i> {{ $karyawan->nama_bank }} - {{ $karyawan->nomor_rekening_bank }}
                    </li>
                    <li><i class="bi bi-gender-ambiguous me-1"></i>
                        {{ $karyawan->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}</li>
                    <li><i class="bi bi-heart-fill me-1"></i> {{ ucfirst($karyawan->status_pernikahan) }}
                        ({{ $karyawan->jumlah_anak }} anak)</li>
                    <li><i class="bi bi-receipt-cutoff me-1"></i> NPWP: {{ $karyawan->npwp }}</li>
                    @if ($karyawan->catatan)
                        <li><strong>Catatan:</strong> {{ $karyawan->catatan }}</li>
                    @endif
                </ul>
            </div>
        </div>
        {{--
        <div class="container mt-4">
            <div class="row justify-content-center g-2">
                <div class="col-6">
                    <button class="btn btn-sm btn-warning w-100" data-bs-toggle="modal"
                        data-bs-target="#modalGantiPassword">
                        <i class="bi bi-key-fill me-1"></i> Ganti Password
                    </button>
                </div>
                <div class="col-6">
                    <button class="btn btn-sm btn-info w-100 text-white" data-bs-toggle="modal"
                        data-bs-target="#modalUploadFoto">
                        <i class="bi bi-upload me-1"></i> Upload Foto
                    </button>
                </div>
                <div class="col-4">
                    <button class="btn btn-sm btn-primary w-100" data-bs-toggle="modal" data-bs-target="#modalEditKaryawan">
                        <i class="bi bi-pencil-square me-1"></i> Edit Data
                    </button>
                </div>
            </div>
        </div> --}}
    </div>

    <!-- Modal Ganti Password -->
    <div class="modal fade" id="modalGantiPassword" tabindex="-1" aria-labelledby="modalGantiPasswordLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <form action="{{ route('karyawan.password.update', $karyawan->nik) }}" method="POST" class="modal-content">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title" id="modalGantiPasswordLabel">Ganti Password</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label>Password Baru</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Konfirmasi Password</label>
                        <input type="password" name="password_confirmation" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Update Password</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Upload Foto -->
    <div class="modal fade" id="modalUploadFoto" tabindex="-1" aria-labelledby="modalUploadFotoLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form id="form-upload-foto" enctype="multipart/form-data" class="modal-content">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Upload Foto</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label>Pilih Foto</label>
                        <input type="file" name="foto_karyawan" class="form-control" accept="image/*" required
                            onchange="previewFoto(event)">
                        <img id="preview-image" src="#" alt="Preview" class="mt-3 rounded"
                            style="display:none; width:100px; height:100px; object-fit:cover;">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-success" onclick="uploadFoto()">Upload</button>
                </div>
            </form>
        </div>
    </div>
    <div class="modal fade" id="modalEditKaryawan" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="form-update-karyawan">
                    @csrf
                    <input type="hidden" name="nik" value="{{ $karyawan->nik }}">

                    <div class="modal-header">
                        <h5 class="modal-title">Edit Data Karyawan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <div class="mb-3">
                            <label>Nama Lengkap</label>
                            <input type="text" name="nama_lengkap" class="form-control"
                                value="{{ $karyawan->nama_lengkap }}">
                        </div>
                        <div class="mb-3">
                            <label>Alamat</label>
                            <textarea name="alamat" class="form-control">{{ $karyawan->alamat }}</textarea>
                        </div>
                        <div class="mb-3">
                            <label>Nomor Telepon</label>
                            <input type="text" name="nomor_telepon" class="form-control"
                                value="{{ $karyawan->nomor_telepon }}">
                        </div>
                        <div class="mb-3">
                            <label>Email</label>
                            <input type="email" name="email" class="form-control" value="{{ $karyawan->email }}">
                        </div>
                        <div class="mb-3">
                            <label>Tanggal Lahir</label>
                            <input type="date" name="tanggal_lahir" class="form-control"
                                value="{{ $karyawan->tanggal_lahir }}">
                        </div>
                        <div class="mb-3">
                            <label>Jenis Kelamin</label>
                            <select name="jenis_kelamin" class="form-control">
                                <option value="L" {{ $karyawan->jenis_kelamin == 'L' ? 'selected' : '' }}>Laki-laki
                                </option>
                                <option value="P" {{ $karyawan->jenis_kelamin == 'P' ? 'selected' : '' }}>Perempuan
                                </option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label>Tanggal Masuk</label>
                            <input type="date" name="tgl_masuk" class="form-control"
                                value="{{ $karyawan->tgl_masuk }}">
                        </div>
                        <div class="mb-3">
                            <label>Status Karyawan</label>
                            <input type="text" name="status_karyawan" class="form-control"
                                value="{{ $karyawan->status_karyawan }}">
                        </div>
                        <div class="mb-3">
                            <label>Nomor KTP</label>
                            <input type="text" name="nomor_ktp" class="form-control"
                                value="{{ $karyawan->nomor_ktp }}">
                        </div>
                        <div class="mb-3">
                            <label>NPWP</label>
                            <input type="text" name="npwp" class="form-control" value="{{ $karyawan->npwp }}">
                        </div>
                        <div class="mb-3">
                            <label>Pendidikan Terakhir</label>
                            <input type="text" name="pendidikan_terakhir" class="form-control"
                                value="{{ $karyawan->pendidikan_terakhir }}">
                        </div>
                        <div class="mb-3">
                            <label>Nomor Rekening Bank</label>
                            <input type="text" name="nomor_rekening_bank" class="form-control"
                                value="{{ $karyawan->nomor_rekening_bank }}">
                        </div>
                        <div class="mb-3">
                            <label>Nama Bank</label>
                            <input type="text" name="nama_bank" class="form-control"
                                value="{{ $karyawan->nama_bank }}">
                        </div>
                        <div class="mb-3">
                            <label>Status Pernikahan</label>
                            <input type="text" name="status_pernikahan" class="form-control"
                                value="{{ $karyawan->status_pernikahan }}">
                        </div>
                        <div class="mb-3">
                            <label>Jumlah Anak</label>
                            <input type="number" name="jumlah_anak" class="form-control"
                                value="{{ $karyawan->jumlah_anak }}">
                        </div>
                        <div class="mb-3">
                            <label>Catatan</label>
                            <textarea name="catatan" class="form-control">{{ $karyawan->catatan }}</textarea>
                        </div>
                        <div class="mb-3">
                            <label>Status</label>
                            <select name="status" class="form-control">
                                <option value="1" {{ $karyawan->status == 1 ? 'selected' : '' }}>Aktif</option>
                                <option value="0" {{ $karyawan->status == 0 ? 'selected' : '' }}>Nonaktif</option>
                            </select>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="button" class="btn btn-primary" onclick="updateDataKaryawan()">Simpan
                            Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- SweetAlert Success -->
    @if (session('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: '{{ session('success') }}',
                timer: 2000,
                showConfirmButton: false
            });
        </script>
    @endif

    <!-- JS Preview Foto Upload -->
    <script>
        function previewFoto(event) {
            const input = event.target;
            const reader = new FileReader();

            reader.onload = function() {
                const imgElement = document.getElementById('preview-image');
                imgElement.src = reader.result;
                imgElement.style.display = 'block';
            }

            if (input.files && input.files[0]) {
                reader.readAsDataURL(input.files[0]);
            }
        }

        function updateDataKaryawan() {
            const form = document.getElementById('form-update-karyawan');
            const formData = new FormData(form);
            const nik = formData.get('nik');
            fetch(`/mobile/karyawan/${nik}/update-data`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: formData
                })
                .then(response => {
                    if (!response.ok) throw response;
                    return response.json();
                })
                .then(data => {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: 'Data karyawan berhasil diupdate.',
                        timer: 2000,
                        showConfirmButton: false
                    });
                    // Tutup modal
                    const modalEdit = bootstrap.Modal.getInstance(document.getElementById('modalEditKaryawan'));
                    modalEdit.hide();
                })
                .catch(async (error) => {
                    console.error('Error Object:', error);
                    let message = 'Terjadi kesalahan saat menyimpan.';

                    if (error instanceof Response) {
                        const text = await error.text();
                        console.error('Response Text:', text);
                        message = 'Server Error: ' + error.status;
                    } else if (error instanceof SyntaxError) {
                        console.error('Syntax Error:', error.message);
                        message = 'Server mengembalikan format yang tidak sesuai (bukan JSON).';
                    } else {
                        console.error('Other Error:', error);
                    }

                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: message
                    });
                });
        }

        function uploadFoto() {
            const form = document.getElementById('form-upload-foto');
            const formData = new FormData(form);

            // Tambahkan _method agar Laravel tahu ini PUT request
            formData.append('_method', 'PUT');

            fetch("{{ route('karyawan.upload-foto.store', $karyawan->nik) }}", {
                    method: "POST", // Tetap POST karena fetch tidak bisa direct PUT with FormData
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: data.message,
                            timer: 2000,
                            showConfirmButton: false
                        });

                        // Update Foto Profil di Kartu
                        document.getElementById('foto-preview').src = data.new_foto_url;

                        // Tutup Modal
                        const modalUpload = bootstrap.Modal.getInstance(document.getElementById('modalUploadFoto'));
                        modalUpload.hide();
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: data.message
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Terjadi kesalahan saat mengupload.'
                    });
                });
        }
    </script>

@endsection
