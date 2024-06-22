<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CartItem;
use App\Models\Course;
use Illuminate\Support\Facades\Validator;

class CartController extends Controller
{
    public function addToCart(Request $request, $courseId)
    {
        $user = $request->user;
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $course = Course::find($courseId);
        if (!$course) {
            return response()->json(['message' => 'Course not found'], 404);
        }

        // Cek apakah item sudah ada di keranjang
        $existingCartItem = CartItem::where('user_id', $user->id)->where('course_id', $courseId)->first();
        if ($existingCartItem) {
            $existingCartItem->quantity += 1;
            $existingCartItem->save();
        } else {
            // Jika tidak ada, tambahkan item baru ke keranjang
            CartItem::create([
                'user_id' => $user->id,
                'course_id' => $courseId,
                'total_price' => $course->price,
                'quantity' => 1,
            ]);
        }

        return response()->json(['message' => 'Course added to cart successfully']);
    }

    public function removeFromCart(Request $request, $cartId)
    {
        $user = $request->user;
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        // Cari dan hapus item di keranjang
        $cartItem = cartitem::where('user_id', $user->id)->find($cartId);
        if (!$cartItem) {
            return response()->json(['message' => 'Item not found in cart'], 404);
        }

        $cartItem->delete();

        return response()->json(['message' => 'Item removed from cart successfully']);
    }

    public function cart(Request $request)
    {
        $user = $request->user;
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        // Ambil semua item dalam keranjang untuk pengguna tertentu
        $cartItems = cartitem::where('user_id', $user->id)->with('course')->get();

        return response()->json(['cart_items' => $cartItems]);
    }
}
