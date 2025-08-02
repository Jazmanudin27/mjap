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

                <form action="{{ route('updatePelanggan') }}" method="POST" enctype="multipart/form-data" autocomplete="off">
                    @csrf
                    @method('POST')

                    <div class="row g-4">
                        {{-- Kolom Kiri --}}
                        <div class="col-md-6">
                            <div class="mb-2">
                                <label class="form-label small fw-semibold">
                                    <i class="bi bi-person-badge"></i> Kode Pelanggan
                                </label>
                                <input type="text" name="kode_pelanggan" class="form-control form-control-sm"
                                    value="{{ $pelanggan->kode_pelanggan }}" readonly>
                            </div>

                            <div class="mb-2">
                                <label class="form-label small fw-semibold">
                                    <i class="bi bi-person"></i> Nama Pelanggan
                                </label>
                                <input type="text" name="nama_pelanggan" class="form-control form-control-sm"
                                    value="{{ $pelanggan->nama_pelanggan }}" required>
                            </div>

                            <div class="mb-2">
                                <label class="form-label small fw-semibold">
                                    <i class="bi bi-geo-alt"></i> Alamat
                                </label>
                                <input type="text" name="alamat_pelanggan" class="form-control form-control-sm"
                                    value="{{ $pelanggan->alamat_pelanggan }}">
                            </div>

                            <div class="mb-2">
                                <label class="form-label small fw-semibold">
                                    <i class="bi bi-phone"></i> No HP
                                </label>
                                <input type="text" name="no_hp_pelanggan" class="form-control form-control-sm"
                                    value="{{ $pelanggan->no_hp_pelanggan }}">
                            </div>

                            <div class="mb-2">
                                <label class="form-label small fw-semibold">
                                    <i class="bi bi-check-circle"></i> Status
                                </label>
                                <select name="status" class="form-select form-select-sm" required>
                                    <option value="1" {{ $pelanggan->status == '1' ? 'selected' : '' }}>Aktif</option>
                                    <option value="0" {{ $pelanggan->status == '0' ? 'selected' : '' }}>Nonaktif</option>
                                </select>
                            </div>
                        </div>

                        {{-- Kolom Kanan --}}
                        <div class="col-md-6">
                            <div class="mb-2">
                                <label class="form-label small fw-semibold">
                                    <i class="bi bi-person-check"></i> Kepemilikan
                                </label>
                                <input type="text" name="kepemilikan" class="form-control form-control-sm"
                                    value="{{ $pelanggan->kepemilikan }}">
                            </div>

                            <div class="mb-2">
                                <label class="form-label small fw-semibold">
                                    <i class="bi bi-graph-up-arrow"></i> Omset Toko
                                </label>
                                <input type="number" name="omset_toko" class="form-control form-control-sm"
                                    value="{{ $pelanggan->omset_toko }}">
                            </div>

                            <div class="mb-2">
                                <label class="form-label small fw-semibold">
                                    <i class="bi bi-wallet2"></i> Limit Pelanggan
                                </label>
                                <input type="number" name="limit_pelanggan" class="form-control form-control-sm"
                                    value="{{ $pelanggan->limit_pelanggan }}">
                            </div>

                            <div class="mb-2">
                                <label class="form-label small fw-semibold">
                                    <i class="bi bi-bank"></i> Bank
                                </label>
                                <input type="text" name="bank" class="form-control form-control-sm"
                                    value="{{ $pelanggan->bank }}">
                            </div>

                            <div class="mb-2">
                                <label class="form-label small fw-semibold">
                                    <i class="bi bi-credit-card-2-front"></i> No Rekening
                                </label>
                                <input type="text" name="no_rekening" class="form-control form-control-sm"
                                    value="{{ $pelanggan->no_rekening }}">
                            </div>

                            <div class="mb-2">
                                <label class="form-label small fw-semibold">
                                    <i class="bi bi-person-vcard"></i> Pemilik Rekening
                                </label>
                                <input type="text" name="pemilik_rekening" class="form-control form-control-sm"
                                    value="{{ $pelanggan->pemilik_rekening }}">
                            </div>
                        </div>

                        {{-- Upload Foto --}}
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">
                                <i class="bi bi-upload"></i> Upload Foto
                            </label>
                            <input type="file" name="foto" class="form-control form-control-sm" onchange="previewImage(event)">
                        </div>

                        <div class="col-md-6 text-center">
                            <label class="form-label small fw-semibold d-block">Preview Foto</label>
                            <img id="preview"
                                src="{{ $pelanggan->foto ? asset('storage/pelanggan/' . $pelanggan->foto) : '' }}"
                                class="img-thumbnail"
                                style="max-height: 120px; {{ $pelanggan->foto ? '' : 'display: none;' }}">
                        </div>

                        <div class="col-12 mt-2">
                            <button type="submit" class="btn btn-primary w-100 shadow-sm">
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
        reader.onload = function () {
            const output = document.getElementById('preview');
            output.src = reader.result;
            output.style.display = 'block';
        };
        reader.readAsDataURL(event.target.files[0]);
    }
</script>
@endsection
