@extends('layouts.template')
@section('titlepage', 'Detail Penjualan')
@section('contents')
    @php
        $totalDiskon = $detail->sum('total_diskon'); // perbaikan di sini
        $grandTotal = $detail->sum('total'); // subtotal = harga kotor, total = harga bersih setelah diskon
        $karyawan = DB::table('hrd_karyawan')->where('nik', $penjualan->id_user)->first();
        $jmlhBayar = DB::table('penjualan_pembayaran')->where('no_faktur', $penjualan->no_faktur)->sum('jumlah');
        $jmlhBayarTf = DB::table('penjualan_pembayaran_transfer')->where('status', 'disetujui')->where('no_faktur', $penjualan->no_faktur)->sum('jumlah');
        $jmlhBayarGiro = DB::table('penjualan_pembayaran_giro')->where('status', 'disetujui')->where('no_faktur', $penjualan->no_faktur)->sum('jumlah');
        $sisaBayar = $grandTotal - ($jmlhBayar + $jmlhBayarTf + $jmlhBayarGiro);
        $classSisa = $sisaBayar > 0 ? 'table-danger' : 'table-success';
    @endphp
    <div class="container-fluid mt-3 p-0 animate__animated animate__fadeIn">
        <div class="row">
            <div class="col-12 col-xl-12 mx-auto">
                <div class="card border-0 shadow-lg">
                    <div
                        class="card-header bg-primary shadow-sm bg-gradient text-white d-flex justify-content-between align-items-center">
                        <h4 class="mb-0"><i class="bi bi-receipt"></i> Detail Penjualan</h4>
                        <span class="badge bg-light text-primary fs-6">{{ $penjualan->no_faktur }}</span>
                    </div>
                    <div class="card-body">
                        <div class="row g-4 mt-1 mb-3">
                            <div class="col-md-6">
                                <div class="border rounded p-3 h-100">
                                    <h6 class="text-primary fw-bold mb-3"><i class="bi bi-info-circle"></i> Info Transaksi
                                    </h6>
                                    <table class="table table-borderless table-sm mb-0">
                                        <tr>
                                            <th style="width:40%">Tanggal</th>
                                            <td>{{ tanggal_indo2($penjualan->tanggal) }}</td>
                                        </tr>
                                        <tr>
                                            <th>Jenis Transaksi</th>
                                            <td>
                                                <a
                                                    class="btn btn-sm {{ $penjualan->jenis_transaksi == 'T' ? 'btn-primary' : 'btn-warning text-dark' }}">
                                                    {{ $penjualan->jenis_transaksi }}
                                                </a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Keterangan</th>
                                            <td>{{ $penjualan->keterangan ?: '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Waktu Input</th>
                                            <td>{{ $penjualan->created_at ?: '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Penginput</th>
                                            <td>{{ $karyawan->nama_lengkap ?: '-' }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="border rounded p-3 h-100">
                                    <h6 class="text-primary fw-bold mb-3"><i class="bi bi-truck"></i> Info Pelanggan</h6>
                                    <table class="table table-borderless table-sm mb-0">
                                        <tr>
                                            <th style="width:40%">Kode Pelanggan</th>
                                            <td>{{ $pelanggan->kode_pelanggan }}</td>
                                        </tr>
                                        <tr>
                                            <th>Nama Pelanggan</th>
                                            <td>{{ $pelanggan->nama_pelanggan }}</td>
                                        </tr>
                                        <tr>
                                            <th>Alamat</th>
                                            <td>{{ $pelanggan->alamat_pelanggan }}</td>
                                        </tr>
                                        <tr>
                                            <th>Wilayah</th>
                                            <td>{{ $pelanggan->nama_wilayah }}</td>
                                        </tr>
                                        <tr>
                                            <th>No HP</th>
                                            <td>{{ $pelanggan->no_hp_pelanggan }}</td>
                                        </tr>
                                        <tr>
                                            <th>LJT</th>
                                            <td>{{ $pelanggan->ljt }} Hari</td>
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
                                        <th style="width:7%">D1 (%)</th>
                                        <th style="width:7%">D2 (%)</th>
                                        <th style="width:7%">D3 (%)</th>
                                        <th style="width:10%">Potongan</th>
                                        <th style="width:12%">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $no = 1; @endphp
                                    @foreach ($detail as $d)
                                        <tr class="{{ $d->is_promo == '1' ? 'table-danger' : '' }}">
                                            <td class="text-center">{{ $no++ }}</td>
                                            <td>{{ $d->kode_barang }}</td>
                                            <td>{{ $d->nama_barang }}</td>
                                            <td class="text-end">{{ $d->qty }}</td>
                                            <td><a class="btn btn-sm btn-primary">{{ strtoupper($d->satuan) }}</a></td>
                                            <td class="text-end">{{ rupiah($d->harga) }}</td>
                                            <td class="text-center">{{ $d->diskon1_persen ?? '' }}</td>
                                            <td class="text-center">{{ $d->diskon2_persen ?? '' }}</td>
                                            <td class="text-center">{{ $d->diskon3_persen ?? '' }}</td>
                                            <td class="text-end">{{ rupiah($d->total_diskon) }}</td>
                                            <td class="text-end">{{ rupiah($d->total) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr class="fw-bold text-end table-primary">
                                        <td colspan="10" class="text-end"><i class="fas fa-tags"></i> Potongan</td>
                                        <td>{{ rupiah($totalDiskon) }}</td>
                                    </tr>
                                    <tr class="fw-bold text-end table-primary">
                                        <td colspan="10" class="text-end"><i class="fas fa-receipt"></i> Subtotal</td>
                                        <td>{{ rupiah($grandTotal + $totalDiskon) }}</td>
                                    </tr>
                                    <tr class="fw-bold text-end table-danger text-white">
                                        <td colspan="10" class="text-end"><i class="fas fa-undo-alt"></i> Retur Potong
                                            Faktur</td>
                                        <td>{{ rupiah($potongFaktur ?? 0) }}</td>
                                    </tr>
                                    <tr class="fw-bold text-end bg-dark text-white fs-5">
                                        <td colspan="10" class="text-end"><i class="fas fa-coins"></i> Total Keseluruhan
                                        </td>
                                        <td>{{ rupiah(($grandTotal ?? 0) - ($potongFaktur ?? 0)) }}</td>
                                    </tr>
                                    <tr class="fw-bold text-end table-success">
                                        <td colspan="10" class="text-end"><i class="fas fa-hand-holding-usd"></i> Jumlah
                                            Bayar</td>
                                        <td>{{ rupiah($jmlhBayar ?? 0) }}</td>
                                    </tr>
                                    <tr class="fw-bold text-end {{ $classSisa }}">
                                        <td colspan="10" class="text-end"><i class="fas fa-wallet"></i> Sisa Bayar</td>
                                        <td>
                                            @if((($sisaBayar-$potongFaktur) ?? 0) == 0)
                                                <a class="btn btn-sm btn-success">Lunas</a>
                                            @else
                                                <a class="btn btn-sm btn-danger">{{ rupiah(($sisaBayar-$potongFaktur)) }}</a>
                                            @endif
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        @if(count($retur) > 0)
                            <h5 class="fw-semibold mb-3"><i class="bi bi-arrow-counterclockwise"></i> Retur Barang</h5>
                            <div class="table-responsive mb-4">
                                <table class="table table-bordered table-sm table-hover text-nowrap">
                                    <thead class="table-light text-center">
                                        <tr>
                                            <th style="width:3%">No</th>
                                            <th style="width:12%">Tgl Retur</th>
                                            <th style="width:10%">No Retur</th>
                                            <th style="width:8%">Jenis</th>
                                            <th style="width:8%">Kode</th>
                                            <th>Nama Barang</th>
                                            <th style="width:6%">Qty</th>
                                            <th style="width:8%">Satuan</th>
                                            <th style="width:10%">Harga Retur</th>
                                            <th style="width:12%">Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $no = 1;
                                            $totalRetur = 0;
                                        @endphp
                                        @foreach($retur as $r)
                                            @php
                                                $totalRetur += $r->subtotal_retur;
                                            @endphp
                                            <tr>
                                                <td class="text-center">{{ $no++ }}</td>
                                                <td class="text-center">{{ tanggal_indo($r->tanggal) }}</td>
                                                <td>{{ $r->no_retur }}</td>
                                                <td class="text-center">{{ strtoupper($r->jenis_retur) }}</td>
                                                <td>{{ $r->kode_barang }}</td>
                                                <td>{{ $r->nama_barang }}</td>
                                                <td class="text-end">{{ $r->qty }}</td>
                                                <td class="text-center">{{ $r->satuan ?? '-' }}</td>
                                                <td class="text-end">{{ rupiah($r->harga_retur) }}</td>
                                                <td class="text-end">{{ rupiah($r->subtotal_retur) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr class="fw-bold text-end table-warning">
                                            <td colspan="9" class="text-end">TOTAL</td>
                                            <td>{{ rupiah($totalRetur) }}</td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        @endif
                        @if($sisaBayar != 0)
                            <div class="d-flex justify-content-end mb-2">
                                <button class="btn btn-primary btn-sm" id="btnTambahPembayaran">
                                    <i class="bi bi-plus-circle"></i> Tambah Pembayaran
                                </button>
                            </div>
                        @endif
                        <h5 class="fw-semibold mb-2"><i class="bi bi-clock-history"></i> Riwayat Pembayaran</h5>
                        @if($pembayaran->count())
                            <div class="table-responsive mb-4" style="max-height:500px;overflow-y:auto;">
                                <table class="table table-bordered table-sm table-hover text-nowrap">
                                    <thead class="table-light text-center sticky-top bg-white shadow-sm">
                                        <tr>
                                            <th style="width:3%">No</th>
                                            <th style="width:10%">Tanggal</th>
                                            <th style="width:8%">Penagih</th>
                                            <th style="width:8%">Jenis</th>
                                            <th style="width:12%">Jumlah</th>
                                            <th>Keterangan</th>
                                            <th style="width:12%">Tgl Input</th>
                                            <th style="width:8%">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($pembayaran as $no => $pb)
                                            <tr>
                                                <td class="text-center">{{ $no + 1 }}</td>
                                                <td>{{ tanggal_indo2($pb->tanggal) }}</td>
                                                <td class="text-center">{{ $pb->nama_sales }}</td>
                                                <td class="text-center">
                                                    <a
                                                        class="btn btn-sm {{ $pb->jenis_bayar == 'tunai' ? 'btn-success' : 'btn-warning' }}">{{ ucfirst($pb->jenis_bayar) }}
                                                    </a>
                                                </td>
                                                <td class="text-end fw-bold" title="{{ rupiah($pb->jumlah) }}">
                                                    {{ rupiah($pb->jumlah) }}
                                                </td>
                                                <td>{{ $pb->keterangan ?: '-' }}</td>
                                                <td>{{ \Carbon\Carbon::parse($pb->created_at)->format('d-m-Y H:i') }}</td>
                                                @php
                                                    $tanggalPembayaran = \Carbon\Carbon::parse($pb->tanggal);
                                                    $batasEdit = $tanggalPembayaran->copy()->addDays(3);
                                                    $sekarang = \Carbon\Carbon::now();
                                                @endphp
                                                @if($sisaBayar != 0 && $sekarang->lessThan($batasEdit))
                                                    <td class="text-center">
                                                        <button class="btn btn-sm btn-outline-primary btnEditPembayaran"
                                                            data-id="{{ $pb->no_bukti }}" data-tgl="{{ $pb->tanggal }}"
                                                            data-jml="{{ $pb->jumlah }}" data-jenisbayar="{{ $pb->jenis_bayar }}"
                                                            data-ket="{{ $pb->keterangan }}" data-sales="{{ $pb->kode_sales }}"
                                                            title="Edit"><i class="bi bi-pencil-square"></i></button>

                                                        <button class="btn btn-sm btn-outline-danger btnHapusPembayaran"
                                                            data-href="{{ route('deletePembayaranPenjualan', $pb->no_bukti) }}"
                                                            title="Hapus"><i class="bi bi-trash"></i></button>
                                                    </td>
                                                @endif
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="alert alert-info">Belum ada riwayat pembayaran.</div>
                        @endif

                        @if($transfer->count())
                            <h5 class="fw-semibold mb-2"><i class="bi bi-clock-history"></i> Riwayat Pembayaran Transfer</h5>
                            <div class="table-responsive mb-4" style="max-height:500px;overflow-y:auto;">
                                <table class="table table-bordered table-sm table-hover text-nowrap">
                                    <thead class="table-light text-center sticky-top bg-white shadow-sm">
                                        <tr>
                                            <th style="width:3%">No</th>
                                            <th style="width:10%">Tanggal</th>
                                            <th style="width:8%">Penagih</th>
                                            <th style="width:8%">Status</th>
                                            <th style="width:12%">Jumlah</th>
                                            <th style="width:8%">Bank</th>
                                            <th>Keterangan</th>
                                            <th style="width:12%">Tgl Input</th>
                                            <th style="width:8%">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($transfer as $no => $tr)
                                            <tr>
                                                <td class="text-center">{{ $no + 1 }}</td>
                                                <td>{{ tanggal_indo2($tr->tanggal) }}</td>
                                                <td class="text-center">{{ $tr->nama_sales }}</td>
                                                <td class="text-center">
                                                    <a
                                                        class="btn btn-sm fw-bold {{ $tr->status == 'disetujui' ? 'btn-success' : ($tr->status == 'ditolak' ? 'btn-danger' : 'btn-warning') }}">
                                                        {{ ucfirst($tr->status) }}
                                                    </a>
                                                </td>
                                                <td class="text-end fw-bold" title="{{ rupiah($tr->jumlah) }}">
                                                    {{ rupiah($tr->jumlah) }}
                                                </td>
                                                <td class="text-center">{{ $tr->bank_pengirim }}</td>
                                                <td>{{ $tr->keterangan ?: '-' }}</td>
                                                <td>{{ \Carbon\Carbon::parse($tr->created_at)->format('d-m-Y H:i') }}</td>
                                                @if($tr->status == 'pending')
                                                    <td class="text-center">
                                                        <button class="btn btn-sm btn-outline-primary btnEditPembayaran"
                                                            data-id="{{ $tr->no_faktur }}" data-tgl="{{ $tr->tanggal }}"
                                                            data-jml="{{ $tr->jumlah }}" data-jenisbayar="transfer"
                                                            data-ket="{{ $tr->keterangan }}" data-sales="{{ $tr->kode_sales }}"
                                                            data-bank="{{ $tr->bank_pengirim }}" title="Edit"><i
                                                                class="bi bi-pencil-square"></i></button>
                                                        <button class="btn btn-sm btn-outline-danger btnHapusPembayaran"
                                                            data-href="{{ route('deletePembayaranPenjualanTransfer', $tr->kode_transfer) }}"
                                                            title="Hapus"><i class="bi bi-trash"></i></button>
                                                    </td>
                                                @endif
                                            </tr>
                                        @endforeach
                                    </tbody>

                                </table>
                            </div>
                        @endif

                        @if($giro->count())
                            <h5 class="fw-semibold mb-2"><i class="bi bi-clock-history"></i> Riwayat Pembayaran Giro</h5>
                            <div class="table-responsive mb-4" style="max-height:500px;overflow-y:auto;">
                                <table class="table table-bordered table-sm table-hover text-nowrap">
                                    <thead class="table-light text-center sticky-top bg-white shadow-sm">
                                        <tr>
                                            <th style="width:3%">No</th>
                                            <th style="width:10%">Tanggal</th>
                                            <th style="width:8%">Penagih</th>
                                            <th style="width:8%">Status</th>
                                            <th style="width:12%">Jumlah</th>
                                            <th style="width:12%">No Giro</th>
                                            <th style="width:8%">Bank</th>
                                            <th style="width:10%">Jatuh Tempo</th>
                                            <th>Keterangan</th>
                                            <th style="width:12%">Tgl Input</th>
                                            <th style="width:8%">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($giro as $no => $gr)
                                            <tr>
                                                <td class="text-center">{{ $no + 1 }}</td>
                                                <td>{{ tanggal_indo2($gr->tanggal) }}</td>
                                                <td class="text-center">{{ $gr->nama_sales }}</td>
                                                <td class="text-center">
                                                    <a
                                                        class="btn btn-sm fw-bold {{ $gr->status == 'disetujui' ? 'btn-success' : ($gr->status == 'ditolak' ? 'btn-danger' : 'btn-warning') }}">
                                                        {{ ucfirst($gr->status) }}
                                                    </a>
                                                </td>
                                                <td class="text-end fw-bold" title="{{ rupiah($gr->jumlah) }}">
                                                    {{ rupiah($gr->jumlah) }}
                                                </td>
                                                <td class="text-center">{{ $gr->no_giro ?: '-' }}</td>
                                                <td class="text-center">{{ $gr->bank_pengirim }}</td>
                                                <td class="text-center">{{ tanggal_indo2($gr->jatuh_tempo) }}</td>
                                                <td>{{ $gr->keterangan ?: '-' }}</td>
                                                <td>{{ \Carbon\Carbon::parse($gr->created_at)->format('d-m-Y H:i') }}</td>
                                                @if($gr->status == 'pending')
                                                    <td class="text-center">
                                                        <button class="btn btn-sm btn-outline-primary btnEditPembayaran"
                                                            data-id="{{ $gr->no_faktur }}" data-tgl="{{ $gr->tanggal }}"
                                                            data-jml="{{ $gr->jumlah }}" data-jenisbayar="giro"
                                                            data-ket="{{ $gr->keterangan }}" data-sales="{{ $gr->kode_sales }}"
                                                            data-bank="{{ $gr->bank_pengirim }}" data-nogiro="{{ $gr->no_giro }}"
                                                            data-jatuhtempo="{{ $gr->jatuh_tempo }}" title="Edit"><i
                                                                class="bi bi-pencil-square"></i></button>

                                                        <button class="btn btn-sm btn-outline-danger btnHapusPembayaran"
                                                            data-href="{{ route('deletePembayaranPenjualanGiro', $gr->kode_giro) }}"
                                                            title="Hapus"><i class="bi bi-trash"></i></button>
                                                    </td>
                                                @else
                                                    <td></td>
                                                @endif
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif

                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modalPembayaran" tabindex="-1" aria-labelledby="modalPembayaranLabel" aria-hidden="true"
        data-bs-backdrop="static" data-bs-keyboard="false">
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
                        <input type="hidden" name="no_faktur" value="{{ $penjualan->no_faktur }}" id="pay_faktur">
                        <input type="hidden" id="pay_pelanggan" name="kode_pelanggan"
                            value="{{ $penjualan->kode_pelanggan }}">
                        <div class="mb-3">
                            <label for="pay_tanggal" class="form-label fw-semibold">
                                <i class="bi bi-calendar-check me-1"></i> Tanggal
                            </label>
                            <input type="date" class="form-control form-control-sm" value="{{ Date('Y-m-d')}}"
                                id="pay_tanggal" name="tanggal" required>
                        </div>

                        <div class="mb-3">
                            <label for="pay_sales" class="form-label fw-semibold">
                                <i class="bi bi-person-badge me-1"></i> Sales Penagih
                            </label>
                            <select class="form-select-sm select2-sales" name="kode_sales" id="pay_sales" required>
                                <option value="">Pilih Sales</option>
                                @foreach($sales as $s)
                                    <option value="{{ $s->nik }}" {{ $penjualan->kode_sales == $s->nik ? 'selected' : '' }}>
                                        {{ $s->nama_lengkap }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="pay_metode" class="form-label fw-semibold">
                                <i class="bi bi-wallet2 me-1"></i> Jenis Bayar
                            </label>
                            <select class="select2-sales" id="pay_metode" name="jenis_bayar" required>
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
                            <input type="text" class="form-control form-control-sm text-end" id="pay_jumlah" name="jumlah"
                                placeholder="Masukkan jumlah" required>
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
        $(document).ready(function () {

            function toggleBankPengirim() {
                const jenis = $('#pay_metode').val();
                if (jenis === 'transfer') {
                    $('#bank_pengirim_wrapper').show();
                    $('#bank_pengirim_wrapper input').attr('required', true);
                } else {
                    $('#bank_pengirim_wrapper').hide();
                    $('#bank_pengirim_wrapper input').removeAttr('required');
                }
            }

            $('#modalPembayaran').on('shown.bs.modal', function () {
                toggleBankPengirim();
            });

            $('#pay_metode').on('select2:select', function () {
                toggleBankPengirim();
            });


            $('.select2-sales').select2({
                dropdownParent: $('#modalPembayaran'),
                width: '100%',
            });

            $('.btnHapusPembayaran').on('click', function (e) {
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

            $('#pay_jumlah').on('input', function () {
                let clear = parseRupiah($(this).val());
                $(this).val(formatRupiah(clear));
            });

            $('#btnTambahPembayaran').on('click', function () {
                resetForm();
                $('#modalPembayaranLabel').text('Tambah Pembayaran');
                $('.btnSimpan').text('Simpan');
                $('#modalPembayaran').modal('show');
            });

            $('.btnEditPembayaran').on('click', function () {
                const id = $(this).data('id');
                const tgl = $(this).data('tgl');
                const jml = $(this).data('jml');
                const metode = $(this).data('jenisbayar');
                const ket = $(this).data('ket');
                const sales = $(this).data('sales');

                resetForm();

                $('#pay_id').val(id);
                $('#pay_tanggal').val(tgl);
                $('#pay_jumlah').val(formatRupiah(jml));

                $('#modalPembayaran').modal('show');

                $('#modalPembayaran').one('shown.bs.modal', function () {
                    $('#pay_metode').val(metode).trigger('change.select2');
                    $('#pay_sales').val(sales).trigger('change.select2');
                    $('#pay_keterangan').val(ket);
                });

                $('#modalPembayaranLabel').text('Edit Pembayaran');
                $('.btnSimpan').text('Perbarui');
            });

            const ROUTE_STORE = "{{ route('storePembayaranPenjualan') }}";
            const ROUTE_UPDATE = "{{ route('updatePembayaranPenjualan', ['id' => ':id']) }}";

            $('#formPembayaran').on('submit', function (e) {
                e.preventDefault();
                const id = $('#pay_id').val();
                const url = id
                    ? ROUTE_UPDATE.replace(':id', id)
                    : ROUTE_STORE;
                $.ajax({
                    type: 'POST',
                    url: url,
                    data: {
                        _token: "{{ csrf_token() }}",
                        id: $('#pay_id').val(),
                        tanggal: $('#pay_tanggal').val(),
                        jenis_bayar: $('#pay_metode').select2('val'),
                        jumlah: parseRupiah($('#pay_jumlah').val()),
                        no_faktur: $('#pay_faktur').val(),
                        keterangan: $('#pay_keterangan').val(),
                        kode_pelanggan: $('#pay_pelanggan').val(),
                        kode_sales: $('#pay_sales').val(),
                        bank_pengirim: $('input[name="bank_pengirim"]').val()
                    },
                    success: function () {
                        $('#modalPembayaran').modal('hide');
                        Swal.fire('Berhasil', 'Pembayaran disimpan.', 'success')
                            .then(() => location.reload());
                    },
                    error: function (xhr) {
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
