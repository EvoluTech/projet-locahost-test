<?php

namespace App\Http\Middleware;

use Closure;

class CorsMiddleware
{
    public function handle($request, Closure $next)
    {
        // Autoriser les domaines spécifiques à accéder à l'API
        $allowedOrigins = [
            'http://localhost:5173',
        ];

        // Vérifiez si la requête provient d'un domaine autorisé
        if (in_array($request->header('Origin'), $allowedOrigins)) {
            // Ajoutez les en-têtes CORS appropriés
            return $next($request)
                ->header('Access-Control-Allow-Origin', $request->header('Origin'))
                ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, PATCH, DELETE, OPTIONS')
                ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization');
        }

        // Si le domaine n'est pas autorisé, vous pouvez choisir de retourner une réponse d'erreur ou simplement continuer la requête sans les en-têtes CORS.
        // Exemple de réponse d'erreur :
        // return response()->json(['message' => 'Domain not allowed'], 403);

        return $next($request);
    }
}
