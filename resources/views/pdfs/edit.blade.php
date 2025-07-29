@extends('layouts.app')

@section('content')
    <div class="min-h-screen flex items-center justify-center bg-gray-100 py-12 px-4 sm:px-6 lg:px-8">
        <div class="bg-white shadow-2xl rounded-3xl w-full max-w-4xl p-10">
            <h2 class="text-3xl font-bold text-center text-gray-800 mb-8">✏️ Edit PDF</h2>

            <form action="{{ route('pdfs.update', $pdf) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')
                @include('pdfs._form', [
                    'form_action' => route('pdfs.update', $pdf),
                    'method' => 'PUT',
                    'submit_label' => 'Perbarui PDF',
                    'pdf' => $pdf
                ])
            </form>
        </div>
    </div>
@endsection
