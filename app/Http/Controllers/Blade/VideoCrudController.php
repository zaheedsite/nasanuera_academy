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
            'title' => 'required|string',
            'description' => 'required|string',
            'video_url' => 'required|url',
            'thumbnail' => 'required|image|mimes:jpg,jpeg,png',
            'duration' => 'required|string',
        ]);

        // Simpan thumbnail
        if ($request->hasFile('thumbnail')) {
            $path = $request->file('thumbnail')->store('thumbnails', 'public');
            $validated['thumbnail'] = $path;
        }

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
            'title' => 'required|string',
            'description' => 'required|string',
            'video_url' => 'required|url',
            'thumbnail' => 'nullable|image|mimes:jpg,jpeg,png',
            'duration' => 'required|string',
        ]);

        // Update thumbnail jika diupload ulang
        if ($request->hasFile('thumbnail')) {
            if ($video->thumbnail && Storage::disk('public')->exists($video->thumbnail)) {
                Storage::disk('public')->delete($video->thumbnail);
            }

            $path = $request->file('thumbnail')->store('thumbnails', 'public');
            $validated['thumbnail'] = $path;
        } else {
            unset($validated['thumbnail']);
        }

        $video->update($validated);
        return redirect()->route('videos.index')->with('success', 'Video berhasil diperbarui!');
    }

    public function destroy(Video $video)
    {
        if ($video->thumbnail && Storage::disk('public')->exists($video->thumbnail)) {
            Storage::disk('public')->delete($video->thumbnail);
        }

        $video->delete();
        return back()->with('success', 'Video berhasil dihapus!');
    }
}
