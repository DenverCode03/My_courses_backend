<?php

namespace App\Http\Middleware;

use App\Models\ApiToken;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateWithToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();
        // dd($token);
        if (!$token) {
            return response()->json(['message' => 'Non authorisé : token manquant'], 401);
        }

        $apitoken = ApiToken::where('token', $token)->with('user')->first();


        if (!$apitoken) {
            return response()->json(['message' => 'Non authorisé : token invalide'], 401);
        }
        // dd($apitoken->is_expired());
        if ($apitoken->is_expired()) {
            $apitoken->delete();
            return response()->json(['message' => 'Non authorisé : token expiré'], 401);
        }

        // ajoutons l utilisateur a la Request
        $user = $apitoken->user;

        if (!$user) {
            return response()->json(['message' => 'Utilisateur introuvable'], 404);
        }
        if ($user->email_verified_at == null) {
            return response()->json([
                'message' => 'Compte inactif : veillez verifier votre email pour activer votre compte'
            ], 401);
        }
        $request->merge(['user' => $user]);

        // dd($request->user);
        
        $apitoken->update(['last_used_at' => now()]);
        
        return $next($request);
    }
}
