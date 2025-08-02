<div class="container-fluid p-0">
    <div class="row">
        <div class="col-6">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fa fa-box"></i> Laporan Penjualan</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('cetakLaporanPenjualan') }}" method="POST" target="_blank"
                        autocomplete="off">
                        @csrf

                        <div class="row g-2 mt-3">
                            <div class="col-md-6">
                                <input type="date" class="form-control form-control-sm" id="tanggal_dari"
                                    name="tanggal_dari" placeholder="Tanggal Dari" value="{{ request('tanggal_dari') }}"
                                    required>
                            </div>
                            <div class="col-md-6">
                                <input type="date" class="form-control form-control-sm" id="tanggal_sampai"
                                    name="tanggal_sampai" placeholder="Tanggal Sampai"
                                    value="{{ request('tanggal_sampai') }}" required>
                            </div>
                        </div>

                        {{-- Salesman --}}
                        <div class="row mt-3">
                            <div class="col-md-12">
                                <select name="salesman" class="form-select2 form-select-sm">
                                    <option value="">-- Semua Salesman --</option>
                                    @foreach ($sales as $s)
                                        <option value="{{ $s->nik }}" {{ request('salesman') == $s->nik ? 'selected' : '' }}>
                                            {{ $s->nama_lengkap }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- Pelanggan --}}
                        <div class="row mt-3">
                            <div class="col-md-12">
                                <select name="kode_pelanggan" class="form-select2 form-select-sm">
                                    <option value="">-- Semua Pelanggan --</option>
                                    @foreach ($pelanggan as $p)
                                        <option value="{{ $p->kode_pelanggan }}" {{ request('kode_pelanggan') == $p->kode_pelanggan ? 'selected' : '' }}>
                                            {{ $p->nama_pelanggan }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- Jenis Laporan --}}
                        <div class="row mt-3">
                            <div class="col-md-12">
                                <select name="jenis_laporan" class="form-select2 form-select-sm" required>
                                    <option value="">-- Pilih Jenis Laporan --</option>
                                    <option value="1" {{ request('jenis_laporan') == '1' ? 'selected' : '' }}>Format 1
                                    </option>
                                    <option value="2" {{ request('jenis_laporan') == '2' ? 'selected' : '' }}>Format 2
                                    </option>
                                </select>
                            </div>
                        </div>

                        {{-- Jenis Transaksi --}}
                        <div class="row mt-3">
                            <div class="col-md-12">
                                <select name="jenis_transaksi" class="form-select2 form-select-sm">
                                    <option value="">-- Semua Jenis Transaksi --</option>
                                    <option value="T" {{ request('jenis_transaksi') == 'T' ? 'selected' : '' }}>Tunai
                                    </option>
                                    <option value="K" {{ request('jenis_transaksi') == 'K' ? 'selected' : '' }}>Kredit
                                    </option>
                                </select>
                            </div>
                        </div>

                        {{-- Status Faktur --}}
                        <div class="row mt-3">
                            <div class="col-md-12">
                                <select name="batal" class="form-select2 form-select-sm">
                                    <option value="">-- Semua Faktur --</option>
                                    <option value="1" {{ request('batal') == '1' ? 'selected' : '' }}>Faktur Batal
                                    </option>
                                    <option value="0" {{ request('batal') == '0' ? 'selected' : '' }}>Faktur Aktif
                                    </option>
                                </select>
                            </div>
                        </div>

                        <div class="row text-center mt-4">
                            <div class="col-md-6 mb-2">
                                <button type="submit" name="cetak" class="btn btn-sm btn-primary w-100 py-1">
                                    <i class="fa fa-print me-1"></i> Cetak Laporan
                                </button>
                            </div>
                            <div class="col-md-6 mb-2">
                                <button type="submit" name="export" class="btn btn-sm btn-success w-100 py-1">
                                    <i class="fa fa-file-excel me-1"></i> Export Excel
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
