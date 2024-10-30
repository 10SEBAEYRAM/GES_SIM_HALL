<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckUser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (!$request->user()) {
            return redirect('login'); // Redirige vers la page de connexion si l'utilisateur n'est pas authentifié
        }

        return $next($request); // Continue le traitement de la requête
    }
}
