@extends('mobile.layout')
@section('title', 'Input Retur')
@section('header', 'Retur Pelanggan')

@section('content')
    <div class="container py-3">

        {{-- Info Pelanggan --}}
        <div class="card shadow-sm border-0 rounded-4 mb-3 text-white" style="background: #dc3545;">
            <div class="card-body rounded-4"
                style="background-image: url('{{ $pelanggan->foto ? asset('storage/pelanggan/' . $pelanggan->foto) : '' }}'); background-size: cover; background-position: center;">
                <div class="bg-dark bg-opacity-50 p-3 rounded-4">
                    <div class="fw-bold fs-6">{{ $pelanggan->kode_pelanggan }}</div>
                    <div class="fw-bold fs-5">{{ $pelanggan->nama_pelanggan }}</div>
                    <div>{{ $pelanggan->alamat_toko }}</div>
                </div>
            </div>
        </div>

        <form action="{{ route('storeReturMobile') }}" method="POST">
            @csrf
            <input type="hidden" name="kode_pelanggan" value="{{ $pelanggan->kode_pelanggan }}">
            <input type="hidden" name="keranjang" id="input_keranjang">
            <input type="hidden" name="satuan_id" id="satuan_id">
            <input type="hidden" name="total" id="input_total">

            {{-- Faktur & Jenis Retur --}}
            <div class="card border-0 shadow-sm rounded-4 mb-2">
                <div class="card-body">
                    <div class="mb-2">
                        <label class="form-label">Faktur Penjualan</label>
                        <select name="no_faktur" class="form-select form-select-sm" required>
                            <option value="">Pilih Faktur</option>
                            @foreach ($fakturList as $faktur)
                                <option value="{{ $faktur->no_faktur }}">{{ $faktur->no_faktur }} - {{ $faktur->tanggal }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Jenis Retur</label>
                        <select name="jenis_retur" class="form-select form-select-sm" required>
                            <option value="">Pilih Jenis</option>
                            <option value="GB">Ganti Barang</option>
                            <option value="FP">Potong Faktur</option>
                        </select>
                    </div>
                </div>
            </div>

            {{-- Input Produk --}}
            <div class="card border-0 shadow-sm rounded-4 mb-2">
                <div class="card-body">
                    <h6 class="fw-bold text-danger mb-2"><i class="bi bi-arrow-counterclockwise me-1"></i> Input Barang
                        Retur</h6>

                    <div class="mb-2">
                        <label class="form-label">Pilih Barang</label>
                        <select id="kode_barang" name="kode_barang" class="form-select form-select-sm"></select>
                    </div>

                    <div class="row g-2">
                        <div class="col-4">
                            <label class="form-label">Satuan</label>
                            <select id="satuan" class="form-select form-select-sm">
                                <option value="">Satuan</option>
                            </select>
                        </div>

                        <div class="col-5">
                            <label class="form-label">Harga Retur</label>
                            <input type="text" id="harga_jual" name="harga_retur"
                                class="form-control form-control-sm text-end" placeholder="Rp 0">
                        </div>

                        <div class="col-3">
                            <label class="form-label">Qty</label>
                            <input type="number" name="qty" id="qty" class="form-control form-control-sm text-end"
                                placeholder="Qty">
                        </div>
                    </div>

                    <div class="d-grid mt-4">
                        <button type="button" class="btn btn-sm btn-danger rounded-3" id="btnTambahProduk">
                            <i class="bi bi-plus-circle me-1"></i> Tambah Barang
                        </button>
                    </div>
                </div>
            </div>

            {{-- Keranjang Retur --}}
            <div class="card border-0 shadow-sm rounded-4 mb-2" id="keranjangCard">
                <div class="card-body" style="zoom:95%">
                    <h6 class="fw-bold mb-2"><i class="bi bi-basket me-1"></i> Keranjang Retur</h6>
                    <div class="table-responsive">
                        <table class="table table-sm align-middle mb-0" id="tabelKeranjang">
                            <tbody></tbody>
                        </table>
                    </div>
                    <div class="mt-2 text-end fw-bold">
                        Total: <span id="totalKeranjang">Rp 0</span>
                    </div>
                </div>
            </div>

            {{-- Informasi Tambahan --}}
            <div class="card border-0 shadow-sm rounded-4 mb-2">
                <div class="card-body">
                    <div class="mb-2">
                        <label class="form-label">Keterangan</label>
                        <textarea name="keterangan" class="form-control form-control-sm" rows="2"
                            placeholder="Contoh: retur karena rusak, kadaluarsa, dsb."></textarea>
                    </div>

                    <button type="submit" class="btn btn-sm btn-danger w-100 py-2">
                        <i class="bi bi-save me-1"></i> Simpan Retur
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

        let keranjang = ambilKeranjang();

        function simpanKeranjang() {
            localStorage.setItem('keranjangRetur', JSON.stringify(keranjang));
        }

        function ambilKeranjang() {
            const data = localStorage.getItem('keranjangRetur');
            return data ? JSON.parse(data) : [];
        }

        $(document).ready(function () {
            $('#kode_barang').select2({
                placeholder: 'Cari barang...',
                dropdownParent: $('#kode_barang').parent(),
                ajax: {
                    url: '{{ route("getBarang") }}',
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

            $('#kode_barang').change(function () {
                const kode_barang = $(this).val();
                $('#satuan').html('<option value="">Memuat...</option>');

                $.get("{{ route('getSatuanBarang', '') }}/" + kode_barang, function (res) {
                    let html = '<option value="">Satuan</option>';
                    res.forEach(function (item) {
                        html += `<option value="${item.satuan}" data-harga="${item.harga_jual}" data-id="${item.id}">${item.satuan}</option>`;
                    });
                    $('#satuan').html(html);
                    $('#harga_jual').val('');
                    $('#satuan_id').val('');
                });
            });

            $('#satuan').change(function () {
                const selected = $(this).find(':selected');
                const harga = selected.data('harga') || 0;
                const id = selected.data('id') || 0;
                $('#harga_jual').val(formatRupiah(harga));
                $('#satuan_id').val(id);
            });

            renderKeranjang();

            function renderKeranjang() {
                let tbody = $('#tabelKeranjang tbody');
                tbody.empty();
                let total = 0;

                keranjang.forEach((item, index) => {
                    let subtotal = item.qty * item.harga;
                    total += subtotal;

                    tbody.append(`
                            <tr class="table-light keranjang-item" data-index="${index}">
                                <td colspan="3" class="text-primary fw-semibold">${item.nama_barang}</td>
                            </tr>
                            <tr class="keranjang-item" data-index="${index}">
                                <td class="small">${item.qty} ${item.satuan}</td>
                                <td class="small">@ ${formatRupiah(item.harga)}</td>
                                <td class="text-end small">${formatRupiah(subtotal)}</td>
                            </tr>
                        `);
                });

                $('#totalKeranjang').text(formatRupiah(total));
                simpanKeranjang();
            }

            $('#btnTambahProduk').click(function () {
                let barangSelect = $('#kode_barang').select2('data')[0];
                let satuan = $('#satuan').val();
                let satuanText = $('#satuan option:selected').text();
                let harga = parseRupiah($('#harga_jual').val());
                let qty = parseInt($('#qty').val()) || 0;

                if (!barangSelect || !satuan || !harga || qty < 1) {
                    alert('Lengkapi data produk sebelum ditambahkan ke keranjang.');
                    return;
                }

                let item = {
                    nama_barang: barangSelect.text,
                    kode_barang: barangSelect.id,
                    satuan: satuanText,
                    satuan_id: $('#satuan_id').val(),
                    harga: harga,
                    qty: qty
                };

                keranjang.push(item);
                renderKeranjang();

                $('#kode_barang').val(null).trigger('change');
                $('#satuan').html('<option value="">Satuan</option>');
                $('#harga_jual').val('');
                $('#qty').val('');
                $('#satuan_id').val('');
            });

            // Triple Tap Delete
            let tapCount = 0;
            let tapTimer = null;

            $(document).on('click', '.keranjang-item', function () {
                const index = $(this).data('index');

                tapCount++;

                clearTimeout(tapTimer);
                tapTimer = setTimeout(() => {
                    tapCount = 0;
                }, 400);

                if (tapCount === 3) {
                    if (navigator.vibrate) navigator.vibrate(100);
                    keranjang.splice(index, 1);
                    renderKeranjang();
                    tapCount = 0;
                }
            });

            $('form').on('submit', function () {
                let total = 0;
                keranjang.forEach(item => {
                    total += item.qty * item.harga;
                });

                $('#input_keranjang').val(JSON.stringify(keranjang));
                $('#input_total').val(total); // << Tambahkan ini
                localStorage.removeItem('keranjangRetur');
            });
        });
    </script>
@endsection
