@extends('layouts.template')
@section('contents')
    <section class="section dashboard">
        <div class="row justify-content-center">
            <div class="col-lg-12">
                <div class="card shadow rounded-4">
                    <div class="card-header bg-warning text-dark text-center rounded-top-4">
                        <h5 class="mb-0"><i class="bi bi-pencil-square me-2"></i>Edit Data Karyawan</h5>
                    </div>
                    <div class="card-body mt-3">
                        <form action="{{ route('updateKaryawan', $karyawan->nik) }}" method="POST"
                            enctype="multipart/form-data" autocomplete="off">
                            @csrf
                            @method('PUT')
                            <div class="row g-3">
                                {{-- NIK, Nama, Tanggal Lahir --}}
                                <div class="col-md-4">
                                    <label class="small">No. Induk Karyawan</label>
                                    <input type="text" name="nik" value="{{ old('nik', $karyawan->nik) }}"
                                        class="form-control form-control-sm" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="small">Nama Lengkap</label>
                                    <input type="text" name="nama_lengkap"
                                        value="{{ old('nama_lengkap', $karyawan->nama_lengkap) }}"
                                        class="form-control form-control-sm" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="small">Tanggal Lahir</label>
                                    <input type="date" name="tanggal_lahir"
                                        value="{{ old('tanggal_lahir', $karyawan->tanggal_lahir) }}"
                                        class="form-control form-control-sm" required>
                                </div>

                                {{-- Jenis Kelamin, Tgl Masuk, Status --}}
                                <div class="col-md-4">
                                    <label class="small">Jenis Kelamin</label>
                                    <select name="jenis_kelamin" class="form-select form-select-sm" required>
                                        <option value="">Pilih</option>
                                        <option value="Laki-laki" {{ old('jenis_kelamin', $karyawan->jenis_kelamin) == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                                        <option value="Perempuan" {{ old('jenis_kelamin', $karyawan->jenis_kelamin) == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="small">Tanggal Masuk</label>
                                    <input type="date" name="tgl_masuk" value="{{ old('tgl_masuk', $karyawan->tgl_masuk) }}"
                                        class="form-control form-control-sm" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="small">Status Karyawan</label>
                                    <select name="status_karyawan" class="form-select form-select-sm" required>
                                        <option value="">Pilih</option>
                                        <option value="Tetap" {{ old('status_karyawan', $karyawan->status_karyawan) == 'Tetap' ? 'selected' : '' }}>Tetap</option>
                                        <option value="Kontrak" {{ old('status_karyawan', $karyawan->status_karyawan) == 'Kontrak' ? 'selected' : '' }}>Kontrak</option>
                                    </select>
                                </div>

                                {{-- KTP, NPWP, Pendidikan --}}
                                <div class="col-md-4">
                                    <label class="small">Nomor KTP</label>
                                    <input type="text" name="nomor_ktp" value="{{ old('nomor_ktp', $karyawan->nomor_ktp) }}"
                                        class="form-control form-control-sm" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="small">NPWP</label>
                                    <input type="text" name="npwp" value="{{ old('npwp', $karyawan->npwp) }}"
                                        class="form-control form-control-sm" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="small">Pendidikan Terakhir</label>
                                    <input type="text" name="pendidikan_terakhir"
                                        value="{{ old('pendidikan_terakhir', $karyawan->pendidikan_terakhir) }}"
                                        class="form-control form-control-sm" required>
                                </div>

                                {{-- Alamat & Catatan --}}
                                <div class="col-md-6">
                                    <label class="small">Alamat Lengkap</label>
                                    <textarea name="alamat" class="form-control form-control-sm" rows="2"
                                        required>{{ old('alamat', $karyawan->alamat) }}</textarea>
                                </div>
                                <div class="col-md-6">
                                    <label class="small">Catatan Tambahan</label>
                                    <textarea name="catatan" class="form-control form-control-sm"
                                        rows="2">{{ old('catatan', $karyawan->catatan) }}</textarea>
                                </div>

                                {{-- Kontak --}}
                                <div class="col-md-3">
                                    <label class="small">Nomor Telepon</label>
                                    <input type="text" name="nomor_telepon"
                                        value="{{ old('nomor_telepon', $karyawan->nomor_telepon) }}"
                                        class="form-control form-control-sm" required>
                                </div>
                                <div class="col-md-3">
                                    <label class="small">Email</label>
                                    <input type="email" name="email" value="{{ old('email', $karyawan->email) }}"
                                        class="form-control form-control-sm" required>
                                </div>

                                {{-- Kantor, Jabatan, Dept, Group --}}
                                <div class="col-md-3">
                                    <label class="small">Kantor</label>
                                    <select name="id_kantor" class="form-select form-select-sm" required>
                                        <option value="">Pilih Kantor</option>
                                        @foreach ($kantor as $k)
                                            <option value="{{ $k->id }}" {{ old('id_kantor', $karyawan->id_kantor) == $k->id ? 'selected' : '' }}>{{ $k->nama_kantor }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="small">Jabatan</label>
                                    <select name="id_jabatan" class="form-select form-select-sm" required>
                                        <option value="">Pilih Jabatan</option>
                                        @foreach ($jabatan as $j)
                                            <option value="{{ $j->id }}" {{ old('id_jabatan', $karyawan->id_jabatan) == $j->id ? 'selected' : '' }}>{{ $j->nama_jabatan }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="small">Department</label>
                                    <select name="id_department" class="form-select form-select-sm" required>
                                        <option value="">Pilih Department</option>
                                        @foreach ($department as $d)
                                            <option value="{{ $d->id }}" {{ old('id_department', $karyawan->id_department) == $d->id ? 'selected' : '' }}>{{ $d->nama_department }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="small">Group</label>
                                    <select name="id_group" class="form-select form-select-sm" required>
                                        <option value="">Pilih Group</option>
                                        @foreach ($group as $g)
                                            <option value="{{ $g->id }}" {{ old('id_group', $karyawan->id_group) == $g->id ? 'selected' : '' }}>{{ $g->nama_group }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- Bank & Status --}}
                                <div class="col-md-4">
                                    <label class="small">Nomor Rekening</label>
                                    <input type="text" name="nomor_rekening_bank"
                                        value="{{ old('nomor_rekening_bank', $karyawan->nomor_rekening_bank) }}"
                                        class="form-control form-control-sm" required>
                                </div>
                                <div class="col-md-2">
                                    <label class="small">Nama Bank</label>
                                    <input type="text" name="nama_bank" value="{{ old('nama_bank', $karyawan->nama_bank) }}"
                                        class="form-control form-control-sm" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="small">Status Pernikahan</label>
                                    <select name="status_pernikahan" class="form-select form-select-sm" required>
                                        <option value="">Pilih</option>
                                        <option value="Single" {{ old('status_pernikahan', $karyawan->status_pernikahan) == 'Single' ? 'selected' : '' }}>Single</option>
                                        <option value="Menikah" {{ old('status_pernikahan', $karyawan->status_pernikahan) == 'Menikah' ? 'selected' : '' }}>Menikah</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="small">Jumlah Anak</label>
                                    <input type="number" name="jumlah_anak"
                                        value="{{ old('jumlah_anak', $karyawan->jumlah_anak) }}"
                                        class="form-control form-control-sm">
                                </div>

                                {{-- Foto --}}
                                <div class="col-md-4">
                                    <label class="small">Upload Foto</label>
                                    <input type="file" name="foto_karyawan" class="form-control form-control-sm"
                                        accept="image/*" onchange="previewImage(event)">
                                    @if($karyawan->foto_karyawan)
                                        <img id="preview" src="{{ asset('storage/foto/' . $karyawan->foto_karyawan) }}"
                                            class="mt-2 rounded border shadow-sm" style="max-width:150px;">
                                    @else
                                        <img id="preview" class="mt-2 rounded border shadow-sm"
                                            style="max-width:150px; display:none;">
                                    @endif
                                </div>
                            </div>

                            <div class="text-end mt-4">
                                <button type="submit" class="btn btn-primary w-100"><i
                                        class="bi bi-save me-2"></i>Update</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script>
        function previewImage(event) {
            const reader = new FileReader();
            reader.onload = () => {
                const img = document.getElementById('preview');
                img.src = reader.result;
                img.style.display = 'block';
            };
            reader.readAsDataURL(event.target.files[0]);
        }
    </script>
@endsection
