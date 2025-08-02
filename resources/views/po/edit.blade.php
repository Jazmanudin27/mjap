@extends('layouts.template')
@section('titlepage', 'Edit Purchase Order (PO)')
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
                    <form action="{{ route('storePO') }}" method="POST">
                        @csrf
                        @method('POST')

                        <h4 class="text-start fw-bold mb-2 mt-3">Edit Purchase Order (PO)</h4>

                        <div class="row">
                            <div class="col-lg-12">
                                <div class="form-section">
                                    <div class="row g-3 mb-2">
                                        <div class="col-md-3">
                                            <div class="col-md-12">
                                                <input type="text" name="no_po" class="form-control form-control-sm"
                                                    readonly value="{{ $po->no_po }}">
                                            </div>
                                            <div class="col-md-12 mt-1">
                                                <input type="date" name="tanggal" value="{{ $po->tanggal }}"
                                                    class="form-control form-control-sm">
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
                                                            data-nama="{{ $s->nama_supplier }}"
                                                            data-ppn="{{ $s->ppn }}" data-nohp="{{ $s->no_hp }}"
                                                            data-tempo="{{ $s->tempo }}"
                                                            data-alamat="{{ $s->alamat }}"
                                                            @if ($po->kode_supplier == $s->kode_supplier) selected @endif>
                                                            {{ $s->kode_supplier }} - {{ $s->nama_supplier }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-6 mt-1">
                                                    <input type="text" name="kode_supplier"
                                                        value="{{ $po->kode_supplier }}"
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
                                                        <i class="bi bi-file-earmark-text-fill"
                                                            style="font-size: 2.5rem; color:yellow;"></i>
                                                    </div>
                                                    <div class="flex-grow-1 text-end">
                                                        <small class="text-light">Total Estimasi PO</small>
                                                        <h2 class="mb-0"><b id="totalPenjualanDisplay">Rp. 0</b></h2>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-section">
                                    <div class="mt-1">
                                        <div class="row g-1 align-items-end">
                                            <div class="col-md-3">
                                                <select id="barang" name="barang"
                                                    class="form-select2 form-select-sm"></select>
                                            </div>
                                            <div class="col-md-1">
                                                <select id="satuan" class="form-select2 form-select-sm">
                                                    <option value="">Satuan</option>
                                                </select>
                                            </div>
                                            <div class="col-md-2">
                                                <input type="text" id="harga"
                                                    class="form-control form-control-sm text-end"
                                                    placeholder="Estimasi Harga">
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
                                                    <th style="width: 2%">Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody></tbody>
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
                                        <div class="col-12" hidden>
                                            <input type="text" name="pajak"
                                                class="form-control form-control-sm text-end"
                                                placeholder="Pajak (Opsional)"
                                                value="{{ RupiahHelper::format($po->pajak) }}">
                                        </div>
                                        <div class="col-12">
                                            <input type="text" name="potongan_claim"
                                                class="form-control form-control-sm text-end" placeholder="Potongan Lain"
                                                value="{{ RupiahHelper::format($po->potongan_claim) }}">
                                        </div>
                                        <div class="col-12">
                                            <input type="text" name="grand_total"
                                                class="form-control form-control-sm text-end fw-bold text-primary" readonly
                                                value="{{ RupiahHelper::format($po->grand_total) }}">
                                        </div>
                                        <div class="col-12">
                                            <textarea name="keterangan" class="form-control form-control-sm" placeholder="Catatan atau Keterangan"
                                                rows="2">{{ $po->keterangan }}</textarea>
                                        </div>
                                        <div class="col-12">
                                            <button type="submit" name="submit" class="btn btn-primary btn-sm w-100">
                                                Simpan PO
                                            </button>
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
    </script>

    <script>
        $(document).ready(function() {
            let keranjang = @json($detail);
            renderKeranjang();

            // Ganti supplier
            function loadBarangSupplier(kode_supplier, forceLoad = false) {
                const selected = $('#kode_supplier').find(`option[value="${kode_supplier}"]`);
                const kode = selected.val();
                const no_hp = selected.data('nohp');
                const ppn = selected.data('ppn');
                const alamat = selected.data('alamat');
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
                        html += `<option value="${item.kode_barang}">${item.nama_barang}</option>`;
                    });
                    $('#barang').html(html);

                    $('input[name="kode_supplier"]').val(kode);
                    $('input[name="ppn"]').val(ppnValue);
                    $('input[name="no_hp"]').val(no_hp);
                    $('input[name="alamat"]').val(alamat);

                    if (forceLoad) {
                        // Saat pertama load edit, tidak hapus keranjang
                    } else {
                        // Hapus keranjang hanya saat user benar-benar ubah supplier
                        localStorage.removeItem('keranjangPO');
                    }

                    $('#barang').focus();
                });

                // Simpan ke localStorage
                localStorage.setItem('lastSupplier', JSON.stringify({
                    kode_supplier: kode_supplier,
                    kode: kode,
                    no_hp: no_hp,
                    ppnValue: ppnValue,
                    alamat: alamat
                }));
            }

            // On supplier change (user click)
            $('#kode_supplier').change(function() {
                const kode_supplier = $(this).val();
                loadBarangSupplier(kode_supplier,
                    false); // false → user change supplier, keranjang dibersihkan
            });

            // Load supplier sebelumnya on page load (edit mode)
            const savedSupplier = localStorage.getItem('lastSupplier');
            if (savedSupplier) {
                const data = JSON.parse(savedSupplier);
                $('#kode_supplier').val(data.kode_supplier);
                loadBarangSupplier(data.kode_supplier, true); // true → load awal, keranjang tidak dihapus
            }

            // Load keranjang PO dari localStorage
            const savedKeranjang = localStorage.getItem('keranjangPO');
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

            $('#satuan').change(function() {
                const harga = $('option:selected', this).data('harga') || 0;
                $('#harga').val(formatRupiah(harga));
                hitungTotal();
            });

            $('#harga, #jumlah, #diskon_persen, #potongan').on('input', function() {
                hitungTotal();
            });

            function hitungTotal() {
                const harga = parseRupiah($('#harga').val());
                const jumlah = parseFloat($('#jumlah').val()) || 0;
                const total = harga * jumlah;

                let diskonPersen = parseFloat($('#diskon_persen').val()) || 0;
                let potongan = parseRupiah($('#potongan').val());
                let nilaiPotongan = 0;

                if ($('#diskon_persen').is(':focus') && diskonPersen > 0) {
                    nilaiPotongan = total * (diskonPersen / 100);
                    $('#potongan').val(formatRupiah(nilaiPotongan.toFixed(0)));
                } else if ($('#potongan').is(':focus') && potongan > 0) {
                    diskonPersen = (potongan / total) * 100;
                    $('#diskon_persen').val(diskonPersen.toFixed(2));
                    nilaiPotongan = potongan;
                } else {
                    nilaiPotongan = potongan;
                }

                const totalAkhir = total - nilaiPotongan;
                $('#total').val(formatRupiah(totalAkhir));
            }

            $('#jumlah, #harga, #potongan, #diskon_persen').on('keydown', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    hitungTotal();
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

                // PO BOLEH barang sama lebih dari sekali, jadi tidak perlu cek duplikat
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

                $('#jumlah, #harga, #potongan, #diskon_persen, #total').val('');
                renderKeranjang();
                $('#barang').focus();
            }

            // Hapus item dari keranjang
            $('#keranjangTable').on('click', '.btn-hapus', function() {
                const index = $(this).data('index');
                keranjang.splice(index, 1);
                renderKeranjang();
            });

            function renderKeranjang() {
                const tbody = $('#keranjangTable tbody');
                tbody.empty();

                let subtotal = 0;
                let totalPotongan = 0;
                let grandTotal = 0;

                keranjang.forEach((item, idx) => {
                    const totalItem = (item.qty * item.harga) - item.diskon;

                    subtotal += item.qty * item.harga;
                    totalPotongan += item.diskon;
                    grandTotal += totalItem;

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
                    ${formatRupiah(totalItem)}
                </td>
                <td class="text-center">
                    <button type="button"
                            class="btn btn-sm btn-danger btnHapus"
                            data-index="${idx}"
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
                localStorage.setItem('keranjangPO', JSON.stringify(keranjang)); // ⬅️ gunakan storage khusus PO

                const totalSemua = keranjang.reduce((s, i) => s + (parseFloat(i.qty) * parseFloat(i.harga)), 0);
                const totalDiskon = keranjang.reduce((s, i) => s + (parseFloat(i.diskon) || 0), 0);
                const pajakNominal = parseRupiah($('input[name="pajak"]').val());
                const potonganKlaim = parseRupiah($('input[name="potongan_claim"]').val());

                const subtotal = totalSemua;
                const totalSetelahDiskon = subtotal - totalDiskon;
                const grandTotal = totalSetelahDiskon + pajakNominal - potonganKlaim;

                $('#totalPenjualanDisplay').text(formatRupiah(grandTotal));
                $('#footerSubtotal').text(formatRupiah(subtotal));
                $('#footerPotongan').text(formatRupiah(totalDiskon));
                $('#footerTotal').text(formatRupiah(totalSetelahDiskon));
                $('input[name="grand_total"]').val(formatRupiah(grandTotal));
            }

            $('input[name="potongan_claim"]').on('input', function() {
                const val = parseRupiah($(this).val());
                $(this).val(formatRupiah(val));
                syncKeranjang();
            });

            $('input[name="pajak"]').on('input', function() {
                const val = parseRupiah($(this).val());
                $(this).val(formatRupiah(val));
                syncKeranjang();
            });

            $(document).on('input', '.inputQty', function() {
                const idx = $(this).data('index');
                const qty = parseFloat($(this).val()) || 0;

                keranjang[idx].qty = qty;
                keranjang[idx].total = (keranjang[idx].harga * qty) - keranjang[idx].diskon;

                $(this).closest('tr').find('.totalRow')
                    .text(formatRupiah(keranjang[idx].total));
                syncKeranjang();
            });

            $(document).on('input', '.inputHarga', function() {
                const idx = $(this).data('index');
                const harga = parseRupiah($(this).val());

                keranjang[idx].harga = harga;
                keranjang[idx].total = (harga * keranjang[idx].qty) - keranjang[idx].diskon;

                $(this).val(formatRupiah(harga));
                $(this).closest('tr').find('.totalRow')
                    .text(formatRupiah(keranjang[idx].total));

                syncKeranjang();
            });

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
                localStorage.removeItem('keranjangPO');
                localStorage.removeItem('lastSupplier');
            });

        });
    </script>
@endsection
