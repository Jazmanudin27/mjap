@extends('layouts.template')
@section('titlepage', 'Tambah Barang')
@section('contents')
    <div class="container-fluid p-0">
        <div class="col-xl-7">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Form Tambah Barang</h5>
                </div>
                <div class="card-body mt-4 mb-4">
                    <form action="{{ route('storeBarang') }}" method="POST" autocomplete="off">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Kode Barang (Auto)</label>
                                <input type="text" class="form-control form-control-sm" value="Auto Generate" readonly>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Kode Item</label>
                                <input type="text" name="kode_item" class="form-control form-control-sm"
                                    placeholder="Masukkan Kode Item">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Nama Barang</label>
                                <input type="text" name="nama_barang" class="form-control form-control-sm"
                                    placeholder="Masukkan Nama Barang" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Jenis Barang</label>
                                <input type="text" name="kategori" class="form-control form-control-sm"
                                    placeholder="Jenis Barang" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Merk Barang</label>
                                <input type="text" name="merk" class="form-control form-control-sm"
                                    placeholder="Merk Barang" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Stok Minimal</label>
                                <input type="number" name="stok_min" class="form-control form-control-sm"
                                    placeholder="Stok Minimal" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Supplier</label>
                                <select name="kode_supplier" class="form-control form-control-sm select2" required>
                                    <option value="">-- Pilih Supplier --</option>
                                    @foreach ($suppliers as $supplier)
                                        <option value="{{ $supplier->kode_supplier }}">{{ $supplier->nama_supplier }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Status</label>
                                <select name="status" class="form-control form-control-sm" required>
                                    <option value="Aktif">Aktif</option>
                                    <option value="Tidak Aktif">Tidak Aktif</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Keterangan</label>
                                <textarea name="keterangan" class="form-control form-control-sm" placeholder="Keterangan tambahan (opsional)"
                                    rows="2"></textarea>
                            </div>

                            <input type="hidden" name="jenis" value="-" />

                            <div class="col-md-6 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary w-100 mt-2">💾 Simpan Barang</button>
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
