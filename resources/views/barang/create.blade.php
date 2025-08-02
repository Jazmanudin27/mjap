@extends('layouts.template')
@section('titlepage', 'Tambah Barang')
@section('contents')
    <div class="container-fluid p-0">
        <div class="col-12 col-xl-6">
            <div class="card">
                <div class="card-body mt-3">
                    <h1 class="h3 mb-3">Form Tambah Barang</h1>
                    <div class="row">
                        <div class="col-12 col-xl-12">
                            <form action="{{ route('storeBarang') }}" method="POST" autocomplete="off">
                                @csrf
                                <div class="mb-3">
                                    <input type="text" name="kode_barang" class="form-control form-control-sm"
                                        placeholder="Kode Barang" reqbarang.storeuired>
                                </div>
                                <div class="mb-3">
                                    <input type="text" name="nama_barang" class="form-control form-control-sm"
                                        placeholder="Nama Barang" required>
                                </div>
                                <div class="mb-3">
                                    <input type="text" name="jenis" class="form-control form-control-sm"
                                        placeholder="Jenis Barang" required>
                                </div>
                                <div class="mb-3">
                                    <input type="number" name="stok_min" class="form-control form-control-sm"
                                        placeholder="Stok Minimal" required>
                                </div>
                                <div class="mb-3">
                                    <select name="kode_supplier" class="form-control form-control-sm select2" required>
                                        <option value="">-- Pilih Supplier --</option>
                                        @foreach($suppliers as $supplier)
                                            <option value="{{ $supplier->kode_supplier }}">{{ $supplier->nama_supplier }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <textarea name="keterangan" class="form-control form-control-sm"
                                        placeholder="Keterangan"></textarea>
                                </div>
                                <div class="mb-3">
                                    <select name="status" class="form-control form-control-sm" required>
                                        <option value="Aktif">Aktif</option>
                                        <option value="Tidak Aktif">Tidak Aktif</option>
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-primary w-100">Simpan</button>
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
