@extends('mobile.layout')
@section('title', 'Detail Retur')
@section('header', 'Detail Retur')

@section('content')
    <div class="container py-3">

        @php
            $totalRetur = $detail->sum('subtotal_retur');
        @endphp

        {{-- Info Retur --}}
        <div class="card shadow-sm border-0 rounded-4 mb-3">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="fw-bold text-danger fs-6">
                            <i class="bi bi-arrow-counterclockwise me-1"></i> {{ $retur->no_retur }}
                        </div>
                        <div class="text-muted small">
                            <i class="bi bi-calendar-event me-1"></i>
                            {{ \Carbon\Carbon::parse($retur->tanggal)->format('d M Y') }}
                        </div>
                    </div>
                    <div class="text-end">
                        <span class="badge bg-warning text-dark">
                            {{ $retur->jenis_retur == 'GB' ? 'Ganti Barang' : 'Potong Faktur' }}
                        </span>
                    </div>
                </div>
                <hr class="my-2">
                <div class="small text-muted">
                    <div class="mb-1"><i class="bi bi-person-circle me-1"></i> {{ $retur->nama_pelanggan }}</div>
                    <div class="mb-1"><i class="bi bi-geo-alt me-1"></i> {{ $retur->alamat_toko }}</div>
                    <div><i class="bi bi-receipt me-1"></i> Faktur: {{ $retur->no_faktur }}</div>
                </div>
            </div>
        </div>

        {{-- Detail Barang Retur --}}
        <div class="card border-0 shadow-sm rounded-4 mb-3">
            <div class="card-body">
                <h6 class="fw-bold mb-3"><i class="bi bi-basket me-1"></i> Barang Diretur</h6>
                <div class="table-responsive">
                    <table class="table table-sm align-middle mb-0" style="font-size: 13px;">
                        <tbody>
                            @foreach ($detail as $item)
                                <tr class="table-light">
                                    <td colspan="3" class="fw-semibold text-danger">{{ $item->nama_barang }}</td>
                                </tr>
                                <tr>
                                    <td class="small">{{ $item->qty }} {{ $item->satuan }}</td>
                                    <td class="small">@ Rp{{ number_format($item->harga_retur, 0, ',', '.') }}</td>
                                    <td class="text-end small">Rp{{ number_format($item->subtotal_retur, 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    <table class="table table-borderless table-sm" style="font-size: 14px;">
                        <tr>
                            <td class="text-end fw-semibold">Total Retur</td>
                            <td class="text-end text-danger" style="width: 40%;">
                                Rp{{ number_format($totalRetur, 0, ',', '.') }}
                            </td>
                        </tr>
                        <tr>
                            <td class="text-end fw-semibold">Keterangan</td>
                            <td class="text-end text-muted">{{ $retur->keterangan ?? '-' }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
