<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Video;

class VideoController extends Controller
{
    public function getBySubject($subjectId)
    {
        $videos = Video::where('subject_id', $subjectId)->get();

        return response()->json([
            'status' => 'success',
            'data' => $videos,
        ]);
    }

    public function index()
    {
        $videos = Video::with('subject')->latest()->get();

        return response()->json([
            'status' => 'success',
            'message' => 'List video berhasil diambil',
            'data' => $videos,
        ]);
    }
}
