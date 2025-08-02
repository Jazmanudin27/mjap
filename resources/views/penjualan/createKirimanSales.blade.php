@extends('layouts.template')
@section('titlepage', 'Create Kiriman Sales')
@section('contents')
    <div class="container-fluid p-0">
        <div class="col-12">
            <div class="card shadow-sm rounded-4">
                <div class="card-header border-0 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 text-dark fw-bold">Input Kiriman Sales</h5>
                </div>
                <div class="card-body mt-3">
                    <form action="{{ route('storeKirimanSales') }}" method="POST"
                        onsubmit="localStorage.removeItem('keranjang_kiriman');">
                        @csrf
                        <div class="row g-3 mb-3">
                            <div class="col-md-12">
                                <div class="row g-3 mb-3">
                                    <div class="col-md-12">
                                        <label class="form-label">Wilayah</label>
                                        <select name="kode_wilayah" id="kode_wilayah" class="form-select select2">
                                            <option value="">-- Semua Wilayah --</option>
                                            @foreach($wilayah as $w)
                                                <option value="{{ $w->kode_wilayah }}">{{ $w->nama_wilayah }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="border rounded-3 p-2">
                                            <h6 class="fw-bold">Daftar Faktur
                                                <div class="form-check float-end">
                                                    <input class="form-check-input" type="checkbox" id="checkAllFaktur">
                                                    <label class="form-check-label small" for="checkAllFaktur">Pilih
                                                        Semua</label>
                                                </div>
                                            </h6>
                                            <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                                                <table class="table table-sm table-bordered align-middle" id="tabelFaktur">
                                                    <thead class="table-light text-center">
                                                        <tr>
                                                            <th style="width: 2%;">No</th>
                                                            <th style="width:13%">No Faktur</th>
                                                            <th style="width:9%">Tanggal</th>
                                                            <th>Pelanggan</th>
                                                            <th>Sales</th>
                                                            <th style="width:12%">Wilayah</th>
                                                            <th style="width:9%">Total</th>
                                                            <th style="width:2%">Cheklist</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody></tbody>
                                                    <tfoot class="table-light fw-bold text-end">
                                                        <tr>
                                                            <td colspan="6" class="text-end">Total</td>
                                                            <td id="totalFaktur" class="text-end">Rp0</td>
                                                            <td></td>
                                                        </tr>
                                                    </tfoot>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="row g-3 mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Tanggal Kirim</label>
                                        <input type="date" name="tanggal" id="tanggal" value="{{ date('Y-m-d') }}"
                                            class="form-control form-control-sm" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Wilayah Pengiriman</label>
                                        <select name="kode_wilayah_pengiriman" id="kode_wilayah_pengiriman"
                                            class="form-select select2" required>
                                            <option value="">-- Pilih Wilayah --</option>
                                            @foreach($wilayah as $w)
                                                <option value="{{ $w->kode_wilayah }}">{{ $w->nama_wilayah }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="border rounded-3 p-2">
                                            <h6 class="fw-bold">Keranjang Kiriman</h6>
                                            <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                                                <table class="table table-sm table-bordered align-middle" id="keranjangTable">
                                                    <thead class="table-light text-center">
                                                        <tr>
                                                            <th style="width: 2%;">No</th>
                                                            <th style="width:13%">No Faktur</th>
                                                            <th style="width:9%">Tanggal</th>
                                                            <th>Pelanggan</th>
                                                            <th>Sales</th>
                                                            <th style="width:12%">Wilayah</th>
                                                            <th style="width:9%">Total</th>
                                                            <th style="width:2%">Aksi</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody></tbody>
                                                    <tfoot class="table-light fw-bold text-end">
                                                        <tr>
                                                            <td colspan="6" class="text-end">Total</td>
                                                            <td id="keranjangTotal" class="text-end">Rp0</td>
                                                            <td></td>
                                                        </tr>
                                                    </tfoot>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-success mt-4 w-100" id="btnSimpan" style="display: none;">
                            <i class="bi bi-truck me-1"></i> Simpan Kiriman
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function () {

            function formatRupiah(angka) {
                let number = parseInt(angka) || 0;
                return 'Rp' + number.toLocaleString('id-ID');
            }

            function parseRupiah(str) {
                return parseInt((str || '0').toString().replace(/[^0-9]/g, '')) || 0;
            }

            $('.select2').select2({ width: '100%' });

            let keranjang = [];
            const savedKeranjang = localStorage.getItem('keranjang_kiriman');
            if (savedKeranjang) {
                keranjang = JSON.parse(savedKeranjang);
                renderKeranjang();
            }

            loadFaktur();

            function renderKeranjang() {
                let html = '';
                keranjang.forEach((item, i) => {
                    html += `
                    <tr data-no="${item.no_faktur}">
                        <td class="text-center">${i + 1}</td>
                        <td>
                            <input type="hidden" name="items[${i}][no_faktur]" value="${item.no_faktur}">
                            ${item.no_faktur}
                        </td>
                        <td>${item.tanggal}</td>
                        <td>${item.nama_pelanggan}</td>
                        <td>${item.nama_sales}</td>
                        <td>${item.nama_wilayah}</td>
                        <td class="text-end">${formatRupiah(item.grand_total)}</td>
                        <td class="text-center">
                            <button type="button" class="btn btn-sm btn-danger btnHapusItem" title="Hapus">
                                <i class="bi bi-x"></i>
                            </button>
                        </td>
                    </tr>`;
                });

                $('#keranjangTable tbody').html(html);
                $('#btnSimpan').toggle(keranjang.length > 0);
                localStorage.setItem('keranjang_kiriman', JSON.stringify(keranjang));

                let total = keranjang.reduce((sum, item) => sum + parseFloat(item.grand_total || 0), 0);
                $('#keranjangTotal').text(formatRupiah(total));
            }

            function loadFaktur() {
                let wilayah = $('#kode_wilayah').val();
                let paramWilayah = wilayah ? wilayah : 'null';

                $('#tabelFaktur tbody').html('<tr><td colspan="8">Memuat data...</td></tr>');

                $.get(`{{ url('getFakturByWilayah') }}/${paramWilayah}`, function (data) {
                    let html = '';
                    let totalFaktur = 0;

                    if (data.length === 0) {
                        html = '<tr><td colspan="8" class="text-center text-muted">Tidak ada faktur ditemukan.</td></tr>';
                    } else {
                        data.forEach((f, index) => {
                            const sudahAda = keranjang.find(item => item.no_faktur === f.no_faktur);
                            html += `
                            <tr>
                                <td class="text-center">${index + 1}</td>
                                <td>${f.no_faktur}</td>
                                <td>${f.tanggal}</td>
                                <td>${f.nama_pelanggan}</td>
                                <td>${f.nama_sales}</td>
                                <td>${f.nama_wilayah}</td>
                                <td class="text-end">${formatRupiah(f.grand_total)}</td>
                                <td class="text-center">
                                    <input type="checkbox" class="cekFaktur" data-faktur='${JSON.stringify(f)}' ${sudahAda ? 'checked' : ''}>
                                </td>
                            </tr>`;
                            totalFaktur += parseFloat(f.grand_total) || 0;
                        });
                    }

                    $('#tabelFaktur tbody').html(html);
                    $('#totalFaktur').text(formatRupiah(totalFaktur));

                    renderKeranjang();
                });
            }

            $(document).on('change', '.cekFaktur', function () {
                const data = $(this).data('faktur');
                const sudahAda = keranjang.find(item => item.no_faktur === data.no_faktur);
                if ($(this).is(':checked') && !sudahAda) {
                    keranjang.push(data);
                } else if (!$(this).is(':checked')) {
                    keranjang = keranjang.filter(item => item.no_faktur !== data.no_faktur);
                }
                renderKeranjang();
            });

            $('#checkAllFaktur').on('change', function () {
                $('.cekFaktur').prop('checked', this.checked).trigger('change');
            });

            $(document).on('click', '.btnHapusItem', function () {
                const no = $(this).closest('tr').data('no');
                keranjang = keranjang.filter(item => item.no_faktur !== no);
                renderKeranjang();
                $(`.cekFaktur[data-faktur*="${no}"]`).prop('checked', false);
            });
            $('#kode_wilayah').on('change', function () {
                $('#checkAllFaktur').prop('checked', false);
                loadFaktur();
            });
        });

    </script>
@endsection
