@extends('layouts.template')
@section('titlepage', 'Detail Pembelian')
@section('contents')
    @php
        $totalDiskon = $detail->sum('diskon');
        $grandTotal = $detail->sum('subtotal');
        $jmlhBayar = DB::table('pembelian_pembayaran')->where('no_faktur', $pembelian->no_faktur)->sum('jumlah');
        $karyawan = DB::table('hrd_karyawan')->where('nik', $pembelian->id_user)->first();
        $sisaBayar = $grandTotal + $pembelian->pajak + $pembelian->biaya_lain - $jmlhBayar;
        $classSisa = $sisaBayar > 0 ? 'table-danger' : 'table-success';
        $percentPaid =
            $grandTotal + $pembelian->pajak + $pembelian->biaya_lain > 0
                ? min(100, ($jmlhBayar / ($grandTotal + $pembelian->pajak + $pembelian->biaya_lain)) * 100)
                : 0;
    @endphp
    <div class="container-fluid mt-3 p-0 animate__animated animate__fadeIn">
        <div class="row">
            <div class="col-12 col-xl-12 mx-auto">
                <div class="card border-0 shadow-lg">
                    <!-- Card Header -->
                    <div
                        class="card-header bg-primary shadow-sm bg-gradient text-white d-flex justify-content-between align-items-center">
                        <h4 class="mb-0"><i class="bi bi-receipt"></i> Detail Pembelian</h4>
                        <span class="badge bg-light text-primary fs-6">{{ $pembelian->no_faktur }}</span>
                    </div>

                    <!-- Card Body -->
                    <div class="card-body">
                        <!-- Info Blocks -->
                        <div class="row g-4 mt-1 mb-3">
                            <!-- Info Transaksi -->
                            <div class="col-md-6">
                                <div class="border rounded p-3 h-100">
                                    <h6 class="text-primary fw-bold mb-3"><i class="bi bi-info-circle"></i> Info Transaksi
                                    </h6>
                                    <table class="table table-borderless table-sm mb-0">
                                        <tr>
                                            <th style="width:40%">Tanggal</th>
                                            <td>{{ tanggal_indo2($pembelian->tanggal) }}</td>
                                        </tr>
                                        <tr>
                                            <th>Jatuh Tempo</th>
                                            <td>{{ tanggal_indo2($pembelian->jatuh_tempo) }}</td>
                                        </tr>
                                        <tr>
                                            <th>Jenis Transaksi</th>
                                            <td>
                                                <span
                                                    class="badge {{ $pembelian->jenis_transaksi == 'Tunai' ? 'bg-primary' : 'bg-warning text-dark' }}">
                                                    {{ $pembelian->jenis_transaksi }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Keterangan</th>
                                            <td>{{ $pembelian->keterangan ?: '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Waktu Input</th>
                                            <td>{{ $pembelian->created_at ?: '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Penginput</th>
                                            <td>{{ $karyawan->nama_lengkap ?: '-' }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>

                            <!-- Info Supplier -->
                            <div class="col-md-6">
                                <div class="border rounded p-3 h-100">
                                    <h6 class="text-primary fw-bold mb-3"><i class="bi bi-truck"></i> Info Supplier</h6>
                                    <table class="table table-borderless table-sm mb-0">
                                        <tr>
                                            <th style="width:40%">Kode Supplier</th>
                                            <td>{{ $supplier->kode_supplier }}</td>
                                        </tr>
                                        <tr>
                                            <th>Nama Supplier</th>
                                            <td>{{ $supplier->nama_supplier }}</td>
                                        </tr>
                                        <tr>
                                            <th>Alamat</th>
                                            <td>{{ $supplier->alamat }}</td>
                                        </tr>
                                        <tr>
                                            <th>No HP</th>
                                            <td>{{ $supplier->no_hp }}</td>
                                        </tr>
                                        <tr>
                                            <th>Tempo</th>
                                            <td>{{ $supplier->tempo }} Hari</td>
                                        </tr>
                                        <tr>
                                            <th>PPN</th>
                                            <td>{{ $supplier->ppn == 1 ? 'Include' : 'Exclude' }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Detail Barang -->
                        <h5 class="fw-semibold mb-3"><i class="bi bi-box-seam"></i> Detail Barang</h5>
                        <div class="table-responsive mb-4" style="max-height:500px; overflow-y:auto;">
                            <table class="table table-bordered table-sm table-hover text-nowrap">
                                <thead class="table-light text-center sticky-top bg-white shadow-sm">
                                    <tr>
                                        <th style="width:3%">No</th>
                                        <th style="width:5%">Kode</th>
                                        <th>Nama</th>
                                        <th style="width:6%">Jumlah</th>
                                        <th style="width:8%">Satuan</th>
                                        <th style="width:10%">Harga</th>
                                        <th style="width:10%">Potongan</th>
                                        <th style="width:12%">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $no = 1; @endphp
                                    @foreach ($detail as $d)
                                        <tr>
                                            <td class="text-center">{{ $no++ }}</td>
                                            <td>{{ $d->kode_barang }}</td>
                                            <td>{{ $d->nama_barang }}</td>
                                            <td class="text-end">{{ $d->qty }}</td>
                                            <td><span class="badge bg-secondary">{{ strtoupper($d->satuan) }}</span></td>
                                            <td class="text-end" title="{{ number_format($d->harga, 2, ',', '.') }}">
                                                {{ rupiah($d->harga) }}
                                            </td>
                                            <td class="text-end" title="{{ number_format($d->diskon, 2, ',', '.') }}">
                                                {{ rupiah($d->diskon) }}
                                            </td>
                                            <td class="text-end" title="{{ number_format($d->subtotal, 2, ',', '.') }}">
                                                {{ rupiah($d->subtotal) }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr class="fw-bold text-end table-primary">
                                        <td colspan="7" class="text-end"><i class="fas fa-receipt"></i> Subtotal</td>
                                        <td>{{ rupiah($grandTotal + $totalDiskon - $pembelian->potongan_claim) }}</td>
                                    </tr>
                                    <tr class="fw-bold text-end table-primary">
                                        <td colspan="7" class="text-end"><i class="fas fa-tags"></i> Potongan</td>
                                        <td>{{ rupiah($totalDiskon) }}</td>
                                    </tr>
                                    <tr class="fw-bold text-end table-warning">
                                        <td colspan="7" class="text-end"><i class="fas fa-hand-scissors"></i> Potongan
                                            Klaim</td>
                                        <td>{{ rupiah($pembelian->potongan_claim) }}</td>
                                    </tr>
                                    <tr class="fw-bold text-end table-primary">
                                        <td colspan="7" class="text-end"><i class="fas fa-calculator"></i> Total</td>
                                        <td>{{ rupiah($grandTotal) }}</td>
                                    </tr>
                                    <tr class="fw-bold text-end table-info">
                                        <td colspan="7" class="text-end"><i class="fas fa-percent"></i> PPN (Rp)</td>
                                        <td>{{ rupiah($pembelian->pajak) }}</td>
                                    </tr>
                                    <tr class="fw-bold text-end table-info">
                                        <td colspan="7" class="text-end"><i class="fas fa-truck-loading"></i> Biaya Lain
                                        </td>
                                        <td>{{ rupiah($pembelian->biaya_lain) }}</td>
                                    </tr>
                                    <tr class="fw-bold text-end bg-dark text-white fs-5">
                                        <td colspan="7" class="text-end"><i class="fas fa-coins"></i> Total Keseluruhan
                                        </td>
                                        <td>{{ rupiah($grandTotal + $pembelian->pajak + $pembelian->biaya_lain - $pembelian->potongan_claim) }}
                                        </td>
                                    </tr>
                                    <tr class="fw-bold text-end table-success">
                                        <td colspan="7" class="text-end"><i class="fas fa-hand-holding-usd"></i> Jumlah
                                            Bayar</td>
                                        <td>{{ rupiah($jmlhBayar) }}</td>
                                    </tr>
                                    <tr class="fw-bold text-end {{ $classSisa }}">
                                        <td colspan="7" class="text-end"><i class="fas fa-wallet"></i> Sisa Bayar</td>
                                        <td>{{ rupiah($sisaBayar) }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <!-- Riwayat Pembayaran -->
                        <h5 class="fw-semibold mb-2"><i class="bi bi-clock-history"></i> Riwayat Pembayaran</h5>
                        <div class="d-flex justify-content-end mb-2">
                            <button class="btn btn-success btn-sm" id="btnTambahPembayaran"><i
                                    class="bi bi-plus-circle"></i> Tambah Pembayaran</button>
                        </div>
                        @if (count($pembayaran))
                            <div class="table-responsive border rounded shadow-sm">
                                <table class="table table-sm table-hover table-bordered align-middle">
                                    <thead class="table-primary text-center">
                                        <tr>
                                            <th style="width:5%">No</th>
                                            <th style="width:10%">Tanggal</th>
                                            <th style="width:10%">Jenis Bayar</th>
                                            <th style="width:12%">Jumlah</th>
                                            <th style="width:15%">Tgl Input</th>
                                            <th>Keterangan</th>
                                            <th style="width:10%">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php $no = 1; @endphp
                                        @foreach ($pembayaran as $pb)
                                            <tr>
                                                <td class="text-center">{{ $no++ }}</td>
                                                <td>{{ tanggal_indo2($pb->tanggal) }}</td>
                                                <td class="text-center">
                                                    @if ($pb->jenis_bayar == 'cash')
                                                        <span class="badge bg-success"><i class="bi bi-cash"></i>
                                                            Cash</span>
                                                    @elseif ($pb->jenis_bayar == 'transfer')
                                                        <span class="badge bg-warning text-dark"><i
                                                                class="bi bi-bank"></i>
                                                            Transfer</span>
                                                    @else
                                                        <span
                                                            class="badge bg-secondary text-white">{{ ucfirst($pb->jenis_bayar) }}</span>
                                                    @endif
                                                </td>
                                                <td class="text-end text-success fw-bold"
                                                    title="{{ number_format($pb->jumlah, 2, ',', '.') }}">
                                                    {{ rupiah($pb->jumlah) }}
                                                </td>
                                                <td>{{ \Carbon\Carbon::parse($pb->created_at)->format('d-m-Y H:i') }}</td>
                                                <td>{{ $pb->keterangan ?: '-' }}</td>
                                                <td class="text-center">
                                                    <button class="btn btn-sm btn-outline-primary btnEditPembayaran"
                                                        data-id="{{ $pb->no_bukti }}" data-tgl="{{ $pb->tanggal }}"
                                                        data-jml="{{ $pb->jumlah }}"
                                                        data-jenisbayar="{{ $pb->jenis_bayar }}"
                                                        data-ket="{{ $pb->keterangan }}" title="Edit Pembayaran"><i
                                                            class="bi bi-pencil-square"></i></button>
                                                    <button data-href="{{ route('deletePembayaranPembelian', $pb->id) }}"
                                                        class="btn btn-sm btn-outline-danger btnHapusPembayaran"
                                                        title="Hapus Pembayaran"><i class="bi bi-trash"></i></button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="alert alert-info">Belum ada riwayat pembayaran.</div>
                        @endif
                    </div> <!-- end card-body -->
                </div> <!-- end card -->
            </div> <!-- end col -->
        </div> <!-- end row -->
    </div> <!-- end container-fluid -->
    <div class="modal fade" id="modalPembayaran" tabindex="-1" aria-labelledby="modalPembayaranLabel"
        aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered">
            <form id="formPembayaran" method="POST" class="needs-validation" novalidate>
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
                        <input type="hidden" name="no_faktur" value="{{ $pembelian->no_faktur }}" id="pay_faktur">

                        <div class="mb-3">
                            <label for="pay_tanggal" class="form-label fw-semibold">
                                <i class="bi bi-calendar-check me-1"></i> Tanggal
                            </label>
                            <input type="date" class="form-control form-control-sm" value="{{ Date('Y-m-d') }}"
                                id="pay_tanggal" name="tanggal" required>
                        </div>

                        <div class="mb-3">
                            <label for="pay_metode" class="form-label fw-semibold">
                                <i class="bi bi-wallet2 me-1"></i> Jenis Bayar
                            </label>
                            <select class="select2-sales" id="pay_metode" name="jenis_bayar" required>
                                <option value="">Pilih</option>
                                <option value="cash">Cash</option>
                                <option value="transfer">Transfer</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="pay_jumlah" class="form-label fw-semibold">
                                <i class="bi bi-currency-dollar me-1"></i> Jumlah Pembayaran
                            </label>
                            <input type="text" class="form-control form-control-sm text-end" id="pay_jumlah"
                                name="jumlah" placeholder="Masukkan jumlah" required>
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
            $('.select2-sales').select2({
                dropdownParent: $('#modalPembayaran'),
                width: '100%',
            });

            $('.btnHapusPembayaran').on('click', function(e) {
                e.preventDefault();
                const url = $(this).data('href');
                Swal.fire({
                    title: 'Hapus Pembayaran?',
                    text: 'Data tidak dapat dikembalikan.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Hapus',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#d33'
                }).then(result => {
                    if (result.isConfirmed) {
                        Swal.fire('Berhasil', 'Pembayaran berhasil dihapus.', 'success')
                            .then(() => location.href = url);
                    }
                });
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

            $('.btnEditPembayaran').on('click', function() {
                const id = $(this).data('id');
                const tgl = $(this).data('tgl');
                const jml = $(this).data('jml');
                const metode = $(this).data('jenisbayar');
                const ket = $(this).data('ket');

                resetForm();
                $('#pay_id').val(id);
                $('#pay_tanggal').val(tgl);
                $('#pay_jumlah').val(formatRupiah(jml));
                $('#pay_metode').val(metode).trigger('change');
                $('#pay_keterangan').val(ket);

                $('#modalPembayaranLabel').text('Edit Pembayaran');
                $('.btnSimpan').text('Perbarui');
                $('#modalPembayaran').modal('show');
            });

            const ROUTE_STORE = "{{ route('storePembayaranPembelian') }}";
            const ROUTE_UPDATE = "{{ route('updatePembayaranPembelian', ['id' => ':id']) }}";

            $('#formPembayaran').on('submit', function(e) {
                e.preventDefault();
                const id = $('#pay_id').val();
                const url = id ?
                    ROUTE_UPDATE.replace(':id', id) :
                    ROUTE_STORE;

                $.ajax({
                    type: 'POST',
                    url: url,
                    data: {
                        _token: "{{ csrf_token() }}",
                        id: id,
                        tanggal: $('#pay_tanggal').val(),
                        jenis_bayar: $('#pay_metode').val(),
                        jumlah: parseRupiah($('#pay_jumlah').val()),
                        no_faktur: $('#pay_faktur').val(),
                        keterangan: $('#pay_keterangan').val()
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
            }
        });
    </script>


@endsection
