@extends('mobile.layout')
@section('title', 'Pengajuan Limit Kredit')
@section('header', 'Tambah Pengajuan')

@section('content')
    <div class="container py-3">
        <form action="{{ route('storePengajuanLimitMobile') }}" method="POST" autocomplete="off">
            @csrf
            <div class="card border-0 shadow rounded-4 mb-4">
                {{-- HEADER FORM --}}
                <div class="card-header bg-primary text-white rounded-top py-3">
                    <h5 class="mb-0">
                        <i class="fa fa-credit-card me-2"></i> Form Pengajuan Limit Kredit
                    </h5>
                </div>

                <div class="card-body p-4">
                    {{-- KODE PELANGGAN --}}
                    <div class="mb-3" hidden>
                        <label class="form-label fw-semibold">Kode Pelanggan</label>
                        <input type="text" name="kode_pelanggan" value="{{ request()->segment(3) }}"
                            class="form-control form-control-sm shadow-sm" placeholder="Pelanggan" required>
                    </div>

                    {{-- NIK --}}
                    <div class="mb-3" hidden>
                        <label class="form-label fw-semibold">NIK Penginput</label>
                        <input type="text" name="nik" class="form-control form-control-sm shadow-sm"
                            value="{{ auth()->user()->nik ?? '' }}" readonly>
                    </div>

                    {{-- ALASAN --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Alasan Pengajuan</label>
                        <textarea name="alasan" class="form-control form-control-sm shadow-sm" rows="3"
                            placeholder="Contoh: Volume pembelian meningkat" required></textarea>
                    </div>

                    {{-- NILAI --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nilai Pengajuan</label>
                        <input type="text" name="nilai_pengajuan"
                            class="form-control form-control-sm format-rupiah shadow-sm text-end" placeholder="Rp 0"
                            required>
                    </div>

                    {{-- SUBMIT --}}
                    <div class="d-grid mt-4">
                        <button type="submit" class="btn btn-sm btn-primary rounded-pill">
                            <i class="fa fa-save me-1"></i> Simpan Pengajuan
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    {{-- Format Rupiah --}}
    <script>
        $(document).ready(function() {

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
        });
    </script>
@endsection
