@extends('layouts.template')
@section('titlepage', 'Tambah Pembelian')
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

    <div class="container-fluid p-0 mt-2">
        <div class="col-12 col-xl-12 mx-auto">
            <div class="card shadow-sm">
                <div class="card-body">
                    <form action="{{ route('storePembelian') }}" method="POST">
                        @csrf
                        <h4 class="text-start fw-bold mb-2 mt-3">Input Pembelian</h4>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="form-section">
                                    <div class="row g-3 mb-2">
                                        <div class="col-md-3">
                                            <div class="col-md-12">
                                                <input type="text" name="no_faktur" class="form-control form-control-sm"
                                                    readonly placeholder="Auto">
                                            </div>
                                            <div class="col-md-12 mt-1">
                                                <input type="date" name="tanggal" value="{{ Date('Y-m-d') }}"
                                                    class="form-control form-control-sm">
                                            </div>
                                            <div class="col-md-12 mt-1">
                                                <input type="date" name="jatuh_tempo" value="{{ Date('Y-m-d') }}"
                                                    class="form-control form-control-sm" placeholder="Jatuh Tempo">
                                            </div>
                                            <div class="col-md-12 mt-1">
                                                <input type="text" name="user" class="form-control form-control-sm"
                                                    value="{{ Auth::user()->name }}" readonly>
                                            </div>
                                        </div>
                                        <div class="col-md-5">
                                            <div class="col-md-12">
                                                <select id="kode_supplier" autofocus class="form-select2 form-select-sm"
                                                    required>
                                                    <option value="">Pilih Supplier</option>
                                                    @foreach ($suppliers as $s)
                                                        <option value="{{ $s->kode_supplier }}"
                                                            data-nama="{{ $s->nama_supplier }}"
                                                            data-ppn="{{ $s->ppn }}" data-nohp="{{ $s->no_hp }}"
                                                            data-tempo="{{ $s->tempo }}"
                                                            data-alamat="{{ $s->alamat }}">{{ $s->kode_supplier }} -
                                                            {{ $s->nama_supplier }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6 mt-1">
                                                    <input type="text" name="kode_supplier" placeholder="Kode Supplier"
                                                        class="form-control form-control-sm" readonly>
                                                </div>
                                                <div class="col-md-6 mt-1">
                                                    <input type="text" name="ppn" placeholder="PPN"
                                                        class="form-control form-control-sm" readonly>
                                                </div>
                                            </div>
                                            <div class="col-md-12 mt-1">
                                                <input type="text" name="no_hp" placeholder="No HP"
                                                    class="form-control form-control-sm" readonly>
                                            </div>
                                            <div class="col-md-12 mt-1">
                                                <input type="text" name="alamat" placeholder="Alamat"
                                                    class="form-control form-control-sm" readonly>
                                            </div>
                                            <div class="col-md-12 mt-1">
                                                <select id="no_po" name="no_po" class="form-select2 form-select-sm">
                                                    <option value="">Pilih Nomor PO</option>
                                                    {{-- Akan diisi via JS --}}
                                                </select>
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

                                {{-- Detail Produk --}}
                                <div class="form-section">
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
                                                    class="form-control form-control-sm text-end" placeholder="Harga Modal">
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
                                    <div class="table-responsive mt-2">
                                        <table class="table table-bordered table-sm align-middle" id="keranjangTable">
                                            <thead class="table-light">
                                                <tr class="text-center">
                                                    <th style="width: 2%">No.</th>
                                                    <th style="width: 9%">Kode</th>
                                                    <th>Nama</th>
                                                    <th style="width: 7%">Satuan</th>
                                                    <th style="width: 7%">Jumlah</th>
                                                    <th style="width: 10%">Harga</th>
                                                    <th style="width: 10%">Pot.</th>
                                                    <th style="width: 10%">Total</th>
                                                    <th>Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>

                                            </tbody>
                                            <tfoot class="table-light">
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
                        <div class="row g-2">
                            <div class="col-lg-8">
                                <input type="hidden" id="keranjangInput" name="keranjang">
                            </div>
                            <div class="col-lg-4">
                                <div class="form-section mt-1">
                                    <div class="row g-2">
                                        <div class="col-12" hidden>
                                            <select name="jenis_transaksi" class="form-select2 form-select-sm" required>
                                                <option value="Kredit">Kredit</option>
                                                <option value="Tunai">Tunai</option>
                                            </select>
                                        </div>
                                        <div class="col-12" id="pajakContainer">
                                            <input type="text" name="pajak"
                                                class="form-control form-control-sm text-end" placeholder="Pajak (Rp)">
                                        </div>
                                        <div class="col-12">
                                            <input type="text" name="biaya_lain"
                                                class="form-control form-control-sm text-end" placeholder="Biaya Lain">
                                        </div>
                                        <div class="col-12">
                                            <input type="text" name="potongan_claim"
                                                class="form-control form-control-sm text-end"
                                                placeholder="Potongan Klaim">
                                        </div>
                                        <div class="col-12">
                                            <input type="text" name="grand_total"
                                                class="form-control form-control-sm text-end fw-bold text-primary" readonly
                                                placeholder="Total Keseluruhan">
                                        </div>
                                        <div class="col-12">
                                            <textarea name="keterangan" class="form-control form-control-sm" placeholder="Keterangan" rows="2"></textarea>
                                        </div>
                                        <div class="col-12">
                                            <button type="submit" name="submit"
                                                class="btn btn-success btn-sm w-100">Simpan
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
        $(document).ready(function() {
            let keranjang = [];

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
            $('#satuan').change(function() {
                const harga = $('option:selected', this).data('harga') || 0;
                $('#harga').val(formatRupiah(harga));
                hitungTotal();
            });

            // Harga otomatis diformat
            $('#harga').on('input', function() {
                let harga = parseRupiah($(this).val());
                $(this).val(formatRupiah(harga));
                hitungTotal();
            });

            // Jumlah barang diinput
            $('#jumlah').on('input', function() {
                hitungTotal();
            });

            // Diskon persen input (angka saja)
            $('#diskon_persen').on('input', function() {
                let val = $(this).val().replace(/[^\d.]/g, '');
                $(this).val(val);
                hitungTotal();
            });

            // Potongan nominal input → format jadi Rupiah
            $('#potongan').on('input', function() {
                let val = $(this).val().replace(/[^\d]/g, '');
                $(this).val(formatRupiah(val));
                hitungTotal();
            });

            // Tambah barang via enter
            $('#jumlah, #harga, #potongan, #diskon_persen').on('keydown', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();

                    hitungTotal(); // Pastikan nilai total dan potongan sudah update
                    tambahKeKeranjang();
                }
            });

            $('#pajakContainer').hide();

            $('#kode_supplier').change(function() {
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

                const url = "{{ route('getBarangPembelian', ':kode_supplier') }}".replace(':kode_supplier',
                    kode_supplier);

                $.get(url, function(res) {
                    let html = '<option value="">Pilih Barang</option>';
                    res.forEach(item => {
                        html +=
                            `<option value="${item.kode_barang}">${item.nama_barang}</option>`;
                    });
                    $('#barang').html(html);
                    $('input[name="kode_supplier"]').val(kode);
                    $('input[name="ppn"]').val(ppnValue);
                    $('input[name="no_hp"]').val(no_hp);
                    $('input[name="alamat"]').val(alamat);
                    const tanggalTransaksi = $('input[name="tanggal"]').val();
                    if (tempo > 0 && tanggalTransaksi) {
                        const jatuhTempo = addDays(tanggalTransaksi, tempo);
                        $('input[name="jatuh_tempo"]').val(jatuhTempo);
                    } else {
                        $('input[name="jatuh_tempo"]').val('');
                    }
                    if (ppnValue === 'Exclude') {
                        $('#pajakContainer').show();
                    } else {
                        $('#pajakContainer').hide();
                        $('input[name="pajak"]').val();
                    }
                    $('#barang').focus();
                });

                $.get(`getPOBySupplier/${kode_supplier}`, function(data) {
                    let options = '<option value="">Pilih Nomor PO</option>';
                    data.forEach(po => {
                        options += `<option value="${po.no_po}" data-potongan="${po.potongan_claim}">
                        ${po.no_po} - ${po.tanggal}
                    </option>`;
                    });
                    $('#no_po').html(options);
                    const selectedNoPO = localStorage.getItem('selectedNoPO');
                    if (selectedNoPO) {
                        $('#no_po').val(selectedNoPO).trigger('change');
                    }
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

            $('#no_po').on('change', function() {
                const no_po = $(this).val();
                if (!no_po) return;

                $.get(`getDetailPO/${no_po}`, function(res) {
                    keranjang = res.map(item => ({
                        kode_barang: item.kode_barang,
                        nama_barang: item.nama_barang,
                        satuan: item.satuan,
                        qty: item.qty,
                        harga: item.harga,
                        satuan_id: item.satuan_id,
                        diskon: item.diskon,
                        total: (item.harga * item.qty) - item.diskon
                    }));

                    renderKeranjang();
                });

                const selected = $(this).find(':selected');
                const potonganClaim = selected.data('potongan') || 0;

                $('input[name="potongan_claim"]').val(formatRupiah(potonganClaim));
                $('#inputNoPo').val($(this).val());


                localStorage.setItem('selectedNoPO', $(this).val());
            });

            const selectedNoPO = localStorage.getItem('selectedNoPO');
            if (selectedNoPO) {
                $('#no_po').val(selectedNoPO).trigger('change');
            }

            $('input[name="potongan_claim"]').on('input', function() {
                const val = parseRupiah($(this).val());
                $(this).val(formatRupiah(val));
                syncKeranjang(); // kalau kamu punya fungsi untuk total
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

            $('#barang').change(function() {
                const kode_barang = $(this).val();
                $('#satuan').html('<option value="">Memuat...</option>');

                $.get("{{ route('getSatuanBarang', '') }}/" + kode_barang, function(res) {
                    let html = '<option value="">Pilih Satuan</option>';
                    res.forEach(function(item) {
                        html +=
                            `<option value="${item.satuan}" data-id="${item.id}" data-harga="${item.harga_pokok}">${item.satuan}</option>`;
                    });
                    $('#satuan').html(html);
                });
            });


            function tambahKeKeranjang() {
                const kodeBarang = $('#barang').val();
                const namaBarang = $('#barang option:selected').text();
                const satuan = $('#satuan').val();
                const idSatuan = $('#satuan option:selected').data('id');
                const jumlah = parseFloat($('#jumlah').val()) || 0;

                const hargaInput = parseRupiah($('#harga').val());
                const hargaDefault = parseInt($('#satuan option:selected').data('harga')) || 0;

                const diskon = parseRupiah($('#potongan').val());

                if (!kodeBarang || !satuan || jumlah <= 0) {
                    alert('Lengkapi barang, satuan, dan jumlah.');
                    return;
                }

                // Cek duplikat
                // if (keranjang.some(i => i.kode_barang === kodeBarang)) {
                //     Swal.fire({
                //         icon: 'warning',
                //         title: 'Barang sudah ada!',
                //         text: 'Barang ini sudah ada di keranjang.',
                //     }).then(() => $('#barang').focus());
                //     return;
                // }

                // ⇨  Konfirmasi jika harga berbeda  ⇦
                const lanjutTambah = (hargaFinal) => {
                    const total = (hargaFinal * jumlah) - diskon;

                    keranjang.push({
                        kode_barang: kodeBarang,
                        nama_barang: namaBarang,
                        satuan: satuan,
                        id_satuan: idSatuan,
                        qty: jumlah,
                        harga: hargaFinal,
                        diskon: diskon,
                        total: total
                    });

                    // bersihkan input
                    $('#jumlah, #harga').val('');
                    $('#potongan').val(formatRupiah(0));
                    $('#total').val(formatRupiah(0));

                    renderKeranjang();
                    $('#barang').focus();
                };

                if (hargaInput !== hargaDefault) {
                    Swal.fire({
                        title: 'Harga diubah?',
                        html: `Harga asli: <b>${formatRupiah(hargaDefault)}</b><br>
                                                                                                                        Harga baru: <b class="text-danger">${formatRupiah(hargaInput)}</b><br>
                                                                                                                        Gunakan harga baru?`,
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: 'Ya, pakai harga baru',
                        cancelButtonText: 'Tidak, pakai harga asli'
                    }).then(res => {
                        if (res.isConfirmed) {
                            lanjutTambah(hargaInput); // harga yang diinput
                        } else if (res.isDismissed) {
                            $('#harga').val(formatRupiah(hargaDefault));
                            lanjutTambah(hargaDefault); // kembali ke harga default
                        }
                    });
                } else {
                    // harga belum diubah ⇒ langsung push
                    lanjutTambah(hargaInput);
                }
            }


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
                const pajakPersen = parseFloat($('input[name="pajak"]').val()) || 0;
                const biayaLain = parseRupiah($('input[name="biaya_lain"]').val());
                const potonganKlaim = parseRupiah($('input[name="potongan_claim"]').val());

                $('#footerTotal').text(formatRupiah(totalSemua));
                $('#footerPotongan').text(formatRupiah(totalDiskon));

                const pajakNominal = (totalSemua * pajakPersen) / 100;
                const grandTotal = totalSemua + pajakNominal + biayaLain - potonganKlaim;

                $('input[name="grand_total"]').val(formatRupiah(grandTotal));
                $('#totalPenjualanDisplay').text(formatRupiah(grandTotal));
            }

            $('input[name="biaya_lain"]').on('input', function() {
                const val = parseRupiah($(this).val());
                $(this).val(formatRupiah(val));
                syncKeranjang();
            });

            $('input[name="pajak"]').on('input', function() {
                syncKeranjang();
            });


            $(document).on('input', '.inputQty', function() {
                const idx = $(this).data('index');
                const qty = parseFloat($(this).val()) || 0;

                keranjang[idx].qty = qty;
                keranjang[idx].total = (keranjang[idx].harga * qty) - keranjang[idx].diskon;

                // perbarui total baris & ringkasan
                $(this).closest('tr').find('.totalRow')
                    .text(formatRupiah(keranjang[idx].total));
                syncKeranjang();
            });

            $(document).on('input', '.inputHarga', function() {
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
            $(document).on('input', '.inputDiskon', function() {
                const idx = $(this).data('index');
                const diskon = parseRupiah($(this).val());

                keranjang[idx].diskon = diskon;
                keranjang[idx].total = (keranjang[idx].harga * keranjang[idx].qty) - diskon;

                $(this).val(formatRupiah(diskon));
                $(this).closest('tr').find('.totalRow')
                    .text(formatRupiah(keranjang[idx].total));

                syncKeranjang();
            });


            $(document).on('click', '.btnHapus', function() {
                const index = $(this).closest('tr').data('index');
                keranjang.splice(index, 1);
                renderKeranjang();
            });

            $('form').on('submit', function() {
                localStorage.removeItem('keranjangBelanja');
                localStorage.removeItem('lastSupplier');
            });

            $(document).on('keydown', function(e) {
                const kode = e.which;

                if (kode === 37) {
                    e.preventDefault();
                    $('select[name="barang"]').focus();
                } else if (kode === 39) {
                    e.preventDefault();
                    $('select[name="jenis_transaksi"]').focus();
                }
            });

        });
    </script>
@endsection
