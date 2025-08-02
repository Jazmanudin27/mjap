@extends('layouts.template')
@section('titlepage', 'Edit Retur Pembelian')@section('contents')

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
            <h4 class="mb-0 fw-semibold">Form Edit Retur Pembelian</h4>
        </div>
        <form action="{{ route('updateReturPembelian', $retur->no_retur) }}" method="POST" id="formReturPembelian">
            @csrf
            @method('POST')
            <div class="form-section">
                <div class="row g-2">
                    <div class="col-md-3">
                        <label class="form-label small">No Retur</label>
                        <input type="text" name="no_retur" class="form-control form-control-sm"
                            value="{{ $retur->no_retur }}" readonly>

                        <label class="form-label small mt-2">Tanggal Retur</label>
                        <input type="date" name="tanggal" class="form-control form-control-sm"
                            value="{{ $retur->tanggal }}" required>

                        <label class="form-label small mt-2">Jenis Retur</label>
                        <select name="jenis_retur" class="form-select form-select-sm" required>
                            <option value="PF" {{ $retur->jenis_retur == 'PF' ? 'selected' : '' }}>Potong Faktur
                            </option>
                            <option value="GB" {{ $retur->jenis_retur == 'GB' ? 'selected' : '' }}>Ganti Barang
                            </option>
                        </select>
                    </div>

                    <div class="col-md-5">
                        <label class="form-label small">Pilih Supplier</label>
                        <select name="kode_supplier" id="kode_supplier" class="form-select form-select-sm" required>
                            @foreach ($suppliers as $supplier)
                                <option value="{{ $supplier->kode_supplier }}"
                                    {{ $retur->kode_supplier == $supplier->kode_supplier ? 'selected' : '' }}>
                                    {{ $supplier->nama_supplier }}
                                </option>
                            @endforeach
                        </select>

                        <label class="form-label small mt-2">No Faktur</label>
                        <select name="no_faktur" class="form-select form-select-sm" required>
                            @foreach ($fakturs as $faktur)
                                <option value="{{ $faktur->no_faktur }}"
                                    {{ $retur->no_faktur == $faktur->no_faktur ? 'selected' : '' }}>
                                    {{ $faktur->no_faktur }}
                                </option>
                            @endforeach
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
                <div class="mt-1 mb-2">
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
                <div class="table-responsive">
                    <table class="table table-bordered table-sm" id="tabelRetur">
                        <thead class="table-light text-center">
                            <tr class="text-center">
                                <th style="width: 2%">No.</th>
                                <th style="width: 9%">Kode</th>
                                <th>Nama</th>
                                <th style="width: 7%">Jumlah</th>
                                <th style="width: 7%">Harga</th>
                                <th style="width: 10%">Total</th>
                                <th style="width: 5%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <th colspan="5" class="text-end">Total</th>
                                <th id="footerTotalRetur" class="text-end">Rp. 0</th>
                                <th></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <input type="hidden" name="keranjang" id="keranjangReturInput">
            <input type="hidden" name="total" id="totalReturInput">

            <div class="form-group mt-3">
                <label class="form-label small">Catatan</label>
                <textarea name="keterangan" class="form-control form-control-sm" rows="2">{{ $retur->keterangan }}</textarea>
            </div>

            <div class="d-grid pt-3">
                <button type="submit" class="btn btn-sm btn-primary">Update Retur</button>
            </div>
        </form>
    </div>
</div>

