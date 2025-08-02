@extends('layouts.template')
@section('titlepage', 'Tambah Retur Pembelian')
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
                <h4 class="mb-0 fw-semibold">Form Retur Pembelian</h4>
            </div>

            <form id="formReturPembelian" action="{{ route('storeReturPembelian') }}" method="POST" autocomplete="off">
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

                        <!-- Kolom Supplier & Faktur -->
                        <div class="col-md-5">
                            <label class="form-label small">Pilih Supplier</label>
                            <select id="kode_supplier" name="kode_supplier" class="form-select form-select-sm w-100"
                                required></select>

                            <label class="form-label small mt-2">No Faktur</label>
                            <select name="no_faktur" id="no_faktur" class="form-select form-select-sm w-100" required>
                                <option value="">Pilih No Faktur</option>
                            </select>
                        </div>

                        <div class="col-md-4 mt-3 mt-md-0">
                            <div class="card shadow-sm border-0 h-50">
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
                <div class="form-section">
                    <div class="mt-1">
                        <div class="row g-1 align-items-end">
                            <div class="col-md-4">
                                <select id="barang" name="barang" class="form-select2 form-select-sm">
                                    <option value="">Pilih Barang</option>
                                </select>
                            </div>

                            <div class="col-md-2">
                                <select id="satuan" class="form-select2 form-select-sm">
                                    <option value="">Satuan</option>
                                </select>
                            </div>

                            <div class="col-md-2">
                                <input type="text" id="harga" class="form-control form-control-sm text-end"
                                    placeholder="Harga">
                            </div>

                            <div class="col-md-2">
                                <input type="number" id="jumlah" class="form-control form-control-sm text-end"
                                    placeholder="Jumlah" min="1">
                            </div>

                            <div class="col-md-2">
                                <input type="text" id="subtotal" class="form-control form-control-sm text-end"
                                    placeholder="Subtotal" readonly>
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
                                    <th style="width: 10%">Qty</th>
                                    <th style="width: 15%">Harga Retur</th>
                                    <th style="width: 15%">Subtotal</th>
                                    <th style="width: 4%">Aksi</th>
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
        $(document).ready(function() {
            let keranjangRetur = [];

            function formatRupiah(angka) {
                let number = parseInt(angka) || 0;
                return 'Rp' + number.toLocaleString('id-ID');
            }

            function parseRupiah(str) {
                return parseInt((str || '0').toString().replace(/[^0-9]/g, '')) || 0;
            }

            function hitungSubtotal() {
                const harga = parseRupiah($('#harga').val());
                const jumlah = parseFloat($('#jumlah').val()) || 0;
                const subtotal = harga * jumlah;
                $('#subtotal').val(formatRupiah(subtotal));
            }

            $('#harga, #jumlah').on('input', function() {
                hitungSubtotal();
            });

            $('#harga').on('input', function() {
                const nilai = parseRupiah($(this).val());
                $(this).val(formatRupiah(nilai));
            });

            const savedSupplier = localStorage.getItem('kode_supplier');
            const savedFaktur = localStorage.getItem('no_faktur');
            const savedKeranjang = localStorage.getItem('keranjang_retur');
            if (savedKeranjang) {
                keranjangRetur = JSON.parse(savedKeranjang);
                keranjangRetur.forEach((item, i) => {
                    const row = `
                            <tr data-kode="${item.kode_barang}">
                                <td class="text-center">${i + 1}</td>
                                <td>${item.kode_barang}</td>
                                <td>${item.nama_barang}</td>
                                <td><input type="number" class="form-control form-control-sm text-end qtyRetur" value="${item.qty}" min="1" data-kode="${item.kode_barang}" data-satuan_id="${item.satuan_id}"></td>
                                <td><input type="text" class="form-control form-control-sm text-end hargaRetur" value="${formatRupiah(item.harga)}"></td>
                                <td class="text-end subtotal">${formatRupiah(item.subtotal)}</td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-danger btn-hapus"><i class="bi bi-x"></i></button>
                                </td>
                            </tr>
                        `;
                    $('#tabelRetur tbody').append(row);
                });

                hitungTotalRetur();
            }

            $(document).on('input change', '.qtyRetur, .hargaRetur', function() {
                const hargaInput = $(this).closest('tr').find('.hargaRetur');
                const raw = parseRupiah(hargaInput.val());
                hargaInput.val(formatRupiah(raw)); // jaga agar tetap dalam format Rupiah

                hitungTotalRetur(); // hitung ulang dan simpan
            });

            $('#kode_supplier').select2({
                placeholder: 'Cari supplier…',
                ajax: {
                    url: "{{ route('getSupplier') }}",
                    dataType: 'json',
                    delay: 250,
                    data: params => ({
                        kode_supplier: params.term
                    }),
                    processResults: data => ({
                        results: data.results
                    }),
                    cache: true
                }
            });

            function tambahKeKeranjang() {
                const kodeBarang = $('#barang').val();
                const namaBarang = $('#barang option:selected').text();
                const satuanId = $('#satuan option:selected').data('id');
                const satuanText = $('#satuan option:selected').text();
                const harga = parseRupiah($('#harga').val());
                const jumlah = parseFloat($('#jumlah').val()) || 0;
                const subtotal = harga * jumlah;

                if (!kodeBarang || !satuanId || harga <= 0 || jumlah <= 0) {
                    Swal.fire('Lengkapi data barang terlebih dahulu!', '', 'warning');
                    return;
                }

                keranjangRetur.push({
                    kode_barang: kodeBarang,
                    nama_barang: namaBarang,
                    qty: jumlah,
                    harga: harga,
                    subtotal: subtotal,
                    satuan_id: satuanId
                });

                const row = `
                            <tr data-kode="${kodeBarang}">
                                <td class="text-center">${$('#tabelRetur tbody tr').length + 1}</td>
                                <td>${kodeBarang}</td>
                                <td>${namaBarang}</td>
                                <td><input type="number" class="form-control form-control-sm text-end qtyRetur" value="${jumlah}" min="1" data-satuan_id="${satuanId}" data-kode="${kodeBarang}"></td>
                                <td><input type="text" class="form-control form-control-sm text-end hargaRetur" value="${formatRupiah(harga)}"></td>
                                <td class="text-end subtotal">${formatRupiah(subtotal)}</td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-danger btn-hapus"><i class="bi bi-x"></i></button>
                                </td>
                            </tr>
                        `;


                $('#tabelRetur tbody').append(row);
                hitungTotalRetur();
                resetFormBarang();
            }

            $('#harga, #jumlah').on('keydown', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    tambahKeKeranjang();
                }
            });

            function resetFormBarang() {
                $('#barang').val('').trigger('change');
                $('#satuan').val('').trigger('change');
                $('#harga').val('');
                $('#jumlah').val('');
                $('#subtotal').val('');
                $('#barang').focus();
            }

            function hitungTotalKeranjang() {
                let total = 0;
                $('#keranjangTable tbody tr').each(function() {
                    const subtotal = parseRupiah($(this).find('td').eq(6).text());
                    total += subtotal;
                });
                $('#footerTotal').text(formatRupiah(total));
            }

            $(document).on('click', '.btn-hapus', function() {
                $(this).closest('tr').remove();
                hitungTotalKeranjang();
                hitungTotalRetur();
            });

            // Jika ada supplier tersimpan, masukkan ke select2
            if (savedSupplier) {
                $.ajax({
                    url: "{{ route('getSupplier') }}",
                    data: {
                        kode_supplier: savedSupplier
                    },
                    success: function(data) {
                        const opt = data.results.find(s => s.id === savedSupplier);
                        if (opt) {
                            const newOption = new Option(opt.text, opt.id, true, true);
                            $('#kode_supplier').append(newOption).trigger('change');
                        }
                    }
                });

                const url = "{{ route('getBarangPembelian', ':kode_supplier') }}".replace(':kode_supplier',
                    savedSupplier);
                $('#barang').html('<option value="">Memuat…</option>');

                $.get(url, function(res) {
                    let options = '<option value="">Pilih Barang</option>';
                    res.forEach(item => {
                        options +=
                            `<option value="${item.kode_barang}" data-nama="${item.nama_barang}" data-harga="${item.harga}" data-satuan_id="${item.satuan_id}">${item.nama_barang}</option>`;
                    });
                    $('#barang').html(options);
                    $('#barang').select2({
                        width: '100%' // ⬅️ penting: agar mengikuti lebar kolom
                    }); // jika pakai select2
                });
            }

            // Jika ada faktur tersimpan, load faktur
            if (savedSupplier && savedFaktur) {
                $.get(`/getFakturBySupplier/${savedSupplier}`, function(res) {
                    let opsi = '<option value="">Pilih No Faktur</option>';
                    res.forEach(f => {
                        opsi +=
                            `<option value="${f.no_faktur}" ${f.no_faktur === savedFaktur ? 'selected' : ''}>${f.no_faktur}</option>`;
                    });
                    $('#no_faktur').html(opsi);
                    $('#no_faktur').trigger('change'); // trigger load detail
                });
            }

            // Saat supplier dipilih
            $('#kode_supplier').on('select2:select', function() {
                const kode = $(this).val();
                localStorage.setItem('kode_supplier', kode);

                $('#no_faktur').html('<option value="">Loading...</option>');
                $.get(`/getFakturBySupplier/${kode}`, function(res) {
                    let opsi = '<option value="">Pilih No Faktur</option>';
                    res.forEach(f => {
                        opsi += `<option value="${f.no_faktur}">${f.no_faktur}</option>`;
                    });
                    $('#no_faktur').html(opsi);
                });
                // Load barang berdasarkan supplier
                const url = "{{ route('getBarangPembelian', ':kode_supplier') }}".replace(':kode_supplier',
                    kode);
                $('#barang').html('<option value="">Memuat…</option>');

                $.get(url, function(res) {
                    let options = '<option value="">Pilih Barang</option>';
                    res.forEach(item => {
                        options +=
                            `<option value="${item.kode_barang}" data-nama="${item.nama_barang}" data-harga="${item.harga}" data-satuan_id="${item.satuan_id}">${item.nama_barang}</option>`;
                    });
                    $('#barang').html(options);
                    $('#barang').select2({
                        width: '100%' // ⬅️ penting: agar mengikuti lebar kolom
                    }); // jika pakai select2// jika pakai select2
                });
            });

            // Saat faktur dipilih
            $('#no_faktur').on('change', function() {
                const noFaktur = $(this).val();
                if (!noFaktur) return;
                localStorage.setItem('no_faktur', noFaktur);

            });

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
                hitungTotalRetur();
            });

            $(document).on('input', '.hargaRetur', function() {
                let raw = parseRupiah($(this).val());
                $(this).val(formatRupiah(raw));
            });

            $(document).on('input change', '.qtyRetur, .hargaRetur', function() {
                hitungTotalRetur();
            });

            $(document).on('click', '.btn-hapus', function() {
                $(this).closest('tr').remove();
                hitungTotalRetur();
            });

            function hitungTotalRetur() {
                let total = 0;
                keranjangRetur = [];

                $('#tabelRetur tbody tr').each(function() {
                    const qtyInput = $(this).find('.qtyRetur');
                    const hargaInput = $(this).find('.hargaRetur');
                    const satuan_id = qtyInput.data('satuan_id');
                    const kode = qtyInput.data('kode');
                    const nama_barang = $(this).find('td').eq(2).text().trim();

                    let qty = parseFloat(qtyInput.val()) || 0;
                    let harga = parseRupiah(hargaInput.val());
                    const subtotal = qty * harga;

                    $(this).find('.subtotal').text(formatRupiah(subtotal));

                    if (qty > 0) {
                        keranjangRetur.push({
                            kode_barang: kode,
                            nama_barang,
                            qty,
                            harga,
                            subtotal,
                            satuan_id
                        });
                        total += subtotal;
                    }
                });

                $('#footerTotalRetur').text(formatRupiah(total));
                $('#totalReturDisplay').text(formatRupiah(total));
                $('#totalReturInput').val(formatRupiah(total));
                $('#keranjangReturInput').val(JSON.stringify(keranjangRetur));
                localStorage.setItem('keranjang_retur', JSON.stringify(keranjangRetur)); // ⬅️ Simpan
            }

            $('form').on('submit', function(e) {
                const supplier = $('#kode_supplier').val();
                const faktur = $('#no_faktur').val();
                const jenis = $('#jenis_retur').val();
                const total = parseInt($('#totalReturInput').val()) || 0;
                const keranjang = $('#keranjangReturInput').val();
                localStorage.removeItem('kode_supplier');
                localStorage.removeItem('no_faktur');
                localStorage.removeItem('keranjang_retur');
            });

        });
    </script>


@endsection
