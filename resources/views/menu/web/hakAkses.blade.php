@php
    $roles = Auth::user()->role;
@endphp
@if ($roles == 'super admin')
    @include('menu.web.superAdmin')
@elseif ($roles == 'owner')
    @include('menu.web.owner')
@elseif ($roles == 'admin')
    @include('menu.web.admin')
@elseif ($roles == 'admin pembelian')
    @include('menu.web.adminPembelian')
@elseif ($roles == 'owner')
    @include('menu.web.owner')
@elseif ($roles == 'spv sales')
    @include('menu.web.spvSales')
@elseif ($roles == 'admin penjualan')
    @include('menu.web.adminPenjualan')
@elseif ($roles == 'admin gudang')
    @include('menu.web.adminGudang')
@endif
