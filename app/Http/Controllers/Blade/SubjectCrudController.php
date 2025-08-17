<?php

namespace App\Http\Controllers\Blade;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SubjectCrudController extends Controller
{
    public function index()
    {
        $subjects = Subject::all();
        return view('subjects.index', compact('subjects'));
    }

    public function create()
    {
        return view('subjects.create');
    }

    public function show(Subject $subject)
    {
        return view('subjects.show', compact('subject'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string',
            'description' => 'required|string',
            'role' => 'required|string',
            'thumbnail' => 'required|image|mimes:jpg,jpeg,png',
            'jumlah_video' => 'required|numeric',
        ]);

        if ($request->hasFile('thumbnail')) {
            // Upload ke Spaces
            $path = $request->file('thumbnail')->store('thumbnail_subject', 's3');

            // Set file agar bisa diakses publik
            Storage::disk('s3')->setVisibility($path, 'public');

            // Simpan URL publik
            $validated['thumbnail'] = Storage::url($path);
        }

        Subject::create($validated);

        return redirect()->route('subjects.index')->with('success', 'Subject berhasil ditambahkan!');
    }

    public function edit(Subject $subject)
    {
        return view('subjects.edit', compact('subject'));
    }

    public function update(Request $request, Subject $subject)
    {
        $validated = $request->validate([
            'title' => 'required|string',
            'description' => 'required|string',
            'role' => 'required|string',
            'thumbnail' => 'nullable|image|mimes:jpg,jpeg,png',
            'jumlah_video' => 'required|numeric',
        ]);

        if ($request->hasFile('thumbnail')) {
            // Hapus thumbnail lama di Spaces kalau ada
            if ($subject->thumbnail) {
                $oldPath = str_replace(config('filesystems.disks.s3.url') . '/', '', $subject->thumbnail);
                Storage::disk('s3')->delete($oldPath);
            }

            // Upload thumbnail baru ke Spaces
            $path = $request->file('thumbnail')->store('thumbnail_subject', 's3');

            // Set file agar bisa diakses publik
            Storage::disk('s3')->setVisibility($path, 'public');

            // Simpan URL publik
            $validated['thumbnail'] = Storage::url($path);
        } else {
            $validated['thumbnail'] = $subject->thumbnail;
        }

        $subject->update($validated);

        return redirect()->route('subjects.index')->with('success', 'Subject berhasil diupdate!');
    }

    public function destroy(Subject $subject)
    {
        if ($subject->thumbnail) {
            $oldPath = str_replace(config('filesystems.disks.s3.url') . '/', '', $subject->thumbnail);
            Storage::disk('s3')->delete($oldPath);
        }

        $subject->delete();

        return back()->with('success', 'Subject berhasil dihapus!');
    }
}
