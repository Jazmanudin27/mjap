<ul class="sidebar-nav" id="sidebar-nav">
    <li class="nav-item">
        <a class="nav-link" href="{{ url('dashboard') }}">
            <i class="bi bi-speedometer2"></i>
            <span>Dashboard</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{ url('mapsPelanggan') }}">
            <i class="bi bi-speedometer2"></i>
            <span>Maping Pelanggan</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{ url('trackingSales') }}">
            <i class="bi bi-speedometer2"></i>
            <span>Tracking Salesman</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link collapsed" data-bs-target="#data-master" data-bs-toggle="collapse" href="#">
            <i class="bi bi-archive"></i><span>Data Master</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="data-master" class="nav-content collapse" data-bs-parent="#sidebar-nav">
            <li><a href="{{ url('viewKaryawan') }}"><i class="bi bi-person-badge"></i><span>Karyawan</span></a>
            </li>
            <li><a href="{{ url('viewBarang') }}"><i class="bi bi-box-seam"></i><span>Barang</span></a>
            </li>
            <li><a href="{{ url('viewSupplier') }}"><i class="bi bi-truck"></i><span>Supplier</span></a>
            </li>
            <li><a href="{{ url('viewPelanggan') }}"><i class="bi bi-people"></i><span>Pelanggan</span></a>
            </li>
        </ul>
    </li>

    <li class="nav-item">
        <a class="nav-link collapsed" data-bs-target="#pembelian" data-bs-toggle="collapse" href="#">
            <i class="bi bi-cart-plus"></i><span>Pembelian</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="pembelian" class="nav-content collapse" data-bs-parent="#sidebar-nav">
            <li><a href="{{ url('viewPO') }}"><i class="bi bi-circle"></i><span>Purchase Order</span></a>
            </li>
        </ul>
        <ul id="pembelian" class="nav-content collapse" data-bs-parent="#sidebar-nav">
            <li><a href="{{ url('viewPembelian') }}"><i class="bi bi-circle"></i><span>Pembelian</span></a>
            </li>
        </ul>
        <ul id="pembelian" class="nav-content collapse" data-bs-parent="#sidebar-nav">
            <li><a href="{{ url('viewReturPembelian') }}"><i class="bi bi-circle"></i><span>Retur
                        Pembelian</span></a>
            </li>
            <li><a href="{{ url('laporanPembelian') }}"><i class="bi bi-box-arrow-up"></i><span>Laporan</span></a>
            </li>
        </ul>
    </li>

    <li class="nav-item">
        <a class="nav-link collapsed" data-bs-target="#marketing" data-bs-toggle="collapse" href="#">
            <i class="bi bi-receipt-cutoff"></i><span>Marketing</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="marketing" class="nav-content collapse" data-bs-parent="#sidebar-nav">
            <li><a href="{{ url('viewPenjualan') }}"><i class="bi bi-basket"></i><span>Penjualan</span></a>
            </li>
            <li><a href="{{ url('viewTargetSales') }}"><i class="bi bi-basket"></i><span>Target
                        Sales</span></a>
            </li>
            <li><a href="{{ url('viewKirimanSales') }}"><i class="bi bi-basket"></i><span>Rekap
                        Kiriman</span></a>
            </li>
            <li><a href="{{ url('viewPengajuanLimit') }}"><i class="bi bi-arrow-counterclockwise"></i><span>Pengajuan
                        Limit</span></a></li>
            <li><a href="{{ url('viewReturPenjualan') }}"><i class="bi bi-arrow-counterclockwise"></i><span>Retur
                        Pejualan</span></a></li>
            <li><a href="{{ url('laporanPenjualan') }}"><i class="bi bi-clipboard-data"></i><span>Laporan
                    </span></a></li>
        </ul>
    </li>

    <li class="nav-item">
        <a class="nav-link collapsed" data-bs-target="#gudang" data-bs-toggle="collapse" href="#">
            <i class="bi bi-boxes"></i><span>Gudang</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="gudang" class="nav-content collapse" data-bs-parent="#sidebar-nav">
            <li><a href="{{ url('viewSaldoAwalGS') }}"><i class="bi bi-box-arrow-in-down"></i><span>Saldo
                        Awal GS</span></a></li>
            <li><a href="{{ url('viewSaldoAwalBS') }}"><i class="bi bi-box-arrow-in-down"></i><span>Saldo
                        Awal BS</span></a></li>
            <li><a href="{{ url('viewMutasiBarangMasuk') }}"><i class="bi bi-box-arrow-in-down"></i><span>Barang
                        Masuk</span></a></li>
            <li><a href="{{ url('viewMutasiBarangKeluar') }}"><i class="bi bi-box-arrow-up"></i><span>Barang
                        Keluar</span></a></li>
            <li><a href="{{ url('laporanGudang') }}"><i class="bi bi-box-arrow-up"></i><span>Laporan</span></a>
            </li>
        </ul>
    </li>

    {{-- Keuangan --}}
    <li class="nav-item">
        <a class="nav-link collapsed" data-bs-target="#keuangan" data-bs-toggle="collapse" href="#">
            <i class="bi bi-cash-stack"></i><span>Keuangan</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="keuangan" class="nav-content collapse" data-bs-parent="#sidebar-nav">
            <li><a href="{{ url('viewSetoranPenjualan') }}"><i class="bi bi-wallet2"></i><span>Setoran
                        Penjualan</span></a></li>
            <li><a href="{{ url('viewTransfer') }}"><i class="bi bi-wallet2"></i><span>
                        Transfer</span></a></li>
            <li><a href="{{ url('viewGiro') }}"><i class="bi bi-wallet2"></i><span>
                        Giro</span></a></li>
            <li><a href="{{ url('viewKasKecil') }}"><i class="bi bi-wallet2"></i><span>
                        Kas Kecil</span></a></li>
            <li><a href="{{ url('viewKasBank') }}"><i class="bi bi-wallet2"></i><span>
                        Kas & Bank</span></a></li>
        </ul>
    </li>

    <li class="nav-item">
        <a class="nav-link collapsed" data-bs-target="#components-nav" data-bs-toggle="collapse" href="#">
            <i class="bi bi-gear"></i><span>Settings</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="components-nav" class="nav-content collapse" data-bs-parent="#sidebar-nav">
            <li><a href="{{ url('listRole') }}"><i class="bi bi-person-gear"></i><span>Role</span></a></li>
            <li><a href="{{ url('users') }}"><i class="bi bi-person-circle"></i><span>Users</span></a></li>
            <li><a href="{{ url('activity-logs') }}"><i class="bi bi-clock-history"></i><span>Logs</span></a>
            </li>
        </ul>
    </li>
</ul>
