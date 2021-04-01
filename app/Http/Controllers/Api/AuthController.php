<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Redis;
use Illuminate\Validation\ValidationException;

use function PHPUnit\Framework\throwException;

class AuthController extends Controller
{

    public function __construct()
    {
        $this->middleware(['auth:sanctum'])->only(['logout']);
    }

    public function register(Request $request)
    {
        $validatedData = $request->validate([

            'name' => 'required|string|min:3|max:255',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|confirmed'

        ]);

        $user = User::create([
             'name'     => $validatedData['name'],
             'email'    => $validatedData['email'],
             'password' => Hash::make($validatedData['password'])
        ]);

        $token = $user->createToken('apptoken')->plainTextToken;

        return response()->json([
            'message' => 'success',
            'token' => $token,
        ], 200);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'     => 'required',
            'password'  => 'required'
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'message' => 'The credentials are not correct'
            ]);
        }

        return $user->createToken('apptoken')->plainTextToken;
    }

    public function logout(Request $request)
    {
        auth()->user()->tokens()->delete();
        return response()->json([
            'status' => 'success',
            'message' => 'Logged out!'
        ]);
    }
}
