@extends('layouts.template')
@section('contents')
<section class="section dashboard">
    <div class="row justify-content-center">
        <div class="col-lg-12">
            <div class="card shadow-lg rounded-4">
                <div class="card-header bg-primary text-white text-center rounded-top-4">
                    <h5 class="mb-0"><i class="bi bi-person-plus-fill me-2"></i>Tambah Data Karyawan</h5>
                </div>
                <div class="card-body mt-3">
                    <form action="{{ url('storeKaryawan') }}" method="POST" enctype="multipart/form-data"
                        autocomplete="off">
                        @csrf

                        {{-- Identitas Karyawan --}}
                        <h6 class="text-primary border-bottom pb-1 mb-3">Identitas Karyawan</h6>
                        <div class="row row-cols-1 row-cols-md-2 g-3">
                            <div>
                                <label class="form-label form-label-sm">No. Induk Karyawan</label>
                                <input type="text" name="nik" value="{{ old('nik') }}"
                                    class="form-control form-control-sm" required>
                            </div>
                            <div>
                                <label class="form-label form-label-sm">Nama Lengkap</label>
                                <input type="text" name="nama_lengkap" value="{{ old('nama_lengkap') }}"
                                    class="form-control form-control-sm" required>
                            </div>
                            <div>
                                <label class="form-label form-label-sm">Tanggal Lahir</label>
                                <input type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir') }}"
                                    class="form-control form-control-sm" required>
                            </div>
                            <div>
                                <label class="form-label form-label-sm">Jenis Kelamin</label>
                                <select name="jenis_kelamin" class="form-select form-select-sm" required>
                                    <option value="">Pilih</option>
                                    <option value="L" {{ old('jenis_kelamin') == 'L' ? 'selected' : '' }}>
                                        Laki-laki</option>
                                    <option value="P" {{ old('jenis_kelamin') == 'P' ? 'selected' : '' }}>
                                        Perempuan</option>
                                </select>
                            </div>
                            <div>
                                <label class="form-label form-label-sm">Tanggal Masuk</label>
                                <input type="date" name="tgl_masuk" value="{{ old('tgl_masuk') }}"
                                    class="form-control form-control-sm" required>
                            </div>
                            <div>
                                <label class="form-label form-label-sm">Status Karyawan</label>
                                <select name="status_karyawan" class="form-select form-select-sm" required>
                                    <option value="">Pilih</option>
                                    <option value="Tetap" {{ old('status_karyawan') == 'Tetap' ? 'selected' : '' }}>Tetap
                                    </option>
                                    <option value="Kontrak" {{ old('status_karyawan') == 'Kontrak' ? 'selected' : '' }}>
                                        Kontrak</option>
                                </select>
                            </div>
                        </div>

                        {{-- Dokumen & Pendidikan --}}
                        <h6 class="text-primary border-bottom pt-4 pb-1 mb-3">Dokumen & Pendidikan</h6>
                        <div class="row row-cols-1 row-cols-md-2 g-3">
                            <div>
                                <label class="form-label form-label-sm">Nomor KTP</label>
                                <input type="text" name="nomor_ktp" value="{{ old('nomor_ktp') }}"
                                    class="form-control form-control-sm" required>
                            </div>
                            <div>
                                <label class="form-label form-label-sm">NPWP</label>
                                <input type="text" name="npwp" value="{{ old('npwp') }}"
                                    class="form-control form-control-sm" required>
                            </div>
                            <div>
                                <label class="form-label form-label-sm">Pendidikan Terakhir</label>
                                <input type="text" name="pendidikan_terakhir" value="{{ old('pendidikan_terakhir') }}"
                                    class="form-control form-control-sm" required>
                            </div>
                        </div>

                        {{-- Alamat & Kontak --}}
                        <h6 class="text-primary border-bottom pt-4 pb-1 mb-3">Alamat & Kontak</h6>
                        <div class="row row-cols-1 row-cols-md-2 g-3">
                            <div>
                                <label class="form-label form-label-sm">Alamat Lengkap</label>
                                <textarea name="alamat" class="form-control form-control-sm" rows="2"
                                    required>{{ old('alamat') }}</textarea>
                            </div>
                            <div>
                                <label class="form-label form-label-sm">Catatan Tambahan</label>
                                <textarea name="catatan" class="form-control form-control-sm"
                                    rows="2">{{ old('catatan') }}</textarea>
                            </div>
                            <div>
                                <label class="form-label form-label-sm">Nomor Telepon</label>
                                <input type="text" name="nomor_telepon" value="{{ old('nomor_telepon') }}"
                                    class="form-control form-control-sm" required>
                            </div>
                            <div>
                                <label class="form-label form-label-sm">Email</label>
                                <input type="email" name="email" value="{{ old('email') }}"
                                    class="form-control form-control-sm" required>
                            </div>
                        </div>

                        {{-- Jabatan & Penempatan --}}
                        <h6 class="text-primary border-bottom pt-4 pb-1 mb-3">Jabatan & Penempatan</h6>
                        <div class="row row-cols-1 row-cols-md-2 g-3">
                            <div>
                                <label class="form-label form-label-sm">Kantor</label>
                                <select name="id_kantor" class="form-select form-select-sm" required>
                                    <option value="">Pilih Kantor</option>
                                    @php($kantor = DB::table('hrd_kantor')->orderBy('nama_kantor')->get())
                                    @foreach ($kantor as $k)
                                        <option value="{{ $k->id }}" {{ old('id_kantor') == $k->id ? 'selected' : '' }}>
                                            {{ $k->nama_kantor }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="form-label form-label-sm">Jabatan</label>
                                <select name="id_jabatan" class="form-select form-select-sm" required>
                                    <option value="">Pilih Jabatan</option>
                                    @php($jabatan = DB::table('hrd_jabatan')->orderBy('nama_jabatan')->get())
                                    @foreach ($jabatan as $j)
                                        <option value="{{ $j->id }}" {{ old('id_jabatan') == $j->id ? 'selected' : '' }}>
                                            {{ $j->nama_jabatan }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="form-label form-label-sm">Department</label>
                                <select name="id_department" class="form-select form-select-sm" required>
                                    <option value="">Pilih Department</option>
                                    @php($department = DB::table('hrd_department')->orderBy('nama_department')->get())
                                    @foreach ($department as $d)
                                        <option value="{{ $d->id }}" {{ old('id_department') == $d->id ? 'selected' : '' }}>
                                            {{ $d->nama_department }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="form-label form-label-sm">Group</label>
                                <select name="id_group" class="form-select form-select-sm" required>
                                    <option value="">Pilih Group</option>
                                    @php($group = DB::table('hrd_group')->orderBy('nama_group')->get())
                                    @foreach ($group as $g)
                                        <option value="{{ $g->id }}" {{ old('id_group') == $g->id ? 'selected' : '' }}>
                                            {{ $g->nama_group }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- Keluarga & Bank --}}
                        <h6 class="text-primary border-bottom pt-4 pb-1 mb-3">Keluarga & Bank</h6>
                        <div class="row row-cols-1 row-cols-md-2 g-3">
                            <div>
                                <label class="form-label form-label-sm">Nomor Rekening</label>
                                <input type="text" name="nomor_rekening_bank" value="{{ old('nomor_rekening_bank') }}"
                                    class="form-control form-control-sm" required>
                            </div>
                            <div>
                                <label class="form-label form-label-sm">Nama Bank</label>
                                <input type="text" name="nama_bank" value="{{ old('nama_bank') }}"
                                    class="form-control form-control-sm" required>
                            </div>
                            <div>
                                <label class="form-label form-label-sm">Status Pernikahan</label>
                                <select name="status_pernikahan" class="form-select form-select-sm" required>
                                    <option value="">Pilih</option>
                                    <option value="Lajang" {{ old('status_pernikahan') == 'Lajang' ? 'selected' : '' }}>
                                        Single</option>
                                    <option value="Menikah" {{ old('status_pernikahan') == 'Menikah' ? 'selected' : '' }}>
                                        Menikah</option>
                                </select>
                            </div>
                            <div>
                                <label class="form-label form-label-sm">Jumlah Anak</label>
                                <input type="number" name="jumlah_anak" value="{{ old('jumlah_anak') }}"
                                    class="form-control form-control-sm">
                            </div>
                        </div>

                        {{-- Foto --}}
                        <h6 class="text-primary border-bottom pt-4 pb-1 mb-3">Upload Foto</h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label form-label-sm">Foto Karyawan</label>
                                <input type="file" name="foto_karyawan" class="form-control form-control-sm"
                                    accept="image/*" onchange="previewImage(event)">
                            </div>
                            <div class="col-md-6">
                                <img id="preview" class="mt-2 rounded border shadow-sm"
                                    style="max-width:150px; display:none;">
                            </div>
                        </div>

                        <div class="text-end mt-4">
                            <button type="submit" class="btn btn-success px-4"><i class="bi bi-save me-2"></i>
                                Simpan</button>
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
