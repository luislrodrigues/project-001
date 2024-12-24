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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Scripts -->
    <script type="text/javascript" src="{{ asset('js/alert/alert.js') }}"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Styles -->
    @livewireStyles
</head>
<body class="font-sans antialiased h-screen flex">
    @auth
    <div id="sidebar" class="w-16 bg-white h-full flex flex-col transition-all duration-200 ease-in-out">
        <div class="p-2 space-y-4 flex flex-col items-center">
            <button id="menu-button" class="focus:outline-none self-center" onclick="expandSidebar()">
                <i class="fas fa-bars text-cyan-500 text-lg text-black"></i>
            </button>
            <a href="{{ route('report') }}" title="Informes" class="relative w-full px-3 py-3 flex items-center space-x-4 justify-start text-gray-500 rounded-lg group @if(request()->routeIs('report')) bg-blue-300 text-white @endif" onclick="highlightSidebarItem(this)">
                <i class="fas fa-file-alt"></i>
                <span class="font-medium transition-all duration-200 opacity-0">Informes</span>
            </a>
            <a href="{{ route('component') }}" title="Componentes" class="relative w-full px-3 py-3 flex items-center space-x-4 justify-start text-gray-500 rounded-lg group @if(request()->routeIs('component')) bg-blue-300 text-white @endif" onclick="highlightSidebarItem(this)">
                <i class="fas fa-weight"></i>
                <span class="font-medium transition-all duration-200 opacity-0">Componentes</span>
            </a>
            <a href="{{ route('componentType') }}" title="Tipo de componentes" class="relative w-full px-3 py-3 flex items-center space-x-4 justify-start text-gray-500 rounded-lg group @if(request()->routeIs('componentType')) bg-blue-300 text-white @endif" onclick="highlightSidebarItem(this)">
                <i class="fas fa-snowplow"></i>
                <span class="font-medium transition-all duration-200 opacity-0">Tipo de Componentes</span>
            </a>
            <a href="{{ route('equipment') }}" title="Equipos" class="relative w-full px-3 py-3 flex items-center space-x-4 justify-start text-gray-500 rounded-lg group @if(request()->routeIs('equipment')) bg-blue-300 text-white @endif" onclick="highlightSidebarItem(this)">
                <i class="fas fa-cog"></i>
                <span class="font-medium transition-all duration-200 opacity-0">Equipos</span>
            </a>
            <a href="{{ route('company') }}" title="Empresas" class="relative w-full px-3 py-3 flex items-center space-x-4 justify-start text-gray-500 rounded-lg group @if(request()->routeIs('company')) bg-blue-300 text-white @endif" onclick="highlightSidebarItem(this)">
                <i class="fas fa-building"></i>
                <span class="font-medium transition-all duration-200 opacity-0">Empresas</span>
            </a>
            <a href="{{ route('user') }}" title="Usuarios" class="relative w-full px-3 py-3 flex items-center space-x-4 justify-start text-gray-500 rounded-lg group @if(request()->routeIs('user')) bg-blue-300 text-white @endif" onclick="highlightSidebarItem(this)">
                <i class="fas fa-users"></i>
                <span class="font-medium transition-all duration-200 opacity-0">Usuarios</span>
            </a>
            <div class="dropdown-menu dropdown-menu-end w-full" aria-labelledby="navbarDropdown">
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

    <div class="flex-1 flex flex-col">
        {{-- <nav class="bg-white border-b border-gray-300 w-full">
            <div class="flex justify-between items-center px-6 bg-blue-300" style="height: 60px">
                <!-- Removed the button from here -->
            </div>
        </nav> --}}
        <div id="main-content" class="flex-1 bg-gray-100 transition-all duration-200 ease-in-out">
            <main class="p-4">
                {{$slot}}
            </main>
        </div>
        <footer class="bg-light text-center text-lg-start w-full h-0">
            <div class="text-center p-3 bg-blue-300 ">
                <span class="text-gray-700">© 2024 3Sanchez</span>
            </div>
        </footer>
        
    </div>
    @endauth

    @guest

        <livewire:user-login.user-login />
    @endguest
    <script>
        function expandSidebar() {
            const sidebar = document.getElementById('sidebar');
            const menuButton = document.getElementById('menu-button');

            if (sidebar.style.width === '16rem') {
                sidebar.style.width = '4rem';
                menuButton.classList.remove('self-end');
                menuButton.classList.add('self-center');
            } else {
                sidebar.style.width = '16rem';
                menuButton.classList.remove('self-center');
                menuButton.classList.add('self-end');
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
    @stack('scripts')
</body>
</html>
