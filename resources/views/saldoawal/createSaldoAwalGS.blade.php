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
                                @foreach($suppliers as $s)
                                    <option value="{{ $s->kode_supplier }}">{{ $s->nama_supplier }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Bulan</label>
                            <select id="bulan" name="bulan" class="form-select2 form-select-sm">
                                @for ($i = 1; $i <= 12; $i++)
                                    <option value="{{ $i }}" {{ $i == date('n') ? 'selected' : '' }}>
                                        {{ DateTime::createFromFormat('!m', $i)->format('F') }}
                                    </option>
                                @endfor
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Tahun</label>
                            <select id="tahun" name="tahun" class="form-select2 form-select-sm">
                                @for ($y = date('Y') - 2; $y <= date('Y') + 1; $y++)
                                    <option value="{{ $y }}" {{ $y == date('Y') ? 'selected' : '' }}>{{ $y }}</option>
                                @endfor
                            </select>
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
        $(document).ready(function () {
            $('.select2').select2({ width: '100%' });

            function loadBarang() {
                let kode_supplier = $('#kode_supplier').val();
                let bulan = $('#bulan').val();
                let tahun = $('#tahun').val();

                $('#input_bulan').val(bulan);
                $('#input_tahun').val(tahun);
                $('#tabel-barang').html('<p class="text-muted">Memuat data barang...</p>');
                $('#btnSimpan').hide();

                if (!kode_supplier) {
                    $('#tabel-barang').html('<div class="alert alert-warning">Silakan pilih supplier terlebih dahulu.</div>');
                    return;
                }

                $.get(`{{ url('getBarangBySupplier') }}/${kode_supplier}`, function (data) {
                    if (data.length === 0) {
                        $('#tabel-barang').html('<div class="alert alert-info">Tidak ada barang untuk supplier ini.</div>');
                        return;
                    }

                    let html = `
                                            <div class="table-responsive rounded-3 border">
                                                <table class="table table-sm table-hover table-bordered align-middle mb-0">
                                                    <thead class="table-primary text-center align-middle sticky-top">
                                                        <tr>
                                                            <th style="width: 3%;">No.</th>
                                                            <th style="width: 10%;">Kode Barang</th>
                                                            <th>Nama Barang</th>
                                                            <th style="width: 6%;">Satuan</th>
                                                            <th style="width: 8%;">Qty</th>
                                                            <th>Keterangan</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>`;
                    data.forEach((item, index) => {
                        html += `
                                                <tr>
                                                    <td class="text-center">${index + 1}</td>
                                                    <td>
                                                        <span class="fw-semibold">${item.kode_barang}</span>
                                                        <input type="hidden" name="items[${index}][kode_barang]" value="${item.kode_barang}">
                                                    </td>
                                                    <td>${item.nama_barang}</td>
                                                    <td class="text-center"><a class="btn btn-sm btn-primary fw-semibold">${item.satuan}</a></td>
                                                    <td>
                                                        <input type="number" name="items[${index}][qty]" class="form-control form-control-sm text-end" min="0" value="0">
                                                    </td>
                                                    <td>
                                                        <input type="text" name="items[${index}][keterangan]" class="form-control form-control-sm">
                                                    </td>
                                                </tr>`;
                    });

                    html += `
                                                        </tbody>
                                                    </table>
                                                </div>`;
                    $('#tabel-barang').html(html);
                    $('#btnSimpan').show();
                });
            }

            $('#kode_supplier, #bulan, #tahun').on('change', loadBarang);
        });
    </script>
@endsection
