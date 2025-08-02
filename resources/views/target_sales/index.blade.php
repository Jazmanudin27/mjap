@extends('layouts.template')
@section('titlepage', 'Data Target Sales')

@section('contents')
    <div class="container-fluid p-0">
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm border-0 rounded-4">
                    <div
                        class="card-header bg-primary text-white rounded-top-4 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-bold">
                            <i class="bi bi-flag me-2"></i> Data Target Sales
                        </h5>
                        <button class="btn btn-light btn-sm text-primary fw-semibold d-flex align-items-center gap-2"
                            data-bs-toggle="modal" data-bs-target="#modalTambahTarget">
                            <i class="bi bi-plus-circle"></i> <span>Get Target</span>
                        </button>
                    </div>

                    <div class="card-body mt-3">
                        @php
                            $bulanSekarang = request('bulan', now()->format('n'));
                            $tahunSekarang = request('tahun', now()->year);
                        @endphp

                        <form method="GET" action="{{ route('viewTargetSales') }}" class="mb-4">
                            <div class="row g-2">
                                {{-- <div class="col-md-6">
                                    <select name="nik" class="form-select2 form-select-sm">
                                        <option value="">-- Semua Sales --</option>
                                        @foreach ($karyawan as $k)
                                        <option value="{{ $k->nik }}" {{ request('nik')==$k->nik ? 'selected' : '' }}>
                                            {{ $k->nama_lengkap }} ({{ $k->nik }})
                                        </option>
                                        @endforeach
                                    </select>
                                </div> --}}

                                <div class="col-md-5">
                                    <select name="bulan" class="form-select form-select-sm">
                                        <option value="">-- Semua Bulan --</option>
                                        @for ($i = 1; $i <= 12; $i++)
                                            <option value="{{ $i }}" {{ $bulanSekarang == $i ? 'selected' : '' }}>
                                                {{ DateTime::createFromFormat('!m', $i)->format('F') }}
                                            </option>
                                        @endfor
                                    </select>
                                </div>

                                <div class="col-md-5">
                                    <select name="tahun" class="form-select form-select-sm">
                                        @for ($y = now()->year - 1; $y <= now()->year + 1; $y++)
                                            <option value="{{ $y }}" {{ $tahunSekarang == $y ? 'selected' : '' }}>{{ $y }}
                                            </option>
                                        @endfor
                                    </select>
                                </div>

                                <div class="col-md-2 d-grid">
                                    <button type="submit" class="btn btn-sm btn-primary w-100">
                                        <i class="bi bi-search me-1"></i> Filter
                                    </button>
                                </div>
                            </div>
                        </form>
                        {{-- Table --}}
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered table-hover align-middle mb-0">
                                <thead class="table-light text-center">
                                    <tr>
                                        <th style="width:3%">No</th>
                                        <th style="width:8%">Kode Sales</th>
                                        <th>Nama Sales</th>
                                        <th style="width:13%">OA</th>
                                        <th style="width:13%">EC</th>
                                        <th style="width:13%">Penjualan</th>
                                        <th style="width:13%">Tagihan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($targets as $index => $item)
                                        <tr>
                                            <td class="text-center">{{ $targets->firstItem() + $index }}</td>
                                            <td>{{ $item->kode_sales }}</td>
                                            <td>{{ $item->nama_lengkap ?? '-' }}</td>

                                            <td class="text-end">
                                                <input type="text" class="form-control form-control-sm text-end input-target"
                                                    value="{{ number_format($item->target_1, 0, ',', '.') }}"
                                                    data-id="{{ $item->id }}" data-field="target_1"
                                                    data-value="{{ $item->target_1 }}">
                                            </td>
                                            <td class="text-end">
                                                <input type="text" class="form-control form-control-sm text-end input-target"
                                                    value="{{ number_format($item->target_2, 0, ',', '.') }}"
                                                    data-id="{{ $item->id }}" data-field="target_2"
                                                    data-value="{{ $item->target_2 }}">
                                            </td>
                                            <td class="text-end">
                                                <input type="text" class="form-control form-control-sm text-end input-target"
                                                    value="{{ number_format($item->target_3, 0, ',', '.') }}"
                                                    data-id="{{ $item->id }}" data-field="target_3"
                                                    data-value="{{ $item->target_3 }}">
                                            </td>
                                            <td class="text-end">
                                                <input type="text" class="form-control form-control-sm text-end input-target"
                                                    value="{{ number_format($item->target_4, 0, ',', '.') }}"
                                                    data-id="{{ $item->id }}" data-field="target_4"
                                                    data-value="{{ $item->target_4 }}">
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="10" class="text-center text-muted">Tidak ada data target.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        {{-- Pagination --}}
                        <div class="mt-3">
                            {{ $targets->links('pagination::bootstrap-5') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal Tambah Target -->
    <div class="modal fade" id="modalTambahTarget" tabindex="-1" aria-labelledby="modalTambahTargetLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form action="{{ route('storeTargetSales') }}" method="POST" class="modal-content">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title text-primary" id="modalTambahTargetLabel">
                        <i class="bi bi-plus-circle me-1"></i> Pilih Bulan & Tahun
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Bulan</label>
                        <select name="bulan" class="form-select" required>
                            <option value="">-- Pilih Bulan --</option>
                            @for ($i = 1; $i <= 12; $i++)
                                <option value="{{ $i }}">{{ DateTime::createFromFormat('!m', $i)->format('F') }}</option>
                            @endfor
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Tahun</label>
                        <select name="tahun" class="form-select" required>
                            @php
                                $tahunIni = date('Y');
                                $tahunLalu = $tahunIni - 1;
                            @endphp
                            <option value="">-- Pilih Tahun --</option>
                            <option value="{{ $tahunLalu }}">{{ $tahunLalu }}</option>
                            <option value="{{ $tahunIni }}">{{ $tahunIni }}</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-arrow-right-circle me-1"></i> Lanjutkan
                    </button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Delete Confirmation --}}
    <script>
        // Format angka ke rupiah
        function formatRupiah(angka) {
            let number_string = angka.replace(/[^,\d]/g, '').toString(),
                split = number_string.split(','),
                sisa = split[0].length % 3,
                rupiah = split[0].substr(0, sisa),
                ribuan = split[0].substr(sisa).match(/\d{3}/gi);

            if (ribuan) {
                let separator = sisa ? '.' : '';
                rupiah += separator + ribuan.join('.');
            }

            rupiah = split[1] !== undefined ? rupiah + ',' + split[1] : rupiah;
            return rupiah;
        }

        // Hilangkan titik
        function unformatRupiah(str) {
            return str.replace(/\./g, '').replace(/[^0-9]/g, '');
        }

        $(function () {
            // Tombol hapus dengan SweetAlert
            $(document).on("click", ".delete", function (e) {
                e.preventDefault();
                const url = $(this).data('href');

                Swal.fire({
                    title: 'Hapus target?',
                    text: 'Data ini akan dihapus permanen.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    confirmButtonText: 'Hapus',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = url;
                    }
                });
            });

            // Format semua nilai rupiah saat pertama kali load
            $('.input-target').each(function () {
                const raw = $(this).data('value');
                $(this).val(formatRupiah(String(raw)));
            });

            // Saat input diketik
            $(document).on('input', '.input-target', function () {
                const val = $(this).val();
                $(this).val(formatRupiah(val));
            });

            // Saat value berubah dan focus keluar
            $(document).on('change', '.input-target', function () {
                const id = $(this).data('id');
                const field = $(this).data('field');
                const valFormatted = $(this).val();
                const valNumeric = unformatRupiah(valFormatted);
                $.ajax({
                    url: '{{ route('updateTargetSales') }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        id: id,
                        field: field,
                        value: valNumeric
                    },
                    success: function (res) {
                    },
                });
            });
        });
    </script>
@endsection
