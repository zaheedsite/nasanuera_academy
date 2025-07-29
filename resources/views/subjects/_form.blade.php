@extends('layouts.app')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-100 py-12 px-4 sm:px-6 lg:px-8">
    <div class="bg-white shadow-2xl rounded-3xl w-full max-w-4xl p-10">
        <h2 class="text-3xl font-bold text-center text-gray-800 mb-8">
            {{ $submit_label ?? 'Tambah Subject' }}
        </h2>

        <form action="{{ $form_action }}" method="POST" class="space-y-6" enctype="multipart/form-data">
            @csrf
            @if ($method === 'PUT')
                @method('PUT')
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Judul --}}
                <div>
                    <label for="title" class="block font-semibold text-gray-700 mb-1">Judul Subject</label>
                    <input type="text" name="title" id="title" value="{{ old('title', $subject->title ?? '') }}"
                        class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-yellow-400 focus:outline-none"
                        required>
                </div>

                {{-- Deskripsi --}}
                <div>
                    <label for="description" class="block font-semibold text-gray-700 mb-1">Deskripsi</label>
                    <textarea name="description" id="description" rows="3"
                        class="w-full px-4 py-2 rounded-lg border border-gray-300 resize-y focus:ring-2 focus:ring-yellow-400 focus:outline-none"
                        required>{{ old('description', $subject->description ?? '') }}</textarea>
                </div>

                {{-- Role --}}
                <div>
                    <label for="role" class="block font-semibold text-gray-700 mb-1">Untuk Role</label>
                    <select name="role" id="role"
                        class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-yellow-400 focus:outline-none"
                        required>
                        <option value="">Pilih Role</option>
                        <option value="star_seller" {{ old('role', $subject->role ?? '') == 'star_seller' ? 'selected' : '' }}>Star Seller</option>
                        <option value="mitra_usaha" {{ old('role', $subject->role ?? '') == 'mitra_usaha' ? 'selected' : '' }}>Mitra Usaha</option>
                        <option value="guest" {{ old('role', $subject->role ?? '') == 'guest' ? 'selected' : '' }}>Guest</option>
                    </select>
                </div>

                {{-- Upload Thumbnail --}}
                <div>
                    <label for="thumbnail" class="block font-semibold text-gray-700 mb-1">Thumbnail</label>
                    <input type="file" name="thumbnail" id="thumbnail"
                        class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-yellow-400 focus:outline-none"
                        {{ $method === 'POST' ? 'required' : '' }} accept="image/*" onchange="previewThumbnail(event)">

                    {{-- Preview Thumbnail --}}
                    <div class="mt-2">
                        <img id="thumbnailPreview"
                            src="{{ !empty($subject->thumbnail)
                                    ? (\Illuminate\Support\Str::startsWith($subject->thumbnail, 'http')
                                        ? $subject->thumbnail
                                        : asset('storage/' . $subject->thumbnail))
                                    : '' }}"
                            class="rounded-lg border h-48 object-cover w-full"
                            alt="Preview Thumbnail"
                            style="{{ empty($subject->thumbnail) ? 'display: none;' : '' }}">
                    </div>
                </div>

                {{-- Jumlah Video --}}
                <div>
                    <label for="jumlah_video" class="block font-semibold text-gray-700 mb-1">Jumlah Video</label>
                    <input type="number" name="jumlah_video" id="jumlah_video"
                        value="{{ old('jumlah_video', $subject->jumlah_video ?? '') }}"
                        class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-yellow-400 focus:outline-none">
                </div>
            </div>

            {{-- Tombol --}}
            <div class="flex justify-end space-x-4 pt-6">
                <button type="submit"
                    class="px-6 py-2 rounded-lg text-white font-semibold bg-yellow-500 hover:bg-yellow-600 transition duration-200">
                    {{ $submit_label ?? 'Tambah Data' }}
                </button>
                <a href="{{ route('subjects.index') }}"
                    class="px-6 py-2 rounded-lg text-white font-semibold bg-gray-400 hover:bg-gray-500 transition duration-200">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>

{{-- Script Preview Thumbnail Lokal --}}
<script>
    function previewThumbnail(event) {
        const reader = new FileReader();
        reader.onload = function () {
            const output = document.getElementById('thumbnailPreview');
            output.src = reader.result;
            output.style.display = 'block';
        };
        reader.readAsDataURL(event.target.files[0]);
    }
</script>
@endsection