<script>
    function formatRupiah(angka) {
        let number = parseInt(angka) || 0;
        return 'Rp' + number.toLocaleString('id-ID');
    }

    function parseRupiah(rp) {
        if (typeof rp !== 'string') rp = String(rp || '0');
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
        const detailKeranjang = @json($detail);

        detailKeranjang.forEach((item, i) => {
            const subtotal = item.qty * item.harga_retur;
            keranjang.push({
                kode_barang: item.kode_barang,
                nama_barang: item.nama_barang,
                satuan: item.satuan,
                id_satuan: item.satuan_id,
                qty: item.qty,
                harga: item.harga_retur,
                diskon: item.diskon || 0,
                subtotal: subtotal
            });

        });

        renderKeranjang();

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
            $('#subtotal').val(formatRupiah(totalAkhir));
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

        function loadBarangSupplier(kode_supplier) {
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
            });
        }

        // Trigger manual saat pertama kali (saat edit)
        const initKodeSupplier = $('#kode_supplier').val();
        if (initKodeSupplier) {
            loadBarangSupplier(initKodeSupplier);
        }

        $('#kode_supplier').on('change', function() {
            const kode_supplier = $(this).val();
            const selected = $(this).find(':selected');
            const no_hp = selected.data('nohp');
            const ppn = selected.data('ppn');
            const alamat = selected.data('alamat');
            const tempo = parseInt(selected.data('tempo')) || 0;
            const ppnValue = ppn == 1 ? 'Include' : (ppn == 0 ? 'Exclude' : 'Non');
            const tanggalTransaksi = $('input[name="tanggal"]').val();

            const assignSupplierData = () => {
                $('input[name="ppn"]').val(ppnValue);
                $('input[name="no_hp"]').val(no_hp);
                $('input[name="alamat"]').val(alamat);

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
                    $('input[name="pajak"]').val('');
                }

                localStorage.setItem('lastSupplier', JSON.stringify({
                    kode_supplier,
                    no_hp,
                    ppnValue,
                    alamat,
                    tempo
                }));

                loadBarangSupplier(kode_supplier);
            };

            if (keranjang.length > 0) {
                Swal.fire({
                    title: 'Ganti Supplier?',
                    text: 'Pergantian supplier akan menghapus semua isi keranjang. Lanjutkan?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, lanjut',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        keranjang = [];
                        renderKeranjang();
                        $('#keranjangReturInput').val('');
                        localStorage.removeItem('keranjangBelanja');

                        assignSupplierData();
                    }
                });
            } else {
                assignSupplierData();
            }
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
                const subtotal = (hargaFinal * jumlah) - diskon;

                keranjang.push({
                    kode_barang: kodeBarang,
                    nama_barang: namaBarang,
                    satuan: satuan,
                    id_satuan: idSatuan,
                    qty: jumlah,
                    harga: hargaFinal,
                    diskon: diskon,
                    subtotal: subtotal
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
            const tbody = $('#tabelRetur tbody');
            tbody.empty();

            keranjang.forEach((item, idx) => {
                tbody.append(`
                                    <tr data-index="${idx}">
                                        <td>${idx + 1}</td>
                                        <td>${item.kode_barang}</td>
                                        <td>${item.nama_barang}</td>
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
                                        <td class="text-end totalRow">
                                            ${formatRupiah(item.subtotal)}
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
            $('#keranjangReturInput').val(JSON.stringify(keranjang));
            localStorage.setItem('keranjangBelanja', JSON.stringify(keranjang));

            const totalSemua = keranjang.reduce((s, i) => s + (parseFloat(i.subtotal) || 0), 0);
            const totalDiskon = keranjang.reduce((s, i) => s + (parseFloat(i.diskon) || 0), 0);
            const pajakPersen = parseFloat($('input[name="pajak"]').val()) || 0;
            const biayaLain = parseRupiah($('input[name="biaya_lain"]').val());

            // Update tampilan total
            $('#totalReturDisplay').text(formatRupiah(totalSemua));
            $('#footerTotalRetur').text(formatRupiah(totalSemua));
            $('#totalReturInput').val(totalSemua);
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
            keranjang[idx].subtotal = (keranjang[idx].harga * qty) - keranjang[idx].diskon;

            $(this).closest('tr').find('.totalRow')
                .text(formatRupiah(keranjang[idx].subtotal));

            syncKeranjang();
        });

        $(document).on('input', '.inputHarga', function() {
            const idx = $(this).data('index');
            const harga = parseRupiah($(this).val());

            keranjang[idx].harga = harga;
            keranjang[idx].subtotal = (harga * keranjang[idx].qty) - keranjang[idx].diskon;

            $(this).val(formatRupiah(harga));

            $(this).closest('tr').find('.totalRow')
                .text(formatRupiah(keranjang[idx].subtotal));

            syncKeranjang();
        });

        $(document).on('input', '.inputDiskon', function() {
            const idx = $(this).data('index');
            const diskon = parseRupiah($(this).val());

            keranjang[idx].diskon = diskon;
            keranjang[idx].subtotal = (keranjang[idx].harga * keranjang[idx].qty) - diskon;

            $(this).val(formatRupiah(diskon));
            $(this).closest('tr').find('.totalRow')
                .text(formatRupiah(keranjang[idx].subtotal));

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
