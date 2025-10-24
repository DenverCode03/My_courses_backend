<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Mail\RegisterUserMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;
use Throwable;

class AuthController extends Controller
{
    public function register(RegisterRequest $request) {

        $data = $request->validated();
        
        $data['password'] = Hash::make($request->password);
        $data['verification_token'] = hash("sha256", Str::random(64));

        try {
            $user = User::create($data);
        }catch(Throwable $e) {
            return response()->json([
                'status' => false,
                'message' => 'Une erreur est survenue pendant la creation de l\'utilisateur',
                'error' => $e->getMessage()
            ], 422);
        }
        // 

        try {
            $user = User::latest()->first();
            log::debug(gettype($user));
            Mail::to($user->email)->send(new RegisterUserMail($user));
            $user->delete();
            
            return response()->json([
                'message' => 'Un email de confirmation avec le lien de connexion vient de vous etre envoyer'
            // 'user' => [
            //     'id' => $user->id,
            //     'name' => $user->name,
            //     'email' => $user->email,
            //     'role' => $user->role
            // ],
            // 'token' => $token->token,
            // 'expires_at' => $token->expires_at
        ], 201);
        }catch(Throwable $e) {
            $user->delete();
            return response()->json([
                'status' => false,
                'message' => 'Une erreur est survenue pendant l\'envoi d\'email',
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
        
        if ($user->email_verified_at == null) {
            return response()->json([
                'message' => 'Compte inactif : veillez verifier votre email pour activer votre compte'
            ], 401);
        }

        $token = $user->createToken('the token');

        return response()->json([
            'status' => "succes",
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
