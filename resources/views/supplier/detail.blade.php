@extends('layouts.template')
@section('titlepage', 'Edit Supplier')

@section('contents')
    <div class="container-fluid p-0">
        <div class="col-12 col-xl-8">
            <div class="card shadow border-0 rounded-4">
                <div class="card-header bg-primary text-white text-center rounded-top-4">
                    <h5 class="mb-0"><i class="bi bi-pencil-square me-2"></i>Edit Data Supplier</h5>
                </div>
                <div class="card-body px-4 py-4">
                    <form action="{{ route('updateSupplier') }}" method="POST" autocomplete="off">
                        @csrf
                        @method('POST')

                        <div class="row g-4">
                            {{-- Kolom Kiri --}}
                            <div class="col-md-6">
                                <h6 class="text-muted mb-2">Informasi Supplier</h6>

                                <div class="mb-2">
                                    <label class="form-label small">Kode Supplier</label>
                                    <input type="text" name="kode_supplier" class="form-control form-control-sm"
                                        value="{{ $supplier->kode_supplier }}" readonly>
                                </div>

                                <div class="mb-2">
                                    <label class="form-label small">Nama Supplier</label>
                                    <input type="text" name="nama_supplier" class="form-control form-control-sm"
                                        value="{{ $supplier->nama_supplier }}" required>
                                </div>

                                <div class="mb-2">
                                    <label class="form-label small">Status</label>
                                    <select name="status" class="form-select form-select-sm" required>
                                        <option value="1" {{ $supplier->status == '1' ? 'selected' : '' }}>Aktif</option>
                                        <option value="0" {{ $supplier->status == '0' ? 'selected' : '' }}>Nonaktif</option>
                                    </select>
                                </div>
                            </div>

                            {{-- Kolom Kanan --}}
                            <div class="col-md-6">
                                <h6 class="text-muted mb-2">Kontak & Alamat</h6>

                                <div class="mb-2">
                                    <label class="form-label small">Alamat</label>
                                    <input type="text" name="alamat" class="form-control form-control-sm"
                                        value="{{ $supplier->alamat }}">
                                </div>

                                <div class="mb-2">
                                    <label class="form-label small">Telepon</label>
                                    <input type="text" name="no_hp" class="form-control form-control-sm"
                                        value="{{ $supplier->no_hp }}">
                                </div>

                                <div class="mb-2">
                                    <label class="form-label small">Email</label>
                                    <input type="email" name="email" class="form-control form-control-sm"
                                        value="{{ $supplier->email }}">
                                </div>
                            </div>

                            {{-- Tombol Simpan --}}
                            <div class="col-12">
                                <button type="submit" class="btn btn-success w-100 mt-3"><i
                                        class="bi bi-save me-2"></i>Update Data</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
