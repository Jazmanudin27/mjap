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
                            <select name="kode_wilayah" id="kode_wilayah" class="form-select form-select-sm select2">
                                <option value="">-- Semua Wilayah --</option>
                                @foreach ($wilayah as $w)
                                    <option value="{{ $w->kode_wilayah }}"
                                        {{ request('kode_wilayah') == $w->kode_wilayah ? 'selected' : '' }}>
                                        {{ $w->nama_wilayah }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <select name="kirimanke" id="kirimanke" class="form-select select2">
                                <option value="">-- Semua Kiriman --</option>
                                <option value="1" {{ request('kirimanke') == '1' ? 'selected' : '' }}>Kiriman Ke-1
                                </option>
                                <option value="2" {{ request('kirimanke') == '2' ? 'selected' : '' }}>Kiriman Ke-2
                                </option>
                                <option value="3" {{ request('kirimanke') == '3' ? 'selected' : '' }}>Kiriman Ke-3
                                </option>
                            </select>
                        </div>
                        <div class="col-md-2 d-grid">
                            <button
                                class="btn btn-sm btn-primary w-100 d-flex align-items-center justify-content-center gap-1">
                                <i class="bi bi-search"></i> Filter
                            </button>
                        </div>
                        {{-- <div class="col-md-2 d-grid">
                            <a class="btn btn-sm btn-success w-100 d-flex align-items-center justify-content-center gap-1"
                                href="{{ route('cetakKirimanSales', request()->except(['_token'])) }}" target="_blank">
                                <i class="bi bi-printer"></i> Cetak Rekap Kiriman
                            </a>
                        </div>
                        <div class="col-md-2 d-grid">
                            <a class="btn btn-sm btn-secondary w-100 d-flex align-items-center justify-content-center gap-1"
                                href="{{ route('cetakKirimanGudang', request()->except(['_token'])) }}" target="_blank">
                                <i class="bi bi-box-seam"></i> Cetak Kiriman Barang
                            </a>
                        </div> --}}
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-sm table-bordered align-middle" id="tabelKiriman">
                        <thead class="table-light text-center">
                            <tr>
                                <th style="width: 2%;">No</th>
                                <th style="width:9%">Tgl Kirim</th>
                                <th>Wilayah</th>
                                <th style="width:15%">Total</th>
                                <th style="width:10%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $no = 1;
                                $grandTotal = 0;
                            @endphp
                            @forelse ($data as $d)
                                <tr>
                                    <td class="text-center">{{ $no++ }}</td>
                                    <td class="text-center">{{ tanggal_indo2($d->tanggal) }}</td>
                                    <td>{{ $d->nama_wilayah }}</td>
                                    <td class="text-end">
                                        @php $grandTotal += $d->total; @endphp
                                        {{ number_format($d->total, 0, ',', '.') }}
                                    </td>
                                    <td class="text-center">
                                        <a href="#" class="btn btn-sm btn-danger  btn-hapus"
                                            data-tanggal="{{ $d->tanggal }}" data-kode_wilayah="{{ $d->kode_wilayah }}">
                                            <i class="bi bi-trash"></i>
                                        </a>
                                        <a href="{{ route('cetakKirimanSales', ['tanggal' => $d->tanggal, 'kode_wilayah' => $d->kode_wilayah, 'kirimanke' => $d->kirimanke]) }}"
                                            target="_blank" class="btn btn-sm btn-success">
                                            <i class="bi bi-printer"></i>
                                        </a>
                                        <a href="{{ route('cetakKirimanGudang', ['tanggal' => $d->tanggal, 'kode_wilayah' => $d->kode_wilayah, 'kirimanke' => $d->kirimanke]) }}"
                                            target="_blank" class="btn btn-sm btn-secondary ">
                                            <i class="bi bi-box-seam"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted">Tidak ada data ditemukan.</td>
                                </tr>
                            @endforelse
                        </tbody>
                        <tfoot class="table-light fw-bold text-end">
                            <tr>
                                <td colspan="3" class="text-end">Grand Total</td>
                                <td>{{ 'Rp' . number_format($grandTotal, 0, ',', '.') }}</td>
                                <td></td>
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
                let tanggal = $(this).data('tanggal');
                let kodeWilayah = $(this).data('kode_wilayah');
                let row = $(this).closest('tr');

                Swal.fire({
                    title: 'Yakin ingin menghapus?',
                    text: "Semua data kiriman untuk tanggal dan wilayah ini akan dihapus permanen!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('deleteGroupKirimanSales') }}",
                            type: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}',
                                tanggal: tanggal,
                                kode_wilayah: kodeWilayah
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
