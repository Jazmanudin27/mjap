@extends('mobile.layout')
@section('title', 'Data Barang')
@section('header', 'Data Barang')

@section('content')
    {{-- Header --}}
    <div class="px-3 py-4 text-white"
        style="background: linear-gradient(135deg, #0d6efd, #0a58ca);
               box-shadow: 0 4px 15px rgba(0,0,0,0.25);
               border-bottom-left-radius: 1.5rem;
               border-bottom-right-radius: 1.5rem;">
        <div class="d-flex align-items-center">
            <div class="ms-3">
                <div class="fw-bold fs-5">📦 Daftar Barang</div>
                <small class="text-white-50">Lihat & kelola data barang</small>
            </div>
        </div>
    </div>

    <div class="container py-3">
        <form method="GET" action="{{ route('viewBarangMobile') }}" class="mb-3">
            <div class="input-group shadow-sm rounded-pill overflow-hidden">
                <input type="text" name="nama_barang" value="{{ request('nama_barang') }}" class="form-control border-0"
                    placeholder="🔍 Cari nama barang...">
                <button class="btn btn-primary px-3"><i class="fa fa-search"></i></button>
            </div>
            <div class="input-group shadow-sm rounded-pill overflow-hidden mt-2">
                <select name="kode_supplier" class="form-select">
                    <option value="">-- Semua Supplier --</option>
                    @foreach ($suppliers as $sup)
                        <option value="{{ $sup->kode_supplier }}"
                            {{ request('kode_supplier') == $sup->kode_supplier ? 'selected' : '' }}>
                            {{ $sup->nama_supplier }}
                        </option>
                    @endforeach
                </select>
            </div>
        </form>

        {{-- List Barang --}}
        @foreach ($barangs as $b)
            <div class="card mb-3 shadow-sm border-0 rounded-4"
                style="background: rgba(255,255,255,0.85); backdrop-filter: blur(6px); transition: transform 0.2s;">
                <div class="card-body small">

                    {{-- Nama Barang --}}
                    <h6 class="fw-bold text-primary mb-1">
                        <i class="fa fa-cube me-1 text-info"></i> {{ $b->nama_barang }}
                    </h6>

                    {{-- Kode & Supplier --}}
                    <div class="text-muted small mb-2">
                        <i class="fa fa-barcode me-1 text-secondary"></i> {{ $b->kode_barang }} <br>
                        <i class="fa fa-industry me-1 text-warning"></i> {{ $b->nama_supplier }}
                    </div>

                    {{-- Satuan --}}
                    <div class="mb-2">
                        <small class="text-muted fw-bold">Satuan & Harga:</small>
                        <ul class="mb-0 ps-3">
                            @foreach ($b->satuan_list as $sat)
                                <li>
                                    <span class="badge bg-light text-dark border shadow-sm">
                                        {{ $sat->satuan }}
                                    </span>
                                    <span class="ms-1 text-muted">isi {{ $sat->isi }}</span>
                                    <span class="text-success fw-bold">Rp
                                        {{ number_format($sat->harga_jual, 0, ',', '.') }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    {{-- Stok --}}
                    <div class="mt-2">
                        <small class="text-muted fw-bold">📊 Stok Tersedia:</small><br>
                        {!! $b->stok_html !!}
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <style>
        .card:hover {
            transform: scale(1.02);
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.15);
        }
    </style>
@endsection
