<!-- component -->
<div class="container mx-auto px-4 sm:px-8">
    {{-- Incluir el gestor de opciones global --}}
    @livewire('field-options-manager.field-options-manager')
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
                        <i class="fas fa-snowplow text-blue-600 mr-3"></i>
                        Tipo de Componentes
                    </h2>
                    <p class="text-gray-500 text-sm mt-1">Gestiona los tipos de componentes del sistema</p>
                </div>
                @can('save equipment')
                <button id="openModalButton" 
                        class="flex items-center px-5 py-2.5 bg-blue-500 text-white rounded-lg hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors duration-200 shadow-sm font-medium">
                    <i class="fas fa-plus mr-2"></i>
                    Crear Tipo de Componente
                </button>
                @endcan
            </div>

            {{-- Search Section --}}
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                <div class="flex flex-col sm:flex-row gap-3">
                    <div class="flex-1">
                        <label for="search" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-search mr-2 text-gray-400"></i>
                            Buscar tipo de componente
                        </label>
                        <div class="relative">
                            <input id="search" 
                                   type="text" 
                                   placeholder="Buscar por nombre..." 
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
            @if($type_components->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gradient-to-r from-blue-50 to-blue-100">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-blue-900 uppercase tracking-wider">
                                    <div class="flex items-center">
                                        <i class="fas fa-tag mr-2"></i>
                                        Nombre del Tipo
                                    </div>
                                </th>
                                @can('actions equipment')
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
                            @foreach($type_components as $type_component)
                            <tr class="hover:bg-blue-50 transition-colors duration-150">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10 bg-blue-100 rounded-lg flex items-center justify-center mr-4">
                                            <i class="fas fa-snowplow text-blue-600"></i>
                                        </div>
                                        <div>
                                            <div class="text-sm font-semibold text-gray-900">
                                                {{ $type_component->name }}
                                            </div>
                                            <div class="text-xs text-gray-500">
                                                Tipo de componente
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                @can('actions equipment')
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex items-center justify-end space-x-2">
                                        <button wire:click="OpenModalEdit({{ $type_component->id }})"
                                                class="inline-flex items-center px-3 py-2 bg-blue-50 hover:bg-blue-100 text-blue-600 rounded-lg transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-1"
                                                title="Editar">
                                            <i class="fas fa-edit text-sm"></i>
                                            <span class="ml-1.5 text-xs font-medium">Editar</span>
                                        </button>
                                        <button onclick="deleted('component-type.component-type', 'destroy',{{ $type_component->id }})" 
                                                wire:loading.attr="disabled"
                                                class="inline-flex items-center px-3 py-2 bg-red-50 hover:bg-red-100 text-red-600 rounded-lg transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-1"
                                                title="Eliminar">
                                            <i class="fas fa-trash text-sm"></i>
                                            <span class="ml-1.5 text-xs font-medium">Eliminar</span>
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
                @if($type_components->hasPages())
                <div class="bg-gradient-to-r from-gray-50 to-blue-50 px-6 py-4 border-t border-gray-200">
                    <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                        <div class="text-sm text-gray-700 flex items-center">
                            <i class="fas fa-info-circle text-blue-600 mr-2"></i>
                            Mostrando 
                            <span class="font-semibold text-blue-600 mx-1">{{ $type_components->firstItem() }}</span>
                            a 
                            <span class="font-semibold text-blue-600 mx-1">{{ $type_components->lastItem() }}</span>
                            de 
                            <span class="font-semibold text-blue-600 mx-1">{{ $type_components->total() }}</span>
                            resultados
                        </div>
                        <div class="flex items-center space-x-1">
                            {{-- Previous Button --}}
                            @if($type_components->onFirstPage())
                                <button disabled class="px-3 py-2 rounded-lg bg-gray-100 text-gray-400 cursor-not-allowed border border-gray-200">
                                    <i class="fas fa-chevron-left"></i>
                                </button>
                            @else
                                <button wire:click="previousPage" wire:loading.attr="disabled" 
                                        class="px-3 py-2 rounded-lg bg-white text-gray-700 hover:bg-blue-500 hover:text-white border border-gray-300 hover:border-blue-500 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-1">
                                    <i class="fas fa-chevron-left"></i>
                                </button>
                            @endif

                            {{-- Page Numbers --}}
                            @php
                                $currentPage = $type_components->currentPage();
                                $lastPage = $type_components->lastPage();
                                $showPages = [];
                                
                                // Always show first page
                                if ($lastPage > 0) {
                                    $showPages[] = 1;
                                }
                                
                                // Show pages around current page
                                for ($i = max(2, $currentPage - 2); $i <= min($lastPage - 1, $currentPage + 2); $i++) {
                                    if (!in_array($i, $showPages)) {
                                        $showPages[] = $i;
                                    }
                                }
                                
                                // Always show last page
                                if ($lastPage > 1 && !in_array($lastPage, $showPages)) {
                                    $showPages[] = $lastPage;
                                }
                                
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

                            {{-- Next Button --}}
                            @if($type_components->hasMorePages())
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
                {{-- Empty State --}}
                <div class="text-center py-16 px-6">
                    <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-gray-100 mb-4">
                        <i class="fas fa-snowplow text-3xl text-gray-400"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">
                        @if($search)
                            No se encontraron resultados
                        @else
                            No hay tipos de componentes
                        @endif
                    </h3>
                    <p class="text-gray-500 mb-6 max-w-sm mx-auto">
                        @if($search)
                            Intenta buscar con otros términos o crea un nuevo tipo de componente.
                        @else
                            Comienza creando tu primer tipo de componente para organizar mejor tu inventario.
                        @endif
                    </p>
                    @if(!$search && auth()->user()->can('save equipment'))
                    <button id="openModalButton" 
                            class="inline-flex items-center px-5 py-2.5 bg-blue-500 text-white rounded-lg hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors duration-200 shadow-sm font-medium">
                        <i class="fas fa-plus mr-2"></i>
                        Crear Primer Tipo de Componente
                    </button>
                    @endif
                </div>
            @endif
        </div>
     @include('livewire.component-type.modal-create')
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
        Livewire.on('alertInfo', (message) => {
            if (typeof alertInfo === 'function') {
                alertInfo(message);
            }
        });
        Livewire.on('open-modal-edit-type-component', () => {
            const modalCreate = document.getElementById('modalCreate');
            if (modalCreate) {
                modalCreate.classList.remove('hidden');
            }
        });
    });
</script>
@endpush
 </div>
 