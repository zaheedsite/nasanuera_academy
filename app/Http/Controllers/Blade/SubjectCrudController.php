<?php

namespace App\Http\Controllers\Blade;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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
            $path = $request->file('thumbnail')->store('thumbnail_subject', 'public');
            $validated['thumbnail'] = $path;
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
            if ($subject->thumbnail && Storage::disk('public')->exists($subject->thumbnail)) {
                Storage::disk('public')->delete($subject->thumbnail);
            }

            $path = $request->file('thumbnail')->store('thumbnail_subject', 'public');
            $validated['thumbnail'] = $path;
        } else {
            $validated['thumbnail'] = $subject->thumbnail;
        }

        $subject->update($validated);

        return redirect()->route('subjects.index')->with('success', 'Subject berhasil diupdate!');
    }

    public function destroy(Subject $subject)
    {
        if ($subject->thumbnail && Storage::disk('public')->exists($subject->thumbnail)) {
            Storage::disk('public')->delete($subject->thumbnail);
        }

        $subject->delete();

        return back()->with('success', 'Subject berhasil dihapus!');
    }
}
