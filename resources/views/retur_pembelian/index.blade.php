@extends('layouts.template')
@section('titlepage', 'Data Retur Pembelian')
@section('contents')
    <div class="container-fluid p-0">
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm border-0 rounded-4">
                    <div
                        class="card-header bg-primary text-white rounded-top-4 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-bold"><i class="fa fa-undo-alt me-2"></i> Data Retur Pembelian</h5>
                        <a href="{{ route('tambahReturPembelian') }}"
                            class="btn btn-light btn-sm text-primary fw-semibold d-flex align-items-center gap-2">
                            <i class="fa fa-plus-circle"></i> <span>Input Retur</span>
                        </a>
                    </div>
                    <div class="card-body mt-3">
                        <form method="GET" action="{{ route('viewReturPembelian') }}" class="mb-4">
                            <div class="row g-2">
                                <div class="col-md-6">
                                    <input type="date" name="tanggal_dari" class="form-control form-control-sm"
                                        placeholder="Tanggal Dari" value="{{ request('tanggal_dari') }}">
                                </div>
                                <div class="col-md-6">
                                    <input type="date" name="tanggal_sampai" class="form-control form-control-sm"
                                        placeholder="Tanggal Sampai" value="{{ request('tanggal_sampai') }}">
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-md-3">
                                    <input type="text" name="no_retur" class="form-control form-control-sm"
                                        placeholder="No Retur" value="{{ request('no_retur') }}">
                                </div>
                                <div class="col-md-3">
                                    <input type="text" name="kode_supplier" class="form-control form-control-sm"
                                        placeholder="Kode Supplier" value="{{ request('kode_supplier') }}">
                                </div>
                                <div class="col-md-4">
                                    <input type="text" name="nama_supplier" class="form-control form-control-sm"
                                        placeholder="Nama Supplier" value="{{ request('nama_supplier') }}">
                                </div>
                                <div class="col-md-2 d-grid">
                                    <button type="submit" class="btn btn-primary btn-sm">Filter</button>
                                </div>
                            </div>
                        </form>

                        <div class="table-responsive">
                            <table class="table table-bordered table-hover table-sm align-middle mb-0">
                                <thead class="table-light text-center">
                                    <tr>
                                        <th style="width: 3%;">No</th>
                                        <th style="width: 9%;">Tanggal</th>
                                        <th style="width: 8%;">No Retur</th>
                                        <th style="width: 12%;">No Faktur</th>
                                        <th style="width: 7%;">Kode</th>
                                        <th>Nama Supplier</th>
                                        <th style="width: 5%;">Jenis</th>
                                        <th style="width: 9%;" class="text-end">Total Retur</th>
                                        <th style="width: 11%;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($retur as $i => $r)
                                        <tr class="align-middle">
                                            <td>{{ $retur->firstItem() + $i }}</td>
                                            <td>{{ tanggal_indo2($r->tanggal) }}</td>
                                            <td>{{ $r->no_retur }}</td>
                                            <td>{{ $r->no_faktur }}</td>
                                            <td>{{ $r->kode_supplier }}</td>
                                            <td class="text-start">{{ $r->nama_supplier }}</td>
                                            <td>
                                                <span
                                                    class="btn btn-sm btn-{{ $r->jenis_retur == 'PF' ? 'success' : 'warning' }}">
                                                    {{ strtoupper($r->jenis_retur) }}
                                                </span>
                                            </td>
                                            <td class="text-end">{{ rupiah($r->total) }}</td>
                                            <td class="text-nowrap">
                                                <a href="{{ route('detailReturPembelian', $r->no_retur) }}"
                                                    class="btn btn-sm btn-success" title="Detail">
                                                    <i class="fa fa-list"></i>
                                                </a>
                                                <a href="{{ route('editReturPembelian', $r->no_retur) }}"
                                                    class="btn btn-sm btn-warning" title="Edit">
                                                    <i class="fa fa-pencil"></i>
                                                </a>
                                                <a href="#" class="btn btn-sm btn-danger deleteReturPembelian"
                                                    data-href="{{ route('deleteReturPembelian', $r->no_retur) }}"
                                                    title="Hapus">
                                                    <i class="fa fa-trash"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="11" class="text-center text-muted">Tidak ada data retur
                                                pembelian.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-3">
                            {{ $retur->withQueryString()->links('pagination::bootstrap-5') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        $(function() {
            $(document).on('click', '.deleteReturPembelian', function(e) {
                e.preventDefault();
                const url = $(this).data('href');
                Swal.fire({
                    title: 'Hapus Retur?',
                    text: 'Data dan detail retur akan dihapus permanen.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    confirmButtonText: 'Hapus',
                    cancelButtonText: 'Batal'
                }).then(r => {
                    if (r.isConfirmed) window.location.href = url;
                });
            });

        });
    </script>
@endsection
