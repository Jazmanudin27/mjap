<div class="container-fluid p-0">
    <div class="row">
        <div class="col-6">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fa fa-wallet"></i> Kartu Piutang</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('cetakKartuPiutang') }}" method="POST" target="_blank"
                        autocomplete="off">
                        @csrf

                        <div class="row g-2 mt-3">
                            <div class="col-md-6">
                                <input type="date" class="form-control form-control-sm" id="tanggal_dari"
                                    name="tanggal_dari" placeholder="Tanggal Dari"
                                    value="{{ request('tanggal_dari') }}" required>
                            </div>
                            <div class="col-md-6">
                                <input type="date" class="form-control form-control-sm" id="tanggal_sampai"
                                    name="tanggal_sampai" placeholder="Tanggal Sampai"
                                    value="{{ request('tanggal_sampai') }}" required>
                            </div>
                        </div>

                        {{-- Pelanggan --}}
                        <div class="row mt-3">
                            <div class="col-md-12">
                                <select name="kode_pelanggan" class="form-select2 form-select-sm">
                                    <option value="">-- Semua Pelanggan --</option>
                                    @foreach($pelanggan as $p)
                                        <option value="{{ $p->kode_pelanggan }}"
                                            {{ request('kode_pelanggan') == $p->kode_pelanggan ? 'selected' : '' }}>
                                            {{ $p->nama_pelanggan }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-12">
                                <select name="status_tempo" class="form-select form-select-sm">
                                    <option value="1" {{ request('status_tempo') == '1' ? 'selected' : '' }}>Sudah Jatuh Tempo > 30 Hari</option>
                                    <option value="2" {{ request('status_tempo') == '2' ? 'selected' : '' }}>Belum Jatuh Tempo</option>
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
