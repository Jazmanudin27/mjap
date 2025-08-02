@extends('layouts.template')
@section('contents')
    <section class="section dashboard">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title" style="text-align: center">Data Users</h5>
                        @if (!empty($PermissionTambah))
                            <div class="col-md-3 pb-3">
                                <a href="{{ url('users/create') }}" class="btn btn-sm btn-primary">Tambah Data</a>
                            </div>
                        @endif
                        <div class="col-md-12 pb-3">
                            <div class="row">
                                <div class="col-md-3">
                                    <input type="text" name="nama_lengkap" id="nama_lengkap"
                                        class="form-control form-control-sm" placeholder="Nama Lengkap"
                                        value="{{ request('nama_lengkap') }}">
                                </div>
                                <div class="col-md-3">
                                    <input type="text" name="email" id="email" class="form-control form-control-sm"
                                        placeholder="Email" value="{{ request('email') }}">
                                </div>
                                <div class="col-md-2">
                                    <select name="status" id="status" class="form-control form-control-sm">
                                        <option value="">Semua Status</option>
                                        <option value="Aktif" {{ request('status') == 'Aktif' ? 'selected' : '' }}>Aktif
                                        </option>
                                        <option value="Tidak Aktif"
                                            {{ request('status') == 'Tidak Aktif' ? 'selected' : '' }}>
                                            Tidak Aktif
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Nama Karyawan</th>
                                        <th>Email</th>
                                        <th>Role</th>
                                        <th>Terakhir Aktif</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="detailUsers">

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script>
        $(document).ready(function() {

            showUsers();

            function showUsers() {

                var nama = $('#nama_lengkap').val();
                var email = $('#email').val();
                var status = $('#status').val();

                $.ajax({
                    url: "{{ url('detailUsers') }}",
                    method: 'GET',
                    data: {
                        nama_lengkap: nama,
                        email: email,
                        status: status
                    },
                    success: function(response) {
                        $('#detailUsers').html(response);
                    }
                });
            }

            $('#nama_lengkap,#email,#status').on('input', function(e) {
                e.preventDefault();
                showUsers();
            });

            // $('#status').on('click', function(e) {
            //     e.preventDefault();
            //     showUsers();
            // });


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
                            'Data Users berhasil dihapus.',
                            'success'
                        )
                    }
                })
            });
        });
    </script>
@endsection
