<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CartItem;
use App\Models\Transaction;
use Midtrans\Config;
use Midtrans\Snap;

class TransactionController extends Controller
{
    public function createTransactionFromCart(Request $request)
    {
        $user = $request->user;
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        // Get cart items for the user
        $cartItems = CartItem::where('user_id', $user->id)->get();
        if ($cartItems->isEmpty()) {
            return response()->json(['message' => 'Cart is empty'], 400);
        }

        $orderId = uniqid(); // Generate unique order ID
        $totalAmount = 0;
        $itemDetails = [];

        foreach ($cartItems as $cartItem) {
            $course = $cartItem->course;
            $itemAmount = $course->price * $cartItem->quantity;
            $totalAmount += $itemAmount;
            $itemDetails[] = [
                'id' => $course->id,
                'price' => $course->price,
                'quantity' => $cartItem->quantity,
                'name' => $course->title, // Ensure the key is 'name' with lowercase and matches course title
            ];

            // Create a transaction record for each cart item
            $transaction = Transaction::create([
                'user_id' => $user->id,
                'course_id' => $course->id,
                'quantity' => $cartItem->quantity,
                'amount' => $itemAmount,
                'order_id' => $orderId,
                'status' => 'pending',
            ]);
        }

        // Set Midtrans configuration
        Config::$serverKey = config('services.midtrans.server_key');
        Config::$isProduction = config('services.midtrans.is_production');
        Config::$isSanitized = true;
        Config::$is3ds = true;

        try {
            // Create Snap token
            $snapToken = Snap::getSnapToken([
                'transaction_details' => [
                    'order_id' => $orderId,
                    'gross_amount' => $totalAmount,
                ],
                'item_details' => $itemDetails,
                'customer_details' => [
                    'first_name' => $user->name,
                    'email' => $user->email,
                ],
            ]);

            // Update transactions with the Snap token
            Transaction::where('order_id', $orderId)->update(['snap_token' => $snapToken]);

            return response()->json([
                'message' => 'Transaction created successfully',
                'order_id' => $orderId,
                'snap_token' => $snapToken,
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to create transaction: ' . $e->getMessage()], 500);
        }
    }

    public function showPaymentPage($transactionId)
    {
        $transaction = Transaction::findOrFail($transactionId);
        $snap_token = $transaction->snap_token;

        return view('payment', compact('snap_token'));
    }
}