<!-- component -->
<div class="container mx-auto px-4 sm:px-8">
    <div wire:loading class="fixed top-0 left-0 w-screen h-screen flex items-center justify-center z-50 bg-black bg-opacity-30 backdrop-blur-sm">
        <div class="flex flex-col items-center justify-center h-full">
            <svg class="animate-spin h-16 w-16 text-blue-600 mr-3 mb-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="10" stroke-opacity="0.25" stroke-width="2" stroke="currentColor" fill="none"></circle>
                <path d="M12 2a10 10 0 00-9.5 7.5M12 2c2.5 0 5 1 7 2.5" stroke="currentColor" stroke-width="2" fill="none"></path>
            </svg>
            <p class="text-gray-700 font-medium">Cargando...</p>
        </div>
    </div>
    <div class="py-6">
        {{-- Header Section --}}
        <div class="mb-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
                <div class="mb-4 sm:mb-0">
                    <h2 class="text-3xl font-bold text-gray-800 dark:text-white flex items-center">
                        <i class="fas fa-users text-blue-600 mr-3"></i>
                        Usuarios
                    </h2>
                    <p class="text-gray-500 text-sm mt-1">Gestiona los usuarios del sistema</p>
                </div>
                @can('save user')
                <button id="openModalButton" 
                        class="flex items-center px-5 py-2.5 bg-blue-500 text-white rounded-lg hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors duration-200 shadow-sm font-medium">
                    <i class="fas fa-plus mr-2"></i>
                    Crear Usuario
                </button>
                @endcan
            </div>

            {{-- Search Section --}}
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                <div class="flex flex-col sm:flex-row gap-3">
                    <div class="flex-1">
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-filter mr-2 text-gray-400"></i>
                            Estado
                        </label>
                        <select id="status" 
                                class="block w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 px-4 py-2.5 transition-all duration-200"
                                wire:model.live='status'>
                            <option value="">Todos</option>
                            <option value="active">Activo</option>
                            <option value="inactive">Inactivo</option>
                        </select>
                    </div>
                    <div class="flex-1">
                        <label for="company_searc" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-building mr-2 text-gray-400"></i>
                            Empresa
                        </label>
                        <select id="status" 
                                class="block w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 px-4 py-2.5 transition-all duration-200"
                                wire:model.live='company_searc'>
                            <option value="">Todas</option>
                            @foreach($companies as $company)
                                <option value="{{ $company->id }}">{{ $company->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex-1">
                        <label for="search" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-search mr-2 text-gray-400"></i>
                            Buscar usuario
                        </label>
                        <div class="relative">
                            <input id="search" 
                                   type="text" 
                                   placeholder="Buscar por nombre o email..." 
                                   class="block w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 pl-10 pr-4 py-2.5 transition-all duration-200"
                                   wire:model.live.debounce.300ms='search'>
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-search text-gray-400"></i>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-end">
                        <button class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-400 focus:ring-offset-2 transition-colors duration-200 font-medium" 
                                wire:click="$refresh">
                            <i class="fas fa-redo mr-2"></i>
                            Actualizar
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Table Section --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            @if($users->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gradient-to-r from-blue-50 to-blue-100">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-blue-900 uppercase tracking-wider">
                                    <div class="flex items-center">
                                        <i class="fas fa-user mr-2"></i>
                                        Nombre
                                    </div>
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-blue-900 uppercase tracking-wider">
                                    <div class="flex items-center">
                                        <i class="fas fa-envelope mr-2"></i>
                                        Correo
                                    </div>
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-blue-900 uppercase tracking-wider">
                                    <div class="flex items-center">
                                        <i class="fas fa-building mr-2"></i>
                                        Empresa
                                    </div>
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-blue-900 uppercase tracking-wider">
                                    <div class="flex items-center">
                                        <i class="fas fa-briefcase mr-2"></i>
                                        Cargo
                                    </div>
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-blue-900 uppercase tracking-wider">
                                    <div class="flex items-center">
                                        <i class="fas fa-info-circle mr-2"></i>
                                        Estado
                                    </div>
                                </th>
                                @can('actions user')
                                <th class="px-6 py-4 text-right text-xs font-semibold text-blue-900 uppercase tracking-wider">
                                    <div class="flex items-center justify-end">
                                        <i class="fas fa-cog mr-2"></i>
                                        Acciones
                                    </div>
                                </th>
                                @endcan
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($users as $user)
                            <tr class="hover:bg-blue-50 transition-colors duration-150">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10 bg-blue-100 rounded-full flex items-center justify-center mr-4">
                                            <i class="fas fa-user text-blue-600"></i>
                                        </div>
                                        <div class="text-sm font-semibold text-gray-900">
                                            {{ $user->name }}
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $user->email }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $user->company->name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        {{ $user->getRoleNames()->first() ?? 'Sin rol' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($user->status == 'active')
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 border border-green-300">
                                            <i class="fas fa-check-circle mr-1"></i>
                                            Activo
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800 border border-red-300">
                                            <i class="fas fa-times-circle mr-1"></i>
                                            Inactivo
                                        </span>
                                    @endif
                                </td>
                                @can('actions user')
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex items-center justify-end space-x-2">
                                        <button wire:click="editUser({{ $user->id }})"
                                                class="inline-flex items-center px-3 py-2 bg-blue-50 hover:bg-blue-100 text-blue-600 rounded-lg transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-1"
                                                title="Editar">
                                            <i class="fas fa-edit text-sm"></i>
                                            <span class="ml-1.5 text-xs font-medium">Editar</span>
                                        </button>
                                        <button wire:click.prevent="toggleUserStatus({{ $user->id }})" 
                                                wire:loading.attr="disabled"
                                                class="inline-flex items-center px-3 py-2 {{ $user->status == 'active' ? 'bg-green-50 hover:bg-green-100 text-green-600' : 'bg-red-50 hover:bg-red-100 text-red-600' }} rounded-lg transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-offset-1"
                                                title="{{ $user->status == 'active' ? 'Desactivar' : 'Activar' }}">
                                            @if($user->status == 'active')
                                                <i class="fas fa-toggle-on text-sm"></i>
                                            @else
                                                <i class="fas fa-toggle-off text-sm"></i>
                                            @endif
                                            <span class="ml-1.5 text-xs font-medium">{{ $user->status == 'active' ? 'Activo' : 'Inactivo' }}</span>
                                        </button>
                                    </div>
                                </td>
                                @endcan
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                {{-- Pagination --}}
                @if($users->hasPages())
                <div class="bg-gradient-to-r from-gray-50 to-blue-50 px-6 py-4 border-t border-gray-200">
                    <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                        <div class="text-sm text-gray-700 flex items-center">
                            <i class="fas fa-info-circle text-blue-600 mr-2"></i>
                            Mostrando 
                            <span class="font-semibold text-blue-600 mx-1">{{ $users->firstItem() }}</span>
                            a 
                            <span class="font-semibold text-blue-600 mx-1">{{ $users->lastItem() }}</span>
                            de 
                            <span class="font-semibold text-blue-600 mx-1">{{ $users->total() }}</span>
                            resultados
                        </div>
                        <div class="flex items-center space-x-1">
                            @if($users->onFirstPage())
                                <button disabled class="px-3 py-2 rounded-lg bg-gray-100 text-gray-400 cursor-not-allowed border border-gray-200">
                                    <i class="fas fa-chevron-left"></i>
                                </button>
                            @else
                                <button wire:click="previousPage" wire:loading.attr="disabled" 
                                        class="px-3 py-2 rounded-lg bg-white text-gray-700 hover:bg-blue-500 hover:text-white border border-gray-300 hover:border-blue-500 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-1">
                                    <i class="fas fa-chevron-left"></i>
                                </button>
                            @endif

                            @php
                                $currentPage = $users->currentPage();
                                $lastPage = $users->lastPage();
                                $showPages = [];
                                if ($lastPage > 0) $showPages[] = 1;
                                for ($i = max(2, $currentPage - 2); $i <= min($lastPage - 1, $currentPage + 2); $i++) {
                                    if (!in_array($i, $showPages)) $showPages[] = $i;
                                }
                                if ($lastPage > 1 && !in_array($lastPage, $showPages)) $showPages[] = $lastPage;
                                sort($showPages);
                            @endphp
                            
                            @foreach($showPages as $index => $page)
                                @if($index > 0 && $showPages[$index - 1] < $page - 1)
                                    <span class="px-2 py-2 text-gray-400">...</span>
                                @endif
                                @if($page == $currentPage)
                                    <button disabled class="px-4 py-2 rounded-lg bg-blue-500 text-white font-semibold border border-blue-500 shadow-sm">
                                        {{ $page }}
                                    </button>
                                @else
                                    <button wire:click="gotoPage({{ $page }})" wire:loading.attr="disabled"
                                            class="px-4 py-2 rounded-lg bg-white text-gray-700 hover:bg-blue-50 hover:text-blue-600 border border-gray-300 hover:border-blue-300 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-1 font-medium">
                                        {{ $page }}
                                    </button>
                                @endif
                            @endforeach

                            @if($users->hasMorePages())
                                <button wire:click="nextPage" wire:loading.attr="disabled"
                                        class="px-3 py-2 rounded-lg bg-white text-gray-700 hover:bg-blue-500 hover:text-white border border-gray-300 hover:border-blue-500 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-1">
                                    <i class="fas fa-chevron-right"></i>
                                </button>
                            @else
                                <button disabled class="px-3 py-2 rounded-lg bg-gray-100 text-gray-400 cursor-not-allowed border border-gray-200">
                                    <i class="fas fa-chevron-right"></i>
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
                @endif
            @else
                <div class="text-center py-16 px-6">
                    <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-gray-100 mb-4">
                        <i class="fas fa-users text-3xl text-gray-400"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">
                        @if($search || $status || $company_searc)
                            No se encontraron resultados
                        @else
                            No hay usuarios
                        @endif
                    </h3>
                    <p class="text-gray-500 mb-6 max-w-sm mx-auto">
                        @if($search || $status || $company_searc)
                            Intenta buscar con otros términos o crea un nuevo usuario.
                        @else
                            Comienza creando tu primer usuario para gestionar el sistema.
                        @endif
                    </p>
                    @if(!$search && !$status && !$company_searc && auth()->user()->can('save user'))
                    <button id="openModalButton" 
                            class="inline-flex items-center px-5 py-2.5 bg-blue-500 text-white rounded-lg hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors duration-200 shadow-sm font-medium">
                        <i class="fas fa-plus mr-2"></i>
                        Crear Primer Usuario
                    </button>
                    @endif
                </div>
            @endif
        </div>
    @include('livewire.user.modal-create')
    </div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const openModalButton = document.getElementById('openModalButton');
        const modalCreate = document.getElementById('modalCreate');
        
        if (openModalButton && modalCreate) {
            openModalButton.addEventListener('click', function() {
                modalCreate.classList.remove('hidden');
            });
        }
    });
</script>

@push('scripts')
<script>
    document.addEventListener('livewire:initialized', function () {
        Livewire.on('alertSuccess', (message) => {
            if (typeof alertSuccess === 'function') {
                alertSuccess(message);
            }
        });
        Livewire.on('alertFailed', (message) => {
            if (typeof alertFailed === 'function') {
                alertFailed(message);
            }
        });
        Livewire.on('open-modal-edit-user', () => {
            const modalCreate = document.getElementById('modalCreate');
            if (modalCreate) {
                modalCreate.classList.remove('hidden');
            }
        });
    });
</script>
@endpush
</div>
