@extends('mobile.layout')
@section('title', 'Pelanggan')
@section('header', 'Data Pelanggan')
@section('content')

    {{-- Logo --}}
    <div class="px-3 py-3 text-white"
        style="background: linear-gradient(135deg, #0d6efd, #0a58ca); box-shadow: 0 4px 12px rgba(0,0,0,0.15); border-bottom-left-radius: 1rem; border-bottom-right-radius: 1rem;">
        <div class="d-flex align-items-center">
            <div class="bg-white text-primary rounded-circle d-flex justify-content-center align-items-center shadow"
                style="width: 50px; height: 50px;">
                <i class="fa fa-users fa-lg"></i>
            </div>
            <div class="ms-3">
                <div class="fw-bold fs-5">Data Pelanggan</div>
            </div>
        </div>
    </div>
    <div class="container py-3">
        <a href="{{ route('createPelangganMobile') }}" class="btn btn-primary rounded-circle shadow position-fixed"
            style="bottom: 80px; right: 20px; z-index: 1050; width: 55px; height: 55px; display: flex; align-items: center; justify-content: center;">
            <i class="bi bi-plus fs-4"></i>
        </a>
        {{-- Form Pencarian --}}
        <form method="GET" action="{{ route('viewPelangganMobile') }}" class="mb-3" autocomplete="off">
            <div class="input-group mb-2 shadow-sm rounded">
                <span class="input-group-text bg-light"><i class="bi bi-search"></i></span>
                <input type="text" name="search" class="form-control" value="{{ request('search') }}"
                    placeholder="Cari nama pelanggan...">
                <button class="btn btn-outline-secondary" type="submit">
                    <i class="bi bi-arrow-right-circle"></i>
                </button>
            </div>

            <select name="wilayah" class="form-select form-select-sm mb-2 shadow-sm" onchange="this.form.submit()">
                <option value="">🌍 Semua Wilayah</option>
                @foreach ($wilayahList as $kode => $nama)
                    <option value="{{ $kode }}" {{ request('wilayah') == $kode ? 'selected' : '' }}>
                        {{ $nama }}
                    </option>
                @endforeach
            </select>
        </form>

        {{-- List Pelanggan --}}
        @forelse($pelanggan as $p)
            <a href="{{ route('viewDetailPelangganMobile', $p->kode_pelanggan) }}" class="text-decoration-none">
                <div class="card shadow-sm border-0 rounded-4 mb-1">
                    <div class="card-body py-2 px-3 d-flex align-items-center">
                        {{-- Info --}}
                        <div class="flex-grow-1">
                            <h6 class="mb-1 fw-bold text-primary">
                                {{ $p->nama_pelanggan }}
                            </h6>
                            <div class="small text-muted">
                                <i class="bi bi-geo-alt me-1"></i> {{ $p->alamat_toko ?? '-' }}<br>
                                <i class="bi bi-phone me-1"></i> {{ $p->no_hp_pelanggan ?? '-' }}
                            </div>
                        </div>

                        <div class="text-end">
                            <span class="badge bg-light border text-dark small d-block mb-1">
                                {{ $p->kode_pelanggan }}
                            </span>
                            <span class="badge {{ $p->status == '1' ? 'bg-success' : 'bg-danger' }} small">
                                <i class="bi {{ $p->status == '1' ? 'bi-check-circle' : 'bi-x-circle' }} me-1"></i>
                                {{ $p->status == '1' ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </div>
                    </div>
                </div>
            </a>
        @empty
            <div class="alert alert-warning text-center rounded-4">
                <i class="bi bi-emoji-frown fs-4 d-block mb-1"></i>
                Tidak ada data pelanggan ditemukan.
            </div>
        @endforelse

    </div>
@endsection
