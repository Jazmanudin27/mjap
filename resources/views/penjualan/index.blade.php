@extends('layouts.template')
@section('titlepage', 'Data Penjualan')
@section('contents')
    <div class="container-fluid p-0">
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm border-0 rounded-4">
                    <div
                        class="card-header bg-primary text-white rounded-top-4 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-bold"><i class="fa fa-shopping-cart me-2"></i> Data Penjualan</h5>
                        @if (isset($TambahPenjualan) && $TambahPenjualan)
                            <a href="{{ route('tambahPenjualan') }}"
                                class="btn btn-light btn-sm text-primary fw-semibold d-flex align-items-center gap-2">
                                <i class="fa fa-plus-circle"></i> <span>Input Penjualan</span>
                            </a>
                        @endif
                    </div>
                    <div class="card-body mt-3">
                        <form method="GET" action="{{ route('viewPenjualan') }}" class="mb-4">
                            <div class="row g-2">
                                <div class="col-md-6">
                                    <div class="custom-datepicker-wrapper">
                                        <input type="text" class="form-control form-control-sm datepicker-input"
                                            id="tanggal_dari" name="tanggal_dari" placeholder="Tanggal Dari" readonly
                                            value="{{ request('tanggal_dari') }}">
                                        <div class="custom-calendar" id="calendar-dari"></div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="custom-datepicker-wrapper">
                                        <input type="text" class="form-control form-control-sm datepicker-input"
                                            id="tanggal_sampai" name="tanggal_sampai" placeholder="Tanggal Sampai" readonly
                                            value="{{ request('tanggal_sampai') }}">
                                        <div class="custom-calendar" id="calendar-sampai"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="row g-2 mt-1">
                                <div class="col-md-2">
                                    <input type="text" name="no_faktur" class="form-control form-control-sm"
                                        placeholder="No Faktur" value="{{ request('no_faktur') }}">
                                </div>
                                <div class="col-md-2">
                                    <input type="text" name="kode_pelanggan" class="form-control form-control-sm"
                                        placeholder="Kode Pelanggan" value="{{ request('kode_pelanggan') }}">
                                </div>
                                <div class="col-md-3">
                                    <input type="text" name="nama_pelanggan" class="form-control form-control-sm"
                                        placeholder="Nama Pelanggan" value="{{ request('nama_pelanggan') }}">
                                </div>
                                <div class="col-md-2">
                                    <select name="kode_sales" class="form-select2 form-select-sm">
                                        <option value="">Semua Sales</option>
                                        @foreach ($sales as $s)
                                            <option value="{{ $s->nik }}"
                                                {{ request('kode_sales') == $s->nik ? 'selected' : '' }}>
                                                {{ $s->nama_lengkap }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <select name="jenis_transaksi" class="form-select2 form-select-sm">
                                        <option value="">Semua JT</option>
                                        <option value="T" {{ request('jenis_transaksi') == 'T' ? 'selected' : '' }}>
                                            Tunai</option>
                                        <option value="K" {{ request('jenis_transaksi') == 'K' ? 'selected' : '' }}>
                                            Kredit
                                        </option>
                                    </select>
                                </div>
                                <div class="col-md-1 d-grid">
                                    <button type="submit" class="btn btn-primary btn-sm">Filter</button>
                                </div>
                            </div>
                        </form>

                        <div class="table-responsive">
                            <table class="table table-bordered table-hover table-sm align-middle mb-0" style="zoom: 93%">
                                <thead class="table-light text-center">
                                    <tr>
                                        <th style="width: 2%">No</th>
                                        <th style="width: 8%">Tanggal</th>
                                        <th style="width: 9%">Kirim</th>
                                        <th style="width: 12%">No Faktur</th>
                                        <th>Nama Pelanggan</th>
                                        <th style="width: 12%">Nama Sales</th>
                                        <th class="text-end" style="width: 10%">Total</th>
                                        <th class="text-center" style="width: 4%;">JT</th>
                                        <th class="text-center" style="width: 10%;">Status</th>
                                        <th class="text-center" style="width: 16%;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($penjualan as $i => $p)
                                        @php $selisih = $p->grand_total - $p->jumlah_bayar; @endphp
                                        <tr class="{{ $p->batal == '1' ? 'table-danger' : '' }}">
                                            <td class="text-center">{{ $penjualan->firstItem() + $i }}</td>
                                            <td>{{ tanggal_indo2($p->tanggal) }}</td>
                                            @php
                                                $label =
                                                    $p->batal == '1'
                                                        ? 'Batal'
                                                        : ($p->tanggal_kirim
                                                            ? tanggal_indo2($p->tanggal_kirim)
                                                            : 'Outstanding');
                                                $btnClass =
                                                    $p->batal == '1'
                                                        ? 'btn-danger'
                                                        : ($p->tanggal_kirim
                                                            ? 'btn-success'
                                                            : 'btn-warning');
                                            @endphp
                                            <td class="text-center">
                                                <a href="#" class="btn btn-sm {{ $btnClass }}">
                                                    {{ $label }}
                                                </a>
                                            </td>
                                            <td>{{ $p->no_faktur }}</td>
                                            <td>{{ $p->nama_pelanggan }}</td>
                                            <td>{{ $p->nama_lengkap }}</td>
                                            <td class="text-end">{{ rupiah($p->grand_total) }}</td>
                                            <td class="text-center">
                                                <a
                                                    class="btn btn-sm {{ $p->jenis_transaksi == 'T' ? 'btn-primary' : 'btn-warning' }}">{{ $p->jenis_transaksi }}</a>
                                            </td>
                                            <td class="text-center">
                                                @if ($p->batal == '1')
                                                    <a href="#" class="btn btn-danger btn-sm showAlasanBatal"
                                                        data-alasan="{{ $p->alasan_batal }}">Batal</a>
                                                @else
                                                    <a
                                                        class="btn btn-sm {{ $selisih <= 0 ? 'btn-success' : 'btn-warning' }}">
                                                        {{ $selisih <= 0 ? 'Lunas' : 'Belum Lunas' }}
                                                    </a>
                                                @endif
                                            </td>
                                            <td class="text-start">
                                                <a href="{{ route('detailPenjualan', $p->no_faktur) }}"
                                                    class="btn btn-sm btn-success">
                                                    <i class="fa fa-list"></i>
                                                </a>

                                                <a href="{{ route('cetakFaktur1', $p->no_faktur) }}"
                                                    class="btn btn-sm btn-primary position-relative" target="_blank">
                                                    <i class="fa fa-print"></i>
                                                    @if ($p->cetak > 0)
                                                        <span
                                                            class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                                            {{ $p->cetak }}
                                                        </span>
                                                    @endif
                                                </a>

                                                {{-- @if ($p->batal != '1' && !$p->tanggal_kirim) --}}
                                                    <a href="#" class="btn btn-sm btn-danger batalPenjualan"
                                                        data-no="{{ $p->no_faktur }}">
                                                        <i class="fa fa-ban"></i>
                                                    </a>
                                                    <a href="#" class="btn btn-sm btn-danger deletePenjualan"
                                                        data-href="{{ route('deletePenjualan', $p->no_faktur) }}">
                                                        <i class="fa fa-trash"></i>
                                                    </a>
                                                    <a href="{{ route('editPenjualan', $p->no_faktur) }}?{{ request()->getQueryString() }}"
                                                        class="btn btn-sm btn-warning">
                                                        <i class="fa fa-pencil"></i>
                                                    </a>
                                                {{-- @endif --}}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="10" class="text-center text-muted">Tidak ada data.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-3">
                            {{ $penjualan->withQueryString()->links('pagination::bootstrap-5') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(function() {
            $(document).on('click', '.deletePenjualan', function(e) {
                e.preventDefault();
                const url = $(this).data('href');
                Swal.fire({
                    title: 'Hapus Penjualan?',
                    text: 'Data dan detail produk akan terhapus permanen.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    confirmButtonText: 'Hapus',
                    cancelButtonText: 'Batal'
                }).then(r => {
                    if (r.isConfirmed) window.location.href = url;
                });
            });

            $(document).on('click', '.showAlasanBatal', function(e) {
                e.preventDefault();
                const alasan = $(this).data('alasan');
                Swal.fire({
                    title: 'Alasan Pembatalan',
                    text: alasan,
                    icon: 'info',
                    confirmButtonText: 'Tutup'
                });
            });

            $(document).on("click", ".batalPenjualan", function(e) {
                e.preventDefault();
                const noFaktur = $(this).data('no');

                Swal.fire({
                    title: 'Batalkan Faktur?',
                    input: 'text',
                    inputLabel: 'Alasan Pembatalan',
                    inputPlaceholder: 'Contoh: Salah input barang',
                    inputAttributes: {
                        autocomplete: 'off'
                    },
                    inputValidator: (value) => {
                        if (!value) {
                            return 'Alasan wajib diisi!';
                        }
                    },
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Batalkan',
                    cancelButtonText: 'Batal',
                    preConfirm: (alasan) => {
                        return fetch(`/batalFaktur/${noFaktur}`, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr(
                                    'content'),
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({
                                alasan
                            })
                        }).then(response => {
                            if (!response.ok) {
                                throw new Error('Gagal membatalkan!');
                            }
                            return response.json();
                        });
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire('Berhasil', 'Faktur dibatalkan.', 'success').then(() => {
                            location.reload();
                        });
                    }
                });
            });
        });
    </script>
@endsection
