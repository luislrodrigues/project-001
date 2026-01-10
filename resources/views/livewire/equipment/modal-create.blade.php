<div wire:ignore.self id="modalCreate" tabindex="-1" aria-hidden="true"
    class="hidden overflow-y-auto overflow-x-hidden fixed inset-0 z-50 flex items-center justify-center">
    <!-- Overlay con backdrop blur -->
    <div class="fixed inset-0 bg-gray-900 bg-opacity-75 backdrop-blur-sm transition-opacity z-40"
         onclick="document.getElementById('modalCreate').classList.add('hidden')"></div>

    <div wire:loading wire:target="saveEquipment,updateEquipment"
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
                        <i class="fas {{ $equipmentIdToUpdate ? 'fa-edit' : 'fa-plus-circle' }} text-white text-xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-white">
                        {{ $equipmentIdToUpdate ? 'Editar equipo' : 'Crear nuevo equipo' }}
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
                    @if ($equipmentIdToUpdate) wire:submit.prevent="updateEquipment" @else wire:submit.prevent="saveEquipment" @endif>
                    <div class="mb-6">
                        <div class="flex items-center mb-4 pb-2 border-b border-gray-200">
                            <i class="fas fa-info-circle text-blue-600 mr-2"></i>
                            <h4 class="text-lg font-semibold text-gray-800 dark:text-white">Información del Equipo</h4>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                <label for="name" class="flex items-center mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                    <i class="fas fa-tag mr-2 text-blue-600"></i>
                                    Nombre
                                    <span class="text-red-500 ml-1">*</span>
                                </label>
                                <input type="text" id="name"
                                    class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 block w-full px-4 py-2.5 dark:bg-gray-600 dark:border-gray-500 dark:text-white transition-all duration-200"
                                    placeholder="Ingresar nombre" wire:model.defer="name">
                                @error('name')
                                    <span class="flex items-center mt-1 text-xs text-red-600">
                                        <i class="fas fa-exclamation-circle mr-1"></i>
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>

                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                <label for="equipment_type" class="flex items-center mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                    <i class="fas fa-cog mr-2 text-blue-600"></i>
                                    Tipo equipo
                                    <span class="text-red-500 ml-1">*</span>
                                </label>
                                <div class="flex space-x-2 items-stretch">
                                    <select name="equipment_type" id="selecteEuipmentType"
                                        class="flex-1 min-w-0 bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 px-4 py-2.5 dark:bg-gray-600 dark:border-gray-500 dark:text-white transition-all duration-200"
                                        wire:model.lazy="equipment_type">
                                        <option value="">Seleccione</option>
                                        @foreach ($equipment_types as $option)
                                            <option value="{{ $option['id'] }}">{{ $option['name'] }}</option>
                                        @endforeach
                                    </select>
                                    <button type="button"
                                            wire:click="openManageModal('equipment_type')"
                                            class="flex-shrink-0 w-10 h-10 bg-blue-500 hover:bg-blue-600 text-white rounded-lg transition-colors duration-200 flex items-center justify-center"
                                            title="Gestionar tipos de equipo">
                                        <i class="fas fa-cog text-sm"></i>
                                    </button>
                                </div>
                                @error('equipment_type')
                                    <span class="flex items-center mt-1 text-xs text-red-600">
                                        <i class="fas fa-exclamation-circle mr-1"></i>
                                        {{ $message }}
                                    </span>
                                @enderror
                                <livewire:modal-create-select.modal-create-select :label="'Tipo equipo'" :field="'equipment_type'"
                                    updateOptions="updatedEquipmentTypes" />
                            </div>

                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                <label for="process" class="flex items-center mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                    <i class="fas fa-tasks mr-2 text-blue-600"></i>
                                    Proceso
                                    <span class="text-red-500 ml-1">*</span>
                                </label>
                                <select name="process" id="selecteEuipmentType"
                                    class="block w-full bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 px-4 py-2.5 dark:bg-gray-600 dark:border-gray-500 dark:text-white transition-all duration-200"
                                    wire:model.lazy="process" wire:change="handleSelectChange($event.target.value,'process')">
                                    <option value="">Seleccione</option>
                                    @foreach ($processes as $option)
                                        <option value="{{ $option['id'] }}">{{ $option['name'] }}</option>
                                    @endforeach
                                    <option value="add_new">Añadir nueva</option>
                                </select>
                                @error('process')
                                    <span class="flex items-center mt-1 text-xs text-red-600">
                                        <i class="fas fa-exclamation-circle mr-1"></i>
                                        {{ $message }}
                                    </span>
                                @enderror
                                <livewire:modal-create-select.modal-create-select :label="'Proceso'" :field="'process'"
                                    updateOptions="updatedProcesses" />
                            </div>

                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                <label for="ubication" class="flex items-center mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                    <i class="fas fa-map-marker-alt mr-2 text-blue-600"></i>
                                    Ubicación
                                    <span class="text-red-500 ml-1">*</span>
                                </label>
                                <div class="flex space-x-2 items-stretch">
                                    <select name="ubication" id="selecteEuipmentType"
                                        class="flex-1 min-w-0 bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 px-4 py-2.5 dark:bg-gray-600 dark:border-gray-500 dark:text-white transition-all duration-200"
                                        wire:model.lazy="ubication">
                                        <option value="">Seleccione</option>
                                        @foreach ($ubications as $option)
                                            <option value="{{ $option['id'] }}">{{ $option['name'] }}</option>
                                        @endforeach
                                    </select>
                                    <button type="button"
                                            wire:click="openManageModal('ubication')"
                                            class="flex-shrink-0 w-10 h-10 bg-blue-500 hover:bg-blue-600 text-white rounded-lg transition-colors duration-200 flex items-center justify-center"
                                            title="Gestionar ubicaciones">
                                        <i class="fas fa-cog text-sm"></i>
                                    </button>
                                </div>
                                @error('ubication')
                                    <span class="flex items-center mt-1 text-xs text-red-600">
                                        <i class="fas fa-exclamation-circle mr-1"></i>
                                        {{ $message }}
                                    </span>
                                @enderror
                                <livewire:modal-create-select.modal-create-select :label="'Ubicación'" :field="'ubication'"
                                    updateOptions="updatedUbications" />
                            </div>

                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                <label for="code" class="flex items-center mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                    <i class="fas fa-barcode mr-2 text-blue-600"></i>
                                    Código
                                </label>
                                <input type="text" id="code"
                                    class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 block w-full px-4 py-2.5 dark:bg-gray-600 dark:border-gray-500 dark:text-white transition-all duration-200"
                                    placeholder="Ingresar código" wire:model.defer="code">
                                @error('code')
                                    <span class="flex items-center mt-1 text-xs text-red-600">
                                        <i class="fas fa-exclamation-circle mr-1"></i>
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>

                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                <label for="tag" class="flex items-center mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                    <i class="fas fa-tag mr-2 text-blue-600"></i>
                                    Tag
                                </label>
                                <input type="text" id="tag"
                                    class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 block w-full px-4 py-2.5 dark:bg-gray-600 dark:border-gray-500 dark:text-white transition-all duration-200"
                                    placeholder="Ingresar tag" wire:model.defer="tag">
                                @error('tag')
                                    <span class="flex items-center mt-1 text-xs text-red-600">
                                        <i class="fas fa-exclamation-circle mr-1"></i>
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>

                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                <label for="company" class="flex items-center mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                    <i class="fas fa-building mr-2 text-blue-600"></i>
                                    Empresa
                                    <span class="text-red-500 ml-1">*</span>
                                </label>
                                <select id="company"
                                    class="block w-full bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 px-4 py-2.5 dark:bg-gray-600 dark:border-gray-500 dark:text-white transition-all duration-200"
                                    wire:model.defer="company">
                                    <option value="">Seleccione</option>
                                    @foreach ($companies as $company)
                                        <option value="{{ $company->id }}">{{ $company->name }}</option>
                                    @endforeach
                                </select>
                                @error('company')
                                    <span class="flex items-center mt-1 text-xs text-red-600">
                                        <i class="fas fa-exclamation-circle mr-1"></i>
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>

                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 md:col-span-3">
                                <label for="description" class="flex items-center mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                    <i class="fas fa-align-left mr-2 text-blue-600"></i>
                                    Descripción
                                </label>
                                <textarea id="description"
                                    class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 block w-full px-4 py-2.5 dark:bg-gray-600 dark:border-gray-500 dark:text-white transition-all duration-200 min-h-[80px]"
                                    placeholder="Ingresar descripción" wire:model.defer="description"></textarea>
                                @error('description')
                                    <span class="flex items-center mt-1 text-xs text-red-600">
                                        <i class="fas fa-exclamation-circle mr-1"></i>
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>
                        </div>
                    </div>

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
                            {{ $equipmentIdToUpdate ? 'Actualizar' : 'Guardar' }}
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
                Livewire.on('close-modal-equipment', () => {
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

            document.addEventListener('open-modal', (event) => {
                const modalId = event.detail.id;
                const modal = document.getElementById(`modal-${modalId}`);
                if (modal) {
                    modal.classList.remove('hidden');
                }
            });

            document.addEventListener('close-modal', (event) => {
                const modalId = event.detail.id;
                const modal = document.getElementById(`modal-${modalId}`);
                if (modal) {
                    modal.classList.add('hidden');
                }
            });

            function closeModal(id) {
                const modal = document.getElementById(`modal-${id}`);
                if (modal) modal.classList.add('hidden');
            }
        });
    </script>
@endpush
