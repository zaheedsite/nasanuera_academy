@extends('layouts.app')

@section('content')
<div class="p-6 max-w-4xl mx-auto">
    <form action="{{ route('videos.update', $video->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        @include('videos._form', ['submitLabel' => 'Update', 'video' => $video])
    </form>
</div>
@endsection
