<div class="row">
    <div class="col-md-12">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fa fa-shopping-bag"></i> Laporan Semua Pembelian</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('cetakLaporanSemuaPembelian') }}" method="POST" autocomplete="off"
                    target="_blank">
                    @csrf
                    <div class="row g-2 mt-2">
                        <div class="col-md-12">
                            <label for="kode_supplier" class="form-label mb-0">Supplier</label>
                            <select name="kode_supplier" id="kode_supplier" class="form-select form-select-sm ">
                                <option value="">-- Semua Supplier --</option>
                                @foreach($suppliers as $s)
                                    <option value="{{ $s->kode_supplier }}" {{ request('kode_supplier') == $s->kode_supplier ? 'selected' : '' }}>
                                        {{ $s->nama_supplier }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label for="tanggal_mulai" class="form-label mb-0">Tanggal Mulai</label>
                            <input type="date" name="tanggal_mulai" id="tanggal_mulai"
                                class="form-control form-control-sm"
                                value="{{ request('tanggal_mulai') ?? date('Y-m-01') }}">
                        </div>
                        <div class="col-md-6">
                            <label for="tanggal_sampai" class="form-label mb-0">Tanggal Sampai</label>
                            <input type="date" name="tanggal_sampai" id="tanggal_sampai"
                                class="form-control form-control-sm"
                                value="{{ request('tanggal_sampai') ?? date('Y-m-d') }}">
                        </div>

                        <div class="col-md-12">
                            <label for="jenis_laporan" class="form-label mb-0">Jenis Laporan</label>
                            <select name="jenis_laporan" id="jenis_laporan" class="form-select form-select-sm">
                                <option value="1" {{ request('jenis_laporan') == '1' ? 'selected' : '' }}>Format 1
                                </option>
                                <option value="2" {{ request('jenis_laporan') == '2' ? 'selected' : '' }}>Format 2
                                </option>
                                <option value="3" {{ request('jenis_laporan') == '3' ? 'selected' : '' }}>Format 3
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
