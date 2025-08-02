<div class="row">
    <div class="col-md-12">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fa fa-warehouse"></i> Laporan Stok Gudang Bad Stok</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('cetakLaporanPersediaanBS') }}" method="POST" autocomplete="off" target="_blank">
                    @csrf
                    <div class="row g-2 mt-2">
                        <div class="col-md-12">
                            <select name="kode_supplier" class="form-select form-select-sm select2">
                                <option value="">-- Semua Supplier --</option>
                                @foreach($suppliers as $s)
                                    <option value="{{ $s->kode_supplier }}" {{ request('kode_supplier') == $s->kode_supplier ? 'selected' : '' }}>
                                        {{ $s->nama_supplier }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <select name="bulan" class="form-select form-select-sm">
                                @for($i = 1; $i <= 12; $i++)
                                    <option value="{{ $i }}" {{ (request('bulan') ?? date('n')) == $i ? 'selected' : '' }}>
                                        {{ DateTime::createFromFormat('!m', $i)->format('F') }}
                                    </option>
                                @endfor
                            </select>
                        </div>
                        <div class="col-md-6">
                            <select name="tahun" class="form-select form-select-sm">
                                @for($y = date('Y') - 2; $y <= date('Y') + 1; $y++)
                                    <option value="{{ $y }}" {{ (request('tahun') ?? date('Y')) == $y ? 'selected' : '' }}>
                                        {{ $y }}
                                    </option>
                                @endfor
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
