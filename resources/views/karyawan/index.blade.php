@extends('layouts.template')
@section('contents')
    <section class="section dashboard">
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm border-0 rounded-4">
                    <div
                        class="card-header bg-primary text-white rounded-top-4 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-bold"><i class="fa fa-users me-2"></i> Data Karyawan</h5>
                        @if (!empty($TambahKaryawan))
                            <a href="{{ route('tambahKaryawan') }}" class="btn btn-light btn-sm text-primary fw-semibold">
                                <i class="fa fa-plus-circle me-1"></i> Tambah Data
                            </a>
                        @endif
                    </div>

                    {{-- ===== FILTER FORM ===== --}}
                    <div class="card-body mt-3">
                        <form id="filterForm" method="GET" action="{{ route('viewKaryawan') }}" class="mb-3">
                            <div class="row g-2">
                                {{-- Baris 1 --}}
                                <div class="col-md-2">
                                    <input type="text" name="nik" class="form-control form-control-sm"
                                        placeholder="NIK Karyawan" value="{{ request('nik') }}">
                                </div>
                                <div class="col-md-2">
                                    <select name="id_jabatan" class="form-select2 form-select-sm">
                                        <option value="">Semua Jabatan</option>
                                        @foreach ($jabatan as $k)
                                            <option value="{{ $k->id }}"
                                                {{ request('id_jabatan') == $k->id ? 'selected' : '' }}>
                                                {{ $k->nama_jabatan }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <select name="id_kantor" class="form-select2 form-select-sm">
                                        <option value="">Semua Kantor</option>
                                        @foreach ($kantor as $k)
                                            <option value="{{ $k->id }}"
                                                {{ request('id_kantor') == $k->id ? 'selected' : '' }}>
                                                {{ $k->nama_kantor }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <select name="id_department" class="form-select2 form-select-sm">
                                        <option value="">Semua Department</option>
                                        @foreach ($department as $k)
                                            <option value="{{ $k->id }}"
                                                {{ request('id_department') == $k->id ? 'selected' : '' }}>
                                                {{ $k->nama_department }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <select name="id_group" class="form-select2 form-select-sm">
                                        <option value="">Semua Group</option>
                                        @foreach ($group as $k)
                                            <option value="{{ $k->id }}"
                                                {{ request('id_group') == $k->id ? 'selected' : '' }}>
                                                {{ $k->nama_group }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <select name="status" class="form-select2 form-select-sm">
                                        <option value="">Semua Status</option>
                                        <option value="Aktif" {{ request('status') == 'Aktif' ? 'selected' : '' }}>Aktif
                                        </option>
                                        <option value="Tidak Aktif"
                                            {{ request('status') == 'Tidak Aktif' ? 'selected' : '' }}>Tidak Aktif</option>
                                    </select>
                                </div>

                                {{-- Baris 2 --}}
                                <div class="col-md-4">
                                    <input type="text" name="nama_lengkap" class="form-control form-control-sm"
                                        placeholder="Nama Karyawan" value="{{ request('nama_lengkap') }}">
                                </div>

                                <div class="col-md-2 d-grid">
                                    <button type="submit" class="btn btn-sm btn-primary w-100">
                                        <i class="fa fa-filter me-1"></i> Filter
                                    </button>
                                </div>
                            </div>
                        </form>

                        {{-- ===== DATA TABLE ===== --}}
                        <div class="table-responsive">
                            <table class="table table-hover table-striped table-sm table-bordered align-middle">
                                <thead class="table-primary text-center">
                                    <tr>
                                        <th style="width: 3%">No.</th>
                                        <th style="width: 5%">NIK</th>
                                        <th>Nama Karyawan</th>
                                        <th style="width: 8%">Kantor</th>
                                        <th style="width: 8%">Jabatan</th>
                                        <th style="width: 8%">Dept.</th>
                                        <th style="width: 9%">Tgl Masuk</th>
                                        <th style="width: 8%">Umur</th>
                                        <th style="width: 8%">Masa Kerja</th>
                                        <th style="width: 7%">Karyawan</th>
                                        <th style="width: 15%">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($data as $index => $karyawan)
                                        <tr>
                                            <td class="text-center">{{ $index + 1 }}</td>
                                            <td>{{ $karyawan->nik }}</td>
                                            <td>{{ $karyawan->nama_lengkap }}</td>
                                            <td>{{ $karyawan->nama_kantor }}</td>
                                            <td>{{ $karyawan->nama_jabatan }}</td>
                                            <td>{{ $karyawan->nama_department }}</td>
                                            <td>{{ tanggal_indo2($karyawan->tgl_masuk) }}</td>
                                            <td>{{ round(\Carbon\Carbon::parse($karyawan->tanggal_lahir)->age, 1) }} tahun
                                            </td>
                                            <td>{{ round(\Carbon\Carbon::parse($karyawan->tgl_masuk)->diffInYears(now()), 1) }}
                                                tahun</td>
                                            <td>{{ $karyawan->status_karyawan }}</td>
                                            <td class="text-center">
                                                @if ($karyawan->status == '1')
                                                    <span class="btn btn-sm btn-success">
                                                        <i class="fa fa-thumbs-up"></i>
                                                    </span>
                                                @else
                                                    <span class="btn btn-sm btn-secondary">
                                                        <i class="fa fa-thumbs-down"></i>
                                                    </span>
                                                @endif
                                                @if (isset($PermissionStatusKaryawan) && $PermissionStatusKaryawan)
                                                    <a href="{{ route('karyawan.toggleStatus', $karyawan->nik) }}"
                                                        class="btn btn-sm {{ $karyawan->status == 'Aktif' ? 'btn-success' : 'btn-danger' }} px-2">
                                                        <i
                                                            class="fa {{ $karyawan->status == 'Aktif' ? 'fa-thumbs-up' : 'fa-thumbs-down' }}"></i>
                                                    </a>
                                                @endif

                                                @if (isset($EditKaryawan) && $EditKaryawan)
                                                    <a href="{{ route('editKaryawan', $karyawan->nik) }}"
                                                        class="btn btn-sm btn-warning px-2">
                                                        <i class="fa fa-pencil"></i>
                                                    </a>
                                                @endif

                                                @if (isset($DeleteKaryawan) && $DeleteKaryawan)
                                                    <a data-href="{{ route('deleteKaryawan', $karyawan->nik) }}"
                                                        class="btn btn-sm btn-danger delete px-2">
                                                        <i class="fa fa-trash"></i>
                                                    </a>
                                                @endif

                                                @if (isset($DetailKaryawan) && $DetailKaryawan)
                                                    <a href="{{ route('detailKaryawan', $karyawan->nik) }}"
                                                        class="btn btn-sm btn-info px-2">
                                                        <i class="fa fa-list"></i>
                                                    </a>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <div class="mt-3">
                                {{ $data->withQueryString()->links('pagination::bootstrap-5') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script>
        $(document).ready(function() {
            $('#filterForm').on('change', 'input,select', function() {
                $('#filterForm').submit();
            });

            $('.delete').on('click', function(e) {
                e.preventDefault();

                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = $(this).attr('data-href');
                        Swal.fire('Deleted!', 'Data karyawan berhasil dihapus.', 'success');
                    }
                });
            });
        });
    </script>
@endsection
