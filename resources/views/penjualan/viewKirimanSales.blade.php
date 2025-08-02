@extends('layouts.template')
@section('titlepage', 'Data Kiriman Sales')
@section('contents')
    <div class="container-fluid p-0">
        <div class="card shadow-sm border-0 rounded-4">
            <div class="card-header bg-primary text-white rounded-top-4 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold"><i class="fa fa-truck me-2"></i> Data Kiriman Sales</h5>
                <a href="{{ route('createKirimanSales') }}"
                    class="btn btn-light btn-sm text-primary fw-semibold d-flex align-items-center gap-2">
                    <i class="fa fa-plus-circle"></i> <span>Input Kiriman</span>
                </a>
            </div>
            <div class="card-body mt-3">
                <form method="GET" action="{{ route('viewKirimanSales') }}" class="mb-3">
                    <div class="row g-2">
                        <div class="col-md-3">
                            <input type="date" name="tanggal" value="{{ request('tanggal') ?? date('Y-m-d') }}"
                                class="form-control form-control-sm">
                        </div>
                        <div class="col-md-3">
                            <select name="kode_wilayah" class="form-select form-select-sm select2">
                                <option value="">-- Semua Wilayah --</option>
                                @foreach ($wilayah as $w)
                                    <option value="{{ $w->kode_wilayah }}"
                                        {{ request('kode_wilayah') == $w->kode_wilayah ? 'selected' : '' }}>
                                        {{ $w->nama_wilayah }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2 d-grid">
                            <button
                                class="btn btn-sm btn-primary w-100 d-flex align-items-center justify-content-center gap-1">
                                <i class="bi bi-search"></i> Filter
                            </button>
                        </div>
                        <div class="col-md-2 d-grid">
                            <a class="btn btn-sm btn-success w-100 d-flex align-items-center justify-content-center gap-1"
                                href="{{ route('cetakKirimanSales', request()->except(['_token'])) }}" target="_blank">
                                <i class="bi bi-printer"></i> Cetak Rekap Kiriman
                            </a>
                        </div>

                        <!-- Tombol Cetak Kiriman Gudang -->
                        <div class="col-md-2 d-grid">
                            <a class="btn btn-sm btn-secondary w-100 d-flex align-items-center justify-content-center gap-1"
                                href="{{ route('cetakKirimanGudang', request()->except(['_token'])) }}"
                                target="_blank">
                                <i class="bi bi-box-seam"></i> Cetak Kiriman Barang
                            </a>
                        </div>
                    </div>
                </form>


                <div class="table-responsive">
                    <table class="table table-sm table-bordered align-middle" id="tabelKiriman">
                        <thead class="table-light text-center">
                            <tr>
                                <th style="width: 2%;">No</th>
                                <th style="width:13%">No Faktur</th>
                                <th style="width:9%">Tanggal</th>
                                <th>Pelanggan</th>
                                <th>Sales</th>
                                <th style="width:12%">Wilayah</th>
                                <th style="width:9%">Total</th>
                                <th style="width:5%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $grandTotal = 0; @endphp
                            @forelse($data as $i => $d)
                                <tr>
                                    <td class="text-center">{{ $i + 1 }}</td>
                                    <td>{{ $d->no_faktur }}</td>
                                    <td class="text-center">{{ $d->tanggal }}</td>
                                    <td>{{ $d->nama_pelanggan }}</td>
                                    <td>{{ $d->nama_sales }}</td>
                                    <td>{{ $d->nama_wilayah }}</td>
                                    <td class="text-end">
                                        @php $grandTotal += $d->grand_total; @endphp
                                        {{ number_format($d->grand_total, 0, ',', '.') }}
                                    </td>
                                    <td class="text-center">
                                        <a href="#" data-href="{{ route('deleteKirimanSales', $d->id) }}"
                                            class="btn btn-sm btn-danger btn-hapus">
                                            <i class="bi bi-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted">Tidak ada data ditemukan.</td>
                                </tr>
                            @endforelse
                        </tbody>
                        <tfoot class="table-light fw-bold text-end">
                            <tr>
                                <td colspan="6" class="text-end">Total</td>
                                <td>{{ 'Rp' . number_format($grandTotal, 0, ',', '.') }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

            </div>
        </div>
    </div>

    <script>
        $(function() {
            $('.select2').select2({
                width: '100%'
            });

            $('.select2').select2({
                width: '100%'
            });

            $('.btn-hapus').on('click', function() {
                let href = $(this).data('href');
                let row = $(this).closest('tr');

                Swal.fire({
                    title: 'Yakin ingin menghapus?',
                    text: "Data kiriman ini akan dihapus permanen!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: href,
                            type: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}',
                                _method: 'POST'
                            },
                            success: function() {
                                Swal.fire('Berhasil!', 'Data berhasil dihapus.',
                                    'success');
                                row.fadeOut(300, function() {
                                    $(this).remove();
                                });
                            },
                            error: function() {
                                Swal.fire('Gagal!', 'Terjadi kesalahan saat menghapus.',
                                    'error');
                            }
                        });
                    }
                });
            });
        });
    </script>
@endsection
