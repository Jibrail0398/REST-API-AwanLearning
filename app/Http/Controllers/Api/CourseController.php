<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Requirement;

class CourseController extends Controller
{
    /**
     * Store a newly created course.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:20000',
            'pre_vidio' => 'required|mimes:mp4,mov,ogg,qt|max:10000240',
            'category_ids' => 'required|array',
            'category_ids.*' => 'exists:categories,id',
            'requirements' => 'required|array',
            'requirements.*.description' => 'required|string', // Validasi description pada setiap requirement
            'level_id' => 'required|exists:levels,id',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $user = $request->user;
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        // Simpan data course
        $course = Course::create([
            'title' => $request->title,
            'description' => $request->description,
            'price' => $request->price,
            'image' => $request->file('image')->store('images', 'public'),
            'pre_vidio' => $request->file('pre_vidio')->store('videos', 'public'),
            'instructor_id' => $user->id,
            'level_id' => $request->level_id,
        ]);

        // Simpan requirements
        if ($request->has('requirements')) {
            foreach ($request->requirements as $requirement) {
                Requirement::create([
                    'description' => $requirement['description'],
                    'course_id' => $course->id,
                ]);
            }
        }

        $course->categories()->attach($request->category_ids);

        return response()->json(['message' => 'Course created successfully', 'course' => $course], 201);
    }

    /**
     * Display the specified course.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    /**
     * Display the specified course with confirmed status.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        // Mengambil course yang statusnya 'confirm' beserta owner-nya (instruktur)
        $course = Course::with('owner')->where('status', 'confirm')->findOrFail($id);
        return response()->json(['course' => $course]);
    }

    /**
     * Update the specified course.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
{
    // Ambil data kursus berdasarkan $id
    $course = Course::findOrFail($id);

    // Periksa apakah pengguna yang melakukan request adalah pemilik course
    $user = $request->user;
    if (!$user || $user->id !== $course->instructor_id) {
        return response()->json(['message' => 'Kamu Bukan pemilik Course jangan coba-coba masuk'], 403);
    }

    // Validasi input
    $validator = Validator::make($request->all(), [
        'title' => 'required|string|max:255',
        'description' => 'required|string',
        'price' => 'sometimes|numeric',
        'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:20000',
        'pre_vidio' => 'required|mimes:mp4,mov,ogg,qt|max:10000240',
        // 'category_ids' => 'required|array',
        // 'category_ids.*' => 'exists:categories,id',
        // 'level_id' => 'required|exists:levels,id',
    ]);

    if ($validator->fails()) {
        return response()->json($validator->errors(), 400);
    }

    // Lakukan update data course
    $course->title = $request->title;
    $course->description = $request->description;
    $course->price = $request->price;
    

    // Handle upload gambar jika ada
    if ($request->hasFile('image')) {
        $course->image = $request->file('image')->store('images', 'public');
    }

    // Simpan kategori baru
    $course->categories()->sync($request->category_ids);

    $course->save();

    return response()->json(['message' => 'Course updated successfully', 'course' => $course]);
}


    /**
     * Remove the specified course.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id, Request $request)
    {
        // Ambil data kursus berdasarkan $id
        $course = Course::findOrFail($id);
    
        // Periksa apakah pengguna yang melakukan request adalah pemilik course
        $user = $request->user;
        if (!$user || $user->id !== $course->instructor_id) {
            return response()->json(['message' => 'You are not authorized to delete this course'], 403);
        }
    
        // Hapus kursus
        $course->delete();
    
        return response()->json(['message' => 'Course deleted successfully']);
    }

    public function showall(){
        $course = Course::all()->where('status', 'confirm');
        return response()->json(['course' => $course]);
    }
    
}
