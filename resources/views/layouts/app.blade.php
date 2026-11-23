<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Spotlight')</title>
    <!-- Favicon de Spotlight -->
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Modern Typography: Plus Jakarta Sans & Outfit -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800;900&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Spotlight Design System (separated by concern) -->
    <link href="{{ asset('css/tokens.css') }}?v=2.7" rel="stylesheet">
    <link href="{{ asset('css/components.css') }}?v=2.7" rel="stylesheet">
    <link href="{{ asset('css/layout.css') }}?v=2.7" rel="stylesheet">
    <link href="{{ asset('css/utilities.css') }}?v=2.7" rel="stylesheet">
    @auth
        @if(in_array(auth()->user()->role, ['user', 'business']))
        {{-- El widget de IA se renderiza en el <body>, después de @stack('styles'),
             habilitado exclusivamente para consumidores y emprendedores registrados --}}
        <link href="{{ asset('css/chatbot.css') }}?v=2.7" rel="stylesheet">
        @endif
    @endauth
    @stack('styles')
    <script>
        (function(){
            var l = localStorage.getItem('spotlight-lang') || 'es';
            document.documentElement.setAttribute('lang', l);
        })();
    </script>
</head>
<body>
@yield('content')
<x-cookie-banner />
@auth
    @if(in_array(auth()->user()->role, ['user', 'business']))
        <x-chatbot-widget />
    @endif
@endauth
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('js/i18n.js') }}?v=2.5"></script>
@stack('scripts')
</body>
</html>
