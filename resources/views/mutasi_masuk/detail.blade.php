@php
    $totalQty = $detail->sum('qty');
@endphp

<div class="row">
    <div class="col-12 mx-auto">
        <div class="card border-0 shadow-lg">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h4 class="mb-0"><i class="bi bi-box-arrow-in-down"></i> Detail Barang Masuk</h4>
                <span class="badge bg-light text-primary fs-6">{{ $barangmasuk->kode_transaksi }}</span>
            </div>

            <div class="card-body">
                <!-- Info Transaksi dan Form Terima Barang (dalam 1 baris) -->
                <div class="row g-4 mb-4">
                    <!-- Info Transaksi (kiri) -->
                    <div class="col-md-6">
                        <div class="border rounded p-3 h-100">
                            <h6 class="text-primary fw-bold mb-3"><i class="bi bi-info-circle"></i> Info Transaksi</h6>
                            <table class="table table-borderless table-sm mb-0">
                                <tr>
                                    <th style="width:40%">Tanggal</th>
                                    <td>{{ tanggal_indo2($barangmasuk->tanggal) }}</td>
                                </tr>
                                <tr>
                                    <th>Sumber</th>
                                    <td>{{ $barangmasuk->sumber }}</td>
                                </tr>
                                <tr>
                                    <th>Keterangan</th>
                                    <td>{{ $barangmasuk->keterangan ?: '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Waktu Input</th>
                                    <td>{{ $barangmasuk->created_at }}</td>
                                </tr>
                                <tr>
                                    <th>Dibuat Oleh</th>
                                    <td>{{ $barangmasuk->user->name ?? '-' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <!-- Form Terima Barang (kanan) -->
                    <div class="col-md-6">
                        @if ($barangmasuk->tanggal_diterima)
                            <div class="border rounded p-3 bg-light">
                                <h6 class="fw-bold text-success mb-3"><i class="bi bi-check-circle me-2"></i> Barang
                                    Sudah Diterima</h6>
                                <table class="table table-sm table-borderless mb-0">
                                    <tr>
                                        <th>Tanggal Kirim</th>
                                        <td>{{ tanggal_indo2($barangmasuk->tanggal_diterima) }}</td>
                                    </tr>
                                    <tr>
                                        <th>Catatan</th>
                                        <td>{{ $barangmasuk->catatan ?: '-' }}</td>
                                    </tr>
                                </table>
                            </div>
                        @else
                            <div class="border rounded p-3">
                                <form action="{{ route('storeTerimaBarang') }}" method="POST" id="formSuratJalan">
                                    @csrf
                                    <input type="hidden" name="kode_transaksi"
                                        value="{{ $barangmasuk->kode_transaksi }}">

                                    <div class="row g-2">
                                        <div class="col-12">
                                            <label class="form-label small text-muted">Tanggal Terima</label>
                                            <input type="date" name="tanggal" value="{{ date('Y-m-d') }}"
                                                class="form-control form-control-sm" required>
                                        </div>

                                        <div class="col-12">
                                            <label class="form-label small text-muted">Catatan (Opsional)</label>
                                            <input type="text" name="keterangan" class="form-control form-control-sm"
                                                placeholder="Catatan penerimaan">
                                        </div>

                                        @if ($barangmasuk->jenis_pemasukan == 'Retur Penjualan')
                                            <div class="col-12 mt-2">
                                                <div class="alert alert-info p-2 text-center small">
                                                    <i class="bi bi-info-circle"></i> Atur jumlah barang yang
                                                    <strong>baik (GS)</strong> untuk di-repack
                                                </div>
                                            </div>
                                        @endif

                                        <div class="col-12 mt-3">
                                            <button type="submit" class="btn btn-primary btn-sm w-100">
                                                <i class="fa fa-paper-plane me-1"></i> Terima Barang
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Daftar Barang Masuk (hanya 1x, di bawah) -->

                <h5 class="fw-semibold mb-3"><i class="bi bi-bag-plus"></i> Daftar Barang Masuk</h5>
                <div class="table-responsive">
                    <table class="table table-sm table-bordered table-hover text-nowrap">
                        <thead class="table-light text-center">
                            <tr>
                                <th>No</th>
                                <th>Kode</th>
                                <th>Nama Barang</th>
                                <th>Satuan</th>
                                <th>Qty</th>
                                @if ($barangmasuk->jenis_pemasukan == 'Retur Penjualan' && empty($barangmasuk->tanggal_diterima))
                                    <th>Qty (GS)</th>
                                    <th>Qty (BS)</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($detail as $i => $d)
                                <tr>
                                    <td class="text-center">{{ $i + 1 }}</td>
                                    <td>{{ $d->kode_barang }}</td>
                                    <td>{{ $d->nama_barang }}</td>
                                    <td class="text-center">{{ $d->satuan }}</td>
                                    <td class="text-center" data-qty="{{ $d->qty }}">{{ $d->qty }}
                                    </td>

                                    @if ($barangmasuk->jenis_pemasukan == 'Retur Penjualan' && empty($barangmasuk->tanggal_diterima))
                                        <td>
                                            <input type="number"
                                                class="form-control text-center form-control-sm qty-repack"
                                                name="repack[{{ $d->id }}]" value="0" min="0"
                                                max="{{ $d->qty }}" form="formSuratJalan"
                                                style="font-size: 0.85rem; padding: 0.25rem;">
                                        </td>
                                        <td class="text-center fw-bold text-danger qty-bad">{{ $d->qty }}
                                        </td>
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript tetap -->
<script>
    $(document).on('input', '.qty-repack', function() {
        const row = $(this).closest('tr');
        const maxQty = parseInt($(this).attr('max')); // ambil dari max
        let repackQty = parseInt($(this).val()) || 0;

        if (repackQty < 0) repackQty = 0;
        if (repackQty > maxQty) {
            repackQty = maxQty;
            $(this).val(repackQty);
        }

        const badQty = maxQty - repackQty;
        row.find('.qty-bad').text(badQty);
    });
</script>
