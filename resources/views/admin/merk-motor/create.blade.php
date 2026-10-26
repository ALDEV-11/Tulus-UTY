@extends('layouts.admin')
@section('title', 'Tambah Merk Motor')
@section('content')<div class="card"><div class="card-header"><h5>Tambah Merk Motor</h5></div><div class="card-body"><form method="POST" action="{{ route('admin.merk-motor.store') }}">@include('admin.merk-motor._form')</form></div></div>@endsection
