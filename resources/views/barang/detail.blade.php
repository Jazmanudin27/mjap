@extends('layouts.template')
@section('titlepage', 'Detail Barang')
@section('contents')
    <div class="container-fluid p-0">
        <div class="row g-4">

            {{-- Kiri: Data Barang --}}
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header bg-light">
                        <h6 class="mb-0 text-black">Data Barang</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3 row">
                            <label class="col-sm-4 col-form-label" for="kode_barang">Kode Barang</label>
                            <div class="col-sm-8">
                                <input type="text" readonly class="form-control form-control-sm" id="kode_barang"
                                    value="{{ $barang->kode_barang }}">
                            </div>
                        </div>

                        <div class="mb-3 row">
                            <label class="col-sm-4 col-form-label" for="nama_barang">Nama Barang</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control form-control-sm" id="nama_barang"
                                    value="{{ $barang->nama_barang }}">
                            </div>
                        </div>

                        <div class="mb-3 row">
                            <label class="col-sm-4 col-form-label" for="jenis">Jenis</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control form-control-sm" id="jenis"
                                    value="{{ ucfirst($barang->kategori) }}">
                            </div>
                        </div>

                        <div class="mb-3 row">
                            <label class="col-sm-4 col-form-label" for="merk">Merk</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control form-control-sm" id="merk"
                                    value="{{ ucfirst($barang->merk) }}">
                            </div>
                        </div>

                        <div class="mb-3 row">
                            <label class="col-sm-4 col-form-label" for="stok_min">Stok Minimal</label>
                            <div class="col-sm-8">
                                <input type="number" class="form-control form-control-sm" id="stok_min"
                                    value="{{ $barang->stok_min }}">
                            </div>
                        </div>

                        <div class="mb-3 row">
                            <label class="col-sm-4 col-form-label" for="nama_supplier">Supplier</label>
                            <div class="col-sm-8">
                                <select id="kode_supplier" class="form-control forsm-control-sm form-select2">
                                    @foreach ($suppliers as $supplier)
                                        <option value="{{ $supplier->kode_supplier }}"
                                            {{ $supplier->kode_supplier == $barang->kode_supplier ? 'selected' : '' }}>
                                            {{ $supplier->nama_supplier }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="mb-3 row">
                            <label class="col-sm-4 col-form-label" for="keterangan">Keterangan</label>
                            <div class="col-sm-8">
                                <textarea class="form-control form-control-sm" id="keterangan" rows="3">{{ $barang->keterangan }}</textarea>
                            </div>
                        </div>

                        <div class="mb-3 row">
                            <label class="col-sm-4 col-form-label" for="status">Status</label>
                            <div class="col-sm-8">
                                <select id="status" class="form-control forsm-control-sm form-select">
                                    <option value="1" {{ $barang->status == 1 ? 'selected' : '' }}>Aktif</option>
                                    <option value="0" {{ $barang->status == 0 ? 'selected' : '' }}>Non Aktif</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3 row">
                            <div class="col-sm-12 d-grid">
                                <a class="btn btn-block btn-primary btn-sm" id="updateBarang">Update Barang</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Kanan: Harga per Satuan --}}
            <div class="col-lg-8">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header bg-light">
                            <h6 class="mb-0 text-black">Detail Harga per Satuan</h6>
                        </div>

                        @if (!empty($PermissionTambahHarga))
                            <div class="p-3 border-top">
                                <div class="row g-2">
                                    <div class="col-sm-2">
                                        <input type="hidden" id="id">
                                        <input type="text" id="satuan" class="form-control form-control-sm"
                                            placeholder="Satuan">
                                    </div>
                                    <div class="col-sm-2">
                                        <input type="text" id="isi" class="form-control form-control-sm"
                                            placeholder="Isi/Satuan">
                                    </div>
                                    <div class="col-sm-4">
                                        <input type="text" id="harga_pokok" class="form-control form-control-sm uang"
                                            placeholder="Harga Poko">
                                    </div>
                                    <div class="col-sm-4">
                                        <input type="text" id="harga_jual" class="form-control form-control-sm uang"
                                            placeholder="Harga Jual">
                                    </div>
                                </div>
                            </div>
                        @endif
                        <div class="card-body p-2">
                            <div class="table-responsive">
                                <table class="table table-striped table-sm mb-0">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Satuan</th>
                                            <th>Isi</th>
                                            <th class="text-end">Harga Pokok</th>
                                            <th class="text-end">Harga Jual</th>
                                            <th class="text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $no = 1;
                                        @endphp
                                        @foreach ($satuanBarang as $s)
                                            <tr>
                                                <td>{{ $no++ }}</td>
                                                <td>{{ $s->satuan }}</td>
                                                <td>{{ $s->isi }}</td>
                                                <td style="text-align: right">{{ number_format($s->harga_pokok) }}</td>
                                                <td style="text-align: right">{{ number_format($s->harga_jual) }}</td>
                                                <td style="text-align: center">
                                                    @if (!empty($PermissionEditHarga))
                                                        <a href="#" class="btn btn-sm btn-warning editHarga"
                                                            data-id="{{ $s->id }}"
                                                            data-satuan="{{ $s->satuan }}"
                                                            data-isi="{{ $s->isi }}"
                                                            data-harga_pokok="{{ round($s->harga_pokok) }}"
                                                            data-harga_jual="{{ round($s->harga_jual) }}">
                                                            <i class="fa fa-edit"></i>
                                                        </a>
                                                    @endif
                                                    <a href="#" data-href="{{ route('hapusSatuanHarga', $s->id) }}"
                                                        class="btn btn-sm btn-danger hapusHarga">
                                                        <i class="fa fa-trash"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-12">
                <div class="card shadow-lg">
                    <div class="card-body mt-3">
                        <div class="row">
                            <div class="col-sm-12">
                                <form id="formDiskonStarlaReguler" action="{{ route('storeDiskonStarla') }}"
                                    method="POST" autocomplete="off">
                                    @csrf
                                    <div class="row g-3">
                                        <div class="col-md-4">
                                            <label class="form-label">Satuan</label>
                                            <select id="satuan_diskon" class="form-select form-select-sm" required>
                                                <option value="">Satuan</option>
                                            </select>
                                            <input type="hidden" name="satuan_id" id="satuan_id">
                                        </div>

                                        <div class="col-md-4">
                                            <label class="form-label">Jenis Diskon</label>
                                            <select name="jenis_diskon" class="form-select form-select-sm" required>
                                                <option value="reguler">Reguler</option>
                                                <option value="promo">Promo</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Tipe Syarat</label>
                                            <select name="tipe_syarat" class="form-select form-select-sm" required>
                                                <option value="">Pilih Tipe</option>
                                                <option value="qty">Qty</option>
                                                <option value="nominal">Nominal</option>
                                            </select>
                                        </div>

                                        <div class="col-md-4">
                                            <label class="form-label">Syarat</label>
                                            <input type="text" name="syarat" class="form-control form-control-sm"
                                                required>
                                        </div>

                                        <div class="col-md-4">
                                            <label class="form-label">Nominal (Rp)</label>
                                            <input type="text" name="nominal_persen" id="nominal_persen"
                                                class="form-control form-control-sm">
                                        </div>

                                        <div class="col-md-2">
                                            <label class="form-label">Persen (%)</label>
                                            <input type="number" name="persentase" id="persentase"
                                                class="form-control form-control-sm" required>
                                        </div>

                                        <div class="col-md-2">
                                            <label class="form-label">Cash (%)</label>
                                            <input type="number" name="cash" id="cash"
                                                class="form-control form-control-sm" required>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="col-sm-6">
                                <h6 class="mt-4 mb-1 text-center">Diskon Reguler</h6>
                                <div class="table-responsive pt-2">
                                    <table class="table table-sm table-bordered table-hover align-middle mb-0">
                                        <thead class="table-light text-center">
                                            <tr>
                                                <th style="width: 50px;">No</th>
                                                <th>Syarat</th>
                                                <th>Persentase</th>
                                                <th>Nominal (Rp)</th>
                                                <th>Cash</th>
                                                <th>Tipe</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($diskonReguler as $index => $d)
                                                @php
                                                    $nominal = ($d->persentase / 100) * $d->harga_jual;
                                                @endphp
                                                <tr>
                                                    <td class="text-center">{{ $diskonReguler->firstItem() + $index }}
                                                    </td>
                                                    <td class="text-center">
                                                        @if ($d->tipe_syarat == 'qty')
                                                            {{ $d->syarat }} {{ $d->satuan ?? '' }}
                                                        @elseif ($d->tipe_syarat == 'nominal')
                                                            Rp. {{ number_format($d->syarat, 0, ',', '.') }}
                                                        @else
                                                            {{ $d->syarat }}
                                                        @endif
                                                    </td>
                                                    <td class="text-center">{{ round($d->persentase, 5) }}%</td>
                                                    <td class="text-end">{{ number_format($nominal) }}</td>
                                                    <td class="text-center">{{ round($d->cash, 5) }}%</td>
                                                    <td class="text-center text-uppercase">{{ $d->tipe_syarat }}</td>
                                                    <td class="text-center">
                                                        <a href="#"
                                                            class="btn btn-sm btn-danger btn-icon hapusDiskon"
                                                            data-href="{{ route('deleteDiskonStrata', $d->id) }}">
                                                            <i class="bi bi-trash"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="6" class="text-center text-muted">Tidak ada data
                                                        diskon.</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <h6 class="mt-4 mb-1 text-center">Diskon Promo</h6>
                                <div class="table-responsive pt-2">
                                    <table class="table table-sm table-bordered table-hover align-middle mb-0">
                                        <thead class="table-light text-center">
                                            <tr>
                                                <th style="width: 50px;">No</th>
                                                <th>Syarat</th>
                                                <th>Persentase</th>
                                                <th>Nominal (Rp)</th>
                                                <th>Cash</th>
                                                <th>Tipe</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($diskon as $index => $d)
                                                @php
                                                    $nominal = ($d->persentase / 100) * $d->harga_jual;
                                                @endphp
                                                <tr>
                                                    <td class="text-center">{{ $diskon->firstItem() + $index }}</td>
                                                    <td class="text-center">
                                                        @if ($d->tipe_syarat == 'qty')
                                                            {{ $d->syarat }} {{ $d->satuan ?? '' }}
                                                        @elseif ($d->tipe_syarat == 'nominal')
                                                            Rp. {{ number_format($d->syarat, 0, ',', '.') }}
                                                        @else
                                                            {{ $d->syarat }}
                                                        @endif
                                                    </td>
                                                    <td class="text-center">{{ round($d->persentase, 5) }}%</td>
                                                    <td class="text-end">{{ number_format($nominal) }}</td>
                                                    <td class="text-center">{{ round($d->cash, 5) }}%</td>
                                                    <td class="text-center text-uppercase">{{ $d->tipe_syarat }}</td>
                                                    <td class="text-center">
                                                        <a href="#"
                                                            class="btn btn-sm btn-danger btn-icon hapusDiskon"
                                                            data-href="{{ route('deleteDiskonStrata', $d->id) }}">
                                                            <i class="bi bi-trash"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="6" class="text-center text-muted">Tidak ada data
                                                        diskon.</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script>
            $(document).ready(function() {

                function formatAngka(angka) {
                    if (typeof(angka) != 'string') angka = angka.toString();
                    var reg = new RegExp('([0-9]+)([0-9]{3})');
                    while (reg.test(angka)) angka = angka.replace(reg, '$1,$2');
                    return angka;
                }

                function bersihkanAngka(str) {
                    return (str || '').toString().replace(/[^\d]/g, '');
                }

                let hargaDasar = 0;

                // Saat satuan berubah → ambil harga jual sebagai basis
                $('#satuan_diskon').on('change', function() {
                    const selected = $(this).find(':selected');
                    hargaDasar = parseFloat(selected.data('harga')) || 0;
                    const satuanId = selected.data('id') || '';

                    console.log('Harga Dasar:', hargaDasar);
                    console.log('Satuan ID:', satuanId);

                    // Set nilai harga dasar dan reset input diskon
                    $('input[name="persentase"]').val('');
                    $('#nominal_persen').val('');

                    // ⬅️ Set satuan_id ke input tersembunyi
                    $('#satuan_id').val(satuanId);
                });

                // Saat input Persentase, update Nominal
                $('input[name="persentase"]').on('input', function() {
                    let persen = parseFloat($(this).val()) || 0;
                    let nominal = (persen / 100) * hargaDasar;
                    $('#nominal_persen').val(formatAngka(Math.round(nominal)));
                });

                // Saat input Nominal, update Persentase
                $('#nominal_persen').on('input', function() {
                    let nominal = parseInt(bersihkanAngka($(this).val())) || 0;
                    let persen = hargaDasar ? (nominal / hargaDasar) * 100 : 0;
                    $('input[name="persentase"]').val(persen.toFixed(5));
                    $(this).val(formatAngka(nominal));
                });

                // Load satuan
                getSatuan();

                function getSatuan() {
                    var kode_barang = $('#kode_barang').val();
                    $('#satuan_diskon').html('<option value="">Memuat...</option>');
                    $.get("{{ route('getSatuanBarang', '') }}/" + kode_barang, function(res) {
                        let html = '<option value="">Satuan</option>';
                        res.forEach(function(item) {
                            html +=
                                `<option value="${item.satuan}" data-harga="${item.harga_jual}" data-id="${item.id}">${item.satuan}</option>`;
                        });
                        $('#satuan_diskon').html(html);
                    });
                }

                // AJAX Submit Form Diskon
                $('#formDiskonStarlaReguler').on('submit', function(e) {
                    e.preventDefault();

                    let kode_barang = $('#kode_barang').val();
                    let formData = $(this).serialize() + '&kode_barang=' + kode_barang;

                    $.ajax({
                        type: "POST",
                        url: $(this).attr('action'),
                        data: formData,
                        success: function(response) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: 'Diskon berhasil disimpan.',
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => location.reload());
                        },
                        error: function(xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops!',
                                text: 'Terjadi kesalahan saat menyimpan.',
                            });
                            console.log(xhr.responseText);
                        }
                    });
                });

                // Load Satuan Barang

                // CRUD Satuan Harga Barang (tetap seperti sebelumnya)
                $('#satuan, #isi, #harga_pokok, #harga_jual').on('keydown', function(e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        $.ajax({
                            url: "{{ route('storeBarangSatuan') }}",
                            method: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}',
                                id: $('#id').val(),
                                kode_barang: $('#kode_barang').val(),
                                satuan: $('#satuan').val(),
                                isi: $('#isi').val(),
                                harga_pokok: $('#harga_pokok').val(),
                                harga_jual: $('#harga_jual').val(),
                            },
                            success(response) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil!',
                                    text: response.message
                                }).then(() => location.reload());
                            },
                            error(xhr) {
                                if (xhr.status === 409) {
                                    Swal.fire({
                                        icon: 'warning',
                                        title: 'Duplikat!',
                                        text: xhr.responseJSON.message
                                    });
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Error',
                                        text: 'Terjadi kesalahan!'
                                    });
                                }
                            }
                        });
                    }
                });

                // Edit Harga Satuan
                $('.editHarga').on("click", function(e) {
                    e.preventDefault();
                    const id = $(this).data('id');
                    const satuan = $(this).data('satuan');
                    const isi = $(this).data('isi');
                    const harga_pokok = $(this).data('harga_pokok');
                    const harga_jual = $(this).data('harga_jual');

                    $('#id').val(id);
                    $('#satuan').val(satuan);
                    $('#isi').val(formatAngka(isi));
                    $('#harga_pokok').val(formatAngka(harga_pokok));
                    $('#harga_jual').val(formatAngka(harga_jual));
                });

                // Delete Diskon
                $('.hapusDiskon').on('click', function(e) {
                    e.preventDefault();
                    const url = $(this).data('href');
                    const row = $(this).closest('tr');
                    Swal.fire({
                        title: 'Yakin ingin menghapus?',
                        text: "Data diskon akan dihapus permanen.",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#aaa',
                        confirmButtonText: 'Ya, Hapus!',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.ajax({
                                url: url,
                                method: 'POST',
                                data: {
                                    _token: '{{ csrf_token() }}'
                                },
                                success: function(response) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Berhasil!',
                                        text: response.message
                                    });
                                    row.fadeOut(300, function() {
                                        $(this).remove();
                                    });
                                },
                            });
                        }
                    });
                });

                // Delete Harga Satuan
                $('.hapusHarga').on('click', function(e) {
                    e.preventDefault();
                    const url = $(this).data('href');
                    const row = $(this).closest('tr');
                    Swal.fire({
                        title: 'Yakin ingin menghapus?',
                        text: "Data satuan akan dihapus permanen.",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#aaa',
                        confirmButtonText: 'Ya, Hapus!',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.ajax({
                                url: url,
                                method: 'POST',
                                data: {
                                    _token: '{{ csrf_token() }}'
                                },
                                success: function(response) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Berhasil!',
                                        text: response.message
                                    });
                                    row.fadeOut(300, function() {
                                        $(this).remove();
                                    });
                                },
                            });
                        }
                    });
                });

                $('#formDiskonStarlaReguler').on('keydown', 'input', function(e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        $('#formDiskonStarlaReguler').submit();
                    }
                });
            });
        </script>
    @endsection
