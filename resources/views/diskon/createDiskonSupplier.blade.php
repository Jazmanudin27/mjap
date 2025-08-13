@extends('layouts.template')
@section('titlepage', 'Tambah Diskon Strata')
@section('contents')
    <div class="container-fluid p-0">
        <div class="col-xl-7">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Form Tambah Diskon Strata</h5>
                </div>
                <div class="card-body mt-4 mb-4">
                    <form action="{{ route('storeDiskonSupplier') }}" method="POST" autocomplete="off">
                        @csrf
                        <div class="row g-3">

                            <div class="col-md-6">
                                <label class="form-label">Kode Barang</label>
                                <select name="kode_barang" class="form-control form-control-sm select2" required>
                                    <option value="">-- Pilih Barang --</option>
                                    @foreach ($barang as $b)
                                        <option value="{{ $b->kode_barang }}">{{ $b->kode_barang }} - {{ $b->nama_barang }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Satuan</label>
                                <select name="satuan_id" class="form-control form-control-sm select2" required>
                                    <option value="">-- Pilih Satuan --</option>
                                    @foreach ($satuan as $s)
                                        <option value="{{ $s->id }}">{{ $s->satuan }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Persentase Diskon (%)</label>
                                <input type="number" step="0.01" name="persentase" class="form-control form-control-sm"
                                    placeholder="Masukkan Persentase" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Syarat (Nominal / Jumlah)</label>
                                <input type="number" name="syarat" class="form-control form-control-sm"
                                    placeholder="Masukkan Syarat" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Tipe Syarat</label>
                                <select name="tipe_syarat" class="form-control form-control-sm" required>
                                    <option value="">-- Pilih Tipe Syarat --</option>
                                    <option value="per_barang">Per Barang</option>
                                    <option value="per_supplier">Per Supplier</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Jenis Diskon</label>
                                <select name="jenis_diskon" class="form-control form-control-sm" required>
                                    <option value="">-- Pilih Jenis Diskon --</option>
                                    <option value="reguler">Reguler</option>
                                    <option value="promo">Promo</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Cash?</label>
                                <select name="cash" class="form-control form-control-sm">
                                    <option value="0">Tidak</option>
                                    <option value="1">Ya</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Supplier</label>
                                <select name="supplier" class="form-control form-control-sm select2">
                                    <option value="">-- Pilih Supplier --</option>
                                    @foreach ($suppliers as $sup)
                                        <option value="{{ $sup->kode_supplier }}">{{ $sup->nama_supplier }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary w-100 mt-2">💾 Simpan Diskon</button>
                            </div>

                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Select2 --}}
    <script>
        $(document).ready(function() {
            $('.select2').select2({
                width: '100%',
                dropdownAutoWidth: true
            });
        });
    </script>
@endsection
