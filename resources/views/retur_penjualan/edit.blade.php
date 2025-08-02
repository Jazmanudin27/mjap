@extends('layouts.template')
@section('titlepage', 'Edit Retur Penjualan')
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
                <h4 class="mb-0 fw-semibold">Edit Retur Penjualan</h4>
            </div>
            <form id="formReturPenjualan" action="{{ route('updateReturPenjualan', $retur->no_retur) }}" method="POST"
                autocomplete="off">
                @csrf
                <div class="form-section">
                    <h6 class="fw-semibold mb-3 border-bottom pb-1 text-primary">
                        Informasi Retur Penjualan
                    </h6>
                    <div class="row g-3">
                        <!-- Kolom Kiri -->
                        <div class="col-md-4">
                            <div class="mb-2">
                                <label class="form-label small text-muted">No Retur</label>
                                <input type="text" name="no_retur" value="{{ $retur->no_retur }}"
                                    class="form-control form-control-sm" readonly>
                            </div>
                            <div class="mb-2">
                                <label class="form-label small text-muted">Tanggal Retur</label>
                                <input type="date" name="tanggal" value="{{ $retur->tanggal }}"
                                    class="form-control form-control-sm" required>
                            </div>
                            <div>
                                <label class="form-label small text-muted">Jenis Retur</label>
                                <select name="jenis_retur" id="jenis_retur" class="form-select form-select-sm" required>
                                    <option value="PF" {{ $retur->jenis_retur == 'PF' ? 'selected' : '' }}>Potong Faktur
                                    </option>
                                    <option value="GB" {{ $retur->jenis_retur == 'GB' ? 'selected' : '' }}>Ganti Barang
                                    </option>
                                </select>
                            </div>
                        </div>

                        <!-- Kolom Tengah -->
                        <div class="col-md-4">
                            <div class="mb-2">
                                <label class="form-label small text-muted">Pilih Pelanggan</label>
                                <select id="kode_pelanggan" name="kode_pelanggan" class="form-select form-select-sm"
                                    required></select>
                            </div>
                            <div>
                                <label class="form-label small text-muted">No Faktur</label>
                                <select name="no_faktur" id="no_faktur" class="form-select form-select-sm" required>
                                    <option value="">Pilih No Faktur</option>
                                </select>
                            </div>
                        </div>

                        <!-- Kolom Kanan -->
                        <div class="col-md-4">
                            <div class="card shadow-sm border-0 h-80">
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

                <div class="table-responsive form-section">
                    <table class="table table-bordered table-sm align-middle" id="tabelRetur">
                        <thead class="table-light text-center">
                            <tr>
                                <th style="width: 5%">No</th>
                                <th style="width: 10%">Kode</th>
                                <th>Nama Barang</th>
                                <th style="width: 10%">Satuan</th>
                                <th style="width: 8%">Qty</th>
                                <th style="width: 15%">Harga Retur</th>
                                <th style="width: 15%">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                        <tfoot class="table-light">
                            <tr>
                                <th colspan="6" class="text-end">Total</th>
                                <th id="footerTotalRetur" class="text-end">Rp. 0</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <input type="hidden" name="keranjang" id="keranjangReturInput">
                <input type="hidden" name="total" id="totalReturInput">

                <div class="form-group mt-3">
                    <textarea name="keterangan" class="form-control form-control-sm" rows="2"
                        placeholder="Catatan retur (opsional)...">{{ $retur->keterangan }}</textarea>
                </div>

                <div class="d-grid pt-3">
                    <button type="submit" class="btn btn-sm btn-danger">Update Retur</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        $(document).ready(function () {
            let keranjangRetur = [];
            const pelanggan = @json($retur->kode_pelanggan);
            const noFaktur = @json($retur->no_faktur);
            const detailKeranjang = @json($detail);

            function formatRupiah(angka) {
                let number = parseInt(angka) || 0;
                return 'Rp' + number.toLocaleString('id-ID');
            }

            function parseRupiah(str) {
                return parseInt((str || '0').toString().replace(/[^0-9]/g, '')) || 0;
            }

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

            if (pelanggan && noFaktur) {
                $.ajax({
                    url: "{{ route('getPelanggan') }}",
                    data: { kode_pelanggan: pelanggan },
                    success: function (data) {
                        const opt = data.results.find(p => p.id === pelanggan);
                        if (opt) {
                            const newOption = new Option(opt.text, opt.id, true, true);
                            $('#kode_pelanggan').append(newOption).trigger('change');
                        }

                        // Setelah pelanggan tampil, muat faktur
                        $.get(`/getFakturByPelanggan/${pelanggan}`, function (res) {
                            let opsi = '<option value="">Pilih No Faktur</option>';
                            res.forEach(f => {
                                opsi += `<option value="${f.no_faktur}" ${f.no_faktur === noFaktur ? 'selected' : ''}>${f.no_faktur}</option>`;
                            });
                            $('#no_faktur').html(opsi);

                            let html = '';
                            keranjangRetur = [];

                            detailKeranjang.forEach((item, i) => {
                                const hargaDefault = formatRupiah(item.harga_retur);
                                const subtotal = item.qty * item.harga_retur;
                                html += `
                                    <tr>
                                        <td class="text-center">${i + 1}</td>
                                        <td>${item.kode_barang}</td>
                                        <td class="td-nama-barang" data-nama="${item.nama_barang}">${item.nama_barang}</td>
                                        <td>${item.nama_satuan || '-'}</td> <!-- Tambahan kolom satuan -->
                                        <td><input type="number" class="form-control form-control-sm text-center qtyRetur"
                                            data-kode="${item.kode_barang}" max="${item.qty_faktur || 999}" min="1" value="${item.qty}"></td>
                                        <td><input type="text" class="form-control form-control-sm text-end hargaRetur"
                                            value="${hargaDefault}"></td>
                                        <td class="text-end subtotal">${formatRupiah(subtotal)}</td>
                                    </tr>`;

                                keranjangRetur.push({
                                    kode_barang: item.kode_barang,
                                    nama_barang: item.nama_barang,
                                    qty: item.qty,
                                    harga: item.harga_retur,
                                    subtotal: subtotal,
                                    satuan: item.nama_satuan || '-' // bisa ikut disimpan kalau perlu
                                });
                            });

                            $('#tabelRetur tbody').html(html);
                            updateTotalDisplay();
                        });
                    }
                });
            }

            // Harga input format
            $(document).on('input', '.hargaRetur', function () {
                let raw = parseRupiah($(this).val());
                $(this).val(formatRupiah(raw));
            });

            // Perubahan qty/harga
            $(document).on('input change', '.qtyRetur, .hargaRetur', function () {
                hitungTotalRetur();
            });

            // Hapus baris (opsional kalau kamu ada tombol hapus)
            $(document).on('click', '.btn-hapus', function () {
                $(this).closest('tr').remove();
                hitungTotalRetur();
            });

            // Kalkulasi total & simpan ke input hidden
            function hitungTotalRetur() {
                keranjangRetur = [];
                let total = 0;

                $('#tabelRetur tbody tr').each(function () {
                    console.log('ISI DETAIL KERANJANG:', detailKeranjang);
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
                    const nama_barang = $(this).find('.td-nama-barang').data('nama');
                    const subtotal = qty * harga;
                    $(this).find('.subtotal').text(formatRupiah(subtotal));

                    if (qty > 0) {
                        keranjangRetur.push({ kode_barang: kode, nama_barang, qty, harga, subtotal });
                        total += subtotal;
                    }
                });

                updateTotalDisplay();
            }

            function updateTotalDisplay() {
                const total = keranjangRetur.reduce((a, b) => a + b.subtotal, 0);
                $('#footerTotalRetur, #totalReturDisplay').text(formatRupiah(total));
                $('#keranjangReturInput').val(JSON.stringify(keranjangRetur));
                $('#totalReturInput').val(total);
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
                }
            });

        });
    </script>

@endsection
