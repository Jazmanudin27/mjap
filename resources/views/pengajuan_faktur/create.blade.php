@extends('layouts.template')
@section('titlepage', 'Tambah Pengajuan Limit Kredit')

@section('contents')
    <div class="container-fluid p-0">
        <div class="col-5">
            <div class="card shadow-lg border-0 rounded-4 mt-3">
                <div class="card-header bg-primary text-white rounded-top">
                    <h4 class="text-start mb-0">
                        <i class="fa fa-plus-circle me-2"></i> Form Pengajuan Limit Kredit
                    </h4>
                </div>
                <div class="card-body p-3">
                    <form action="{{ route('storePengajuanLimit') }}" method="POST" autocomplete="off">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">
                                    Kode Pelanggan
                                </label>
                                <input type="text" name="kode_pelanggan" class="form-control form-control-sm"
                                    placeholder="Contoh: CUST001" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">
                                    NIK Penginput
                                </label>
                                <input type="text" name="nik" class="form-control form-control-sm"
                                    placeholder="NIK Anda" value="{{ auth()->user()->nik ?? '' }}" readonly>
                            </div>

                            <div class="col-md-12">
                                <label class="form-label fw-semibold">
                                    Alasan Pengajuan
                                </label>
                                <textarea name="alasan" class="form-control form-control-sm" rows="3"
                                    placeholder="Contoh: Perlu tambahan limit karena volume pembelian meningkat." required></textarea>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">
                                    Nilai Pengajuan
                                </label>
                                <input type="text" name="nilai_pengajuan"
                                    class="form-control form-control-sm format-rupiah text-end" placeholder="Rp 0" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">
                                    Status
                                </label>
                                <select name="status" class="form-select form-select-sm" required>
                                    <option value="pending" selected>Pending</option>
                                    <option value="disetujui">Disetujui</option>
                                    <option value="ditolak">Ditolak</option>
                                </select>
                            </div>

                            <div class="col-12 mt-3">
                                <button type="submit" class="btn btn-primary w-100 py-2">
                                    <i class="fa fa-save me-1"></i> Simpan Pengajuan
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Format angka rupiah --}}
    <script>
        $(document).on('input', '.format-rupiah', function() {
            let val = $(this).val().replace(/[^,\d]/g, '');
            let split = val.split(',');
            let sisa = split[0].length % 3;
            let rupiah = split[0].substr(0, sisa);
            let ribuan = split[0].substr(sisa).match(/\d{3}/gi);

            if (ribuan) {
                let separator = sisa ? '.' : '';
                rupiah += separator + ribuan.join('.');
            }

            rupiah = split[1] !== undefined ? rupiah + ',' + split[1] : rupiah;
            $(this).val('Rp ' + rupiah);
        });
    </script>
@endsection
