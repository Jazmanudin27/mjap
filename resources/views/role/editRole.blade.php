@extends('layouts.template')
@section('contents')
    <section class="section dashboard">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title" style="text-align: center">Tambah Role & Permission</h5>
                        <form action="{{ url('updateRole', $data->id) }}" method="post" autocomplete="off">
                            @csrf
                            <div class="row mb-5">
                                <div class="col-sm-2">
                                    <label for="inputText" class="col-sm-12 col-form-label">Nama Role</label>
                                </div>
                                <div class="col-sm-10">
                                    <input type="text" name="name" value="{{ $data->name }}" required
                                        class="form-control">
                                </div>
                            </div>
                            <div class="row mb-3">
                                @foreach ($getPermission as $value)
                                    <div class="col-sm-2">
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <h5><b>{{ $value['name'] }}</b></h5>
                                            </div>
                                            @foreach ($value['group'] as $group)
                                                @php
                                                    $getPermissionRole = DB::table('permissions_roles')
                                                        ->where('role_id', $data->id)
                                                        ->get();
                                                    $checked = $getPermissionRole->contains(
                                                        'permission_id',
                                                        $group['id'],
                                                    )
                                                        ? 'checked'
                                                        : '';
                                                @endphp

                                                <div class="col-sm-12">
                                                    <label>
                                                        <input type="checkbox" {{ $checked }}
                                                            value="{{ $group['id'] }}" name="permission_id[]">
                                                        {{ $group['name'] }}
                                                    </label>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <div class="row mb-3">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <button type="submit" class="btn btn-primary btn-block">Update</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
