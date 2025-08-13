@extends('layouts.template')
@section('titlepage', 'Data Barang')
@section('contents')
    <div class="container-fluid p-0">
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm border-0 rounded-4">
                    <div
                        class="card-header bg-primary text-white rounded-top-4 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-bold"><i class="fa fa-box me-2"></i> Data Harga Barang</h5>
                    </div>

                    <div class="card-body mt-3">
                        <form method="GET" action="{{ route('hargaBarang') }}" class="mb-3">
                            <div class="row">
                                <div class="col-md-4">
                                    <input type="text" name="nama_barang" class="form-control form-control-sm"
                                        placeholder="Cari Nama Barang" value="{{ request('nama_barang') }}">
                                </div>

                                {{-- Dropdown Supplier --}}
                                <div class="col-md-4">
                                    <select name="kode_supplier" class="form-control form-select2 form-control-sm">
                                        <option value="">-- Pilih Supplier --</option>
                                        @foreach ($suppliers as $supplier)
                                            <option value="{{ $supplier->kode_supplier }}"
                                                {{ request('kode_supplier') == $supplier->kode_supplier ? 'selected' : '' }}>
                                                {{ $supplier->nama_supplier }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-2 col-lg-4 d-flex gap-2">
                                    <button type="submit" name="action" value="filter"
                                        class="btn btn-primary btn-sm w-100">
                                        <i class="fa fa-filter me-1"></i> Filter
                                    </button>
                                </div>
                            </div>
                        </form>
                        <div class="table-responsive">
                            <table class="table table-striped table-sm mb-0">
                                <thead>
                                    <tr>
                                        <th style="width:5%">No</th>
                                        <th style="width:10%">Kode</th>
                                        <th style="width:25%">Nama Barang</th>
                                        <th style="width:10%">Jenis</th>
                                        <th style="width:10%">Merk</th>
                                        <th style="width:10%">Satuan</th>
                                        <th style="width:10%">Konversi</th>
                                        <th style="width:10%" class="text-end">Harga Pokok</th>
                                        <th style="width:10%" class="text-end">Harga Jual</th>
                                        <th style="width:10%">Margin</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $no = 1;
                                    @endphp
                                    @foreach ($satuanBarang as $s)
                                        @php
                                            $margin =
                                                $s->harga_pokok > 0
                                                    ? (($s->harga_jual - $s->harga_pokok) / $s->harga_pokok) * 100
                                                    : 0;
                                        @endphp
                                        <tr>
                                            <td>{{ $no++ }}</td>
                                            <td>{{ $s->kode_item }}</td>
                                            <td>{{ $s->nama_barang }}</td>
                                            <td>{{ $s->kategori }}</td>
                                            <td>{{ $s->merk }}</td>
                                            <td>{{ $s->satuan }}</td>
                                            <td>
                                                <input type="number" class="form-control form-control-sm update-field"
                                                    data-id="{{ $s->id }}" data-field="isi"
                                                    value="{{ $s->isi }}">
                                            </td>
                                            <td>
                                                <input type="text"
                                                    class="form-control form-control-sm text-end update-field format-rupiah"
                                                    data-id="{{ $s->id }}" data-field="harga_pokok"
                                                    value="{{ number_format($s->harga_pokok, 0, ',', '.') }}">
                                            </td>
                                            <td>
                                                <input type="text"
                                                    class="form-control form-control-sm text-end update-field format-rupiah"
                                                    data-id="{{ $s->id }}" data-field="harga_jual"
                                                    value="{{ number_format($s->harga_jual, 0, ',', '.') }}">
                                            </td>
                                            <td class="text-end margin-cell">{{ number_format($margin, 2) }}%</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {

            function formatRupiah(angka) {
                angka = angka.replace(/[^,\d]/g, "");
                var split = angka.split(",");
                var sisa = split[0].length % 3;
                var rupiah = split[0].substr(0, sisa);
                var ribuan = split[0].substr(sisa).match(/\d{3}/gi);

                if (ribuan) {
                    var separator = sisa ? "." : "";
                    rupiah += separator + ribuan.join(".");
                }
                return split[1] != undefined ? rupiah + "," + split[1] : rupiah;
            }

            function hitungMargin(row) {
                let hargaPokok = parseInt(row.find('input[data-field="harga_pokok"]').val().replace(/\./g, "")) ||
                    0;
                let hargaJual = parseInt(row.find('input[data-field="harga_jual"]').val().replace(/\./g, "")) || 0;
                let margin = 0;
                if (hargaPokok > 0) {
                    margin = ((hargaJual - hargaPokok) / hargaPokok) * 100;
                }
                row.find('.margin-cell').text(margin.toFixed(2) + "%");
            }

            $(".format-rupiah").on("input", function() {
                this.value = formatRupiah(this.value);

                let row = $(this).closest('tr');
                hitungMargin(row);
            });

            $(".update-field").on("input", function() {
                let id = $(this).data("id");
                let field = $(this).data("field");
                let value = $(this).val().replace(/\./g, "");

                // Hitung ulang margin setiap kali input berubah
                let row = $(this).closest('tr');
                hitungMargin(row);

                $.ajax({
                    url: "{{ route('updateHargaBarang') }}",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        id: id,
                        field: field,
                        value: value
                    },
                    success: function(res) {
                        // toastr sukses/error bisa diaktifkan lagi kalau toastr sudah di-load
                    },
                    error: function() {
                        // toastr error juga sama
                    }
                });
            });

        });
    </script>
@endsection
