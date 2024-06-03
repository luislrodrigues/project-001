<!-- resources/views/layouts/app.blade.php -->
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Styles -->
    @livewireStyles
</head>
<body class="font-sans antialiased">
<div id="app">
    @auth
    <nav class="bg-white border-b border-gray-300">
        <div class="flex justify-between items-center px-6 bg-blue-300" style="height: 60px">
            <button id="menu-button" onclick="expandSidebar()">
                <i class="fas fa-bars text-cyan-500 text-lg text-white"></i>
            </button>
        </div>
    </nav>
    <div id="sidebar" class="w-16 bg-white h-screen fixed transition-all duration-200 ease-in-out">
        <div class="p-2 space-y-4">
            <a href="{{ route('report') }}" title="Informes" class="relative w-full px-3 py-3 flex items-center space-x-4 justify-start text-gray-500 rounded-lg group @if(request()->routeIs('report')) bg-blue-300 text-white @endif" onclick="highlightSidebarItem(this)">
                <i class="fas fa-file-alt"></i>
                <span class="font-medium transition-all duration-200 opacity-0">Informes</span>
            </a>
            <a href="{{ route('company') }}" title="Empresas" class="relative w-full px-3 py-3 flex items-center space-x-4 justify-start text-gray-500 rounded-lg group @if(request()->routeIs('company')) bg-blue-300 text-white @endif" onclick="highlightSidebarItem(this)">
                <i class="fas fa-building"></i>
                <span class="font-medium transition-all duration-200 opacity-0">Empresas</span>
            </a>
            <a href="{{ route('equipment') }}" title="Equipos" class="relative w-full px-3 py-3 flex items-center space-x-4 justify-start text-gray-500 rounded-lg group @if(request()->routeIs('equipment')) bg-blue-300 text-white @endif" onclick="highlightSidebarItem(this)">
                <i class="fas fa-cogs"></i>
                <span class="font-medium transition-all duration-200 opacity-0">Equipos</span>
            </a>
            <a href="{{ route('user') }}" title="Usuarios" class="relative w-full px-3 py-3 flex items-center space-x-4 justify-start text-gray-500 rounded-lg group @if(request()->routeIs('user')) bg-blue-300 text-white @endif" onclick="highlightSidebarItem(this)">
                <i class="fas fa-users"></i>
                <span class="font-medium transition-all duration-200 opacity-0">Usuarios</span>
            </a>
            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                <a title="Cerrar sesión" id="logout" class="relative w-full px-3 py-3 flex items-center space-x-4 justify-start text-gray-500 rounded-lg group" href="{{ route('logoutUser') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="fas fa-sign-out-alt"></i>
                    <span class="font-medium transition-all duration-200 opacity-0">Cerrar sesión</span>
                </a>
                <form id="logout-form" action="{{ route('logoutUser') }}" method="POST" class="hidden">
                    @csrf
                </form>
            </div>
        </div>
    </div>
    
    @endauth

    @guest
        <livewire:user-login.user-login />
    @else
    <div class="ml-16 bg-gray-100 h-screen fixed w-full lg:w-4/4 transition-all duration-200 ease-in-out">
        <main class="p-4">
            {{$slot}}
        </main>
    </div>
    @endguest
</div>

<script>
    function expandSidebar() {
        const sidebar = document.getElementById('sidebar');
        const mainContent = document.querySelector('.ml-16');

        if (sidebar.style.width === '16rem') {
            sidebar.style.width = '4rem';
            mainContent.style.marginLeft = '4rem';
            sidebar.classList.remove('text-left', 'px-6');
            sidebar.classList.add('text-center', 'px-0');
        } else {
            sidebar.style.width = '16rem';
            mainContent.style.marginLeft = '16rem';
            sidebar.classList.add('text-left', 'px-6');
            sidebar.classList.remove('text-center', 'px-0');
        }

        const labels = sidebar.querySelectorAll('span');
        labels.forEach(label => label.classList.toggle('opacity-0'));
    }

    function highlightSidebarItem(element) {
        const links = document.querySelectorAll("#sidebar a");
        links.forEach(link => {
            link.classList.remove('bg-blue-300', 'text-white');
            link.querySelector('i').classList.remove('text-white');
        });
        element.classList.add('bg-blue-300', 'text-white');
        element.querySelector('i').classList.add('text-white');
    }

</script>

@livewireScripts
</body>
</html>
