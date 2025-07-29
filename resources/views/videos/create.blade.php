@extends('layouts.app')

@section('content')
<div class="p-6 max-w-4xl mx-auto">
    <form action="{{ route('videos.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @include('videos._form', ['submitLabel' => 'Tambah'])
    </form>
</div>
@endsection
