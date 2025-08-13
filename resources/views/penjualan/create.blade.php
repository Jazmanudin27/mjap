@extends('layouts.template')
@section('titlepage', 'Tambah Penjualan')
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
                <h4 class="mb-0 fw-semibold">Input Penjualan</h4>
            </div>
            <form id="formPenjualan" action="{{ route('storePenjualan') }}" method="POST" autocomplete="off">
                @csrf
                <div class="form-section">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="col-md-12 mt-1">
                                <small class="form-text text-muted">No Faktur</small>
                                <input type="text" name="no_faktur" class="form-control form-control-sm"
                                    placeholder="No Faktur (Auto)" readonly required>
                            </div>

                            <div class="col-md-12 mt-1">
                                <small class="form-text text-muted">Tanggal Transaksi</small>
                                <input type="date" name="tanggal" id="tanggal" value="{{ Date('Y-m-d') }}"
                                    class="form-control form-control-sm" required>
                            </div>

                            <div class="col-md-12 mt-1" hidden>
                                <small class="form-text text-muted">Tgl Kiriman</small>
                                <input type="date" name="tanggal_kirim" id="tanggal_kirim"
                                    class="form-control form-control-sm">
                            </div>

                            <div class="col-md-12 mt-1">
                                <small class="form-text text-muted">User</small>
                                <input type="text" name="username" value="{{ Auth::user()->name }}"
                                    class="form-control form-control-sm" placeholder="User" required>
                            </div>

                            <div class="col-md-12 mt-1">
                                <small class="form-text text-muted">Salesman</small>
                                <select id="kode_sales" name="kode_sales" autofocus class="form-select2 form-select-sm"
                                    required>
                                    <option value="">Pilih Salesman</option>
                                    @foreach ($sales as $s)
                                        <option value="{{ $s->nik }}" data-nama="{{ $s->nama_lengkap }}">
                                            {{ $s->nik }} - {{ $s->nama_lengkap }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- Kolom 2 --}}
                        <div class="col-md-5">
                            <div class="col-md-12 mt-1">
                                <small class="form-text text-muted">Pelanggan</small>
                                <select id="kode_pelanggan" name="kode_pelanggan" class="form-select form-select-sm"
                                    tabindex="1" required></select>
                            </div>
                            <input type="hidden" id="kode_wilayah" name="kode_wilayah" class="form-control form-control-sm"
                                readonly>
                            <div class="col-md-12 mt-1">
                                <div class="row">
                                    <div class="col-md-6">
                                        <small class="form-text text-muted">Wilayah</small>
                                        <input type="text" id="nama_wilayah" name="nama_wilayah"
                                            class="form-control form-control-sm" readonly>
                                    </div>
                                    <div class="col-md-6">
                                        <small class="form-text text-muted">Sisa Piutang</small>
                                        <input type="text" id="sisa_piutang" name="sisa_piutang"
                                            class="form-control form-control-sm text-end" readonly>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-12 mt-1">
                                <div class="row">
                                    <div class="col-md-6">
                                        <small class="form-text text-muted">Limit Pelanggan</small>
                                        <input type="text" id="limit_pelanggan" name="limit_pelanggan"
                                            class="form-control form-control-sm text-end" readonly>
                                    </div>
                                    <div class="col-md-6">
                                        <small class="form-text text-muted">Sisa Limit</small>
                                        <input type="text" id="sisa_limit" name="sisa_limit"
                                            class="form-control form-control-sm text-end" readonly>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-12 mt-1">
                                <div class="row">
                                    <div class="col-md-6">
                                        <small class="form-text text-muted">Max Faktur</small>
                                        <input type="text" id="max_faktur" name="max_faktur"
                                            class="form-control form-control-sm text-end" readonly>
                                    </div>
                                    <div class="col-md-6">
                                        <small class="form-text text-muted">Faktur Kredit</small>
                                        <input type="text" id="faktur_kredit" name="faktur_kredit"
                                            class="form-control form-control-sm text-end" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4 mt-1">
                            <div class="card shadow-sm bg-primary text-white p-3 h-50 rounded-3">
                                <div class="d-flex align-items-center h-100">
                                    <div class="me-3">
                                        <i class="bi bi-cart-check-fill" style="font-size: 2.5rem; color:yellow;"></i>
                                    </div>
                                    <div class="flex-grow-1 text-end">
                                        <small class="text-light">Total Penjualan</small>
                                        <h2 class="mb-0"><b id="totalPenjualanDisplay">Rp. 0</b></h2>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Form Tambah Produk --}}
                <div class="form-section mt-3">
                    <div class="row g-1 align-items-end mb-2">
                        <div class="col-md-4">
                            <select id="kode_barang" name="kode_barang" class="form-select form-select-sm"
                                tabindex="2">
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select id="satuan" class="form-select2 form-select-sm">
                                <option value="">Satuan</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <input type="text" id="harga_jual" class="form-control form-control-sm text-end"
                                placeholder="Harga Jual">
                        </div>
                        <div class="col-md-2">
                            <input type="text" id="jumlah" class="form-control form-control-sm text-end"
                                placeholder="Qty">
                        </div>
                        <div class="col-md-2">
                            <input type="text" id="total" class="form-control form-control-sm text-end"
                                placeholder="Total" readonly>
                        </div>
                        <div class="col-md-12">
                            <div class="row align-items-center mb-2 mt-1">
                                <div class="col-2">
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text">D1</span>
                                        <input type="text" id="diskon1_nominal" placeholder="Rp. 0"
                                            class="form-control text-end">
                                    </div>
                                </div>
                                <div class="col-1">
                                    <input type="text" id="diskon1_persen" placeholder="%"
                                        class="form-control text-end">
                                </div>
                                <div class="col-2">
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text">D2</span>
                                        <input type="text" id="diskon2_nominal" placeholder="Rp. 0"
                                            class="form-control text-end">
                                    </div>
                                </div>
                                <div class="col-1">
                                    <input type="text" id="diskon2_persen" placeholder="%"
                                        class="form-control text-end">
                                </div>
                                <div class="col-2">
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text">D3</span>
                                        <input type="text" id="diskon3_nominal" placeholder="Rp. 0"
                                            class="form-control text-end">
                                    </div>
                                </div>
                                <div class="col-1">
                                    <input type="text" id="diskon3_persen" placeholder="%"
                                        class="form-control text-end">
                                </div>
                                <div class="col-2">
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text">D4</span>
                                        <input type="text" id="diskon4_nominal" placeholder="Rp. 0"
                                            class="form-control text-end">
                                    </div>
                                </div>
                                <div class="col-1">
                                    <input type="text" id="diskon4_persen" placeholder="%"
                                        class="form-control form-control-sm text-end">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-sm align-middle" id="keranjangTable">
                            <thead class="table-light">
                                <tr class="text-center">
                                    <th style="width:2%">No</th>
                                    <th style="width:9%">Kode</th>
                                    <th>Nama</th>
                                    <th style="width:5%">Satuan</th>
                                    <th style="width:10%">Harga</th>
                                    <th style="width:5%">Qty</th>
                                    <th style="width:6%">D1</th>
                                    <th style="width:6%">D2</th>
                                    <th style="width:6%">D3</th>
                                    <th style="width:6%">D4</th>
                                    <th style="width:3%">Promo</th>
                                    <th style="width:11%">Total</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <th colspan="11" class="text-end">Subtotal</th>
                                    <th id="footerSubtotal" class="text-end">Rp 0</th>
                                </tr>
                                <tr>
                                    <th colspan="11" class="text-end">Total Diskon</th>
                                    <th id="footerPotongan" class="text-end">Rp 0</th>
                                </tr>
                                <tr>
                                    <th colspan="11" class="text-end">Total</th>
                                    <th id="footerTotal" class="text-end">Rp 0</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                <input type="hidden" name="keranjang" id="keranjangInput">
                <input type="hidden" name="grand_total" id="grandTotalInput">
                <input type="hidden" name="satuan_id" id="satuan_id">
                <input type="hidden" id="kode_supplier">
                {{-- Pembayaran --}}
                <div class="row g-3">
                    <div class="col-md-8"></div>
                    <div class="col-md-4">
                        {{-- Jenis Transaksi --}}
                        <div class="mb-2">
                            <select name="jenis_transaksi" id="jenis_transaksi" class="form-select2 form-select-sm"
                                required>
                                <option value="">Jenis Transaksi</option>
                                <option value="T">Tunai</option>
                                <option value="K">Kredit</option>
                            </select>
                        </div>
                        <div id="jenisBayarWrapper" hidden>
                            <div class="mb-2">
                                <select name="jenis_bayar" id="jenis_bayar" class="form-select2 form-select-sm">
                                    <option value="">Pilih Jenis Bayar</option>
                                    <option value="tunai">Cash</option>
                                    <option value="transfer">Transfer</option>
                                </select>
                            </div>
                            <div class="mb-2" id="bankPengirim">
                                <input type="text" name="bank_pengirim" id="bank_pengirim"
                                    class="form-control form-control-sm" placeholder="Bank">
                            </div>
                        </div>
                        <div class="mb-2">
                            <textarea name="keterangan" class="form-control form-control-sm" rows="2"
                                placeholder="Tambahkan catatan jika ada..."></textarea>
                        </div>
                    </div>
                </div>

                {{-- Submit --}}
                <div class="d-grid pt-4">
                    <button type="submit" class="btn btn-sm btn-primary">Simpan Penjualan</button>
                </div>
            </form>

        </div>
    </div>
    <script>
        function formatRupiah(angka) {
            let number = parseInt(angka) || 0;
            return 'Rp' + number.toLocaleString('id-ID');
        }

        function parseRupiah(str) {
            return parseInt((str || '0').toString().replace(/[^0-9]/g, '')) || 0;
        }

        function addDays(dateStr, days) {
            const d = new Date(dateStr);
            d.setDate(d.getDate() + days);
            return d.toISOString().slice(0, 10);
        }

        function hitungDiskonNominal(persen, basis) {
            persen = parseFloat(persen) || 0;
            return Math.round((persen / 100) * basis);
        }

        function hitungDiskonPersen(nominal, harga) {
            return harga > 0 ? (nominal / harga * 100).toFixed(2) : 0;
        }

        function pilihDiskonTerbaik(d1, d2) {
            const p1 = parseFloat(d1?.persentase) || 0;
            const p2 = parseFloat(d2?.persentase) || 0;

            if (p1 >= p2 && d1) return d1;
            if (p2 > p1 && d2) return d2;
            return null;
        }

        function setDiskon(persen, nominal) {
            $('#diskon1_persen').val(persen);
            $('#diskon1_nominal').val(formatRupiah(nominal));
        }
    </script>

    <script>
        $(document).ready(function() {

            setTimeout(function() {
                $('#kode_pelanggan').select2('open');
            }, 500);

            function hitungJatuhTempo() {
                const tanggalTransaksi = $('#tanggal').val();
                if (tanggalTransaksi) {
                    const tanggal = new Date(tanggalTransaksi);
                    tanggal.setDate(tanggal.getDate() + 2);
                    const jatuhTempo = tanggal.toISOString().split('T')[0];
                    $('#tanggal_kirim').val(jatuhTempo);
                }
            }

            hitungJatuhTempo();
            $('#tanggal').on('change', hitungJatuhTempo);

            function toggleJenisTransaksi() {
                if ($('#jenis_transaksi').val() === 'T') {
                    $('#jenisBayarWrapper').show();
                    $('#bankPengirim').hide();
                } else {
                    $('#jenisBayarWrapper').hide();
                    $('#jenis_bayar').val('');
                }
            }

            function toggleJenisBayar() {
                if ($('#jenis_bayar').val() === 'transfer') {
                    $('#bankPengirim').show();
                } else {
                    $('#bankPengirim').hide();
                }
            }
            toggleJenisTransaksi();
            toggleJenisBayar();

            $('#jenis_transaksi').on('change', toggleJenisTransaksi);
            $('#jenis_bayar').on('change', toggleJenisBayar);

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

            $('#kode_pelanggan').on('select2:select', function(e) {
                const d = e.params.data;
                $('#kode_wilayah').val(d.kode_wilayah);
                $('#nama_wilayah').val(d.nama_wilayah);
                $('#faktur_kredit').val(d.faktur_kredit);
                $('#max_faktur').val(d.max_faktur);
                $('#limit_pelanggan').val(formatRupiah(d.limit_pelanggan));
                $('#sisa_piutang').val(formatRupiah(d.sisa_piutang));
                $('#sisa_limit').val(formatRupiah(d.sisa_limit));

                $('#jenis_transaksi').val('').trigger('change');
                $('#kode_sales').select2('open');
                saveState();
            });

            $('#kode_sales').on('select2:select', function(e) {
                e.preventDefault();
                $('#kode_barang').select2('open');
            });

            $('#tanggal').on('change', function() {
                if ($('#kode_pelanggan').data('select2')) {
                    setTimeout(() => {
                        $('#kode_pelanggan').select2('open');
                    }, 100);
                }
            });

            $('#kode_barang').select2({
                placeholder: 'Cari barang...',
                dropdownParent: $('#kode_barang').parent(),
                ajax: {
                    url: '{{ route('getBarang') }}',
                    dataType: 'json',
                    delay: 250,
                    data: params => ({
                        q: params.term
                    }),
                    processResults: data => ({
                        results: data.results
                    }),
                    cache: true
                }
            });


            let saldoAkhirPCS = 0;

            $('#kode_barang').on('select2:select', function(e) {
                const data = e.params.data;
                saldoAkhirPCS = data.saldo_akhir || 0;

                const kode_barang = $(this).val();
                $('#satuan').html('<option value="">Memuat...</option>');

                $.get("{{ route('getSatuanBarang', '') }}/" + kode_barang, function(res) {
                    let html = '<option value="">Satuan</option>';
                    res.forEach(function(item) {
                        html += `<option value="${item.satuan}"
                            data-harga="${item.harga_jual}"
                            data-id="${item.id}"
                            data-isi="${item.isi}"
                            data-supplier="${item.kode_supplier}">
                        ${item.satuan}
                    </option>`;
                    });
                    $('#satuan').html(html);
                    $('#harga_jual').val('');
                    $('#satuan_id').val('');
                });
            }).on('select2:close', function() {
                $('#satuan').focus();
            });

            $('#satuan').change(function() {
                const selected = $(this).find(':selected');
                const harga = selected.data('harga') || 0;
                const id = selected.data('id') || 0;
                const isi = selected.data('isi') || 1;
                const kode_supplier = selected.data('supplier');

                $('#harga_jual').val(formatRupiah(harga));
                $('#satuan_id').val(id);
                $('#kode_supplier').val(kode_supplier);

                const maxQty = Math.floor(saldoAkhirPCS / isi);

                if (maxQty <= 0) {
                    Swal.fire('Stok habis!', 'Tidak ada stok tersedia dalam satuan ini.', 'warning');
                    $('#jumlah').focus();
                    // $('#jumlah').val('').attr('readonly', true);
                } else {
                    // $('#jumlah').attr('readonly', false).attr('max', maxQty);
                    $('#jumlah').focus();
                }

                hitungTotal(); // Update total
            }).on('select2:close', function() {
                $('#jumlah').focus();
            });

            function hitungTotal() {
                const harga = parseRupiah($('#harga_jual').val());
                const jumlah = parseInt($('#jumlah').val()) || 0;
                let total = harga * jumlah;

                // Kurangi diskon berjenjang (D1 → D4)
                const d1 = parseFloat($('#diskon1_persen').val()) || 0;
                const d2 = parseFloat($('#diskon2_persen').val()) || 0;
                const d3 = parseFloat($('#diskon3_persen').val()) || 0;
                const d4 = parseFloat($('#diskon4_persen').val()) || 0;

                let diskon1 = total * (d1 / 100);
                let diskon2 = (total - diskon1) * (d2 / 100);
                let diskon3 = (total - diskon1 - diskon2) * (d3 / 100);
                let diskon4 = (total - diskon1 - diskon2 - diskon3) * (d4 / 100);

                const totalDiskon = diskon1 + diskon2 + diskon3 + diskon4;
                total -= totalDiskon;

                $('#total').val(formatRupiah(total));
            }

            // function setupDiskonInputEvents() {
            //     const $hargaJual = $('#harga_jual');
            //     const $jumlah = $('#jumlah');

            //     function getBasisTotal() {
            //         const harga = parseRupiah($hargaJual.val()) || 0;
            //         const qty = parseInt($jumlah.val()) || 0;
            //         return harga * qty;
            //     }

            //     function updateDiskonPair(nominalId, persenId) {
            //         const $nominal = $(nominalId);
            //         const $persen = $(persenId);

            //         // Saat nominal diisi → hitung persen berdasarkan total
            //         $nominal.on('input', function() {
            //             const total = getBasisTotal();
            //             const nominal = parseRupiah($(this).val()) || 0;
            //             if (total > 0) {
            //                 const persen = (nominal / total) * 100;
            //                 $persen.val(persen.toFixed(2));
            //             } else {
            //                 $persen.val('');
            //             }
            //             hitungTotal(); // Pastikan total diupdate
            //         });

            //         // Saat persen diisi → hitung nominal berdasarkan total
            //         $persen.on('input', function() {
            //             const total = getBasisTotal();
            //             const persen = parseFloat($(this).val()) || 0;
            //             const nominal = (persen / 100) * total;
            //             $nominal.val(formatRupiah(nominal));
            //             hitungTotal();
            //         });
            //     }

            //     // Terapkan untuk D1 sampai D4
            //     updateDiskonPair('#diskon1_nominal', '#diskon1_persen');
            //     updateDiskonPair('#diskon2_nominal', '#diskon2_persen');
            //     updateDiskonPair('#diskon3_nominal', '#diskon3_persen');
            //     updateDiskonPair('#diskon4_nominal', '#diskon4_persen');
            // }

            function updateTotalPenjualan() {
                let subtotal = 0;
                let potongan = 0;
                let grandTotal = 0;

                keranjang.forEach(it => {
                    subtotal += it.harga_jual * it.jumlah;
                    potongan += it.total_diskon;
                    grandTotal += it.total;
                });

                $('#footerSubtotal').text(formatRupiah(subtotal));
                $('#footerPotongan').text(formatRupiah(potongan));
                $('#footerTotal').text(formatRupiah(grandTotal));
                $('#totalPenjualanDisplay').text(formatRupiah(grandTotal));
                $('#grandTotalInput').val(grandTotal);
            }

            $('#harga_jual').on('input', function() {
                let val = parseRupiah($(this).val());
                $(this).val(formatRupiah(val));
                hitungTotal();
            });

            function cekSyaratDiskon(diskon, qty, totalNominal) {
                if (diskon.tipe_syarat === 'nominal') {
                    return totalNominal >= diskon.syarat;
                } else if (diskon.tipe_syarat === 'qty') {
                    return qty >= diskon.syarat;
                }
                return false;
            }

            function updateDiskonDariSupplier() {
                // Hitung total nominal per supplier
                const totalPerSupplier = {};
                keranjang.forEach(item => {
                    const subTotal = item.harga_jual * item.jumlah;
                    const supplier = item.kode_supplier;
                    if (!supplier) return;

                    if (!totalPerSupplier[supplier]) {
                        totalPerSupplier[supplier] = 0;
                    }
                    totalPerSupplier[supplier] += subTotal;
                });

                // Ambil diskon supplier dari API
                $.getJSON(`/getDiskonStrataSemuaGlobal`, function(response) {
                    const diskonSupplierD1 = {}; // reguler
                    const diskonSupplierD2 = {}; // promo
                    const diskonSupplierD3 = {}; // cash
                    const jenis_transaksi = $('#jenis_transaksi').val();

                    response.forEach(diskon => {
                        if (!diskon.kode_barang && diskon.kode_supplier) {
                            const supplier = diskon.kode_supplier;
                            const total = totalPerSupplier[supplier] || 0;
                            const qty = countQtyPerSupplier(keranjang, supplier);

                            // Cek syarat dulu untuk semua jenis diskon
                            if (!cekSyaratDiskon(diskon, qty, total)) return;

                            if (diskon.jenis_diskon === 'd1') {
                                if (!diskonSupplierD1[supplier] || diskon.persentase >
                                    diskonSupplierD1[supplier].persentase) {
                                    diskonSupplierD1[supplier] = diskon;
                                }
                            } else if (diskon.jenis_diskon === 'd2') {
                                if (diskon.cash == 1 && jenis_transaksi !== 'T') return;
                                if (!diskonSupplierD2[supplier] || diskon.persentase >
                                    diskonSupplierD2[supplier].persentase) {
                                    diskonSupplierD2[supplier] = diskon;
                                }
                            } else if (diskon.jenis_diskon === 'd3') {
                                if (diskon.cash == 1 && jenis_transaksi !== 'T') return;
                                if (!diskonSupplierD3[supplier] || diskon.persentase >
                                    diskonSupplierD3[supplier].persentase) {
                                    diskonSupplierD3[supplier] = diskon;
                                }
                            }
                        }
                    });

                    // Terapkan diskon supplier ke keranjang
                    keranjang.forEach((item) => {
                        const supplier = item.kode_supplier;
                        if (!supplier) return;

                        const jumlahBarangSupplier = countBarangPerSupplier(keranjang, supplier);

                        // D1
                        if (diskonSupplierD1[supplier]) {
                            const diskonTotal = parseFloat(diskonSupplierD1[supplier].persentase);
                            item.diskon1_persen = parseFloat((diskonTotal / jumlahBarangSupplier)
                                .toFixed(4));
                            item.asal_diskon1 = 'supplier';
                        }

                        // D2
                        if (diskonSupplierD2[supplier]) {
                            const diskonTotal = parseFloat(diskonSupplierD2[supplier].persentase);
                            item.diskon2_persen = parseFloat((diskonTotal / jumlahBarangSupplier)
                                .toFixed(4));
                            item.asal_diskon2 = 'supplier';
                        }

                        // D3
                        if (diskonSupplierD3[supplier]) {
                            const diskonTotal = parseFloat(diskonSupplierD3[supplier].persentase);
                            item.diskon3_persen = parseFloat((diskonTotal / jumlahBarangSupplier)
                                .toFixed(4));
                            item.asal_diskon3 = 'supplier';
                        }

                        // Hitung ulang total
                        const subtotal = item.harga_jual * item.jumlah;
                        const d1 = subtotal * (item.diskon1_persen / 100);
                        const d2 = (subtotal - d1) * (item.diskon2_persen / 100);
                        const d3 = (subtotal - d1 - d2) * (item.diskon3_persen / 100);
                        const d4 = (subtotal - d1 - d2 - d3) * (item.diskon4_persen / 100);

                        item.total_diskon = d1 + d2 + d3 + d4;
                        item.total = subtotal - item.total_diskon;
                    });

                    renderKeranjang();
                    updateTotalPenjualan();
                }).fail(() => {
                    console.error('Gagal memuat diskon supplier');
                });
            }

            function hitungDiskonStrata(kode_barang, qty) {
                return new Promise((resolve, reject) => {
                    const hargaSatuan = parseRupiah($('#harga_jual').val());
                    const totalNominal = qty * hargaSatuan;

                    $.getJSON(`/getDiskonStrataSemua/${kode_barang}/${qty}/${totalNominal}`, function(
                        response) {
                        let d1 = null,
                            d2 = null,
                            d3 = null;

                        response.forEach(diskon => {
                            if (diskon.jenis_diskon === 'd1') {
                                if (cekSyaratDiskon(diskon, qty, totalNominal)) {
                                    if (!d1 || diskon.persentase > d1.persentase) {
                                        d1 = diskon;
                                    }
                                }
                            }
                            if (diskon.jenis_diskon === 'd2') {
                                if (diskon.cash == 1 && $('#jenis_transaksi').val() !== 'T')
                                    return;
                                if (cekSyaratDiskon(diskon, qty, totalNominal)) {
                                    if (!d2 || diskon.persentase > d2.persentase) {
                                        d2 = diskon;
                                    }
                                }
                            }
                            if (diskon.jenis_diskon === 'd3') {
                                if (diskon.cash == 1 && $('#jenis_transaksi').val() !== 'T')
                                    return;
                                if (cekSyaratDiskon(diskon, qty, totalNominal)) {
                                    if (!d3 || diskon.persentase > d3.persentase) {
                                        d3 = diskon;
                                    }
                                }
                            }
                        });

                        const jenisTransaksi = $('#jenis_transaksi').val();

                        const d1_persen = d1 ? parseFloat(d1.persentase) : 0;
                        const d2_persen = d2 ? parseFloat(d2.persentase) : 0;
                        const d3_persen = (d3 && (jenisTransaksi === 'T')) ? parseFloat(d3
                            .persentase) : 0;

                        $('#diskon1_persen').val(d1_persen);
                        $('#diskon2_persen').val(d2_persen);
                        $('#diskon3_persen').val(d3_persen);

                        $('#diskon1_nominal').val(formatRupiah((d1_persen / 100) * totalNominal));
                        $('#diskon2_nominal').val(formatRupiah((d2_persen / 100) * totalNominal));
                        $('#diskon3_nominal').val(formatRupiah((d3_persen / 100) * totalNominal));

                        hitungTotal();
                        resolve(d1_persen);
                    }).fail(() => {
                        $('#diskon1_persen').val(0);
                        $('#diskon2_persen').val(0);
                        $('#diskon3_persen').val(0);
                        $('#diskon1_nominal').val(formatRupiah(0));
                        $('#diskon2_nominal').val(formatRupiah(0));
                        $('#diskon3_nominal').val(formatRupiah(0));
                        hitungTotal();
                        resolve(0);
                    });
                });
            }

            function countBarangPerSupplier(keranjang, supplier) {
                return keranjang.filter(item => item.kode_supplier === supplier).length;
            }

            function countQtyPerSupplier(keranjang, supplier) {
                return keranjang
                    .filter(item => item.kode_supplier === supplier)
                    .reduce((sum, item) => sum + (item.jumlah * item.isi), 0);
            }

            // Fungsi bantu: pilih diskon terbaik berdasarkan persentase
            // function pilihDiskonTerbaik(...diskonList) {
            //     let best = null;
            //     for (const d of diskonList) {
            //         if (d && (!best || d.persentase > best.persentase)) {
            //             best = d;
            //         }
            //     }
            //     return best;
            // }

            function pilihDiskonTerbaik(d1, d2) {
                const p1 = parseFloat(d1?.persentase) || 0;
                const p2 = parseFloat(d2?.persentase) || 0;

                if (p1 >= p2 && d1) return d1;
                if (p2 > p1 && d2) return d2;
                return null;
            }

            function konversiKeSatuanBesar(totalPCS, konversiSatuan) {
                let sisaPCS = totalPCS;
                const hasil = {};

                for (const [satuan, isi] of Object.entries(konversiSatuan)) {
                    hasil[satuan] = Math.floor(sisaPCS / isi);
                    sisaPCS = sisaPCS % isi;
                }

                return hasil;
            }

            $('#jumlah, #jenis_transaksi').on('input change', function() {
                const kode_barang = $('#kode_barang').val();
                const qty = parseInt($(this).val()) || 0;
                const satuan = $('#satuan').find(':selected');
                const isi = satuan.data('isi') || 1;
                const inputElement = this; // simpan referensi input

                // if (qty <= 0) return;
                //     const totalPcs = qty * isi;

                //             if (totalPcs > saldoAkhirPCS) {
                //                 $.get(`/getKonversiSatuan/${kode_barang}`, function(konversiSatuan) {
                //                     const stokKonversi = konversiKeSatuanBesar(saldoAkhirPCS, konversiSatuan);

                //                     Swal.fire({
                //                         title: '<h5>🚫 Stok Tidak Cukup!</h5>',
                //                         icon: 'warning',
                //                         html: `
            //                 <div style="font-size: 14px; text-align: left; margin-bottom: 10px;">
            //                     <p><strong>Stok Tersedia:</strong></p>
            //                     <div style="display: grid; grid-template-columns: auto auto; gap: 5px 10px;">
            //                         ${Object.entries(stokKonversi).map(([satuanName, jumlah]) => {
            //                             return jumlah > 0
            //                                 ? `<div>${satuanName}</div><div>: ${jumlah}</div>`
            //                                 : '';
            //                         }).join('')}
            //                     </div>
            //                     <hr style="margin: 10px 0;">
            //                     <p><strong>Maksimal Stok:</strong>
            //                         <span style="color: red; font-weight: bold;">${Math.floor(saldoAkhirPCS / isi)} ${satuan.val()}</span>
            //                     </p>
            //                 </div>
            //             `,
                //             confirmButtonText: 'Saya Mengerti',
                //             customClass: {
                //                 popup: 'animated tada'
                //             }
                //         });

                //         // Hapus baris ini supaya tidak memotong input:
                //         // $(inputElement).val(maxQty || '');
                //     });
                // }

                hitungTotal();
                hitungDiskonStrata(kode_barang, qty);
            });


            let keranjang = JSON.parse(localStorage.getItem('keranjangPenjualan') || '[]');

            async function simpanKeKeranjang() {
                const kode_barang = $('#kode_barang').val();
                const nama_barang = $('#kode_barang').find(':selected').text();
                const satuan = $('#satuan').val();
                const satuan_id = $('#satuan_id').val();
                const harga_jual = parseRupiah($('#harga_jual').val());
                const jumlah = parseInt($('#jumlah').val()) || 0;
                const isi = $('#satuan').find(':selected').data('isi') || 1;

                if (!kode_barang || !satuan || jumlah <= 0) {
                    Swal.fire('Lengkapi data barang!', '', 'warning');
                    return;
                }

                // Hitung diskon dari strata barang
                const diskon1_persen = await hitungDiskonStrata(kode_barang, jumlah);
                const diskon2_persen = parseFloat($('#diskon2_persen').val()) || 0;
                const diskon3_persen = parseFloat($('#diskon3_persen').val()) || 0;
                const diskon4_persen = parseFloat($('#diskon4_persen').val()) || 0;

                const subtotal = harga_jual * jumlah;
                const d1 = subtotal * (diskon1_persen / 100);
                const d2 = (subtotal - d1) * (diskon2_persen / 100);
                const d3 = (subtotal - d1 - d2) * (diskon3_persen / 100);
                const d4 = (subtotal - d1 - d2 - d3) * (diskon4_persen / 100);

                const total_diskon = d1 + d2 + d3 + d4;
                const total = subtotal - total_diskon;

                keranjang.push({
                    kode_barang,
                    nama_barang,
                    satuan,
                    satuan_id,
                    harga_jual,
                    harga_asli: harga_jual,
                    jumlah,
                    isi,
                    subtotal,
                    diskon1_persen,
                    diskon2_persen,
                    diskon3_persen,
                    diskon4_persen,
                    total_diskon,
                    total,
                    is_promo: false,
                    kode_supplier: $('#kode_supplier').val(),

                    // 🔽 TANDAI ASAL DISKON
                    asal_diskon1: 'strata',
                    asal_diskon2: 'strata',
                    asal_diskon3: 'strata', // karena diisi oleh hitungDiskonStrata()
                    asal_diskon4: 'manual'
                });

                renderKeranjang();
                saveState();
                updateTotalPenjualan();

                // Update diskon per supplier (akan atur ulang hanya jika dari supplier)
                updateDiskonDariSupplier();

                // Reset form
                $('#kode_barang').val(null).trigger('change');
                $('#satuan').html('<option value="">Satuan</option>');
                $('#harga_jual, #jumlah, #total').val('');
                $('#diskon1_persen, #diskon2_persen, #diskon3_persen, #diskon4_persen').val('');
                $('#diskon1_nominal, #diskon2_nominal, #diskon3_nominal, #diskon4_nominal').val('');
                $('#kode_barang').select2('open');
            }

            function renderKeranjang() {
                $('#keranjangInput').val(JSON.stringify(keranjang));
                const tbody = $('#keranjangTable tbody');
                tbody.html('');

                keranjang.forEach((item, i) => {
                    const isChecked = item.is_promo ? 'checked' : '';
                    const rowClass = item.is_promo ? 'table-warning' : '';

                    tbody.append(`
                        <tr class="${rowClass}" data-index="${i}" data-supplier="${item.kode_supplier}">
                            <td class="text-center">${i + 1}</td>
                            <td class="text-start">${item.kode_barang}</td>
                            <td>${item.nama_barang}</td>
                            <td class="text-center">${item.satuan}</td>
                            <td class="text-end">${formatRupiah(item.harga_jual)}</td>
                            <td class="text-end">
                                <input type="text" class="form-control form-control-sm text-center input-jumlah" data-index="${i}" value="${item.jumlah}">
                            </td>
                            <td class="text-center">
                                <input type="text" class="form-control form-control-sm text-center input-diskon" data-type="1" data-index="${i}" value="${item.diskon1_persen || ''}">
                            </td>
                            <td class="text-center">
                                <input type="text" class="form-control form-control-sm text-center input-diskon" data-type="2" data-index="${i}" value="${item.diskon2_persen || ''}">
                            </td>
                            <td class="text-center">
                                <input type="text" class="form-control form-control-sm text-center input-diskon" data-type="3" data-index="${i}" value="${item.diskon3_persen || ''}">
                            </td>
                            <td class="text-center">
                                <input type="text" class="form-control form-control-sm text-center input-diskon" data-type="4" data-index="${i}" value="${item.diskon4_persen || ''}">
                            </td>
                            <td class="text-center">
                                <input type="checkbox" class="form-check-input input-promo" data-index="${i}" ${isChecked}>
                            </td>
                            <td class="text-end">${formatRupiah(item.total)}</td>
                            <!-- Tambahkan kolom tersembunyi untuk supplier -->
                            <td class="supplier d-none">${item.kode_supplier}</td>
                        </tr>
                    `);
                });

                saveState();
                updateTotalPenjualan();
            }

            async function recalcItem(i, skipFetchStrata = false) {
                const it = keranjang[i];
                if (it.is_promo) return;

                it.subtotal = it.harga_asli * it.jumlah;

                // Update D1 dari strata jika belum skip
                if (!skipFetchStrata) {
                    const persenD1 = await hitungDiskonStrata(it.kode_barang, it.jumlah);
                    it.diskon1_persen = persenD1;
                }

                // Gunakan diskon dari item, bukan dari input form
                let d1 = it.subtotal * (it.diskon1_persen / 100);
                let d2 = (it.subtotal - d1) * (it.diskon2_persen / 100);

                // Untuk D3: hanya aktif jika tunai
                const jenisTransaksi = $('#jenis_transaksi').val();
                let d3_persen = (jenisTransaksi === 'T') ? it.diskon3_persen : 0;
                let d3 = (it.subtotal - d1 - d2) * (d3_persen / 100);

                let d4 = (it.subtotal - d1 - d2 - d3) * (it.diskon4_persen / 100);

                it.total_diskon = d1 + d2 + d3 + d4;
                it.total = it.subtotal - it.total_diskon;

                // Update tampilan
                const $row = $('#keranjangTable tbody tr').eq(i);
                $row.find('[data-type="1"]').val(it.diskon1_persen);
                $row.find('[data-type="2"]').val(it.diskon2_persen);
                $row.find('[data-type="3"]').val(d3_persen);
                $row.find('td:eq(11)').text(formatRupiah(it.total));

                saveState();
                updateTotalPenjualan();
            }

            $('#keranjangTable tbody').on('input', '.input-diskon', function() {
                const i = $(this).data('index');
                const type = $(this).data('type');
                let val = parseFloat($(this).val()) || 0;

                val = Math.min(Math.max(val, 0), 100);

                if (type === 1) keranjang[i].diskon1_persen = val;
                if (type === 2) keranjang[i].diskon2_persen = val;
                if (type === 3) keranjang[i].diskon3_persen = val;
                if (type === 4) keranjang[i].diskon4_persen = val;

                recalcItem(i, true); // <-- skipFetchStrata = true
            });

            $('#keranjangTable tbody').on('change', '.input-promo', function() {
                const i = $(this).data('index');
                const isPromo = $(this).is(':checked');
                keranjang[i].is_promo = isPromo;

                if (isPromo) {
                    if (keranjang[i].harga_asli === undefined) {
                        keranjang[i].harga_asli = keranjang[i].harga_jual;
                    }
                    keranjang[i].harga_jual = 0;
                    keranjang[i].subtotal = 0;
                    keranjang[i].total_diskon = 0;
                    keranjang[i].total = 0;
                } else {
                    const harga = keranjang[i].harga_asli;
                    const jumlah = keranjang[i].jumlah;
                    const d1 = parseFloat(keranjang[i].diskon1_persen) || 0;
                    const d2 = parseFloat(keranjang[i].diskon2_persen) || 0;
                    const d3 = parseFloat(keranjang[i].diskon3_persen) || 0;
                    const d4 = parseFloat(keranjang[i].diskon4_persen) || 0;

                    const subtotal = harga * jumlah;
                    const diskon1 = subtotal * (d1 / 100);
                    const diskon2 = (subtotal - diskon1) * (d2 / 100);
                    const diskon3 = (subtotal - diskon1 - diskon2) * (d3 / 100);
                    const diskon4 = (subtotal - diskon1 - diskon2 - diskon3) * (d4 / 100);
                    const total_diskon = diskon1 + diskon2 + diskon3 + diskon4;
                    const total = subtotal - total_diskon;

                    keranjang[i].harga_jual = harga;
                    keranjang[i].subtotal = subtotal;
                    keranjang[i].total_diskon = total_diskon;
                    keranjang[i].total = total;
                }

                saveState();
                renderKeranjang(); // ← Hanya render ulang tampilan, jangan reset form!
            });

            $('#keranjangTable tbody').on('input', '.input-jumlah', function() {
                const i = $(this).data('index');
                const qtyBaru = parseInt($(this).val()) || 0;
                keranjang[i].jumlah = parseInt($(this).val()) || 0;
                const item = keranjang[i];
                item.jumlah = qtyBaru;
                item.subtotal = item.harga_asli * qtyBaru;

                const d1 = item.subtotal * item.diskon1_persen / 100;
                const d2 = (item.subtotal - d1) * item.diskon2_persen / 100;
                const d3 = (item.subtotal - d1 - d2) * item.diskon3_persen / 100;
                const d4 = (item.subtotal - d1 - d2 - d3) * item.diskon4_persen / 100;

                item.total_diskon = d1 + d2 + d3 + d4;
                item.total = item.subtotal - item.total_diskon;

                $(this).closest('tr').find('td:eq(11)').text(formatRupiah(item.total));

                saveState();
                updateTotalPenjualan();
                recalcItem(i);
            });

            function saveState() {
                localStorage.setItem('keranjangPenjualan', JSON.stringify(keranjang));
            }

            renderKeranjang();

            let clickCount = 0;
            let clickTimer = null;

            $('#keranjangTable tbody').on('click', 'tr', function() {
                const row = $(this);
                const index = row.index();

                clickCount++;

                if (clickCount === 3) {
                    clearTimeout(clickTimer);
                    clickCount = 0;

                    Swal.fire({
                        title: 'Hapus item ini?',
                        text: 'Data akan dihapus dari keranjang.',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Ya, hapus!',
                        cancelButtonText: 'Batal'
                    }).then(result => {
                        if (result.isConfirmed) {
                            keranjang.splice(index, 1);
                            renderKeranjang();
                            saveState();
                        }
                    });
                } else {
                    clearTimeout(clickTimer);
                    clickTimer = setTimeout(() => {
                        clickCount = 0;
                    }, 500);
                }
            });

            $('#harga_jual, #jumlah, #diskon1_persen, #diskon2_persen, #diskon3_persen, #diskon4_persen').on(
                'keydown',
                function(e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        simpanKeKeranjang();
                    }
                }).on('select2:close', function() {
                $('#kode_barang').select2('open');
            });


            // setupDiskonInputEvents();

            function clearKeranjang() {
                keranjang = [];
                localStorage.removeItem('keranjangPenjualan');
                renderKeranjang();
                updateTotalPenjualan();
            }

            $('#formPenjualan').on('submit', function(e) {
                e.preventDefault();

                Swal.fire({
                    title: 'Simpan Transaksi?',
                    text: 'Apakah kamu yakin ingin menyimpannya sekarang?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, simpan!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: $(e.target).attr('action'),
                            type: 'POST',
                            data: $(e.target).serialize(),
                            success: function(res) {
                                Swal.fire('Berhasil disimpan!', '', 'success');
                                localStorage.removeItem('keranjangPenjualan');
                                clearKeranjang();
                            },
                            error: function(xhr) {
                                Swal.fire('Gagal!', xhr.responseText, 'error');
                            }
                        });
                    }
                });
            });

            function updateDiskonForAllItems() {
                keranjang.forEach(async (item) => {
                    await hitungDiskonStrata(item.kode_barang, item.jumlah);
                    item.diskon1_persen = parseFloat($('#diskon1_persen').val()) || 0;
                    item.diskon2_persen = parseFloat($('#diskon2_persen').val()) || 0;
                    item.diskon3_persen = parseFloat($('#diskon3_persen').val()) || 0;
                });

                // Tunggu sebentar agar semua async selesai
                setTimeout(() => {
                    updateDiskonDariSupplier();
                }, 200);
            }

            $('#jenis_transaksi').change(function() {
                let val = $(this).val();
                updateDiskonForAllItems();

                let fakturBelumLunas = parseInt($('#faktur_kredit').val()) || 0;
                let maxFaktur = parseInt($('#max_faktur').val()) || 0;

                let limitRaw = $('#limit_pelanggan').val() || '0';
                let limitClean = limitRaw.replace(/[^0-9]/g, '');
                let limitKredit = parseInt(limitClean, 10) || 0;

                let totalText = $('#footerTotal').text();
                let totalClean = totalText.replace(/[^\d]/g, '');
                let totalTransaksi = parseFloat(totalClean) || 0;
                console.log({
                    jenis_transaksi: val,
                    fakturBelumLunas,
                    maxFaktur,
                    limitKredit,
                    totalTransaksi
                });

                if (val === 'K') {
                    // Cek apakah total transaksi melebihi limit kredit
                    if (totalTransaksi > limitKredit) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Limit Kredit Terlampaui!',
                            html: `Total transaksi Rp ${totalTransaksi.toLocaleString()} melebihi limit kredit Rp ${limitKredit.toLocaleString()}.<br>Harap gunakan pembayaran tunai.`,
                            confirmButtonColor: '#d33',
                            confirmButtonText: 'Mengerti'
                        }).then(() => {
                            resetPembayaran();
                        });
                        return;
                    }

                    // Peringatan jika faktur belum lunas melebihi batas, tapi tidak memblokir
                    if (fakturBelumLunas >= maxFaktur) {
                        Swal.fire({
                            icon: 'info',
                            title: 'Faktur Belum Lunas Melebihi Batas!',
                            html: `Jumlah faktur belum lunas (${fakturBelumLunas}) melebihi batas maksimum (${maxFaktur}).<br>Namun limit kredit masih mencukupi sehingga transaksi diperbolehkan.`,
                            confirmButtonColor: '#3085d6',
                            confirmButtonText: 'Lanjutkan'
                        });
                        // Tidak ada return, lanjut ke pengecekan supplier
                    }

                }

                // Tampilkan/Sembunyikan opsi pembayaran sesuai jenis transaksi
                if (val === 'T') {
                    $('#div_jenis_bayar').removeClass('d-none');
                } else {
                    $('#div_jenis_bayar').addClass('d-none');
                    $('#div_bank_pengirim').addClass('d-none');
                    $('#jenis_bayar').val('');
                    $('#bank_pengirim').val('');
                }
            });


            const savedNik = localStorage.getItem('selectedNik');
            if (savedNik) {
                $('#nik').val(savedNik).trigger('change');
            }

            $('#nik').on('change', function() {
                const nik = $(this).val();
                localStorage.setItem('selectedNik', nik);
            });

            $(document).on('keydown', function(e) {
                const kode = e.which;
                if (kode === 37) {
                    e.preventDefault();
                    $('#kode_barang').select2('open');
                } else if (kode === 39) {
                    e.preventDefault();
                    $('select[name="jenis_transaksi"]').focus();
                }
            });

        });
    </script>
@endsection
