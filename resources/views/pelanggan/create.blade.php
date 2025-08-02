@extends('layouts.template')
@section('titlepage', 'Tambah Pelanggan')
@section('contents')
    <div class="container-fluid p-0">
        <div class="col-12 col-xl-8 mx-auto">
            <div class="card shadow-lg">
                <div class="card-body mt-3">
                    <h1 class="h3 mb-4 text-center text-primary">Tambah Pelanggan</h1>

                    <form action="{{ route('storePelanggan') }}" method="POST" enctype="multipart/form-data"
                        autocomplete="off">
                        @csrf
                        <div class="row g-3">

                            {{-- Kode Pelanggan --}}
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-primary">
                                    <i class="bi bi-person-badge me-1"></i> Kode Pelanggan
                                </label>
                                <input type="text" name="kode_pelanggan"
                                    class="form-control form-control-sm rounded-pill shadow-sm"
                                    value="{{ $kodePelanggan ?? '' }}" readonly>
                            </div>

                            {{-- Nama Pelanggan --}}
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-primary">
                                    <i class="bi bi-person me-1"></i> Nama Pelanggan
                                </label>
                                <input type="text" name="nama_pelanggan"
                                    class="form-control form-control-sm rounded-pill shadow-sm" placeholder="Nama Pelanggan"
                                    required>
                            </div>

                            {{-- Alamat Pelanggan --}}
                            <div class="col-md-12">
                                <label class="form-label fw-semibold text-primary">
                                    <i class="bi bi-geo-alt me-1"></i> Alamat Pelanggan
                                </label>
                                <input type="text" name="alamat_pelanggan"
                                    class="form-control form-control-sm rounded-pill shadow-sm" placeholder="Alamat"
                                    required>
                            </div>

                            {{-- No HP --}}
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-primary">
                                    <i class="bi bi-telephone me-1"></i> No HP
                                </label>
                                <input type="text" name="no_hp_pelanggan"
                                    class="form-control form-control-sm rounded-pill shadow-sm" placeholder="08xxxxx"
                                    required>
                            </div>

                            {{-- Kepemilikan --}}
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-primary">
                                    <i class="bi bi-house-door me-1"></i> Kepemilikan
                                </label>
                                <select name="kepemilikan" class="form-select form-select-sm rounded-pill shadow-sm"
                                    required>
                                    <option value="" disabled selected>-- Pilih Kepemilikan --</option>
                                    <option value="Pribadi">Pribadi</option>
                                    <option value="Sewa">Sewa</option>
                                    <option value="Milik Keluarga">Milik Keluarga</option>
                                    <option value="Lainnya">Lainnya</option>
                                </select>
                            </div>

                            {{-- Hari Kunjungan --}}
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-primary">
                                    <i class="bi bi-calendar-event me-1"></i> Hari Kunjungan
                                </label>
                                <select name="hari" class="form-select form-select-sm rounded-pill shadow-sm">
                                    <option value="" disabled selected>-- Pilih Hari --</option>
                                    @foreach (['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'] as $hari)
                                        <option value="{{ $hari }}"
                                            {{ old('hari', $pelanggan->hari ?? '') == $hari ? 'selected' : '' }}>
                                            {{ $hari }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Frekuensi Kunjungan --}}
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-primary">
                                    <i class="bi bi-repeat me-1"></i> Frekuensi Kunjungan
                                </label>
                                <select name="frekuensi_kunjungan"
                                    class="form-select form-select-sm rounded-pill shadow-sm">
                                    <option value="" disabled selected>-- Pilih Frekuensi --</option>
                                    <option value="mingguan"
                                        {{ old('frekuensi_kunjungan', $pelanggan->frekuensi_kunjungan ?? '') == 'mingguan' ? 'selected' : '' }}>
                                        Mingguan</option>
                                    <option value="dua_mingguan"
                                        {{ old('frekuensi_kunjungan', $pelanggan->frekuensi_kunjungan ?? '') == 'dua_mingguan' ? 'selected' : '' }}>
                                        2 Minggu
                                        Sekali</option>
                                </select>
                            </div>

                            {{-- Status --}}
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-primary">
                                    <i class="bi bi-toggle-on me-1"></i> Status
                                </label>
                                <select name="status" class="form-select form-select-sm rounded-pill shadow-sm" required>
                                    <option value="Aktif">Aktif</option>
                                    <option value="Nonaktif">Nonaktif</option>
                                </select>
                            </div>

                            {{-- Upload Foto --}}
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-primary">
                                    <i class="bi bi-image me-1"></i> Upload Foto
                                </label>
                                <input type="file" name="foto"
                                    class="form-control form-control-sm rounded-pill shadow-sm" id="foto" required
                                    onchange="previewImage(event)">
                            </div>

                            {{-- Preview Foto --}}
                            <div class="col-md-6 text-center">
                                <img id="preview" src="" alt="Preview Foto" class="img-thumbnail mt-2"
                                    style="max-width: 150px; display: none;">
                            </div>

                            {{-- Tombol Submit --}}
                            <div class="col-md-12 mt-3">
                                <button type="submit" class="btn btn-primary w-100 rounded-pill shadow">
                                    <i class="bi bi-save me-1"></i> Simpan
                                </button>
                            </div>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>

    <script>
        function previewImage(event) {
            var reader = new FileReader();
            reader.onload = function() {
                var output = document.getElementById('preview');
                output.src = reader.result;
                output.style.display = "block";
            }
            reader.readAsDataURL(event.target.files[0]);
        }
    </script>
@endsection
