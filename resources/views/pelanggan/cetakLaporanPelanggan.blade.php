@extends('layouts.print')

@section('title', 'Laporan Data Pelanggan')

@section('periode')
    Dicetak Tanggal: {{ date('d-m-Y H:i:s') }} <br>
@endsection

@section('content')
    <table border="1" cellspacing="0" cellpadding="5" width="100%" style="font-size: 10px; border-collapse: collapse;">
        <thead style="background-color: #f1f1f1;">
            <tr>
                <th>No</th>
                <th>Kode Pelanggan</th>
                <th>Tanggal Register</th>
                <th>Nama Pelanggan</th>
                <th>Alamat Pelanggan</th>
                <th>Alamat Toko</th>
                <th>No HP</th>
                <th>Kepemilikan</th>
                <th>Omset Toko</th>
                <th>Limit</th>
                <th>Hari</th>
                <th>Kunjungan</th>
                <th>Latitude</th>
                <th>Longitude</th>
                <th>Status</th>
                <th>Wilayah</th>
                <th>Email</th>
                <th>Created At</th>
                <th>Updated At</th>
            </tr>
        </thead>
        <tbody>
            @php $no = 1; @endphp
            @foreach ($pelanggan as $p)
                @php
                    $hariIni = date('l'); // contoh: Monday, Tuesday, dst
                    $hariMapping = [
                        'Senin' => 'Monday',
                        'Selasa' => 'Tuesday',
                        'Rabu' => 'Wednesday',
                        'Kamis' => 'Thursday',
                        'Jumat' => 'Friday',
                        'Sabtu' => 'Saturday',
                        'Minggu' => 'Sunday',
                    ];

                    $kunjunganHariIni = false;

                    if (isset($hariMapping[$p->hari]) && $hariMapping[$p->hari] == $hariIni) {
                        if (strtolower($p->kunjungan) == 'mingguan') {
                            $kunjunganHariIni = true;
                        } elseif (strtolower($p->kunjungan) == 'dua_mingguan') {
                            $mingguKe = date('W'); // minggu ke berapa dalam tahun
                            if ($mingguKe % 2 == 0) {
                                // jika genap (misal kamu hitungnya di minggu genap)
                                $kunjunganHariIni = true;
                            }
                        }
                    }
                @endphp
                <tr>
                    <td class="text-center">{{ $no++ }}</td>
                    <td>{{ $p->kode_pelanggan }}</td>
                    <td>{{ $p->tanggal_register }}</td>
                    <td>{{ $p->nama_pelanggan }}</td>
                    <td>{{ $p->alamat_pelanggan }}</td>
                    <td>{{ $p->alamat_toko }}</td>
                    <td>{{ $p->no_hp_pelanggan }}</td>
                    <td>{{ $p->kepemilikan }}</td>
                    <td class="text-end">{{ number_format($p->omset_toko) }}</td>
                    <td class="text-end">{{ number_format($p->limit_pelanggan) }}</td>
                    <td>{{ $p->hari }}</td>
                    <td>
                        {{ $p->kunjungan }}
                        @if ($kunjunganHariIni)
                            <span style="color: green; font-weight: bold;">(KUNJUNGAN HARI INI)</span>
                        @endif
                    </td>
                    <td class="text-end">{{ $p->latitude }}</td>
                    <td class="text-end">{{ $p->longitude }}</td>
                    <td>{{ $p->status == 1 ? 'Aktif' : 'Nonaktif' }}</td>
                    <td>{{ $p->nama_wilayah }}</td>
                    <td>{{ $p->email }}</td>
                    <td>{{ $p->created_at }}</td>
                    <td>{{ $p->updated_at }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
