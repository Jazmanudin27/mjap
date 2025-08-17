@extends('layouts.template')
@section('titlepage', 'Get Saldo Awal Good Stok')
@section('contents')
    <div class="container-fluid p-0">
        <div class="col-12">
            <div class="card shadow-sm rounded-4">
                <div class="card-header border-0 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 text-dark fw-bold">Get Saldo Awal Good Stok</h5>
                </div>
                <div class="card-body mt-3">
                    <div class="row g-2 mb-3">
                        <div class="col-md-4">
                            <label class="form-label">Pilih Supplier</label>
                            <select id="kode_supplier" class="form-select select2">
                                <option value="">-- Pilih Supplier --</option>
                                @foreach ($suppliers as $s)
                                    <option value="{{ $s->kode_supplier }}">{{ $s->nama_supplier }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Nama Barang</label>
                            <input type="text" id="filter_nama_barang" class="form-control" placeholder="Nama Barang">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Kode Barang</label>
                            <input type="text" id="filter_kode_barang" class="form-control" placeholder="Kode Barang">
                        </div>
                    </div>
                    <form action="{{ route('storeSaldoAwalGS') }}" method="POST">
                        @csrf
                        <input type="hidden" name="tanggal" value="{{ date('Y-m-d') }}">
                        <input type="hidden" name="bulan" id="input_bulan" value="{{ date('n') }}">
                        <input type="hidden" name="tahun" id="input_tahun" value="{{ date('Y') }}">

                        <div id="tabel-barang" class="mt-3"></div>

                        <button type="submit" class="btn btn-success mt-4 w-100" id="btnSimpan" style="display: none;">
                            <i class="bi bi-check-circle me-1"></i> Simpan
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script>
        $(document).ready(function() {
            $('.select2').select2({
                width: '100%'
            });

            function loadBarang() {
                let kode_supplier = $('#kode_supplier').val();
                let bulan = $('#bulan').val();
                let tahun = $('#tahun').val();
                let nama_barang = $('#filter_nama_barang').val();
                let kode_barang = $('#filter_kode_barang').val();

                $('#input_bulan').val(bulan);
                $('#input_tahun').val(tahun);
                $('#tabel-barang').html('<p class="text-muted">Memuat data barang...</p>');
                $('#btnSimpan').hide();

                if (!kode_supplier) {
                    $('#tabel-barang').html(
                        '<div class="alert alert-warning">Silakan pilih supplier terlebih dahulu.</div>');
                    return;
                }

                $.get(`{{ url('getBarangGSBySupplier') }}`, {
                    kode_supplier: kode_supplier,
                    nama_barang: nama_barang,
                    kode_barang: kode_barang
                }, function(data) {
                    // render tabel seperti biasa...
                });
            }

            $('#kode_supplier, #bulan, #tahun, #filter_nama_barang, #filter_kode_barang').on('change keyup',
                loadBarang);
        });
    </script>
@endsection
