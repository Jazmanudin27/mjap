@extends('layouts.template')
@section('titlepage', 'Data Transfer Pelanggan')

@section('contents')
    <div class="container-fluid p-0">
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm border-0 rounded-4">
                    <div
                        class="card-header bg-primary text-white rounded-top-4 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-bold">
                            <i class="bi bi-arrow-left-right me-2"></i> Data Transfer
                        </h5>
                    </div>


                    <div class="card-body mt-3">
                        <form method="GET" action="{{ route('viewTransfer') }}" class="row g-2 mb-3">
                            <input type="hidden" name="tab" value="{{ request('tab', 'transfer') }}">
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
                                    <select id="kode_pelanggan" name="kode_pelanggan"
                                        class="form-select form-select-sm"></select>
                                </div>
                                <div class="col-md-3">
                                    <select name="status" class="form-select form-select-sm">
                                        <option value="">Semua Status</option>
                                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>
                                            Pending
                                        </option>
                                        <option value="disetujui" {{ request('status') == 'disetujui' ? 'selected' : '' }}>
                                            Disetujui
                                        </option>
                                        <option value="ditolak" {{ request('status') == 'ditolak' ? 'selected' : '' }}>
                                            Ditolak
                                        </option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <button type="submit" class="btn btn-sm btn-primary w-100">Filter</button>
                                </div>
                            </div>
                        </form>

                        <div class="table-responsive">
                            <table class="table table-bordered table-sm">
                                <thead class="table-primary">
                                    <tr>
                                        <th>#</th>
                                        <th>Tanggal</th>
                                        <th>No Faktur</th>
                                        <th>Kode Transfer</th>
                                        <th>Pelanggan</th>
                                        <th>Sales</th>
                                        <th>Bank</th>
                                        <th>Jumlah</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($data as $row)
                                        <tr>
                                            <td>{{ $loop->iteration + ($data->firstItem() - 1) }}</td>
                                            <td>{{ tanggal_indo2($row->tanggal) }}</td>
                                            <td>{{ $row->no_faktur }}</td>
                                            <td>{{ $row->kode_transfer }}</td>
                                            <td>{{ $row->kode_pelanggan }} - {{ $row->nama_pelanggan }}</td>
                                            <td>{{ $row->kode_sales }} - {{ $row->nama_sales }}</td>
                                            <td>{{ $row->bank_pengirim }}</td>
                                            <td class="text-end">{{ number_format($row->jumlah, 0, ',', '.') }}</td>
                                            <td>
                                                @if ($row->status == 'pending')
                                                    <span class="btn btn-sm btn-warning">Pending</span>
                                                @elseif ($row->status == 'ditolak')
                                                    <span
                                                        class="btn btn-sm btn-danger">{{ $row->tanggal_diterima ? tanggal_indo2($row->tanggal_diterima) : '-' }}</span>
                                                @elseif ($row->status == 'disetujui')
                                                    <span
                                                        class="btn btn-sm btn-success">{{ $row->tanggal_diterima ? tanggal_indo2($row->tanggal_diterima) : '-' }}</span>
                                                @else
                                                    <span class="btn btn-sm btn-secondary">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($row->status == 'pending')
                                                    <div class="d-flex justify-content-center gap-1">
                                                        <button class="btn btn-sm btn-success verifikasi-btn"
                                                            data-kode="{{ $row->kode_transfer }}" data-tab="transfer"
                                                            data-aksi="setuju"
                                                            data-route="{{ route('verifikasiPembayaran', ['kode' => 'KODE_REPLACE']) }}">
                                                            ✔
                                                        </button>
                                                        <button class="btn btn-sm btn-danger verifikasi-btn"
                                                            data-kode="{{ $row->kode_transfer }}" data-tab="transfer"
                                                            data-aksi="tolak"
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
        $(function() {
            const bankList = @json($bankList);

            $('.verifikasi-btn').click(function(e) {
                e.preventDefault();

                const kode = $(this).data('kode');
                const tab = $(this).data('tab');
                const aksi = $(this).data('aksi');
                const teks = aksi === 'setuju' ? 'menyetujui' : 'menolak';
                const statusText = aksi === 'setuju' ? 'disetujui' : 'ditolak';
                const baseUrl = $(this).data('route').replace('KODE_REPLACE', kode);
                const fullUrl = `${baseUrl}?tab=${tab}`;

                Swal.fire({
                    title: `<strong>Konfirmasi Verifikasi</strong>`,
                    html: `
                        <div style="text-align: left;">
                            <label class="mt-2" for="tanggal_verifikasi" style="font-weight: 500;">📅 Tanggal Verifikasi:</label>
                            <input type="date" id="tanggal_verifikasi" value="{{ Date('Y-m-d') }}" class="form-control form-control-sm mt-2" style="width: 100%;" required>

                            <label class="mt-4" for="bank" style="font-weight: 500;">🏦 Pilih Bank:</label>
                            <select id="bank" class="form-select2 form-select-sm mt-2" style="width: 100%;" required>
                                <option value="">-- Pilih Bank --</option>
                            </select>
                        </div>
                    `,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: `✅ Ya, ${statusText}`,
                    cancelButtonText: '❌ Batal',
                    confirmButtonColor: aksi === 'setuju' ? '#198754' : '#dc3545',
                    showClass: {
                        popup: 'animate__animated animate__fadeInDown'
                    },
                    hideClass: {
                        popup: 'animate__animated animate__fadeOutUp'
                    },
                    didOpen: () => {
                        const bankSelect = document.getElementById('bank');
                        bankList.forEach(bank => {
                            const option = document.createElement('option');
                            option.value = bank.id;
                            option.text =
                                `${bank.nama_bank} - ${bank.no_rekening} a.n ${bank.atas_nama}`;
                            bankSelect.appendChild(option);
                        });
                    },
                    preConfirm: () => {
                        const tanggal = document.getElementById('tanggal_verifikasi').value;
                        const bankId = document.getElementById('bank').value;

                        if (!tanggal) {
                            Swal.showValidationMessage('📌 Tanggal harus diisi!');
                            return false;
                        }
                        if (!bankId) {
                            Swal.showValidationMessage('📌 Bank harus dipilih!');
                            return false;
                        }

                        return {
                            tanggal,
                            bank_id: bankId
                        };
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        const {
                            tanggal,
                            bank_id
                        } = result.value;

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
                        form.append($('<input>', {
                            type: 'hidden',
                            name: 'bank_id',
                            value: bank_id
                        }));

                        $('body').append(form);
                        form.submit();
                    }
                });
            });

            // select2 pelanggan
            $('#kode_pelanggan').select2({
                placeholder: 'Cari pelanggan…',
                dropdownParent: $('#kode_pelanggan').parent(),
                ajax: {
                    url: "{{ route('getPelanggan') }}",
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            kode_pelanggan: params.term
                        };
                    },
                    processResults: function(data) {
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
