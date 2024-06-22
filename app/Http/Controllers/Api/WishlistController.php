<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Wishlist;
use App\Models\Course;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    public function addToWishlist(Request $request, $courseId)
{
    $user = $request->user;
    if (!$user) {
        return response()->json(['message' => 'Unauthenticated'], 401);
    }

    // Pastikan course dengan ID yang diberikan ada
    $course = Course::findOrFail($courseId);

    // Pastikan tidak ada duplikasi wishlist untuk user dan course yang sama
    $existingWishlist = Wishlist::where('user_id', $user->id)->where('course_id', $courseId)->first();
    if ($existingWishlist) {
        return response()->json(['message' => 'Course already in wishlist'], 400);
    }

    // Tambahkan ke wishlist
    $wishlist = Wishlist::create([
        'user_id' => $user->id,
        'course_id' => $courseId,
    ]);

    return response()->json(['message' => 'Course added to wishlist successfully', 'wishlist' => $wishlist]);
}


public function removeFromWishlist(Request $request, $courseId)
{
    $user = $request->user;
    if (!$user) {
        return response()->json(['message' => 'Unauthenticated'], 401);
    }

    // Cari wishlist yang sesuai untuk pengguna dan kursus yang diberikan
    $wishlist = Wishlist::where('user_id', $user->id)->where('course_id', $courseId)->first();
    if (!$wishlist) {
        return response()->json(['message' => 'Course not found in wishlist'], 404);
    }

    // Hapus wishlist
    $wishlist->delete();

    return response()->json(['message' => 'Course removed from wishlist successfully']);
}

public function getWishlist(Request $request)
{
    $user = $request->user;
    if (!$user) {
        return response()->json(['message' => 'Unauthenticated'], 401);
    }

    // Ambil semua wishlist yang dimiliki oleh pengguna
    $wishlists = Wishlist::where('user_id', $user->id)->with('course')->get();

    return response()->json(['wishlists' => $wishlists]);
}
}
