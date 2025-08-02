@extends('layouts.template')
@section('contents')
    <section class="section profile">
        <div class="row">
            <div class="col-xl-3">
                <div class="card shadow">
                    <div class="card-body profile-card pt-4 d-flex flex-column align-items-center">
                        <img src="{{ asset('assets/img/profile-img.jpg') }}" alt="Profile" class="rounded-circle shadow"
                            style="width: 120px; height: 120px; object-fit: cover;">
                        <h4 class="mt-3 mb-1 fw-bold text-center">{{ $karyawan->nama_lengkap }}</h4>
                        <span class="text-muted text-center">{{ $karyawan->nama_jabatan }}</span>
                        <span class="badge bg-secondary mt-2">{{ $karyawan->nama_department ?? 'N/A' }}</span>
                    </div>
                </div>
            </div>

            <div class="col-xl-9">
                <div class="card shadow">
                    <div
                        class="card-header bg-primary text-white py-2 px-3 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Detail Karyawan</h5>
                        <a href="{{ route('viewKaryawan') }}" class="btn btn-light btn-sm">Back</a>
                    </div>
                    <div class="card-body">
                        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-2 g-1 pt-3">
                            @php
                                $data = [
                                    'NIK' => $karyawan->nik,
                                    'Nama Lengkap' => $karyawan->nama_lengkap,
                                    'Alamat' => $karyawan->alamat,
                                    'Telepon' => $karyawan->nomor_telepon,
                                    'Email' => $karyawan->email,
                                    'Tanggal Lahir' => \Carbon\Carbon::parse($karyawan->tanggal_lahir)->format('d-m-Y'),
                                    'Jenis Kelamin' => $karyawan->jenis_kelamin,
                                    'Jabatan' => $karyawan->nama_jabatan ?? 'N/A',
                                    'Departemen' => $karyawan->nama_department ?? 'N/A',
                                    'Tanggal Masuk' => \Carbon\Carbon::parse($karyawan->tgl_masuk)->format('d-m-Y'),
                                    'Status Karyawan' => $karyawan->status_karyawan,
                                    'Nomor KTP' => $karyawan->nomor_ktp,
                                    'NPWP' => $karyawan->npwp,
                                    'Pendidikan Terakhir' => $karyawan->pendidikan_terakhir,
                                    'Nomor Rekening Bank' => $karyawan->nomor_rekening_bank,
                                    'Nama Bank' => $karyawan->nama_bank,
                                    'Status Pernikahan' => $karyawan->status_pernikahan,
                                    'Jumlah Anak' => $karyawan->jumlah_anak,
                                    'Catatan' => $karyawan->catatan,
                                    'Status' => $karyawan->status == 1 ? 'Tetap' : ($karyawan->status == 2 ? 'Kontrak' : 'Lainnya'),
                                    'Kantor' => $karyawan->nama_kantor ?? 'N/A',
                                    'Group' => $karyawan->nama_group ?? 'N/A'
                                ];
                            @endphp

                            @foreach ($data as $label => $value)
                                <div class="col">
                                    <div class="p-1 border rounded bg-light h-100">
                                        <div class="fw-semibold text-primary">{{ $label }}</div>
                                        <div class="text-dark small">{{ $value }}</div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
