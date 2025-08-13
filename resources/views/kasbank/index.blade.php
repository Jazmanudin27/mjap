@extends('layouts.template')
@section('titlepage', 'Mutasi Kas & Bank')
@section('contents')
    <style>
        input.no-arrow::-webkit-outer-spin-button,
        input.no-arrow::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        input.no-arrow[type=number] {
            -moz-appearance: textfield;
        }
    </style>

    <div class="container-fluid p-0">
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm border-0 rounded-4">
                    <div
                        class="card-header bg-primary text-white rounded-top-4 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-bold"><i class="fa fa-wallet me-2"></i> Mutasi Kas & Bank</h5>
                        <button type="button" class="btn btn-light btn-sm text-primary fw-semibold" data-bs-toggle="modal"
                            data-bs-target="#modalKasBank">
                            <i class="fa fa-plus-circle me-1"></i> Tambah Mutasi
                        </button>
                    </div>
                    <div class="card-body mt-3">
                        <form method="GET" action="{{ route('viewKasBank') }}" class="mb-4">
                            <div class="row g-2">
                                <div class="col-md-3">
                                    <input type="date" name="tanggal_dari" class="form-control form-control-sm"
                                        value="{{ old('tanggal_dari', request('tanggal_dari') ?: date('Y-m-01')) }}"
                                        required>
                                </div>
                                <div class="col-md-3">
                                    <input type="date" name="tanggal_sampai" class="form-control form-control-sm"
                                        value="{{ old('tanggal_sampai', request('tanggal_sampai') ?: date('Y-m-d')) }}"
                                        required>
                                </div>
                                <div class="col-md-4">
                                    <select name="kode_bank" class="form-select2 form-select-sm" required>
                                        <option value="">Pilih Bank/Kas</option>
                                        @foreach ($bank as $b)
                                            <option value="{{ $b->id }}"
                                                {{ request('kode_bank') == $b->id ? 'selected' : '' }}>
                                                {{ $b->nama_bank }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2 d-grid">
                                    <button type="submit" class="btn btn-primary btn-sm w-100">
                                        <i class="fa fa-filter me-1"></i> Filter
                                    </button>
                                </div>
                            </div>
                        </form>

                        <div class="table-responsive">
                            @if (request()->filled('tanggal_dari') && request()->filled('tanggal_sampai'))
                                <table class="table table-bordered table-sm text-center align-middle">
                                    <thead class="table-primary">
                                        <tr>
                                            <th style="width: 120px">Tanggal</th>
                                            <th style="width: 35%">Keterangan</th>
                                            <th>Bank/Kas</th>
                                            <th style="width: 120px">Debet</th>
                                            <th style="width: 120px">Kredit</th>
                                            <th style="width: 120px">Saldo</th>
                                            <th style="width: 80px">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {{-- SALDO AWAL --}}
                                        <tr class="table-light fw-semibold">
                                            <td colspan="5" class="text-start text-primary">
                                                Saldo Awal
                                            </td>
                                            <td class="text-end text-primary fw-bold" colspan="1">
                                                {{ rupiah($saldoAwal) }}</td>
                                            <td></td>
                                        </tr>

                                        @php
                                            $totalDebet = 0;
                                            $totalKredit = 0;
                                            $saldoBerjalan = $saldoAwal;
                                        @endphp

                                        {{-- DATA MUTASI --}}
                                        @forelse ($mutasi as $row)
                                            @php
                                                if ($row->tipe == 'debet') {
                                                    $totalDebet += $row->jumlah;
                                                    $saldoBerjalan += $row->jumlah;
                                                } else {
                                                    $totalKredit += $row->jumlah;
                                                    $saldoBerjalan -= $row->jumlah;
                                                }
                                            @endphp
                                            <tr>
                                                <td>{{ tanggal_indo($row->tanggal) }}</td>
                                                <td class="text-start">{{ $row->keterangan }}</td>
                                                <td>{{ $row->nama_bank }}</td>
                                                <td class="text-end text-success">
                                                    {{ $row->tipe == 'debet' ? rupiah($row->jumlah) : '-' }}
                                                </td>
                                                <td class="text-end text-danger">
                                                    {{ $row->tipe == 'kredit' ? rupiah($row->jumlah) : '-' }}
                                                </td>
                                                <td
                                                    class="text-end fw-semibold {{ $saldoBerjalan < 0 ? 'text-danger' : 'text-dark' }}">
                                                    {{ rupiah($saldoBerjalan) }}
                                                </td>
                                                <td>
                                                    <button type="button" class="btn btn-sm btn-warning btn-edit-mutasi"
                                                        data-id="{{ $row->id }}" data-tanggal="{{ $row->tanggal }}"
                                                        data-bank="{{ $row->kode_bank }}"
                                                        data-jumlah="{{ $row->jumlah }}" data-tipe="{{ $row->tipe }}"
                                                        data-keterangan="{{ $row->keterangan }}">
                                                        <i class="fa fa-edit"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-danger btn-hapus"
                                                        data-id="{{ $row->id }}">
                                                        <i class="fa fa-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7" class="text-center text-muted">Tidak ada data mutasi
                                                    ditemukan.</td>
                                            </tr>
                                        @endforelse

                                        {{-- SALDO AKHIR --}}
                                        <tr class="table-primary fw-bold">
                                            <td colspan="3" class="text-center text-dark">Saldo Akhir</td>
                                            <td class="text-end text-success">{{ rupiah($totalDebet) }}</td>
                                            <td class="text-end text-danger">{{ rupiah($totalKredit) }}</td>
                                            <td class="text-end text-dark" colspan="1">{{ rupiah($saldoBerjalan) }}</td>
                                            <td></td>
                                        </tr>
                                    </tbody>
                                </table>

                                {{-- PAGINATION --}}
                                <div class="d-flex justify-content-center mt-3">
                                    {{ $mutasi->links('pagination::bootstrap-5') }}
                                </div>
                            @else
                                <div class="alert alert-info text-center mt-3">
                                    <i class="fa fa-info-circle me-1"></i>
                                    Silakan filter tanggal terlebih dahulu untuk menampilkan data mutasi kas/bank.
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalKasBank" tabindex="-1" aria-labelledby="modalKasBankLabel" aria-hidden="true">
        <div class="modal-dialog modal-md modal-dialog-centered">
            <form method="POST" action="{{ route('storeKasBank') }}" class="needs-validation" novalidate id="formKasBank">
                @csrf
                <input type="hidden" name="tanggal_dari" value="{{ request('tanggal_dari') }}">
                <input type="hidden" name="tanggal_sampai" value="{{ request('tanggal_sampai') }}">
                <input type="hidden" name="kode_bank" value="{{ request('kode_bank') }}">
                <div class="modal-content shadow rounded-4 border-0">
                    <div class="modal-header bg-gradient bg-primary text-white rounded-top-4">
                        <h5 class="modal-title fw-semibold" id="modalKasBankLabel">
                            <i class="fa fa-wallet me-2"></i> Tambah Mutasi Kas/Bank
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body px-4 py-3">
                        <input type="hidden" name="id_mutasi" id="id_mutasi">
                        <div class="mb-3">
                            <label class="form-label">Tanggal</label>
                            <input type="date" name="tanggal" class="form-control form-control-sm" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Bank/Kas</label>
                            <select name="kode_bank" class="form-select form-select-sm select2" required>
                                <option value="">Pilih Bank/Kas</option>
                                @foreach ($bank as $b)
                                    <option value="{{ $b->id }}">{{ $b->nama_bank }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Tipe Transaksi</label>
                            <select name="tipe" class="form-select form-select-sm select2" required>
                                <option value="">Pilih Tipe</option>
                                <option value="debet">Debet (Uang Masuk)</option>
                                <option value="kredit">Kredit (Uang Keluar)</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Jumlah</label>
                            <input type="text" name="jumlah" id="jumlah"
                                class="form-control form-control-sm text-end rupiah" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Keterangan</label>
                            <textarea name="keterangan" rows="2" class="form-control form-control-sm" placeholder="Tulis keterangan"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer px-4 py-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-save me-1"></i> Simpan
                        </button>
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                            <i class="fa fa-times me-1"></i> Batal
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <script>
        $(document).ready(function() {
            $('.select2').select2({
                dropdownParent: $('#modalKasBank'),
                width: '100%',
            });

            function formatRupiah(angka) {
                let number = Math.abs(parseInt(angka) || 0);
                return 'Rp' + number.toLocaleString('id-ID');
            }

            function parseRupiah(rp) {
                return parseInt((rp || '0').toString().replace(/[^\d]/g, '')) || 0;
            }

            $('.rupiah').on('input', function() {
                let val = $(this).val();
                $(this).val(formatRupiah(parseRupiah(val)));
            });

            $(document).on('click', '.btn-hapus', function() {
                const id = $(this).data('id');

                // Ambil query string dari URL sekarang (misalnya ?tanggal_dari=...&tanggal_sampai=...&kode_bank=...)
                const queryString = window.location.search;

                Swal.fire({
                    title: 'Yakin ingin menghapus?',
                    text: "Data yang dihapus tidak bisa dikembalikan.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = `/deleteKasBank/${id}${queryString}`;
                    }
                });
            });

            $('.btn-edit-mutasi').on('click', function() {
                const data = $(this).data();

                // Ubah label dan form action
                $('#modalKasBankLabel').html('<i class="fa fa-edit me-2"></i> Edit Transaksi Kas/Bank');
                $('#formKasBank').attr('action', '{{ route('updateKasBank') }}');

                // Set nilai field satu per satu
                $('#id_mutasi').val(data.id);
                $('#formKasBank input[name="tanggal"]').val(data.tanggal);

                $('#formKasBank select[name="kode_bank"]').val(data.bank).trigger('change.select2');
                $('#formKasBank select[name="tipe"]').val(data.tipe).trigger('change.select2');

                $('#formKasBank input[name="jumlah"]').val(formatRupiah(data.jumlah));
                $('#formKasBank textarea[name="keterangan"]').val(data.keterangan);

                // Tampilkan modal
                $('#modalKasBank').modal('show');
            });

            $('#modalKasBank').on('hidden.bs.modal', function() {
                $('#formKasBank')[0].reset();
                $('#formKasBank').attr('action', '{{ route('storeKasBank') }}');
                $('#modalKasBankLabel').html(
                    '<i class="fa fa-cash-register me-2"></i> Tambah Mutasi Kas/Bank');
                $('#kode_bank').val('').trigger('change');
                $('#id_mutasi').val('');
            });

            $('form').on('submit', function() {
                let jumlah = parseRupiah($('#jumlah').val());
                $('#jumlah').val(jumlah);
            });
        });
    </script>
@endsection
