@extends('layouts.template')
@section('titlepage', 'Tambah Setoran Penjualan')
@section('contents')
    <div class="container-fluid p-0">
        <div class="col-12 col-xl-6">
            <div class="card">
                <div class="card-body mt-3">
                    <h1 class="h4 mb-3 fw-bold">Form Tambah Setoran Penjualan</h1>
                    <div class="row">
                        <div class="col-12 col-xl-12">
                            <form action="{{ route('setoran.store') }}" method="POST" autocomplete="off">
                                @csrf
                                <div class="mb-3">
                                    <input type="text" name="kode_setoran" class="form-control" placeholder="Kode Setoran"
                                        required>
                                </div>
                                <div class="mb-3">
                                    <input type="date" name="tanggal" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <select name="kode_sales" class="form-control form-select2" required>
                                        <option value="">-- Pilih Sales --</option>
                                        @foreach($sales as $s)
                                            <option value="{{ $s->nik }}">{{ $s->nama_lengkap }} ({{ $s->nik }})</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <input type="number" name="lhp_tunai" class="form-control" placeholder="LHP Tunai"
                                        required>
                                </div>
                                <div class="mb-3">
                                    <input type="number" name="lhp_tagihan" class="form-control" placeholder="LHP Tagihan"
                                        required>
                                </div>
                                <div class="mb-3">
                                    <input type="number" name="setoran_kertas" class="form-control"
                                        placeholder="Setoran Kertas" required>
                                </div>
                                <div class="mb-3">
                                    <input type="number" name="setoran_logam" class="form-control"
                                        placeholder="Setoran Logam" required>
                                </div>
                                <div class="mb-3">
                                    <input type="number" name="setoran_lainnya" class="form-control"
                                        placeholder="Setoran Lainnya">
                                </div>
                                <div class="mb-3">
                                    <input type="number" name="setoran_giro" class="form-control"
                                        placeholder="Setoran Giro">
                                </div>
                                <div class="mb-3">
                                    <input type="number" name="setoran_transfer" class="form-control"
                                        placeholder="Setoran Transfer">
                                </div>
                                <div class="mb-3">
                                    <input type="number" name="giro_to_cash" class="form-control"
                                        placeholder="Giro ke Cash">
                                </div>
                                <div class="mb-3">
                                    <input type="number" name="giro_to_transfer" class="form-control"
                                        placeholder="Giro ke Transfer">
                                </div>
                                <div class="mb-3">
                                    <textarea name="keterangan" class="form-control" placeholder="Keterangan"></textarea>
                                </div>
                                <button type="submit" class="btn btn-primary w-100">Simpan Setoran</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Select2 --}}
    <script>
        $(document).ready(function () {
            $('.select2').select2({
                width: '100%',
                dropdownAutoWidth: true
            });
        });
    </script>
@endsection
