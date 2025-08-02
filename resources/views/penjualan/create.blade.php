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

            $('#kode_barang').change(function() {
                const kode_barang = $(this).val();
                $('#satuan').html('<option value="">Memuat...</option>');

                $.get("{{ route('getSatuanBarang', '') }}/" + kode_barang, function(res) {
                    let html = '<option value="">Satuan</option>';
                    res.forEach(function(item) {
                        html +=
                            `<option value="${item.satuan}" data-harga="${item.harga_jual}" data-id="${item.id}">${item.satuan} </option>`;
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
                $('#harga_jual').val(formatRupiah(harga));
                $('#satuan_id').val(id);
            }).on('select2:close', function() {
                $('#jumlah').focus();
            });;

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

            function setupDiskonInputEvents() {
                const $hargaJual = $('#harga_jual');
                const $jumlah = $('#jumlah');

                function getBasisTotal() {
                    const harga = parseRupiah($hargaJual.val()) || 0;
                    const qty = parseInt($jumlah.val()) || 0;
                    return harga * qty;
                }

                function updateDiskonPair(nominalId, persenId) {
                    const $nominal = $(nominalId);
                    const $persen = $(persenId);

                    // Saat nominal diisi → hitung persen berdasarkan total
                    $nominal.on('input', function() {
                        const total = getBasisTotal();
                        const nominal = parseRupiah($(this).val()) || 0;
                        if (total > 0) {
                            const persen = (nominal / total) * 100;
                            $persen.val(persen.toFixed(2));
                        } else {
                            $persen.val('');
                        }
                        hitungTotal(); // Pastikan total diupdate
                    });

                    // Saat persen diisi → hitung nominal berdasarkan total
                    $persen.on('input', function() {
                        const total = getBasisTotal();
                        const persen = parseFloat($(this).val()) || 0;
                        const nominal = (persen / 100) * total;
                        $nominal.val(formatRupiah(nominal));
                        hitungTotal();
                    });
                }

                // Terapkan untuk D1 sampai D4
                updateDiskonPair('#diskon1_nominal', '#diskon1_persen');
                updateDiskonPair('#diskon2_nominal', '#diskon2_persen');
                updateDiskonPair('#diskon3_nominal', '#diskon3_persen');
                updateDiskonPair('#diskon4_nominal', '#diskon4_persen');
            }

            function updateTotalPenjualan() {
                let subtotal = 0;
                let potongan = 0;
                let grandTotal = 0;

                keranjang.forEach(it => {
                    const sub = it.harga_jual * it.jumlah;
                    subtotal += sub;
                    potongan += it.total_diskon;
                    grandTotal += it.total;
                });

                $('#footerSubtotal').text(formatRupiah(subtotal));
                $('#footerTotal').text(formatRupiah(grandTotal));
                $('#footerPotongan').text(formatRupiah(potongan));
                $('#totalPenjualanDisplay').text(formatRupiah(grandTotal));
                $('#grandTotalInput').val(grandTotal);
            }

            $('#harga_jual').on('input', function() {
                let val = parseRupiah($(this).val());
                $(this).val(formatRupiah(val));
                hitungTotal();
            });

            function hitungDiskonStrata(kode_barang, qty) {
                const hargaSatuan = parseRupiah($('#harga_jual').val());
                const totalNominal = qty * hargaSatuan;

                $.getJSON(`/getDiskonStrataSemua/${kode_barang}/${qty}/${totalNominal}`, function(response) {
                    let diskonRegulerQty = null;
                    let diskonRegulerNominal = null;
                    let diskonPromo = null;
                    let diskonCash = null;

                    response.forEach(diskon => {
                        if (diskon.jenis_diskon === 'reguler') {
                            if (diskon.tipe_syarat === 'qty') {
                                if (!diskonRegulerQty || diskon.persentase > diskonRegulerQty
                                    .persentase) {
                                    diskonRegulerQty = diskon;
                                }
                            } else if (diskon.tipe_syarat === 'nominal') {
                                if (!diskonRegulerNominal || diskon.persentase >
                                    diskonRegulerNominal.persentase) {
                                    diskonRegulerNominal = diskon;
                                }
                            }
                        }

                        if (diskon.jenis_diskon === 'promo') {
                            if (!diskonPromo || diskon.persentase > diskonPromo.persentase) {
                                diskonPromo = diskon;
                            }
                        }

                        if (diskon.cash != 0) {
                            if (!diskonCash || diskon.cash > diskonCash.cash) {
                                diskonCash = diskon;
                            }
                        }
                    });

                    // --- Pilih reguler terbaik (qty vs nominal)
                    const diskonReguler = pilihDiskonTerbaik(diskonRegulerQty, diskonRegulerNominal);
                    if (diskonReguler) {
                        const persen = parseFloat(diskonReguler.persentase) || 0;
                        const nominal = (persen / 100) * totalNominal;
                        $('#diskon1_persen').val(persen);
                        $('#diskon1_nominal').val(formatRupiah(nominal));
                    } else {
                        $('#diskon1_persen').val(0);
                        $('#diskon1_nominal').val(formatRupiah(0));
                    }

                    // --- Promo (D2)
                    if (diskonPromo) {
                        const persen = parseFloat(diskonPromo.persentase) || 0;
                        const nominal = (persen / 100) * totalNominal;
                        $('#diskon2_persen').val(persen);
                        $('#diskon2_nominal').val(formatRupiah(nominal));
                    } else {
                        $('#diskon2_persen').val(0);
                        $('#diskon2_nominal').val(formatRupiah(0));
                    }

                    // --- Cash (D3)
                    if (diskonCash) {
                        const persen = parseFloat(diskonCash.cash) || 0;
                        let nominal = (persen / 100) * totalNominal;

                        const jenisTransaksi = $('#jenis_transaksi').val();
                        if (jenisTransaksi !== 'T') {
                            persen = 0;
                            nominal = 0;
                        }

                        $('#diskon3_persen').val(persen);
                        $('#diskon3_nominal').val(formatRupiah(nominal));
                    } else {
                        $('#diskon3_persen').val(0);
                        $('#diskon3_nominal').val(formatRupiah(0));
                    }

                    hitungTotal();
                });
            }

            $('#jumlah, #jenis_transaksi').on('input change', function() {
                const qty = parseInt($('#jumlah').val()) || 0;
                const kode_barang = $('#kode_barang').val();

                hitungDiskonStrata(kode_barang, qty);
            });

            let keranjang = JSON.parse(localStorage.getItem('keranjangPenjualan') || '[]');

            function simpanKeKeranjang() {
                const kode_barang = $('#kode_barang').val();
                const nama_barang = $('#kode_barang').find(':selected').text();
                const satuan = $('#satuan').val();
                const satuan_id = $('#satuan_id').val();
                const harga_jual = parseRupiah($('#harga_jual').val());
                const jumlah = parseInt($('#jumlah').val()) || 0;

                const diskon1_persen = parseFloat($('#diskon1_persen').val()) || 0;
                const diskon2_persen = parseFloat($('#diskon2_persen').val()) || 0;
                const diskon3_persen = parseFloat($('#diskon3_persen').val()) || 0;
                const diskon4_persen = parseFloat($('#diskon4_persen').val()) || 0;

                let subtotal = harga_jual * jumlah;

                let diskon1 = subtotal * (diskon1_persen / 100);
                let diskon2 = (subtotal - diskon1) * (diskon2_persen / 100);
                let diskon3 = (subtotal - diskon1 - diskon2) * (diskon3_persen / 100);
                let diskon4 = (subtotal - diskon1 - diskon2 - diskon3) * (diskon4_persen / 100);

                let total_diskon = diskon1 + diskon2 + diskon3 + diskon4;
                let total = subtotal - total_diskon;

                if (!kode_barang || !satuan || jumlah <= 0) {
                    Swal.fire('Lengkapi data barang!', '', 'warning');
                    return;
                }

                const duplikat = keranjang.find(
                    it => it.kode_barang === kode_barang && it.satuan === satuan && !it.is_promo
                );

                if (duplikat) {
                    Swal.fire({
                        title: 'Barang sudah ada di keranjang!',
                        text: 'Edit kuantitas di tabel jika ingin mengubah nilai.',
                        icon: 'info',
                        confirmButtonText: 'OK'
                    });
                    return;
                }

                keranjang.push({
                    kode_barang,
                    nama_barang,
                    satuan,
                    satuan_id,
                    harga_jual,
                    harga_asli: harga_jual,
                    jumlah,
                    subtotal,
                    diskon1_persen,
                    diskon2_persen,
                    diskon3_persen,
                    diskon4_persen,
                    total_diskon,
                    total,
                    is_promo: false
                });

                renderKeranjang();
                saveState();
                updateTotalPenjualan();

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
                        <tr class="${rowClass}" data-index="${i}">
                            <td class="text-center">${i + 1}</td>
                            <td class="text-start">${item.kode_barang}</td>
                            <td>${item.nama_barang}</td>
                            <td class="text-center">${item.satuan}</td>
                            <td class="text-end">${formatRupiah(item.harga_jual)}</td>

                            <td class="text-end">
                                <input type="text"
                                        class="form-control form-control-sm text-center input-jumlah"
                                        data-index="${i}" value="${item.jumlah}">
                            </td>

                            <!-- D2-D4 = input agar bisa diedit -->
                            <td class="text-center">
                                <input type="text"
                                        class="form-control form-control-sm text-center input-diskon"
                                        data-type="1" data-index="${i}"
                                        value="${item.diskon1_persen || ''}">
                            </td>

                            <!-- D2-D4 = input agar bisa diedit -->
                            <td class="text-center">
                                <input type="text"
                                        class="form-control form-control-sm text-center input-diskon"
                                        data-type="2" data-index="${i}"
                                        value="${item.diskon2_persen || ''}">
                            </td>
                            <td class="text-center">
                                <input type="text"
                                        class="form-control form-control-sm text-center input-diskon"
                                        data-type="3" data-index="${i}"
                                        value="${item.diskon3_persen || ''}">
                            </td>
                            <td class="text-center">
                                <input type="text"
                                        class="form-control form-control-sm text-center input-diskon"
                                        data-type="4" data-index="${i}"
                                        value="${item.diskon4_persen || ''}">
                            </td>
                            <td class="text-center">
                                <input type="checkbox" class="form-check-input input-promo"
                                        data-index="${i}" ${isChecked}>
                            </td>
                            <td class="text-end">${formatRupiah(item.total)}</td>
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

                // Ambil ulang diskon strata jika skipFetchStrata = false
                if (!skipFetchStrata) {
                    const persenD1 = await fetchDiskonStrata(it.kode_barang, it.jumlah, it.subtotal);
                    it.diskon1_persen = persenD1;
                }

                // Hitung diskon D2-D4
                const d1 = it.subtotal * it.diskon1_persen / 100;
                const d2 = (it.subtotal - d1) * it.diskon2_persen / 100;
                const d3 = (it.subtotal - d1 - d2) * it.diskon3_persen / 100;
                const d4 = (it.subtotal - d1 - d2 - d3) * it.diskon4_persen / 100;
                it.total_diskon = d1 + d2 + d3 + d4;
                it.total = it.subtotal - it.total_diskon;

                // 🔁 Update tampilan di tabel
                const $row = $('#keranjangTable tbody tr').eq(i);
                $row.find('[data-type="1"]').val(it.diskon1_persen); // ← Update D1
                $row.find('td:eq(11)').text(formatRupiah(it.total)); // Update total

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
                    const harga = keranjang[i].harga_asli ?? keranjang[i].harga_jual;
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
                renderKeranjang();
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


            setupDiskonInputEvents();

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

            $('#jenis_transaksi').change(function() {
                const jenis = $(this).val();
                const sisaLimit = parseRupiah($('#sisa_limit').val());
                const faktur_kredit = $('#faktur_kredit').val();
                const max_faktur = $('#max_faktur').val();
                const grandTotal = keranjang.reduce((t, item) => t + (parseFloat(item.total) || 0), 0);

                if (jenis === 'K' && grandTotal > sisaLimit) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Limit Kredit Terlampaui!',
                        text: `Total penjualan (${formatRupiah(grandTotal)}) melebihi sisa limit (Rp ${formatRupiah(sisaLimit)}).`,
                        confirmButtonText: 'Mengerti',
                        confirmButtonColor: '#d33',
                    });
                    $(this).val('');
                } else {
                    if (jenis === 'K' && faktur_kredit >= max_faktur) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Peringatan!',
                            text: 'Pelanggan ini masih memiliki tagihan belum lunas melebihi batas maksimal!',
                            confirmButtonText: 'Mengerti',
                            confirmButtonColor: '#d33',
                        });
                        $(this).val('');
                    }
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
