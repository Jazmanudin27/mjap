@extends('layouts.template')
@section('titlepage', 'Data Saldo Awal')
@section('contents')
    <style>
        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .table td,
        .table th {
            white-space: nowrap;
        }

        .satuan-input {
            font-size: 1rem;
            padding: 0.375rem 0.5rem;
            min-width: 70px;
        }

        @media (max-width: 576px) {
            .satuan-input {
                font-size: 1.1rem;
                min-width: 100px;
            }
        }
    </style>
    <div class="container-fluid p-0">
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm border-0 rounded-4">
                    <div
                        class="card-header bg-primary text-white rounded-top-4 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-bold"><i class="fa fa-wallet me-2"></i> Data Saldo Awal GS</h5>
                        <a href="{{ route('createSaldoAwalGS') }}"
                            class="btn btn-light btn-sm text-primary fw-semibold d-flex align-items-center gap-2">
                            <i class="fa fa-plus-circle"></i> <span>Tambah Saldo Awal</span>
                        </a>
                    </div>
                    <div class="card-body mt-3">
                        <form method="GET" action="{{ route('viewSaldoAwalGS') }}" class="mb-3">
                            <div class="row g-2">
                                <div class="col-md-6 col-lg-4">
                                    <input type="text" name="nama_barang" class="form-control form-control-sm"
                                        placeholder="Nama Barang" value="{{ request('nama_barang') }}">
                                </div>
                                <div class="col-md-6 col-lg-4">
                                    <input type="text" name="kode_barang" class="form-control form-control-sm"
                                        placeholder="Kode Barang" value="{{ request('kode_barang') }}">
                                </div>
                                <div class="col-md-6 col-lg-4">
                                    <select name="supplier" class="form-select2 form-select-sm">
                                        <option value="">Supplier</option>
                                        @foreach ($suppliers as $s)
                                            <option value="{{ $s->kode_supplier }}"
                                                {{ request('supplier') == $s->kode_supplier ? 'selected' : '' }}>
                                                {{ $s->nama_supplier }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                @php
                                    $bulanFilter = request('bulan') ?? date('n');
                                    $tahunFilter = request('tahun') ?? date('Y');
                                @endphp

                                <div class="col-md-6 col-lg-2">
                                    <select name="bulan" id="filter_bulan" class="form-select form-select-sm">
                                        <option value="">-- Semua Bulan --</option>
                                        @for ($m = 1; $m <= 12; $m++)
                                            <option value="{{ $m }}" {{ $bulanFilter == $m ? 'selected' : '' }}>
                                                {{ DateTime::createFromFormat('!m', $m)->format('F') }}
                                            </option>
                                        @endfor
                                    </select>
                                </div>

                                <div class="col-md-6 col-lg-2">
                                    <select name="tahun" id="filter_tahun" class="form-select form-select-sm">
                                        <option value="">-- Semua Tahun --</option>
                                        @php
                                            $tahunSekarang = date('Y');
                                        @endphp
                                        @for ($y = $tahunSekarang; $y <= $tahunSekarang + 1; $y++)
                                            <option value="{{ $y }}"
                                                {{ $tahunFilter == $y ? 'selected' : '' }}>
                                                {{ $y }}
                                            </option>
                                        @endfor
                                    </select>
                                </div>
                                <div class="col-md-6 col-lg-4">
                                    <select name="status" class="form-select2 form-select-sm">
                                        <option value="">Status</option>
                                        <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Aktif
                                        </option>
                                        <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Non Aktif
                                        </option>
                                    </select>
                                </div>
                                <div class="col-md-6 col-lg-4 d-grid">
                                    <button type="submit" class="btn btn-primary btn-sm">
                                        <i class="fa fa-filter me-1"></i> Filter
                                    </button>
                                </div>
                            </div>
                        </form>
                        <form action="{{ route('storeSaldoAwalGS') }}" method="POST">
                            @csrf
                            <div class="table-responsive">
                                <table class="table table-bordered table-sm align-middle">
                                    <thead>
                                        <tr class="text-center table-primary">
                                            <th style="width: 10%">Kode Barang</th>
                                            <th>Nama Barang</th>
                                            <th colspan="4" style="width: 25%">Satuan</th>
                                            <th style="width: 7%">Konversi (PCS)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($barangList as $kodeBarang => $dataSatuan)
                                            @php
                                                $ds = $dataSatuan->sortByDesc('isi')->take(4)->values();
                                                $satuanArr = $ds->pluck('satuan')->toArray();
                                                $isiArr = $ds->pluck('isi')->toArray();
                                                $qtyPcs = optional($ds->first())->qty_saldo_awal ?? 0;
                                                $sisaPcs = $qtyPcs;
                                                $qtyPerSatuan = [];

                                                foreach ($isiArr as $isi) {
                                                    $qtyPerSatuan[] = floor($sisaPcs / $isi);
                                                    $sisaPcs = $sisaPcs % $isi;
                                                }
                                            @endphp
                                            <tr>
                                                <td>{{ optional($ds->first())->kode_barang }}</td>
                                                <td>{{ optional($ds->first())->nama_barang }}</td>

                                                @foreach ($satuanArr as $idx => $sat)
                                                    <td>
                                                        <input type="number"
                                                            name="stok[{{ $kodeBarang }}][{{ $sat }}]"
                                                            class="form-control form-control-sm satuan-input"
                                                            data-isi="{{ $isiArr[$idx] }}"
                                                            data-barang="{{ $kodeBarang }}"
                                                            value="{{ $qtyPerSatuan[$idx] ?? 0 }}" min="0"
                                                            step="any">
                                                        <small>{{ $sat }}</small>
                                                    </td>
                                                @endforeach
                                                @for ($i = count($satuanArr); $i < 4; $i++)
                                                    <td>-</td>
                                                @endfor
                                                <td>
                                                    <input type="number" name="konversi[{{ $kodeBarang }}]"
                                                        class="form-control form-control-sm konversi-input" readonly
                                                        value="{{ $qtyPcs }}">
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).on('input', '.satuan-input', function() {
            const kodeBarang = $(this).data('barang');
            let totalPCS = 0;
            let stokData = {};

            $(`.satuan-input[data-barang="${kodeBarang}"]`).each(function() {
                const isi = parseFloat($(this).data('isi')) || 1;
                const qty = parseFloat($(this).val()) || 0;
                totalPCS += qty * isi;
                stokData[$(this).attr('name')] = qty;
            });

            $(`.konversi-input[name="konversi[${kodeBarang}]"]`).val(totalPCS);

            // Ambil bulan dan tahun dari filter
            const bulan = $('#filter_bulan').val();
            const tahun = $('#filter_tahun').val();

            $.ajax({
                url: "{{ route('storeSaldoAwalGS') }}",
                type: "POST",
                data: {
                    kode_barang: kodeBarang,
                    stok: stokData,
                    konversi: totalPCS,
                    bulan: bulan,
                    tahun: tahun,
                    _token: "{{ csrf_token() }}"
                },
                success: function(res) {
                    console.log("Disimpan:", res);
                },
                error: function(xhr) {
                    console.error("Gagal simpan:", xhr.responseText);
                }
            });
        });
    </script>

@endsection
