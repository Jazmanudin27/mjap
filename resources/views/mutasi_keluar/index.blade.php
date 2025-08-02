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
                                <div class="col-md-6">
                                    <input type="date" class="form-control form-control-sm" placeholder="Tanggal Dari"
                                        value="{{ request('tanggal_dari') }}" autocomplete="off">
                                </div>
                                <div class="col-md-6">
                                    <input type="date" class="form-control form-control-sm" placeholder="Tanggal Sampai"
                                        value="{{ request('tanggal_sampai') }}" autocomplete="off">
                                </div>
                                <div class="col-md-6">
                                    <select name="kode_pelanggan" id="kode_pelanggan" class="form-select form-select-sm">
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <input type="text" name="kode_transaksi" class="form-control form-control-sm"
                                        placeholder="Kode Transaksi" value="{{ request('kode_transaksi') }}">
                                </div>
                                <div class="col-md-2">
                                    <input type="text" name="no_faktur" class="form-control form-control-sm"
                                        placeholder="No Faktur" value="{{ request('no_faktur') }}">
                                </div>
                                <div class="col-md-2">
                                    <input type="text" name="tujuan" class="form-control form-control-sm"
                                        placeholder="Tujuan" value="{{ request('tujuan') }}">
                                </div>
                                <div class="col-md-12 d-grid">
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
                                        <th style="width: 12%">Tujuan</th>
                                        <th style="width: 12%">Jenis Pengeluaran</th>
                                        <th style="width: 10%">Status</th>
                                        <th style="width: 9%">Tgl Dikirim</th>
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
                                            <td>{{ $m->tujuan }}</td>
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
                                                <a href="{{ route('detailMutasiBarangKeluar', $m->kode_transaksi) }}"
                                                    class="btn btn-sm btn-success" title="Detail">
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
                                            <td colspan="9" class="text-center text-muted">Tidak ada data barang keluar.
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
