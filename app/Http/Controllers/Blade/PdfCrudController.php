<?php

namespace App\Http\Controllers\Blade;

use App\Http\Controllers\Controller;
use App\Models\Pdf;
use App\Models\Subject;
use Illuminate\Http\Request;

class PdfCrudController extends Controller
{
    public function index()
    {
        $pdfs = Pdf::with('subject')->latest()->get();
        return view('pdfs.index', compact('pdfs'));
    }

    public function create()
    {
        $subjects = Subject::all();
        return view('pdfs.create', compact('subjects'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'title' => 'required|string',
            'pages' => 'required|numeric|min:1',
            'pdf_url' => 'required|url',
        ]);

        Pdf::create($validated);

        return redirect()->route('pdfs.index')->with('success', 'PDF berhasil ditambahkan!');
    }

    public function show(Pdf $pdf)
    {
        return view('pdfs.show', compact('pdf'));
    }

    public function edit(Pdf $pdf)
    {
        $subjects = Subject::all();
        return view('pdfs.edit', compact('pdf', 'subjects'));
    }

    public function update(Request $request, Pdf $pdf)
    {
        $validated = $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'title' => 'required|string',
            'pages' => 'required|numeric|min:1',
            'pdf_url' => 'required|url',
        ]);

        $pdf->update($validated);

        return redirect()->route('pdfs.index')->with('success', 'PDF berhasil diperbarui!');
    }

    public function destroy(Pdf $pdf)
    {
        $pdf->delete();
        return back()->with('success', 'PDF berhasil dihapus.');
    }
}
