@extends('mobile.layout')
@section('title', 'Pengajuan Limit Faktur')
@section('header', 'Tambah Pengajuan')

@section('content')
    <div class="container py-3">
        <form action="{{ route('storePengajuanFakturMobile') }}" method="POST" autocomplete="off">
            @csrf
            <div class="card border-0 shadow rounded-4 mb-4">
                {{-- HEADER FORM --}}
                <div class="card-header bg-primary text-white rounded-top py-3">
                    <h5 class="mb-0">
                        <i class="fa fa-file-invoice-dollar me-2"></i> Form Pengajuan Limit Faktur
                    </h5>
                </div>

                <div class="card-body p-4">
                    {{-- KODE PELANGGAN --}}
                    <div class="mb-3" hidden>
                        <label class="form-label fw-semibold">Kode Pelanggan</label>
                        <input type="text" name="kode_pelanggan" value="{{ request()->segment(3) }}"
                            class="form-control form-control-sm shadow-sm" required>
                    </div>

                    {{-- CREATED BY --}}
                    <div class="mb-3" hidden>
                        <label class="form-label fw-semibold">NIK Penginput</label>
                        <input type="text" name="created_by" class="form-control form-control-sm shadow-sm"
                            value="{{ auth()->user()->nik ?? '' }}" readonly>
                    </div>

                    {{-- ALASAN --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Alasan Pengajuan</label>
                        <textarea name="alasan" class="form-control form-control-sm shadow-sm" rows="3"
                            placeholder="Contoh: Butuh tambahan jumlah faktur untuk transaksi rutin" required></textarea>
                    </div>

                    {{-- JUMLAH FAKTUR --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Jumlah Faktur yang Diajukan</label>
                        <input type="number" name="jumlah_faktur" class="form-control form-control-sm shadow-sm text-end"
                            placeholder="0" min="1" required>
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
@endsection
