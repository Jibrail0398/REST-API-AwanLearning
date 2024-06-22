<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Content;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ContentController extends Controller
{
    public function index($courseId)
    {
        $contents = Content::where('course_id', $courseId)->get();
        return response()->json($contents);
    }

    public function store(Request $request, $courseId)
{
    $user = $request->user;
    if (!$user) {
        return response()->json(['error' => 'Unauthenticated'], 401);
    }

    $course = Course::findOrFail($courseId);

    if ($user->id !== $course->instructor_id) {
        return response()->json(['error' => 'You are not the owner of this course'], 403);
    }

    $validator = Validator::make($request->all(), [
        'title' => 'required|string|max:255',
        'description' => 'required|string',
        // 'thumnailURL' => 'required|mimes:jpeg,png,jpg,gif,svg|max:10240',
        'vidioURL' => 'required|mimes:mp4,mov,ogg,qt|max:100240',
    ]);

    if ($validator->fails()) {
        return response()->json($validator->errors(), 400);
    }

    // Store the uploaded files
    // $tumbnailPath = $request->file('thumnailURL')->store('public/thumbnails');
    $vidioPath = $request->file('vidioURL')->store('public/videos');

    // Create the content
    $content = new Content([
        'title' => $request->title,
        'description' => $request->description,
        // 'thumnailURL' => Storage::url($tumbnailPath), // Store the URL path
        'vidioURL' => Storage::url($vidioPath), // Store the URL path
    ]);

    // Save content relation to the course
    $course->contents()->save($content);

    return response()->json($content, 201);
}

    

    public function show($courseId, $id)
        {
            $content = Content::where('course_id', $courseId)->findOrFail($id);
            return response()->json($content);
        }

        public function update(Request $request, $courseId, $id)
        {
            $user = $request->user;
    
            // Periksa apakah user valid
            if (!$user) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }
    
            $course = Course::findOrFail($courseId);
    
            if ($user->id !== $course->instructor_id) {
                return response()->json(['error' => 'You are not the owner of this course'], 403);
            }
    
            // Logging the request data
            Log::info('Request data: ', $request->all());
    
            $validator = Validator::make($request->all(), [
                'title' => 'sometimes|string|max:255',
                'description' => 'sometimes|string',
                // 'thumnailURL' => 'sometimes|mimes:jpeg,png,jpg,gif,svg|max:10240',
                'vidioURL' => 'sometimes|mimes:mp4,mov,ogg,qt|max:100240',
                'status' => 'sometimes|required|in:preview,not_preview',
            ]);
    
            if ($validator->fails()) {
                Log::warning('Validation failed: ', $validator->errors()->toArray());
                return response()->json($validator->errors(), 400);
            }
    
            $validatedData = $validator->validated();
            // Log the validated data
            Log::info('Validated data: ', $validatedData);
    
            $content = Content::where('course_id', $courseId)->findOrFail($id);
            
            // Log content before update
            Log::info('Content before update: ', $content->toArray());
    
            $updateResult = $content->update($validatedData);
    
            // Log the result of the update operation
            Log::info('Update result: ', ['success' => $updateResult]);
    
            // Log content after update
            $content->refresh(); // Reload the model from the database
            Log::info('Content after update: ', $content->toArray());
    
            if (!$updateResult) {
                return response()->json(['error' => 'Update failed'], 500);
            }
    
            return response()->json(['message' => 'Content updated successfully', 'content' => $content]);
        }

    public function destroy($courseId, $id, Request $request)
    {
        $user = $request->user;
        $course = Course::findOrFail($courseId);

        if ($user->id !== $course->instructor_id) {
            return response()->json(['error' => 'You are not the owner of this course'], 403);
        }

        $content = Content::where('course_id', $courseId)->findOrFail($id);
        $content->delete();

        return response()->json(['message' => 'Content deleted successfully']);
    }
}
