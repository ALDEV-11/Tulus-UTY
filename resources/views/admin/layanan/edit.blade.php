@extends('layouts.admin')
@section('title', 'Edit Layanan')
@section('content')<div class="card"><div class="card-header"><h5>Edit Layanan</h5></div><div class="card-body"><form method="POST" action="{{ route('admin.layanan.update', $layanan) }}">@method('PUT')@include('admin.layanan._form')</form></div></div>@endsection
