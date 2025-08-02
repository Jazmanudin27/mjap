@extends('layouts.template')
@section('titlepage', 'Data Setoran Penjualan')
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
                        <h5 class="mb-0 fw-bold"><i class="fa fa-cash-register me-2"></i> Data Setoran Penjualan</h5>
                        @if (!empty($TambahSetoranPenjualan))
                            <button type="button" class="btn btn-light btn-sm text-primary fw-semibold" data-bs-toggle="modal"
                                data-bs-target="#modalSetoran">
                                <i class="fa fa-plus-circle me-1"></i> Tambah Setoran
                            </button>
                        @endif
                    </div>

                    <div class="card-body mt-3">
                        <form method="GET" action="{{ route('viewSetoranPenjualan') }}" class="mb-4">
                            <div class="row g-2">
                                <div class="col-md-3">
                                    <input type="date" name="tanggal_dari" class="form-control form-control-sm"
                                        value="{{ request('tanggal_dari') }}">
                                </div>
                                <div class="col-md-3">
                                    <input type="date" name="tanggal_sampai" class="form-control form-control-sm"
                                        value="{{ request('tanggal_sampai') }}">
                                </div>
                                <div class="col-md-4">
                                    <select name="kode_sales" class="form-select2 form-select-sm select2">
                                        <option value="">Pilih Sales</option>
                                        @foreach ($sales as $s)
                                            <option value="{{ $s->nik }}" {{ request('kode_sales') == $s->nik ? 'selected' : '' }}>
                                                {{ $s->nama_lengkap }} ({{ $s->nik }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2 d-flex align-items-end">
                                    <button type="submit" class="btn btn-primary btn-sm h-100 w-100">
                                        <i class="fa fa-filter me-1"></i> Filter
                                    </button>
                                </div>
                            </div>
                        </form>

                        <div class="table-responsive">
                            <table class="table table-bordered table-sm align-middle text-center">
                                <thead class="table-primary align-middle">
                                    <tr>
                                        <th rowspan="2">Tanggal</th>
                                        <th rowspan="2">Salesman</th>
                                        <th colspan="2">Penjualan</th>
                                        <th rowspan="2">Total LPH</th>
                                        <th colspan="5">Setoran</th>
                                        <th rowspan="2">Total Setoran</th>
                                        <th rowspan="2">Aksi</th>
                                    </tr>
                                    <tr>
                                        <th>Tunai</th>
                                        <th>Tagihan</th>
                                        <th>Kertas</th>
                                        <th>Logam</th>
                                        <th>Giro</th>
                                        <th>Transfer</th>
                                        <th>Lainnya</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $totalLhpTunai = $totalLhpTagihan = $totalKertas = $totalLogam = $totalGiro = $totalTransfer = $totalLainnya = $totalSetoran = 0;
                                    @endphp
                                    @forelse ($setoran as $row)
                                        @php
                                            $setoranTotal = $row->setoran_kertas + $row->setoran_logam + $row->setoran_giro + $row->setoran_transfer + $row->setoran_lainnya;
                                            $totalLph = $row->lhp_tunai + $row->lhp_tagihan;
                                            $isMismatch = $totalLph !== $setoranTotal;
                                            $totalLhpTunai += $row->lhp_tunai;
                                            $totalLhpTagihan += $row->lhp_tagihan;
                                            $totalKertas += $row->setoran_kertas;
                                            $totalLogam += $row->setoran_logam;
                                            $totalGiro += $row->setoran_giro;
                                            $totalTransfer += $row->setoran_transfer;
                                            $totalLainnya += $row->setoran_lainnya;
                                            $totalSetoran += $setoranTotal;
                                        @endphp
                                        <tr class="{{ $isMismatch ? 'table-danger' : '' }}">
                                            <td>{{ $row->tanggal }}</td>
                                            <td class="text-start">{{ $row->nama_sales ?? $row->kode_sales }}</td>
                                            <td class="text-end">{{ rupiah($row->lhp_tunai) }}</td>
                                            <td class="text-end">{{ rupiah($row->lhp_tagihan) }}</td>
                                            <td class="text-end fw-bold text-primary">
                                                {{ rupiah($row->lhp_tunai + $row->lhp_tagihan) }}</td>
                                            <td class="text-end">{{ rupiah($row->setoran_kertas) }}</td>
                                            <td class="text-end">{{ rupiah($row->setoran_logam) }}</td>
                                            <td class="text-end">{{ rupiah($row->setoran_giro) }}</td>
                                            <td class="text-end">{{ rupiah($row->setoran_transfer) }}</td>
                                            <td class="text-end">{{ rupiah($row->setoran_lainnya) }}</td>
                                            <td class="text-end fw-bold text-success">{{ rupiah($setoranTotal) }}</td>
                                            <td class="text-nowrap">
                                                <button type="button" class="btn btn-sm btn-warning btn-edit-setoran"
                                                    data-id="{{ $row->kode_setoran }}" data-tanggal="{{ $row->tanggal }}"
                                                    data-sales="{{ $row->kode_sales }}"
                                                    data-kertas="{{ rupiah($row->setoran_kertas) }}"
                                                    data-logam="{{ rupiah($row->setoran_logam) }}"
                                                    data-transfer="{{ rupiah($row->setoran_transfer) }}"
                                                    data-giro="{{ rupiah($row->setoran_giro) }}"
                                                    data-lainnya="{{ rupiah($row->setoran_lainnya) }}"
                                                    data-tunai="{{ rupiah($row->lhp_tunai) }}"
                                                    data-tagihan="{{ rupiah($row->lhp_tagihan) }}"
                                                    data-total="{{ rupiah($row->lhp_tunai + $row->lhp_tagihan) }}"
                                                    data-total_setoran="{{ rupiah($setoranTotal) }}"
                                                    data-selisih="{{ rupiah(($row->lhp_tunai + $row->lhp_tagihan) - ($row->setoran_kertas + $row->setoran_logam + $row->setoran_lainnya)) }}"
                                                    data-keterangan="{{ $row->keterangan ?? '' }}">
                                                    <i class="fa fa-edit"></i>
                                                </button>
                                                <form action="{{ route('deleteSetoranPenjualan', $row->kode_setoran) }}"
                                                    method="POST" class="d-inline"
                                                    onsubmit="return confirm('Yakin hapus data ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="btn btn-sm btn-danger"><i class="fa fa-trash"></i></button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="12" class="text-center text-muted">Tidak ada data setoran.</td>
                                        </tr>
                                    @endforelse
                                    @if ($setoran->count())
                                        <tr class="table-light fw-bold">
                                            <td colspan="2" class="text-center">Total</td>
                                            <td class="text-end">{{ rupiah($totalLhpTunai) }}</td>
                                            <td class="text-end">{{ rupiah($totalLhpTagihan) }}</td>
                                            <td class="text-end text-primary">{{ rupiah($totalLhpTunai + $totalLhpTagihan) }}
                                            </td>
                                            <td class="text-end">{{ rupiah($totalKertas) }}</td>
                                            <td class="text-end">{{ rupiah($totalLogam) }}</td>
                                            <td class="text-end">{{ rupiah($totalGiro) }}</td>
                                            <td class="text-end">{{ rupiah($totalTransfer) }}</td>
                                            <td class="text-end">{{ rupiah($totalLainnya) }}</td>
                                            <td class="text-end text-success">{{ rupiah($totalSetoran) }}</td>
                                            <td></td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                        <div class="d-flex justify-content-center mt-3">
                            {{ $setoran->links('pagination::bootstrap-5') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modalSetoran" tabindex="-1" aria-labelledby="modalSetoranLabel" aria-hidden="true">
        <div class="modal-dialog modal-md modal-dialog-centered">
            <form id="formSetoranPenjualan" method="POST" action="{{ route('storeSetoranPenjualan') }}" class="needs-validation" novalidate>
                @csrf
                <input type="hidden" name="id_setoran" id="id_setoran">
                <div class="modal-content shadow rounded-4 border-0">
                    <div class="modal-header bg-gradient bg-primary text-white rounded-top-4">
                        <h5 class="modal-title fw-semibold" id="modalSetoranLabel">
                            <i class="fa fa-cash-register me-2"></i> Tambah Setoran Penjualan
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body px-4 py-3">
                        <div class="row g-2">
                            <div class="border rounded p-2 bg-light mb-2">
                                <div class="mb-2 row">
                                    <label for="tanggal_setoran" class="col-4 col-form-label fw-semibold">Tanggal</label>
                                    <div class="col-8">
                                        <input type="date" name="tanggal_setoran" id="tanggal_setoran"
                                            class="form-control form-control-sm" required>
                                    </div>
                                </div>
                                <div class="mb-2 row">
                                    <label for="kode_sales_setoran" class="col-4 col-form-label fw-semibold">Sales</label>
                                    <div class="col-8">
                                        <select name="kode_sales_setoran" id="kode_sales_setoran"
                                            class="select2 form-select-sm" required>
                                            <option value="">-- Pilih Sales --</option>
                                            @foreach ($sales as $s)
                                                <option value="{{ $s->nik }}">{{ $s->nama_lengkap }} ({{ $s->nik }})</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="border rounded p-2 bg-light mb-2">
                                <h6 class="text-primary"><i class="fa fa-file-invoice-dollar me-1"></i> Data LHP</h6>
                                @foreach (['tunai' => 'Tunai', 'tagihan' => 'Tagihan', 'total_tagihan' => 'Total Tagihan'] as $id => $label)
                                    <div class="mb-2 row">
                                        <label for="{{ $id }}" class="col-4 col-form-label">{{ $label }}</label>
                                        <div class="col-8">
                                            <input type="text" name="{{ $id }}" id="{{ $id }}" placeholder="{{ $label }}"
                                                class="form-control form-control-sm text-end rupiah" readonly>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div class="border rounded p-2 bg-light mb-2">
                                <h6 class="text-success"><i class="fa fa-wallet me-1"></i> Rincian Setoran</h6>
                                <div class="mb-1 row">
                                    <label for="setoran_kertas" class="col-4 col-form-label">Kertas</label>
                                    <div class="col-8">
                                        <input type="text" name="setoran_kertas" id="setoran_kertas"
                                            class="form-control form-control-sm text-end rupiah fw-bold" required>
                                    </div>
                                </div>
                                <div class="mb-1 row">
                                    <label for="setoran_logam" class="col-4 col-form-label">Logam</label>
                                    <div class="col-8">
                                        <input type="text" name="setoran_logam" id="setoran_logam"
                                            class="form-control form-control-sm text-end rupiah fw-bold" required>
                                    </div>
                                </div>
                                <div class="mb-1 row">
                                    <label for="setoran_lainnya" class="col-4 col-form-label">Lainnya</label>
                                    <div class="col-8">
                                        <input type="number" name="setoran_lainnya" id="setoran_lainnya"
                                            class="form-control form-control-sm text-end rupiah-negative fw-bold no-arrow"
                                            placeholder="Lainnya">
                                    </div>
                                </div>
                            </div>

                            <div class="border rounded p-2 bg-light mb-1">
                                <h6 class="text-warning"><i class="fa fa-credit-card me-1"></i> Pembayaran Lain</h6>
                                <div class="mb-1 row">
                                    <label for="setoran_transfer" class="col-4 col-form-label">Transfer</label>
                                    <div class="col-8">
                                        <input type="text" name="setoran_transfer" id="setoran_transfer"
                                            class="form-control form-control-sm text-end rupiah fw-bold bg-white" readonly>
                                    </div>
                                </div>
                                <div class="mb-1 row">
                                    <label for="setoran_giro" class="col-4 col-form-label">Giro</label>
                                    <div class="col-8">
                                        <input type="text" name="setoran_giro" id="setoran_giro"
                                            class="form-control form-control-sm text-end rupiah fw-bold bg-white" readonly>
                                    </div>
                                </div>
                            </div>

                            <div class="border rounded p-2 bg-light mb-2">
                                <h6 class="text-info"><i class="fa fa-calculator me-1"></i> Perhitungan</h6>
                                <div class="mb-2 row">
                                    <label for="total_setoran" class="col-4 col-form-label">Total Setoran</label>
                                    <div class="col-8">
                                        <input type="text" id="total_setoran" name="total_setoran"
                                            class="form-control form-control-sm text-end rupiah fw-bold bg-white text-success"
                                            readonly>
                                    </div>
                                </div>
                                <div class="mb-2 row">
                                    <label for="selisih" class="col-4 col-form-label">Selisih</label>
                                    <div class="col-8">
                                        <input type="text" id="selisih" name="selisih"
                                            class="form-control form-control-sm text-end rupiah fw-bold bg-white text-danger"
                                            readonly>
                                    </div>
                                </div>
                            </div>

                            <div class="border rounded p-2 bg-light mb-2">
                                <div class="mb-2 row">
                                    <label class="col-4 col-form-label fw-semibold">Keterangan</label>
                                    <div class="col-8">
                                        <textarea name="keterangan" id="keterangan" class="form-control form-control-sm"
                                            rows="2" placeholder="Opsional..."></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer px-4 py-2">
                        <button type="button" class="btn btn-light btn-sm" data-bs-dismiss="modal">
                            <i class="fa fa-times me-1"></i> Batal
                        </button>
                        <button type="submit" class="btn btn-primary btn-sm">
                            <i class="fa fa-save me-1"></i> Simpan Setoran
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <script>
        $(document).ready(function () {
            $('.select2').select2({
                dropdownParent: $('#modalSetoran'),
                width: '100%',
            });
            function formatRupiah(angka) {
                let isNegative = parseInt(angka) < 0;
                let number = Math.abs(parseInt(angka) || 0);
                return (isNegative ? '-' : '') + 'Rp' + number.toLocaleString('id-ID');
            }

            function parseRupiah(rp) {
                let isNegative = rp.toString().includes('-');
                let number = parseInt(rp.replace(/[^\d]/g, '')) || 0;
                return isNegative ? -number : number;
            }

            function hitungTotal() {
                const tunai = parseRupiah($('#tunai').val());
                const tagihan = parseRupiah($('#tagihan').val());
                const total_tagihan = tunai + tagihan;
                $('#total_tagihan').val(formatRupiah(total_tagihan));

                const kertas = parseRupiah($('#setoran_kertas').val());
                const logam = parseRupiah($('#setoran_logam').val());
                const lainnya = parseRupiah($('#setoran_lainnya').val());
                const transfer = parseRupiah($('#setoran_transfer').val());
                const giro = parseRupiah($('#setoran_giro').val());

                const total_setoran = transfer + giro;
                $('#total_setoran').val(formatRupiah(total_setoran+kertas + logam + lainnya));

                const selisih = total_tagihan - (kertas + logam + lainnya + transfer + giro);
                $('#selisih').val(formatRupiah(selisih));
            }

            function getDataSetoran() {
                const tanggal = $('#tanggal_setoran').val();
                const kode_sales = $('#kode_sales_setoran').val();

                if (tanggal && kode_sales) {
                    $.ajax({
                        url: '{{ route('getSetoranPenjualan') }}',
                        type: 'GET',
                        data: { tanggal, kode_sales },
                        success: function (data) {
                            $('#tunai').val(formatRupiah(data.tunai || 0));
                            $('#tagihan').val(formatRupiah(data.tagihan || 0));
                            $('#setoran_transfer').val(formatRupiah(data.transfer || 0));
                            $('#setoran_giro').val(formatRupiah(data.giro || 0));
                            hitungTotal();
                        },
                        error: function () {
                            alert('Gagal mengambil data setoran!');
                        }
                    });
                }
            }

            $('.rupiah, .rupiah-negative').on('input', function () {
                let val = $(this).val();
                $(this).val(formatRupiah(parseRupiah(val)));
                hitungTotal();
            });

            $('#tanggal_setoran, #kode_sales_setoran').on('change', getDataSetoran);

            $('.btn-edit-setoran').on('click', function () {
                const data = $(this).data();
                $('#modalSetoranLabel').html('<i class="fa fa-edit me-2"></i> Edit Setoran Penjualan');
                $('#formSetoranPenjualan').attr('action', '{{ route("updateSetoranPenjualan") }}');

                $('#kode_sales_setoran').val(data.sales).trigger('change');
                $('#id_setoran').val(data.id);
                $('#tanggal_setoran').val(data.tanggal);
                $('#setoran_kertas').val(data.kertas);
                $('#setoran_logam').val(data.logam);
                $('#setoran_lainnya').val(data.lainnya);
                $('#setoran_transfer').val(data.transfer);
                $('#setoran_giro').val(data.giro);
                $('#tunai').val(data.tunai);
                $('#tagihan').val(data.tagihan);
                $('#total_tagihan').val(data.total);
                $('#keterangan').val(data.keterangan);
                $('#total_setoran').val(data.total_setoran);
                $('#selisih').val(data.selisih);

                $('#modalSetoran').modal('show');
            });


            $('#modalSetoran').on('hidden.bs.modal', function () {
                $('#formSetoranPenjualan')[0].reset();
                $('#formSetoranPenjualan').attr('action', '{{ route("storeSetoranPenjualan") }}');
                $('#modalSetoranLabel').html('<i class="fa fa-cash-register me-2"></i> Tambah Setoran Penjualan');
                $('#kode_sales_setoran').val('').trigger('change');
            });
        });
    </script>
@endsection
