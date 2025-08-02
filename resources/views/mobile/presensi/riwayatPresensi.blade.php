@extends('mobile.presensi.layout')
@section('title', 'Riwayat Presensi')
@section('header', 'Riwayat Presensi')
@section('content')

    <!-- HEADER -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary px-3 py-3 shadow-sm rounded-bottom">
        <div class="container-fluid justify-content-center">
            <img src="{{ asset('assets/img/PresenTech.jpg') }}" alt="Avatar" class="img-fluid rounded"
                style="max-width: 60%;">
        </div>
    </nav>

    <div class="container-fluid px-3 py-3">
        <!-- FILTER FORM -->
        <div class="card shadow-sm mb-3">
            <div class="card-body">
                <form method="GET" action="{{ route('riwayatPresensi') }}" class="row g-2 align-items-end">
                    <div class="col-6">
                        <label for="dari" class="form-label mb-1">Dari</label>
                        <input type="date" name="dari" id="dari" class="form-control form-control-sm"
                            value="{{ request('dari') }}">
                    </div>
                    <div class="col-6">
                        <label for="sampai" class="form-label mb-1">Sampai</label>
                        <input type="date" name="sampai" id="sampai" class="form-control form-control-sm"
                            value="{{ request('sampai') }}">
                    </div>
                    <div class="col-12 d-grid">
                        <button type="submit" class="btn btn-sm btn-primary">Filter</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- RIWAYAT PRESENSI -->
        <!-- RIWAYAT PRESENSI -->
        <div class="card shadow-sm">
            <div class="card-body">
                <h5 class="card-title mb-3">Riwayat Presensi</h5>

                @forelse($presensiList as $item)
                    <div class="d-flex justify-content-between align-items-center py-3 px-2 rounded mb-2 bg-light presensi-item"
                        data-tanggal="{{ \Carbon\Carbon::parse($item->tanggal)->format('d M Y') }}"
                        data-jamin="{{ $item->jam_in ? substr($item->jam_in, 11, 5) : 'Belum Scan' }}"
                        data-jamout="{{ $item->jam_out ? substr($item->jam_out, 11, 5) : 'Belum Scan' }}"
                        data-fotoin="{{ $item->foto_in ? asset('storage/' . $item->foto_in) : asset('assets/img/no-image.png') }}"
                        data-fotoout="{{ $item->foto_out ? asset('storage/' . $item->foto_out) : asset('assets/img/no-image.png') }}">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-calendar-check me-3 fs-4 text-primary"></i>
                            <div>
                                <div class="fw-bold">
                                    @if ($item->jam_in)
                                        <span class="text-success">
                                            <i class="bi bi-box-arrow-in-right me-1"></i>
                                            {{ substr($item->jam_in, 11, 5) }}
                                        </span>
                                    @else
                                        <span class="text-muted">
                                            <i class="bi bi-box-arrow-in-right me-1"></i>
                                            Belum Scan
                                        </span>
                                    @endif
                                    &nbsp; - &nbsp;
                                    @if ($item->jam_out)
                                        <span class="text-danger">
                                            <i class="bi bi-box-arrow-left me-1"></i>
                                            {{ substr($item->jam_out, 11, 5) }}
                                        </span>
                                    @else
                                        <span class="text-muted">
                                            <i class="bi bi-box-arrow-left me-1"></i>
                                            Belum Scan
                                        </span>
                                    @endif
                                </div>
                                <div class="text-muted small">
                                    {{ \Carbon\Carbon::parse($item->tanggal)->format('d M Y') }}
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="alert alert-warning text-center m-0">
                        Belum ada riwayat presensi.
                    </div>
                @endforelse
            </div>
        </div>
    </div>



    <div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4 shadow-lg">
                <div class="modal-header bg-primary text-white rounded-top">
                    <h5 class="modal-title" id="detailModalLabel">Detail Presensi</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="text-center mb-3">
                        <small class="text-muted">Tanggal</small>
                        <h5 id="modalTanggal" class="mb-3 fw-bold"></h5>
                    </div>

                    <div class="d-flex justify-content-around mb-3">
                        <div class="text-center">
                            <div class="rounded-circle overflow-hidden border border-success mb-2"
                                style="width: 80px; height: 80px;">
                                <img id="fotoMasuk" src="" alt="Foto Masuk"
                                    style="width: 100%; height: 100%; object-fit: cover;">
                            </div>
                            <div>
                                <span class="badge bg-success">Masuk</span>
                                <div id="modalJamIn" class="fw-bold mt-1">-</div>
                            </div>
                        </div>
                        <div class="text-center">
                            <div class="rounded-circle overflow-hidden border border-danger mb-2"
                                style="width: 80px; height: 80px;">
                                <img id="fotoPulang" src="" alt="Foto Pulang"
                                    style="width: 100%; height: 100%; object-fit: cover;">
                            </div>
                            <div>
                                <span class="badge bg-danger">Pulang</span>
                                <div id="modalJamOut" class="fw-bold mt-1">-</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $('.presensi-item').on('click', function() {
                const tanggal = $(this).data('tanggal');
                const jamin = $(this).data('jamin');
                const jamout = $(this).data('jamout');
                const fotoIn = $(this).data('fotoin');
                const fotoOut = $(this).data('fotoout');

                $('#modalTanggal').text(tanggal);
                $('#modalJamIn').text(jamin ? jamin : 'Belum Scan');
                $('#modalJamOut').text(jamout ? jamout : 'Belum Scan');

                $('#fotoMasuk').attr('src', fotoIn ? fotoIn : '{{ asset('assets/img/mjap.png') }}');
                $('#fotoPulang').attr('src', fotoOut ? fotoOut : '{{ asset('assets/img/mjap.png') }}');

                $('#detailModal').modal('show');
            });
        });
    </script>
@endsection
