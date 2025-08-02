@extends('layouts.template')
@section('titlepage', 'Data Purchase Order')
@section('contents')
    <div class="container-fluid p-0">
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm border-0 rounded-4">
                    <div
                        class="card-header bg-primary text-white rounded-top-4 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-bold"><i class="fa fa-file-invoice me-2"></i> Data Purchase Order</h5>
                        <a href="{{ route('tambahPO') }}" class="btn btn-light btn-sm text-primary fw-semibold">
                            <i class="fa fa-plus-circle me-1"></i> Input PO
                        </a>
                    </div>
                    <div class="card-body mt-2">

                        <form method="GET" action="{{ route('viewPO') }}">
                            <div class="row">
                                <div class="col-sm-6">
                                    <input type="date" class="form-control form-control-sm" id="tanggal_dari"
                                        name="tanggal_dari" value="{{ request('tanggal_dari') }}">
                                </div>
                                <div class="col-sm-6">
                                    <input type="date" class="form-control form-control-sm" id="tanggal_sampai"
                                        name="tanggal_sampai" value="{{ request('tanggal_sampai') }}">
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-sm-3">
                                    <input type="text" class="form-control form-control-sm" id="no_po" name="no_po"
                                        placeholder="No PO" value="{{ request('no_po') }}">
                                </div>
                                <div class="col-sm-6">
                                    <select id="kode_supplier" name="kode_supplier" class="form-select2 form-select-sm">
                                        <option value="">-- Pilih Supplier --</option>
                                        @foreach ($suppliers as $s)
                                            <option value="{{ $s->kode_supplier }}"
                                                {{ request('kode_supplier') == $s->kode_supplier ? 'selected' : '' }}>
                                                {{ $s->kode_supplier }} - {{ $s->nama_supplier }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-sm-3 d-grid">
                                    <button type="submit" class="btn btn-success btn-sm">Filter</button>
                                </div>
                            </div>
                        </form>
                        <div class="table-responsive mt-3">
                            <table class="table table-striped table-sm table-bordered align-middle">
                                <thead class="table-primary">
                                    <tr>
                                        <th style="width: 2%">No</th>
                                        <th style="width: 9%">Tanggal</th>
                                        <th style="width: 9%">Tgl Kirim</th>
                                        <th style="width: 13%">No PO</th>
                                        <th style="width: 6%">Kode</th>
                                        <th>Nama Supplier</th>
                                        <th class="text-end" style="width: 9%">Potongan</th>
                                        <th class="text-end" style="width: 9%">Pot. Klaim</th>
                                        <th class="text-end" style="width: 12%">Grand Total</th>
                                        <th class="text-center" style="width: 7%">Status</th>
                                        <th class="text-center" style="width: 10%">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $no = ($po->currentPage() - 1) * $po->perPage() + 1; @endphp
                                    @foreach ($po as $row)
                                        <tr>
                                            <td class="text-center">{{ $no++ }}</td>
                                            <td>{{ tanggal_indo2($row->tanggal) }}</td>
                                            <td>{{ tanggal_indo2($row->jatuh_tempo) }}</td>
                                            <td>{{ $row->no_po }}</td>
                                            <td>{{ $row->kode_supplier }}</td>
                                            <td>{{ $row->nama_supplier }}</td>
                                            <td class="text-end">{{ rupiah($row->potongan) }}</td>
                                            <td class="text-end">{{ rupiah($row->potongan_claim) }}</td>
                                            <td class="text-end">{{ rupiah($row->grand_total) }}</td>
                                            <td class="text-center">
                                                <span
                                                    class="btn btn-sm btn-{{ $row->status == 'closed' ? 'success' : ($row->status == 'open' ? 'warning' : 'danger') }}">
                                                    {{ ucfirst($row->status) }}
                                                </span>
                                            </td>
                                            <td style="width: 180px">
                                                <a href="{{ route('detailPO', $row->no_po) }}"
                                                    class="btn btn-sm btn-success">
                                                    <i class="fa fa-list"></i>
                                                </a>
                                                <a href="{{ route('editPO', $row->no_po) }}"
                                                    class="btn btn-sm btn-warning">
                                                    <i class="fa fa-pencil"></i>
                                                </a>
                                                <a href="#" data-href="{{ route('deletePO', $row->no_po) }}"
                                                    class="btn btn-sm btn-danger deletePO">
                                                    <i class="fa fa-trash"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                    <tr>
                                        <td colspan="12">
                                            <div class="d-flex justify-content-between align-items-center small">
                                                <div class="text-muted">
                                                    Menampilkan {{ $po->firstItem() }} -
                                                    {{ $po->lastItem() }}
                                                    dari total
                                                    {{ $po->total() }} data
                                                </div>
                                                <div class="pagination-wrapper">
                                                    {!! $po->appends(request()->except('page'))->links('pagination::bootstrap-5') !!}
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

            $(document).on('click', '.deletePO', function(e) {
                e.preventDefault();
                const url = $(this).data('href');

                Swal.fire({
                    title: 'Hapus PO?',
                    text: "Data PO dan detail produk akan terhapus permanen.",
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
