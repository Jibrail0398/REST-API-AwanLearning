<?php

namespace App\Http\Controllers\Api;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::all();

        return response()->json($categories, 200);
    }

    public function show($id)
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json(['message' => 'Category not found.'], 404);
        }

        return response()->json($category, 200);
    }

    public function store(Request $request)
    {
        

        $messages = [
            'name.required' => 'Nama kategori harus diisi.',
            'name.unique' => 'Nama kategori sudah digunakan.'
        ];

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|unique:categories',
        ], $messages);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        try {
            $category = Category::create([
                'name' => $request->name,
            ]);

            return response()->json($category, 201);
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json(['message' => 'Failed to create category.'], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json(['message' => 'Category not found.'], 404);
        }

        $messages = [
            'name.required' => 'Nama kategori harus diisi.',
            'name.unique' => 'Nama kategori sudah digunakan.'
        ];

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|unique:categories,name,' . $id,
        ], $messages);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        try {
            $category->update([
                'name' => $request->name,
            ]);

            return response()->json($category, 200);
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json(['message' => 'Failed to update category.'], 500);
        }
    }

    public function destroy($id)
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json(['message' => 'Category not found.'], 404);
        }

        try {
            $category->delete();

            return response()->json(['message' => 'Category deleted successfully.'], 200);
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json(['message' => 'Failed to delete category.'], 500);
        }
    }
}
