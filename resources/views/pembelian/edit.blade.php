@extends('layouts.template')
@section('titlepage', 'Tambah Pembelian')@section('contents')
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

    <div class="container-fluid p-0 mt-2">
        <div class="col-12 col-xl-12 mx-auto">
            <div class="card shadow-sm">
                <div class="card-body">
                    <form action="{{ route('storePembelian') }}" method="POST">
                        @csrf
                        @method('POST')
                        <h4 class="text-start fw-bold mb-2 mt-3">Edit Pembelian</h4>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="form-section">
                                    <div class="row g-3 mb-2">
                                        <div class="col-md-3">
                                            <div class="col-md-12">
                                                <input type="text" name="no_faktur" class="form-control form-control-sm"
                                                    readonly value="{{ $pembelian->no_faktur }}">
                                            </div>
                                            <div class="col-md-12 mt-1">
                                                <input type="date" name="tanggal" value="{{ $pembelian->tanggal }}"
                                                    class="form-control form-control-sm">
                                            </div>
                                            <div class="col-md-12 mt-1">
                                                <input type="date" name="jatuh_tempo" value="{{ $pembelian->jatuh_tempo }}"
                                                    class="form-control form-control-sm" readonly>
                                            </div>
                                            <div class="col-md-12 mt-1">
                                                <input type="text" name="user" class="form-control form-control-sm"
                                                    value="{{ Auth::user()->name }}" readonly>
                                            </div>
                                        </div>
                                        <div class="col-md-5">
                                            <div class="col-md-12">
                                                <select id="kode_supplier" class="form-select2 form-select-sm">
                                                    <option value="">Pilih Supplier</option>
                                                    @foreach ($suppliers as $s)
                                                        <option value="{{ $s->kode_supplier }}"
                                                            data-nama="{{ $s->nama_supplier }}" data-ppn="{{ $s->ppn }}"
                                                            data-nohp="{{ $s->no_hp }}" data-tempo="{{ $s->tempo }}"
                                                            data-alamat="{{ $s->alamat }}"
                                                            @if($pembelian->kode_supplier == $s->kode_supplier) selected @endif>
                                                            {{ $s->kode_supplier }} - {{ $s->nama_supplier }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6 mt-1">
                                                    <input type="text" name="kode_supplier"
                                                        value="{{ $pembelian->kode_supplier }}"
                                                        class="form-control form-control-sm" placeholder="Kode Supplier"
                                                        readonly>
                                                </div>
                                                <div class="col-md-6 mt-1">
                                                    <input type="text" name="ppn"
                                                        value="{{ $supplier->ppn == 1 ? 'Include' : 'Exclude' }}"
                                                        class="form-control form-control-sm" placeholder="PPN" readonly>
                                                </div>
                                            </div>
                                            <div class="col-md-12 mt-1">
                                                <input type="text" name="no_hp"
                                                    value="{{ $supplier->supplier->no_hp ?? '' }}"
                                                    class="form-control form-control-sm" placeholder="No HP" readonly>
                                            </div>
                                            <div class="col-md-12 mt-1">
                                                <input type="text" name="alamat"
                                                    value="{{ $supplier->supplier->alamat ?? '' }}"
                                                    class="form-control form-control-sm" placeholder="Alamat" readonly>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="card shadow-sm bg-primary text-white p-3 h-100 rounded-3">
                                                <div class="d-flex align-items-center h-100">
                                                    <div class="me-3">
                                                        <i class="bi bi-cart-check-fill"
                                                            style="font-size: 2.5rem; color:yellow;"></i>
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

                                <div class="form-section">
                                    <div class="mt-1">
                                        <div class="mt-1">
                                            <div class="row g-1 align-items-end">
                                                <div class="col-md-3">
                                                    <select id="barang" name="barang" class="form-select2 form-select-sm">
                                                    </select>
                                                </div>

                                                <div class="col-md-1">
                                                    <select id="satuan" class="form-select2 form-select-sm">
                                                        <option value="">Satuan</option>
                                                    </select>
                                                </div>

                                                <div class="col-md-2">
                                                    <input type="text" id="harga"
                                                        class="form-control form-control-sm text-end"
                                                        placeholder="Harga Modal">
                                                </div>

                                                <div class="col-md-1">
                                                    <input type="text" id="jumlah"
                                                        class="form-control form-control-sm text-end" placeholder="Jumlah">
                                                </div>

                                                <div class="col-md-1">
                                                    <input type="text" id="diskon_persen"
                                                        class="form-control form-control-sm text-end" placeholder="%">
                                                </div>

                                                <div class="col-md-2">
                                                    <input type="text" id="potongan"
                                                        class="form-control form-control-sm text-end"
                                                        placeholder="Potongan (Rp)">
                                                </div>

                                                <div class="col-md-2">
                                                    <input type="text" id="total"
                                                        class="form-control form-control-sm text-end" placeholder="Total"
                                                        readonly>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="table-responsive mt-2">
                                        <table class="table table-bordered table-sm align-middle" id="keranjangTable">
                                            <thead class="table-light">
                                                <tr class="text-center">
                                                    <th style="width: 2%">No.</th>
                                                    <th style="width: 9%">Kode</th>
                                                    <th>Nama</th>
                                                    <th style="width: 7%">Satuan</th>
                                                    <th style="width: 7%">Jumlah</th>
                                                    <th style="width: 10%">Jumlah</th>
                                                    <th style="width: 10%">Pot.</th>
                                                    <th style="width: 10%">Total</th>
                                                    <th>Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                            <tfoot class="table-light">
                                                <tr>
                                                    <th colspan="7" class="text-end">Total</th>
                                                    <th id="footerSubtotal" class="text-end">Rp 0</th>
                                                    <th></th>
                                                </tr>
                                                <tr>
                                                    <th colspan="7" class="text-end">Total Potongan</th>
                                                    <th id="footerPotongan" class="text-end">Rp 0</th>
                                                    <th></th>
                                                </tr>
                                                <tr>
                                                    <th colspan="7" class="text-end">Subtotal</th>
                                                    <th id="footerTotal" class="text-end">Rp 0</th>
                                                    <th></th>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <input type="hidden" id="keranjangInput" name="keranjang">
                        <input type="hidden" id="id_satuan" name="id_satuan">
                        <div class="row g-2">
                            <div class="col-lg-8"></div>
                            <div class="col-lg-4">
                                <div class="form-section mt-1">
                                    <div class="row g-2">
                                        <div class="col-12">
                                            <select name="jenis_transaksi" class="form-select2 form-select-sm" required>
                                                <option value="">Jenis Transaksi</option>
                                                <option value="Tunai" @if($pembelian->jenis_transaksi == 'Tunai') selected
                                                @endif>Tunai</option>
                                                <option value="Kredit" @if($pembelian->jenis_transaksi == 'Kredit') selected
                                                @endif>Kredit</option>
                                            </select>
                                        </div>
                                        <div class="col-12">
                                            <input type="text" name="pajak" class="form-control form-control-sm text-end"
                                                placeholder="Pajak" value="{{ RupiahHelper::format($pembelian->pajak) }}">
                                        </div>
                                        <div class="col-12">
                                            <input type="text" name="biaya_lain"
                                                class="form-control form-control-sm text-end"
                                                value="{{ RupiahHelper::format($pembelian->biaya_lain) }}">
                                        </div>
                                        <div class="col-12">
                                            <input type="text" name="grand_total"
                                                class="form-control form-control-sm text-end fw-bold text-primary" readonly
                                                value="{{ RupiahHelper::format($pembelian->grand_total) }}">
                                        </div>
                                        <div class="col-12">
                                            <textarea name="keterangan" class="form-control form-control-sm"
                                                placeholder="Keterangan" rows="2">{{ $pembelian->keterangan }}</textarea>
                                        </div>
                                        <div class="col-12">
                                            <button type="submit" name="submit" class="btn btn-warning btn-sm w-100">Update
                                                Pembelian</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script>
        function formatRupiah(angka) {
            let number = parseInt(angka) || 0;
            return 'Rp' + number.toLocaleString('id-ID');
        }

        function parseRupiah(rp) {
            return parseInt(rp.replace(/[^\d]/g, '')) || 0;
        }

        function addDays(dateStr, days) {
            const d = new Date(dateStr);
            d.setDate(d.getDate() + days);
            return d.toISOString().slice(0, 10); // kembalikan 'YYYY-MM-DD'
        }
    </script>

    <script>
        $(document).ready(function () {
            let keranjang = @json($detail);
            renderKeranjang();

            $('#kode_supplier').change(function () {
                const kode_supplier = $(this).val();
                const selected = $(this).find(':selected');
                const kode = selected.val();
                const no_hp = selected.data('nohp');
                const ppn = selected.data('ppn');
                const alamat = selected.data('alamat');
                const tempo = parseInt(selected.data('tempo')) || 0;
                const ppnValue = ppn == 1 ? 'Include' : (ppn == 0 ? 'Exclude' : 'Non');
                if (!kode_supplier) {
                    $('#barang').html('<option value="">Pilih Barang</option>');
                    return;
                }

                $('#barang').html('<option value="">Memuat…</option>');

                const url = "{{ route('getBarangPembelian', ':kode_supplier') }}".replace(':kode_supplier', kode_supplier);

                $.get(url, function (res) {
                    let html = '<option value="">Pilih Barang</option>';
                    res.forEach(item => {
                        html += `<option value="${item.kode_barang}">${item.nama_barang}</option>`;
                    });
                    $('#barang').html(html);
                    $('input[name="kode_supplier"]').val(kode);
                    $('input[name="ppn"]').val(ppnValue);
                    $('input[name="no_hp"]').val(no_hp);
                    $('input[name="alamat"]').val(alamat);
                    $('input[name="alamat"]').val(alamat);
                    const tanggalTransaksi = $('input[name="tanggal"]').val();
                    if (tempo > 0 && tanggalTransaksi) {
                        const jatuhTempo = addDays(tanggalTransaksi, tempo);
                        $('input[name="jatuh_tempo"]').val(jatuhTempo);
                    } else {
                        $('input[name="jatuh_tempo"]').val('');
                    }
                    localStorage.removeItem('keranjangBelanja');
                    $('#barang').focus();
                });


                localStorage.setItem('lastSupplier', JSON.stringify({
                    kode_supplier: kode_supplier,
                    kode: kode,
                    no_hp: no_hp,
                    ppnValue: ppnValue,
                    alamat: alamat,
                    tempo: tempo
                }));
            });

            const savedSupplier = localStorage.getItem('lastSupplier');
            if (savedSupplier) {
                const data = JSON.parse(savedSupplier);
                $('#kode_supplier').val(data.kode_supplier).trigger('change');
                $('input[name="kode_supplier"]').val(data.kode);
                $('input[name="ppn"]').val(data.ppn);
                $('input[name="no_hp"]').val(data.no_hp);
                $('input[name="alamat"]').val(data.alamat);
            }

            const savedKeranjang = localStorage.getItem('keranjangBelanja');
            if (savedKeranjang) {
                keranjang = JSON.parse(savedKeranjang);
                renderKeranjang();
            }

            $('#barang').change(function () {
                const kode_barang = $(this).val();
                $('#satuan').html('<option value="">Memuat...</option>');

                $.get("{{ route('getSatuanBarang', '') }}/" + kode_barang, function (res) {
                    let html = '<option value="">Pilih Satuan</option>';
                    res.forEach(function (item) {
                        html += `<option value="${item.satuan}" data-id="${item.id}" data-harga="${item.harga_pokok}">${item.satuan}</option>`;
                    });
                    $('#satuan').html(html);
                });
            });

            $('#satuan').change(function () {
                const harga = $('option:selected', this).data('harga') || 0;
                $('#harga').val(formatRupiah(harga));
                hitungTotal();
            });

             function hitungTotal() {
                const harga = parseRupiah($('#harga').val());
                const jumlah = parseFloat($('#jumlah').val()) || 0;
                const total = harga * jumlah;

                let diskonPersen = parseFloat($('#diskon_persen').val()) || 0;
                let potongan = parseRupiah($('#potongan').val());
                let nilaiPotongan = 0;

                // Jika diskon persen sedang diinput, hitung potongan otomatis
                if ($('#diskon_persen').is(':focus') && diskonPersen > 0) {
                    nilaiPotongan = total * (diskonPersen / 100);
                    $('#potongan').val(formatRupiah(nilaiPotongan.toFixed(0)));
                }
                // Jika potongan nominal sedang diinput, hitung diskon % otomatis
                else if ($('#potongan').is(':focus') && potongan > 0) {
                    diskonPersen = (potongan / total) * 100;
                    $('#diskon_persen').val(diskonPersen.toFixed(2));
                    nilaiPotongan = potongan;
                } else {
                    // Default fallback (pakai potongan langsung jika tidak sedang input fokus)
                    nilaiPotongan = potongan;
                }

                const totalAkhir = total - nilaiPotongan;
                $('#total').val(formatRupiah(totalAkhir));
            }
            // Saat pilih satuan, ambil harga dari <option>
            $('#satuan').change(function () {
                const harga = $('option:selected', this).data('harga') || 0;
                $('#harga').val(formatRupiah(harga));
                hitungTotal();
            });

            // Harga otomatis diformat
            $('#harga').on('input', function () {
                let harga = parseRupiah($(this).val());
                $(this).val(formatRupiah(harga));
                hitungTotal();
            });

            // Jumlah barang diinput
            $('#jumlah').on('input', function () {
                hitungTotal();
            });

            // Diskon persen input (angka saja)
            $('#diskon_persen').on('input', function () {
                let val = $(this).val().replace(/[^\d.]/g, '');
                $(this).val(val);
                hitungTotal();
            });

            // Potongan nominal input → format jadi Rupiah
            $('#potongan').on('input', function () {
                let val = $(this).val().replace(/[^\d]/g, '');
                $(this).val(formatRupiah(val));
                hitungTotal();
            });

            // Tambah barang via enter
            $('#jumlah, #harga, #potongan, #diskon_persen').on('keydown', function (e) {
                if (e.key === 'Enter') {
                    e.preventDefault();

                    hitungTotal(); // Pastikan nilai total dan potongan sudah update
                    tambahKeKeranjang();
                }
            });


            function tambahKeKeranjang() {
                const kodeBarang = $('#barang').val();
                const namaBarang = $('#barang option:selected').text();
                const satuan = $('#satuan').val();
                const jumlah = parseFloat($('#jumlah').val()) || 0;
                const harga = parseRupiah($('#harga').val());
                const diskon = parseRupiah($('#potongan').val());
                const total = parseRupiah($('#total').val());
                const idSatuan = $('#satuan option:selected').data('id');

                if (!kodeBarang || !satuan || jumlah <= 0) {
                    alert('Lengkapi barang, satuan, dan jumlah.');
                    return;
                }

                // ❌ Cek apakah barang dengan kode sama sudah ada
                const sudahAda = keranjang.some(item => item.kode_barang === kodeBarang);
                if (sudahAda) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Barang sudah ada!',
                        text: 'Barang ini sudah ada di keranjang.',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        $('#barang').focus();
                    });
                    return;
                }

                // ✅ Tambahkan ke keranjang
                keranjang.push({
                    kode_barang: kodeBarang,
                    nama_barang: namaBarang,
                    satuan: satuan,
                    id_satuan: idSatuan,
                    qty: jumlah,
                    harga: harga,
                    diskon: diskon,
                    total: total
                });

                // Kosongkan input
                $('#jumlah').val('');
                $('#harga').val('');
                $('#jumlah').val('');
                $('#potongan').val(formatRupiah(0));
                $('#total').val(formatRupiah(0));

                renderKeranjang();
                $('#barang').focus();
            }

            $(document).on('keydown', function (e) {
                const kode = e.which;

                if (kode === 38) {
                    e.preventDefault();
                    $('select[id="kode_supplier"]').focus();
                } else if (kode === 37) {
                    e.preventDefault();
                    $('select[name="barang"]').focus();
                } else if (kode === 39) {
                    e.preventDefault();
                    $('select[name="jenis_transaksi"]').focus();
                }
            });

            function renderKeranjang() {
                const tbody = $('#keranjangTable tbody');
                tbody.empty();

                keranjang.forEach((item, idx) => {
                    tbody.append(`
                                <tr data-index="${idx}">
                                    <td>${idx + 1}</td>

                                    <td>${item.kode_barang}</td>
                                    <td>${item.nama_barang}</td>
                                    <td>${item.satuan}</td>
                                    <td>
                                        <input type="number"
                                            class="form-control text-center form-control-sm inputQty"
                                            data-index="${idx}"
                                            min="1"
                                            value="${item.qty}">
                                    </td>
                                    <td>
                                        <input type="text"
                                            class="form-control form-control-sm inputHarga text-end"
                                            data-index="${idx}"
                                            value="${formatRupiah(item.harga)}">
                                    </td>
                                    <td>
                                        <input type="text"
                                            class="form-control form-control-sm inputDiskon text-end"
                                            data-index="${idx}"
                                            value="${formatRupiah(item.diskon)}">
                                    </td>
                                    <td class="text-end totalRow">
                                        ${formatRupiah(item.total)}
                                    </td>
                                    <td class="text-center">
                                        <button type="button"
                                                class="btn btn-sm btn-danger btnHapus"
                                                title="Hapus">
                                            <i class="bi bi-trash-fill"></i>
                                        </button>
                                    </td>
                                </tr>
                            `);
                });

                syncKeranjang();
            }

            function syncKeranjang() {
                $('#keranjangInput').val(JSON.stringify(keranjang));
                localStorage.setItem('keranjangBelanja', JSON.stringify(keranjang));

                const totalSemua = keranjang.reduce((s, i) => s + (parseFloat(i.total) || 0), 0);
                const totalDiskon = keranjang.reduce((s, i) => s + (parseFloat(i.diskon) || 0), 0);
                const pajakNominal = parseRupiah($('input[name="pajak"]').val());
                const biayaLain = parseRupiah($('input[name="biaya_lain"]').val());

                $('#totalPenjualanDisplay').text(formatRupiah(totalSemua - totalDiskon + pajakNominal + biayaLain));
                $('#footerTotal').text(formatRupiah(totalSemua - totalDiskon));
                $('#footerSubtotal').text(formatRupiah(totalSemua));
                $('#footerPotongan').text(formatRupiah(totalDiskon));

                const grandTotal = (totalSemua - totalDiskon) + pajakNominal + biayaLain;

                $('input[name="grand_total"]').val(formatRupiah(grandTotal));
            }

            $('input[name="biaya_lain"]').on('input', function () {
                const val = parseRupiah($(this).val());
                $(this).val(formatRupiah(val));
                syncKeranjang();
            });

            $('input[name="pajak"]').on('input', function () {
                const val = parseRupiah($(this).val());
                $(this).val(formatRupiah(val));
                syncKeranjang();
            });


            $(document).on('input', '.inputQty', function () {
                const idx = $(this).data('index');
                const qty = parseFloat($(this).val()) || 0;

                keranjang[idx].qty = qty;
                keranjang[idx].total = (keranjang[idx].harga * qty) - keranjang[idx].diskon;

                // perbarui total baris & ringkasan
                $(this).closest('tr').find('.totalRow')
                    .text(formatRupiah(keranjang[idx].total));
                syncKeranjang();
            });

            $(document).on('input', '.inputHarga', function () {
                const idx = $(this).data('index');
                const harga = parseRupiah($(this).val());

                keranjang[idx].harga = harga;
                keranjang[idx].total = (harga * keranjang[idx].qty) - keranjang[idx].diskon;

                // Tulis balik ke input dalam format Rupiah
                $(this).val(formatRupiah(harga));

                // Total baris
                $(this).closest('tr').find('.totalRow')
                    .text(formatRupiah(keranjang[idx].total));

                syncKeranjang();
            });

            // Diskon berubah realtime
            $(document).on('input', '.inputDiskon', function () {
                const idx = $(this).data('index');
                const diskon = parseRupiah($(this).val());

                keranjang[idx].diskon = diskon;
                keranjang[idx].total = (keranjang[idx].harga * keranjang[idx].qty) - diskon;

                $(this).val(formatRupiah(diskon));
                $(this).closest('tr').find('.totalRow')
                    .text(formatRupiah(keranjang[idx].total));

                syncKeranjang();
            });


            $(document).on('click', '.btnHapus', function () {
                const index = $(this).closest('tr').data('index');
                keranjang.splice(index, 1);
                renderKeranjang();
            });

            $('form').on('submit', function () {
                localStorage.removeItem('keranjangBelanja');
                localStorage.removeItem('lastSupplier');
            });

        });
    </script>
@endsection
