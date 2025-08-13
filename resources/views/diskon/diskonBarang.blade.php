@extends('layouts.template')
@section('titlepage', 'Data Diskon Strata')
@section('contents')
    <div class="container-fluid p-0">
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm border-0 rounded-4">
                    <div
                        class="card-header bg-primary text-white rounded-top-4 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-bold"><i class="fa fa-percent me-2"></i> Data Diskon Strata</h5>
                        <button class="btn btn-light btn-sm text-primary fw-semibold" id="btnAddDiskon">
                            <i class="fa fa-plus-circle me-1"></i> Tambah Diskon
                        </button>
                    </div>

                    <div class="card-body mt-3">
                        <form method="GET" action="{{ route('diskonBarang') }}" class="mb-4" id="filterForm">
                            <div class="row g-2">
                                <div class="col-md-8">
                                    <select name="kode_barang" id="kode_barang" class="form-select2 form-select-sm">
                                        <option value="">Semua Barang</option>
                                        @foreach ($barang as $s)
                                            <option value="{{ $s->kode_barang }}"
                                                {{ request('kode_barang') == $s->kode_barang ? 'selected' : '' }}>
                                                {{ $s->nama_barang }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4 d-flex gap-2">
                                    <button type="submit" name="action" value="filter"
                                        class="btn btn-primary btn-sm w-100">
                                        <i class="fa fa-filter me-1"></i> Filter
                                    </button>
                                </div>
                            </div>
                        </form>

                        <form id="formDiskonStarlaReguler" action="{{ route('storeDiskon') }}" method="POST"
                            autocomplete="off">
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
                                        <option value="d1">Diskon 1</option>
                                        <option value="d2">Diskon 2</option>
                                        <option value="d3">Diskon 3</option>
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
                                    <input type="text" name="syarat" class="form-control form-control-sm" required>
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
                                    <input type="number" name="cash" id="cash" class="form-control form-control-sm"
                                        required>
                                </div>
                            </div>
                        </form>

                        <div class="table-responsive mt-3">
                            <table class="table table-hover table-striped table-sm table-bordered align-middle">
                                <thead class="table-primary text-center">
                                    <tr>
                                        <th style="width: 3%">No</th>
                                        <th>Kode Barang</th>
                                        <th>Nama Barang</th>
                                        <th>Syarat</th>
                                        <th>Persentase</th>
                                        <th>Nominal</th>
                                        <th>Tipe Syarat</th>
                                        <th>Jenis Diskon</th>
                                        <th>Cash</th>
                                        <th style="width: 10%">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $no = 1; @endphp
                                    @foreach ($diskon as $row)
                                        @php
                                            $nominal = ($row->persentase / 100) * ($row->harga_jual ?? 0);
                                        @endphp
                                        <tr>
                                            <td class="text-center">{{ $no++ }}</td>
                                            <td>{{ $row->kode_barang }}</td>
                                            <td>{{ $row->nama_barang ?? '-' }}</td>
                                            <td class="text-center">
                                                @if ($row->tipe_syarat == 'qty')
                                                    {{ $row->syarat }} {{ $row->satuan ?? '' }}
                                                @elseif ($row->tipe_syarat == 'nominal')
                                                    Rp. {{ number_format($row->syarat, 0, ',', '.') }}
                                                @else
                                                    {{ $row->syarat }}
                                                @endif
                                            </td>
                                            <td class="text-center">{{ number_format($row->persentase, 2) }}%</td>
                                            <td class="text-end">Rp {{ number_format($nominal, 0, ',', '.') }}</td>
                                            <td class="text-capitalize text-center">{{ $row->tipe_syarat }}</td>
                                            <td class="text-capitalize text-center">{{ $row->jenis_diskon }}</td>
                                            <td class="text-center">
                                                @if ($row->cash)
                                                    <span class="badge bg-success">Ya</span>
                                                @else
                                                    <span class="badge bg-secondary">Tidak</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <a href="#" class="btn btn-sm btn-danger btn-icon hapusDiskon"
                                                    data-href="{{ route('deleteDiskonStrata', $row->id) }}">
                                                    <i class="bi bi-trash"></i>
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
    </div>

    {{-- Modal Form Tambah/Edit --}}

    <script>
        $(document).ready(function() {
            // Tombol Tambah

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

            $('#satuan_diskon').on('change', function() {
                const selected = $(this).find(':selected');
                hargaDasar = parseFloat(selected.data('harga')) || 0;
                const satuanId = selected.data('id') || '';

                console.log('Harga Dasar:', hargaDasar);
                console.log('Satuan ID:', satuanId);

                // Set nilai harga dasar dan reset input diskon
                $('input[name="persentase"]').val('');
                $('#nominal_persen').val('');
                $('#satuan_id').val(satuanId);
            });

            $('input[name="persentase"]').on('input', function() {
                let persen = parseFloat($(this).val()) || 0;
                let nominal = (persen / 100) * hargaDasar;
                $('#nominal_persen').val(formatAngka(Math.round(nominal)));
            });

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
                if (!kode_barang) {
                    $('#satuan_diskon').html('<option value="">Satuan</option>');
                    return;
                }
                $('#satuan_diskon').html('<option value="">Memuat...</option>');

                $.get("{{ route('getSatuanBarang', ':kode_barang') }}".replace(':kode_barang', kode_barang),
                    function(res) {
                        let html = '<option value="">Satuan</option>';
                        res.forEach(function(item) {
                            html +=
                                `<option value="${item.satuan}" data-harga="${item.harga_jual}" data-id="${item.id}">${item.satuan}</option>`;
                        });
                        $('#satuan_diskon').html(html);
                    });
            }

            $('#kode_barang').on('change', function() {
                getSatuan();
            });
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

            $('#formDiskonStarlaReguler').on('keydown', 'input', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    $('#formDiskonStarlaReguler').submit();
                }
            });
        });
    </script>
@endsection
