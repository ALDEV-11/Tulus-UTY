@extends('customer.layouts.app')

@section('title', 'Cek Slot')
@section('activeMenu', 'cek-slot')

@section('content')
    @include('customer.partials.page-header', [
        'title' => 'Cek Slot Servis',
        'subtitle' => 'Pilih tanggal untuk melihat ketersediaan jam servis motor.',
    ])

    @include('partials.slot-availability', ['showHeading' => false])
@endsection
