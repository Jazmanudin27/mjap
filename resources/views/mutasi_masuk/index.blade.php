@extends('layouts.template')
@section('titlepage', 'Data Mutasi Barang Masuk')
@section('contents')
    <div class="container-fluid p-0">
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm border-0 rounded-4">
                    <div
                        class="card-header bg-primary text-white rounded-top-4 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-bold"><i class="fa fa-truck-loading me-2"></i> Data Mutasi Barang Masuk</h5>
                        @if (isset($TambahMutasiMasuk) && $TambahMutasiMasuk)
                            <a href="{{ route('tambahMutasiBarangMasuk') }}"
                                class="btn btn-light btn-sm text-primary fw-semibold">
                                <i class="fa fa-plus-circle me-1"></i> Input Barang Masuk
                            </a>
                        @endif
                    </div>
                    <div class="card-body mt-3">
                        <form method="GET" action="{{ route('viewMutasiBarangMasuk') }}" class="mb-4">
                            <div class="row g-2">
                                <!-- Tanggal Dari & Sampai -->
                                <div class="col-md-3">
                                    <input type="date" name="tanggal_dari" class="form-control form-control-sm"
                                        value="{{ request('tanggal_dari') }}" placeholder="Tanggal Dari">
                                </div>
                                <div class="col-md-3">
                                    <input type="date" name="tanggal_sampai" class="form-control form-control-sm"
                                        value="{{ request('tanggal_sampai') }}" placeholder="Tanggal Sampai">
                                </div>

                                <!-- Kode Transaksi & No Faktur -->
                                <div class="col-md-3">
                                    <input type="text" name="kode_transaksi" class="form-control form-control-sm"
                                        placeholder="Kode Transaksi" value="{{ request('kode_transaksi') }}">
                                </div>
                                <div class="col-md-3">
                                    <input type="text" name="no_faktur" class="form-control form-control-sm"
                                        placeholder="No Faktur" value="{{ request('no_faktur') }}">
                                </div>
                                <!-- Kondisi -->
                                <div class="col-md-3">
                                    <select name="kondisi" class="form-select form-select-sm">
                                        <option value="">-- Pilih Kondisi --</option>
                                        <option value="gs" {{ request('kondisi') == 'gs' ? 'selected' : '' }}>Good Stok
                                        </option>
                                        <option value="bs" {{ request('kondisi') == 'bs' ? 'selected' : '' }}>Bad Stok
                                        </option>
                                    </select>
                                </div>

                                <!-- Jenis Pemasukan -->
                                <div class="col-md-3">
                                    <select name="jenis_pemasukan" class="form-select form-select-sm">
                                        <option value="">-- Pilih Jenis Barang Masuk --</option>
                                        <option value="Repack"
                                            {{ request('jenis_pemasukan') == 'Repack' ? 'selected' : '' }}>Repack</option>
                                        <option value="Retur Pengganti"
                                            {{ request('jenis_pemasukan') == 'Retur Pengganti' ? 'selected' : '' }}>Retur
                                            Pengganti</option>
                                        <option value="Penyesuaian"
                                            {{ request('jenis_pemasukan') == 'Penyesuaian' ? 'selected' : '' }}>Penyesuaian
                                        </option>
                                        <option value="Lainnya"
                                            {{ request('jenis_pemasukan') == 'Lainnya' ? 'selected' : '' }}>Lainnya
                                        </option>
                                    </select>
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
                                        <th style="width: 3%">No</th>
                                        <th style="width: 11%">Kode Transaksi</th>
                                        <th style="width: 12%">Tanggal</th>
                                        <th style="width: 10%">Jenis Masuk</th>
                                        <th style="width: 12%">No Faktur</th>
                                        <th class="text-start">Keterangan</th>
                                        <th style="width: 10%">Status</th>
                                        <th style="width: 11%">Tgl Diterima</th>
                                        <th style="width: 10%" class="text-nowrap">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($mutasi as $i => $m)
                                        <tr class="align-middle">
                                            <td>{{ $mutasi->firstItem() + $i }}</td>
                                            <td>{{ $m->kode_transaksi }}</td>
                                            <td>{{ tanggal_indo2($m->tanggal) }}</td>
                                            <td>{{ $m->jenis_pemasukan }}</td>
                                            <td>{{ $m->no_faktur }}</td>
                                            <td class="text-start">{{ $m->keterangan }}</td>
                                            <td class="text-center">
                                                <a href="#"
                                                    class="btn btn-{{ $m->kondisi == 'gs' ? 'primary' : 'danger' }} btn-sm">
                                                    {{ $m->kondisi == 'gs' ? 'Good Stok' : 'Bad Stok' }}
                                                </a>
                                            </td>
                                            <td class="text-center">
                                                @if ($m->tanggal_diterima)
                                                    <span
                                                        class="btn btn-sm btn-success">{{ tanggal_indo2($m->tanggal_diterima) }}</span>
                                                @else
                                                    <span class="btn btn-sm btn-warning">Pending</span>
                                                @endif
                                            </td>
                                            <td class="text-nowrap">
                                                <a href="javascript:void(0);" class="btn btn-sm btn-success btn-detail"
                                                    data-id="{{ $m->kode_transaksi }}" title="Detail">
                                                    <i class="fa fa-list"></i>
                                                </a>
                                                <a href="{{ route('editMutasiBarangMasuk', $m->kode_transaksi) }}"
                                                    class="btn btn-sm btn-warning" title="Edit">
                                                    <i class="fa fa-pencil"></i>
                                                </a>
                                                <button type="button" class="btn btn-sm btn-danger deleteBarangMasuk"
                                                    data-href="{{ route('deleteMutasiBarangMasuk', $m->kode_transaksi) }}"
                                                    title="Hapus">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center text-muted">Tidak ada data barang masuk.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-3">
                            {{ $mutasi->withQueryString()->links('pagination::bootstrap-5') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modalDetail" tabindex="-1" aria-labelledby="modalDetailLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalDetailLabel">Detail Mutasi Barang Masuk</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body" id="modalDetailContent">
                    <p class="text-center text-muted">Memuat data...</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(function() {

            $(document).on('click', '.btn-detail', function() {
                let kodeTransaksi = $(this).data('id');
                $('#modalDetail').modal('show');
                $('#modalDetailContent').html('<p class="text-center text-muted">Memuat data...</p>');

                $.get(`{{ url('detailMutasiBarangMasuk') }}/${kodeTransaksi}`, function(response) {
                    $('#modalDetailContent').html(response);
                }).fail(function() {
                    $('#modalDetailContent').html(
                        '<div class="alert alert-danger">Gagal memuat data.</div>');
                });
            });

            $(document).on('click', '.deleteBarangMasuk', function(e) {
                e.preventDefault();
                const url = $(this).data('href');
                Swal.fire({
                    title: 'Hapus Transaksi?',
                    text: 'Data dan detail barang masuk akan dihapus permanen.',
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
