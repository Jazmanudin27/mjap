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

                    <div class="row g-1 align-items-end mb-2">
                        <div class="col-md-4">
                            <select id="kode_barang" name="kode_barang" class="form-select form-select-sm" tabindex="2">
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
                    data: params => ({
                        kode_pelanggan: params.term
                    }),
                    processResults: data => ({
                        results: data.results
                    }),
                    cache: true
                }
            });

            if (savedPelanggan) {
                $.ajax({
                    url: "{{ route('getPelanggan') }}",
                    data: {
                        kode_pelanggan: savedPelanggan
                    },
                    success: function(data) {
                        const opt = data.results.find(p => p.id === savedPelanggan);
                        if (opt) {
                            const newOption = new Option(opt.text, opt.id, true, true);
                            $('#kode_pelanggan').append(newOption).trigger('change');

                            // 🔽 Muat no_faktur setelah pelanggan dimuat
                            $('#no_faktur').html('<option value="">Loading...</option>');
                            $.get(`/getFakturByPelanggan/${savedPelanggan}`, function(res) {
                                let opsi = '<option value="">Pilih No Faktur</option>';
                                res.forEach(f => {
                                    opsi +=
                                        `<option value="${f.no_faktur}">${f.no_faktur}</option>`;
                                });
                                $('#no_faktur').html(opsi);

                                // 🔽 Pilih no_faktur jika tersimpan
                                if (savedFaktur) {
                                    if ($('#no_faktur option[value="' + savedFaktur + '"]')
                                        .length) {
                                        $('#no_faktur').val(savedFaktur).trigger('change');
                                    } else {
                                        localStorage.removeItem(
                                            'no_faktur'); // bersihkan jika tidak valid
                                    }
                                }
                            }).fail(() => {
                                $('#no_faktur').html('<option value="">Gagal muat</option>');
                            });
                        }
                    }
                });
            }

            if (savedKeranjang) {
                try {
                    const data = JSON.parse(savedKeranjang);
                    if (Array.isArray(data) && data.length > 0) {
                        data.forEach(item => {
                            const newRow = `
                                <tr>
                                    <td class="text-center">${$('#tabelRetur tbody tr').length + 1}</td>
                                    <td>${item.kode_barang}</td>
                                    <td>${item.nama_barang}</td>
                                    <td>
                                        <input type="text" step="0.01" class="form-control form-control-sm text-end qtyRetur"
                                            value="${parseFloat(item.qty).toFixed(2)}" data-kode="${item.kode_barang}"
                                            max="${parseFloat(item.qty).toFixed(2)}" min="0.01">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control form-control-sm text-end hargaRetur"
                                            value="${formatRupiah(item.harga)}">
                                    </td>
                                    <td class="text-end subtotal">${formatRupiah(item.subtotal)}</td>
                                </tr>
                            `;
                            $('#tabelRetur tbody').append(newRow);
                        });
                        hitungTotalRetur();
                    }
                } catch (e) {
                    console.error('Gagal parsing keranjang dari localStorage', e);
                    localStorage.removeItem('keranjang_retur');
                }
            }

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


            // Saat pelanggan dipilih
            $('#kode_pelanggan').on('select2:select', function() {
                const kode = $(this).val();
                localStorage.setItem('kode_pelanggan', kode);

                $('#no_faktur').html('<option value="">Loading...</option>');
                $.get(`/getFakturByPelanggan/${kode}`, function(res) {
                    let opsi = '<option value="">Pilih No Faktur</option>';
                    res.forEach(f => {
                        opsi += `<option value="${f.no_faktur}">${f.no_faktur}</option>`;
                    });
                    $('#no_faktur').html(opsi);
                });
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


            // $(document).on('input', '#jumlah', function() {
            //     let val = $(this).val().trim();
            //     if (val === '' || isNaN(parseFloat(val))) return;

            //     const num = parseFloat(val).toFixed(2);
            //     if (val !== num) {
            //         $(this).val(num);
            //     }
            // });
            $(document).on('input', '#jumlah, #harga_jual', function() {
                const qty = parseFloat($('#jumlah').val().trim()) || 0;
                const harga = parseRupiah($('#harga_jual').val());
                const total = qty * harga;
                $('#total').val(formatRupiah(total));
            });

            $('#satuan').on('select2:close', function() {
                $('#jumlah').focus();
            });

            // Saat input harga jual, format otomatis
            $(document).on('input', '#harga_jual', function() {
                const val = $(this).val();
                $(this).val(formatRupiah(parseRupiah(val)));
            });

            $(document).on('keydown', '#jumlah', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    simpanKeranjang();
                }
            });
            let clickCount = 0;
            let clickTimer = null;

            $(document).on('click', '#tabelRetur tbody tr', function() {
                clickCount++;

                if (clickCount === 3) {
                    Swal.fire({
                        title: 'Hapus item?',
                        text: 'Item ini akan dihapus dari keranjang',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Ya, hapus',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $(this).remove();
                            hitungTotalRetur();
                        }
                    });
                    clickCount = 0;
                    clearTimeout(clickTimer);
                }

                // reset hitungan kalau lewat 400ms tanpa klik berikutnya
                clearTimeout(clickTimer);
                clickTimer = setTimeout(function() {
                    clickCount = 0;
                }, 400);
            });

            function simpanKeranjang() {
                const kodeEl = $('#kode_barang');
                const satuanEl = $('#satuan');
                const hargaEl = $('#harga_jual');
                const jumlahEl = $('#jumlah');
                const totalEl = $('#total');

                const kode = kodeEl.val();
                const nama = kodeEl.find('option:selected').text();
                const satuan = satuanEl.val();
                const satuanText = satuanEl.find('option:selected').text();
                const qty = parseFloat(jumlahEl.val().trim()) || 0;
                const harga = parseRupiah(hargaEl.val());
                const subtotal = qty * harga;

                if (!kode || !satuan || qty <= 0 || harga <= 0) {
                    Swal.fire('Lengkapi data barang terlebih dahulu!', '', 'warning');
                    return;
                }

                // Cek duplikasi barang + satuan
                const exists = $('#tabelRetur tbody tr').filter(function() {
                    const trKode = $(this).find('.qtyRetur').data('kode');
                    const trSatuan = $(this).find('td').eq(2).text().split(' (')[1]?.replace(')', '');
                    return trKode === kode && trSatuan === satuan;
                });

                if (exists.length > 0) {
                    Swal.fire('Barang ini sudah ditambahkan!', '', 'info');
                    return;
                }

                // Tambahkan ke tabel
                const newRow = `
                        <tr>
                            <td class="text-center">${$('#tabelRetur tbody tr').length + 1}</td>
                            <td>${kode}</td>
                            <td>${nama} (${satuanText})</td>
                            <td>
                                <input type="text" step="0.01" class="form-control form-control-sm text-end qtyRetur"
                                    value="${qty.toFixed(2)}" data-kode="${kode}" max="${qty.toFixed(2)}" min="0.01">
                            </td>
                            <td>
                                <input type="text" class="form-control form-control-sm text-end hargaRetur"
                                    value="${formatRupiah(harga)}">
                            </td>
                            <td class="text-end subtotal">${formatRupiah(subtotal)}</td>
                        </tr>
                    `;
                $('#tabelRetur tbody').append(newRow);

                // Reset input
                kodeEl.val(null).trigger('change');
                satuanEl.html('<option value="">Satuan</option>');
                hargaEl.val('');
                jumlahEl.val('');
                totalEl.val('');

                // Update total
                hitungTotalRetur();

                // Fokus kembali ke pencarian barang
                setTimeout(() => $('#kode_barang').focus(), 100);
            }

            function hitungTotalRetur() {
                let total = 0;
                keranjangRetur = [];

                $('#tabelRetur tbody tr').each(function() {
                    const qtyInput = $(this).find('.qtyRetur');
                    const hargaInput = $(this).find('.hargaRetur');

                    // ✅ Gunakan parseFloat untuk baca desimal
                    let qty = parseFloat(qtyInput.val()) || 0;
                    const maxQty = parseFloat(qtyInput.attr('max')) || 0;

                    // Validasi qty desimal
                    // if (qty > maxQty) {
                    //     Swal.fire('Qty melebihi jumlah faktur!', '', 'warning');
                    //     qty = maxQty;
                    //     qtyInput.val(maxQty.toFixed(2));
                    // }

                    // if (qty < 0.01) {
                    //     Swal.fire('Qty minimal 0.01!', '', 'warning');
                    //     qty = 0.01;
                    //     qtyInput.val(0.01);
                    // }

                    const harga = parseRupiah(hargaInput.val());
                    const kode = qtyInput.data('kode');
                    const nama_barang = $(this).find('td').eq(2).text().trim();
                    const subtotal = qty * harga;

                    $(this).find('.subtotal').text(formatRupiah(subtotal));

                    if (qty > 0) {
                        keranjangRetur.push({
                            kode_barang: kode,
                            nama_barang,
                            qty: parseFloat(qty.toFixed(2)), // simpan 2 angka desimal
                            harga,
                            subtotal
                        });
                        total += subtotal;
                    }
                });

                $('#footerTotalRetur, #totalReturDisplay').text(formatRupiah(total));
                $('#totalReturInput').val(total);
                $('#keranjangReturInput').val(JSON.stringify(keranjangRetur));
                localStorage.setItem('keranjang_retur', JSON.stringify(keranjangRetur));
            }

            $('form').on('submit', function(e) {
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
