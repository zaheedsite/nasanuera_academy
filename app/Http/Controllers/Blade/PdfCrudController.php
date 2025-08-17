<?php

namespace App\Http\Controllers\Blade;

use App\Http\Controllers\Controller;
use App\Models\Pdf;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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
            'title'      => 'required|string',
            'pages'      => 'required|numeric|min:1',
            'pdf_file'   => 'required|file|mimes:pdf|max:10240', // max 10MB
        ]);

        // Upload ke S3
        $path = $request->file('pdf_file')->store('pdfs', 's3');
        Storage::disk('s3')->setVisibility($path, 'public');

        $validated['pdf_url'] = Storage::url($path);

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
            'title'      => 'required|string',
            'pages'      => 'required|numeric|min:1',
            'pdf_file'   => 'nullable|file|mimes:pdf|max:10240',
        ]);

        if ($request->hasFile('pdf_file')) {
            // Hapus file lama (opsional, kalau mau bersih)
            if ($pdf->pdf_url) {
                $oldPath = parse_url($pdf->pdf_url, PHP_URL_PATH);
                $oldPath = ltrim($oldPath, '/');
                Storage::disk('s3')->delete($oldPath);
            }

            // Upload baru
            $path = $request->file('pdf_file')->store('pdfs', 's3');
            Storage::disk('s3')->setVisibility($path, 'public');
            $validated['pdf_url'] = Storage::url($path);
        }

        $pdf->update($validated);

        return redirect()->route('pdfs.index')->with('success', 'PDF berhasil diperbarui!');
    }

    public function destroy(Pdf $pdf)
    {
        if ($pdf->pdf_url) {
            $oldPath = parse_url($pdf->pdf_url, PHP_URL_PATH);
            $oldPath = ltrim($oldPath, '/');
            Storage::disk('s3')->delete($oldPath);
        }

        $pdf->delete();
        return back()->with('success', 'PDF berhasil dihapus.');
    }
}
