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
        // Mitigación CVE-2026-109618: Rechazar payloads sobredimensionados o anidamiento excesivo
        $contentLength = (int) $request->header('Content-Length', 0);
        if ($contentLength > self::MAX_CONTENT_LENGTH) {
            abort(Response::HTTP_REQUEST_ENTITY_TOO_LARGE, 'El tamaño de la solicitud excede el límite seguro permitido.');
        }

        if ($this->hasExcessiveNesting($request->all(), 16)) {
            abort(Response::HTTP_BAD_REQUEST, 'Estructura de parámetros no permitida.');
        }

        $response = $next($request);

        // Ocultar cabeceras que revelen la versión exacta de PHP (mitigación de reconocimiento)
        if (function_exists('header_remove')) {
            @header_remove('X-Powered-By');
        }

        // Only add headers if the response is an instance of Illuminate\Http\Response or similar
        if (method_exists($response, 'header')) {
            $response->headers->remove('X-Powered-By');

            // Mitigate Clickjacking
            $response->header('X-Frame-Options', 'SAMEORIGIN');

            // Mitigate XSS attacks by forcing the browser to block the response if XSS is detected
            $response->header('X-XSS-Protection', '1; mode=block');

            // Prevent MIME-sniffing
            $response->header('X-Content-Type-Options', 'nosniff');

            // Protect privacy by strictly controlling the referrer information
            $response->header('Referrer-Policy', 'strict-origin-when-cross-origin');

            // Cross-Origin isolation & protection headers
            $response->header('X-Permitted-Cross-Domain-Policies', 'none');
            $response->header('Cross-Origin-Opener-Policy', 'same-origin');

            // Content Security Policy (CSP)
            // Relajado para permitir Bootstrap, AlpineJS y GSAP que usan estilos y scripts en línea
            $csp = "default-src 'self'; ";
            $csp .= "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdnjs.cloudflare.com https://cdn.jsdelivr.net https://unpkg.com; ";
            $csp .= "style-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com https://fonts.googleapis.com https://unpkg.com; ";
            $csp .= "img-src 'self' data: https:; ";
            $csp .= "font-src 'self' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com data: https://fonts.gstatic.com https://unpkg.com; ";
            $csp .= "connect-src 'self' https:; ";
            $csp .= "frame-src 'self' https://accounts.google.com;"; // Para permitir OAuth de Google
            
            $response->header('Content-Security-Policy', $csp);

            // Strict-Transport-Security (HSTS)
            // Obliga al navegador a comunicarse siempre por HTTPS (se asume que en producción se usa HTTPS)
            $response->header('Strict-Transport-Security', 'max-age=31536000; includeSubDomains; preload');
            
            // Permissions-Policy: permitir geolocalización al propio origen (self) y restringir APIs sensibles
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
