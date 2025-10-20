<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Throwable;

class AuthController extends Controller
{
    public function register (RegisterRequest $request) {
        $data = $request->validated();
        $data['password'] = Hash::make($request->password);

        // $user = User::first();
        // $token = $user->createToken('token');

        // dd($token);


        try {
            $user = User::create($data);
        }catch(Throwable $e) {
            return response()->json([
                'status' => false,
                'message' => 'Une erreur est survenue',
                'error' => $e->getMessage()
            ], 422);
        }

        try {
            $token = $user->createToken('my_token');

            return response()->json([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role
            ],
            'token' => $token->token,
            'expires_at' => $token->expires_at
        ], 201);
        }catch(Throwable $e) {
            $user->delete();
            return response()->json([
                'status' => false,
                'message' => 'Une erreur est survenue',
                'error' => $e->getMessage()
            ], 422);
        }
    }

    public function login(LoginRequest $request) {
        Log::debug('debut');
        $request->validated();
        Log::debug('fin');

        try {
            $user = User::where('email', $request->email)->first();
             

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => 'Indentifiants incorrects'
            ]);

            // return response()->json()
        }
// dd($user);
        $token = $user->createToken('the token');

        return response()->json([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role
            ],
            'token' => $token->token,
            'expires_at' => $token->expires_at
        ]);
    }catch(\Exception $e) {
        Log::error($e->getMessage());
        throw $e;
    }
    }
}
