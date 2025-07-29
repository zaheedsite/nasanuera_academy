@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card shadow rounded-3 p-4">
        <h2 class="mb-4">Tambah Subject</h2>

        <form action="{{ route('subjects.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            @include('subjects._form', [
                'form_action' => route('subjects.store'),
                'method' => 'POST',
                'submit_label' => 'Simpan',
                'subject' => new \App\Models\Subject
            ])

            <div class="mt-3">
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>
@endsection
