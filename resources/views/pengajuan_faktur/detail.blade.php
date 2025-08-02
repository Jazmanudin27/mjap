@extends('layouts.template')
@section('titlepage', 'Edit Pengajuan Limit Kredit')

@section('contents')
    <div class="container-fluid p-0">
        <div class="col-12 col-xl-8 mx-auto">
            <div class="card shadow-sm border-0">
                <div class="card-body px-4 py-4">
                    <h4 class="text-center text-primary mb-4">Form Edit Pengajuan Limit Kredit</h4>
                    <form action="{{ route('updatePengajuanLimitKredit') }}" method="POST" autocomplete="off">
                        @csrf
                        @method('POST')

                        <input type="hidden" name="id" value="{{ $pengajuan->id }}">

                        <div class="row g-4">
                            {{-- Kolom Kiri --}}
                            <div class="col-md-6">
                                <h6 class="text-muted mb-2">Informasi Pelanggan</h6>

                                <div class="mb-2">
                                    <label class="form-label">Kode Pelanggan</label>
                                    <input type="text" name="kode_pelanggan" class="form-control form-control-sm"
                                        value="{{ $pengajuan->kode_pelanggan }}" readonly>
                                </div>

                                <div class="mb-2">
                                    <label class="form-label">NIK Penginput</label>
                                    <input type="text" name="nik" class="form-control form-control-sm"
                                        value="{{ $pengajuan->nik }}" readonly>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <h6 class="text-muted mb-2">Detail Pengajuan</h6>

                                <div class="mb-2">
                                    <label class="form-label">Nilai Pengajuan</label>
                                    <input type="text" name="nilai_pengajuan"
                                        class="form-control form-control-sm text-end format-rupiah"
                                        value="{{ number_format($pengajuan->nilai_pengajuan, 0, ',', '.') }}" required>
                                </div>

                                <div class="mb-2">
                                    <label class="form-label">Status</label>
                                    <select name="status" class="form-select form-select-sm" required>
                                        <option value="pending" {{ $pengajuan->status == 'pending' ? 'selected' : '' }}>
                                            Pending</option>
                                        <option value="disetujui" {{ $pengajuan->status == 'disetujui' ? 'selected' : '' }}>
                                            Disetujui</option>
                                        <option value="ditolak" {{ $pengajuan->status == 'ditolak' ? 'selected' : '' }}>
                                            Ditolak</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="mb-2">
                                    <label class="form-label">Alasan Pengajuan</label>
                                    <textarea name="alasan" class="form-control form-control-sm"
                                        rows="3">{{ $pengajuan->alasan }}</textarea>
                                </div>
                            </div>

                            {{-- Tombol Simpan --}}
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary w-100 mt-2">Update Pengajuan</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Format Rupiah --}}
    <script>
        $(document).on('input', '.format-rupiah', function () {
            let val = $(this).val().replace(/[^,\d]/g, '').toString();
            let split = val.split(',');
            let sisa = split[0].length % 3;
            let rupiah = split[0].substr(0, sisa);
            let ribuan = split[0].substr(sisa).match(/\d{3}/gi);

            if (ribuan) {
                let separator = sisa ? '.' : '';
                rupiah += separator + ribuan.join('.');
            }

            rupiah = split[1] !== undefined ? rupiah + ',' + split[1] : rupiah;
            $(this).val(rupiah);
        });
    </script>
@endsection
