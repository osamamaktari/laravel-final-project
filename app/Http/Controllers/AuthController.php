<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'sometimes|in:admin,organizer,attendee', //
            'phone' => 'nullable|string|max:20',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role ?? 'attendee',
            'phone' => $request->phone,
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'User registered successfully',
            'user' => $user,
            'token' => $token,
        ], 201);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if (!Auth::attempt($request->only('email', 'password'))) {
            throw ValidationException::withMessages([
                'email' => ['These credentials do not match our records.'],
            ]);
        }

        $user = Auth::user();
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Logged in successfully',
            'user' => $user,
            'token' => $token,
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out successfully']);
    }

    public function user(Request $request)
    {
        return response()->json($request->user());
    }



public function updateProfile(Request $request)
{
    $user = $request->user();

    $validated = $request->validate([
        'name'  => ['required', 'string', 'max:255'],
        'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
        'phone' => ['nullable', 'string', 'max:20'],
    ]);

    $user->update($validated);

    return response()->json([
        'message' => 'Profile updated successfully',
        'user'    => $user,
    ]);
}

public function updatePassword(Request $request)
{
    $user = $request->user();

    $validated = $request->validate([
        'current_password' => ['required'],
        'new_password'     => ['required', 'string', 'min:8', 'confirmed'],
    ]);

    // Check old password
    if (!Hash::check($validated['current_password'], $user->password)) {
        return response()->json(['message' => 'Current password is incorrect'], 422);
    }

    $user->update([
        'password' => Hash::make($validated['new_password']),
    ]);

    return response()->json(['message' => 'Password updated successfully']);
}





}
