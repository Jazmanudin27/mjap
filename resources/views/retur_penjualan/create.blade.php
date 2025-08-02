@extends('layouts.template')
@section('titlepage', 'Tambah Retur Penjualan')
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
                <h4 class="mb-0 fw-semibold">Form Retur Penjualan</h4>
            </div>
            <form id="formReturPenjualan" action="{{ route('storeReturPenjualan') }}" method="POST" autocomplete="off">
                @csrf
                <div class="form-section">
                    <div class="row g-2">
                        <!-- Kolom Tanggal & No Retur -->
                        <div class="col-md-3">
                            <label class="form-label small">No Retur</label>
                            <input type="text" name="no_retur" class="form-control form-control-sm" placeholder="Auto"
                                readonly>

                            <label class="form-label small mt-2">Tanggal Retur</label>
                            <input type="date" name="tanggal" value="{{ date('Y-m-d') }}"
                                class="form-control form-control-sm" required>

                            <label class="form-label small mt-2">Jenis Retur</label>
                            <select name="jenis_retur" id="jenis_retur" class="form-select form-select-sm w-100" required>
                                <option value="PF">Potong Faktur</option>
                                <option value="GB">Ganti Barang</option>
                            </select>
                        </div>

                        <!-- Kolom Pelanggan, Faktur, Jenis Retur -->
                        <div class="col-md-5">
                            <label class="form-label small">Pilih Pelanggan</label>
                            <select id="kode_pelanggan" name="kode_pelanggan" class="form-select form-select-sm w-100"
                                required></select>

                            <label class="form-label small mt-2">No Faktur</label>
                            <select name="no_faktur" id="no_faktur" class="form-select form-select-sm w-100" required>
                                <option value="">Pilih No Faktur</option>
                            </select>
                        </div>

                        <!-- Kolom Total Retur -->
                        <div class="col-md-4 mt-3 mt-md-0">
                            <div class="card shadow-sm border-0 h-100">
                                <div class="card-body bg-light rounded-3 d-flex flex-column justify-content-between p-3">
                                    <div class="d-flex align-items-center mb-2">
                                        <div class="bg-danger rounded-circle d-flex align-items-center justify-content-center me-3"
                                            style="width: 45px; height: 45px;">
                                            <i class="bi bi-arrow-counterclockwise text-white fs-4"></i>
                                        </div>
                                        <div>
                                            <div class="text-muted small mb-0">Total Retur</div>
                                            <h4 class="mb-0 text-danger fw-bold" id="totalReturDisplay">Rp. 0</h4>
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        <small class="text-muted fst-italic">*Total dari item yang diretur</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                {{-- Tabel Barang --}}
                <div class="form-section mt-3">
                    <div class="table-responsive form-section">
                        <table class="table table-bordered table-sm align-middle" id="tabelRetur">
                            <thead class="table-light text-center">
                                <tr>
                                    <th style="width: 5%">No</th>
                                    <th style="width: 10%">Kode</th>
                                    <th>Nama Barang</th>
                                    <th style="width: 10%">Qty</th>
                                    <th style="width: 15%">Harga Retur</th>
                                    <th style="width: 15%">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <th colspan="5" class="text-end">Total</th>
                                    <th id="footerTotalRetur" class="text-end">Rp. 0</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                <input type="hidden" name="keranjang" id="keranjangReturInput">
                <input type="hidden" name="total" id="totalReturInput">

                <div class="form-group mt-3">
                    <textarea name="keterangan" class="form-control form-control-sm" rows="2"
                        placeholder="Catatan retur (opsional)..."></textarea>
                </div>

                <div class="d-grid pt-3">
                    <button type="submit" class="btn btn-sm btn-danger">Simpan Retur</button>
                </div>
            </form>
        </div>
    </div>
    <script>
        $(document).ready(function () {
            let keranjangRetur = [];

            function formatRupiah(angka) {
                let number = parseInt(angka) || 0;
                return 'Rp' + number.toLocaleString('id-ID');
            }

            function parseRupiah(str) {
                return parseInt((str || '0').toString().replace(/[^0-9]/g, '')) || 0;
            }

            const savedPelanggan = localStorage.getItem('kode_pelanggan');
            const savedFaktur = localStorage.getItem('no_faktur');
            const savedKeranjang = localStorage.getItem('keranjang_retur');

            // ========== Select2 pelanggan ==========
            $('#kode_pelanggan').select2({
                placeholder: 'Cari pelanggan…',
                ajax: {
                    url: "{{ route('getPelanggan') }}",
                    dataType: 'json',
                    delay: 250,
                    data: params => ({ kode_pelanggan: params.term }),
                    processResults: data => ({ results: data.results }),
                    cache: true
                }
            });

            // Jika ada pelanggan tersimpan, masukkan ke select2
            if (savedPelanggan) {
                $.ajax({
                    url: "{{ route('getPelanggan') }}",
                    data: { kode_pelanggan: savedPelanggan },
                    success: function (data) {
                        const opt = data.results.find(p => p.id === savedPelanggan);
                        if (opt) {
                            const newOption = new Option(opt.text, opt.id, true, true);
                            $('#kode_pelanggan').append(newOption).trigger('change');
                        }
                    }
                });
            }

            // Jika ada faktur tersimpan, load faktur
            if (savedPelanggan && savedFaktur) {
                $.get(`/getFakturByPelanggan/${savedPelanggan}`, function (res) {
                    let opsi = '<option value="">Pilih No Faktur</option>';
                    res.forEach(f => {
                        opsi += `<option value="${f.no_faktur}" ${f.no_faktur === savedFaktur ? 'selected' : ''}>${f.no_faktur}</option>`;
                    });
                    $('#no_faktur').html(opsi);
                    $('#no_faktur').trigger('change'); // trigger load detail
                });
            }

            // Saat pelanggan dipilih
            $('#kode_pelanggan').on('select2:select', function () {
                const kode = $(this).val();
                localStorage.setItem('kode_pelanggan', kode);

                $('#no_faktur').html('<option value="">Loading...</option>');
                $.get(`/getFakturByPelanggan/${kode}`, function (res) {
                    let opsi = '<option value="">Pilih No Faktur</option>';
                    res.forEach(f => {
                        opsi += `<option value="${f.no_faktur}">${f.no_faktur}</option>`;
                    });
                    $('#no_faktur').html(opsi);
                });
            });

            // Saat faktur dipilih
            $('#no_faktur').on('change', function () {
                const noFaktur = $(this).val();
                if (!noFaktur) return;
                localStorage.setItem('no_faktur', noFaktur);

                $.get(`/getDetailFakturPenjualan/${noFaktur}`, function (res) {
                    let html = '';
                    keranjangRetur = [];

                    res.detail.forEach((item, i) => {
                        const hargaDefault = formatRupiah(item.harga);
                        html += `
                        <tr>
                            <td class="text-center">${i + 1}</td>
                            <td>${item.kode_barang}</td>
                            <td>${item.nama_barang}</td>
                            <td><input type="number" class="form-control form-control-sm text-end qtyRetur"
                                data-kode="${item.kode_barang}" max="${item.qty}" min="1"></td>
                            <td><input type="text" class="form-control form-control-sm text-end hargaRetur"
                                value="${hargaDefault}"></td>
                            <td class="text-end subtotal">Rp0</td>
                        </tr>`;
                    });

                    $('#tabelRetur tbody').html(html);

                    if (savedKeranjang) {
                        const data = JSON.parse(savedKeranjang);
                        data.forEach(k => {
                            const row = $(`#tabelRetur tbody tr`).filter((i, el) => $(el).find('td').eq(1).text() === k.kode_barang);
                            row.find('.qtyRetur').val(k.qty);
                            row.find('.hargaRetur').val(formatRupiah(k.harga));
                        });
                    }

                    hitungTotalRetur();
                });
            });

            $(document).on('input', '.hargaRetur', function () {
                let raw = parseRupiah($(this).val());
                $(this).val(formatRupiah(raw));
            });

            $(document).on('input change', '.qtyRetur, .hargaRetur', function () {
                hitungTotalRetur();
            });

            $(document).on('click', '.btn-hapus', function () {
                $(this).closest('tr').remove();
                hitungTotalRetur();
            });

            function hitungTotalRetur() {
                let total = 0;
                keranjangRetur = [];

                $('#tabelRetur tbody tr').each(function () {
                    const qtyInput = $(this).find('.qtyRetur');
                    const hargaInput = $(this).find('.hargaRetur');

                    let qty = parseInt(qtyInput.val()) || 0;
                    const maxQty = parseInt(qtyInput.attr('max')) || 0;
                    if (qty > maxQty) {
                        Swal.fire('Qty melebihi jumlah faktur!', '', 'warning');
                        qty = maxQty;
                        qtyInput.val(maxQty);
                    }

                    const harga = parseRupiah(hargaInput.val());
                    const kode = qtyInput.data('kode');
                    const nama_barang = $(this).find('td').eq(2).text().trim();
                    const subtotal = qty * harga;

                    $(this).find('.subtotal').text(formatRupiah(subtotal));

                    if (qty > 0) {
                        keranjangRetur.push({ kode_barang: kode, nama_barang, qty, harga, subtotal });
                        total += subtotal;
                    }
                });

                $('#footerTotalRetur, #totalReturDisplay').text(formatRupiah(total));
                $('#totalReturInput').val(total);
                $('#keranjangReturInput').val(JSON.stringify(keranjangRetur));
                localStorage.setItem('keranjang_retur', JSON.stringify(keranjangRetur));
            }

            $('form').on('submit', function (e) {
                const pelanggan = $('#kode_pelanggan').val();
                const faktur = $('#no_faktur').val();
                const jenis = $('#jenis_retur').val();
                const total = parseInt($('#totalReturInput').val()) || 0;
                const keranjang = $('#keranjangReturInput').val();

                if (!pelanggan || !faktur || !jenis || total <= 0 || !keranjang || keranjang === '[]') {
                    e.preventDefault(); // batalkan submit
                    Swal.fire('Lengkapi data terlebih dahulu!', '', 'warning');
                } else {
                    localStorage.removeItem('kode_pelanggan');
                    localStorage.removeItem('no_faktur');
                    localStorage.removeItem('keranjang_retur');
                }
            });

        });

    </script>

@endsection
