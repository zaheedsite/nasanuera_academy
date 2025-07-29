@extends('layouts.app')

@section('title', 'Home')

@section('content')
    <h3>Pilih Role</h3>
    <a href="{{ route('subjects.index', 'star_seller') }}" class="btn btn-warning">Star Seller</a>
    <a href="{{ route('subjects.index', 'mitra_usaha') }}" class="btn btn-success">Mitra Usaha</a>
@endsection
