<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    /**
     * Store the authenticated user's profile.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|string|max:15',
            'address' => 'required|string',
            'city' => 'required|string',
            'state' => 'required|string',
            'country' => 'required|string',
            'zipcode' => 'required|string',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:20000',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $user = $request->user;
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $imagePath = $request->file('image')->store('images', 'public');

        $profile = Profile::create([
            'user_id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'city' => $request->city,
            'state' => $request->state,
            'country' => $request->country,
            'zipcode' => $request->zipcode,
            'image' => $imagePath,
        ]);

        return response()->json(['message' => 'Profile created successfully', 'profile' => $profile], 201);
    }

    public function show(Request $request)
    {
        // Ambil informasi pengguna dari middleware atau autentikasi
        $user = $request->user;
        if (!$user || !isset($user->id)) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // Cari profil berdasarkan user_id
        $profile = Profile::where('user_id', $user->id)->first();

        if (!$profile) {
            return response()->json(['message' => 'Profile not found'], 404);
        }

        return response()->json(['profile' => $profile]);
    }

    
    public function update(Request $request)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'phone' => 'required|string|max:15',
            'address' => 'required|string',
            'city' => 'required|string',
            'state' => 'required|string',
            'country' => 'required|string',
            'zipcode' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:20000',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        // Ambil informasi pengguna dari middleware atau autentikasi
        $user = $request->user;
        if (!$user || !isset($user->id)) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // Cari profil berdasarkan user_id
        $profile = Profile::where('user_id', $user->id)->first();

        if (!$profile) {
            return response()->json(['message' => 'Profile not found'], 404);
        }

        // Update data profil
        $profile->phone = $request->phone;
        $profile->address = $request->address;
        $profile->city = $request->city;
        $profile->state = $request->state;
        $profile->country = $request->country;
        $profile->zipcode = $request->zipcode;

        // Handle upload gambar jika ada
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('images', 'public');
            $profile->image = $imagePath;
        }

        $profile->save();

        return response()->json(['message' => 'Profile updated successfully', 'profile' => $profile]);
    }
    public function changePassword(Request $request) {
        $user = $request->user;
        $oldPassword = $request->oldPassword;
        $newPassword = $request->newPassword;

        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }
        $user = User::find($user);
        if ($user == null) {
            return "User not found!";
        }

        // Check if the old password matches
        if (!password_verify($oldPassword, $user->password)) {
            return "Old password is incorrect!";
        }

        // Update the password
        $user->password = password_hash($newPassword, PASSWORD_DEFAULT);
        $user->save();

        // Return success message or handle errors accordingly
        return "Password changed successfully!";
    }
}
