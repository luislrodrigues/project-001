<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts: Figtree opcional; si falla la carga externa se usa fuente del sistema -->
    <link rel="preconnect" href="https://fonts.bunny.net" crossorigin>
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" media="print" onload="this.media='all'" />
    <noscript><link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" /></noscript>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --sidebar-width: 260px;
            --sidebar-collapsed-width: 70px;
        }
        .sidebar-gradient {
            background: linear-gradient(180deg, #1e40af 0%, #3b82f6 50%, #60a5fa 100%);
        }
        .nav-item-active {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(10px);
            border-left: 3px solid #fbbf24;
        }
        .nav-item-hover:hover {
            background: rgba(255, 255, 255, 0.1);
            transition: all 0.2s ease;
        }
    </style>
</head>
<body>
    <div id="app"></div>
</body>
</html>
