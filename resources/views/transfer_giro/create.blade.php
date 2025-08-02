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
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Kode Pelanggan</label>
                                <input type="text" name="kode_pelanggan" class="form-control"
                                    value="{{ $kodePelanggan ?? '' }}" readonly>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nama Pelanggan</label>
                                <input type="text" name="nama_pelanggan" class="form-control" placeholder="Nama Pelanggan"
                                    required>
                            </div>

                            <div class="col-md-12 mb-3">
                                <label class="form-label">Alamat Pelanggan</label>
                                <input type="text" name="alamat_pelanggan" class="form-control" placeholder="Alamat"
                                    required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">No HP</label>
                                <input type="text" name="no_hp_pelanggan" class="form-control" placeholder="No HP" required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Kepemilikan</label>
                                <input type="text" name="kepemilikan" class="form-control" placeholder="Kepemilikan"
                                    required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Omset Toko</label>
                                <input type="number" name="omset_toko" class="form-control" placeholder="Omset" required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Limit Pelanggan</label>
                                <input type="number" name="limit_pelanggan" class="form-control" placeholder="Limit"
                                    required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Bank</label>
                                <input type="text" name="bank" class="form-control" placeholder="Nama Bank" required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">No Rekening</label>
                                <input type="text" name="no_rekening" class="form-control" placeholder="No Rekening"
                                    required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Pemilik Rekening</label>
                                <input type="text" name="pemilik_rekening" class="form-control"
                                    placeholder="Nama Pemilik Rekening" required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Salesman</label>
                                <select name="kode_sales" required class="form-select">
                                    <option value="">Pilih Salesman</option>
                                    @php
                                        $karyawan = DB::table('hrd_karyawan')
                                            ->where('hrd_karyawan.id_group', '8')
                                            ->where('hrd_karyawan.status', 'Aktif')
                                            ->orderBy('nama_lengkap', 'ASC')
                                            ->get();
                                    @endphp
                                    @foreach ($karyawan as $k)
                                        <option value="{{ $k->nik }}">{{ $k->nama_lengkap }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Metode Bayar</label>
                                <select name="metode_bayar" class="form-select" required>
                                    <option value="">Pilih Metode Bayar</option>
                                    <option value="TF">Transfer</option>
                                    <option value="CD">Cash</option>
                                </select>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Status</label>
                                <select name="status" class="form-select" required>
                                    <option value="Aktif">Aktif</option>
                                    <option value="Nonaktif">Nonaktif</option>
                                </select>
                            </div>

                            <!-- Upload Foto dengan Preview -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Upload Foto</label>
                                <input type="file" name="foto" class="form-control" id="foto" required
                                    onchange="previewImage(event)">
                            </div>

                            <div class="col-md-6 mb-3 text-center">
                                <img id="preview" src="" alt="Preview Foto" class="img-thumbnail"
                                    style="max-width: 150px; display: none;">
                            </div>

                            <div class="col-md-12 mt-4">
                                <button type="submit" class="btn btn-primary w-100">
                                    Simpan
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
            reader.onload = function () {
                var output = document.getElementById('preview');
                output.src = reader.result;
                output.style.display = "block";
            }
            reader.readAsDataURL(event.target.files[0]);
        }
    </script>
@endsection
