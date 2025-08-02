<ul class="list-group list-group-flush">
    @forelse ($history as $item)
        @php
            $isBatal = $item->batal == '1';
        @endphp
        <a href="{{ route('detailPenjualanMobile', $item->no_faktur) }}"
            class="text-decoration-none {{ $isBatal ? 'text-danger' : 'text-primary' }}">
            <li class="list-group-item px-3 py-3 mb-3 shadow-sm rounded-4 border-0"
                style="background-color: {{ $isBatal ? '#fdecea' : '#f8f9fa' }};">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div
                            class="fw-bold {{ $isBatal ? 'text-danger text-decoration-line-through' : 'text-primary' }}">
                            #{{ $item->no_faktur ?? '-' }}
                        </div>
                        <div class="text-secondary {{ $isBatal ? 'text-decoration-line-through' : '' }}">
                            {{ $item->nama_pelanggan ?? '-' }}
                        </div>
                        <div class="text-secondary small">
                            <i class="bi bi-person-badge me-1"></i> {{ $item->nama_sales }}
                        </div>
                        <div class="text-muted small mb-1">
                            <i class="bi bi-calendar-event me-1"></i>
                            {{ \Carbon\Carbon::parse($item->tanggal)->format('d M Y') }}
                        </div>

                        <div class="mt-1 d-flex flex-wrap gap-1" style="zoom:90%">
                            <span
                                class="badge rounded-pill {{ $item->jenis_transaksi === 'T' ? 'bg-success' : 'bg-warning text-dark' }}">
                                <i
                                    class="bi {{ $item->jenis_transaksi === 'T' ? 'bi-cash' : 'bi-credit-card' }} me-1"></i>
                                {{ $item->jenis_transaksi === 'T' ? 'Tunai' : 'Kredit' }}
                            </span>

                            @if ($item->jenis_transaksi === 'T' && $item->jenis_bayar)
                                <span class="badge rounded-pill bg-info text-dark">
                                    <i class="bi bi-wallet2 me-1"></i>{{ ucfirst($item->jenis_bayar) }}
                                </span>
                            @endif

                            @if ($isBatal)
                                <span class="badge rounded-pill bg-danger text-white">
                                    <i class="bi bi-x-circle me-1"></i> Batal
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="text-end">
                        <span
                            class="badge {{ $isBatal ? 'bg-light text-muted text-decoration-line-through' : 'bg-white text-dark' }} border shadow-sm fs-6">
                            Rp{{ number_format($item->grand_total, 0, ',', '.') }}
                        </span>
                    </div>
                </div>
            </li>
        </a>
    @empty
        <li class="list-group-item text-center text-muted py-3">
            <i class="bi bi-emoji-frown fs-4 d-block mb-1"></i>
            Tidak ada data penjualan.
        </li>
    @endforelse
</ul>
