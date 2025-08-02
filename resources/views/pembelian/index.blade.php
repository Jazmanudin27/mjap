@extends('layouts.template')
@section('titlepage', 'Data Pembelian')
@section('contents')
    <div class="container-fluid p-0">
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm border-0 rounded-4">
                    <div
                        class="card-header bg-primary text-white rounded-top-4 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-bold"><i class="fa fa-shopping-cart me-2"></i> Data Pembelian</h5>
                        <a href="{{ route('tambahPembelian') }}" class="btn btn-light btn-sm text-primary fw-semibold">
                            <i class="fa fa-plus-circle me-1"></i> Input Pembelian
                        </a>
                    </div>
                    <div class="card-body mt-3">
                        <div class="row">
                            <div class="col-sm-6">
                                <input type="date" class="form-control form-control-sm" id="tanggal_dari"
                                    placeholder="Tanggal Dari">
                            </div>
                            <div class="col-sm-6">
                                <input type="date" class="form-control form-control-sm" id="tanggal_sampai"
                                    placeholder="Tanggal Sampai">
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-sm-3">
                                <input type="text" class="form-control form-control-sm" id="no_faktur"
                                    placeholder="No Faktur">
                            </div>
                            <div class="col-sm-6">
                                <select id="supplier" class="form-select2 form-select-sm">
                                    <option value="">-- Pilih Supplier --</option>
                                    @foreach ($suppliers as $s)
                                        <option value="{{ $s->kode_supplier }}">
                                            {{ $s->kode_supplier }} - {{ $s->nama_supplier }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-sm-3 d-grid">
                                <button class="btn btn-success btn-sm" id="btnCari">Filter</button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body mt-2">
                        <div class="table-responsive">
                            <table class="table table-striped table-sm table-bordered align-middle">
                                <thead class="table-primary">
                                    <tr>
                                        <th style="width: 2%">No</th>
                                        <th style="width: 10%">Tanggal</th>
                                        <th style="width: 13%">No Faktur</th>
                                        <th style="width: 6%">Kode</th>
                                        <th>Nama Supplier</th>
                                        <th class="text-end" style="width: 12%">Total</th>
                                        <th class="text-center" style="width: 7%">Status</th>
                                        <th class="text-center" style="width: 10%">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $no = ($pembelian->currentPage() - 1) * $pembelian->perPage() + 1; @endphp
                                    @foreach ($pembelian as $row)
                                        @php $selisih = $row->grand_total - $row->jumlah_bayar; @endphp
                                        <tr>
                                            <td class="text-center">{{ $no++ }}</td>
                                            <td>{{ tanggal_indo2($row->tanggal) }}</td>
                                            <td>{{ $row->no_faktur }}</td>
                                            <td>{{ $row->kode_supplier }}</td>
                                            <td>{{ $row->nama_supplier }}</td>
                                            <td class="text-end">{{ rupiah($row->grand_total) }}</td>
                                            <td class="text-center">
                                                <a href="#"
                                                    class="btn btn-sm {{ $selisih <= 0 ? 'btn-success' : 'btn-danger' }}">
                                                    {{ $selisih <= 0 ? 'L' : 'BL' }}
                                                </a>
                                            </td>
                                            <td style="width: 180px">
                                                <a href="{{ route('detailPembelian', $row->no_faktur) }}"
                                                    class="btn btn-sm btn-success">
                                                    <i class="fa fa-list"></i>
                                                </a>
                                                <a href="{{ route('editPembelian', $row->no_faktur) }}"
                                                    class="btn btn-sm btn-warning">
                                                    <i class="fa fa-pencil"></i>
                                                </a>
                                                <a href="#"
                                                    data-href="{{ route('deletePembelian', $row->no_faktur) }}"
                                                    class="btn btn-sm btn-danger deletePembelian">
                                                    <i class="fa fa-trash"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                    <tr>
                                        <td colspan="8">
                                            <div class="d-flex justify-content-between align-items-center small">
                                                <div class="text-muted">
                                                    Menampilkan {{ $pembelian->firstItem() }} -
                                                    {{ $pembelian->lastItem() }}
                                                    dari total
                                                    {{ $pembelian->total() }} data
                                                </div>
                                                <div class="pagination-wrapper">
                                                    {!! $pembelian->appends(request()->except('page'))->links('pagination::bootstrap-5') !!}
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(function() {

            function viewPembelian(pageUrl = '{{ route('viewPembelian') }}') {
                $.get(pageUrl, {
                    no_faktur: $('#no_faktur').val(),
                    kode_supplier: $('#kode_supplier').val(),
                    nama_supplier: $('#nama_supplier').val(),
                    tanggal_dari: $('#tanggal_dari').val(),
                    tanggal_sampai: $('#tanggal_sampai').val()
                }, function(res) {
                    $('#viewPembelian').html(res);
                }).fail(() => Swal.fire('Gagal', 'Tidak dapat mengambil data', 'error'));
            }


            // panggil pertama kali
            viewPembelian();

            // tombol cari
            $('#btnCari').on('click', e => {
                e.preventDefault();
                viewPembelian();
            });

            // pagination via AJAX
            $(document).on('click', '.pagination a', function(e) {
                e.preventDefault();
                viewPembelian($(this).attr('href')); // kirim URL page ke fungsi
            });

            $(document).on('click', '.deletePembelian', function(e) {
                e.preventDefault();
                const url = $(this).data('href');

                Swal.fire({
                    title: 'Hapus Pembelian?',
                    text: "Data dan detail produk akan terhapus permanen.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#e3342f',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, hapus!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = url;
                    }
                });
            });

        });
    </script>
@endsection
