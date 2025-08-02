@extends('layouts.template')
@section('titlepage', 'Tambah Barang Keluar')
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
                <h4 class="mb-0 fw-semibold">Form Barang Keluar</h4>
            </div>

            <form id="formBarangKeluar" action="{{ route('storeMutasiBarangKeluar') }}" method="POST" autocomplete="off">
                @csrf

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-section">
                            <label class="form-label small">Kode Transaksi</label>
                            <input type="text" name="kode_transaksi" class="form-control form-control-sm" placeholder="Auto"
                                readonly>

                            <label class="form-label small mt-2">Tanggal</label>
                            <input type="date" name="tanggal" value="{{ date('Y-m-d') }}"
                                class="form-control form-control-sm" required>

                            <label class="form-label small mt-2">Kondisi</label>
                            <select name="kondisi" class="form-select form-select-sm" required>
                                <option value="gs">Good Stok</option>
                                <option value="bs">Bad Stok</option>
                            </select>

                            <label class="form-label small mt-2">Jenis Barang Keluar</label>
                            <select name="jenis_pengeluaran" class="form-select form-select-sm" required>
                                <option value="">-- Pilih Jenis Barang Keluar --</option>
                                <option value="Reject">Reject</option>
                                <option value="Penyesuaian">Penyesuaian</option>
                                <option value="Lainnya">Lainnya</option>
                            </select>

                            <label class="form-label small mt-2">Keterangan</label>
                            <textarea name="keterangan" rows="3" class="form-control form-control-sm"
                                placeholder="Keterangan tambahan..."></textarea>
                        </div>
                    </div>

                    {{-- Form Kanan --}}
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

                        {{-- Tabel Barang --}}
                        <div class="form-section mt-3">
                            <div class="table-responsive">
                                <table class="table table-bordered table-sm align-middle" id="tabelBarangKeluar">
                                    <thead class="table-light text-center">
                                        <tr>
                                            <th style="width: 5%">No</th>
                                            <th style="width: 10%">Kode</th>
                                            <th>Nama Barang</th>
                                            <th style="width: 10%">Qty</th>
                                            <th style="width: 5%">Satuan</th>
                                            <th style="width: 5%">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>

                        {{-- Tombol Simpan --}}
                        <div class="d-grid pt-3">
                            <button type="submit" class="btn btn-sm btn-primary">Simpan Barang Keluar</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        $(document).ready(function () {
            let keranjangBarangKeluar = [];

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
                                <tr data-kode="${kode}" data-nama="${nama}" data-satuan-id="${satuan_id}" data-satuan="${satuan}">
                                    <td class="text-center">${no}</td>
                                    <td class="text-center">${kode}</td>
                                    <td>${nama} (${satuan})</td>
                                    <td class="text-center">
                                        <input type="number" class="form-control form-control-sm text-end qtyKeluar"
                                            data-kode="${kode}" value="${qty}" min="1">
                                    </td>
                                    <td class="text-center">${satuan}</td>
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

            function hitungTotalBarangKeluar() {
                let total = 0;
                keranjangBarangKeluar = [];

                $('#tabelBarangKeluar tbody tr').each(function () {
                    const qtyInput = $(this).find('.qtyKeluar');
                    const qty = parseInt(qtyInput.val()) || 0;
                    const kode = qtyInput.data('kode');
                    const nama_barang = $(this).data('nama');
                    const satuan_id = $(this).data('satuan-id');
                    const satuan = $(this).data('satuan');

                    if (qty > 0) {
                        keranjangBarangKeluar.push({ kode_barang: kode, nama_barang, qty, satuan_id, satuan });
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
                }
            });
        });
    </script>
@endsection
