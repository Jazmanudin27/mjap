@extends('layouts.template')
@section('titlepage', 'Data Pelanggan')

@section('contents')
    <div class="container-fluid p-0">
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm border-0 rounded-4">
                    <div
                        class="card-header bg-primary text-white rounded-top-4 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-bold"><i class="fa fa-users me-2"></i> Data Pelanggan</h5>
                        <a href="{{ route('tambahPelanggan') }}" class="btn btn-light btn-sm text-primary fw-semibold">
                            <i class="fa fa-plus-circle me-1"></i> Tambah Data
                        </a>
                    </div>
                    <div class="card-body mt-3">
                        <form method="GET" action="{{ route('viewPelanggan') }}" class="mb-4" id="filterForm">
                            <div class="row g-2">
                                <div class="col-md-3">
                                    <input type="text" name="nama_pelanggan" class="form-control form-control-sm"
                                        placeholder="Nama Pelanggan" value="{{ request('nama_pelanggan') }}">
                                </div>
                                <div class="col-md-2">
                                    <input type="text" name="kode_pelanggan" class="form-control form-control-sm"
                                        placeholder="Kode Pelanggan" value="{{ request('kode_pelanggan') }}">
                                </div>
                                <div class="col-md-2">
                                    <select name="status" class="form-select form-select-sm">
                                        <option value="">Semua Status</option>
                                        <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Aktif
                                        </option>
                                        <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Nonaktif
                                        </option>
                                    </select>
                                </div>
                                <!-- input kolom-kolom -->
                                <div class="col-md-6 col-lg-4 d-flex gap-2">
                                    <button type="submit" name="action" value="filter"
                                        class="btn btn-primary btn-sm w-100">
                                        <i class="fa fa-filter me-1"></i> Filter
                                    </button>
                                    <button type="submit" name="action" value="cetak"
                                        class="btn btn-secondary btn-sm w-100 btn-open-blank">
                                        <i class="fa fa-print me-1"></i> Cetak
                                    </button>
                                    <button type="submit" name="action" value="export"
                                        class="btn btn-success btn-sm w-100 btn-open-blank">
                                        <i class="fa fa-file-excel me-1"></i> Export
                                    </button>
                                </div>
                            </div>
                        </form>

                        <div class="table-responsive">
                            <table class="table table-hover table-striped table-sm table-bordered align-middle">
                                <thead class="table-primary text-center">
                                    <tr>
                                        <th style="width: 50px;">No</th>
                                        <th>Kode</th>
                                        <th>Nama Pelanggan</th>
                                        <th>Wilayah</th>
                                        <th>Limit</th>
                                        <th>Foto</th>
                                        <th style="width: 120px;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($pelanggan as $index => $s)
                                        <tr>
                                            <td class="text-center">{{ $pelanggan->firstItem() + $index }}</td>
                                            <td>{{ $s->kode_pelanggan }}</td>
                                            <td>{{ $s->nama_pelanggan }}</td>
                                            <td>{{ $s->nama_wilayah }}</td>
                                            <td class="text-end">Rp. {{ number_format($s->limit_pelanggan) }}</td>
                                            <td class="text-center">
                                                @if ($s->foto)
                                                    <button type="button" class="btn btn-sm btn-primary lihat-foto"
                                                        data-foto="{{ asset('storage/pelanggan/' . $s->foto) }}"
                                                        data-nama="{{ $s->nama_pelanggan }}">
                                                        <i class="fa fa-image"></i>
                                                    </button>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <a href="{{ route('toggleStatusPelanggan', $s->kode_pelanggan) }}"
                                                    class="btn btn-sm {{ $s->status == '1' ? 'btn-success' : 'btn-danger' }}">
                                                    <i
                                                        class="fa {{ $s->status == '1' ? 'fa-thumbs-up' : 'fa-thumbs-down' }}"></i>
                                                </a>
                                                <button type="button" class="btn btn-sm btn-danger delete"
                                                    data-href="{{ route('deletePelanggan', $s->kode_pelanggan) }}">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                                <a href="{{ route('detailPelanggan', $s->kode_pelanggan) }}"
                                                    class="btn btn-sm btn-info">
                                                    <i class="fa fa-list"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center text-muted">Tidak ada data.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-3">
                            {{ $pelanggan->links('pagination::bootstrap-5') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(function() {
            $(document).on("click", ".lihat-foto", function(e) {
                e.preventDefault();
                Swal.fire({
                    title: $(this).data('nama'),
                    imageUrl: $(this).data('foto'),
                    imageWidth: 450,
                });
            });

            $(document).on("click", ".delete", function(e) {
                e.preventDefault();
                const url = $(this).data('href');

                Swal.fire({
                    title: 'Hapus data?',
                    text: 'Data pelanggan akan dihapus permanen.',
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
        $(document).ready(function() {
            $('.btn-open-blank').on('click', function() {
                $('#filterForm').attr('target', '_blank');
            });

            $('button[name="action"][value="filter"]').on('click', function() {
                $('#filterForm').removeAttr('target');
            });
        });
    </script>
@endsection
