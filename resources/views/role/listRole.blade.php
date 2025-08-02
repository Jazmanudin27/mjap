@extends('layouts.template')
@section('contents')
    <section class="section dashboard">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title" style="text-align: center">Data Role</h5>
                        @if (!empty($PermissionTambah))
                            <a href="{{ url('tambahRole') }}" class="btn btn-sm btn-primary">Tambah Data</a>
                        @endif
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>Nama Role</th>
                                    <th>Created At</th>
                                    <th>Updated At</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data as $index => $d)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $d->name }}</td>
                                        <td>{{ $d->created_at }}</td>
                                        <td>{{ $d->updated_at }}</td>
                                        <td>
                                            @if (!empty($PermissionDelete))
                                                <a data-href="{{ url('deleteRole', $d->id) }}"
                                                    class="btn btn-sm btn-danger delete"><i class="fa fa-trash"></i></a>
                                            @endif
                                            @if (!empty($PermissionEdit))
                                                <a href="{{ url('editRole', $d->id) }}" class="btn btn-sm btn-warning"><i
                                                        class="fa fa-pencil"></i></a>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                    </div>
                </div>
            </div>
        </div>
    </section>

    <script>
        $(document).ready(function() {

            $('.delete').on("click", function(e) {
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
                        Swal.fire(
                            'Deleted!',
                            'Your file has been deleted.',
                            'success'
                        )
                    }
                })
            });
        });
    </script>
@endsection
