@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-[#fefbee] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full bg-white rounded-3xl shadow-xl p-6">
        {{-- Thumbnail Subject --}}
        <img src="{{ \Illuminate\Support\Str::startsWith($subject->thumbnail, 'http') ? $subject->thumbnail : asset('storage/' . $subject->thumbnail) }}"
            alt="{{ $subject->title }}"
            class="w-full h-50 object-cover rounded-xl mb-6"
            onerror="this.style.display='none'">

        {{-- Judul Deskripsi --}}
        <h2 class="text-xl text-yellow-600 font-bold mb-3 uppercase tracking-wide">Deskripsi</h2>

        {{-- Deskripsi rata kanan-kiri --}}
        <p class="text-gray-700 text-sm leading-relaxed text-justify mb-6">
            {{ $subject->description }}
        </p>

        {{-- Role & Jumlah Video --}}
        <div class="flex flex-wrap gap-3">
            <span class="px-4 py-1 rounded-full bg-yellow-500 text-white text-sm font-semibold shadow">
                {{ ucfirst($subject->role) }}
            </span>
            <span class="px-4 py-1 rounded-full bg-green-600 text-white text-sm font-semibold shadow">
                {{ $subject->jumlah_video }} Video
            </span>
        </div>
    </div>
</div>
@endsection
