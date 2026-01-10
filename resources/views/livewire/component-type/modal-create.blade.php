<div wire:ignore.self id="modalCreate" tabindex="-1" aria-hidden="true"
    class="hidden overflow-hidden overflow-x-hidden fixed inset-0 z-50 flex items-center justify-center">
    <!-- Overlay con backdrop blur -->
    <div class="fixed inset-0 bg-gray-900 bg-opacity-75 backdrop-blur-sm transition-opacity z-40"
         onclick="document.getElementById('modalCreate').classList.add('hidden')"></div>

    <div wire:loading wire:target="saveType,updateEquipment"
        class="hidden overflow-y-auto overflow-x-hidden fixed inset-0 z-60 flex items-center justify-center">
        <div class="flex flex-col items-center justify-center h-full">
            <svg class="animate-spin h-16 w-16 text-blue-600 mr-3 mb-2" xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="10" stroke-opacity="0.25" stroke-width="2"
                    stroke="currentColor" fill="none"></circle>
                <path d="M12 2a10 10 0 00-9.5 7.5M12 2c2.5 0 5 1 7 2.5" stroke="currentColor" stroke-width="2"
                    fill="none"></path>
            </svg>
        </div>
    </div>
    <div class="relative p-4 w-full max-w-5xl z-50">
        <!-- Modal content -->
        <div class="relative bg-white rounded-xl shadow-2xl dark:bg-gray-800 overflow-hidden" style="max-height:90vh">
            <!-- Modal header con gradiente -->
            <div class="flex items-center justify-between px-6 py-4 bg-gradient-to-r from-blue-500 to-blue-600 rounded-t-xl">
                <div class="flex items-center space-x-3">
                    <div class="bg-white bg-opacity-20 rounded-lg p-2">
                        <i class="fas {{ $IdToUpdate ? 'fa-edit' : 'fa-plus-circle' }} text-white text-xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-white">
                        {{ $IdToUpdate ? 'Editar tipo de componente' : 'Crear nuevo tipo de componente' }}
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
            <div class="overflow-y-auto" style="max-height: calc(90vh - 80px)">
                <form class="p-6"
                    @if ($IdToUpdate) wire:submit.prevent="updateTypeComponent" @else wire:submit.prevent="saveTypeComponent" @endif>
                    <!-- Sección: Información básica -->
                    <div class="mb-8">
                        <div class="flex items-center mb-4 pb-2 border-b border-gray-200">
                            <i class="fas fa-info-circle text-blue-600 mr-2"></i>
                            <h4 class="text-lg font-semibold text-gray-800 dark:text-white">Información Básica</h4>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                            <label for="name" class="flex items-center mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                <i class="fas fa-tag mr-2 text-blue-600"></i>
                                Nombre del tipo de componente
                                <span class="text-red-500 ml-1">*</span>
                            </label>
                            <input type="text" id="name"
                                class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 block w-full px-4 py-3 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white transition-all duration-200"
                                placeholder="Ej: Motor, Sensor, Válvula..." wire:model.defer="name">
                            @error('name')
                                <span class="flex items-center mt-2 text-sm text-red-600">
                                    <i class="fas fa-exclamation-circle mr-1"></i>
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>
                    </div>
                    <!-- Sección: Atributos -->
                    <div class="mb-8">
                        <div class="flex items-center justify-between mb-4 pb-2 border-b border-gray-200">
                            <div class="flex items-center">
                                <i class="fas fa-list-ul text-blue-600 mr-2"></i>
                                <h4 class="text-lg font-semibold text-gray-800 dark:text-white">Atributos del Componente</h4>
                            </div>
                            <button type="button" wire:click="addField"
                                class="flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-colors duration-200 shadow-sm">
                                <i class="fas fa-plus mr-2"></i>
                                Agregar Atributo
                            </button>
                        </div>

                        @if(count($fields) === 0)
                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6 text-center">
                            <i class="fas fa-info-circle text-yellow-600 text-2xl mb-2"></i>
                            <p class="text-gray-600">No hay atributos agregados. Haz clic en "Agregar Atributo" para comenzar.</p>
                        </div>
                        @endif

                        <div class="space-y-4">
                            @foreach ($fields as $index => $field)
                                <div class="bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg p-5 shadow-sm hover:shadow-md transition-shadow duration-200">
                                    <div class="flex items-center justify-between mb-4">
                                        <div class="flex items-center space-x-2">
                                            <div class="bg-blue-100 dark:bg-blue-900 rounded-full w-8 h-8 flex items-center justify-center">
                                                <span class="text-blue-600 dark:text-blue-400 font-semibold text-sm">{{ $index + 1 }}</span>
                                            </div>
                                            <h5 class="font-medium text-gray-800 dark:text-white">Atributo #{{ $index + 1 }}</h5>
                                        </div>
                                        <button type="button" wire:click="removeField({{ $index }})"
                                            class="flex items-center px-3 py-2 bg-red-100 hover:bg-red-200 text-red-700 rounded-lg transition-colors duration-200">
                                            <i class="fas fa-trash mr-1"></i>
                                            <span class="text-sm">Eliminar</span>
                                        </button>
                                    </div>

                                    @php
                                        $fieldType = $fields[$index]['type'] ?? '';
                                        $isNumericType = $fieldType === 'numeric';
                                    @endphp

                                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                                        <!-- Nombre del atributo -->
                                        <div class="md:col-span-2">
                                            <label class="flex items-center mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                                <i class="fas fa-font mr-2 text-gray-500 text-xs"></i>
                                                Nombre del atributo
                                                <span class="text-red-500 ml-1">*</span>
                                            </label>
                                            <input type="text"
                                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 block w-full px-4 py-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white transition-all duration-200"
                                                placeholder="Ej: Potencia, Velocidad, Temperatura..."
                                                wire:model.defer="fields.{{ $index }}.name">
                                            @error("fields.$index.name")
                                                <span class="flex items-center mt-1 text-xs text-red-600">
                                                    <i class="fas fa-exclamation-circle mr-1"></i>
                                                    {{ $message }}
                                                </span>
                                            @enderror
                                        </div>

                                        <!-- Tipo de campo -->
                                        <div>
                                            <label class="flex items-center mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                                <i class="fas fa-tag mr-2 text-gray-500 text-xs"></i>
                                                Tipo
                                                <span class="text-red-500 ml-1">*</span>
                                            </label>
                                            <select wire:model.live="fields.{{ $index }}.type"
                                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 block w-full px-4 py-2.5 dark:bg-gray-600 dark:border-gray-500 dark:text-white transition-all duration-200">
                                                <option value="">Seleccione</option>
                                                <option value="text">📝 Texto</option>
                                                <option value="numeric">🔢 Numérico</option>
                                                <option value="select">☑️ Selección</option>
                                                <option value="multiselect">☑️ Selección Múltiple</option>
                                                <option value="date">📅 Fecha</option>
                                                <option value="boolean">✓ Sí/No</option>
                                            </select>
                                            @error("fields.$index.type")
                                                <span class="flex items-center mt-1 text-xs text-red-600">
                                                    <i class="fas fa-exclamation-circle mr-1"></i>
                                                    {{ $message }}
                                                </span>
                                            @enderror
                                        </div>

                                        <!-- Unidad (solo para tipo numeric) -->
                                        @if($isNumericType)
                                        <div>
                                            <label class="flex items-center mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                                <i class="fas fa-ruler mr-2 text-gray-500 text-xs"></i>
                                                Unidad
                                            </label>
                                            <input type="text"
                                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 block w-full px-4 py-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white transition-all duration-200"
                                                placeholder="Ej: kW, RPM, °C..."
                                                wire:model.defer="fields.{{ $index }}.unit">
                                            @error("fields.$index.unit")
                                                <span class="flex items-center mt-1 text-xs text-red-600">
                                                    <i class="fas fa-exclamation-circle mr-1"></i>
                                                    {{ $message }}
                                                </span>
                                            @enderror
                                        </div>
                                        @endif

                                    </div>

                                    {{-- Sección de opciones (solo se muestra si es select/multiselect) --}}
                                    @php
                                        $hasFieldId = isset($fields[$index]['id']) && !empty($fields[$index]['id']);
                                        $fieldType = $fields[$index]['type'] ?? '';
                                        $isSelectType = $fieldType === 'select' || $fieldType === 'multiselect';
                                        $tempOptionsForField = $tempOptions[$index] ?? [];
                                    @endphp
                                    @if($isSelectType)
                                        <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-600">
                                            <label class="flex items-center mb-3 text-sm font-medium text-gray-700 dark:text-gray-300">
                                                <i class="fas fa-list mr-2 text-blue-600"></i>
                                                Opciones de selección
                                                @if(count($tempOptionsForField) > 0)
                                                    <span class="ml-2 px-2 py-0.5 bg-green-100 text-green-700 rounded-full text-xs font-semibold">
                                                        {{ count($tempOptionsForField) }} {{ count($tempOptionsForField) === 1 ? 'opción' : 'opciones' }}
                                                    </span>
                                                @endif
                                            </label>

                                            {{-- Botón para gestionar opciones --}}
                                            <button type="button"
                                                    wire:click="openTempOptionsManager({{ $index }})"
                                                    class="w-full px-4 py-2.5 bg-blue-500 text-white rounded-lg hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors duration-200 shadow-sm">
                                                <i class="fas fa-cog mr-2"></i>
                                                <span>Gestionar Opciones</span>
                                            </button>

                                            {{-- Mostrar vista previa de opciones temporales --}}
                                            @if(count($tempOptionsForField) > 0 && !$hasFieldId)
                                            <div class="mt-3 p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                                                <div class="text-xs font-semibold text-blue-700 dark:text-blue-300 mb-2 flex items-center">
                                                    <i class="fas fa-eye mr-1"></i>
                                                    Vista previa
                                                </div>
                                                <div class="flex flex-wrap gap-2">
                                                    @foreach($tempOptionsForField as $optIndex => $opt)
                                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $opt['is_default'] ?? false ? 'bg-yellow-100 text-yellow-800 border border-yellow-300' : 'bg-blue-100 text-blue-800 border border-blue-200' }}">
                                                            {{ $opt['label'] }}
                                                            @if($opt['is_default'] ?? false)
                                                                <i class="fas fa-star ml-1.5 text-yellow-500"></i>
                                                            @endif
                                                        </span>
                                                    @endforeach
                                                </div>
                                            </div>
                                            @endif

                                            @error("fields.$index.name")
                                                @if(str_contains($message, 'opción'))
                                                    <span class="flex items-center mt-2 text-xs text-red-600">
                                                        <i class="fas fa-exclamation-circle mr-1"></i>
                                                        {{ $message }}
                                                    </span>
                                                @endif
                                            @enderror
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Footer con botones de acción -->
                    <div class="flex justify-end space-x-3 pt-6 mt-6 border-t border-gray-200 dark:border-gray-600">
                        <button type="button" wire:click="resetForm" id="closeModalButton2"
                            class="flex items-center px-6 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition-colors duration-200 font-medium">
                            <i class="fas fa-times mr-2"></i>
                            Cancelar
                        </button>
                        <button type="submit"
                                class="flex items-center px-6 py-2.5 bg-blue-500 hover:bg-blue-600 text-white rounded-lg transition-colors duration-200 font-medium shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                            <i class="fas fa-save mr-2"></i>
                            {{ $IdToUpdate ? 'Actualizar' : 'Guardar' }}
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>

    {{-- Incluir el gestor de opciones para campos guardados --}}
    @livewire('field-options-manager.field-options-manager')

    {{-- Incluir el gestor de opciones temporales --}}
    @include('livewire.component-type.modal-temp-options')
</div>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const modalCreate = document.getElementById('modalCreate');

            if (!modalCreate) return;

            // Event listener para cerrar desde Livewire
            document.addEventListener('livewire:initialized', function() {
                Livewire.on('close-modal-type-component', () => {
                    if (modalCreate) {
                        modalCreate.classList.add('hidden');
                    }
                });
            });

            // Event listener para botón de cerrar en header
            const closeModalButton = document.getElementById('closeModalButton');
            if (closeModalButton) {
                closeModalButton.addEventListener('click', function() {
                    modalCreate.classList.add('hidden');
                });
            }

            // Event listener para botón cancelar
            const closeModalButton2 = document.getElementById('closeModalButton2');
            if (closeModalButton2) {
                closeModalButton2.addEventListener('click', function() {
                    modalCreate.classList.add('hidden');
                });
            }
        });
    </script>
@endpush
