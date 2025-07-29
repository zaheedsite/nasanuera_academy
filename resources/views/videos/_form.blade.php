<div class="min-h-screen flex items-center justify-center bg-gray-100 py-12 px-4 sm:px-6 lg:px-8">
    <div class="bg-white shadow-2xl rounded-3xl w-full max-w-4xl p-10">
        <h2 class="text-3xl font-bold text-center text-gray-800 mb-8">
            {{ isset($video) ? 'Edit Video' : 'Tambah Video' }}
        </h2>

        <form method="POST" enctype="multipart/form-data"
              action="{{ isset($video) ? route('videos.update', $video->id) : route('videos.store') }}"
              class="space-y-6">
            @csrf
            @if(isset($video)) @method('PUT') @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                {{-- Subject --}}
                <div>
                    <label for="subject_id" class="block font-semibold text-gray-700 mb-1">Subject</label>
                    <select name="subject_id" id="subject_id"
                            class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-yellow-400 focus:outline-none"
                            required>
                        @foreach($subjects as $subject)
                            <option value="{{ $subject->id }}"
                                {{ (old('subject_id') ?? $video->subject_id ?? '') == $subject->id ? 'selected' : '' }}>
                                {{ $subject->title }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Title --}}
                <div>
                    <label for="title" class="block font-semibold text-gray-700 mb-1">Judul Video</label>
                    <input type="text" name="title" id="title"
                           value="{{ old('title', $video->title ?? '') }}"
                           class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-yellow-400 focus:outline-none"
                           required>
                </div>

                {{-- Description --}}
                <div class="md:col-span-2">
                    <label for="description" class="block font-semibold text-gray-700 mb-1">Deskripsi</label>
                    <textarea name="description" id="description" rows="4"
                              class="w-full px-4 py-2 rounded-lg border border-gray-300 resize-y focus:ring-2 focus:ring-yellow-400 focus:outline-none"
                              required>{{ old('description', $video->description ?? '') }}</textarea>
                </div>

                {{-- Video URL --}}
                <div>
                    <label for="video_url" class="block font-semibold text-gray-700 mb-1">Video URL</label>
                    <input type="url" name="video_url" id="video_url"
                           value="{{ old('video_url', $video->video_url ?? '') }}"
                           class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-yellow-400 focus:outline-none"
                           required>
                </div>

                {{-- Duration --}}
                <div>
                    <label for="duration" class="block font-semibold text-gray-700 mb-1">Durasi</label>
                    <input type="text" name="duration" id="duration"
                           value="{{ old('duration', $video->duration ?? '') }}"
                           class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-yellow-400 focus:outline-none"
                           required>
                </div>

                {{-- Thumbnail File Upload --}}
                <div class="md:col-span-2">
                    <label for="thumbnail" class="block font-semibold text-gray-700 mb-1">Thumbnail</label>
                    <input type="file" name="thumbnail" id="thumbnail"
                           class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-yellow-400 focus:outline-none"
                           accept="image/*" onchange="previewThumbnail(event)">
                    <div class="mt-3">
                        <img id="thumbnailPreview"
                             src="{{ isset($video->thumbnail) ? asset('storage/' . $video->thumbnail) : '' }}"
                             class="rounded-lg border h-64 object-cover w-full"
                             onerror="this.style.display='none'">
                    </div>
                </div>

            </div>

            {{-- Buttons --}}
            <div class="flex justify-end space-x-4 pt-6">
                <button type="submit"
                        class="px-6 py-2 rounded-lg text-white font-semibold bg-yellow-500 hover:bg-yellow-600 transition duration-200">
                    {{ isset($video) ? 'Update Video' : 'Simpan Video' }}
                </button>
                <a href="{{ route('videos.index') }}"
                   class="px-6 py-2 rounded-lg text-white font-semibold bg-gray-400 hover:bg-gray-500 transition duration-200">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>

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

