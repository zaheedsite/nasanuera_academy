@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto p-4">
    <h1 class="text-2xl font-bold mb-2">{{ $pdf->title }}</h1>
    <p class="text-gray-600 mb-2">Subject: <strong>{{ $pdf->subject->title }}</strong></p>
    <p class="text-gray-600 mb-2">Jumlah Halaman: {{ $pdf->pages }}</p>
    <p class="mb-4">PDF URL: <a href="{{ $pdf->pdf_url }}" target="_blank" class="text-blue-600 underline">Lihat PDF</a></p>
    <a href="{{ route('pdfs.index') }}" class="text-emerald-600 hover:underline">← Kembali ke daftar PDF</a>
</div>
@endsection
