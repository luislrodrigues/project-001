<div wire:ignore.self id="modalCreate" tabindex="-1" aria-hidden="true"
    class="hidden overflow-y-auto overflow-x-hidden fixed inset-0 z-50 flex items-center justify-center">
    <!-- Overlay con backdrop blur -->
    <div class="fixed inset-0 bg-gray-900 bg-opacity-75 backdrop-blur-sm transition-opacity z-40"
         onclick="document.getElementById('modalCreate').classList.add('hidden')"></div>
    
    <div wire:loading wire:target="saveCompany,updateCompany" 
         class="hidden fixed inset-0 z-60 flex items-center justify-center">
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
    <div class="relative p-4 w-full max-w-2xl z-50">
        <!-- Modal content -->
        <div class="relative bg-white rounded-xl shadow-2xl dark:bg-gray-800 overflow-hidden">
            <!-- Modal header con gradiente -->
            <div class="flex items-center justify-between px-6 py-4 bg-gradient-to-r from-blue-500 to-blue-600 rounded-t-xl">
                <div class="flex items-center space-x-3">
                    <div class="bg-white bg-opacity-20 rounded-lg p-2">
                        <i class="fas {{ $companyIdToUpdate ? 'fa-edit' : 'fa-plus-circle' }} text-white text-xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-white">
                        {{ $companyIdToUpdate ? 'Editar empresa' : 'Crear nueva empresa' }}
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
                    @if ($companyIdToUpdate) wire:submit.prevent="updateCompany" @else wire:submit.prevent="saveCompany" @endif>
                    <div class="mb-6">
                        <div class="flex items-center mb-4 pb-2 border-b border-gray-200">
                            <i class="fas fa-info-circle text-blue-600 mr-2"></i>
                            <h4 class="text-lg font-semibold text-gray-800 dark:text-white">Información de la Empresa</h4>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                            <label for="name" class="flex items-center mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                <i class="fas fa-building mr-2 text-blue-600"></i>
                                Nombre de la empresa
                                <span class="text-red-500 ml-1">*</span>
                            </label>
                            <input type="text" id="name"
                                class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 block w-full px-4 py-3 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white transition-all duration-200"
                                placeholder="Ingresar nombre de la empresa" wire:model.defer="name">
                            @error('name')
                                <span class="flex items-center mt-2 text-sm text-red-600">
                                    <i class="fas fa-exclamation-circle mr-1"></i>
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>
                    </div>

                    {{-- Correos --}}
                    <div class="mb-6">
                        <div class="flex items-center justify-between mb-4 pb-2 border-b border-gray-200">
                            <div class="flex items-center">
                                <i class="fas fa-envelope text-blue-600 mr-2"></i>
                                <h4 class="text-lg font-semibold text-gray-800 dark:text-white">Correos Electrónicos</h4>
                            </div>
                            <button type="button"
                                class="flex items-center px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-colors duration-200 shadow-sm font-medium text-sm"
                                wire:click="addEmail">
                                <i class="fas fa-plus mr-2"></i>
                                Agregar Correo
                            </button>
                        </div>
                        <div class="space-y-3">
                            @if (isset($emails) && is_array($emails) && !empty($emails))
                                @foreach ($emails as $recIndex => $email)
                                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 border border-gray-200 dark:border-gray-600">
                                        <div class="flex items-center space-x-2">
                                            <div class="flex-1">
                                                <label for="email-{{ $recIndex }}" class="block mb-2 text-xs font-medium text-gray-700 dark:text-gray-300">
                                                    Correo {{ $recIndex + 1 }}
                                                </label>
                                                <input type="email"
                                                    id="email-{{ $recIndex }}"
                                                    class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 block w-full px-4 py-2.5 dark:bg-gray-600 dark:border-gray-500 dark:text-white transition-all duration-200"
                                                    placeholder="correo@ejemplo.com"
                                                    wire:model="emails.{{ $recIndex }}">
                                                @error("emails.$recIndex")
                                                    <span class="flex items-center mt-1 text-xs text-red-600">
                                                        <i class="fas fa-exclamation-circle mr-1"></i>
                                                        {{ $message }}
                                                    </span>
                                                @enderror
                                            </div>
                                            @if ($recIndex > 0)
                                                <div class="flex items-end">
                                                    <button type="button"
                                                        wire:click="deleteEmail({{ $recIndex }})"
                                                        class="px-4 py-2.5 bg-red-500 hover:bg-red-600 text-white rounded-lg transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2"
                                                        wire:loading.attr="disabled"
                                                        title="Eliminar correo">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            @endif
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
                            {{ $companyIdToUpdate ? 'Actualizar' : 'Guardar' }}
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
                Livewire.on('close-modal-company', () => {
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
