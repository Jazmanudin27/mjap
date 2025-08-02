@extends('layouts.template')
@section('contents')
    <section class="section dashboard">
        <div class="row">
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title" style="text-align: center">Edit Data User</h5>
                        <form action="{{ route('users.update', $user->id) }}" method="post" autocomplete="off">
                            @csrf
                            @method('PUT')

                            <div class="row mb-3">
                                <div class="col-sm-4">
                                    <label for="name" class="col-sm-12 col-form-label">Nama User</label>
                                </div>
                                <div class="col-sm-8">
                                    <input type="text" name="name" required class="form-control"
                                        value="{{ old('name', $user->name) }}">
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-sm-4">
                                    <label for="email" class="col-sm-12 col-form-label">Email</label>
                                </div>
                                <div class="col-sm-8">
                                    <input type="email" name="email" required class="form-control"
                                        value="{{ old('email', $user->email) }}">
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-sm-4">
                                    <label for="password" class="col-sm-12 col-form-label">Password</label>
                                </div>
                                <div class="col-sm-8">
                                    <input type="password" name="password" class="form-control" placeholder="Password baru">
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-sm-4">
                                    <label for="role_id" class="col-sm-12 col-form-label">Role</label>
                                </div>
                                <div class="col-sm-8">
                                    <select name="role_id" id="role_id" class="form-control">
                                        <option value="">Pilih Role</option>
                                        @php
                                            $roles = DB::table('roles')->orderBy('name', 'ASC')->get();
                                        @endphp
                                        @foreach ($roles as $k)
                                            <option value="{{ $k->id }}"
                                                {{ $user->role_id == $k->id ? 'selected' : '' }}>{{ $k->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-sm-4">
                                    <label for="nik" class="col-sm-12 col-form-label">Karyawan</label>
                                </div>
                                <div class="col-sm-8">
                                    <select name="nik" id="nik" class="form-control">
                                        <option value="">Pilih Karyawan</option>
                                        @php
                                            $karyawan = DB::table('hrd_karyawan')
                                                ->orderBy('nama_lengkap', 'ASC')
                                                ->get();
                                        @endphp
                                        @foreach ($karyawan as $k)
                                            <option value="{{ $k->nik }}"
                                                {{ $user->nik == $k->nik ? 'selected' : '' }}>{{ $k->nama_lengkap }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <div class="row mb-3">
                                <div class="col-sm-12">
                                    <button type="submit" class="btn btn-primary w-100">Update</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
