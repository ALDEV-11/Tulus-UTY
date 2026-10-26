@php($pageTitle = 'Tambah Merk Motor')
@include('admin.partials.page-head')
<div class="card"><div class="card-header"><h5>Tambah Merk Motor</h5></div><div class="card-body"><form method="POST" action="{{ route('admin.merk-motor.store') }}">@include('admin.merk-motor._form')</form></div></div>
@include('admin.partials.page-foot')
