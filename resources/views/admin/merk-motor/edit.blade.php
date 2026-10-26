@php($pageTitle = 'Edit Merk Motor')
@include('admin.partials.page-head')
<div class="card"><div class="card-header"><h5>Edit Merk Motor</h5></div><div class="card-body"><form method="POST" action="{{ route('admin.merk-motor.update', $merk) }}">@method('PUT')@include('admin.merk-motor._form')</form></div></div>
@include('admin.partials.page-foot')
