@extends('layouts.template')
@section('titlepage', 'Edit Barang Keluar')
@section('contents')

    <style>
        th,
        td {
            white-space: nowrap;
        }

        .form-section {
            padding: 1rem;
            border: 1px solid #e3e6f0;
            border-radius: 0.5rem;
            margin-bottom: 0.5rem;
            background-color: #fdfdfd;
        }
    </style>

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="card-header-gradient text-center py-2">
                <h4 class="mb-0 fw-semibold">Edit Barang Keluar</h4>
            </div>

            <form id="formBarangKeluar" action="{{ route('updateMutasiBarangKeluar', $transaksi->kode_transaksi) }}"
                method="POST" autocomplete="off">
                @csrf

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-section">
                            <label class="form-label small">Kode Transaksi</label>
                            <input type="text" name="kode_transaksi" class="form-control form-control-sm"
                                value="{{ $transaksi->kode_transaksi }}" readonly>

                            <label class="form-label small mt-2">Tanggal</label>
                            <input type="date" name="tanggal" value="{{ $transaksi->tanggal }}"
                                class="form-control form-control-sm" required>

                            <label class="form-label small mt-2">Tujuan</label>
                            <select name="tujuan" class="form-select form-select-sm" required>
                                <option value="">-- Pilih Tujuan --</option>
                                <option value="Penjualan" {{ $transaksi->tujuan == 'Penjualan' ? 'selected' : '' }}>Penjualan
                                </option>
                                <option value="Penyesuaian" {{ $transaksi->tujuan == 'Penyesuaian' ? 'selected' : '' }}>
                                    Penyesuaian</option>
                                <option value="Lainnya" {{ $transaksi->tujuan == 'Lainnya' ? 'selected' : '' }}>Lainnya
                                </option>
                            </select>

                            <label class="form-label small mt-2">Keterangan</label>
                            <textarea name="keterangan" rows="3"
                                class="form-control form-control-sm">{{ $transaksi->keterangan }}</textarea>
                        </div>
                    </div>

                    <div class="col-md-8">
                        <div class="form-section">
                            <h6 class="fw-semibold mb-2">Tambah Barang</h6>
                            <div class="row g-2 align-items-end">
                                <div class="col-md-7">
                                    <label class="form-label small">Barang</label>
                                    <select id="kode_barang" class="form-select form-select-sm"></select>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label small">Satuan</label>
                                    <select id="satuan" class="form-select form-select-sm">
                                        <option value="">Satuan</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label small">Qty</label>
                                    <input type="number" id="qtyBarang" class="form-control form-control-sm text-end"
                                        placeholder="0" min="1">
                                </div>
                                <div class="col-md-1 d-grid align-items-end">
                                    <button type="button" id="tambahBarangBtn" class="btn btn-sm btn-success">Add</button>
                                </div>
                            </div>
                        </div>

                        <input type="hidden" id="satuan_id">
                        <input type="hidden" name="keranjang" id="keranjangBarangKeluarInput">
                        <input type="hidden" name="total" id="totalBarangKeluarInput">

                        <div class="form-section mt-3">
                            <div class="table-responsive">
                                <table class="table table-bordered table-sm align-middle" id="tabelBarangKeluar">
                                    <thead class="table-light text-center">
                                        <tr>
                                            <th style="width: 5%">No</th>
                                            <th style="width: 10%">Kode</th>
                                            <th>Nama Barang</th>
                                            <th style="width: 10%">Qty</th>
                                            <th style="width: 5%">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>

                        <div class="d-grid pt-3">
                            <button type="submit" class="btn btn-sm btn-primary">Simpan Perubahan</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        const dataAwalBarang = @json($detail);

        $(document).ready(function () {
            let keranjangBarangKeluar = [];

            if (dataAwalBarang.length > 0) {
                keranjangBarangKeluar = dataAwalBarang.map(item => ({
                    kode_barang: item.kode_barang,
                    nama_barang: item.nama_barang,
                    satuan: item.satuan,
                    satuan_id: item.satuan_id,
                    qty: item.qty
                }));

                keranjangBarangKeluar.forEach((item, index) => {
                    const row = `
                            <tr data-satuan-id="${item.satuan_id}" data-nama="${item.nama_barang}">
                                <td class="text-center">${index + 1}</td>
                                <td class="text-center">${item.kode_barang}</td>
                                <td>${item.nama_barang} (${item.satuan})</td>
                                <td class="text-center">
                                    <input type="number" class="form-control form-control-sm text-end qtyKeluar"
                                        data-kode="${item.kode_barang}" value="${item.qty}" min="1">
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-danger btnHapusBarang">x</button>
                                </td>
                            </tr>
                        `;
                    $('#tabelBarangKeluar tbody').append(row);
                });

                hitungTotalBarangKeluar();
            }

            $('#kode_barang').select2({
                placeholder: 'Cari barang...',
                dropdownParent: $('#kode_barang').parent(),
                ajax: {
                    url: '{{ route("getBarang") }}',
                    dataType: 'json',
                    delay: 250,
                    data: params => ({ q: params.term }),
                    processResults: data => ({ results: data.results }),
                    cache: true
                }
            });

            $('#kode_barang').change(function () {
                const kode_barang = $(this).val();
                $('#satuan').html('<option value="">Memuat...</option>');

                $.get(`{{ route('getSatuanBarang', '') }}/${kode_barang}`, function (res) {
                    let html = '<option value="">Satuan</option>';
                    res.forEach(item => {
                        html += `<option value="${item.satuan}" data-id="${item.id}">${item.satuan}</option>`;
                    });
                    $('#satuan').html(html);
                    $('#satuan_id').val('');
                });
            });

            $('#satuan').change(function () {
                const selected = $(this).find(':selected');
                const id = selected.data('id') || '';
                $('#satuan_id').val(id);
            });

            $('#tambahBarangBtn').click(function () {
                const kode = $('#kode_barang').val();
                const nama = $('#kode_barang').select2('data')[0]?.text || '';
                const satuan = $('#satuan').val();
                const satuan_id = $('#satuan_id').val();
                const qty = parseInt($('#qtyBarang').val()) || 0;

                if (!kode || !nama || !satuan_id || qty <= 0) {
                    Swal.fire('Lengkapi data barang dengan benar!', '', 'warning');
                    return;
                }

                const no = $('#tabelBarangKeluar tbody tr').length + 1;

                const row = `
                        <tr data-satuan-id="${satuan_id}" data-nama="${nama}">
                            <td class="text-center">${no}</td>
                            <td class="text-center">${kode}</td>
                            <td>${nama} (${satuan})</td>
                            <td class="text-center">
                                <input type="number" class="form-control form-control-sm text-end qtyKeluar"
                                    data-kode="${kode}" value="${qty}" min="1">
                            </td>
                            <td class="text-center">
                                <button type="button" class="btn btn-sm btn-danger btnHapusBarang">x</button>
                            </td>
                        </tr>
                    `;

                $('#tabelBarangKeluar tbody').append(row);
                $('#kode_barang, #satuan, #qtyBarang').val(null).trigger('change');
                $('#satuan_id').val('');
                hitungTotalBarangKeluar();
            });

            $(document).on('click', '.btnHapusBarang', function () {
                $(this).closest('tr').remove();
                hitungTotalBarangKeluar();
            });

            $(document).on('input', '.qtyKeluar', function () {
                hitungTotalBarangKeluar();
            });

            function hitungTotalBarangKeluar() {
                let total = 0;
                keranjangBarangKeluar = [];

                $('#tabelBarangKeluar tbody tr').each(function () {
                    const qtyInput = $(this).find('.qtyKeluar');
                    let qty = parseInt(qtyInput.val()) || 0;
                    const kode = qtyInput.data('kode');
                    const nama_barang = $(this).data('nama');
                    const satuan_id = $(this).data('satuan-id');

                    if (qty > 0 && satuan_id) {
                        keranjangBarangKeluar.push({
                            kode_barang: kode,
                            nama_barang,
                            qty,
                            satuan_id
                        });
                        total += qty;
                    }
                });

                $('#totalBarangKeluarInput').val(total);
                $('#keranjangBarangKeluarInput').val(JSON.stringify(keranjangBarangKeluar));
            }

            $('form').on('submit', function (e) {
                const total = parseInt($('#totalBarangKeluarInput').val()) || 0;
                const keranjang = $('#keranjangBarangKeluarInput').val();

                if (total <= 0 || !keranjang || keranjang === '[]') {
                    e.preventDefault();
                    Swal.fire('Lengkapi data barang keluar terlebih dahulu!', '', 'warning');
                    return;
                }

                if (keranjangBarangKeluar.some(item => !item.satuan_id)) {
                    e.preventDefault();
                    Swal.fire('Beberapa barang tidak memiliki satuan!', '', 'error');
                }
            });
        });
    </script>
@endsection
