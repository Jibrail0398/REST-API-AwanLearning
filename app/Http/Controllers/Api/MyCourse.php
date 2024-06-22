<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MyCourse extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $courses = $user->courses;
        return response()->json($courses);
    }

    public function show(Request $request, $id)
    {
        $user = $request->user();
        $course = $user->courses()->findOrFail($id);

        if ($course->status === 'paid' && $course->content_status !== 'not_preview') {
            // Access the content for users who have purchased the course
            return response()->json($course->content);
        } else {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    }
}
