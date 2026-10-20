<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureColombianBilling
{
    /**
     * Handle an incoming request.
     * Restringe el acceso al módulo de Facturación Electrónica DIAN
     * únicamente a comercios legalmente registrados en Colombia.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            abort(401, 'No autenticado.');
        }

        // Si es administrador general, permitimos auditoría
        if ($user->role === 'admin') {
            return $next($request);
        }

        $company = $user->company;

        if (!$company) {
            abort(403, 'No tiene una empresa asociada para acceder a este módulo.');
        }

        // Verificación estricta: Si la empresa es de México (o cualquier país distinto a CO), bloquear acceso
        if ($company->country !== 'CO') {
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'Acceso denegado: El módulo de Facturación Electrónica DIAN está reservado exclusivamente para comercios registrados en Colombia.'
                ], 403);
            }

            return redirect()->route('business.dashboard')->with(
                'error',
                'El módulo de Facturación Electrónica DIAN está reservado exclusivamente para empresas registradas en Colombia. Los comercios en México operan bajo el esquema comercial internacional de Spotlight.'
            );
        }

        return $next($request);
    }
}
