@extends('layouts.app')

@section('content')
    <div class="min-h-screen flex items-center justify-center bg-gray-100 py-12 px-4 sm:px-6 lg:px-8">
        <div class="bg-white shadow-2xl rounded-3xl w-full max-w-4xl p-10">
            <h2 class="text-3xl font-bold text-center text-gray-800 mb-8">➕ Tambah PDF Baru</h2>

            <form action="{{ route('pdfs.store') }}" method="POST" class="space-y-6">
                @csrf
                @include('pdfs._form', [
                    'form_action' => route('pdfs.store'),
                    'method' => 'POST',
                    'submit_label' => 'Tambah PDF',
                    'pdf' => new \App\Models\Pdf()
                ])
            </form>
        </div>
    </div>
@endsection
