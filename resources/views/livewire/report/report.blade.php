<!-- component -->
<div class="container mx-auto px-4 sm:px-8">
    <div wire:loading class="fixed top-0 left-0 w-screen h-screen flex items-center justify-center z-50">
        <div class="flex flex-col items-center justify-center h-full">
            <svg class="animate-spin h-16 w-16 text-blue-600 mr-3 mb-2" xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="10" stroke-opacity="0.25" stroke-width="2" stroke="currentColor"
                    fill="none"></circle>
                <path d="M12 2a10 10 0 00-9.5 7.5M12 2c2.5 0 5 1 7 2.5" stroke="currentColor" stroke-width="2"
                    fill="none"></path>
            </svg>
        </div>
    </div>
    <div class="py-4">
        <div>
            <h2 class="text-2xl font-semibold leading-tight">Reportes</h2>
        </div>
        <div class="py-4 flex justify-between items-center">
            @can('save report')
                <button id="openModalButton"
                    class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 focus:outline-none focus:bg-blue-600">
                    Crear reporte
                </button>
            @endcan
            <button wire:click="export" class="bg-green-500 text-white px-4 py-2 rounded">
                Reporte excel
            </button>
        </div>
        <div class="my-2 flex items-center">
            <div class="flex-1 mr-4">
                <label for="equipment_search" class="block text-sm font-medium text-gray-700">Equipos:</label>
                <div class="relative">
                    <select id="equipment_search"
                        class="block appearance-none w-full bg-white border border-gray-400 text-gray-700 py-2 px-4 pr-8 rounded-l leading-tight focus:outline-none focus:border-gray-500 focus:bg-white focus:border-l focus:border-r"
                        wire:model.lazy='equipment_search'>
                        <option value="">Todos</option>
                        @foreach ($equipments as $equipment)
                            <option value="{{ $equipment->id }}">{{ $equipment->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="flex-1 mr-4">
                <label for="company_search" class="block text-sm font-medium text-gray-700">Empresa:</label>
                <div class="relative">
                    <select id="company_search" class="block appearance-none w-full bg-white border border-gray-400 text-gray-700 py-2 px-4 pr-8 rounded-l leading-tight focus:outline-none focus:border-gray-500 focus:bg-white focus:border-l focus:border-r" wire:model.lazy='company_search'>
                        <option value="">Todas</option>
                        @foreach($companies as $company)
                            <option value="{{ $company->id }}">{{ $company->name }}</option>
                        @endforeach
                   </select>
                </div>
            </div>
            <div class="flex-1 mr-4">
                <label for="condition_search" class="block text-sm font-medium text-gray-700">Condicion:</label>
                <div class="relative">
                    <select id="condition_search"
                        class="block appearance-none w-full bg-white border border-gray-400 text-gray-700 py-2 px-4 pr-8 rounded-l leading-tight focus:outline-none focus:border-gray-500 focus:bg-white focus:border-l focus:border-r"
                        wire:model.lazy='condition_search'>
                        <option value="">Todos</option>
                        <option value="Alarma">Alarma</option>
                        <option value="Critico">Critico</option>
                        <option value="Peligro">Peligro</option>
                        <option value="Seguimiento">Seguimiento</option>
                    </select>
                </div>
            </div>
            <div class="flex-1">
                <label for="priority_search" class="block text-sm font-medium text-gray-700">Prioridad:</label>
                <div class="relative">
                    <select id="priority_search"
                        class="block appearance-none w-full bg-white border border-gray-400 text-gray-700 py-2 px-4 pr-8 rounded-l leading-tight focus:outline-none focus:border-gray-500 focus:bg-white focus:border-l focus:border-r"
                        wire:model.lazy='priority_search'>
                        <option value="">Todas</option>
                        <option value="Alta">Alta</option>
                        <option value="Media">Media</option>
                    </select>
                </div>
            </div>
            <button
                class="ml-4 px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 focus:outline-none focus:bg-blue-600 mt-4"
                wire:click="$refresh">Buscar</button>
        </div>
        <div class="-mx-4 sm:-mx-8 px-4 sm:px-8 py-4 overflow-x-auto">
            <div class="inline-block min-w-full shadow rounded-lg overflow-hidden">
                <table class="min-w-full leading-normal">
                    <thead>
                        <tr>
                            <th
                                class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider text-center">
                                Equipo
                            </th>
                            <th
                                class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider text-center">
                                Empresa
                            </th>
                            <th
                                class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider text-center">
                                Fecha medicion
                            </th>
                            <th
                                class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider text-center">
                                Ubicacion
                            </th>
                            <th
                                class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider text-center">
                                Proceso
                            </th>
                            <th
                                class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider text-center">
                                Condicion
                            </th>
                            <th
                                class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider text-center">
                                Prioridad
                            </th>
                            <th
                                class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider text-center">
                                Analista
                            </th>
                            <th
                                class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider text-center">
                                PDF
                            </th>
                            @can('actions report')
                                <th
                                    class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider text-center">
                                    Acciones
                                </th>
                            @endcan
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($reports as $report)
                            <tr>
                                <td class="px-5 py-3 border-b border-gray-200 bg-white text-sm text-center">
                                    {{ $report->equipment->name }}
                                </td>
                                <td class="px-5 py-3 border-b border-gray-200 bg-white text-sm text-center">
                                    {{ $report->equipment->company->name }}
                                </td>
                                <td class="px-5 py-3 border-b border-gray-200 bg-white text-sm text-center">
                                    {{ $report->measurement_date }}
                                </td>
                                <td class="px-5 py-3 border-b border-gray-200 bg-white text-sm text-center">
                                    {{ $report->location }}
                                </td>
                                <td class="px-5 py-3 border-b border-gray-200 bg-white text-sm text-center">
                                    {{ $report->process }}
                                </td>
                                <td class="px-5 py-3 border-b border-gray-200 bg-white text-sm text-center">
                                    {{ $report->condition }}
                                </td>
                                <td class="px-5 py-3 border-b border-gray-200 bg-white text-sm text-center">
                                    {{ $report->priority }}
                                </td>
                                <td class="px-5 py-3 border-b border-gray-200 bg-white text-sm text-center">
                                    {{ $report->analist }}
                                </td>
                                <td class="px-5 py-3 border-b border-gray-200 bg-white text-sm text-center">
                                    <button class="mr-2" wire:click="downloadPdf({{ $report->id }})">
                                        <i class="fas fa-file-pdf fa-lg text-blue-300" title="Exportar pdf"></i>
                                    </button>
                                </td>

                                @can('actions report')
                                    <td class="px-5 py-3 border-b border-gray-200 bg-white text-sm text-center">
                                        <button class="mr-2"  wire:click="showReportDetails({{ $report->id }})">
                                            <i class="fas fa-eye fa-lg text-blue-300" title="Detalle"></i>
                                        </button>
                                        <button class="mr-2" wire:click="editReport({{ $report->id }})">
                                            <i class="fas fa-edit fa-lg text-blue-300" title="Editar"></i>
                                        </button>
                                        <button class="ml-2"
                                            onclick="deleted('report.report', 'destroy',{{ $report->id }})"
                                            wire:loading.attr="disabled">
                                            <i class="fas fa-trash text-yellow-500" title="Eliminar"></i>
                                        </button>
                                    </td>
                                @endcan
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="flex justify-center">
                    {{ $reports->links() }}
                </div>
            </div>
        </div>
        @include('livewire.report.modal-create')
        @include('livewire.report.modal-show')
    </div>
    <script>
        const openModalButton = document.getElementById('openModalButton');
        const modalCreate = document.getElementById('modalCreate');
        openModalButton.addEventListener('click', function() {
            modalCreate.classList.remove('hidden');
        });
    </script>
    @push('scripts')
        <script>
            document.addEventListener('livewire:initialized', function() {
                Livewire.on('alertSuccess', (message) => {
                    alertSuccess(message);
                });
                Livewire.on('alertFailed', (message) => {
                    alertFailed(message);
                });
                Livewire.on('open-modal-edit-report', () => {
                    const modalCreate = document.getElementById('modalCreate');
                    modalCreate.classList.remove('hidden');
                });
                Livewire.on('open-modal-show-report', () => {
                const modalShow = document.getElementById('modalShow');
                modalShow.classList.add('hidden');
                modalShow.classList.remove('hidden');
             });
            });
        </script>
    @endpush
</div>
