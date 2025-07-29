<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Subject;

class SubjectController extends Controller
{
    public function indexByRole($role)
    {
        $subjects = Subject::where('role', $role)->get();

        return response()->json([
            'status' => 'success',
            'data' => $subjects,
        ]);
    }
}
