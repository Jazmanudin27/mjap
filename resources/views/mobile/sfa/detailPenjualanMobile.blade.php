@extends('mobile.layout')
@section('title', 'Detail Penjualan')
@section('header', 'Detail Penjualan')

@section('content')
    <div class="container py-3">

        @php
            $totalDiskon = $detail->sum('total_diskon'); // perbaikan di sini
            $grandTotal = $detail->sum('total'); // subtotal = harga kotor, total = harga bersih setelah diskon
            $karyawan = DB::table('hrd_karyawan')->where('nik', $penjualan->id_user)->first();
            $jmlhBayar = DB::table('penjualan_pembayaran')->where('no_faktur', $penjualan->no_faktur)->sum('jumlah');
            $jmlhBayarTf = DB::table('penjualan_pembayaran_transfer')
                ->where('status', 'disetujui')
                ->where('no_faktur', $penjualan->no_faktur)
                ->sum('jumlah');
            $jmlhBayarGiro = DB::table('penjualan_pembayaran_giro')
                ->where('status', 'disetujui')
                ->where('no_faktur', $penjualan->no_faktur)
                ->sum('jumlah');
            $sisaBayar = $grandTotal - ($jmlhBayar + $jmlhBayarTf + $jmlhBayarGiro);
            $classSisa = $sisaBayar > 0 ? 'table-danger' : 'table-success';
        @endphp
        {{-- Info Faktur --}}
        <div class="card shadow-sm border-0 rounded-4 mb-3">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="fw-bold text-primary fs-6">
                            <i class="bi bi-receipt me-1"></i> {{ $penjualan->no_faktur }}
                        </div>
                        <div class="text-muted small">
                            <i class="bi bi-calendar-event me-1"></i>
                            {{ \Carbon\Carbon::parse($penjualan->tanggal)->format('d M Y') }}
                        </div>
                    </div>
                    <div class="text-end">
                        <span class="badge {{ $penjualan->jenis_transaksi == 'T' ? 'bg-success' : 'bg-warning text-dark' }}">
                            {{ $penjualan->jenis_transaksi == 'T' ? 'Tunai' : 'Kredit' }}
                        </span>
                        @if ($penjualan->jenis_bayar)
                            <span class="badge bg-info text-dark d-block mt-1">
                                {{ ucfirst($penjualan->jenis_bayar) }}
                            </span>
                        @endif
                    </div>
                </div>
                <hr class="my-2">
                <div class="small text-muted">
                    <div class="mb-1"><i class="bi bi-person-circle me-1"></i> {{ $penjualan->nama_pelanggan }}</div>
                    <div class="mb-1"><i class="bi bi-geo-alt me-1"></i> {{ $penjualan->alamat_toko }}</div>
                    <div><i class="bi bi-telephone me-1"></i> {{ $penjualan->no_hp_pelanggan }}</div>
                    @if ($penjualan->nama_wilayah)
                        <div class="mb-1"><i class="bi bi-map me-1"></i> {{ $penjualan->nama_wilayah }}</div>
                    @endif
                </div>
            </div>
        </div>
        <div class="card border-0 shadow-sm rounded-4 mb-3">
            <div class="card-body">
                <h6 class="fw-bold mb-3"><i class="bi bi-cart-check me-1"></i> Barang Terjual</h6>
                <div class="table-responsive">
                    <table class="table table-sm align-middle mb-0" style="font-size: 13px;">
                        <tbody>
                            @foreach ($detail as $index => $item)
                                @php
                                    $qty = $item->qty;
                                    $harga = $item->harga;
                                    $diskon1 = $item->diskon1_persen ?? 0;
                                    $diskon2 = $item->diskon2_persen ?? 0;
                                    $diskon3 = $item->diskon3_persen ?? 0;
                                    $diskon4 = $item->diskon4_persen ?? 0;

                                    $hargaSetelahDiskon = $harga;
                                    $hargaSetelahDiskon *= 1 - $diskon1 / 100;
                                    $hargaSetelahDiskon *= 1 - $diskon2 / 100;
                                    $hargaSetelahDiskon *= 1 - $diskon3 / 100;
                                    $hargaSetelahDiskon *= 1 - $diskon4 / 100;

                                    $subtotal = $hargaSetelahDiskon * $qty;
                                    $subtotalNormal = $harga * $qty;
                                    $potongan = $item->total_diskon;
                                    $rowClass = $item->is_promo ? 'table-warning' : '';
                                @endphp

                                {{-- Nama Barang --}}
                                <tr class="table-light {{ $rowClass }}">
                                    <td colspan="4" class="fw-semibold text-primary">
                                        {{ $item->nama_barang }}
                                    </td>
                                </tr>
                                <tr class="{{ $rowClass }}">
                                    <td class="small">{{ $item->qty }} {{ $item->satuan }}</td>
                                    <td class="small">@ Rp{{ number_format($item->harga, 0, ',', '.') }}</td>
                                    <td class="text-end small" colspan="2">
                                        Rp{{ number_format($subtotal, 0, ',', '.') }}</td>
                                </tr>
                                <tr class="text-muted small {{ $rowClass }}">
                                    <td>D1: {{ $diskon1 }}%</td>
                                    <td>D2: {{ $diskon2 }}%</td>
                                    <td>D3: {{ $diskon3 }}%</td>
                                </tr>
                                <tr class="{{ $rowClass }}">
                                    <td colspan="4" class="text-end text-success small">
                                        Potongan: -Rp{{ number_format($potongan, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">
                    <table class="table table-borderless table-sm" style="font-size: 14px;">
                        <tr>
                            <td class="text-end fw-semibold">Total Penjualan</td>
                            <td class="text-end" style="width: 40%;">Rp{{ number_format($penjualan->total, 0, ',', '.') }}
                            </td>
                        </tr>
                        <tr>
                            <td class="text-end fw-semibold">Jumlah Pembayaran</td>
                            <td class="text-end text-success">
                                Rp{{ number_format($jmlhBayar + $jmlhBayarTf + $jmlhBayarGiro, 0, ',', '.') }}
                            </td>
                        </tr>
                        <tr>
                            <td class="text-end fw-semibold">Sisa Bayar</td>
                            <td class="text-end text-danger">
                                Rp{{ number_format($sisaBayar, 0, ',', '.') }}
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        @if ($sisaBayar != 0)
            <div class="text-end mb-2">
                <button class="btn btn-sm btn-primary rounded-pill" id="btnTambahPembayaran">
                    <i class="bi bi-plus-circle"></i> Tambah Pembayaran
                </button>
            </div>
        @endif

        @if ($pembayaran->count() || $transfer->count() || $giro->count())
            <div class="card border-0 shadow-sm rounded-4 mb-3">
                <div class="card-body">
                    @if ($pembayaran->count())
                        <h6 class="fw-semibold mb-2"><i class="bi bi-cash me-1"></i>Riwayat Pembayaran</h6>
                        <div class="table-responsive mb-4">
                            <table class="table table-bordered table-sm table-hover text-nowrap" style="font-size: 12px">
                                <thead class="table-light text-center">
                                    <tr>
                                        <th>No</th>
                                        <th>Tanggal</th>
                                        <th>Penagih</th>
                                        <th>Jenis</th>
                                        <th>Jumlah</th>
                                        <th>Keterangan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($pembayaran as $no => $pb)
                                        <tr>
                                            <td class="text-center">{{ $no + 1 }}</td>
                                            <td>{{ tanggal_indo2($pb->tanggal) }}</td>
                                            <td class="text-center">{{ $pb->nama_sales }}</td>
                                            <td class="text-center">
                                                <span
                                                    class="badge {{ $pb->jenis_bayar == 'tunai' ? 'bg-success' : 'bg-warning text-dark' }}">
                                                    {{ ucfirst($pb->jenis_bayar) }}
                                                </span>
                                            </td>
                                            <td class="text-end fw-bold">{{ rupiah($pb->jumlah) }}</td>
                                            <td>{{ $pb->keterangan ?: '-' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif

                    {{-- Pembayaran Transfer --}}
                    @if ($transfer->count())
                        <h6 class="fw-semibold mb-2"><i class="bi bi-bank"></i> Riwayat Pembayaran Transfer</h6>
                        <div class="table-responsive mb-4">
                            <table class="table table-bordered table-sm table-hover text-nowrap" style="font-size: 12px">
                                <thead class="table-light text-center">
                                    <tr>
                                        <th>No</th>
                                        <th>Tanggal</th>
                                        <th>Penagih</th>
                                        <th>Status</th>
                                        <th>Jumlah</th>
                                        <th>Bank</th>
                                        <th>Keterangan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($transfer as $no => $tr)
                                        <tr>
                                            <td class="text-center">{{ $no + 1 }}</td>
                                            <td>{{ tanggal_indo2($tr->tanggal) }}</td>
                                            <td class="text-center">{{ $tr->nama_sales }}</td>
                                            <td class="text-center">
                                                <span
                                                    class="badge fw-semibold {{ $tr->status == 'disetujui' ? 'bg-success' : ($tr->status == 'ditolak' ? 'bg-danger' : 'bg-warning text-dark') }}">
                                                    {{ ucfirst($tr->status) }}
                                                </span>
                                            </td>
                                            <td class="text-end fw-bold">{{ rupiah($tr->jumlah) }}</td>
                                            <td class="text-center">{{ $tr->bank_pengirim }}</td>
                                            <td>{{ $tr->keterangan ?: '-' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif

                    @if ($giro->count())
                        <h6 class="fw-semibold mb-2"><i class="bi bi-journal-text"></i> Riwayat Pembayaran Giro</h6>
                        <div class="table-responsive mb-4">
                            <table class="table table-bordered table-sm table-hover text-nowrap" style="font-size: 12px">
                                <thead class="table-light text-center">
                                    <tr>
                                        <th>No</th>
                                        <th>Tanggal</th>
                                        <th>Penagih</th>
                                        <th>Status</th>
                                        <th>Jumlah</th>
                                        <th>No Giro</th>
                                        <th>Bank</th>
                                        <th>Jatuh Tempo</th>
                                        <th>Keterangan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($giro as $no => $gr)
                                        <tr>
                                            <td class="text-center">{{ $no + 1 }}</td>
                                            <td>{{ tanggal_indo2($gr->tanggal) }}</td>
                                            <td class="text-center">{{ $gr->nama_sales }}</td>
                                            <td class="text-center">
                                                <span
                                                    class="badge fw-semibold {{ $gr->status == 'disetujui' ? 'bg-success' : ($gr->status == 'ditolak' ? 'bg-danger' : 'bg-warning text-dark') }}">
                                                    {{ ucfirst($gr->status) }}
                                                </span>
                                            </td>
                                            <td class="text-end fw-bold">{{ rupiah($gr->jumlah) }}</td>
                                            <td class="text-center">{{ $gr->no_giro ?: '-' }}</td>
                                            <td class="text-center">{{ $gr->bank_pengirim }}</td>
                                            <td class="text-center">{{ tanggal_indo2($gr->jatuh_tempo) }}</td>
                                            <td>{{ $gr->keterangan ?: '-' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        @endif

    </div>

    <div class="modal fade" id="modalPembayaran" aria-labelledby="modalPembayaranLabel" aria-hidden="true"
        data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog ">
            <form id="formPembayaran" method="POST" class="needs-validation" novalidate autocomplete="off">
                @csrf
                <div class="modal-content shadow">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title fw-semibold" id="modalPembayaranLabel">
                            <i class="bi bi-cash-coin me-2"></i>Tambah Pembayaran
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Tutup"></button>
                    </div>

                    <div class="modal-body">
                        <input type="hidden" name="id" id="pay_id">
                        <input type="hidden" name="no_faktur" value="{{ $penjualan->no_faktur }}" id="pay_faktur">
                        <input type="hidden" name="tanggal" id="pay_tanggal" value="{{ date('Y-m-d') }}">
                        <input type="hidden" name="kode_sales" id="pay_sales" value="{{ $penjualan->kode_sales }}">
                        <input type="hidden" id="pay_pelanggan" name="kode_pelanggan"
                            value="{{ $penjualan->kode_pelanggan }}">
                        <div class="mb-3">
                            <label for="pay_metode" class="form-label fw-semibold">
                                <i class="bi bi-wallet2 me-1"></i> Jenis Bayar
                            </label>
                            <select class="form-select" id="pay_metode" name="jenis_bayar" required>
                                <option value="">Pilih</option>
                                <option value="tunai">Tunai</option>
                                <option value="transfer">Transfer</option>
                                <option value="giro">Giro</option>
                                <option value="voucher">Voucher</option>
                            </select>
                        </div>
                        <div class="mb-3" id="bank_pengirim_wrapper">
                            <label class="form-label fw-semibold">
                                <i class="bi bi-bank2 me-1"></i> Bank Pengirim
                            </label>
                            <input type="text" name="bank_pengirim" class="form-control form-control-sm"
                                placeholder="Contoh: BCA, BRI, Mandiri">
                        </div>
                        <div class="mb-3 d-none" id="no_giro_wrapper">
                            <label class="form-label fw-semibold">
                                <i class="bi bi-receipt me-1"></i> No Giro
                            </label>
                            <input type="text" name="no_giro" class="form-control form-control-sm"
                                placeholder="Contoh: 00123/BCA/Giro">
                        </div>

                        <div class="mb-3 d-none" id="jatuh_tempo_wrapper">
                            <label class="form-label fw-semibold">
                                <i class="bi bi-calendar-event me-1"></i> Tanggal Jatuh Tempo
                            </label>
                            <input type="date" name="jatuh_tempo" class="form-control form-control-sm">
                        </div>
                        <div class="mb-3">
                            <label for="pay_jumlah" class="form-label fw-semibold">
                                <i class="bi bi-currency-dollar me-1"></i> Jumlah Pembayaran
                            </label>
                            <input type="text" class="form-control form-control-sm text-end" id="pay_jumlah"
                                name="jumlah" inputmode="numeric" placeholder="Masukkan jumlah" required
                                data-sisa-bayar="{{ $sisaBayar }}">
                        </div>

                        <div class="mb-3">
                            <label for="pay_keterangan" class="form-label fw-semibold">
                                <i class="bi bi-pencil me-1"></i> Keterangan
                            </label>
                            <textarea class="form-control form-control-sm" id="pay_keterangan" name="keterangan" rows="2"
                                placeholder="Contoh: Pembayaran pertama, pelunasan, dsb."></textarea>
                        </div>
                    </div>

                    <div class="modal-footer bg-light">
                        <button type="submit" class="btn btn-sm btn-primary btnSimpan">
                            <i class="bi bi-check-circle me-1"></i> Simpan
                        </button>
                        <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">
                            <i class="bi bi-x-circle me-1"></i> Batal
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        $(document).ready(function() {

            function toggleInputByJenis() {
                const jenis = $('#pay_metode').val();

                // Reset semua dulu
                $('#bank_pengirim_wrapper').addClass('d-none');
                $('#no_giro_wrapper').addClass('d-none');
                $('#jatuh_tempo_wrapper').addClass('d-none');

                $('#bank_pengirim_wrapper input').prop('required', false);
                $('#no_giro_wrapper input').prop('required', false);
                $('#jatuh_tempo_wrapper input').prop('required', false);

                if (jenis === 'transfer') {
                    $('#bank_pengirim_wrapper').removeClass('d-none');
                    $('#bank_pengirim_wrapper input').prop('required', true);
                }

                if (jenis === 'giro') {
                    $('#no_giro_wrapper').removeClass('d-none');
                    $('#jatuh_tempo_wrapper').removeClass('d-none');
                    $('#no_giro_wrapper input').prop('required', true);
                    $('#jatuh_tempo_wrapper input').prop('required', true);
                }
            }
            // Panggil saat awal buka modal
            $('#modalPembayaran').on('shown.bs.modal', function() {
                toggleInputByJenis();
            });

            // Saat dropdown jenis bayar diubah
            $('#pay_metode').on('change', function() {
                toggleInputByJenis();
            });

            $('.select2-sales').select2({
                dropdownParent: $('#modalPembayaran'),
                width: '100%',
            });

            function formatRupiah(angka) {
                let number = parseInt(angka) || 0;
                return 'Rp' + number.toLocaleString('id-ID');
            }

            function parseRupiah(rp) {
                return parseInt(rp.replace(/[^\d]/g, '')) || 0;
            }

            $('#pay_jumlah').on('input', function() {
                let clear = parseRupiah($(this).val());
                $(this).val(formatRupiah(clear));
            });

            $('#btnTambahPembayaran').on('click', function() {
                resetForm();
                $('#modalPembayaranLabel').text('Tambah Pembayaran');
                $('.btnSimpan').text('Simpan');
                $('#modalPembayaran').modal('show');
            });

            const ROUTE_STORE = "{{ route('storePembayaranPenjualan') }}";
            const ROUTE_UPDATE = "{{ route('updatePembayaranPenjualan', ['id' => ':id']) }}";

            $('#formPembayaran').on('submit', function(e) {
                e.preventDefault();

                const jumlahBayar = parseRupiah($('#pay_jumlah').val());
                const sisaBayar = parseInt($('#pay_jumlah').data('sisa-bayar')) || 0;

                if (jumlahBayar > sisaBayar) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Pembayaran Melebihi Sisa Bayar!',
                        text: `Jumlah pembayaran tidak boleh melebihi ${formatRupiah(sisaBayar)}.`,
                    });
                    return; // Hentikan submit
                }

                const id = $('#pay_id').val();
                const url = id ?
                    ROUTE_UPDATE.replace(':id', id) :
                    ROUTE_STORE;

                $.ajax({
                    type: 'POST',
                    url: url,
                    data: {
                        _token: "{{ csrf_token() }}",
                        id: $('#pay_id').val(),
                        tanggal: $('#pay_tanggal').val(),
                        jenis_bayar: $('#pay_metode').val(),
                        jumlah: jumlahBayar,
                        no_faktur: $('#pay_faktur').val(),
                        keterangan: $('#pay_keterangan').val(),
                        kode_pelanggan: $('#pay_pelanggan').val(),
                        kode_sales: $('#pay_sales').val(),
                        bank_pengirim: $('input[name="bank_pengirim"]').val(),
                        no_giro: $('input[name="no_giro"]').val(),
                        jatuh_tempo: $('input[name="jatuh_tempo"]').val(),
                    },
                    success: function() {
                        $('#modalPembayaran').modal('hide');
                        Swal.fire('Berhasil', 'Pembayaran disimpan.', 'success')
                            .then(() => location.reload());
                    },
                    error: function(xhr) {
                        const msg = xhr.responseJSON?.message || 'Terjadi kesalahan.';
                        Swal.fire('Gagal', msg, 'error');
                    }
                });
            });

            function resetForm() {
                $('#formPembayaran')[0].reset();
                $('#pay_id').val('');
                $('#formPembayaran input[name="_method"]').remove();
                toggleInputByJenis();
            }
        });
    </script>
@endsection
