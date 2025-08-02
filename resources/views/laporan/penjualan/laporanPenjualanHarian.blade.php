<div class="container-fluid p-0">
    <div class="row">
        <div class="col-6">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fa fa-box"></i> Laporan Penjualan Harian</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('cetakLaporanPenjualanHarian') }}" method="POST" target="_blank"
                        autocomplete="off">
                        @csrf
                        {{-- <div class="mb-3 mt-4">
                            <select id="kode_barang" name="kode_barang" class="form-select form-select-sm" tabindex="2">
                            </select>
                        </div> --}}
                        <div class="row g-2 mt-3">
                            <div class="col-md-12">
                                <div class="custom-datepicker-wrapper">
                                    <input type="date" class="form-control form-control-sm" id="tanggal" name="tanggal"
                                        placeholder="Tanggal Sampai" value="{{ request('tanggal') }}" required>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-12">
                                <select name="salesman" class="form-select2 form-select-sm" required>
                                    <option value="">-- Pilih Salesman --</option>
                                    @foreach ($sales as $s)
                                        <option value="{{ $s->nik }}" {{ request('salesman') == $s->nik ? 'selected' : '' }}>
                                            {{ $s->nama_lengkap }}
                                        </option>
                                    @endforeach
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
