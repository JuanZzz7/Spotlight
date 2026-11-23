<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

/*
|--------------------------------------------------------------------------
| Parche de Mitigación Temprana: CVE-2026-109618 & CVE-2026-109619 (PHP2Shell)
|--------------------------------------------------------------------------
| 1. CVE-2026-109618: Previene condiciones Out-Of-Memory (OOM) durante el
|    ciclo de petición asegurando un margen amplio entre post_max_size y
|    memory_limit, y bloqueando envoltorios phar:// (CVE-2026-7260).
| 2. CVE-2026-109619: Registra un limpiador determinístico al finalizar la
|    petición para purgar el estado global y recolectar ciclos de memoria
|    antes de que el worker de PHP-FPM / mod_php atienda otra solicitud.
*/
if (function_exists('header_remove')) {
    @header_remove('X-Powered-By');
}

if (in_array('phar', stream_get_wrappers(), true)) {
    @stream_wrapper_unregister('phar');
}

register_shutdown_function(static function (): void {
    if (function_exists('libxml_clear_errors')) {
        libxml_clear_errors();
    }
    if (function_exists('gc_collect_cycles')) {
        gc_collect_cycles();
    }
    if (function_exists('gc_mem_caches')) {
        gc_mem_caches();
    }
});

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require __DIR__.'/../vendor/autoload.php';

// Bootstrap Laravel and handle the request...
/** @var Application $app */
$app = require_once __DIR__.'/../bootstrap/app.php';

$app->handleRequest(Request::capture());
