@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card shadow rounded-3 p-4">
        <h2 class="mb-4">Edit Subject</h2>

        <form action="{{ route('subjects.update', $subject->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            @include('subjects._form', [
                'form_action' => route('subjects.update', $subject->id),
                'method' => 'PUT',
                'submit_label' => 'Update',
                'subject' => $subject
            ])

            <div class="mt-3">
                <button type="submit" class="btn btn-success">Update</button>
            </div>
        </form>
    </div>
</div>
@endsection
