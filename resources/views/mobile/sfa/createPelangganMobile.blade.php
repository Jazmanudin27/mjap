@extends('mobile.layout')
@section('title', 'Tambah Pelanggan')
@section('header', 'Tambah Data Pelanggan')

@section('content')
    <div class="container py-3">
        <form action="{{ route('storePelangganMobile') }}" method="POST" enctype="multipart/form-data" autocomplete="off">
            @csrf

            {{-- Informasi Dasar --}}
            <div class="card border-0 shadow-sm rounded-4 mb-3">
                <div class="card-header bg-primary-subtle rounded-top-4 px-3 py-2">
                    <div class="d-flex align-items-center text-primary fw-bold small">
                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2"
                            style="width: 24px; height: 24px;">
                            <i class="bi bi-person-circle"></i>
                        </div>
                        Tambah Pelanggan
                    </div>
                </div>
                <div class="card-body pt-3">
                    <div class="mb-2">
                        <label class="form-label small fw-semibold">Nama Pelanggan</label>
                        <input type="text" name="nama_pelanggan" class="form-control form-control-sm" required>
                    </div>

                    <div class="mb-2">
                        <label class="form-label small fw-semibold">Alamat</label>
                        <input type="text" name="alamat_pelanggan" class="form-control form-control-sm">
                    </div>


                    <div class="mb-2">
                        <label class="form-label small fw-semibold">Alamat Toko</label>
                        <input type="text" name="alamat_toko" class="form-control form-control-sm">
                    </div>

                    <div class="mb-2">
                        <label class="form-label small fw-semibold">No HP</label>
                        <input type="text" name="no_hp_pelanggan" class="form-control form-control-sm">
                    </div>

                    <div class="mb-2">
                        <label class="form-label small fw-semibold">Wilayah</label>
                        <select name="wilayah" class="form-select form-select-sm" required>
                            <option value="">Pilih Wilayah</option>
                            @foreach ($wilayahList as $kode => $nama)
                                <option value="{{ $kode }}">{{ $nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-2">
                        <label class="form-label small fw-semibold">Hari Kunjungan</label>
                        <select name="hari" class="form-select form-select-sm">
                            <option value="">Pilih Hari</option>
                            @foreach (['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'] as $hari)
                                <option value="{{ $hari }}">{{ $hari }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-2">
                        <label class="form-label small fw-semibold">Frekuensi Kunjungan</label>
                        <select name="kunjungan" class="form-select form-select-sm">
                            <option value="">Pilih Frekuensi</option>
                            <option value="1">Mingguan</option>
                            <option value="2">Dua Mingguan</option>
                        </select>
                    </div>

                    <div class="mb-2">
                        <label class="form-label small fw-semibold">Status</label>
                        <select name="status" class="form-select form-select-sm">
                            <option value="1">Aktif</option>
                            <option value="0">Nonaktif</option>
                        </select>
                    </div>
                </div>
            </div>

            {{-- Upload Foto --}}
            <div class="card border-0 shadow-sm rounded-4 mb-3">
                <div class="card-body">
                    <h6 class="fw-bold text-primary mb-3"><i class="bi bi-camera me-1"></i> Foto Toko / Pemilik</h6>
                    <div class="mb-2">
                        <input type="file" name="foto" class="form-control form-control-sm"
                            onchange="previewImage(event)">
                    </div>
                    <div class="text-center">
                        <img id="preview" class="img-thumbnail" style="max-height: 120px; display: none;">
                    </div>
                </div>
            </div>

            {{-- Tombol Simpan --}}
            <div class="d-grid">
                <button type="submit" class="btn btn-success">
                    <i class="bi bi-plus-circle me-1"></i> Tambah Pelanggan
                </button>
            </div>
        </form>
    </div>

    <script>
        function previewImage(event) {
            const reader = new FileReader();
            reader.onload = function() {
                const output = document.getElementById('preview');
                output.src = reader.result;
                output.style.display = 'block';
            };
            reader.readAsDataURL(event.target.files[0]);
        }
    </script>
@endsection
