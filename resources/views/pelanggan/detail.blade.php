@extends('layouts.template')
@section('titlepage', 'Pelanggan')
@section('contents')
    <div class="container-fluid p-0">
        <div class="col-12 col-lg-10 mx-auto">
            <div class="card border-0 shadow-sm rounded">
                <div class="card-body p-4">
                    <h4 class="mb-4 text-center text-primary">
                        <i class="bi bi-person-lines-fill me-2"></i> Form Edit Pelanggan
                    </h4>

                    <form action="{{ route('updatePelanggan') }}" method="POST" enctype="multipart/form-data"
                        autocomplete="off">
                        @csrf
                        @method('POST')

                        <div class="row g-3">
                            {{-- Kolom Kiri --}}
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-primary">
                                    <i class="bi bi-person-badge me-1"></i> Kode Pelanggan
                                </label>
                                <input type="text" name="kode_pelanggan" class="form-control rounded-pill shadow-sm"
                                    value="{{ $pelanggan->kode_pelanggan }}" readonly>

                                <label class="form-label fw-semibold text-primary mt-3">
                                    <i class="bi bi-person me-1"></i> Nama Pelanggan
                                </label>
                                <input type="text" name="nama_pelanggan" class="form-control rounded-pill shadow-sm"
                                    value="{{ $pelanggan->nama_pelanggan }}" required>

                                <label class="form-label fw-semibold text-primary mt-3">
                                    <i class="bi bi-geo-alt me-1"></i> Alamat Pelanggan
                                </label>
                                <input type="text" name="alamat_pelanggan" class="form-control rounded-pill shadow-sm"
                                    value="{{ $pelanggan->alamat_pelanggan }}">

                                <label class="form-label fw-semibold text-primary mt-3">
                                    <i class="bi bi-telephone me-1"></i> No HP
                                </label>
                                <input type="text" name="no_hp_pelanggan" class="form-control rounded-pill shadow-sm"
                                    value="{{ $pelanggan->no_hp_pelanggan }}">

                                <label class="form-label fw-semibold text-primary mt-3">
                                    <i class="bi bi-toggle-on me-1"></i> Status
                                </label>
                                <select name="status" class="form-select rounded-pill shadow-sm">
                                    <option value="1" {{ $pelanggan->status == '1' ? 'selected' : '' }}>Aktif</option>
                                    <option value="0" {{ $pelanggan->status == '0' ? 'selected' : '' }}>Nonaktif
                                    </option>
                                </select>
                            </div>

                            {{-- Kolom Kanan --}}
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-primary">
                                    <i class="bi bi-house-door me-1"></i> Kepemilikan
                                </label>
                                <select name="kepemilikan" class="form-select rounded-pill shadow-sm">
                                    <option value="">-- Pilih Kepemilikan --</option>
                                    @foreach (['Pribadi', 'Sewa', 'Milik Keluarga', 'Lainnya'] as $item)
                                        <option value="{{ $item }}"
                                            {{ $pelanggan->kepemilikan == $item ? 'selected' : '' }}>
                                            {{ $item }}
                                        </option>
                                    @endforeach
                                </select>

                                <label class="form-label fw-semibold text-primary mt-3">
                                    <i class="bi bi-graph-up-arrow me-1"></i> Omset Toko
                                </label>
                                <input type="number" name="omset_toko" class="form-control rounded-pill shadow-sm"
                                    value="{{ $pelanggan->omset_toko }}">

                                <label class="form-label fw-semibold text-primary mt-3">
                                    <i class="bi bi-wallet2 me-1"></i> Limit Pelanggan
                                </label>
                                <input type="number" name="limit_pelanggan" class="form-control rounded-pill shadow-sm"
                                    value="{{ $pelanggan->limit_pelanggan }}">

                                <label class="form-label fw-semibold text-primary mt-3">
                                    <i class="bi bi-calendar-event me-1"></i> Hari Kunjungan
                                </label>
                                <select name="hari" class="form-select rounded-pill shadow-sm">
                                    <option value="">-- Pilih Hari --</option>
                                    @foreach (['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'] as $hari)
                                        <option value="{{ $hari }}"
                                            {{ $pelanggan->hari == $hari ? 'selected' : '' }}>{{ $hari }}
                                        </option>
                                    @endforeach
                                </select>

                                <label class="form-label fw-semibold text-primary mt-3">
                                    <i class="bi bi-repeat me-1"></i> Frekuensi Kunjungan
                                </label>
                                <select name="kunjungan" class="form-select rounded-pill shadow-sm">
                                    <option value="">-- Pilih Frekuensi --</option>
                                    <option value="1" {{ $pelanggan->kunjungan == '1' ? 'selected' : '' }}>
                                        Mingguan</option>
                                    <option value="2" {{ $pelanggan->kunjungan == '2' ? 'selected' : '' }}>2 Minggu
                                        Sekali
                                    </option>
                                </select>
                            </div>


                            {{-- Kepemilikan --}}
                            <div class="col-md-12">
                                <label class="form-label fw-semibold text-primary">
                                    <i class="bi bi-house-door me-1"></i> Wilayah
                                </label>
                                <select name="wilayah" class="form-select form-select2 form-select-sm" required>
                                    <option value="">Pilih Wilayah</option>
                                    @foreach ($wilayahList as $kode => $nama)
                                        <option value="{{ $kode }}"
                                            {{ $pelanggan->kode_wilayah == $kode ? 'selected' : '' }}>{{ $nama }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Upload Foto --}}
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-primary mt-3">
                                    <i class="bi bi-image me-1"></i> Upload Foto
                                </label>
                                <input type="file" name="foto" class="form-control rounded-pill shadow-sm"
                                    onchange="previewImage(event)">
                            </div>


                            {{-- Preview Foto --}}
                            <div class="col-md-6 text-center">
                                <label class="form-label fw-semibold text-primary mt-3 d-block">Preview Foto</label>
                                <img id="preview"
                                    src="{{ $pelanggan->foto ? asset('storage/pelanggan/' . $pelanggan->foto) : '' }}"
                                    class="img-thumbnail"
                                    style="max-height: 120px; {{ $pelanggan->foto ? '' : 'display: none;' }}">
                            </div>

                            {{-- Tombol Simpan --}}
                            <div class="col-12 mt-3">
                                <button type="submit" class="btn btn-primary w-100 rounded-pill shadow-sm">
                                    <i class="bi bi-save me-1"></i> Simpan Perubahan
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
