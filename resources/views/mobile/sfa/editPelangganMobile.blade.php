@extends('mobile.layout')
@section('title', 'Tambah Pelanggan')
@section('header', 'Tambah Data Pelanggan')

@section('content')
    <div class="container py-3">
        <form action="{{ route('storePelangganMobile') }}" method="POST" enctype="multipart/form-data" autocomplete="off">
            @csrf

            {{-- Informasi Dasar --}}
            <div class="card border-0 shadow-sm rounded-4 mb-3">
                <div class="card-body">
                    <h6 class="fw-bold text-primary mb-3"><i class="bi bi-person-circle me-1"></i> Info Pelanggan</h6>

                    <div class="mb-2">
                        <label class="form-label small fw-semibold">Nama Pelanggan</label>
                        <input type="text" name="nama_pelanggan" class="form-control form-control-sm" required>
                    </div>

                    <div class="mb-2">
                        <label class="form-label small fw-semibold">Alamat Toko</label>
                        <input type="text" name="alamat_toko" class="form-control form-control-sm">
                    </div>

                    <div class="mb-2">
                        <label class="form-label small fw-semibold">Alamat Pelanggan</label>
                        <input type="text" name="alamat_pelanggan" class="form-control form-control-sm">
                    </div>

                    <div class="mb-2">
                        <label class="form-label small fw-semibold">No HP</label>
                        <input type="text" name="no_hp_pelanggan" class="form-control form-control-sm">
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

            {{-- Informasi Tambahan (Hidden by default) --}}
            <div class="card border-0 shadow-sm rounded-4 mb-3" hidden>
                <div class="card-body">
                    <h6 class="fw-bold text-primary mb-3"><i class="bi bi-info-circle me-1"></i> Info Tambahan</h6>

                    <div class="mb-2">
                        <label class="form-label small fw-semibold">Kepemilikan</label>
                        <input type="text" name="kepemilikan" class="form-control form-control-sm">
                    </div>

                    <div class="mb-2">
                        <label class="form-label small fw-semibold">Omset Toko</label>
                        <input type="number" name="omset_toko" class="form-control form-control-sm">
                    </div>

                    <div class="mb-2">
                        <label class="form-label small fw-semibold">Limit Pelanggan</label>
                        <input type="number" name="limit_pelanggan" class="form-control form-control-sm">
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
