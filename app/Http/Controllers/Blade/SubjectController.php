<?php

namespace App\Http\Controllers\Blade;

use App\Http\Controllers\Controller;
use App\Models\Subject;

class SubjectController extends Controller
{
    public function index($role)
    {
        $subjects = Subject::where('role', $role)->latest()->get();

        return view('subjects.index', compact('subjects', 'role'));
    }
}
