@extends('layouts.template')
@section('titlepage', 'Tambah Supplier')
@section('contents')
    <div class="container-fluid p-0">
        <div class="col-12 col-lg-6">
            <div class="card shadow-sm border-0 rounded-4 mt-4">
                <div class="card-body p-4">
                    <h4 class="text-center text-success mb-4">
                        <i class="fa fa-plus me-2"></i>Tambah Supplier
                    </h4>
                    <form action="{{ route('storeSupplier') }}" method="POST" autocomplete="off">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-6">
                                <input type="text" name="kode_supplier" class="form-control form-control-sm bg-light"
                                    value="{{ old('kode_supplier') }}" placeholder="Kode Supplier" required>
                            </div>

                            <div class="col-md-6">
                                <input type="text" name="nama_supplier" class="form-control form-control-sm"
                                    value="{{ old('nama_supplier') }}" placeholder="Nama Supplier" required>
                            </div>

                            <div class="col-md-12">
                                <input type="text" name="alamat" class="form-control form-control-sm"
                                    value="{{ old('alamat') }}" placeholder="Alamat Lengkap" required>
                            </div>

                            <div class="col-md-6">
                                <input type="text" name="telepon" class="form-control form-control-sm"
                                    value="{{ old('telepon') }}" placeholder="Nomor Telepon" required>
                            </div>

                            <div class="col-md-6">
                                <input type="email" name="email" class="form-control form-control-sm"
                                    value="{{ old('email') }}" placeholder="Email (Opsional)">
                            </div>

                            <div class="col-md-6">
                                <select name="status" class="form-select form-select-sm" required>
                                    <option value="" disabled selected>Pilih Status</option>
                                    <option value="1" {{ old('status') == '1' ? 'selected' : '' }}>Aktif</option>
                                    <option value="0" {{ old('status') == '0' ? 'selected' : '' }}>Nonaktif</option>
                                </select>
                            </div>

                            <div class="col-12 mt-3">
                                <button type="submit" class="btn btn-success w-100">
                                    <i class="fa fa-save me-1"></i> Simpan Supplier
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
