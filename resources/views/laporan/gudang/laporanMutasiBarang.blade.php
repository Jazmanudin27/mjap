<div class="row">
    <div class="col-md-12">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fa fa-random me-2"></i> Laporan Mutasi Barang</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('cetakLaporanMutasiBarang') }}" method="POST" autocomplete="off" target="_blank">
                    @csrf
                    <div class="row g-2 mt-2">
                        {{-- Pilih Barang --}}
                        <div class="col-md-12">
                            <select name="kode_barang" class="form-select form-select-sm select2">
                                <option value="">-- Semua Barang --</option>
                                @foreach($barangs as $b)
                                    <option value="{{ $b->kode_barang }}" {{ request('kode_barang') == $b->kode_barang ? 'selected' : '' }}>
                                        {{ $b->kode_barang }} - {{ $b->nama_barang }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Pilih Supplier --}}
                        <div class="col-md-12">
                            <select name="kode_supplier" class="form-select form-select-sm select2">
                                <option value="">-- Semua Supplier --</option>
                                @foreach($suppliers as $s)
                                    <option value="{{ $s->kode_supplier }}" {{ request('kode_supplier') == $s->kode_supplier ? 'selected' : '' }}>
                                        {{ $s->kode_supplier }} - {{ $s->nama_supplier }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Jenis Mutasi --}}
                        <div class="col-md-12">
                            <select name="jenis" class="form-select form-select-sm">
                                <option value="">-- Semua Jenis Mutasi --</option>
                                <option value="Masuk" {{ request('jenis') == 'Masuk' ? 'selected' : '' }}>Barang Masuk
                                </option>
                                <option value="Keluar" {{ request('jenis') == 'Keluar' ? 'selected' : '' }}>Barang Keluar
                                </option>
                            </select>
                        </div>

                        {{-- Tanggal --}}
                        <div class="col-md-6">
                            <input type="date" name="mulai" class="form-control form-control-sm"
                                value="{{ request('mulai') ?? date('Y-m-01') }}">
                        </div>
                        <div class="col-md-6">
                            <input type="date" name="akhir" class="form-control form-control-sm"
                                value="{{ request('akhir') ?? date('Y-m-d') }}">
                        </div>
                    </div>

                    {{-- Tombol Aksi --}}
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
