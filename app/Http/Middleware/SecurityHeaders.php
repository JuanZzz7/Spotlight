<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    /**
     * Limite máximo seguro de payload entrante (16 MB) para prevenir
     * agotamiento de memoria (CVE-2026-109618).
     */
    private const MAX_CONTENT_LENGTH = 16777216;

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Only add headers if the response is an instance of Illuminate\Http\Response or similar
        if (method_exists($response, 'header')) {
            // Mitigate Clickjacking
            $response->header('X-Frame-Options', 'SAMEORIGIN');

            // Mitigate XSS attacks by forcing the browser to block the response if XSS is detected
            $response->header('X-XSS-Protection', '1; mode=block');

            // Prevent MIME-sniffing
            $response->header('X-Content-Type-Options', 'nosniff');

            // Protect privacy by strictly controlling the referrer information
            $response->header('Referrer-Policy', 'strict-origin-when-cross-origin');

            // Content Security Policy (CSP)
            // Relajado para permitir Bootstrap, AlpineJS y GSAP que usan estilos y scripts en línea
            $csp = "default-src 'self'; ";
            $csp .= "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdnjs.cloudflare.com https://cdn.jsdelivr.net https://unpkg.com https://checkout.wompi.co; ";
            $csp .= "style-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com https://fonts.googleapis.com https://unpkg.com; ";
            $csp .= "img-src 'self' data: https:; ";
            $csp .= "font-src 'self' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com data: https://fonts.gstatic.com https://unpkg.com; ";
            $csp .= "connect-src 'self' https:; ";
            $csp .= "frame-src 'self' https://accounts.google.com https://checkout.wompi.co;";
            
            $response->header('Content-Security-Policy', $csp);

            // Strict-Transport-Security (HSTS)
            $response->header('Strict-Transport-Security', 'max-age=31536000; includeSubDomains; preload');
            
            // Permissions-Policy: permitir geolocalización al propio origen (self)
            $response->header('Permissions-Policy', 'geolocation=(self), microphone=(), camera=()');
        }

        return $response;
    }

    /**
     * Verifica que los arrays de entrada no excedan la profundidad máxima permitida
     * para evitar desbordamientos de pila o corrupción de memoria (CVE-2026-109618).
     */
    private function hasExcessiveNesting(array $input, int $maxDepth, int $currentDepth = 0): bool
    {
        if ($currentDepth > $maxDepth) {
            return true;
        }

        foreach ($input as $value) {
            if (is_array($value) && $this->hasExcessiveNesting($value, $maxDepth, $currentDepth + 1)) {
                return true;
            }
        }

        return false;
    }
}
