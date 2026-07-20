<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!$request->user()) {
            return redirect()->route('login');
        }

        // Vérifier si l'utilisateur a l'un des rôles requis
        // Dans la DB, on utilise 'type' pas 'role', et les valeurs sont : admin, employee, client
        $userType = strtolower($request->user()->type->value ?? 'client');

        // Mapping des rôles demandés vers les types DB
        $roleMapping = [
            'administrateur' => 'admin',
            'employe'        => 'employee',
            'client'         => 'client',
        ];

        foreach ($roles as $role) {
            $mappedRole = $roleMapping[strtolower($role)] ?? strtolower($role);
            if ($userType === $mappedRole) {
                return $next($request);
            }
        }

        // Si admin essaie d'accéder à une route, il a tous les droits
        if ($userType === 'admin') {
            return $next($request);
        }

        // Si l'utilisateur n'a pas le bon rôle
        abort(403, 'Accès refusé. Vous n\'avez pas les permissions nécessaires.');
    }
}
