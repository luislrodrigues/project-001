<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Scripts -->
    <script type="text/javascript" src="{{ asset('js/alert/alert.js') }}"></script>

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
<body class="font-sans antialiased bg-gradient-to-br from-gray-50 via-blue-50 to-indigo-50">
    @auth
    <!-- Sidebar -->
    <aside id="sidebar" class="fixed left-0 top-0 h-screen sidebar-gradient shadow-2xl z-40 transition-all duration-300 ease-in-out" style="width: var(--sidebar-width);">
        <!-- Logo and Toggle -->
        <div class="flex items-center justify-between px-6 py-5 border-b border-blue-400/30">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm">
                    <i class="fas fa-cog text-white text-xl"></i>
                </div>
                <span id="logo-text" class="text-white font-bold text-xl transition-opacity duration-300">3Sanchez</span>
            </div>
            <button id="sidebar-toggle" onclick="toggleSidebar()" class="text-white hover:bg-white/20 rounded-lg p-2 transition-colors">
                <i class="fas fa-bars text-lg"></i>
            </button>
        </div>

        <!-- User Profile Section -->
        <div id="user-profile-section" class="px-4 py-4 border-b border-blue-400/30">
            <a href="{{ url('/profile') }}" class="flex items-center space-x-3 bg-white/10 backdrop-blur-sm rounded-xl p-3 hover:bg-white/15 transition-colors cursor-pointer">
                <div class="w-10 h-10 bg-gradient-to-br from-yellow-400 to-orange-500 rounded-full flex items-center justify-center text-white font-semibold text-sm shadow-lg">
                    {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                </div>
                <div id="user-info" class="flex-1 min-w-0 transition-opacity duration-300">
                    <p class="text-white font-semibold text-sm truncate">{{ auth()->user()->name }}</p>
                    <p class="text-blue-100 text-xs truncate">{{ auth()->user()->email }}</p>
                </div>
                <i class="fas fa-chevron-right text-blue-200 text-xs transition-opacity duration-300"></i>
            </a>
        </div>

        <!-- Navigation -->
        <nav class="px-4 py-4 space-y-2 overflow-y-auto" style="max-height: calc(100vh - 200px);">
            <a href="{{ url('/') }}" class="nav-item flex items-center space-x-3 px-4 py-3 text-white rounded-xl transition-all duration-200 nav-item-hover"
               title="Inicio">
                <i class="fas fa-home text-lg w-5"></i>
                <span id="nav-text-home" class="font-medium transition-opacity duration-300">Inicio</span>
            </a>

            <a href="{{ url('/reports') }}" class="nav-item flex items-center space-x-3 px-4 py-3 text-white rounded-xl transition-all duration-200 nav-item-hover"
               title="Informes">
                <i class="fas fa-file-alt text-lg w-5"></i>
                <span id="nav-text-report" class="font-medium transition-opacity duration-300">Informes</span>
            </a>

            <a href="{{ url('/components') }}" class="nav-item flex items-center space-x-3 px-4 py-3 text-white rounded-xl transition-all duration-200 nav-item-hover"
               title="Componentes">
                <i class="fas fa-cubes text-lg w-5"></i>
                <span id="nav-text-component" class="font-medium transition-opacity duration-300">Componentes</span>
            </a>

            <a href="{{ url('/component-types') }}" class="nav-item flex items-center space-x-3 px-4 py-3 text-white rounded-xl transition-all duration-200 nav-item-hover"
               title="Tipos de Componentes">
                <i class="fas fa-tags text-lg w-5"></i>
                <span id="nav-text-componentType" class="font-medium transition-opacity duration-300">Tipos de Componentes</span>
            </a>

            <a href="{{ url('/equipment') }}" class="nav-item flex items-center space-x-3 px-4 py-3 text-white rounded-xl transition-all duration-200 nav-item-hover"
               title="Equipos">
                <i class="fas fa-toolbox text-lg w-5"></i>
                <span id="nav-text-equipment" class="font-medium transition-opacity duration-300">Equipos</span>
            </a>

            <a href="{{ url('/companies') }}" class="nav-item flex items-center space-x-3 px-4 py-3 text-white rounded-xl transition-all duration-200 nav-item-hover"
               title="Empresas">
                <i class="fas fa-building text-lg w-5"></i>
                <span id="nav-text-company" class="font-medium transition-opacity duration-300">Empresas</span>
            </a>

            @can('consult user')
            <a href="{{ url('/users') }}"
               class="nav-item flex items-center space-x-3 px-4 py-3 text-white rounded-xl transition-all duration-200 @if(request()->routeIs('user')) nav-item-active @else nav-item-hover @endif"
               title="Usuarios">
                <i class="fas fa-users text-lg w-5"></i>
                <span id="nav-text-user" class="font-medium transition-opacity duration-300">Usuarios</span>
            </a>
            @endcan
        </nav>

        <!-- Logout Button -->
        <div class="absolute bottom-0 left-0 right-0 px-4 py-4 border-t border-blue-400/30">
            <a href="{{ route('logout') }}"
               onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
               class="nav-item flex items-center space-x-3 px-4 py-3 text-white rounded-xl bg-red-500/20 hover:bg-red-500/30 transition-all duration-200"
               title="Cerrar Sesión">
                <i class="fas fa-sign-out-alt text-lg w-5"></i>
                <span id="nav-text-logout" class="font-medium transition-opacity duration-300">Cerrar Sesión</span>
            </a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                @csrf
            </form>
        </div>
    </aside>

    <!-- Main Content Area -->
    <div class="flex flex-col" style="margin-left: var(--sidebar-width); transition: margin-left 0.3s ease-in-out;">
        <!-- Top Header -->
        <header class="bg-white/80 backdrop-blur-md shadow-sm border-b border-gray-200/50 sticky top-0 z-30">
            <div class="flex items-center justify-between px-6 py-4">
                <div>
                    @php
                        $routeName = request()->route()->getName();
                        $titles = [
                            'home' => 'Dashboard',
                            'dashboard' => 'Dashboard',
                            'report' => 'Informes',
                            'component' => 'Componentes',
                            'componentType' => 'Tipos de Componentes',
                            'equipment' => 'Equipos',
                            'company' => 'Empresas',
                            'user' => 'Usuarios',
                            'profile' => 'Mi Perfil',
                        ];
                        $descriptions = [
                            'home' => 'Panel principal del sistema',
                            'dashboard' => 'Panel principal del sistema',
                            'report' => 'Gestiona y visualiza los informes del sistema',
                            'component' => 'Administra los componentes de los equipos',
                            'componentType' => 'Configura los tipos de componentes disponibles',
                            'equipment' => 'Gestiona el inventario de equipos',
                            'company' => 'Administra las empresas registradas',
                            'user' => 'Gestiona los usuarios del sistema',
                            'profile' => 'Administra tu información personal',
                        ];
                    @endphp
                    <h1 class="text-2xl font-bold text-gray-800">{{ $titles[$routeName] ?? 'Dashboard' }}</h1>
                    <p class="text-sm text-gray-500 mt-1">{{ $descriptions[$routeName] ?? 'Bienvenido de vuelta' }}</p>
                </div>
                <div class="flex items-center space-x-4">
                    <!-- Notifications -->
                    <button class="relative p-2 text-gray-600 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors">
                        <i class="fas fa-bell text-lg"></i>
                        <span class="absolute top-0 right-0 w-2 h-2 bg-red-500 rounded-full"></span>
                    </button>
                    <!-- Profile Quick Access -->
                    <a href="{{ url('/profile') }}" class="flex items-center space-x-3 px-4 py-2 bg-gradient-to-r from-blue-500 to-indigo-600 text-white rounded-lg hover:from-blue-600 hover:to-indigo-700 transition-all shadow-md hover:shadow-lg">
                        <div class="w-8 h-8 bg-white/20 rounded-full flex items-center justify-center text-xs font-semibold">
                            {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                        </div>
                        <span class="text-sm font-medium">Mi Perfil</span>
                    </a>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="flex-1 p-6 min-h-screen">
            {{ $slot }}
        </main>

        <!-- Footer -->
        <footer class="bg-white/60 backdrop-blur-sm border-t border-gray-200/50 py-4">
            <div class="px-6 flex items-center justify-between">
                <p class="text-sm text-gray-600">
                    © {{ date('Y') }} <span class="font-semibold text-blue-600">3Sanchez</span>. Todos los derechos reservados.
                </p>
                <div class="flex items-center space-x-4 text-sm text-gray-500">
                    <span class="flex items-center">
                        <i class="fas fa-shield-alt mr-2"></i>
                        Sistema Seguro
                    </span>
                </div>
            </div>
        </footer>
    </div>
    @endauth

    @guest
        <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-gray-50 via-blue-50 to-indigo-50">
            <div class="text-center">
                <p class="text-gray-600 mb-4">Debes iniciar sesión para continuar.</p>
                <a href="{{ url('/login') }}" class="inline-flex items-center px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">Ir a iniciar sesión</a>
            </div>
        </div>
    @endguest

    <script>
        if (typeof window.sidebarCollapsed === 'undefined') {
            window.sidebarCollapsed = false;
        }

        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.querySelector('div[style*="margin-left"]');
            const logoText = document.getElementById('logo-text');
            const userInfo = document.getElementById('user-info');
            const navTexts = document.querySelectorAll('[id^="nav-text-"]');
            const userProfileSection = document.getElementById('user-profile-section');

            sidebarCollapsed = !sidebarCollapsed;

            if (sidebarCollapsed) {
                sidebar.style.width = 'var(--sidebar-collapsed-width)';
                if (mainContent) mainContent.style.marginLeft = 'var(--sidebar-collapsed-width)';
                if (logoText) {
                    logoText.style.opacity = '0';
                    logoText.style.width = '0';
                    logoText.style.overflow = 'hidden';
                }
                if (userInfo) {
                    userInfo.style.opacity = '0';
                    userInfo.style.height = '0';
                    userInfo.style.overflow = 'hidden';
                }
                if (userProfileSection) userProfileSection.style.padding = '0.5rem';
                navTexts.forEach(text => {
                    text.style.opacity = '0';
                    text.style.width = '0';
                    text.style.overflow = 'hidden';
                });
            } else {
                sidebar.style.width = 'var(--sidebar-width)';
                if (mainContent) mainContent.style.marginLeft = 'var(--sidebar-width)';
                if (logoText) {
                    logoText.style.opacity = '1';
                    logoText.style.width = 'auto';
                    logoText.style.overflow = 'visible';
                }
                if (userInfo) {
                    userInfo.style.opacity = '1';
                    userInfo.style.height = 'auto';
                    userInfo.style.overflow = 'visible';
                }
                if (userProfileSection) userProfileSection.style.padding = '1rem';
                navTexts.forEach(text => {
                    text.style.opacity = '1';
                    text.style.width = 'auto';
                    text.style.overflow = 'visible';
                });
            }
        }

        // Highlight active nav item
        function highlightActiveNavItem() {
            const currentPath = window.location.pathname;
            const navItems = document.querySelectorAll('.nav-item');
            navItems.forEach(item => {
                const href = item.getAttribute('href');
                if (href && (currentPath === href || currentPath.startsWith(href + '/'))) {
                    item.classList.add('nav-item-active');
                } else {
                    item.classList.remove('nav-item-active');
                }
            });
        }

        document.addEventListener('DOMContentLoaded', highlightActiveNavItem);
        window.addEventListener('popstate', highlightActiveNavItem);

        // Reinicializar botones de modales (sin Livewire)
        function reinitializeModals() {
            // Reinicializar botones de abrir modal
            const openModalButtons = document.querySelectorAll('[id^="openModalButton"]');
            openModalButtons.forEach(button => {
                // Remover atributo de listener previo si existe
                if (button.hasAttribute('data-listener-attached')) {
                    return; // Ya tiene listener, no duplicar
                }

                button.setAttribute('data-listener-attached', 'true');
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    const modalId = this.getAttribute('data-modal-id') || 'modalCreate';
                    const modal = document.getElementById(modalId);
                    if (modal) {
                        modal.classList.remove('hidden');
                    }
                });
            });

            // Reinicializar botones de cerrar modal
            const closeButtons = document.querySelectorAll('[id^="closeModalButton"], [id^="closeModalShowButton"]');
            closeButtons.forEach(button => {
                if (button.hasAttribute('data-close-listener-attached')) {
                    return; // Ya tiene listener
                }

                button.setAttribute('data-close-listener-attached', 'true');

                // Crear una copia del botón para remover listeners anteriores
                const newButton = button.cloneNode(true);
                button.parentNode.replaceChild(newButton, button);

                // Agregar nuevo listener
                newButton.addEventListener('click', function(e) {
                    e.preventDefault();
                    // Intentar encontrar el modal padre (modalCreate o modalShow)
                    let modal = null;
                    const modalId = this.getAttribute('data-modal-id');
                    if (modalId) {
                        modal = document.getElementById(modalId);
                    } else {
                        // Buscar el modal más cercano
                        const parent = this.closest('[id^="modal"]');
                        if (parent && parent.id) {
                            modal = document.getElementById(parent.id);
                        } else {
                            // Buscar por IDs comunes
                            modal = document.getElementById('modalCreate') ||
                                   document.getElementById('modalShow') ||
                                   document.getElementById('modalEdit');
                        }
                    }
                    if (modal) {
                        modal.classList.add('hidden');
                    }
                });
            });

            // Reinicializar overlays de modales (clics fuera del modal)
            // Buscar overlays que tienen onclick inline (estos necesitan ser actualizados)
            const modals = document.querySelectorAll('[id^="modal"]');
            modals.forEach(modal => {
                // Buscar el overlay dentro del modal
                const overlay = modal.querySelector('div[class*="fixed"][class*="inset-0"]:first-child');
                if (overlay) {
                    // Si ya tiene un listener, no duplicar
                    if (overlay.hasAttribute('data-overlay-listener-attached')) {
                        return;
                    }

                    overlay.setAttribute('data-overlay-listener-attached', 'true');

                    // Remover onclick inline si existe y reemplazarlo con listener
                    if (overlay.hasAttribute('onclick')) {
                        overlay.removeAttribute('onclick');
                    }

                    overlay.addEventListener('click', function(e) {
                        // Solo cerrar si el clic fue directamente en el overlay (no en el contenido)
                        if (e.target === overlay) {
                            const parentModal = overlay.closest('[id^="modal"]');
                            if (parentModal) {
                                parentModal.classList.add('hidden');
                            }
                        }
                    });
                }
            });

            // Reinicializar listeners de Livewire si están disponibles
            if (typeof Livewire !== 'undefined' && window.Livewire) {
                // Los listeners globales deberían persistir, pero verificamos eventos comunes
                // Esto se hace para asegurar que funcionen después de la navegación
            }
        }

        // Función para inicializar listeners de Livewire
        function initializeLivewireEventListeners() {
            if (typeof Livewire === 'undefined') {
                return;
            }

            // Estos listeners globales deberían funcionar en todas las páginas
            // Solo los registramos una vez si no existen
            if (!window.livewireListenersInitialized) {
                window.livewireListenersInitialized = true;

                // Listener global para abrir modales (común a todos los componentes)
                Livewire.on('open-modal', (event) => {
                    if (typeof event === 'string') {
                        const modalId = event;
                        const modal = document.getElementById(`modal-${modalId}`) || document.getElementById(modalId);
                        if (modal) {
                            modal.classList.remove('hidden');
                        }
                    } else if (event && event.detail) {
                        const modalId = event.detail.id || event.detail;
                        const modal = document.getElementById(`modal-${modalId}`) || document.getElementById(modalId);
                        if (modal) {
                            modal.classList.remove('hidden');
                        }
                    }
                });

                // Listener global para cerrar modales
                Livewire.on('close-modal', (event) => {
                    let modalId = null;
                    if (typeof event === 'string') {
                        modalId = event;
                    } else if (event && event.detail) {
                        modalId = event.detail.id || event.detail;
                    }

                    if (modalId) {
                        const modal = document.getElementById(`modal-${modalId}`) || document.getElementById(modalId);
                        if (modal) {
                            modal.classList.add('hidden');
                        }
                    } else {
                        // Cerrar todos los modales visibles
                        const modals = document.querySelectorAll('[id^="modal"]');
                        modals.forEach(modal => {
                            if (!modal.classList.contains('hidden')) {
                                modal.classList.add('hidden');
                            }
                        });
                    }
                });

                // Listeners específicos para cada componente
                const closeEvents = [
                    'close-modal-equipment',
                    'close-modal-report',
                    'close-modal-company',
                    'close-modal-user',
                    'close-modal-component',
                    'close-modal-type-component'
                ];

                closeEvents.forEach(eventName => {
                    Livewire.on(eventName, () => {
                        // Buscar el modal correspondiente
                        let modal = document.getElementById('modalCreate');
                        if (!modal) {
                            modal = document.querySelector('[id^="modal"]:not(.hidden)');
                        }
                        if (modal) {
                            modal.classList.add('hidden');
                        }
                    });
                });

                // Listener para abrir modal de selección
                Livewire.on('open-modal-select', (field) => {
                    const fieldValue = typeof field === 'string' ? field : (field?.detail || field);
                    if (fieldValue && window.openModalSelect) {
                        window.openModalSelect(fieldValue);
                    }
                });

                // Listener para cerrar modal de selección
                Livewire.on('close-modal-select', (field) => {
                    const fieldValue = typeof field === 'string' ? field : (field?.detail || field);
                    if (fieldValue && window.closeModalSelect) {
                        window.closeModalSelect(fieldValue);
                    }
                });

                // Listeners para abrir modales de edición (todos los componentes)
                const openEditEvents = [
                    'open-modal-edit-equipment',
                    'open-modal-edit-report',
                    'open-modal-edit-company',
                    'open-modal-edit-user',
                    'open-modal-edit-component',
                    'open-modal-edit-type-component'
                ];

                openEditEvents.forEach(eventName => {
                    Livewire.on(eventName, () => {
                        const modal = document.getElementById('modalCreate');
                        if (modal) {
                            modal.classList.remove('hidden');
                        }
                    });
                });

                // Listeners para abrir modales de mostrar detalles
                const openShowEvents = [
                    'open-modal-show-equipment',
                    'open-modal-show-report',
                    'open-modal-show-component'
                ];

                openShowEvents.forEach(eventName => {
                    Livewire.on(eventName, () => {
                        const modal = document.getElementById('modalShow');
                        if (modal) {
                            modal.classList.remove('hidden');
                        }
                    });
                });
            }
        }

        // Escuchar cuando Livewire completa la navegación
        window.addEventListener('popstate', function() {
            // Delay para asegurar que el DOM está actualizado
            setTimeout(() => {
                highlightActiveNavItem();
                reinitializeModals();
                initializeLivewireEventListeners();
            }, 150);
        });

        // Inicializar en la carga inicial
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(() => {
                highlightActiveNavItem();
                reinitializeModals();
                reinitializeSelectModals(); // Reinicializar modales de selección
                reinitializeLivewireModalListeners(); // Reinicializar listeners de Livewire
                initializeLivewireEventListeners();
            }, 300);
        });

        // También inicializar cuando Livewire está listo
        document.addEventListener('livewire:initialized', function() {
            setTimeout(() => {
                reinitializeModals();
                reinitializeSelectModals(); // Reinicializar modales de selección
                reinitializeLivewireModalListeners(); // Reinicializar listeners de Livewire
                initializeLivewireEventListeners();
            }, 100);
        });

        // Hook de Livewire para reinicializar después de cada actualización
        if (typeof Livewire !== 'undefined') {
            document.addEventListener('livewire:init', () => {
                Livewire.hook('morph.updated', ({ el, component }) => {
                    // Reinicializar modales después de cada actualización del DOM
                    setTimeout(() => {
                        reinitializeModals();
                        reinitializeSelectModals(); // Reinicializar modales de selección
                        reinitializeLivewireModalListeners(); // Reinicializar listeners de Livewire
                    }, 50);
                });
            });
        }

        // Asegurar que closeModalSelect esté disponible globalmente después de la navegación
        window.closeModalSelect = function(field) {
            // Verificar si el clic fue en el input para evitar cerrar cuando se escribe
            const activeElement = document.activeElement;
            if (activeElement && activeElement.id === `input-value-new-${field}`) {
                return; // No cerrar si el input está activo
            }

            const container = document.getElementById(`modal-${field}-container`);
            if (container) {
                // Desmarcar como debe estar abierto
                container.removeAttribute('data-should-be-open');
                container.classList.add('hidden');
                const overlay = document.getElementById(`modal-${field}-overlay`);
                if (overlay) {
                    overlay.classList.add('hidden');
                }
            }

            // Resetear el formulario del modal si está abierto
            if (typeof Livewire !== 'undefined') {
                if (Livewire.dispatch) {
                    Livewire.dispatch('reset-modal-select', { field: field });
                } else if (window.Livewire && window.Livewire.dispatch) {
                    window.Livewire.dispatch('reset-modal-select', { field: field });
                }
            }
        };

        // Función para abrir el modal de selección
        window.openModalSelect = function(field) {
            const container = document.getElementById(`modal-${field}-container`);
            if (container) {
                // Marcar como debe estar abierto
                container.setAttribute('data-should-be-open', 'true');
                container.classList.remove('hidden');
                const overlay = document.getElementById(`modal-${field}-overlay`);
                if (overlay) {
                    overlay.classList.remove('hidden');
                }
            }
        };

        // Reinicializar modales de selección específicamente
        function reinitializeSelectModals() {
            // Buscar todos los modales de selección
            const selectModalContainers = document.querySelectorAll('[id^="modal-"][id$="-container"]');
            selectModalContainers.forEach(container => {
                const field = container.id.replace('modal-', '').replace('-container', '');

                // Reinicializar overlay
                const overlay = document.getElementById(`modal-${field}-overlay`);
                if (overlay) {
                    // Remover onclick inline si existe
                    if (overlay.hasAttribute('onclick')) {
                        overlay.removeAttribute('onclick');
                    }

                    // Asegurar que el listener esté adjunto
                    if (!overlay.hasAttribute('data-select-overlay-listener')) {
                        overlay.setAttribute('data-select-overlay-listener', 'true');
                        overlay.addEventListener('click', function(e) {
                            if (e.target === overlay) {
                                window.closeModalSelect(field);
                            }
                        });
                    }
                }

                // Reinicializar botones de cerrar dentro del modal
                const closeButtons = container.querySelectorAll('[onclick*="closeModalSelect"]');
                closeButtons.forEach(button => {
                    if (!button.hasAttribute('data-select-close-listener')) {
                        button.setAttribute('data-select-close-listener', 'true');

                        // Remover onclick inline
                        if (button.hasAttribute('onclick')) {
                            button.removeAttribute('onclick');
                        }

                        // Agregar listener
                        button.addEventListener('click', function(e) {
                            e.preventDefault();
                            e.stopPropagation();
                            window.closeModalSelect(field);
                        });
                    }
                });
            });
        }
    </script>

    @stack('scripts')
</body>
</html>
