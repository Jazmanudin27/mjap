@extends('layouts.template')
@section('titlepage', 'Data Pengajuan Limit Kredit')

@section('contents')
    <div class="container-fluid p-0">
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm border-0 rounded-4">
                    <div
                        class="card-header bg-primary text-white rounded-top-4 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-bold"><i class="fa fa-credit-card me-2"></i> Data Pengajuan Limit Kredit</h5>
                        <button type="button" class="btn btn-light btn-sm text-primary fw-semibold" data-bs-toggle="modal"
                            data-bs-target="#modalTambahPengajuan">
                            <i class="fa fa-plus-circle me-1"></i> Tambah Pengajuan
                        </button>
                    </div>
                    <div class="card-body mt-3">
                        <form method="GET" action="{{ route('viewPengajuanLimit') }}" class="mb-4">
                            <div class="row g-2">
                                <div class="col-md-4">
                                    <select name="kode_pelanggan" class="form-select2 form-select-sm">
                                        <option value="">-- Semua Pelanggan --</option>
                                        @foreach ($pelanggan as $p)
                                            <option value="{{ $p->kode_pelanggan }}"
                                                {{ request('kode_pelanggan') == $p->kode_pelanggan ? 'selected' : '' }}>
                                                {{ $p->kode_pelanggan }} - {{ $p->nama_pelanggan }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <select name="status" class="form-select2 form-select-sm">
                                        <option value="">Semua Status</option>
                                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>
                                            Pending
                                        </option>
                                        <option value="disetujui" {{ request('status') == 'disetujui' ? 'selected' : '' }}>
                                            Disetujui</option>
                                        <option value="ditolak" {{ request('status') == 'ditolak' ? 'selected' : '' }}>
                                            Ditolak
                                        </option>
                                    </select>
                                </div>
                                <div class="col-md-2 d-grid">
                                    <button type="submit" class="btn btn-sm btn-primary">Filter</button>
                                </div>
                            </div>
                        </form>
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 8%;">No Ajuan</th>
                                        <th style="width: 8%;">Tanggal</th>
                                        <th>Pelanggan</th>
                                        <th>Alasan</th>
                                        <th class="text-end" style="width: 13%;">Jumlah</th>
                                        <th style="width: 10%;">Status</th>
                                        <th style="width: 14%;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($pengajuan as $item)
                                        <tr>
                                            <td>{{ $item->id }}</td>
                                            <td>{{ tanggal_indo2($item->tanggal) }}</td>
                                            <td>{{ $item->kode_pelanggan . ' - ' . $item->nama_pelanggan }}</td>
                                            <td>{{ $item->alasan }}</td>
                                            <td class="text-end">Rp{{ number_format($item->nilai_pengajuan, 0, ',', '.') }}
                                            </td>
                                            <td class="text-center">
                                                <span
                                                    class="btn btn-sm btn-{{ $item->status_global == 'disetujui' ? 'success' : ($item->status_global == 'ditolak' ? 'danger' : 'warning') }}">
                                                    {{ ucfirst($item->status_global) }}
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <button type="button" class="btn btn-sm btn-primary btn-open-approval"
                                                    data-id="{{ $item->approval_id }}" data-kode="{{ $item->id }}"
                                                    data-pelanggan="{{ $item->kode_pelanggan . ' - ' . $item->nama_pelanggan }}"
                                                    data-nilai="{{ $item->nilai_pengajuan }}"
                                                    data-pengaju="{{ $item->user_id }}"
                                                    data-disetujui="{{ $item->disetujui ? '1' : '0' }}"
                                                    data-ditolak="{{ $item->ditolak ? '1' : '0' }}">
                                                    <i class="fa fa-list"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-danger delete"
                                                    data-href="{{ route('deletePengajuanLimit', $item->id) }}">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                                @if ($item->user_id == Auth::user()->nik && !$item->disetujui && !$item->ditolak)
                                                    <button type="button" class="btn btn-sm btn-primary btn-open-approval"
                                                        data-id="{{ $item->approval_id }}" data-kode="{{ $item->id }}"
                                                        data-pelanggan="{{ $item->kode_pelanggan . ' - ' . $item->nama_pelanggan }}"
                                                        data-nilai="{{ $item->nilai_pengajuan }}"
                                                        data-pengaju="{{ $item->user_id }}"
                                                        data-disetujui="{{ $item->disetujui ? '1' : '0' }}"
                                                        data-ditolak="{{ $item->ditolak ? '1' : '0' }}">
                                                        <i class="fa fa-check-circle"></i>
                                                    </button>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="9" class="text-center text-muted">Tidak ada data pengajuan.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-3">
                            {{ $pengajuan->links('pagination::bootstrap-5') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="modalTambahPengajuan" tabindex="-1" aria-labelledby="modalTambahLabel" aria-hidden="true">
        <div class="modal-dialog modal-md modal-dialog-centered">
            <form action="{{ route('storePengajuanLimit') }}" method="POST" class="modal-content">
                @csrf
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="modalTambahLabel">
                        <i class="fa fa-plus-circle me-2"></i> Form Pengajuan Limit Kredit
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
                                placeholder="Contoh: Perlu tambahan limit karena volume pembelian meningkat." required></textarea>
                        </div>

                        <!-- Nilai Pengajuan -->
                        <div class="col-md-12">
                            <label for="nilai_pengajuan" class="form-label fw-semibold">Nilai Pengajuan</label>
                            <input type="text" name="nilai_pengajuan" id="nilai_pengajuan"
                                class="form-control form-control-sm format-rupiah text-end" placeholder="Rp 0" required>
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
    <div class="modal fade" id="modalApproval" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <form method="POST" id="approvalForm">
                    @csrf
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title">
                            <i class="fa fa-check-circle me-2"></i> Approval Pengajuan Limit Kredit
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>
                    <div class="modal-body px-4">

                        <!-- Info Pelanggan -->
                        <div class="mb-2">
                            <label class="fw-semibold">Kode Pelanggan:</label>
                            <div id="info-kode-pelanggan" class="text-muted">-</div>
                        </div>

                        <!-- Nilai Pengajuan -->
                        <div class="mb-2">
                            <label class="fw-semibold">Nilai Pengajuan:</label>
                            <div id="info-nilai-pengajuan" class="text-muted">Rp 0</div>
                        </div>

                        <!-- Riwayat Approval -->
                        <div class="mb-2">
                            <label class="fw-semibold">Riwayat Approval:</label>
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered mb-0">
                                    <thead class="table-light text-center">
                                        <tr>
                                            <th>Level</th>
                                            <th>Nama</th>
                                            <th>Status</th>
                                            <th>Keterangan</th>
                                            <th>Revisi Limit</th>
                                            <th>Tanggal</th>
                                        </tr>
                                    </thead>
                                    <tbody id="riwayat-approval">

                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="mt-4">
                            <label class="fw-semibold mb-2">Pembagian Limit Per Supplier:</label>
                            <div id="container-limit-supplier">
                                <!-- Field pertama default -->
                                <div class="row g-2 mb-2">
                                    <div class="col-md-8">
                                        <select name="supplier[]" class="form-select2 form-select-sm">
                                            <option value="">-- Pilih Supplier --</option>
                                            @php
                                                $suppliers = DB::table('supplier')
                                                    ->where('status', '1')
                                                    ->orderBy('nama_supplier')
                                                    ->get();
                                            @endphp
                                            @foreach ($suppliers as $supplier)
                                                <option value="{{ $supplier->kode_supplier }}">
                                                    {{ $supplier->nama_supplier }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <input type="text" name="limit_supplier[]"
                                            class="form-control form-control-sm text-end format-rupiah"
                                            placeholder="Rp 0">
                                    </div>
                                    <div class="col-md-12">
                                        <button type="button" class="btn btn-sm btn-primary mt-2 w-100"
                                            id="btnSimpanLimit">
                                            <i class="fa fa-plus me-1"></i> Tambah
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm">
                                <thead>
                                    <tr>
                                        <th>Supplier</th>
                                        <th style="width:20%">Limit</th>
                                        <th style="width:7%">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="tabel-supplier-limit">
                                </tbody>
                            </table>
                        </div>
                        <!-- Keterangan & Revisi Limit -->
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label class="form-label fw-semibold">Keterangan / Alasan Disetujui</label>
                                <textarea name="keterangan" class="form-control form-control-sm" rows="3" required></textarea>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label fw-semibold">Revisi Limit (Opsional)</label>
                                <input type="text" name="revisi_limit"
                                    class="form-control form-control-sm text-end format-rupiah" placeholder="Rp 0">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" name="aksi" value="setujui" class="btn btn-success">
                            <i class="fa fa-check me-1"></i> Setujui
                        </button>
                        <button type="submit" name="aksi" value="tolak" class="btn btn-danger">
                            <i class="fa fa-times me-1"></i> Tolak
                        </button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    {{-- SweetAlert Delete --}}
    <script>
        $(function() {
            $(document).ready(function() {

                const container = $('#container-limit-supplier');
                const maxFields = 10; // maksimal tambahan field

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
                    $('#info-nilai-pengajuan').text('Rp ' + Number(nilai).toLocaleString('id-ID'));

                    // Reset form & set action
                    $('#approvalForm')[0].reset();
                    $('#approvalForm').attr('action', `approvePengajuanLimit/${approvalId}`);

                    $('#riwayat-approval').show();

                    $('#riwayat-approval tbody').empty(); // Kosongkan riwayat approval

                    // Tampilkan loading atau placeholder
                    $('#riwayat-approval tbody').append(
                        '<tr><td colspan="6" class="text-center">Memuat riwayat approval...</td></tr>'
                    );

                    $.ajax({
                        url: `/getApprovalHistory/${kodePengajuan}`,
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
                let nilai = $('#nilai_pengajuan').val();
                // Hapus semua karakter selain angka
                nilai = nilai.replace(/[^0-9]/g, '');
                $('#nilai_pengajuan').val(nilai);
            });
        });
    </script>
@endsection
