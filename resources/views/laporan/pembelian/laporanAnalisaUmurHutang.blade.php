<div class="row">
    <div class="col-md-12">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fa fa-hourglass-half me-1"></i> Laporan Analisa Umur Hutang</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('cetakAnalisaUmurHutang') }}" method="POST" autocomplete="off" target="_blank">
                    @csrf
                    <div class="row g-2 mt-2">
                        <div class="col-md-12">
                            <label for="tanggal" class="form-label mb-0">Sampai Tanggal</label>
                            <input type="date" name="tanggal" id="tanggal" class="form-control form-control-sm"
                                value="{{ request('tanggal') ?? date('Y-m-d') }}">
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
