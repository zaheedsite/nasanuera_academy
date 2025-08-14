<?php

namespace App\Http\Controllers\Blade;

use App\Http\Controllers\Controller;
use App\Models\Video;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class VideoCrudController extends Controller
{
    public function index()
    {
        $videos = Video::with('subject')->latest()->get();
        return view('videos.index', compact('videos'));
    }

    public function create()
    {
        $subjects = Subject::all();
        return view('videos.create', compact('subjects'));
    }

    public function store(Request $request)
    {
        try {
            // Validasi input dengan pesan error yang lebih spesifik
            $validated = $request->validate([
                'subject_id' => 'required|exists:subjects,id',
                'title' => 'required|string|max:255',
                'description' => 'required|string',
                'video_file' => 'nullable|file|mimes:mp4,mov,avi,wmv|max:51200', // max 50MB
                'video_url' => 'nullable|url', // untuk upload via JavaScript
                'thumbnail' => 'required|image|mimes:jpg,jpeg,png|max:2048',
                'duration' => 'required|string|max:50',
            ], [
                'subject_id.required' => 'Subject harus dipilih',
                'subject_id.exists' => 'Subject yang dipilih tidak valid',
                'title.required' => 'Judul video harus diisi',
                'title.max' => 'Judul video maksimal 255 karakter',
                'description.required' => 'Deskripsi video harus diisi',
                'video_file.mimes' => 'Format video harus: mp4, mov, avi, wmv',
                'video_file.max' => 'Ukuran video maksimal 50MB',
                'video_url.url' => 'URL video tidak valid',
                'thumbnail.required' => 'Thumbnail harus diupload',
                'thumbnail.image' => 'Thumbnail harus berupa gambar',
                'thumbnail.mimes' => 'Format thumbnail harus: jpg, jpeg, png',
                'thumbnail.max' => 'Ukuran thumbnail maksimal 2MB',
                'duration.required' => 'Durasi video harus diisi',
                'duration.max' => 'Durasi video maksimal 50 karakter',
            ]);

            // Cek apakah ada video_file atau video_url
            if (!$request->hasFile('video_file') && empty($validated['video_url'])) {
                return back()->withErrors(['video_file' => 'Video file atau video URL harus diisi'])
                    ->withInput();
            }

            // Handle video upload
            if ($request->hasFile('video_file')) {
                // Upload video ke S3 (default disk)
                $videoFile = $request->file('video_file');

                // Generate unique filename
                $videoFileName = time() . '_' . $videoFile->getClientOriginalName();
                $videoPath = $videoFile->storeAs('videos', $videoFileName, [
                    'visibility' => 'public',
                    'ACL' => 'public-read'
                ]);

                if (!$videoPath) {
                    throw new \Exception('Gagal mengupload video ke S3');
                }

                $validated['video_url'] = Storage::url($videoPath);
            }

            // Handle thumbnail upload
            if ($request->hasFile('thumbnail')) {
                $thumbnailFile = $request->file('thumbnail');

                // Generate unique filename
                $thumbnailFileName = time() . '_' . $thumbnailFile->getClientOriginalName();
                $thumbnailPath = $thumbnailFile->storeAs('thumbnails', $thumbnailFileName, [
                    'disk' => 'public',
                    'visibility' => 'public'
                ]);

                if (!$thumbnailPath) {
                    throw new \Exception('Gagal mengupload thumbnail');
                }

                $validated['thumbnail'] = $thumbnailPath;
            }

            // Remove video_file dari validated data karena tidak ada di database
            unset($validated['video_file']);

            // Create video record
            $video = Video::create($validated);

            if (!$video) {
                throw new \Exception('Gagal menyimpan data video ke database');
            }

            return redirect()->route('videos.index')
                ->with('success', 'Video berhasil diupload!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Validation errors akan otomatis di-handle oleh Laravel
            throw $e;
        } catch (\Exception $e) {
            // Log error untuk debugging
            Log::error('Error uploading video: ' . $e->getMessage(), [
                'request_data' => $request->except(['video_file', 'thumbnail']),
                'user_id' => Auth::id(),
                'trace' => $e->getTraceAsString()
            ]);

            // Cleanup uploaded files jika ada error
            if (isset($videoPath) && Storage::exists($videoPath)) {
                Storage::delete($videoPath);
            }
            if (isset($thumbnailPath) && Storage::disk('public')->exists($thumbnailPath)) {
                Storage::disk('public')->delete($thumbnailPath);
            }

            return back()->withErrors(['error' => 'Terjadi kesalahan saat mengupload video: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function show(Video $video)
    {
        return view('videos.show', compact('video'));
    }

    public function edit(Video $video)
    {
        $subjects = Subject::all();
        return view('videos.edit', compact('video', 'subjects'));
    }

    public function update(Request $request, Video $video)
    {
        try {
            $validated = $request->validate([
                'subject_id' => 'required|exists:subjects,id',
                'title' => 'required|string|max:255',
                'description' => 'required|string',
                'video_file' => 'nullable|file|mimes:mp4,mov,avi,wmv|max:51200',
                'video_url' => 'nullable|url', // untuk update via JavaScript
                'thumbnail' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
                'duration' => 'required|string|max:50',
            ], [
                'subject_id.required' => 'Subject harus dipilih',
                'subject_id.exists' => 'Subject yang dipilih tidak valid',
                'title.required' => 'Judul video harus diisi',
                'title.max' => 'Judul video maksimal 255 karakter',
                'description.required' => 'Deskripsi video harus diisi',
                'video_file.mimes' => 'Format video harus: mp4, mov, avi, wmv',
                'video_file.max' => 'Ukuran video maksimal 50MB',
                'video_url.url' => 'URL video tidak valid',
                'thumbnail.image' => 'Thumbnail harus berupa gambar',
                'thumbnail.mimes' => 'Format thumbnail harus: jpg, jpeg, png',
                'thumbnail.max' => 'Ukuran thumbnail maksimal 2MB',
                'duration.required' => 'Durasi video harus diisi',
                'duration.max' => 'Durasi video maksimal 50 karakter',
            ]);

            $oldVideoPath = null;
            $oldThumbnailPath = null;

            // Handle video update
            if ($request->hasFile('video_file')) {
                // Backup old video path for cleanup
                if ($video->video_url) {
                    $oldVideoPath = str_replace(Storage::url(''), '', $video->video_url);
                }

                $videoFile = $request->file('video_file');
                $videoFileName = time() . '_' . $videoFile->getClientOriginalName();
                $videoPath = $videoFile->storeAs('videos', $videoFileName, [
                    'visibility' => 'public',
                    'ACL' => 'public-read'
                ]);

                if (!$videoPath) {
                    throw new \Exception('Gagal mengupload video baru ke S3');
                }

                $validated['video_url'] = Storage::url($videoPath);
            } elseif (!empty($validated['video_url'])) {
                // Video URL updated via JavaScript, keep the new URL
                // Backup old video path for cleanup if different
                if ($video->video_url && $video->video_url !== $validated['video_url']) {
                    $oldVideoPath = str_replace(Storage::url(''), '', $video->video_url);
                }
            } else {
                // No video update, remove from validated data
                unset($validated['video_file'], $validated['video_url']);
            }

            // Handle thumbnail update
            if ($request->hasFile('thumbnail')) {
                // Backup old thumbnail path for cleanup
                $oldThumbnailPath = $video->thumbnail;

                $thumbnailFile = $request->file('thumbnail');
                $thumbnailFileName = time() . '_' . $thumbnailFile->getClientOriginalName();
                $thumbnailPath = $thumbnailFile->storeAs('thumbnails', $thumbnailFileName, [
                    'disk' => 'public',
                    'visibility' => 'public'
                ]);

                if (!$thumbnailPath) {
                    throw new \Exception('Gagal mengupload thumbnail baru');
                }

                $validated['thumbnail'] = $thumbnailPath;
            } else {
                // No thumbnail update, remove from validated data
                unset($validated['thumbnail']);
            }

            // Remove video_file dari validated data
            unset($validated['video_file']);

            // Update video record
            $updated = $video->update($validated);

            if (!$updated) {
                throw new \Exception('Gagal memperbarui data video');
            }

            // Cleanup old files after successful update
            if ($oldVideoPath && Storage::exists($oldVideoPath)) {
                Storage::delete($oldVideoPath);
            }
            if ($oldThumbnailPath && Storage::disk('public')->exists($oldThumbnailPath)) {
                Storage::disk('public')->delete($oldThumbnailPath);
            }

            return redirect()->route('videos.index')
                ->with('success', 'Video berhasil diperbarui!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            Log::error('Error updating video: ' . $e->getMessage(), [
                'video_id' => $video->id,
                'request_data' => $request->except(['video_file', 'thumbnail']),
                'user_id' => Auth::id(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()->withErrors(['error' => 'Terjadi kesalahan saat memperbarui video: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function destroy(Video $video)
    {
        try {
            $videoTitle = $video->title; // Store for success message
            $thumbnailPath = $video->thumbnail;
            $videoPath = null;

            if ($video->video_url) {
                $videoPath = str_replace(Storage::url(''), '', $video->video_url);
            }

            // Delete video record first
            $deleted = $video->delete();

            if (!$deleted) {
                throw new \Exception('Gagal menghapus data video dari database');
            }

            // Cleanup files after successful database deletion
            if ($thumbnailPath && Storage::disk('public')->exists($thumbnailPath)) {
                Storage::disk('public')->delete($thumbnailPath);
            }

            if ($videoPath && Storage::exists($videoPath)) {
                Storage::delete($videoPath);
            }

            return back()->with('success', "Video '{$videoTitle}' berhasil dihapus!");
        } catch (\Exception $e) {
            Log::error('Error deleting video: ' . $e->getMessage(), [
                'video_id' => $video->id,
                'video_title' => $video->title,
                'user_id' => Auth::id(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()->withErrors(['error' => 'Terjadi kesalahan saat menghapus video: ' . $e->getMessage()]);
        }
    }

    /**
     * Generate signed URL for direct S3 upload
     */
    public function getSignedUrl(Request $request)
    {
        try {
            Log::info('Signed URL request received', [
                'request_data' => $request->only(['filename', 'type']),
                'user_id' => Auth::id(),
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);

            // Validate request
            $validated = $request->validate([
                'filename' => 'required|string|max:255',
                'type' => 'required|string|in:video/mp4,video/mov,video/avi,video/wmv'
            ], [
                'filename.required' => 'Filename harus diisi',
                'filename.max' => 'Filename terlalu panjang',
                'type.required' => 'Type file harus diisi',
                'type.in' => 'Format file tidak didukung'
            ]);

            $filename = $validated['filename'];
            $type = $validated['type'];

            // Generate unique filename
            $uniqueFilename = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $filename);
            $path = 'videos/' . $uniqueFilename;

            Log::info('Generating signed URL', [
                'original_filename' => $filename,
                'unique_filename' => $uniqueFilename,
                'path' => $path,
                'type' => $type,
                'user_id' => Auth::id()
            ]);

            // Check S3 configuration
            $s3Config = config('filesystems.disks.s3');
            Log::info('S3 Configuration check', [
                'driver' => $s3Config['driver'] ?? 'not_set',
                'bucket' => $s3Config['bucket'] ?? 'not_set',
                'region' => $s3Config['region'] ?? 'not_set',
                'key_exists' => !empty($s3Config['key']),
                'secret_exists' => !empty($s3Config['secret']),
                'default_disk' => config('filesystems.default')
            ]);

            // Generate signed URL for PUT request with public ACL
            $signedUrl = Storage::temporaryUrl($path, now()->addMinutes(30), [
                'ResponseContentType' => $type,
                'ResponseContentDisposition' => 'inline; filename="' . $filename . '"',
                'ACL' => 'public-read'
            ]);

            // Generate final file URL
            $fileUrl = Storage::url($path);

            // Verify that the signed URL is for the correct path
            Log::info('Generated signed URL details', [
                'path' => $path,
                'signed_url' => $signedUrl,
                'file_url' => $fileUrl,
                'bucket' => config('filesystems.disks.s3.bucket'),
                'endpoint' => config('filesystems.disks.s3.endpoint')
            ]);

            Log::info('Signed URL generated successfully', [
                'path' => $path,
                'file_url' => $fileUrl,
                'signed_url_length' => strlen($signedUrl),
                'expires_at' => now()->addMinutes(30)->toISOString(),
                'user_id' => Auth::id()
            ]);

            return response()->json([
                'url' => $signedUrl,
                'file_url' => $fileUrl,
                'path' => $path,
                'expires_at' => now()->addMinutes(30)->toISOString()
            ]);
        } catch (\Exception $e) {
            Log::error('Error generating signed URL', [
                'error_message' => $e->getMessage(),
                'error_code' => $e->getCode(),
                'error_file' => $e->getFile(),
                'error_line' => $e->getLine(),
                'stack_trace' => $e->getTraceAsString(),
                'request_data' => [
                    'filename' => $request->input('filename'),
                    'type' => $request->input('type'),
                    'user_id' => Auth::id(),
                    'ip' => $request->ip()
                ],
                's3_config' => [
                    'default_disk' => config('filesystems.default'),
                    'cloud_disk' => config('filesystems.cloud'),
                    'bucket' => config('filesystems.disks.s3.bucket'),
                    'region' => config('filesystems.disks.s3.region'),
                    'key_exists' => !empty(config('filesystems.disks.s3.key')),
                    'secret_exists' => !empty(config('filesystems.disks.s3.secret'))
                ]
            ]);

            return response()->json([
                'error' => 'Gagal membuat signed URL: ' . $e->getMessage(),
                'debug_info' => app()->environment('local') ? [
                    'error_class' => get_class($e),
                    'error_file' => $e->getFile(),
                    'error_line' => $e->getLine()
                ] : null
            ], 500);
        }
    }

    /**
     * Debug method untuk mengecek konfigurasi S3
     */
    public function debugS3Config()
    {
        if (!app()->environment('local')) {
            abort(404);
        }

        $s3Config = config('filesystems.disks.s3');
        $defaultDisk = config('filesystems.default');

        $debugInfo = [
            'environment' => app()->environment(),
            'default_disk' => $defaultDisk,
            'cloud_disk' => config('filesystems.cloud'),
            's3_config' => [
                'driver' => $s3Config['driver'] ?? 'not_set',
                'key' => !empty($s3Config['key']) ? 'SET (length: ' . strlen($s3Config['key']) . ')' : 'NOT_SET',
                'secret' => !empty($s3Config['secret']) ? 'SET (length: ' . strlen($s3Config['secret']) . ')' : 'NOT_SET',
                'region' => $s3Config['region'] ?? 'not_set',
                'bucket' => $s3Config['bucket'] ?? 'not_set',
                'url' => $s3Config['url'] ?? 'not_set',
                'endpoint' => $s3Config['endpoint'] ?? 'not_set',
                'use_path_style_endpoint' => $s3Config['use_path_style_endpoint'] ?? 'not_set',
                'throw' => $s3Config['throw'] ?? 'not_set',
                'report' => $s3Config['report'] ?? 'not_set',
            ],
            'env_vars' => [
                'AWS_ACCESS_KEY_ID' => !empty(env('AWS_ACCESS_KEY_ID')) ? 'SET' : 'NOT_SET',
                'AWS_SECRET_ACCESS_KEY' => !empty(env('AWS_SECRET_ACCESS_KEY')) ? 'SET' : 'NOT_SET',
                'AWS_DEFAULT_REGION' => env('AWS_DEFAULT_REGION', 'not_set'),
                'AWS_BUCKET' => env('AWS_BUCKET', 'not_set'),
                'AWS_URL' => env('AWS_URL', 'not_set'),
                'AWS_ENDPOINT' => env('AWS_ENDPOINT', 'not_set'),
                'FILESYSTEM_DRIVER' => env('FILESYSTEM_DRIVER', 'not_set'),
                'FILESYSTEM_CLOUD' => env('FILESYSTEM_CLOUD', 'not_set'),
            ]
        ];

        // Test Storage connection
        try {
            $testPath = 'test/' . time() . '.txt';
            Storage::put($testPath, 'test content');
            $debugInfo['storage_test'] = [
                'status' => 'SUCCESS',
                'test_file_created' => $testPath,
                'can_write' => true
            ];

            // Clean up test file
            Storage::delete($testPath);
            $debugInfo['storage_test']['test_file_deleted'] = true;
        } catch (\Exception $e) {
            $debugInfo['storage_test'] = [
                'status' => 'FAILED',
                'error' => $e->getMessage(),
                'error_class' => get_class($e),
                'can_write' => false
            ];
        }

        return response()->json($debugInfo, 200, [], JSON_PRETTY_PRINT);
    }

    /**
     * Verify if file exists after upload
     */
    public function verifyUpload(Request $request)
    {
        $validated = $request->validate([
            'path' => 'required|string'
        ]);

        $path = $validated['path'];

        try {
            $exists = Storage::exists($path);
            $fileUrl = Storage::url($path);

            Log::info('File verification request', [
                'path' => $path,
                'exists' => $exists,
                'file_url' => $fileUrl
            ]);

            if ($exists) {
                // Try to make file public immediately
                try {
                    Storage::setVisibility($path, 'public');
                    Log::info("Made uploaded file public: {$path}");
                } catch (\Exception $e) {
                    Log::warning("Could not set file visibility: " . $e->getMessage());
                }

                return response()->json([
                    'exists' => true,
                    'path' => $path,
                    'file_url' => $fileUrl,
                    'message' => 'File uploaded successfully and made public'
                ]);
            } else {
                // List files in videos directory for debugging
                $videoFiles = Storage::files('videos');
                $allFiles = Storage::files('');
                $rootFiles = array_slice($allFiles, 0, 10); // First 10 files only

                // Try different path variations
                $pathVariations = [
                    $path,
                    ltrim($path, '/'),
                    basename($path),
                    str_replace('videos/', '', $path)
                ];

                $foundVariations = [];
                foreach ($pathVariations as $variation) {
                    if (Storage::exists($variation)) {
                        $foundVariations[] = $variation;
                    }
                }

                Log::warning('File not found after upload', [
                    'requested_path' => $path,
                    'path_variations_tried' => $pathVariations,
                    'found_variations' => $foundVariations,
                    'videos_directory_files' => $videoFiles,
                    'root_files_sample' => $rootFiles,
                    'total_files_count' => count($allFiles),
                    'storage_config' => [
                        'default_disk' => config('filesystems.default'),
                        'bucket' => config('filesystems.disks.s3.bucket'),
                        'endpoint' => config('filesystems.disks.s3.endpoint')
                    ]
                ]);

                return response()->json([
                    'exists' => false,
                    'path' => $path,
                    'message' => 'File tidak ditemukan setelah upload. Kemungkinan masalah dengan path atau konfigurasi S3.',
                    'debug' => [
                        'requested_path' => $path,
                        'path_variations_tried' => $pathVariations,
                        'found_variations' => $foundVariations,
                        'videos_files' => $videoFiles,
                        'root_files_sample' => $rootFiles,
                        'total_files_count' => count($allFiles),
                        'suggestions' => [
                            'Cek apakah file benar-benar terupload ke S3',
                            'Verifikasi konfigurasi bucket dan endpoint',
                            'Pastikan path signed URL sesuai dengan storage path',
                            'Cek permission S3 untuk list dan read files'
                        ]
                    ]
                ], 404);
            }
        } catch (\Exception $e) {
            Log::error('Error verifying upload', [
                'path' => $path,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'exists' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Make specific file public by URL
     */
    public function makeFilePublic(Request $request)
    {
        if (!app()->environment('local')) {
            abort(404);
        }

        $validated = $request->validate([
            'file_url' => 'required|url'
        ]);

        try {
            $fileUrl = $validated['file_url'];

            // Extract path from DigitalOcean Spaces URL
            if (strpos($fileUrl, 'digitaloceanspaces.com') !== false) {
                $urlParts = parse_url($fileUrl);
                $path = ltrim($urlParts['path'], '/');

                Log::info("Attempting to make file public", [
                    'file_url' => $fileUrl,
                    'extracted_path' => $path
                ]);

                if (Storage::exists($path)) {
                    // Try to set ACL using putObject with public-read
                    $fileContent = Storage::get($path);
                    Storage::put($path, $fileContent, [
                        'visibility' => 'public',
                        'ACL' => 'public-read'
                    ]);

                    Log::info("Successfully made file public: {$path}");

                    return response()->json([
                        'success' => true,
                        'message' => "File made public: {$path}",
                        'file_url' => $fileUrl
                    ]);
                } else {
                    return response()->json([
                        'success' => false,
                        'error' => "File not found in storage: {$path}",
                        'file_url' => $fileUrl
                    ], 404);
                }
            } else {
                return response()->json([
                    'success' => false,
                    'error' => 'URL is not a DigitalOcean Spaces URL',
                    'file_url' => $fileUrl
                ], 400);
            }
        } catch (\Exception $e) {
            Log::error('Error making file public', [
                'file_url' => $request->input('file_url'),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Make existing video files public
     */
    public function makeVideosPublic()
    {
        if (!app()->environment('local')) {
            abort(404);
        }

        try {
            $videos = Video::whereNotNull('video_url')->get();
            $updated = 0;
            $errors = [];

            foreach ($videos as $video) {
                try {
                    // Extract path from URL
                    $path = str_replace(Storage::url(''), '', $video->video_url);

                    if (Storage::exists($path)) {
                        // Set file visibility to public
                        try {
                            // Try to set ACL using putObject with public-read
                            $fileContent = Storage::get($path);
                            Storage::put($path, $fileContent, [
                                'visibility' => 'public',
                                'ACL' => 'public-read'
                            ]);
                            $updated++;
                            Log::info("Made video public: {$path}");
                        } catch (\Exception $e) {
                            // Fallback to Laravel method
                            Storage::setVisibility($path, 'public');
                            $updated++;
                            Log::info("Made video public (fallback): {$path}", [
                                'error' => $e->getMessage()
                            ]);
                        }
                    } else {
                        $errors[] = "File not found: {$path}";
                    }
                } catch (\Exception $e) {
                    $errors[] = "Error with {$video->title}: " . $e->getMessage();
                    Log::error("Error making video public", [
                        'video_id' => $video->id,
                        'video_url' => $video->video_url,
                        'error' => $e->getMessage()
                    ]);
                }
            }

            return response()->json([
                'success' => true,
                'message' => "Updated {$updated} videos to public",
                'total_videos' => $videos->count(),
                'updated_count' => $updated,
                'errors' => $errors
            ], 200, [], JSON_PRETTY_PRINT);
        } catch (\Exception $e) {
            Log::error('Error in makeVideosPublic: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500, [], JSON_PRETTY_PRINT);
        }
    }
}
