@php($pageTitle = 'Tambah Layanan')
@include('admin.partials.page-head')
<div class="card"><div class="card-header"><h5>Tambah Layanan</h5></div><div class="card-body"><form method="POST" action="{{ route('admin.layanan.store') }}">@include('admin.layanan._form')</form></div></div>
@include('admin.partials.page-foot')
