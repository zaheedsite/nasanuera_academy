@extends('layouts.app')

@section('content')
    <div class="min-h-screen flex items-center justify-center bg-gray-100 py-12 px-4 sm:px-6 lg:px-8">
        <div class="bg-white shadow-2xl rounded-3xl w-full max-w-3xl p-10">
            {{-- Preview Video --}}
            <div class="mb-8">
                <label class="block text-lg font-semibold text-gray-700 mb-2">Preview Video</label>
                <div class="aspect-w-16 aspect-h-9 rounded-xl overflow-hidden shadow border">
                    <video class="w-full h-full" controls>
                        <source src="{{ $video->video_url }}"
                            type="video/{{ pathinfo($video->video_url, PATHINFO_EXTENSION) }}">
                        Browser Anda tidak mendukung pemutar video.
                    </video>
                </div>
            </div>

            {{-- Judul Halaman --}}
            <h2 class="text-3xl font-bold text-center text-gray-800 mb-8">
                Detail Video
            </h2>

            {{-- Informasi Video --}}
            <div class="space-y-5 mb-8">
                {{-- Judul Video --}}
                <div class="flex flex-col border-b pb-3">
                    <span class="text-sm text-gray-500">Judul Video</span>
                    <span class="text-gray-900 font-semibold">{{ $video->title }}</span>
                </div>

                {{-- Subject --}}
                <div class="flex flex-col border-b pb-3">
                    <span class="text-sm text-gray-500">Subject</span>
                    <span class="text-gray-900 font-semibold">{{ $video->subject->title ?? '-' }}</span>
                </div>

                {{-- Durasi --}}
                <div class="flex flex-col border-b pb-3">
                    <span class="text-sm text-gray-500">Durasi</span>
                    <span class="text-gray-900 font-semibold">{{ $video->duration }}</span>
                </div>

                {{-- Deskripsi --}}
                <div class="flex flex-col border-b pb-3">
                    <span class="text-sm text-gray-500">Deskripsi</span>
                    <p class="text-gray-900 text-justify">{{ $video->description }}</p>
                </div>

                {{-- URL Video --}}
                <div class="flex flex-col border-b pb-3">
                    <span class="text-sm text-gray-500">URL Video</span>
                    <a href="{{ $video->video_url }}" target="_blank"
                        class="text-blue-600 hover:underline break-words">{{ $video->video_url }}</a>
                </div>
            </div>

            {{-- Thumbnail --}}
            @if ($video->thumbnail)
                <div class="mb-8 text-center">
                    <span class="block text-sm text-gray-500 mb-2">Thumbnail</span>
                    <img src="{{ asset('storage/' . $video->thumbnail) }}" alt="Thumbnail"
                        class="mx-auto rounded-lg border w-full max-w-xs object-cover shadow">
                </div>
            @endif

            {{-- Tombol Kembali --}}
            <div class="text-center">
                <a href="{{ route('videos.index') }}"
                    class="inline-block text-blue-600 hover:underline text-sm font-medium">
                    ← Kembali ke daftar video
                </a>
            </div>
        </div>
    </div>
@endsection
