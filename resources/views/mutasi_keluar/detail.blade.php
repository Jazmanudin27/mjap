<div class="row">
    <div class="col-12 col-xl-12 mx-auto">
        <div class="card border-0 shadow-lg">
            <div class="card-header bg-danger text-white d-flex justify-content-between align-items-center">
                <h4 class="mb-0"><i class="bi bi-box-arrow-up"></i> Detail Barang Keluar</h4>
                <span class="badge bg-light text-danger fs-6">{{ $barangkeluar->kode_transaksi }}</span>
            </div>
            <div class="card-body">

                <form action="{{ route('storeKirimBarang') }}" autocomplete="off" method="POST" id="formSuratJalan">
                    <div class="row g-4 mt-1 mb-3">
                        <div class="col-md-6">
                            <div class="border rounded p-3 h-100">
                                <h6 class="text-danger fw-bold mb-3"><i class="bi bi-info-circle"></i> Info Transaksi
                                </h6>
                                <table class="table table-borderless table-sm mb-0">
                                    <tr>
                                        <th style="width:40%">Tanggal</th>
                                        <td>{{ tanggal_indo2($barangkeluar->tanggal) }}</td>
                                    </tr>
                                    <tr>
                                        <th>Tujuan</th>
                                        <td>{{ $barangkeluar->tujuan }}</td>
                                    </tr>
                                    <tr>
                                        <th>Keterangan</th>
                                        <td>{{ $barangkeluar->keterangan ?: '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Waktu Input</th>
                                        <td>{{ $barangkeluar->created_at }}</td>
                                    </tr>
                                    <tr>
                                        <th>Dibuat Oleh</th>
                                        <td>{{ $barangkeluar->user->name ?? '-' }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        @if ($barangkeluar->tanggal_dikirim)
                            <div class="col-md-6">
                                <div class="border rounded p-3 bg-light mb-3">
                                    <h6 class="fw-bold text-success mb-3"><i class="bi bi-check-circle me-2"></i> Barang
                                        Sudah
                                        Dikirim</h6>
                                    <table class="table table-sm table-borderless mb-0">
                                        <tr>
                                            <th style="width: 30%">Tanggal Kirim</th>
                                            <td>{{ tanggal_indo2($barangkeluar->tanggal_dikirim) }}</td>
                                        </tr>
                                        <tr>
                                            <th>Catatan</th>
                                            <td>{{ $barangkeluar->catatan ?: '-' }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        @else
                            <div class="col-md-6">
                                <div class="border rounded p-3 h-100">
                                    @csrf
                                    <input type="hidden" name="kode_transaksi"
                                        value="{{ $barangkeluar->kode_transaksi }}">
                                    <div class="row g-2 align-items-end">
                                        <div class="col-md-12">
                                            <label for="tanggal" class="form-label small text-muted">Tanggal
                                                Kirim</label>
                                            <input type="date" value="{{ date('Y-m-d') }}" name="tanggal"
                                                class="form-control form-control-sm shadow-sm" required>
                                        </div>

                                        <div class="col-md-12">
                                            <label for="keterangan" class="form-label small text-muted">Catatan</label>
                                            <input type="text" name="keterangan"
                                                class="form-control form-control-sm shadow-sm"
                                                placeholder="Catatan Kirim Barang (opsional)">
                                        </div>

                                        <div class="col-md-12 d-grid">
                                            <label class="form-label small text-muted">&nbsp;</label>
                                            <button type="submit" class="btn btn-primary btn-sm shadow">
                                                <i class="fa fa-paper-plane me-1"></i> Kirim Barang
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>

                    <h5 class="fw-semibold mb-3"><i class="bi bi-bag-dash"></i> Daftar Barang Keluar</h5>
                    <div class="table-responsive mb-1">
                        <table class="table table-sm table-bordered table-hover text-nowrap align-middle">
                            <thead class="table-light text-center">
                                <tr>
                                    <th style="width:5%">No</th>
                                    <th style="width:10%">Kode</th>
                                    <th>Nama Barang</th>
                                    <th style="width:6%">Satuan</th>
                                    <th style="width:6%">Qty</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($detail as $i => $d)
                                    <tr>
                                        <td class="text-center">{{ $i + 1 }}</td>
                                        <td>{{ $d->kode_barang }}</td>
                                        <td>{{ $d->nama_barang }}</td>
                                        <td class="text-center">{{ $d->satuan }}</td>
                                        <td class="text-center retur-qty">{{ $d->qty }}</td>
                                    </tr>
                                @endforeach
                            </tbody>

                        </table>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

