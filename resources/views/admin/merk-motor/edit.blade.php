@extends('layouts.admin')
@section('title', 'Edit Merk Motor')
@section('content')<div class="card"><div class="card-header"><h5>Edit Merk Motor</h5></div><div class="card-body"><form method="POST" action="{{ route('admin.merk-motor.update', $merk) }}">@method('PUT')@include('admin.merk-motor._form')</form></div></div>@endsection
