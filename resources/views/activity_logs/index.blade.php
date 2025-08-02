@extends('layouts.template')
@section('contents')
    <section class="section dashboard">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title" style="text-align: center">Activity Logs</h5>
                        <div class="col-md-12 pb-3">
                            <div class="row">
                                <div class="col-md-2 mt-3">
                                    <select id="id" required class="form-control form-control-sm">
                                        <option value="">Semua Users</option>
                                        @php
                                            $users = DB::table('users')->orderBy('name', 'ASC')->get();
                                        @endphp
                                        @foreach ($users as $k)
                                            <option value="{{ $k->id }}">{{ $k->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama User</th>
                                        <th>Aksi</th>
                                        <th>Deskripsi</th>
                                        <th>IP Address</th>
                                        <th>Waktu</th>
                                    </tr>
                                </thead>
                                <tbody id="showLogs">

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <script>
        $(document).ready(function () {

            showLogs();

            function showLogs() {

                var id = $('#id').val();

                $.ajax({
                    url: "{{ url('showLogs') }}",
                    method: 'GET',
                    data: {
                        id: id,
                    },
                    success: function (response) {
                        $('#showLogs').html(response);
                    }
                });
            }

            $('#id').on('input', function (e) {
                e.preventDefault();
                showLogs();
            });

        });
    </script>
@endsection
