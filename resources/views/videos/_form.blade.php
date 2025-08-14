<div class="min-h-screen flex items-center justify-center bg-gray-100 py-12 px-4 sm:px-6 lg:px-8">
    <div class="bg-white shadow-2xl rounded-3xl w-full max-w-4xl p-10">
        <h2 class="text-3xl font-bold text-center text-gray-800 mb-8">
            {{ isset($video) ? 'Edit Video' : 'Tambah Video' }}
        </h2>

        {{-- Display Errors --}}
        @if ($errors->any())
            <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800">Terjadi kesalahan:</h3>
                        <div class="mt-2 text-sm text-red-700">
                            <ul class="list-disc pl-5 space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <form method="POST" enctype="multipart/form-data"
            action="{{ isset($video) ? route('videos.update', $video->id) : route('videos.store') }}" class="space-y-6"
            id="videoForm">
            @csrf
            @if (isset($video))
                @method('PUT')
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                {{-- Subject --}}
                <div>
                    <label for="subject_id" class="block font-semibold text-gray-700 mb-1">
                        Subject <span class="text-red-500">*</span>
                    </label>
                    <select name="subject_id" id="subject_id"
                        class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-yellow-400 focus:outline-none @error('subject_id') border-red-500 @enderror"
                        required>
                        <option value="">Pilih Subject</option>
                        @foreach ($subjects as $subject)
                            <option value="{{ $subject->id }}"
                                {{ (old('subject_id') ?? ($video->subject_id ?? '')) == $subject->id ? 'selected' : '' }}>
                                {{ $subject->title }}
                            </option>
                        @endforeach
                    </select>
                    @error('subject_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Title --}}
                <div>
                    <label for="title" class="block font-semibold text-gray-700 mb-1">
                        Judul Video <span class="text-red-500">*</span>
                        <span class="text-sm text-gray-500">(Maksimal 255 karakter)</span>
                    </label>
                    <input type="text" name="title" id="title" value="{{ old('title', $video->title ?? '') }}"
                        maxlength="255"
                        class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-yellow-400 focus:outline-none @error('title') border-red-500 @enderror"
                        required>
                    @error('title')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-sm text-gray-500">
                        <span id="titleCount">0</span>/255 karakter
                    </p>
                </div>

                {{-- Description --}}
                <div class="md:col-span-2">
                    <label for="description" class="block font-semibold text-gray-700 mb-1">
                        Deskripsi <span class="text-red-500">*</span>
                    </label>
                    <textarea name="description" id="description" rows="4"
                        class="w-full px-4 py-2 rounded-lg border border-gray-300 resize-y focus:ring-2 focus:ring-yellow-400 focus:outline-none @error('description') border-red-500 @enderror"
                        required>{{ old('description', $video->description ?? '') }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Video File Upload ke S3 --}}
                <div class="md:col-span-2">
                    <label for="video_file" class="block font-semibold text-gray-700 mb-1">
                        Video File
                        @if (!isset($video))
                            <span class="text-red-500">*</span>
                        @endif
                        <span class="text-sm text-gray-500">(Format: mp4, mov, avi, wmv | Maksimal 50MB)</span>
                    </label>
                    <input type="file" id="video_file" name="video_file"
                        class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-yellow-400 focus:outline-none @error('video_file') border-red-500 @enderror"
                        accept="video/mp4,video/mov,video/avi,video/wmv" onchange="uploadVideoToS3(event)"
                        {{ isset($video) ? '' : 'required' }}>
                    <input type="hidden" name="video_url" id="video_url"
                        value="{{ old('video_url', $video->video_url ?? '') }}">

                    @error('video_file')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror

                    <div class="mt-2 text-sm text-gray-600">
                        <p>💡 <strong>Cara Upload:</strong></p>
                        <ul class="list-disc list-inside ml-4 space-y-1">
                            <li>Pilih file video dari komputer Anda</li>
                            <li>Video akan otomatis diupload ke S3</li>
                            <li>Tunggu hingga muncul preview video</li>
                        </ul>
                    </div>

                    {{-- Upload Progress --}}
                    <div id="uploadProgress" class="mt-3 hidden">
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                            <div class="flex items-center">
                                <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-blue-600 mr-2"></div>
                                <span class="text-sm text-blue-700">Mengupload video ke S3...</span>
                            </div>
                        </div>
                    </div>

                    {{-- Video Preview --}}
                    <div class="mt-3">
                        @if (isset($video) && $video->video_url)
                            <video id="videoPreview" controls class="w-full rounded-lg border" style="max-height:400px">
                                <source src="{{ $video->video_url }}" type="video/mp4">
                                Browser Anda tidak mendukung video HTML5.
                            </video>
                        @else
                            <video id="videoPreview" controls class="w-full rounded-lg border"
                                style="display:none; max-height:400px"></video>
                        @endif
                    </div>
                </div>

                {{-- Duration --}}
                <div>
                    <label for="duration" class="block font-semibold text-gray-700 mb-1">
                        Durasi <span class="text-red-500">*</span>
                        <span class="text-sm text-gray-500">(Contoh: 5 Menit 30 Detik | Maksimal 50 karakter)</span>
                    </label>
                    <input type="text" name="duration" id="duration"
                        value="{{ old('duration', $video->duration ?? '') }}" maxlength="50"
                        placeholder="Contoh: 5 Menit 30 Detik"
                        class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-yellow-400 focus:outline-none @error('duration') border-red-500 @enderror"
                        required>
                    @error('duration')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-sm text-gray-500">
                        <span id="durationCount">0</span>/50 karakter
                    </p>
                </div>

                {{-- Thumbnail --}}
                <div class="md:col-span-2">
                    <label for="thumbnail" class="block font-semibold text-gray-700 mb-1">
                        Thumbnail
                        @if (!isset($video))
                            <span class="text-red-500">*</span>
                        @endif
                        <span class="text-sm text-gray-500">(Format: jpg, jpeg, png | Maksimal 2MB)</span>
                    </label>
                    <input type="file" name="thumbnail" id="thumbnail"
                        class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-yellow-400 focus:outline-none @error('thumbnail') border-red-500 @enderror"
                        accept="image/jpeg,image/jpg,image/png" onchange="previewThumbnail(event)"
                        {{ isset($video) ? '' : 'required' }}>
                    @error('thumbnail')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror

                    <div class="mt-3">
                        @if (isset($video) && $video->thumbnail)
                            <img id="thumbnailPreview" src="{{ asset('storage/' . $video->thumbnail) }}"
                                class="rounded-lg border h-64 object-cover w-full" alt="Thumbnail Preview">
                        @else
                            <img id="thumbnailPreview" src=""
                                class="rounded-lg border h-64 object-cover w-full" style="display:none;"
                                alt="Thumbnail Preview">
                        @endif
                        <div id="thumbnailPlaceholder"
                            class="rounded-lg border h-64 bg-gray-100 flex items-center justify-center {{ isset($video) && $video->thumbnail ? 'hidden' : '' }}">
                            <div class="text-center text-gray-500">
                                <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none"
                                    viewBox="0 0 48 48">
                                    <path
                                        d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02"
                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <p class="mt-2 text-sm">Pilih gambar thumbnail</p>
                            </div>
                        </div>
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
    // Character counters
    document.addEventListener('DOMContentLoaded', function() {
        // Title character counter
        const titleInput = document.getElementById('title');
        const titleCount = document.getElementById('titleCount');

        function updateTitleCount() {
            const count = titleInput.value.length;
            titleCount.textContent = count;
            titleCount.className = count > 255 ? 'text-red-500 font-semibold' : 'text-gray-500';
        }

        titleInput.addEventListener('input', updateTitleCount);
        updateTitleCount(); // Initial count

        // Duration character counter
        const durationInput = document.getElementById('duration');
        const durationCount = document.getElementById('durationCount');

        function updateDurationCount() {
            const count = durationInput.value.length;
            durationCount.textContent = count;
            durationCount.className = count > 50 ? 'text-red-500 font-semibold' : 'text-gray-500';
        }

        durationInput.addEventListener('input', updateDurationCount);
        updateDurationCount(); // Initial count
    });

    function previewThumbnail(event) {
        const file = event.target.files[0];
        if (!file) return;

        // Validate file size (2MB = 2 * 1024 * 1024 bytes)
        if (file.size > 2 * 1024 * 1024) {
            alert('❌ Ukuran file thumbnail terlalu besar! Maksimal 2MB.');
            event.target.value = '';
            return;
        }

        // Validate file type
        const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
        if (!allowedTypes.includes(file.type)) {
            alert('❌ Format file tidak didukung! Gunakan JPG, JPEG, atau PNG.');
            event.target.value = '';
            return;
        }

        const reader = new FileReader();
        reader.onload = function() {
            const preview = document.getElementById('thumbnailPreview');
            const placeholder = document.getElementById('thumbnailPlaceholder');

            preview.src = reader.result;
            preview.style.display = 'block';
            placeholder.classList.add('hidden');
        };
        reader.readAsDataURL(file);
    }

    async function uploadVideoToS3(event) {
        const file = event.target.files[0];
        if (!file) return;

        // Validate file size (50MB = 50 * 1024 * 1024 bytes)
        if (file.size > 50 * 1024 * 1024) {
            alert('❌ Ukuran file video terlalu besar! Maksimal 50MB.');
            event.target.value = '';
            return;
        }

        // Validate file type
        const allowedTypes = ['video/mp4', 'video/mov', 'video/avi', 'video/wmv'];
        if (!allowedTypes.includes(file.type)) {
            alert('❌ Format video tidak didukung! Gunakan MP4, MOV, AVI, atau WMV.');
            event.target.value = '';
            return;
        }

        const progressDiv = document.getElementById('uploadProgress');
        const videoPreview = document.getElementById('videoPreview');

        try {
            // Show progress
            progressDiv.classList.remove('hidden');

            // Minta signed URL dari backend
            const formData = new FormData();
            formData.append('filename', file.name);
            formData.append('type', file.type);
            formData.append('_token', document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ||
                document.querySelector('input[name="_token"]')?.value);

            const res = await fetch("{{ route('videos.signed-url') }}", {
                method: 'POST',
                body: formData
            });

            console.log('Signed URL response status:', res.status);
            console.log('Signed URL response headers:', Object.fromEntries(res.headers.entries()));

            if (!res.ok) {
                const errorText = await res.text();
                console.error('Signed URL error response:', errorText);
                throw new Error(`Gagal mendapatkan signed URL dari server (${res.status}): ${errorText}`);
            }

            const data = await res.json();
            console.log('Signed URL response data:', data);

            if (data.error) {
                console.error('Signed URL error in response:', data);
                throw new Error(data.error + (data.debug_info ? ` (Debug: ${JSON.stringify(data.debug_info)})` :
                    ''));
            }

            // Upload langsung ke S3
            console.log('Starting S3 upload to:', data.url);
            console.log('File info:', {
                name: file.name,
                size: file.size,
                type: file.type
            });

            const uploadRes = await fetch(data.url, {
                method: "PUT",
                headers: {
                    "Content-Type": file.type,
                    "x-amz-acl": "public-read"
                },
                body: file
            });

            console.log('S3 upload response status:', uploadRes.status);
            console.log('S3 upload response headers:', Object.fromEntries(uploadRes.headers.entries()));

            if (!uploadRes.ok) {
                const s3ErrorText = await uploadRes.text();
                console.error('S3 upload error response:', s3ErrorText);
                console.error('S3 upload failed with status:', uploadRes.status);
                console.error('S3 upload headers:', Object.fromEntries(uploadRes.headers.entries()));
                throw new Error(`Gagal mengupload video ke S3 (${uploadRes.status}): ${s3ErrorText}`);
            }

            console.log('✅ S3 upload successful!');

            // Verify upload berhasil dengan retry mechanism
            console.log('Verifying upload success...');

            let verifyAttempts = 0;
            const maxVerifyAttempts = 3;
            let verifyData = null;

            while (verifyAttempts < maxVerifyAttempts) {
                verifyAttempts++;
                console.log(`Verify attempt ${verifyAttempts}/${maxVerifyAttempts}...`);

                // Wait a bit before verification (S3 eventual consistency)
                if (verifyAttempts > 1) {
                    await new Promise(resolve => setTimeout(resolve, 2000)); // Wait 2 seconds
                }

                const verifyFormData = new FormData();
                verifyFormData.append('path', data.path);
                verifyFormData.append('_token', document.querySelector('meta[name="csrf-token"]')?.getAttribute(
                        'content') ||
                    document.querySelector('input[name="_token"]')?.value);

                const verifyRes = await fetch("{{ route('videos.verify-upload') }}", {
                    method: 'POST',
                    body: verifyFormData
                });

                if (!verifyRes.ok) {
                    const verifyErrorText = await verifyRes.text();
                    console.error(`Verify attempt ${verifyAttempts} failed:`, verifyErrorText);

                    if (verifyAttempts >= maxVerifyAttempts) {
                        throw new Error(
                            `Gagal memverifikasi upload setelah ${maxVerifyAttempts} percobaan (${verifyRes.status}): ${verifyErrorText}`
                            );
                    }
                    continue; // Try again
                }

                verifyData = await verifyRes.json();
                console.log(`Verify attempt ${verifyAttempts} response:`, verifyData);

                if (verifyData.exists) {
                    console.log('✅ File verified and made public!');
                    break; // Success, exit retry loop
                } else {
                    console.warn(`❌ Verify attempt ${verifyAttempts} - File not found:`, verifyData.message);

                    if (verifyAttempts >= maxVerifyAttempts) {
                        console.error('❌ File not found after all attempts:', verifyData);

                        // Show debug information
                        if (verifyData.debug) {
                            console.error('Debug info:', verifyData.debug);
                        }

                        throw new Error(
                            `File tidak ditemukan setelah ${maxVerifyAttempts} percobaan verifikasi.\n\n` +
                            `Pesan: ${verifyData.message}\n\n` +
                            `Kemungkinan penyebab:\n` +
                            `- File tidak benar-benar terupload ke S3\n` +
                            `- Masalah konfigurasi S3 atau path\n` +
                            `- Delay dalam sinkronisasi S3\n\n` +
                            `Debug info: ${JSON.stringify(verifyData.debug || {}, null, 2)}`
                        );
                    }
                    // Continue to next attempt
                }
            }

            // If we get here, verification was successful
            if (verifyData && verifyData.exists) {
                // Simpan URL final
                document.getElementById('video_url').value = verifyData.file_url;

                // Preview video
                videoPreview.src = verifyData.file_url;
                videoPreview.style.display = 'block';

                // Hide progress
                progressDiv.classList.add('hidden');
            } else {
                throw new Error('Unexpected error: verification completed but file status unknown');
            }

            // Show success message
            const successDiv = document.createElement('div');
            successDiv.className = 'mt-3 bg-green-50 border border-green-200 rounded-lg p-3';
            successDiv.innerHTML = `
                <div class="flex items-center">
                    <svg class="h-5 w-5 text-green-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="text-sm text-green-700">✅ Video berhasil diupload ke S3!</span>
                </div>
            `;

            // Remove existing success message if any
            const existingSuccess = document.querySelector('.bg-green-50');
            if (existingSuccess) {
                existingSuccess.remove();
            }

            progressDiv.parentNode.insertBefore(successDiv, progressDiv.nextSibling);

            // Auto remove success message after 5 seconds
            setTimeout(() => {
                successDiv.remove();
            }, 5000);

        } catch (error) {
            console.error('Upload error:', error);

            // Hide progress
            progressDiv.classList.add('hidden');

            // Show detailed error message
            const errorDiv = document.createElement('div');
            errorDiv.className = 'mt-3 bg-red-50 border border-red-200 rounded-lg p-3';
            errorDiv.innerHTML = `
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800">Upload Gagal</h3>
                        <div class="mt-2 text-sm text-red-700">
                            <p>${error.message}</p>
                        </div>
                        <div class="mt-3">
                            <button type="button" onclick="this.parentElement.parentElement.parentElement.parentElement.remove()"
                                class="bg-red-50 text-red-800 rounded-md p-1.5 hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-red-600 focus:ring-offset-2 focus:ring-offset-red-50">
                                <span class="sr-only">Tutup</span>
                                <svg class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            `;

            // Remove existing error messages
            const existingErrors = document.querySelectorAll('.bg-red-50');
            existingErrors.forEach(el => el.remove());

            // Add error message
            progressDiv.parentNode.insertBefore(errorDiv, progressDiv.nextSibling);

            // Also show alert for immediate attention
            alert('❌ Upload Gagal: ' + error.message);

            // Reset file input
            event.target.value = '';
            document.getElementById('video_url').value = '';
            videoPreview.style.display = 'none';
        }
    }

    // Form validation before submit
    document.getElementById('videoForm').addEventListener('submit', function(e) {
        const videoFile = document.getElementById('video_file').files[0];
        const videoUrl = document.getElementById('video_url').value;

        // Check if either video file or video URL exists
        if (!videoFile && !videoUrl) {
            e.preventDefault();
            alert('❌ Silakan pilih file video atau pastikan video sudah terupload ke S3!');
            return false;
        }

        // Show loading state
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.textContent;
        submitBtn.disabled = true;
        submitBtn.innerHTML = `
            <div class="flex items-center">
                <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-white mr-2"></div>
                Menyimpan...
            </div>
        `;

        // Reset button after 10 seconds (fallback)
        setTimeout(() => {
            submitBtn.disabled = false;
            submitBtn.textContent = originalText;
        }, 10000);
    });
</script>
