<div wire:ignore.self id="modalCreate" tabindex="-1" aria-hidden="true"
    class="hidden overflow-hidden fixed inset-0 z-50 flex items-center justify-center">
    <!-- Overlay con backdrop blur -->
    <div class="fixed inset-0 bg-gray-900 bg-opacity-75 backdrop-blur-sm transition-opacity z-40"
         onclick="document.getElementById('modalCreate').classList.add('hidden')"></div>

    <div wire:loading wire:target="saveReport,updateReport,addRecommendation,deleteRecommendation"
        class="hidden overflow-y-auto overflow-x-hidden fixed inset-0 z-60 flex items-center justify-center">
        <div class="flex flex-col items-center justify-center h-full">
            <svg class="animate-spin h-16 w-16 text-blue-600 mr-3 mb-2" xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="10" stroke-opacity="0.25" stroke-width="2"
                    stroke="currentColor" fill="none"></circle>
                <path d="M12 2a10 10 0 00-9.5 7.5M12 2c2.5 0 5 1 7 2.5" stroke="currentColor" stroke-width="2"
                    fill="none"></path>
            </svg>
            <p class="text-gray-700 font-medium">Cargando...</p>
        </div>
    </div>
    <div class="relative p-4 w-full max-w-5xl z-50">
        <!-- Modal content -->
        <div class="relative bg-white rounded-xl shadow-2xl dark:bg-gray-800 overflow-hidden" style="max-height:90vh">
            <!-- Modal header con gradiente -->
            <div class="flex items-center justify-between px-6 py-4 bg-gradient-to-r from-blue-500 to-blue-600 rounded-t-xl">
                <div class="flex items-center space-x-3">
                    <div class="bg-white bg-opacity-20 rounded-lg p-2">
                        <i class="fas {{ $reportIdToUpdate ? 'fa-edit' : 'fa-plus-circle' }} text-white text-xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-white">
                        {{ $reportIdToUpdate ? 'Editar reporte' : 'Crear nuevo reporte' }}
                    </h3>
                </div>
                <button wire:click="resetForm" type="button" id="closeModalButton"
                    class="text-white hover:bg-white hover:bg-opacity-20 rounded-lg text-sm w-9 h-9 inline-flex justify-center items-center transition-colors duration-200">
                    <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                    </svg>
                    <span class="sr-only">Close modal</span>
                </button>
            </div>

            <!-- Modal body -->
            <div class="overflow-y-auto" style="max-height: calc(90vh - 80px)">
                <form class="p-6"
                    @if ($reportIdToUpdate) wire:submit.prevent="updateReport" @else wire:submit.prevent="saveReport" @endif>
                    {{-- Información General --}}
                    <div class="mb-8">
                        <div class="flex items-center mb-4 pb-2 border-b border-gray-200">
                            <i class="fas fa-info-circle text-blue-600 mr-2"></i>
                            <h4 class="text-lg font-semibold text-gray-800 dark:text-white">Información General</h4>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                <label for="equipment" class="flex items-center mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                    <i class="fas fa-cog mr-2 text-blue-600"></i>
                                    Equipo
                                    <span class="text-red-500 ml-1">*</span>
                                </label>
                                <select id="equipment"
                                    class="block w-full bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 px-4 py-2.5 dark:bg-gray-600 dark:border-gray-500 dark:text-white transition-all duration-200"
                                    wire:model.live="equipment">
                                    <option value="">Seleccione</option>
                                    @foreach ($equipments as $equipment)
                                        <option value="{{ $equipment->id }}">{{ $equipment->name }}</option>
                                    @endforeach
                                </select>
                                @error('equipment')
                                    <span class="flex items-center mt-1 text-xs text-red-600">
                                        <i class="fas fa-exclamation-circle mr-1"></i>
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>

                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                <label for="equipmentType" class="flex items-center mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                    <i class="fas fa-tag mr-2 text-blue-600"></i>
                                    Tipo Equipo
                                </label>
                                <input type="text" id="equipmentType"
                                    class="bg-gray-100 border border-gray-300 text-gray-700 text-sm rounded-lg block w-full px-4 py-2.5 dark:bg-gray-600 dark:border-gray-500 dark:text-gray-400 cursor-not-allowed"
                                    wire:model.defer="equipmentType" disabled>
                            </div>

                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                <label for="measurement_date" class="flex items-center mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                    <i class="fas fa-calendar mr-2 text-blue-600"></i>
                                    Fecha Medición
                                    <span class="text-red-500 ml-1">*</span>
                                </label>
                                <input type="date" id="measurement_date"
                                    class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 block w-full px-4 py-2.5 dark:bg-gray-600 dark:border-gray-500 dark:text-white transition-all duration-200"
                                    wire:model.defer="measurement_date">
                                @error('measurement_date')
                                    <span class="flex items-center mt-1 text-xs text-red-600">
                                        <i class="fas fa-exclamation-circle mr-1"></i>
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>

                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                <label for="location" class="flex items-center mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                    <i class="fas fa-map-marker-alt mr-2 text-blue-600"></i>
                                    Ubicación
                                </label>
                                <input type="text" id="location"
                                    class="bg-gray-100 border border-gray-300 text-gray-700 text-sm rounded-lg block w-full px-4 py-2.5 dark:bg-gray-600 dark:border-gray-500 dark:text-gray-400 cursor-not-allowed"
                                    wire:model.defer="location" disabled>
                            </div>

                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                <label for="process" class="flex items-center mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                    <i class="fas fa-tasks mr-2 text-blue-600"></i>
                                    Proceso
                                </label>
                                <input type="text" id="process"
                                    class="bg-gray-100 border border-gray-300 text-gray-700 text-sm rounded-lg block w-full px-4 py-2.5 dark:bg-gray-600 dark:border-gray-500 dark:text-gray-400 cursor-not-allowed"
                                    wire:model.defer="process" disabled>
                            </div>

                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                <label for="condition" class="flex items-center mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                    <i class="fas fa-exclamation-triangle mr-2 text-blue-600"></i>
                                    Condición
                                    <span class="text-red-500 ml-1">*</span>
                                </label>
                                <div class="flex space-x-2 items-stretch">
                                    <select name="condition"
                                        class="flex-1 min-w-0 bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 px-4 py-2.5 dark:bg-gray-600 dark:border-gray-500 dark:text-white transition-all duration-200"
                                        wire:model.lazy="condition">
                                        <option value="">Seleccione</option>
                                        @foreach ($conditions as $option)
                                            <option value="{{ $option['id'] }}">{{ $option['name'] }}</option>
                                        @endforeach
                                    </select>
                                    <button type="button"
                                            wire:click="openManageModal('condition')"
                                            class="flex-shrink-0 w-10 h-10 bg-blue-500 hover:bg-blue-600 text-white rounded-lg transition-colors duration-200 flex items-center justify-center"
                                            title="Gestionar condiciones">
                                        <i class="fas fa-cog text-sm"></i>
                                    </button>
                                </div>
                                @error('condition')
                                    <span class="flex items-center mt-1 text-xs text-red-600">
                                        <i class="fas fa-exclamation-circle mr-1"></i>
                                        {{ $message }}
                                    </span>
                                @enderror
                                <livewire:modal-create-select.modal-create-select :label="'Condicion'" :field="'condition'"
                                    updateOptions="updatedConditions" />
                            </div>

                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                <label for="failure_mechanism" class="flex items-center mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                    <i class="fas fa-wrench mr-2 text-blue-600"></i>
                                    Mecanismo Falla
                                </label>
                                <div class="flex space-x-2">
                                    <select name="failure_mechanism"
                                        class="flex-1 bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 px-4 py-2.5 dark:bg-gray-600 dark:border-gray-500 dark:text-white transition-all duration-200"
                                        wire:model.lazy="failure_mechanism">
                                        <option value="">Seleccione</option>
                                        @foreach ($mechanism_fails as $option)
                                            <option value="{{ $option['id'] }}">{{ $option['name'] }}</option>
                                        @endforeach
                                    </select>
                                    <button type="button"
                                            wire:click="$dispatch('open-modal-select', 'failure_mechanism')"
                                            class="px-4 py-2.5 bg-blue-500 hover:bg-blue-600 text-white rounded-lg transition-colors duration-200 flex items-center justify-center"
                                            title="Gestionar mecanismos de falla">
                                        <i class="fas fa-cog"></i>
                                    </button>
                                </div>
                                @error('failure_mechanism')
                                    <span class="flex items-center mt-1 text-xs text-red-600">
                                        <i class="fas fa-exclamation-circle mr-1"></i>
                                        {{ $message }}
                                    </span>
                                @enderror
                                <livewire:modal-create-select.modal-create-select :label="'Mecanismo falla'" :field="'failure_mechanism'"
                                    updateOptions="updatedmechanismFails" />
                            </div>

                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                <label for="component_failure" class="flex items-center mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                    <i class="fas fa-cog mr-2 text-blue-600"></i>
                                    Componente Falla
                                </label>
                                <div class="flex space-x-2 items-stretch">
                                    <select name="component_failure"
                                        class="flex-1 min-w-0 bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 px-4 py-2.5 dark:bg-gray-600 dark:border-gray-500 dark:text-white transition-all duration-200"
                                        wire:model.lazy="component_failure">
                                        <option value="">Seleccione</option>
                                        @foreach ($component_fails as $option)
                                            <option value="{{ $option['id'] }}">{{ $option['name'] }}</option>
                                        @endforeach
                                    </select>
                                    <button type="button"
                                            wire:click="openManageModal('component_failure')"
                                            class="flex-shrink-0 w-10 h-10 bg-blue-500 hover:bg-blue-600 text-white rounded-lg transition-colors duration-200 flex items-center justify-center"
                                            title="Gestionar componentes de falla">
                                        <i class="fas fa-cog text-sm"></i>
                                    </button>
                                </div>
                                @error('component_failure')
                                    <span class="flex items-center mt-1 text-xs text-red-600">
                                        <i class="fas fa-exclamation-circle mr-1"></i>
                                        {{ $message }}
                                    </span>
                                @enderror
                                <livewire:modal-create-select.modal-create-select :label="'Componente falla'" :field="'component_failure'"
                                    updateOptions="updatedcomponentFails" />
                            </div>

                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                <label for="priority" class="flex items-center mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                    <i class="fas fa-flag mr-2 text-blue-600"></i>
                                    Prioridad
                                    <span class="text-red-500 ml-1">*</span>
                                </label>
                                <select id="priority"
                                    class="block w-full bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 px-4 py-2.5 dark:bg-gray-600 dark:border-gray-500 dark:text-white transition-all duration-200"
                                    wire:model.defer="priority">
                                    <option value="">Seleccione</option>
                                    <option value="Alta">Alta</option>
                                    <option value="Media">Media</option>
                                    <option value="Baja">Baja</option>
                                </select>
                                @error('priority')
                                    <span class="flex items-center mt-1 text-xs text-red-600">
                                        <i class="fas fa-exclamation-circle mr-1"></i>
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>

                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                <label for="analist" class="flex items-center mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                    <i class="fas fa-user mr-2 text-blue-600"></i>
                                    Analista
                                    <span class="text-red-500 ml-1">*</span>
                                </label>
                                <select id="analist"
                                    class="block w-full bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 px-4 py-2.5 dark:bg-gray-600 dark:border-gray-500 dark:text-white transition-all duration-200"
                                    wire:model.lazy="analist">
                                    <option value="">Seleccione</option>
                                    @foreach ($users as $analist)
                                        <option value="{{ $analist->id }}">{{ $analist->name }}</option>
                                    @endforeach
                                </select>
                                @error('analist')
                                    <span class="flex items-center mt-1 text-xs text-red-600">
                                        <i class="fas fa-exclamation-circle mr-1"></i>
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>

                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                <label for="ot_created" class="flex items-center mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                    <i class="fas fa-check-circle mr-2 text-blue-600"></i>
                                    OT Creada
                                </label>
                                <input type="text" id="ot_created"
                                    class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 block w-full px-4 py-2.5 dark:bg-gray-600 dark:border-gray-500 dark:text-white transition-all duration-200"
                                    placeholder="Ingresar OT creada" wire:model.defer="ot_created">
                                @error('ot_created')
                                    <span class="flex items-center mt-1 text-xs text-red-600">
                                        <i class="fas fa-exclamation-circle mr-1"></i>
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>

                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                <label for="status_ot" class="flex items-center mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                    <i class="fas fa-info-circle mr-2 text-blue-600"></i>
                                    Estado de OT
                                </label>
                                <input type="text" id="status_ot"
                                    class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 block w-full px-4 py-2.5 dark:bg-gray-600 dark:border-gray-500 dark:text-white transition-all duration-200"
                                    placeholder="Ingresar estado de OT" wire:model.defer="status_ot">
                                @error('status_ot')
                                    <span class="flex items-center mt-1 text-xs text-red-600">
                                        <i class="fas fa-exclamation-circle mr-1"></i>
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>

                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                <label for="execution_date" class="flex items-center mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                    <i class="fas fa-calendar-check mr-2 text-blue-600"></i>
                                    Fecha de Ejecución
                                </label>
                                <input type="date" id="execution_date"
                                    class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 block w-full px-4 py-2.5 dark:bg-gray-600 dark:border-gray-500 dark:text-white transition-all duration-200"
                                    wire:model.defer="execution_date">
                                @error('execution_date')
                                    <span class="flex items-center mt-1 text-xs text-red-600">
                                        <i class="fas fa-exclamation-circle mr-1"></i>
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>

                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 md:col-span-3">
                                <label for="verification_recommendation" class="flex items-center mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                    <i class="fas fa-check-double mr-2 text-blue-600"></i>
                                    Verificación Recomendación
                                </label>
                                <textarea id="verification_recommendation"
                                    class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 block w-full px-4 py-3 dark:bg-gray-600 dark:border-gray-500 dark:text-white transition-all duration-200 min-h-[100px]"
                                    placeholder="Ingresar verificación recomendación" wire:model.defer="verification_recommendation"></textarea>
                                @error('verification_recommendation')
                                    <span class="flex items-center mt-1 text-xs text-red-600">
                                        <i class="fas fa-exclamation-circle mr-1"></i>
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>

                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                <label for="image_1" class="flex items-center mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                    <i class="fas fa-image mr-2 text-blue-600"></i>
                                    Subir Imagen 1
                                </label>
                                <input type="file" id="image_1" accept="image/*"
                                    class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg block w-full px-4 py-2.5 dark:bg-gray-600 dark:border-gray-500 dark:text-white file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
                                    wire:model.defer="image_1">
                                @error('image_1')
                                    <span class="flex items-center mt-1 text-xs text-red-600">
                                        <i class="fas fa-exclamation-circle mr-1"></i>
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>

                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                <label for="image_2" class="flex items-center mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                    <i class="fas fa-image mr-2 text-blue-600"></i>
                                    Subir Imagen 2
                                </label>
                                <input type="file" id="image_2" accept="image/*"
                                    class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg block w-full px-4 py-2.5 dark:bg-gray-600 dark:border-gray-500 dark:text-white file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
                                    wire:model.defer="image_2">
                                @error('image_2')
                                    <span class="flex items-center mt-1 text-xs text-red-600">
                                        <i class="fas fa-exclamation-circle mr-1"></i>
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Diagnósticos y Recomendaciones --}}
                    @if(count($components) > 0)
                    <div class="mb-8">
                        <div class="flex items-center mb-4 pb-2 border-b border-gray-200">
                            <i class="fas fa-stethoscope text-blue-600 mr-2"></i>
                            <h4 class="text-lg font-semibold text-gray-800 dark:text-white">Diagnósticos y Recomendaciones</h4>
                        </div>
                        <div class="space-y-6">
                            @foreach ($components as $index => $component)
                                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-5 border border-gray-200 dark:border-gray-600">
                                    <div class="flex items-center mb-4">
                                        <div class="bg-blue-100 dark:bg-blue-900 rounded-full w-8 h-8 flex items-center justify-center mr-3">
                                            <span class="text-blue-600 dark:text-blue-300 font-semibold text-sm">{{ $index + 1 }}</span>
                                        </div>
                                        <h5 class="text-md font-semibold text-gray-800 dark:text-white">
                                            {{ $component->componentType->name }}
                                        </h5>
                                    </div>

                                    <div class="mb-4">
                                        <label for="diagnostic_{{ $component->id }}" class="flex items-center mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                            <i class="fas fa-clipboard-list mr-2 text-blue-600"></i>
                                            Diagnóstico
                                        </label>
                                        <textarea id="diagnostic_{{ $component->id }}"
                                            class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 block w-full px-4 py-3 dark:bg-gray-600 dark:border-gray-500 dark:text-white transition-all duration-200 min-h-[100px]"
                                            placeholder="Ingresar diagnóstico" wire:model.defer="diagnostics.{{ $component->id }}"></textarea>
                                        @error('diagnostics.' . $component->id)
                                            <span class="flex items-center mt-1 text-xs text-red-600">
                                                <i class="fas fa-exclamation-circle mr-1"></i>
                                                {{ $message }}
                                            </span>
                                        @enderror
                                    </div>

                                    <div class="mb-4">
                                        <div class="flex items-center justify-between mb-3">
                                            <label class="flex items-center text-sm font-medium text-gray-700 dark:text-gray-300">
                                                <i class="fas fa-lightbulb mr-2 text-yellow-600"></i>
                                                Recomendaciones
                                                @if (isset($recommendations[$component->id]) && is_array($recommendations[$component->id]) && !empty($recommendations[$component->id]))
                                                    <span class="ml-2 px-2 py-0.5 bg-blue-100 text-blue-700 rounded-full text-xs font-semibold">
                                                        {{ count($recommendations[$component->id]) }}
                                                    </span>
                                                @endif
                                            </label>
                                            <button type="button"
                                                class="flex items-center px-3 py-1.5 bg-green-500 hover:bg-green-600 text-white rounded-lg transition-colors duration-200 text-xs font-medium"
                                                wire:click="addRecommendation({{ $component->id }})">
                                                <i class="fas fa-plus mr-1"></i>
                                                Agregar
                                            </button>
                                        </div>

                                        @if (isset($recommendations[$component->id]) && is_array($recommendations[$component->id]) && !empty($recommendations[$component->id]))
                                            <div class="space-y-2">
                                                @foreach ($recommendations[$component->id] as $recIndex => $recommendation)
                                                    <div class="flex items-center space-x-2">
                                                        <div class="flex-1">
                                                            <div class="flex items-center mb-1">
                                                                <span class="flex-shrink-0 bg-yellow-100 text-yellow-800 rounded-full w-6 h-6 flex items-center justify-center text-xs font-semibold mr-2">
                                                                    {{ $recIndex + 1 }}
                                                                </span>
                                                                <span class="text-xs text-gray-500">Recomendación {{ $recIndex + 1 }}</span>
                                                            </div>
                                                            <input type="text"
                                                                id="recommendation_{{ $component->id }}_{{ $recIndex }}"
                                                                class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 block w-full px-4 py-2.5 dark:bg-gray-600 dark:border-gray-500 dark:text-white transition-all duration-200"
                                                                placeholder="Recomendación {{ $recIndex + 1 }}"
                                                                wire:model.defer="recommendations.{{ $component->id }}.{{ $recIndex }}">
                                                        </div>
                                                        <button type="button"
                                                            wire:click="deleteRecommendation({{ $component->id }}, {{ $recIndex }})"
                                                            class="px-3 py-2.5 bg-red-500 hover:bg-red-600 text-white rounded-lg transition-colors duration-200"
                                                            wire:loading.attr="disabled"
                                                            title="Eliminar recomendación">
                                                            <i class="fas fa-trash text-sm"></i>
                                                        </button>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <div class="text-center py-4 bg-gray-100 dark:bg-gray-600 rounded-lg border-2 border-dashed border-gray-300 dark:border-gray-500">
                                                <p class="text-sm text-gray-500">No hay recomendaciones aún</p>
                                                <p class="text-xs text-gray-400 mt-1">Haz clic en "Agregar" para crear una recomendación</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <!-- Footer -->
                    <div class="flex justify-end space-x-3 pt-6 mt-6 border-t border-gray-200 dark:border-gray-600">
                        <button wire:click="resetForm" type="button" id="closeModalButton2"
                            class="flex items-center px-6 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition-colors duration-200 font-medium">
                            <i class="fas fa-times mr-2"></i>
                            Cancelar
                        </button>
                        <button type="submit"
                            class="flex items-center px-6 py-2.5 bg-blue-500 hover:bg-blue-600 text-white rounded-lg transition-colors duration-200 font-medium shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                            <i class="fas fa-save mr-2"></i>
                            {{ $reportIdToUpdate ? 'Actualizar' : 'Guardar' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const modalCreate = document.getElementById('modalCreate');
            const closeModalButton = document.getElementById('closeModalButton');
            const closeModalButton2 = document.getElementById('closeModalButton2');

            if (!modalCreate) return;

            document.addEventListener('livewire:initialized', function() {
                Livewire.on('close-modal-report', () => {
                    if (modalCreate) {
                        modalCreate.classList.add('hidden');
                    }
                });
            });

            if (closeModalButton) {
                closeModalButton.addEventListener('click', function() {
                    if (modalCreate) {
                        modalCreate.classList.add('hidden');
                    }
                });
            }

            if (closeModalButton2) {
                closeModalButton2.addEventListener('click', function() {
                    if (modalCreate) {
                        modalCreate.classList.add('hidden');
                    }
                });
            }
        });
    </script>
@endpush
