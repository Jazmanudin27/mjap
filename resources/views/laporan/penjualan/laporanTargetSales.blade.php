<div class="row">
    <div class="col-6">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fa fa-bullseye me-1"></i> Laporan Target Sales</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('cetakTargetSales') }}" method="POST" autocomplete="off" target="_blank">
                    @csrf
                    <div class="row g-2 mt-2">
                        <div class="col-md-6">
                            <label for="bulan" class="form-label mb-0">Bulan</label>
                            <select name="bulan" id="bulan" class="form-select2 form-select-sm">
                                @for ($i = 1; $i <= 12; $i++)
                                    <option value="{{ $i }}" {{ request('bulan') == $i ? 'selected' : '' }}>
                                        {{ bulan_indo($i) }}
                                    </option>
                                @endfor
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="tahun" class="form-label mb-0">Tahun</label>
                            <select name="tahun" id="tahun" class="form-select2 form-select-sm">
                                @for ($y = 2025; $y <= date('Y'); $y++)
                                    <option value="{{ $y }}" {{ request('tahun') == $y ? 'selected' : '' }}>
                                        {{ $y }}
                                    </option>
                                @endfor
                            </select>
                        </div>
                    </div>
                    <div class="row g-2 mt-2">
                        <div class="col-md-12">
                            <label for="bulan" class="form-label mb-0">Team</label>
                            <select name="team" id="team" class="form-select2 form-select-sm">
                                <option value="">Semua Team</option>
                                <option value="25.01.004">Team Danil</option>
                                <option value="25.01.006">Team Dadang</option>
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
