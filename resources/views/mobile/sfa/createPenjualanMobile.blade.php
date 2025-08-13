@extends('mobile.layout')
@section('title', 'Input Penjualan')
@section('header', 'Penjualan Pelanggan')

@section('content')
    @php
        $belumLunasList = DB::table('penjualan')
            ->leftJoin(
                DB::raw(
                    '(SELECT no_faktur, SUM(jumlah) AS total_bayar FROM penjualan_pembayaran GROUP BY no_faktur) AS pbyr',
                ),
                'penjualan.no_faktur',
                '=',
                'pbyr.no_faktur',
            )
            ->leftJoin(
                DB::raw(
                    '(SELECT no_faktur, SUM(jumlah) AS total_giro FROM penjualan_pembayaran_giro GROUP BY no_faktur) AS gr',
                ),
                'penjualan.no_faktur',
                '=',
                'gr.no_faktur',
            )
            ->leftJoin(
                DB::raw(
                    '(SELECT no_faktur, SUM(jumlah) AS total_transfer FROM penjualan_pembayaran_transfer GROUP BY no_faktur) AS tf',
                ),
                'penjualan.no_faktur',
                '=',
                'tf.no_faktur',
            )
            ->select(
                'penjualan.no_faktur',
                'penjualan.tanggal',
                'penjualan.grand_total',
                DB::raw(
                    'COALESCE(pbyr.total_bayar, 0) + COALESCE(gr.total_giro, 0) + COALESCE(tf.total_transfer, 0) AS total_dibayar',
                ),
                DB::raw(
                    'penjualan.grand_total - (COALESCE(pbyr.total_bayar, 0) + COALESCE(gr.total_giro, 0) + COALESCE(tf.total_transfer, 0)) AS sisa',
                ),
            )
            ->where('penjualan.kode_pelanggan', $pelanggan->kode_pelanggan)
            ->where('penjualan.batal', 0)
            ->having('sisa', '>', 0)
            ->orderBy('penjualan.tanggal', 'desc')
            ->get();
        $jumlahBelumLunas = $belumLunasList->count();

        $limitSupplier = DB::table('pengajuan_limit_supplier')
            ->leftJoin(
                'pengajuan_limit_kredit',
                'pengajuan_limit_kredit.id',
                '=',
                'pengajuan_limit_supplier.pengajuan_id',
            )
            ->leftJoin('pengajuan_approvals', function ($join) {
                $join
                    ->on('pengajuan_approvals.pengajuan_id', '=', 'pengajuan_limit_supplier.pengajuan_id')
                    ->on('pengajuan_approvals.user_id', '=', 'pengajuan_limit_supplier.kode_supplier');
            })
            ->leftJoin('supplier', 'supplier.kode_supplier', '=', 'pengajuan_limit_supplier.kode_supplier')
            ->where('pengajuan_limit_kredit.kode_pelanggan', $pelanggan->kode_pelanggan)
            // ->where('pengajuan_approvals.disetujui', 1)
            ->select(
                'supplier.kode_supplier',
                'supplier.nama_supplier',
                'pengajuan_limit_supplier.limit_per_supplier as limit',
                'pengajuan_limit_supplier.sisa_limit',
            )
            ->get()
            ->keyBy('kode_supplier');
        // dd($limitSupplier);
    @endphp
    <div class="container py-3">
        <div class="card shadow-sm border-0 rounded-4 mb-3 text-white" style="background: #0059ff; background-size: cover;">
            <div class="card-body rounded-4"
                style="background-image: url('{{ $pelanggan->foto ? asset('storage/pelanggan/' . $pelanggan->foto) : '' }}'); background-size: cover; background-position: center;">
                <div class="bg-dark bg-opacity-50 p-3 rounded-4">
                    <div class="fw-bold fs-6">{{ $pelanggan->kode_pelanggan }}</div>
                    <div class="fw-bold fs-5">{{ $pelanggan->nama_pelanggan }}</div>
                    <div>{{ $pelanggan->alamat_toko }}</div>
                    <div class="mt-2">Limit Kredit:
                        {{ number_format(getSisaLimitKreditPelanggan($pelanggan->kode_pelanggan)) }}</div>
                    <div>Jumlah Faktur Max: {{ number_format($pelanggan->max_faktur) }}</div>
                    <div class="fw-semibold text-white">Faktur Belum Lunas: {{ $jumlahBelumLunas }}</div>
                </div>
            </div>
        </div>

        @if ($limitSupplier->count())
            <div class="card shadow-sm border-0 rounded-4 mb-3">
                <div class="card-body">
                    <div class="fw-bold mb-3 fs-5">Limit Supplier</div>
                    @foreach ($limitSupplier as $item)
                        @php
                            $terpakai = $item->limit - $item->sisa_limit;
                            $persenTerpakai = $item->limit > 0 ? ($terpakai / $item->limit) * 100 : 0;
                            $warnaProgress =
                                $persenTerpakai < 60
                                    ? 'bg-success'
                                    : ($persenTerpakai < 90
                                        ? 'bg-warning'
                                        : 'bg-danger');
                        @endphp
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <div class="fw-semibold">{{ $item->nama_supplier }}</div>
                                <span class="badge rounded-pill {{ $warnaProgress }}">
                                    {{ number_format($persenTerpakai, 0) }}%
                                </span>
                            </div>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar {{ $warnaProgress }}" role="progressbar"
                                    style="width: {{ $persenTerpakai }}%" aria-valuenow="{{ $persenTerpakai }}"
                                    aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                            <div class="d-flex justify-content-between mt-1">
                                <small class="text-muted">Limit: Rp {{ number_format($item->limit) }}</small>
                                <small class="text-muted">Sisa: Rp {{ number_format($item->sisa_limit) }}</small>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
        <input type="hidden" id="max_faktur" value="{{ $pelanggan->max_faktur }}">
        <input type="hidden" id="faktur_belum_lunas" value="{{ $jumlahBelumLunas }}">
        <input type="hidden" id="limit_kredit" value="{{ getSisaLimitKreditPelanggan($pelanggan->kode_pelanggan) }}">
        <form id="formPenjualan" action="{{ route('storePenjualanMobile') }}" method="POST">
            @csrf
            <input type="hidden" name="kode_pelanggan" value="{{ $pelanggan->kode_pelanggan }}">
            <input type="hidden" name="satuan_id" id="satuan_id">
            <input type="hidden" name="keranjang" id="keranjangInput">
            <input type="hidden" name="grand_total" id="grandTotalInput">
            <input type="hidden" name="satuan_id" id="satuan_id">
            <input type="hidden" name="kode_supplier" id="kode_supplier">
            <div class="card border-0 shadow-sm rounded-4 mb-2">
                <div class="card-body">
                    <h6 class="fw-bold text-primary mb-2"><i class="bi bi-box-seam me-1"></i> Input Produk</h6>
                    <div class="mb-2">
                        <label class="form-label">Pilih Barang</label>
                        <select id="kode_barang" name="kode_barang" class="form-select form-select-sm"></select>
                    </div>
                    <div class="row g-2">
                        <div class="col-4">
                            <div class="mb-2">
                                <label class="form-label">Satuan</label>
                                <select id="satuan" class="form-select form-select-sm">
                                    <option value="">Satuan</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-5">
                            <div class="mb-2">
                                <label class="form-label">Harga Jual</label>
                                <input type="text" id="harga_jual" name="harga_jual"
                                    class="form-control form-control-sm text-end" placeholder="Rp 0" readonly>
                            </div>
                        </div>

                        <div class="col-3">
                            <div class="mb-2">
                                <label class="form-label">Qty</label>
                                <input type="number" name="jumlah" id="jumlah"
                                    class="form-control form-control-sm text-end" placeholder="Qty">
                            </div>
                        </div>
                    </div>

                    <div class="row align-items-center mb-2 mt-1" hidden>
                        <div class="col-2">
                            <div class="input-group input-group-sm">
                                <span class="input-group-text">D1</span>
                                <input type="text" id="diskon1_nominal" placeholder="Rp. 0"
                                    class="form-control text-end">
                            </div>
                        </div>
                        <div class="col-1">
                            <input type="text" id="diskon1_persen" placeholder="%" class="form-control text-end">
                        </div>
                        <div class="col-2">
                            <div class="input-group input-group-sm">
                                <span class="input-group-text">D2</span>
                                <input type="text" id="diskon2_nominal" placeholder="Rp. 0"
                                    class="form-control text-end">
                            </div>
                        </div>
                        <div class="col-1">
                            <input type="text" id="diskon2_persen" placeholder="%" class="form-control text-end">
                        </div>
                        <div class="col-2">
                            <div class="input-group input-group-sm">
                                <span class="input-group-text">D3</span>
                                <input type="text" id="diskon3_nominal" placeholder="Rp. 0"
                                    class="form-control text-end">
                            </div>
                        </div>
                        <div class="col-1">
                            <input type="text" id="diskon3_persen" placeholder="%" class="form-control text-end">
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
                    <div class="d-grid mt-4">
                        <button type="button" class="btn btn-sm btn-primary rounded-3" id="btnTambahProduk">
                            <i class="bi bi-plus-circle me-1"></i> Tambah Data
                        </button>
                    </div>
                </div>
            </div>
            <div class="card border-0 shadow-sm rounded-4 mb-2" id="keranjangCard">
                <div class="card-body" style="zoom:95%">
                    <h6 class="fw-bold mb-2"><i class="bi bi-cart4 me-1"></i> Keranjang Barang</h6>
                    <div class="table-responsive">
                        <table class="table table-sm align-middle mb-0" id="keranjangTable">
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-2 text-end fw-bold">
                        Total: <span id="footerTotal">Rp 0</span>
                    </div>
                </div>
            </div>
            <div class="card border-0 shadow-sm rounded-4 mb-2">
                <div class="card-body">
                    {{-- Jenis Transaksi --}}
                    <div class="mb-2">
                        <label class="form-label">Jenis Transaksi</label>
                        <select name="jenis_transaksi" id="jenis_transaksi" class="form-select form-select-sm" required>
                            <option value="">Pilih</option>
                            <option value="T">Tunai</option>
                            <option value="K">Kredit</option>
                        </select>
                    </div>

                    {{-- Jenis Bayar - muncul hanya jika tunai --}}
                    <div class="mb-2 d-none" id="div_jenis_bayar">
                        <label class="form-label">Jenis Bayar</label>
                        <select name="jenis_bayar" id="jenis_bayar" class="form-select form-select-sm">
                            <option value="">Pilih</option>
                            <option value="tunai">Cash</option>
                            <option value="transfer">Transfer</option>
                        </select>
                    </div>

                    {{-- Bank Pengirim - muncul jika transfer --}}
                    <div class="mb-2 d-none" id="div_bank_pengirim">
                        <label class="form-label">Bank Pengirim</label>
                        <input type="text" name="bank_pengirim" id="bank_pengirim"
                            class="form-control form-control-sm" placeholder="Nama Bank">
                    </div>

                    {{-- Keterangan --}}
                    <div class="mb-2">
                        <label class="form-label">Keterangan</label>
                        <textarea name="keterangan" class="form-control form-control-sm" rows="2"
                            placeholder="Tulis keterangan jika ada..."></textarea>
                    </div>

                    <button type="submit" class="btn btn-sm btn-success w-100 py-2">
                        <i class="bi bi-save me-1"></i> Simpan Penjualan
                    </button>
                </div>
            </div>
        </form>
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
                let fakturBelumLunas = parseInt($('#faktur_belum_lunas').val()) || 0;
                let maxFaktur = parseInt($('#max_faktur').val()) || 0;

                let limitRaw = $('#limit_kredit').val() || '0';
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
                    if (totalTransaksi > limitKredit) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Limit Kredit Terlampaui!',
                            html: `Total transaksi Rp ${totalTransaksi.toLocaleString()} melebihi limit kredit Rp ${limitKredit.toLocaleString()}.<br>Harap gunakan pembayaran tunai.`,
                            confirmButtonColor: '#3085d6',
                            confirmButtonText: 'OK'
                        }).then(() => {
                            resetPembayaran();
                        });
                        return;
                    }

                    if (fakturBelumLunas >= maxFaktur) {
                        Swal.fire({
                            icon: 'info',
                            title: 'Faktur Belum Lunas Melebihi Batas!',
                            html: `Jumlah faktur belum lunas (${fakturBelumLunas}) melebihi batas maksimum (${maxFaktur}).<br>Namun limit kredit masih mencukupi sehingga transaksi diperbolehkan.`,
                            confirmButtonColor: '#3085d6',
                            confirmButtonText: 'Lanjutkan'
                        });
                        // Tidak ada return, tetap lanjut
                    }

                    let supplierTotals = {};

                    keranjang.forEach(item => {
                        if (!supplierTotals[item.kode_supplier]) {
                            supplierTotals[item.kode_supplier] = 0;
                        }
                        supplierTotals[item.kode_supplier] += item.total;
                    });

                    let supplierOverLimit = [];
                    for (let supplierCode in supplierTotals) {
                        let supplierLimitData = limitSupplier[supplierCode];
                        if (!supplierLimitData) continue;

                        let sisaLimit = parseInt(supplierLimitData.sisa_limit) || 0;
                        let totalPerSupplier = supplierTotals[supplierCode];

                        if (totalPerSupplier > sisaLimit) {
                            supplierOverLimit.push({
                                nama_supplier: supplierLimitData.nama_supplier,
                                total: totalPerSupplier,
                                limit: sisaLimit
                            });
                        }
                    }

                    if (supplierOverLimit.length > 0) {
                        let htmlDetail = supplierOverLimit.map(sup =>
                            `<b>${sup.nama_supplier}</b>: Total Rp ${sup.total.toLocaleString()} (Limit Rp ${sup.limit.toLocaleString()})`
                        ).join('<br>');

                        Swal.fire({
                            icon: 'warning',
                            title: 'Limit Supplier Terlampaui!',
                            html: `Ada supplier yang total transaksinya melebihi limit:<br><br>${htmlDetail}<br><br>Harap gunakan pembayaran tunai.`,
                            confirmButtonColor: '#3085d6',
                            confirmButtonText: 'OK'
                        }).then(() => {
                            resetPembayaran();
                        });
                        return;
                    }
                }

                // Tampilkan/Sembunyikan Opsi Pembayaran
                if (val === 'T') {
                    $('#div_jenis_bayar').removeClass('d-none');
                } else {
                    $('#div_jenis_bayar').addClass('d-none');
                    $('#div_bank_pengirim').addClass('d-none');
                    $('#jenis_bayar').val('');
                    $('#bank_pengirim').val('');
                }
            });

            // Fungsi reset input pembayaran
            function resetPembayaran() {
                $('#jenis_transaksi').val('');
                $('#div_jenis_bayar').addClass('d-none');
                $('#div_bank_pengirim').addClass('d-none');
                $('#jenis_bayar').val('');
                $('#bank_pengirim').val('');
            }

            $('#jenis_bayar').change(function() {
                let val = $(this).val();

                if (val === 'transfer') {
                    $('#div_bank_pengirim').removeClass('d-none');
                } else {
                    $('#div_bank_pengirim').addClass('d-none');
                    $('#bank_pengirim').val('');
                }
            });

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
                saldoAkhirPCS = data.saldo_akhir || 0; // Simpan stok dalam PCS

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
                    $('#jumlah').val('').attr('readonly', true);
                } else {
                    $('#jumlah').attr('readonly', false).attr('max', maxQty);
                    $('#jumlah').focus();
                }

                hitungTotal(); // Update total
            }).on('select2:close', function() {
                $('#jumlah').focus();
            });;

            function hitungTotal() {
                const harga = parseRupiah($('#harga_jual').val());
                const jumlah = parseInt($('#jumlah').val()) || 0;
                const total = (harga * jumlah);
                $('#total').val(formatRupiah(total));
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

                if (qty <= 0) return;
                const totalPcs = qty * isi;
                if (totalPcs > saldoAkhirPCS) {
                    $.get(`/getKonversiSatuan/${kode_barang}`, function(konversiSatuan) {

                        const stokKonversi = konversiKeSatuanBesar(saldoAkhirPCS, konversiSatuan);

                        let stokTersediaHTML = '<p><strong>Stok tersedia:</strong></p><ul>';
                        for (const [satuanName, jumlah] of Object.entries(stokKonversi)) {
                            if (jumlah > 0) {
                                stokTersediaHTML += `<li>${jumlah} ${satuanName}</li>`;
                            }
                        }
                        stokTersediaHTML += '</ul>';

                        Swal.fire({
                            title: '<h5>🚫 Stok Tidak Cukup!</h5>',
                            icon: 'warning',
                            html: `
                                <div style="font-size: 14px; text-align: left; margin-bottom: 10px;">
                                    <p><strong>Stok Tersedia:</strong></p>
                                    <div style="display: grid; grid-template-columns: auto auto; gap: 5px 10px;">
                                        ${Object.entries(stokKonversi).map(([satuanName, jumlah]) => {
                                            if (jumlah > 0) {
                                                return `<div>${satuanName}</div><div>: ${jumlah}</div>`;
                                            } else {
                                                return '';
                                            }
                                        }).join('')}
                                    </div>
                                    <hr style="margin: 10px 0;">
                                    <p><strong>Maksimal Input:</strong>
                                        <span style="color: red; font-weight: bold;">${Math.floor(saldoAkhirPCS / isi)} ${satuan.val()}</span>
                                    </p>
                                </div>
                            `,
                            confirmButtonText: 'Saya Mengerti',
                            customClass: {
                                popup: 'animated tada'
                            }
                        });

                        const maxQty = Math.floor(saldoAkhirPCS / isi);
                        $(this).val(maxQty || '');
                    });
                }

                hitungTotal();
                hitungDiskonStrata(kode_barang, qty);
            });

            $('#harga_jual').on('input', function() {
                let val = parseRupiah($(this).val());
                $(this).val(formatRupiah(val));
                hitungTotal();
            });

            $('#btnTambahProduk').on('click', function() {
                simpanKeKeranjang();
            });

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

            let keranjang = JSON.parse(localStorage.getItem('keranjangInput') || '[]');
            const limitSupplier = @json($limitSupplier);

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

                    // Hitung subtotal dan total diskon
                    const subtotal = item.harga_jual * item.jumlah;
                    const diskon1 = subtotal * (item.diskon1_persen / 100);
                    const diskon2 = (subtotal - diskon1) * (item.diskon2_persen / 100);
                    const diskon3 = (subtotal - diskon1 - diskon2) * (item.diskon3_persen / 100);
                    const diskon4 = (subtotal - diskon1 - diskon2 - diskon3) * (item.diskon4_persen / 100);
                    const totalDiskon = diskon1 + diskon2 + diskon3 + diskon4;
                    const totalSetelahDiskon = subtotal - totalDiskon;

                    tbody.append(`
                        <tr class="table-light keranjang-item ${rowClass}" data-index="${i}">
                            <td colspan="4" class="text-primary fw-semibold">${item.nama_barang}</td>
                        </tr>
                        <tr class="keranjang-item ${rowClass}" data-index="${i}">
                            <td class="small">${item.jumlah} ${item.satuan}</td>
                            <td class="small">@ ${formatRupiah(item.harga_jual)}</td>
                            <td class="text-end small" colspan="2">${formatRupiah(totalSetelahDiskon)}</td>
                        </tr>
                        <tr class="keranjang-item ${rowClass}" data-index="${i}">
                            <td class="small">D1: ${item.diskon1_persen || 0}%</td>
                            <td class="small">D2: ${item.diskon2_persen || 0}%</td>
                            <td class="small">D3: ${item.diskon3_persen || 0}%</td>
                            <td class="small">D4: ${item.diskon4_persen || 0}%</td>
                        </tr>
                        <tr class="keranjang-item ${rowClass}" data-index="${i}">
                            <td colspan="3" class="text-end text-success small">
                                Potongan: -${formatRupiah(totalDiskon)}
                            </td>
                            <td class="text-center">
                                <input type="checkbox" class="form-check-input input-promo"
                                    data-index="${i}" ${isChecked}>
                            </td>
                        </tr>
                    `);
                });

                saveState();
                updateTotalPenjualan();
            }

            async function recalcItem(i) {
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

                const $row = $('#keranjangTable tbody tr').eq(i);
                $row.find('td:eq(6)').text(it.diskon1_persen ? it.diskon1_persen + '%' : '');
                $row.find('td:eq(11)').text(formatRupiah(it.total));

                saveState();
                updateTotalPenjualan();
            }

            $('#keranjangTable tbody').on('input', '.input-diskon', function() {
                const i = $(this).data('index');
                const type = $(this).data('type');
                let val = parseFloat($(this).val()) || 0;

                val = Math.min(Math.max(val, 0), 100);

                if (type === 2) keranjang[i].diskon2_persen = val;
                if (type === 3) keranjang[i].diskon3_persen = val;
                if (type === 4) keranjang[i].diskon4_persen = val;

                recalcItem(i);
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
                localStorage.setItem('keranjangInput', JSON.stringify(keranjang));
            }

            renderKeranjang();

            let clickCount = 0;
            let clickTimer = null;

            $('#keranjangTable tbody').on('click', 'tr', function() {
                const row = $(this);
                const index = parseInt(row.data('index'));
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

            function clearKeranjang() {
                keranjang = []; // Kosongkan array keranjang dulu
                localStorage.removeItem('keranjangInput'); // Hapus dari localStorage
                renderKeranjang(); // Baru render ulang tampilan (keranjang sudah kosong)
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
                                clearKeranjang();
                                $('#formPenjualan')[0].reset();
                                $('#kode_barang').val(null).trigger('change');
                                console.log(keranjang);
                                console.log(localStorage.getItem('keranjangInput'));
                            },
                            error: function(xhr) {
                                Swal.fire('Gagal!', xhr.responseText, 'error');
                            }
                        });
                    }
                });
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
