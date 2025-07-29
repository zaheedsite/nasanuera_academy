<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pdf;
use Illuminate\Http\Request;

class PdfController extends Controller
{
    public function index()
    {
        $pdfs = Pdf::with('subject')->latest()->get();

        return response()->json([
            'status' => 'success',
            'data' => $pdfs
        ]);
    }

    public function show($id)
    {
        $pdf = Pdf::with('subject')->find($id);

        if (!$pdf) {
            return response()->json([
                'status' => 'error',
                'message' => 'PDF not found'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $pdf
        ]);
    }
}
