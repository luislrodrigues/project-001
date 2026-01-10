<div class="space-y-6">
    <!-- Dashboard Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Informes Card -->
        @can('consult report')
        <a href="{{ route('report') }}" wire:navigate class="group">
            <div class="bg-white rounded-xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 border border-gray-100 overflow-hidden">
                <div class="bg-gradient-to-r from-blue-500 to-blue-600 h-2"></div>
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-14 h-14 bg-blue-100 rounded-xl flex items-center justify-center group-hover:bg-blue-500 transition-colors duration-300">
                            <i class="fas fa-file-alt text-2xl text-blue-600 group-hover:text-white transition-colors duration-300"></i>
                        </div>
                        <i class="fas fa-arrow-right text-gray-400 group-hover:text-blue-500 transform group-hover:translate-x-2 transition-all duration-300"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">Informes</h3>
                    <p class="text-gray-600 text-sm mb-4">Gestiona y visualiza los informes de diagnósticos del sistema</p>
                    <div class="flex items-center text-sm text-blue-600 font-semibold">
                        <span>Acceder</span>
                    </div>
                </div>
            </div>
        </a>
        @endcan

        <!-- Componentes Card -->
        <a href="{{ route('component') }}" wire:navigate class="group">
            <div class="bg-white rounded-xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 border border-gray-100 overflow-hidden">
                <div class="bg-gradient-to-r from-purple-500 to-purple-600 h-2"></div>
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-14 h-14 bg-purple-100 rounded-xl flex items-center justify-center group-hover:bg-purple-500 transition-colors duration-300">
                            <i class="fas fa-cubes text-2xl text-purple-600 group-hover:text-white transition-colors duration-300"></i>
                        </div>
                        <i class="fas fa-arrow-right text-gray-400 group-hover:text-purple-500 transform group-hover:translate-x-2 transition-all duration-300"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">Componentes</h3>
                    <p class="text-gray-600 text-sm mb-4">Administra los componentes de los equipos del inventario</p>
                    <div class="flex items-center text-sm text-purple-600 font-semibold">
                        <span>Acceder</span>
                    </div>
                </div>
            </div>
        </a>

        <!-- Tipos de Componentes Card -->
        <a href="{{ route('componentType') }}" wire:navigate class="group">
            <div class="bg-white rounded-xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 border border-gray-100 overflow-hidden">
                <div class="bg-gradient-to-r from-indigo-500 to-indigo-600 h-2"></div>
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-14 h-14 bg-indigo-100 rounded-xl flex items-center justify-center group-hover:bg-indigo-500 transition-colors duration-300">
                            <i class="fas fa-tags text-2xl text-indigo-600 group-hover:text-white transition-colors duration-300"></i>
                        </div>
                        <i class="fas fa-arrow-right text-gray-400 group-hover:text-indigo-500 transform group-hover:translate-x-2 transition-all duration-300"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">Tipos de Componentes</h3>
                    <p class="text-gray-600 text-sm mb-4">Configura los tipos de componentes disponibles en el sistema</p>
                    <div class="flex items-center text-sm text-indigo-600 font-semibold">
                        <span>Acceder</span>
                    </div>
                </div>
            </div>
        </a>

        <!-- Equipos Card -->
        @can('consult equipment')
        <a href="{{ route('equipment') }}" wire:navigate class="group">
            <div class="bg-white rounded-xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 border border-gray-100 overflow-hidden">
                <div class="bg-gradient-to-r from-green-500 to-green-600 h-2"></div>
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-14 h-14 bg-green-100 rounded-xl flex items-center justify-center group-hover:bg-green-500 transition-colors duration-300">
                            <i class="fas fa-toolbox text-2xl text-green-600 group-hover:text-white transition-colors duration-300"></i>
                        </div>
                        <i class="fas fa-arrow-right text-gray-400 group-hover:text-green-500 transform group-hover:translate-x-2 transition-all duration-300"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">Equipos</h3>
                    <p class="text-gray-600 text-sm mb-4">Gestiona el inventario completo de equipos y sus características</p>
                    <div class="flex items-center text-sm text-green-600 font-semibold">
                        <span>Acceder</span>
                    </div>
                </div>
            </div>
        </a>
        @endcan

        <!-- Empresas Card -->
        @can('consult company')
        <a href="{{ route('company') }}" wire:navigate class="group">
            <div class="bg-white rounded-xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 border border-gray-100 overflow-hidden">
                <div class="bg-gradient-to-r from-orange-500 to-orange-600 h-2"></div>
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-14 h-14 bg-orange-100 rounded-xl flex items-center justify-center group-hover:bg-orange-500 transition-colors duration-300">
                            <i class="fas fa-building text-2xl text-orange-600 group-hover:text-white transition-colors duration-300"></i>
                        </div>
                        <i class="fas fa-arrow-right text-gray-400 group-hover:text-orange-500 transform group-hover:translate-x-2 transition-all duration-300"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">Empresas</h3>
                    <p class="text-gray-600 text-sm mb-4">Administra las empresas y plantas registradas en el sistema</p>
                    <div class="flex items-center text-sm text-orange-600 font-semibold">
                        <span>Acceder</span>
                    </div>
                </div>
            </div>
        </a>
        @endcan

        <!-- Usuarios Card -->
        @can('consult user')
        <a href="{{ route('user') }}" wire:navigate class="group">
            <div class="bg-white rounded-xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 border border-gray-100 overflow-hidden">
                <div class="bg-gradient-to-r from-red-500 to-red-600 h-2"></div>
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-14 h-14 bg-red-100 rounded-xl flex items-center justify-center group-hover:bg-red-500 transition-colors duration-300">
                            <i class="fas fa-users text-2xl text-red-600 group-hover:text-white transition-colors duration-300"></i>
                        </div>
                        <i class="fas fa-arrow-right text-gray-400 group-hover:text-red-500 transform group-hover:translate-x-2 transition-all duration-300"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">Usuarios</h3>
                    <p class="text-gray-600 text-sm mb-4">Gestiona los usuarios del sistema, roles y permisos</p>
                    <div class="flex items-center text-sm text-red-600 font-semibold">
                        <span>Acceder</span>
                    </div>
                </div>
            </div>
        </a>
        @endcan
    </div>
</div>
