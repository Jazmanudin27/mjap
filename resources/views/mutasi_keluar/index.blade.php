@extends('layouts.template')
@section('titlepage', 'Data Mutasi Barang Keluar')
@section('contents')

    <div class="container-fluid p-0">
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm border-0 rounded-4">
                    <div
                        class="card-header bg-primary text-white rounded-top-4 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-bold"><i class="fa fa-boxes me-2"></i> Data Mutasi Barang Keluar</h5>
                        @if (isset($TambahMutasiKeluar) && $TambahMutasiKeluar)
                            <a href="{{ route('tambahMutasiBarangKeluar') }}"
                                class="btn btn-light btn-sm text-primary fw-semibold">
                                <i class="fa fa-plus-circle me-1"></i> Input Barang Keluar
                            </a>
                        @endif
                    </div>

                    <div class="card-body mt-3">
                        {{-- Form Filter --}}
                        <form method="GET" action="{{ route('viewMutasiBarangKeluar') }}" class="mb-4">
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

                                <div class="col-md-3">
                                    <select name="kondisi" class="form-select form-select-sm">
                                        <option value="">-- Pilih Kondisi --</option>
                                        <option value="gs" {{ request('kondisi') == 'gs' ? 'selected' : '' }}>Good Stok
                                        </option>
                                        <option value="bs" {{ request('kondisi') == 'bs' ? 'selected' : '' }}>Bad Stok
                                        </option>
                                    </select>
                                </div>

                                <!-- Jenis Pengeluaran -->
                                <div class="col-md-3">
                                    <select name="jenis_pengeluaran" class="form-select form-select-sm">
                                        <option value="">-- Pilih Jenis Barang Keluar --</option>
                                        <option value="Penjualan"
                                            {{ request('jenis_pengeluaran') == 'Penjualan' ? 'selected' : '' }}>Penjualan
                                        </option>
                                        <option value="Retur"
                                            {{ request('jenis_pengeluaran') == 'Retur' ? 'selected' : '' }}>Retur</option>
                                        <option value="Penyesuaian"
                                            {{ request('jenis_pengeluaran') == 'Penyesuaian' ? 'selected' : '' }}>
                                            Penyesuaian</option>
                                        <option value="Lainnya"
                                            {{ request('jenis_pengeluaran') == 'Lainnya' ? 'selected' : '' }}>Lainnya
                                        </option>
                                    </select>
                                </div>
                                <div class="col-md-2 d-grid">
                                    <button type="submit" class="btn btn-primary btn-sm">
                                        <i class="bi bi-filter-circle"></i> Filter
                                    </button>
                                </div>
                            </div>
                        </form>


                        {{-- Tabel Data --}}
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover table-sm align-middle mb-0">
                                <thead class="table-light text-center">
                                    <tr>
                                        <th style="width: 3%">No</th>
                                        <th style="width: 9%">Tanggal</th>
                                        <th style="width: 10%">Kode Transaksi</th>
                                        <th style="width: 13%">No Faktur</th>
                                        <th>Pelanggan</th>
                                        <th style="width: 12%">Jenis Pengeluaran</th>
                                        <th style="width: 10%">Status</th>
                                        <th style="width: 11%">Tgl Dikirim</th>
                                        <th style="width: 10%" class="text-nowrap">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($mutasi as $i => $m)
                                        <tr class="align-middle">
                                            <td>{{ $mutasi->firstItem() + $i }}</td>
                                            <td>{{ tanggal_indo2($m->tanggal) }}</td>
                                            <td>{{ $m->kode_transaksi }}</td>
                                            <td>{{ $m->no_faktur ?? '-' }}</td>
                                            <td>{{ $m->nama_pelanggan ?? '-' }}</td>
                                            <td>{{ $m->jenis_pengeluaran }}</td>
                                            <td class="text-center">
                                                <a href="#"
                                                    class="btn btn-{{ $m->kondisi == 'gs' ? 'primary' : 'danger' }} btn-sm">
                                                    {{ $m->kondisi == 'gs' ? 'Good Stok' : 'Bad Stok' }}
                                                </a>
                                            </td>
                                            <td class="text-center">
                                                @if ($m->tanggal_dikirim)
                                                    <span
                                                        class="btn btn-sm btn-success">{{ tanggal_indo2($m->tanggal_dikirim) }}</span>
                                                @else
                                                    <span class="btn btn-sm btn-warning">Pending</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <a href="javascript:void(0);" class="btn btn-sm btn-success btn-detail"
                                                    data-id="{{ $m->kode_transaksi }}" title="Detail">
                                                    <i class="fa fa-list"></i>
                                                </a>
                                                <a href="{{ route('editMutasiBarangKeluar', $m->kode_transaksi) }}"
                                                    class="btn btn-sm btn-warning" title="Edit">
                                                    <i class="fa fa-pencil"></i>
                                                </a>
                                                <button type="button" class="btn btn-sm btn-danger deleteBarangKeluar"
                                                    data-href="{{ route('deleteMutasiBarangKeluar', $m->kode_transaksi) }}"
                                                    title="Hapus">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="9" class="text-center text-muted">Tidak ada data barang
                                                keluar.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        {{-- Pagination --}}
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
                    <h5 class="modal-title" id="modalDetailLabel">Detail Mutasi Barang Keluar</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body" id="modalDetailContent">
                    <p class="text-center text-muted">Memuat data...</p>
                </div>
            </div>
        </div>
    </div>


    {{-- Script datepicker dan konfirmasi delete --}}
    <script>
        $(function() {

            flatpickr(".datepicker", {
                dateFormat: "Y-m-d",
                allowInput: true
            });

            $('#kode_pelanggan').select2({
                placeholder: 'Cari pelanggan…',
                dropdownParent: $('#kode_pelanggan').parent(),
                ajax: {
                    url: "{{ route('getPelanggan') }}",
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            kode_pelanggan: params.term
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: data.results
                        };
                    },
                    cache: true
                }
            });

            $(document).on('click', '.btn-detail', function() {
                let kodeTransaksi = $(this).data('id');
                $('#modalDetail').modal('show');
                $('#modalDetailContent').html('<p class="text-center text-muted">Memuat data...</p>');

                $.get(`{{ url('detailMutasiBarangKeluar') }}/${kodeTransaksi}`, function(response) {
                    $('#modalDetailContent').html(response);
                }).fail(function() {
                    $('#modalDetailContent').html(
                        '<div class="alert alert-danger">Gagal memuat data.</div>');
                });
            });

            $(document).on('click', '.deleteBarangKeluar', function(e) {
                e.preventDefault();
                const url = $(this).data('href');
                Swal.fire({
                    title: 'Hapus Transaksi?',
                    text: 'Data dan detail barang keluar akan dihapus permanen.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    confirmButtonText: 'Hapus',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = url;
                    }
                });
            });
        });
    </script>
@endsection
