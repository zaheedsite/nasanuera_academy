<div class="min-h-screen flex items-center justify-center bg-gray-100 py-12 px-4 sm:px-6 lg:px-8">
    <div class="bg-white shadow-2xl rounded-3xl w-full max-w-4xl p-10">
        <h2 class="text-3xl font-bold text-center text-gray-800 mb-8">
            {{ isset($video) ? 'Edit Video' : 'Tambah Video' }}
        </h2>

        <form id="videoForm" method="POST" enctype="multipart/form-data"
            action="{{ isset($video) ? route('videos.update', $video->id) : route('videos.store') }}" class="space-y-6">
            @csrf
            @if (isset($video))
                @method('PUT')
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                {{-- Subject --}}
                <div>
                    <label for="subject_id" class="block font-semibold text-gray-700 mb-1">Subject</label>
                    <select name="subject_id" id="subject_id"
                        class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-yellow-400 focus:outline-none"
                        required>
                        @foreach ($subjects as $subject)
                            <option value="{{ $subject->id }}"
                                {{ (old('subject_id') ?? ($video->subject_id ?? '')) == $subject->id ? 'selected' : '' }}>
                                {{ $subject->title }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Title --}}
                <div>
                    <label for="title" class="block font-semibold text-gray-700 mb-1">Judul Video</label>
                    <input type="text" name="title" id="title" value="{{ old('title', $video->title ?? '') }}"
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

                {{-- Video File Upload --}}
                <div class="md:col-span-2">
                    <label for="video_file" class="block font-semibold text-gray-700 mb-1">Video File</label>
                    <input type="file" name="video_file" id="video_file"
                        class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-yellow-400 focus:outline-none"
                        accept="video/*" {{ isset($video) ? '' : 'required' }}>

                    {{-- Progress bar video --}}
                    <div class="mt-2 hidden" id="videoProgressWrapper">
                        <div class="w-full bg-gray-200 rounded-full h-2.5">
                            <div id="videoProgressBar" class="bg-yellow-500 h-2.5 rounded-full" style="width: 0%"></div>
                        </div>
                        <p id="videoProgressText" class="text-xs text-gray-500 mt-1">0%</p>
                    </div>

                    @if (isset($video->video_url))
                        <div class="mt-3">
                            <video src="{{ $video->video_url }}" controls class="w-full h-64 rounded-lg border"></video>
                        </div>
                    @endif
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
                        accept="image/*" onchange="previewThumbnail(event)" {{ isset($video) ? '' : 'required' }}>

                    {{-- Progress bar thumbnail --}}
                    <div class="mt-2 hidden" id="thumbProgressWrapper">
                        <div class="w-full bg-gray-200 rounded-full h-2.5">
                            <div id="thumbProgressBar" class="bg-yellow-500 h-2.5 rounded-full" style="width: 0%"></div>
                        </div>
                        <p id="thumbProgressText" class="text-xs text-gray-500 mt-1">0%</p>
                    </div>

                    <div class="mt-3">
                        <img id="thumbnailPreview" src="{{ $video->thumbnail ?? '' }}"
                            class="rounded-lg border h-64 object-cover w-full {{ isset($video->thumbnail) ? '' : 'hidden' }}">
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
        const preview = document.getElementById('thumbnailPreview');
        preview.src = URL.createObjectURL(event.target.files[0]);
        preview.classList.remove('hidden');
    }

    // --- AJAX submit with progress ---
    document.getElementById('videoForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const xhr = new XMLHttpRequest();
        xhr.open('POST', this.action, true);

        // progress event
        xhr.upload.addEventListener('progress', function(e) {
            if (e.lengthComputable) {
                const percent = Math.round((e.loaded / e.total) * 100);

                // tampilkan bar untuk video & thumbnail
                document.getElementById('videoProgressWrapper').classList.remove('hidden');
                document.getElementById('thumbProgressWrapper').classList.remove('hidden');

                document.getElementById('videoProgressBar').style.width = percent + '%';
                document.getElementById('videoProgressText').innerText = percent + '%';

                document.getElementById('thumbProgressBar').style.width = percent + '%';
                document.getElementById('thumbProgressText').innerText = percent + '%';
            }
        });

        xhr.onload = function() {
            if (xhr.status === 200 || xhr.status === 201) {
                window.location.href = "{{ route('videos.index') }}";
            } else {
                alert('Upload gagal!');
            }
        };

        xhr.send(formData);
    });
</script>
