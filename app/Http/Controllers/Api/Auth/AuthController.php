<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{

    

    /**
     * Register a new user.
     *
     * @OA\Post(
     *     path="/api/register",
     *     summary="Register a new user",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "email", "password"},
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="email", type="string", format="email", example="john@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="password123")
     *         )
     *     ),
     *     @OA\Response(response="201", description="User registered successfully"),
     *     @OA\Response(response="422", description="Validation errors")
     * )
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:6',
        ]);

        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->save();

        $token = $user->createToken('authToken')->plainTextToken;

        return response()->json(['message' => 'User registered successfully','token'=>$token,'user'=>$user], 201);
    }



    /**
     * Authenticate user and generate API token.
     *
     * @OA\Post(
     *     path="/api/login",
     *     summary="Authenticate user and generate API token",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email", "password"},
     *             @OA\Property(property="email", type="string", format="email", example="john@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="password123")
     *         )
     *     ),
     *     @OA\Response(response="200", description="Login successful"),
     *     @OA\Response(response="401", description="Invalid credentials")
     * )
     */
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $token = $user->createToken('authToken')->plainTextToken;
            $message = 'Login successful';

            return response()->json(['message'=>$message,'token' => $token,'user'=>$user], 200);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * Logout the authenticated user.
     *
     * @OA\Post(
     *     path="/api/logout",
     *     summary="Logout the authenticated user",
     *     tags={"Authentication"},
     *     security={{ "sanctum": {} }},
     *     @OA\Response(response="200", description="Logged out successfully"),
     *     @OA\Response(response="401", description="Unauthenticated")
     * )
     */
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json(['message' => 'Logged out successfully'], 200);
    }


    /**
     * Get the authenticated user.
     *
     * @OA\Get(
     *     path="/api/user",
     *     summary="Get the authenticated user",
     *     tags={"Authentication"},
     *     security={{ "sanctum": {} }},
     *     @OA\Response(response="200", description="User details"),
     *     @OA\Response(response="401", description="Unauthenticated")
     * )
     */
    public function user(Request $request)
    {
        return response()->json($request->user());
    }
    
}

