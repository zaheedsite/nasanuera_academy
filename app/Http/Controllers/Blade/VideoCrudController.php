<?php

namespace App\Http\Controllers\Blade;

use App\Http\Controllers\Controller;
use App\Models\Video;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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
        $validated = $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'title'      => 'required|string',
            'description' => 'required|string',
            'video_file' => 'required|file|mimetypes:video/mp4,video/avi,video/mpeg,video/quicktime',
            'thumbnail'  => 'required|image|mimes:jpg,jpeg,png',
            'duration'   => 'required|string',
        ]);

        // Upload video ke S3
        if ($request->hasFile('video_file')) {
            $path = $request->file('video_file')->store('videos', 's3');
            Storage::disk('s3')->setVisibility($path, 'public');
            $validated['video_url'] = Storage::url($path);
        }

        // Upload thumbnail ke S3
        if ($request->hasFile('thumbnail')) {
            $thumbPath = $request->file('thumbnail')->store('thumbnails', 's3');
            Storage::disk('s3')->setVisibility($thumbPath, 'public');
            $validated['thumbnail'] = Storage::url($thumbPath);
        }

        unset($validated['video_file']);

        Video::create($validated);

        return redirect()->route('videos.index')->with('success', 'Video berhasil ditambahkan!');
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
        $validated = $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'title'      => 'required|string',
            'description' => 'required|string',
            'video_file' => 'nullable|file|mimetypes:video/mp4,video/avi,video/mpeg,video/quicktime',
            'thumbnail'  => 'nullable|image|mimes:jpg,jpeg,png',
            'duration'   => 'required|string',
        ]);

        // Update video ke S3
        if ($request->hasFile('video_file')) {
            if ($video->video_url) {
                $oldPath = str_replace(config('filesystems.disks.s3.url') . '/', '', $video->video_url);
                Storage::disk('s3')->delete($oldPath);
            }
            $path = $request->file('video_file')->store('videos', 's3');
            Storage::disk('s3')->setVisibility($path, 'public');
            $validated['video_url'] = Storage::url($path);
        }

        // Update thumbnail ke S3
        if ($request->hasFile('thumbnail')) {
            if ($video->thumbnail) {
                $oldThumb = str_replace(config('filesystems.disks.s3.url') . '/', '', $video->thumbnail);
                Storage::disk('s3')->delete($oldThumb);
            }
            $thumbPath = $request->file('thumbnail')->store('thumbnails', 's3');
            Storage::disk('s3')->setVisibility($thumbPath, 'public');
            $validated['thumbnail'] = Storage::url($thumbPath);
        } else {
            unset($validated['thumbnail']);
        }

        unset($validated['video_file']);

        $video->update($validated);

        return redirect()->route('videos.index')->with('success', 'Video berhasil diperbarui!');
    }

    public function destroy(Video $video)
    {
        // Hapus thumbnail dari S3
        if ($video->thumbnail) {
            $oldThumb = str_replace(config('filesystems.disks.s3.url') . '/', '', $video->thumbnail);
            Storage::disk('s3')->delete($oldThumb);
        }

        // Hapus video dari S3
        if ($video->video_url) {
            $oldPath = str_replace(config('filesystems.disks.s3.url') . '/', '', $video->video_url);
            Storage::disk('s3')->delete($oldPath);
        }

        $video->delete();

        return back()->with('success', 'Video berhasil dihapus!');
    }
}
