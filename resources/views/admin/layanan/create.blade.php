@extends('layouts.admin')
@section('title', 'Tambah Layanan')
@section('content')<div class="card"><div class="card-header"><h5>Tambah Layanan</h5></div><div class="card-body"><form method="POST" action="{{ route('admin.layanan.store') }}">@include('admin.layanan._form')</form></div></div>@endsection
