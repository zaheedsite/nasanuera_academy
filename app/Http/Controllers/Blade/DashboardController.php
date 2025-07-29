<?php

namespace App\Http\Controllers\Blade;
use Illuminate\Routing\Controller;
use App\Models\Subject;
use App\Models\Video;
use App\Models\Pdf;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $subjectCount = Subject::count();
        $videoCount = Video::count();
        $pdfCount = Pdf::count();

        return view('dashboard', compact('subjectCount', 'videoCount', 'pdfCount'));
    }
}
