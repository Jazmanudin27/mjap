@extends('mobile.presensi.layout')
@section('title', 'Surat Absen')
@section('header', 'Surat Absen')
@section('content')

    <!-- HEADER -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary px-3 py-3 shadow-sm"
        style="border-bottom-left-radius: 25px; border-bottom-right-radius: 15px;">
        <div class="container-fluid justify-content-center">
            <img src="{{ asset('assets/img/PresenTech.jpg') }}" alt="Avatar" class="img-fluid rounded"
                style="max-width: 60%;">
        </div>
    </nav>

    <div class="container-fluid px-3 py-3">
        <!-- FILTER FORM -->
        <div class="card shadow-sm mb-3">
            <div class="card-body">
                <form method="GET" action="{{ route('suratAbsen') }}" class="row g-2 align-items-end">
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

        <!-- LIST SURAT ABSEN -->
        <div class="card shadow-sm">
            <div class="card-body">
                <h5 class="card-title mb-3">Riwayat Surat Absen</h5>

                @forelse($suratAbsenList as $item)
                    @php
                        $jenisFull =
                            $item->jenis_absen == 'I' ? 'Izin' : ($item->jenis_absen == 'S' ? 'Sakit' : 'Cuti');
                        $badgeClass =
                            $item->jenis_absen == 'I'
                                ? 'bg-info'
                                : ($item->jenis_absen == 'S'
                                    ? 'bg-danger'
                                    : 'bg-warning text-dark');
                    @endphp
                    <div class="surat-item" data-tanggal="{{ \Carbon\Carbon::parse($item->tanggal)->format('d M Y') }}"
                        data-jenis="{{ $jenisFull }}" data-alasan="{{ $item->alasan }}" data-status="{{ $item->status }}"
                        data-id="{{ $item->id }}"
                        data-foto="{{ $item->foto_surat ? asset('storage/' . $item->foto_surat) : asset('assets/img/no-image.png') }}">
                        <div class="d-flex align-items-center">
                            <div class="me-3">
                                <span class="badge rounded-pill {{ $badgeClass }}"
                                    style="font-size: 1rem; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                                    {{ $item->jenis_absen }}
                                </span>
                            </div>
                            <div>
                                <div class="fw-semibold">{{ $jenisFull }}</div>
                                <small
                                    class="text-muted">{{ \Carbon\Carbon::parse($item->tanggal)->format('d M Y') }}</small>
                            </div>
                        </div>

                        <div class="text-end">
                            {{-- <i class="bi bi-chevron-right text-muted fs-5"></i> --}}
                        </div>
                    </div>
                @empty
                    <div class="alert alert-warning text-center m-0">
                        Belum ada surat absen.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
    <button type="button"
        class="btn btn-danger rounded-circle shadow-lg position-fixed d-flex justify-content-center align-items-center"
        style="width: 60px; height: 60px; bottom: 90px; right: 20px; z-index: 999;" data-bs-toggle="modal"
        data-bs-target="#tambahModal">
        <i class="bi bi-plus fs-3"></i>
    </button>

    <!-- Modal Tambah Surat Absen -->
    <div class="modal fade" id="tambahModal" tabindex="-1" aria-labelledby="tambahModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content rounded-4 shadow-lg">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="tambahModalLabel">Tambah Surat Absen</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <form id="formSuratAbsen" enctype="multipart/form-data">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Tanggal</label>
                            <input type="date" name="tanggal" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Jenis</label>
                            <select name="jenis" class="form-select" required>
                                <option value="">Pilih Jenis</option>
                                <option value="I">Izin</option>
                                <option value="S">Sakit</option>
                                <option value="C">Cuti</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Alasan</label>
                            <textarea name="alasan" class="form-control" rows="3" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Upload Surat (Foto)</label>
                            <input type="file" name="foto_surat" class="form-control" accept="image/*">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL DETAIL SURAT -->
    <div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4 shadow-lg">
                <div class="modal-header bg-primary text-white rounded-top">
                    <h5 class="modal-title" id="detailModalLabel">Detail Surat Absen</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="text-center mb-3">
                        <small class="text-muted">Tanggal</small>
                        <h5 id="modalTanggal" class="mb-3 fw-bold"></h5>
                    </div>

                    <div class="text-center mb-3">
                        <span class="badge bg-secondary" id="modalJenis">-</span>
                    </div>

                    <div class="mb-3">
                        <strong>Alasan:</strong>
                        <p id="modalAlasan" class="mb-0"></p>
                    </div>

                    <div class="mb-3 text-center">
                        <small class="text-muted">Status</small>
                        <h5 id="modalStatus" class="mb-3 fw-bold badge bg-secondary"></h5>
                    </div>

                    <div class="text-center" id="fotoContainer" style="display: none;">
                        <div class="rounded overflow-hidden border mb-2" style="max-width: 200px; margin:auto;">
                            <img id="modalFoto" src="" alt="Foto Surat" style="width: 100%; height: auto;">
                        </div>
                    </div>

                    @if (Auth::user()->role == 'Admin')
                        <div class="d-flex justify-content-center gap-2 mb-3">
                            <button class="btn btn-success btn-sm" id="btnApprove">Approve</button>
                            <button class="btn btn-danger btn-sm" id="btnReject">Reject</button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {

            let currentId = null;

            $('.surat-item').on('click', function() {
                const tanggal = $(this).data('tanggal');
                const jenis = $(this).data('jenis');
                const alasan = $(this).data('alasan');
                const status = $(this).data('status');
                const foto = $(this).data('foto');
                currentId = $(this).data('id');

                $('#modalTanggal').text(tanggal);
                $('#modalJenis').text(jenis);
                $('#modalAlasan').text(alasan);
                $('#modalStatus').text(status)
                    .removeClass('bg-secondary bg-success bg-danger')
                    .addClass(status === 'Disetujui' ? 'bg-success' : (status === 'Ditolak' ? 'bg-danger' :
                        'bg-secondary'));

                if (foto && !foto.includes('no-image.png')) {
                    $('#modalFoto').attr('src', foto);
                    $('#fotoContainer').show();
                } else {
                    $('#fotoContainer').hide();
                }

                $('#detailModal').modal('show');
            });

            $('#btnApprove, #btnReject').on('click', function() {
                let status = $(this).attr('id') === 'btnApprove' ? 'Disetujui' : 'Ditolak';

                Swal.fire({
                    title: 'Yakin?',
                    text: "Anda akan mengubah status menjadi " + status,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Lanjutkan!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '{{ route('approveSuratAbsen') }}',
                            type: 'POST',
                            data: {
                                id: currentId,
                                status: status,
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                Swal.fire('Berhasil!', response.message, 'success')
                                    .then(() => {
                                        $('#detailModal').modal('hide');
                                        location.reload();
                                    });
                            }
                        });
                    }
                });
            });


            $('#formSuratAbsen').on('submit', function(e) {
                e.preventDefault();

                var formData = new FormData(this);

                $.ajax({
                    url: "{{ route('storeSuratAbsen') }}",
                    type: "POST",
                    data: formData,
                    contentType: false,
                    processData: false,
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: 'Surat Absen berhasil ditambahkan!',
                            showConfirmButton: false,
                            timer: 1500
                        }).then(() => {
                            $('#tambahModal').modal('hide');
                            location.reload();
                        });
                    },
                    error: function(xhr) {
                        let errorMsg = 'Gagal menyimpan data.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMsg = xhr.responseJSON.message;
                        }

                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: errorMsg,
                        });
                    }
                });
            });
        });
    </script>

@endsection
