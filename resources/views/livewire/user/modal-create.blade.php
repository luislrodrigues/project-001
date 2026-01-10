<div wire:ignore.self id="modalCreate" tabindex="-1" aria-hidden="true"
    class="hidden overflow-y-auto overflow-x-hidden fixed inset-0 z-50 flex items-center justify-center">
    <!-- Overlay con backdrop blur -->
    <div class="fixed inset-0 bg-gray-900 bg-opacity-75 backdrop-blur-sm transition-opacity z-40"
         onclick="document.getElementById('modalCreate').classList.add('hidden')"></div>
    
    <div wire:loading wire:target="saveUser,updateUser"
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
    <div class="relative p-4 w-full max-w-2xl z-50">
        <!-- Modal content -->
        <div class="relative bg-white rounded-xl shadow-2xl dark:bg-gray-800 overflow-hidden">
            <!-- Modal header con gradiente -->
            <div class="flex items-center justify-between px-6 py-4 bg-gradient-to-r from-blue-500 to-blue-600 rounded-t-xl">
                <div class="flex items-center space-x-3">
                    <div class="bg-white bg-opacity-20 rounded-lg p-2">
                        <i class="fas {{ $userIdToUpdate ? 'fa-edit' : 'fa-plus-circle' }} text-white text-xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-white">
                        {{ $userIdToUpdate ? 'Editar Usuario' : 'Crear Nuevo Usuario' }}
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
                    @if ($userIdToUpdate) wire:submit.prevent="updateUser" @else wire:submit.prevent="saveUser" @endif>
                    <div class="mb-6">
                        <div class="flex items-center mb-4 pb-2 border-b border-gray-200">
                            <i class="fas fa-info-circle text-blue-600 mr-2"></i>
                            <h4 class="text-lg font-semibold text-gray-800 dark:text-white">Información del Usuario</h4>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                <label for="name" class="flex items-center mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                    <i class="fas fa-user mr-2 text-blue-600"></i>
                                    Nombre
                                    <span class="text-red-500 ml-1">*</span>
                                </label>
                                <input type="text" id="name"
                                    class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 block w-full px-4 py-3 dark:bg-gray-600 dark:border-gray-500 dark:text-white transition-all duration-200"
                                    placeholder="Ingresar nombre" wire:model.defer="name">
                                @error('name')
                                    <span class="flex items-center mt-1 text-xs text-red-600">
                                        <i class="fas fa-exclamation-circle mr-1"></i>
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>
                            
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                <label for="email" class="flex items-center mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                    <i class="fas fa-envelope mr-2 text-blue-600"></i>
                                    Correo
                                    <span class="text-red-500 ml-1">*</span>
                                </label>
                                <input type="email" id="email"
                                    class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 block w-full px-4 py-3 dark:bg-gray-600 dark:border-gray-500 dark:text-white transition-all duration-200"
                                    placeholder="correo@ejemplo.com" wire:model.defer="email">
                                @error('email')
                                    <span class="flex items-center mt-1 text-xs text-red-600">
                                        <i class="fas fa-exclamation-circle mr-1"></i>
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>
                            
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                <label for="company_id" class="flex items-center mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                    <i class="fas fa-building mr-2 text-blue-600"></i>
                                    Empresa
                                    <span class="text-red-500 ml-1">*</span>
                                </label>
                                <select id="company_id" 
                                        class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 block w-full px-4 py-3 dark:bg-gray-600 dark:border-gray-500 dark:text-white transition-all duration-200"
                                        wire:model.defer="company_id">
                                    <option value="">Seleccione una compañía</option>
                                    @foreach($companies as $company)
                                        <option value="{{ $company->id }}">{{ $company->name }}</option>
                                    @endforeach
                                </select>
                                @error('company_id')
                                    <span class="flex items-center mt-1 text-xs text-red-600">
                                        <i class="fas fa-exclamation-circle mr-1"></i>
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>
                            
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                <label for="role_id" class="flex items-center mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                    <i class="fas fa-briefcase mr-2 text-blue-600"></i>
                                    Cargo
                                    <span class="text-red-500 ml-1">*</span>
                                </label>
                                <select id="role_id" 
                                        class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 block w-full px-4 py-3 dark:bg-gray-600 dark:border-gray-500 dark:text-white transition-all duration-200"
                                        wire:model.defer="role_id">
                                    <option value="">Seleccione un cargo</option>
                                    @foreach($roles as $role)
                                        <option value="{{ $role->id }}">{{ $role->name }}</option>
                                    @endforeach
                                </select>
                                @error('role_id')
                                    <span class="flex items-center mt-1 text-xs text-red-600">
                                        <i class="fas fa-exclamation-circle mr-1"></i>
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>
                            
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                <label for="password" class="flex items-center mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                    <i class="fas fa-lock mr-2 text-blue-600"></i>
                                    Contraseña
                                    @if(!$userIdToUpdate)
                                        <span class="text-red-500 ml-1">*</span>
                                    @else
                                        <span class="ml-2 text-xs text-gray-500">(dejar vacío para no cambiar)</span>
                                    @endif
                                </label>
                                <div class="relative">
                                    <input type="password" id="password"
                                        class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 block w-full px-4 py-3 pr-12 dark:bg-gray-600 dark:border-gray-500 dark:text-white transition-all duration-200"
                                        placeholder="Ingresar contraseña" wire:model.defer="password">
                                    <button type="button" id="toggle-password"
                                        class="absolute inset-y-0 right-0 flex items-center pr-4 text-gray-500 hover:text-gray-700 focus:outline-none">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor" class="w-5 h-5" id="eye-icon">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M15.75 12c0 2.071-1.679 3.75-3.75 3.75S8.25 14.071 8.25 12s1.679-3.75 3.75-3.75 3.75 1.679 3.75 3.75z" />
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M2.25 12c2.11 3.472 5.398 5.25 9 5.25s6.89-1.778 9-5.25c-2.11-3.472-5.398-5.25-9-5.25S4.36 8.528 2.25 12z" />
                                        </svg>
                                    </button>
                                </div>
                                @error('password')
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
                            {{ $userIdToUpdate ? 'Actualizar' : 'Guardar' }}
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
            const passwordInput = document.getElementById('password');
            const togglePassword = document.getElementById('toggle-password');
            const eyeIcon = document.getElementById('eye-icon');
            
            if (!modalCreate) return;

            document.addEventListener('livewire:initialized', function() {
                Livewire.on('close-modal-user', () => {
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

            if (togglePassword && passwordInput && eyeIcon) {
                togglePassword.addEventListener('click', () => {
                    const type = passwordInput.type === 'password' ? 'text' : 'password';
                    passwordInput.type = type;

                    if (type === 'text') {
                        eyeIcon.innerHTML = `
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223C2.845 9.528 2.25 11.24 2.25 12c2.11 3.472 5.398 5.25 9 5.25a10.923 10.923 0 004.782-1.047" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 12c0 2.071-1.679 3.75-3.75 3.75S8.25 14.071 8.25 12s1.679-3.75 3.75-3.75 3.75 1.679 3.75 3.75z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.35 19.65L19.65 4.35" />
                        `;
                    } else {
                        eyeIcon.innerHTML = `
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 12c0 2.071-1.679 3.75-3.75 3.75S8.25 14.071 8.25 12s1.679-3.75 3.75-3.75 3.75 1.679 3.75 3.75z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12c2.11 3.472 5.398 5.25 9 5.25s6.89-1.778 9-5.25c-2.11-3.472-5.398-5.25-9-5.25S4.36 8.528 2.25 12z" />
                        `;
                    }
                });
            }
        });
    </script>
@endpush
