@extends('mobile.layout')
@section('title', 'Pengajuan Limit Faktur')
@section('header', 'Pengajuan Limit Faktur')

@section('content')
    <div class="px-3 py-3 text-white"
        style="background: linear-gradient(135deg, #0d6efd, #0a58ca); box-shadow: 0 4px 12px rgba(0,0,0,0.15); border-bottom-left-radius: 1rem; border-bottom-right-radius: 1rem;">
        <div class="d-flex align-items-center">
            <div class="bg-white text-primary rounded-circle d-flex justify-content-center align-items-center shadow"
                style="width: 50px; height: 50px;">
                <i class="fa fa-money fa-lg"></i>
            </div>
            <div class="ms-3">
                <div class="fw-bold fs-5">Pengajuan Limit Faktur</div>
                <small class="text-white-50">Kelola dan pantau pengajuan Anda</small>
            </div>
        </div>
    </div>
    <div class="container py-3">
        @forelse ($pengajuan as $item)
            <div
                class="card shadow-sm rounded-4 border-start border-4
            border-{{ $item->status_global == 'disetujui' ? 'success' : ($item->status_global == 'ditolak' ? 'danger' : 'warning') }} mb-3">
                <div class="card-body small">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 fw-bold text-primary">#{{ $item->id }}</h6>
                        <span
                            class="badge rounded-pill bg-{{ $item->status_global == 'disetujui' ? 'success' : ($item->status_global == 'ditolak' ? 'danger' : 'warning') }}">
                            {{ ucfirst($item->status_global) }}
                        </span>
                    </div>

                    <div class="mt-2 text-muted small">
                        <i class="fa fa-calendar me-1"></i> {{ tanggal_indo2($item->tanggal) }}
                    </div>

                    <div class="mt-2">
                        <div class="fw-bold">
                            <i class="fa fa-user me-1 text-secondary"></i>
                            {{ $item->kode_pelanggan }} - {{ $item->nama_pelanggan }}
                        </div>
                    </div>

                    <div class="mt-2">
                        <small class="text-muted">Alasan</small><br>
                        <div class="fst-italic">{{ $item->alasan }}</div>
                    </div>

                    <div class="mt-2 d-flex justify-content-between align-items-center">
                        <small class="text-muted">Jumlah Diajukan:</small>
                        <span class="text-end fw-bold text-success">{{ number_format($item->jumlah_faktur, 0, ',', '.') }}
                            Faktur</span>
                    </div>

                    <div class="mt-3 d-flex gap-2">
                        <button type="button" class="btn btn-sm btn-outline-primary flex-fill btn-open-approval"
                            data-id="{{ $item->approval_id }}" data-kode="{{ $item->id }}"
                            data-pelanggan="{{ $item->kode_pelanggan . ' - ' . $item->nama_pelanggan }}"
                            data-nilai="{{ $item->jumlah_faktur }}" data-pengaju="{{ $item->user_id }}"
                            data-disetujui="{{ $item->disetujui ? '1' : '0' }}"
                            data-ditolak="{{ $item->ditolak ? '1' : '0' }}">
                            <i class="fa fa-info-circle me-1"></i> Detail
                        </button>

                        @if ($item->user_id == Auth::user()->nik && !$item->disetujui && !$item->ditolak)
                            <button type="button" class="btn btn-sm btn-outline-success flex-fill btn-open-approval"
                                data-id="{{ $item->approval_id }}" data-kode="{{ $item->id }}"
                                data-pelanggan="{{ $item->kode_pelanggan . ' - ' . $item->nama_pelanggan }}"
                                data-nilai="{{ $item->jumlah_faktur }}" data-pengaju="{{ $item->user_id }}"
                                data-disetujui="{{ $item->disetujui ? '1' : '0' }}"
                                data-ditolak="{{ $item->ditolak ? '1' : '0' }}">
                                <i class="fa fa-check-circle me-1"></i> Approve
                            </button>

                            <button type="button" class="btn btn-sm btn-outline-danger flex-fill delete"
                                data-href="{{ route('deletePengajuanFaktur', $item->id) }}">
                                <i class="fa fa-trash me-1"></i> Hapus
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="alert alert-warning text-center small rounded-pill">Tidak ada data pengajuan.</div>
        @endforelse

        <div class="mt-4">
            {{ $pengajuan->links('pagination::bootstrap-5') }}
        </div>
    </div>
    <div class="modal fade" id="modalApproval" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form method="POST" id="approvalForm">
                    @csrf
                    <div class="modal-header bg-primary text-white">
                        <h6 class="modal-title">
                            <i class="fa fa-check-circle me-2"></i> Approval Faktur
                        </h6>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>

                    <div class="modal-body py-2 px-3">
                        <div class="mb-2">
                            <small class="fw-semibold">Kode Pelanggan:</small>
                            <div id="info-kode-pelanggan" class="text-muted">-</div>
                        </div>

                        <div class="mb-2">
                            <small class="fw-semibold">Jumlah Faktur:</small>
                            <div id="info-nilai-pengajuan" class="text-muted">0</div>
                        </div>

                        <div class="mb-2">
                            <small class="fw-semibold">Riwayat Approval:</small>
                            <div class="table-responsive" style="max-height: 150px; overflow-y: auto;">
                                <table class="table table-sm table-bordered mb-0 text-nowrap">
                                    <thead class="table-light text-center">
                                        <tr>
                                            <th>Level</th>
                                            <th>Nama</th>
                                            <th>Status</th>
                                            <th>Keterangan</th>
                                            <th>Revisi</th>
                                            <th>Tgl</th>
                                        </tr>
                                    </thead>
                                    <tbody id="riwayat-approval"></tbody>
                                </table>
                            </div>
                        </div>

                        <div class="mb-2">
                            <label class="form-label">Keterangan</label>
                            <textarea name="keterangan" class="form-control form-control-sm" rows="3"></textarea>
                        </div>
                    </div>

                    <div class="modal-footer py-2 px-3">
                        <div class="d-grid gap-2 w-100">
                            <button type="submit" name="aksi" value="setujui" class="btn btn-sm btn-success">
                                <i class="fa fa-check me-1"></i> Setujui
                            </button>
                            <button type="submit" name="aksi" value="tolak" class="btn btn-sm btn-danger">
                                <i class="fa fa-times me-1"></i> Tolak
                            </button>
                            <button type="button" class="btn btn-sm btn-secondary"
                                data-bs-dismiss="modal">Tutup</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <a href="#" class="btn btn-primary rounded-circle shadow position-fixed" data-bs-toggle="modal"
        data-bs-target="#modalTambahPengajuan"
        style="bottom: 80px; right: 20px; z-index: 1050; width: 55px; height: 55px; display: flex; align-items: center; justify-content: center;">
        <i class="bi bi-plus fs-4"></i>
    </a>
    <div class="modal fade" id="modalTambahPengajuan" tabindex="-1" aria-labelledby="modalTambahLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-md modal-dialog-centered">
            <form action="{{ route('storePengajuanFakturMobile') }}" method="POST" class="modal-content">
                @csrf
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="modalTambahLabel">
                        <i class="fa fa-plus-circle me-2"></i> Form Ajuan Limit Faktur
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>

                <div class="modal-body px-4 py-3">
                    <div class="row g-3">

                        <!-- Pilih Pelanggan -->
                        <div class="col-md-12">
                            <label for="kode_pelanggan" class="form-label fw-semibold">Pilih Pelanggan</label>
                            <select id="kode_pelanggan" name="kode_pelanggan" class="form-select form-select-sm"
                                tabindex="1" required></select>
                        </div>

                        <!-- Alasan Pengajuan -->
                        <div class="col-md-12">
                            <label for="alasan" class="form-label fw-semibold">Alasan Pengajuan</label>
                            <textarea name="alasan" id="alasan" class="form-control form-control-sm" rows="3"
                                placeholder="Contoh: Perlu tambahan limit karena volume pembelian meningkat."></textarea>
                        </div>

                        <!-- Nilai Pengajuan -->
                        <div class="col-md-12">
                            <label for="jumlah_faktur" class="form-label fw-semibold">Jumlah Faktur</label>
                            <input type="text" name="jumlah_faktur" id="jumlah_faktur"
                                class="form-control form-control-sm text-end" placeholder="0" required>
                        </div>

                    </div>
                </div>

                <div class="modal-footer px-4 pb-3">
                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-save me-1"></i> Simpan Pengajuan
                    </button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        $(function() {
            $(document).ready(function() {
                const container = $('#container-limit-supplier');
                const maxFields = 10;

                $('#modalApproval').on('shown.bs.modal', function() {
                    $('.form-select2').select2({
                        dropdownParent: $('#modalApproval'),
                        width: '100%'
                    });
                });

                $(document).on('click', '.btn-delete', function() {
                    const row = $(this).closest('tr');
                    const id = row.data('id');

                    Swal.fire({
                        title: 'Yakin hapus?',
                        text: 'Data ini tidak bisa dikembalikan!',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Ya, Hapus!'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.ajax({
                                url: "{{ route('hapusLimitSupplier') }}",
                                method: "POST",
                                headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]')
                                        .attr('content')
                                },
                                data: {
                                    id: id
                                },
                                success: function(res) {
                                    if (res.success) {
                                        Swal.fire('Terhapus!', res.message,
                                            'success');
                                        row.remove(); // Hapus baris dari tabel
                                        updateTotalLimit
                                            (); // Hitung ulang total
                                    } else {
                                        Swal.fire('Error!', res.message,
                                            'error');
                                    }
                                },
                                error: function(xhr) {
                                    Swal.fire('AJAX Error!', xhr.responseText,
                                        'error');
                                }
                            });
                        }
                    });
                });

                function updateTotalLimit() {
                    let totalLimit = 0;
                    $('#tabel-supplier-limit tr:not(.table-primary)').each(function() {
                        if (!$(this).find('td').eq(1).hasClass(
                                'text-danger')) { // abaikan baris error
                            let limitText = $(this).find('td:eq(1)').text().replace(/[^0-9]/g, '');
                            totalLimit += parseInt(limitText) || 0;
                        }
                    });

                    // Hapus baris total sebelumnya
                    $('#tabel-supplier-limit tr.table-primary').remove();

                    // Tambahkan baris total baru
                    $('#tabel-supplier-limit').append(`
                        <tr class="table-primary fw-bold">
                            <td>Total</td>
                            <td class="text-end">Rp ${totalLimit.toLocaleString('id-ID')}</td>
                            <td></td>
                        </tr>
                    `);
                }

                const loginUser = '{{ Auth::user()->nik }}';

                $(document).on('click', '.btn-open-approval', function() {
                    const approvalId = $(this).data('id');
                    const kodePengajuan = $(this).data('kode');
                    const pelanggan = $(this).data('pelanggan');
                    const nilai = $(this).data('nilai');
                    const pengaju = $(this).data('pengaju');
                    const disetujui = $(this).data('disetujui') == 1;
                    const ditolak = $(this).data('ditolak') == 1;

                    // Tampilkan info pelanggan & nilai
                    $('#info-kode-pelanggan').text(pelanggan);
                    $('#info-nilai-pengajuan').text(Number(nilai).toLocaleString('id-ID'));

                    // Reset form & set action
                    $('#approvalForm')[0].reset();
                    $('#approvalForm').attr('action', `approvePengajuanFakturMobile/${approvalId}`);

                    $('#riwayat-approval').show();

                    $('#riwayat-approval tbody').empty(); // Kosongkan riwayat approval

                    // Tampilkan loading atau placeholder
                    $('#riwayat-approval tbody').append(
                        '<tr><td colspan="6" class="text-center">Memuat riwayat approval...</td></tr>'
                    );

                    $.ajax({
                        url: `/getApprovalHistoryFaktur/${kodePengajuan}`,
                        method: "GET",
                        success: function(response) {
                            $('#riwayat-approval').empty(); // Kosongkan tbody

                            if (response.riwayat_approval && response.riwayat_approval
                                .length > 0) {
                                response.riwayat_approval.forEach(function(item) {
                                    let statusBadge = '';
                                    if (item.disetujui) {
                                        statusBadge =
                                            '<span class="badge bg-success">Disetujui</span>';
                                    } else if (item.ditolak) {
                                        statusBadge =
                                            '<span class="badge bg-danger">Ditolak</span>';
                                    } else {
                                        statusBadge =
                                            '<span class="badge bg-secondary">Menunggu</span>';
                                    }

                                    $('#riwayat-approval').append(`
                                        <tr>
                                            <td>${item.level_approval}</td>
                                            <td>${item.nama ?? '-'}</td>
                                            <td>${statusBadge}</td>
                                            <td>${item.keterangan ?? '-'}</td>
                                            <td class="text-end">${item.revisi_limit ? 'Rp ' + Number(item.revisi_limit).toLocaleString('id-ID') : '-'}</td>
                                            <td>${item.tanggal_approval ?? '-'}</td>
                                        </tr>
                                    `);
                                });
                            } else {
                                $('#riwayat-approval').append(
                                    '<tr><td colspan="6" class="text-center">Belum ada riwayat approval.</td></tr>'
                                );
                            }
                        },
                        error: function() {
                            $('#riwayat-approval').empty().append(
                                '<tr><td colspan="6" class="text-danger text-center">Gagal memuat riwayat approval.</td></tr>'
                            );
                        }
                    });

                    // Sembunyikan form approval & footer dulu
                    $('#approvalForm .row.g-3').hide();
                    $('#approvalForm .modal-footer').hide();

                    if (loginUser === pengaju && !disetujui && !ditolak) {
                        $('#approvalForm .row.g-3').show();
                        $('#approvalForm .modal-footer').show();
                    }

                    // Tampilkan modal
                    $('#modalApproval').modal('show');

                    // Ambil data supplier & limit dari server dan tampilkan di tabel
                    $.ajax({
                        url: `/getLimitSupplier/${kodePengajuan}`,
                        method: "GET",
                        success: function(response) {
                            $('#tabel-supplier-limit').empty();
                            if (response.data.length > 0) {
                                let totalLimit = 0;
                                response.data.forEach(function(item) {
                                    const limitNumber = Number(item.limit);
                                    totalLimit += limitNumber;
                                    $('#tabel-supplier-limit').append(`
                                        <tr data-id="${item.id}" data-supplier-kode="${item.supplier_kode}">
                                            <td>${item.nama_supplier}</td>
                                            <td class="text-end">Rp ${limitNumber.toLocaleString('id-ID')}</td>
                                            <td class="text-center">
                                                <button class="btn btn-sm btn-danger btn-delete"><i class="fa fa-trash"></i></button>
                                            </td>
                                        </tr>
                                    `);
                                });

                                // Tambahkan baris total
                                $('#tabel-supplier-limit').append(`
                                    <tr class="table-primary fw-bold">
                                        <td>Total</td>
                                        <td class="text-end">Rp ${totalLimit.toLocaleString('id-ID')}</td>
                                        <td></td>
                                    </tr>
                                `);
                            } else {
                                $('#tabel-supplier-limit').append(
                                    '<tr><td colspan="3" class="text-center">Tidak ada data</td></tr>'
                                );
                            }
                        },
                        error: function() {
                            $('#tabel-supplier-limit').append(
                                '<tr><td colspan="3" class="text-danger text-center">Gagal memuat data.</td></tr>'
                            );
                            Swal.fire('Error!', 'Gagal memuat data supplier.', 'error');
                        }
                    });

                    $('#btnSimpanLimit').off('click').on('click', function() {
                        let pengajuanId = kodePengajuan;
                        let suppliers = [];
                        let limits = [];

                        // Ambil semua input supplier & limit
                        $('select[name="supplier[]"]').each(function(index) {
                            let supplier = $(this).val();
                            let limit = $('input[name="limit_supplier[]"]').eq(
                                index).val();
                            if (supplier && limit) {
                                suppliers.push(supplier);
                                limits.push(limit);
                            }
                        });

                        $.ajax({
                            url: "{{ route('simpanLimitSupplier') }}",
                            method: "POST",
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr(
                                    'content')
                            },
                            data: {
                                pengajuan_id: pengajuanId,
                                supplier: suppliers,
                                limit_supplier: limits
                            },
                            success: function(res) {
                                if (res.success) {
                                    Swal.fire('Berhasil!', res.message,
                                        'success');

                                    // Ambil data terbaru dari server
                                    $.ajax({
                                        url: `/getLimitSupplier/${pengajuanId}`,
                                        method: "GET",
                                        success: function(response) {
                                            $('#tabel-supplier-limit')
                                                .empty();
                                            if (response.data
                                                .length > 0) {
                                                let totalLimit = 0;
                                                response.data
                                                    .forEach(
                                                        function(
                                                            item) {
                                                            const
                                                                limitNumber =
                                                                Number(
                                                                    item
                                                                    .limit
                                                                );
                                                            totalLimit
                                                                +=
                                                                limitNumber;
                                                            $('#tabel-supplier-limit')
                                                                .append(`
                                        <tr data-id="${item.id}" data-supplier-kode="${item.supplier_kode}">
                                            <td>${item.nama_supplier}</td>
                                            <td class="text-end">Rp ${limitNumber.toLocaleString('id-ID')}</td>
                                            <td class="text-center">
                                                <button class="btn btn-sm btn-danger btn-delete"><i class="fa fa-trash"></i></button>
                                            </td>
                                        </tr>
                                    `);
                                                        });

                                                // Tambahkan baris total
                                                $('#tabel-supplier-limit')
                                                    .append(`
                                    <tr class="table-primary fw-bold">
                                        <td>Total</td>
                                        <td class="text-end">Rp ${totalLimit.toLocaleString('id-ID')}</td>
                                        <td></td>
                                    </tr>
                                `);
                                            } else {
                                                $('#tabel-supplier-limit')
                                                    .append(
                                                        '<tr><td colspan="3" class="text-center">Tidak ada data</td></tr>'
                                                    );
                                            }
                                        },
                                        error: function() {
                                            $('#tabel-supplier-limit')
                                                .append(
                                                    '<tr><td colspan="3" class="text-danger text-center">Gagal memuat data.</td></tr>'
                                                );
                                            Swal.fire('Error!',
                                                'Gagal memuat data supplier.',
                                                'error');
                                        }
                                    });

                                    // Reset form jika diperlukan
                                    $('select[name="supplier[]"]').val('')
                                        .trigger('change');
                                    $('input[name="limit_supplier[]"]').val('');
                                    $('#btnSimpanLimit').html(
                                        '<i class="fa fa-plus me-1"></i> Tambah'
                                    );
                                    $('#container-limit-supplier').removeData(
                                        'edit-id');
                                } else {
                                    Swal.fire('Error!', res.message, 'error');
                                }
                            },
                            error: function(xhr, status, error) {
                                Swal.fire('AJAX Error!', error, 'error');
                            }
                        });
                    });
                });


                $('#kode_pelanggan').select2({
                    placeholder: 'Cari pelanggan…',
                    width: '100%',
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

                $(document).on("click", ".delete", function(e) {
                    e.preventDefault();
                    const url = $(this).data('href');

                    Swal.fire({
                        title: 'Hapus pengajuan?',
                        text: 'Data ini akan dihapus permanen.',
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

                $(document).on('input', '.format-rupiah', function() {
                    let val = $(this).val().replace(/[^,\d]/g, '');
                    let split = val.split(',');
                    let sisa = split[0].length % 3;
                    let rupiah = split[0].substr(0, sisa);
                    let ribuan = split[0].substr(sisa).match(/\d{3}/gi);

                    if (ribuan) {
                        let separator = sisa ? '.' : '';
                        rupiah += separator + ribuan.join('.');
                    }

                    rupiah = split[1] !== undefined ? rupiah + ',' + split[1] : rupiah;
                    $(this).val('Rp ' + rupiah);
                });
            });

            $('form').on('submit', function() {
                let nilai = $('#jumlah_faktur').val();
                // Hapus semua karakter selain angka
                nilai = nilai.replace(/[^0-9]/g, '');
                $('#jumlah_faktur').val(nilai);
            });
        });
    </script>
@endsection
