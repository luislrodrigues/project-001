<!-- component -->
<div class="container mx-auto px-4 sm:px-8">
    <div wire:loading class="fixed top-0 left-0 w-screen h-screen flex items-center justify-center z-50 bg-black bg-opacity-30 backdrop-blur-sm">
        <div class="flex flex-col items-center justify-center h-full">
            <svg class="animate-spin h-16 w-16 text-blue-600 mr-3 mb-2" xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="10" stroke-opacity="0.25" stroke-width="2" stroke="currentColor"
                    fill="none"></circle>
                <path d="M12 2a10 10 0 00-9.5 7.5M12 2c2.5 0 5 1 7 2.5" stroke="currentColor" stroke-width="2"
                    fill="none"></path>
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
                        <i class="fas fa-file-alt text-blue-600 mr-3"></i>
                        Reportes
                    </h2>
                    <p class="text-gray-500 text-sm mt-1">Gestiona los reportes del sistema</p>
                </div>
                <div class="flex items-center space-x-3">
                    <button wire:click="export" 
                            class="flex items-center px-5 py-2.5 bg-green-500 text-white rounded-lg hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-colors duration-200 shadow-sm font-medium">
                        <i class="fas fa-file-excel mr-2"></i>
                        Exportar Excel
                    </button>
                    @can('save report')
                    <button id="openModalButton"
                            class="flex items-center px-5 py-2.5 bg-blue-500 text-white rounded-lg hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors duration-200 shadow-sm font-medium">
                        <i class="fas fa-plus mr-2"></i>
                        Crear Reporte
                    </button>
                    @endcan
                </div>
            </div>

            {{-- Search Section --}}
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                <div class="grid grid-cols-1 md:grid-cols-5 gap-3">
                    <div>
                        <label for="equipment_search" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-cog mr-2 text-gray-400"></i>
                            Equipo
                        </label>
                        <select id="equipment_search"
                                class="block w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 px-4 py-2.5 transition-all duration-200"
                                wire:model.live='equipment_search'>
                            <option value="">Todos</option>
                            @foreach ($equipments as $equipment)
                                <option value="{{ $equipment->id }}">{{ $equipment->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="company_search" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-building mr-2 text-gray-400"></i>
                            Empresa
                        </label>
                        <select id="company_search" 
                                class="block w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 px-4 py-2.5 transition-all duration-200"
                                wire:model.live='company_search'>
                            <option value="">Todas</option>
                            @foreach($companies as $company)
                                <option value="{{ $company->id }}">{{ $company->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="condition_search" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-exclamation-triangle mr-2 text-gray-400"></i>
                            Condición
                        </label>
                        <select id="condition_search"
                                class="block w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 px-4 py-2.5 transition-all duration-200"
                                wire:model.live='condition_search'>
                            <option value="">Todos</option>
                            <option value="Alarma">Alarma</option>
                            <option value="Critico">Crítico</option>
                            <option value="Peligro">Peligro</option>
                            <option value="Seguimiento">Seguimiento</option>
                        </select>
                    </div>
                    <div>
                        <label for="priority_search" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-flag mr-2 text-gray-400"></i>
                            Prioridad
                        </label>
                        <select id="priority_search"
                                class="block w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 px-4 py-2.5 transition-all duration-200"
                                wire:model.live='priority_search'>
                            <option value="">Todas</option>
                            <option value="Alta">Alta</option>
                            <option value="Media">Media</option>
                        </select>
                    </div>
                    <div class="flex items-end">
                        <button class="w-full px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-400 focus:ring-offset-2 transition-colors duration-200 font-medium" 
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
            @if($reports->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gradient-to-r from-blue-50 to-blue-100">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-blue-900 uppercase tracking-wider">Equipo</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-blue-900 uppercase tracking-wider">Empresa</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-blue-900 uppercase tracking-wider">Fecha Medición</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-blue-900 uppercase tracking-wider">Ubicación</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-blue-900 uppercase tracking-wider">Proceso</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-blue-900 uppercase tracking-wider">Condición</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-blue-900 uppercase tracking-wider">Prioridad</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-blue-900 uppercase tracking-wider">Analista</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-blue-900 uppercase tracking-wider">PDF</th>
                                <th class="px-6 py-4 text-right text-xs font-semibold text-blue-900 uppercase tracking-wider">
                                    <div class="flex items-center justify-end">
                                        <i class="fas fa-cog mr-2"></i>
                                        Acciones
                                    </div>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($reports as $report)
                            <tr class="hover:bg-blue-50 transition-colors duration-150">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">{{ $report->equipment->name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $report->equipment->company->name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $report->measurement_date }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $report->equipment->ubication->name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $report->equipment->process->name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium 
                                        @if($report->condition->name == 'Alarma') bg-yellow-100 text-yellow-800 border border-yellow-300
                                        @elseif($report->condition->name == 'Critico') bg-red-100 text-red-800 border border-red-300
                                        @elseif($report->condition->name == 'Peligro') bg-orange-100 text-orange-800 border border-orange-300
                                        @else bg-blue-100 text-blue-800 border border-blue-300
                                        @endif">
                                        {{ $report->condition->name }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium 
                                        @if($report->priority == 'Alta') bg-red-100 text-red-800 border border-red-300
                                        @else bg-yellow-100 text-yellow-800 border border-yellow-300
                                        @endif">
                                        {{ $report->priority }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $report->analist->name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <button wire:click="downloadPdf({{ $report->id }})"
                                            class="inline-flex items-center px-3 py-2 bg-red-50 hover:bg-red-100 text-red-600 rounded-lg transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-1"
                                            title="Exportar PDF">
                                        <i class="fas fa-file-pdf text-sm"></i>
                                        <span class="ml-1.5 text-xs font-medium">PDF</span>
                                    </button>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex items-center justify-end space-x-2">
                                        <button wire:click="showReportDetails({{ $report->id }})"
                                                class="inline-flex items-center px-3 py-2 bg-blue-50 hover:bg-blue-100 text-blue-600 rounded-lg transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-1"
                                                title="Detalle">
                                            <i class="fas fa-eye text-sm"></i>
                                            <span class="ml-1.5 text-xs font-medium">Ver</span>
                                        </button>
                                        @can('actions report')
                                        <button wire:click="editReport({{ $report->id }})"
                                                class="inline-flex items-center px-3 py-2 bg-blue-50 hover:bg-blue-100 text-blue-600 rounded-lg transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-1"
                                                title="Editar">
                                            <i class="fas fa-edit text-sm"></i>
                                            <span class="ml-1.5 text-xs font-medium">Editar</span>
                                        </button>
                                        <button onclick="deleted('report.report', 'destroy',{{ $report->id }})"
                                                wire:loading.attr="disabled"
                                                class="inline-flex items-center px-3 py-2 bg-red-50 hover:bg-red-100 text-red-600 rounded-lg transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-1"
                                                title="Eliminar">
                                            <i class="fas fa-trash text-sm"></i>
                                            <span class="ml-1.5 text-xs font-medium">Eliminar</span>
                                        </button>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                {{-- Pagination --}}
                @if($reports->hasPages())
                <div class="bg-gradient-to-r from-gray-50 to-blue-50 px-6 py-4 border-t border-gray-200">
                    <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                        <div class="text-sm text-gray-700 flex items-center">
                            <i class="fas fa-info-circle text-blue-600 mr-2"></i>
                            Mostrando 
                            <span class="font-semibold text-blue-600 mx-1">{{ $reports->firstItem() }}</span>
                            a 
                            <span class="font-semibold text-blue-600 mx-1">{{ $reports->lastItem() }}</span>
                            de 
                            <span class="font-semibold text-blue-600 mx-1">{{ $reports->total() }}</span>
                            resultados
                        </div>
                        <div class="flex items-center space-x-1">
                            @if($reports->onFirstPage())
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
                                $currentPage = $reports->currentPage();
                                $lastPage = $reports->lastPage();
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

                            @if($reports->hasMorePages())
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
                        <i class="fas fa-file-alt text-3xl text-gray-400"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">
                        @if($equipment_search || $company_search || $condition_search || $priority_search)
                            No se encontraron resultados
                        @else
                            No hay reportes
                        @endif
                    </h3>
                    <p class="text-gray-500 mb-6 max-w-sm mx-auto">
                        @if($equipment_search || $company_search || $condition_search || $priority_search)
                            Intenta buscar con otros filtros o crea un nuevo reporte.
                        @else
                            Comienza creando tu primer reporte para organizar mejor tus registros.
                        @endif
                    </p>
                    @if(!$equipment_search && !$company_search && !$condition_search && !$priority_search && auth()->user()->can('save report'))
                    <button id="openModalButton" 
                            class="inline-flex items-center px-5 py-2.5 bg-blue-500 text-white rounded-lg hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors duration-200 shadow-sm font-medium">
                        <i class="fas fa-plus mr-2"></i>
                        Crear Primer Reporte
                    </button>
                    @endif
                </div>
            @endif
        </div>
        @include('livewire.report.modal-create')
        @include('livewire.report.modal-show')
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
            document.addEventListener('livewire:initialized', function() {
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
                Livewire.on('open-modal-edit-report', () => {
                    const modalCreate = document.getElementById('modalCreate');
                    if (modalCreate) {
                        modalCreate.classList.remove('hidden');
                    }
                });
                Livewire.on('open-modal-show-report', () => {
                    const modalShow = document.getElementById('modalShow');
                    if (modalShow) {
                        modalShow.classList.add('hidden');
                        modalShow.classList.remove('hidden');
                    }
                });
            });
        </script>
    @endpush
</div>
