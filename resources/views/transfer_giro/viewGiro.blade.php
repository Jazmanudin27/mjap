@extends('layouts.template')
@section('titlepage', 'Data Transfer Pelanggan')

@section('contents')
    <div class="container-fluid p-0">
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm border-0 rounded-4">
                    <div class="card-header bg-primary text-white rounded-top-4 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-bold">
                            <i class="bi bi-wallet2 me-2"></i> Data Giro
                        </h5>
                        {{-- Tambahkan tombol jika diperlukan di sini --}}
                    </div>

                    <div class="card-body mt-3">
                     <form method="GET" action="{{ route('viewGiro') }}" class="row g-2 mb-3">
                        {{-- Hapus input hidden tab --}}
                        <div class="row mt-2">
                            <div class="col-md-6">
                                <input type="date" name="tanggal_dari" value="{{ request('tanggal_dari') }}"
                                    class="form-control form-control-sm" placeholder="Dari">
                            </div>
                            <div class="col-md-6">
                                <input type="date" name="tanggal_sampai" value="{{ request('tanggal_sampai') }}"
                                    class="form-control form-control-sm" placeholder="Sampai">
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-5">
                                <select id="kode_pelanggan" name="kode_pelanggan" class="form-select form-select-sm">
                                    @if(request('kode_pelanggan'))
                                        <option value="{{ request('kode_pelanggan') }}" selected>
                                            {{ request('kode_pelanggan') }} - (Dipilih)
                                        </option>
                                    @endif
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select name="status" class="form-select form-select-sm">
                                    <option value="">Semua Status</option>
                                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="disetujui" {{ request('status') == 'disetujui' ? 'selected' : '' }}>Disetujui</option>
                                    <option value="ditolak" {{ request('status') == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-sm btn-primary w-100">Filter</button>
                            </div>
                        </div>
                    </form>
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Tanggal</th>
                                        <th>No Faktur</th>
                                        <th>Kode Giro</th>
                                        <th>Pelanggan</th>
                                        <th>Sales</th>
                                        <th>Jumlah</th>
                                        <th>Status</th>
                                        <th>Tgl Cair</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($data as $row)
                                        <tr>
                                            <td>{{ $loop->iteration + ($data->firstItem() - 1) }}</td>
                                            <td>{{ tanggal_indo2($row->tanggal) }}</td>
                                            <td>{{ $row->no_faktur }}</td>
                                            <td>{{ $row->kode_giro }}</td>
                                            <td>{{ $row->kode_pelanggan }} - {{ $row->nama_pelanggan }}</td>
                                            <td>{{ $row->kode_sales }} - {{ $row->nama_sales }}</td>
                                            <td class="text-end">{{ number_format($row->jumlah, 0, ',', '.') }}</td>
                                            <td>
                                                @if ($row->status == 'pending')
                                                    <span class="btn btn-sm btn-warning">Pending</span>
                                                @elseif ($row->status == 'ditolak')
                                                    <span class="btn btn-sm btn-danger">
                                                        {{ $row->tanggal_cair ? tanggal_indo2($row->tanggal_cair) : '-' }}
                                                    </span>
                                                @elseif ($row->status == 'disetujui')
                                                    <span class="btn btn-sm btn-success">
                                                        {{ $row->tanggal_cair ? tanggal_indo2($row->tanggal_cair) : '-' }}
                                                    </span>
                                                @else
                                                    <span class="btn btn-sm btn-secondary">-</span>
                                                @endif
                                            </td>

                                            <td>{{ $row->tanggal_cair ? tanggal_indo2($row->tanggal_cair) : '-' }}</td>
                                            <td>
                                                @if ($row->status == 'pending')
                                                    <div class="d-flex justify-content-center gap-1">
                                                        <button class="btn btn-sm btn-success verifikasi-btn" data-kode="{{ $row->kode_giro }}" data-tab="giro" data-aksi="setuju"
                                                            data-route="{{ route('verifikasiPembayaran', ['kode' => 'KODE_REPLACE']) }}">
                                                            ✔
                                                        </button>
                                                        <button class="btn btn-sm btn-danger verifikasi-btn" data-kode="{{ $row->kode_giro }}"
                                                            data-tab="giro" data-aksi="tolak"
                                                            data-route="{{ route('verifikasiPembayaran', ['kode' => 'KODE_REPLACE']) }}">
                                                            ✘
                                                        </button>
                                                    </div>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="10" class="text-center">Tidak ada data</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-2">
                            {{ $data->withQueryString()->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(function () {

            $('.verifikasi-btn').click(function (e) {
                e.preventDefault();

                const kode = $(this).data('kode');
                const tab = $(this).data('tab');
                const aksi = $(this).data('aksi');
                const tipe = $(this).data('tipe'); // misal: 'giro' atau 'transfer' (pastikan ada data-tipe di tombol)
                const teks = aksi === 'setuju' ? 'menyetujui' : 'menolak';
                const statusText = aksi === 'setuju' ? 'disetujui' : 'ditolak';
                const baseUrl = $(this).data('route').replace('KODE_REPLACE', kode);
                const fullUrl = `${baseUrl}?tab=${tab}`;

                const labelTanggal = (tipe === 'giro' && aksi === 'setuju') ? 'Tanggal Cair Giro' : 'Tanggal Verifikasi';
                const today = new Date().toISOString().split('T')[0];

                Swal.fire({
                    title: `Yakin ingin ${teks} pembayaran ini?`,
                    html: `
                        <label for="tanggal_input">${labelTanggal}:</label><br>
                        <input type="date" id="tanggal_input" class="swal2-input" value="${today}" required>
                    `,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: `Ya, ${statusText}`,
                    cancelButtonText: 'Batal',
                    confirmButtonColor: aksi === 'setuju' ? '#198754' : '#dc3545',
                    preConfirm: () => {
                        const tanggal = document.getElementById('tanggal_input').value;
                        if (!tanggal) {
                            Swal.showValidationMessage(`${labelTanggal} harus diisi!`);
                            return false;
                        }
                        return tanggal;
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        const tanggal = result.value;

                        const form = $('<form>', {
                            method: 'POST',
                            action: fullUrl
                        });

                        form.append($('<input>', {
                            type: 'hidden',
                            name: '_token',
                            value: '{{ csrf_token() }}'
                        }));

                        form.append($('<input>', {
                            type: 'hidden',
                            name: '_method',
                            value: 'PATCH'
                        }));

                        form.append($('<input>', {
                            type: 'hidden',
                            name: 'aksi',
                            value: aksi
                        }));

                        form.append($('<input>', {
                            type: 'hidden',
                            name: 'tanggal',
                            value: tanggal
                        }));

                        $('body').append(form);
                        form.submit();
                    }
                });
            });

            $('#kode_pelanggan').select2({
                placeholder: 'Cari pelanggan…',
                dropdownParent: $('#kode_pelanggan').parent(),
                ajax: {
                    url: "{{ route('getPelanggan') }}",
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return {
                            kode_pelanggan: params.term
                        };
                    },
                    processResults: function (data) {
                        return {
                            results: data.results
                        };
                    },
                    cache: true
                }
            });

        });
    </script>
@endsection
