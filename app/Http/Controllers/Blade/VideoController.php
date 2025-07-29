<?php

namespace App\Http\Controllers\Blade;

use App\Http\Controllers\Controller;
use App\Models\Subject;

class VideoController extends Controller
{
    public function index($id)
    {
        $subject = Subject::with('videos')->findOrFail($id);
        return view('videos.index', compact('subject'));
    }
}
