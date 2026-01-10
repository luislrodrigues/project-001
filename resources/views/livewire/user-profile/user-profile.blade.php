<div>
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center space-x-4 mb-2">
                <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-2xl flex items-center justify-center text-white text-2xl font-bold shadow-lg">
                    {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-gray-800">Mi Perfil</h1>
                    <p class="text-gray-500">Gestiona tu información personal y configuración</p>
                </div>
            </div>
        </div>

        <!-- Profile Information Card -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden mb-6">
            <div class="bg-gradient-to-r from-blue-500 to-indigo-600 px-6 py-4">
                <div class="flex items-center space-x-3">
                    <i class="fas fa-user-circle text-white text-2xl"></i>
                    <h2 class="text-xl font-bold text-white">Información Personal</h2>
                </div>
            </div>

            <div class="p-6">
                <form wire:submit.prevent="updateProfile">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Name -->
                        <div>
                            <label class="flex items-center mb-2 text-sm font-semibold text-gray-700">
                                <i class="fas fa-user mr-2 text-blue-600"></i>
                                Nombre Completo
                                <span class="text-red-500 ml-1">*</span>
                            </label>
                            <input type="text"
                                   wire:model="name"
                                   class="w-full bg-gray-50 border-2 border-gray-200 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 block px-4 py-3 transition-all duration-200"
                                   placeholder="Tu nombre completo">
                            @error('name')
                                <span class="flex items-center mt-2 text-xs text-red-600">
                                    <i class="fas fa-exclamation-circle mr-1"></i>
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div>
                            <label class="flex items-center mb-2 text-sm font-semibold text-gray-700">
                                <i class="fas fa-envelope mr-2 text-blue-600"></i>
                                Correo Electrónico
                                <span class="text-red-500 ml-1">*</span>
                            </label>
                            <input type="email"
                                   wire:model="email"
                                   class="w-full bg-gray-50 border-2 border-gray-200 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 block px-4 py-3 transition-all duration-200"
                                   placeholder="tu@email.com">
                            @error('email')
                                <span class="flex items-center mt-2 text-xs text-red-600">
                                    <i class="fas fa-exclamation-circle mr-1"></i>
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>

                        <!-- Workstation -->
                        <div>
                            <label class="flex items-center mb-2 text-sm font-semibold text-gray-700">
                                <i class="fas fa-briefcase mr-2 text-blue-600"></i>
                                Cargo / Puesto
                            </label>
                            <input type="text"
                                   wire:model="workstation"
                                   class="w-full bg-gray-50 border-2 border-gray-200 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 block px-4 py-3 transition-all duration-200"
                                   placeholder="Tu cargo o puesto de trabajo">
                            @error('workstation')
                                <span class="flex items-center mt-2 text-xs text-red-600">
                                    <i class="fas fa-exclamation-circle mr-1"></i>
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>

                        <!-- Company -->
                        <div>
                            <label class="flex items-center mb-2 text-sm font-semibold text-gray-700">
                                <i class="fas fa-building mr-2 text-blue-600"></i>
                                Empresa
                            </label>
                            <select wire:model="company_id"
                                    class="w-full bg-gray-50 border-2 border-gray-200 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 block px-4 py-3 transition-all duration-200">
                                <option value="">Selecciona una empresa</option>
                                @foreach($companies as $company)
                                    <option value="{{ $company->id }}">{{ $company->name }}</option>
                                @endforeach
                            </select>
                            @error('company_id')
                                <span class="flex items-center mt-2 text-xs text-red-600">
                                    <i class="fas fa-exclamation-circle mr-1"></i>
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex justify-end mt-6 pt-6 border-t border-gray-200">
                        <button type="submit"
                                wire:loading.attr="disabled"
                                class="flex items-center px-6 py-3 bg-gradient-to-r from-blue-500 to-indigo-600 text-white rounded-lg hover:from-blue-600 hover:to-indigo-700 transition-all duration-200 font-semibold shadow-md hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                            <i class="fas fa-save mr-2"></i>
                            <span wire:loading.remove wire:target="updateProfile">Guardar Cambios</span>
                            <span wire:loading wire:target="updateProfile">Guardando...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Change Password Card -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
            <div class="bg-gradient-to-r from-orange-500 to-red-600 px-6 py-4">
                <div class="flex items-center space-x-3">
                    <i class="fas fa-lock text-white text-2xl"></i>
                    <h2 class="text-xl font-bold text-white">Cambiar Contraseña</h2>
                </div>
            </div>

            <div class="p-6">
                <form wire:submit.prevent="updatePassword">
                    <div class="grid grid-cols-1 md:grid-cols-1 gap-6 max-w-2xl">
                        <!-- Current Password -->
                        <div>
                            <label class="flex items-center mb-2 text-sm font-semibold text-gray-700">
                                <i class="fas fa-key mr-2 text-orange-600"></i>
                                Contraseña Actual
                                <span class="text-red-500 ml-1">*</span>
                            </label>
                            <input type="password"
                                   wire:model="current_password"
                                   class="w-full bg-gray-50 border-2 border-gray-200 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 block px-4 py-3 transition-all duration-200"
                                   placeholder="Ingresa tu contraseña actual">
                            @error('current_password')
                                <span class="flex items-center mt-2 text-xs text-red-600">
                                    <i class="fas fa-exclamation-circle mr-1"></i>
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>

                        <!-- New Password -->
                        <div>
                            <label class="flex items-center mb-2 text-sm font-semibold text-gray-700">
                                <i class="fas fa-lock mr-2 text-orange-600"></i>
                                Nueva Contraseña
                                <span class="text-red-500 ml-1">*</span>
                            </label>
                            <input type="password"
                                   wire:model="password"
                                   class="w-full bg-gray-50 border-2 border-gray-200 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 block px-4 py-3 transition-all duration-200"
                                   placeholder="Mínimo 8 caracteres">
                            @error('password')
                                <span class="flex items-center mt-2 text-xs text-red-600">
                                    <i class="fas fa-exclamation-circle mr-1"></i>
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>

                        <!-- Confirm Password -->
                        <div>
                            <label class="flex items-center mb-2 text-sm font-semibold text-gray-700">
                                <i class="fas fa-lock mr-2 text-orange-600"></i>
                                Confirmar Nueva Contraseña
                                <span class="text-red-500 ml-1">*</span>
                            </label>
                            <input type="password"
                                   wire:model="password_confirmation"
                                   class="w-full bg-gray-50 border-2 border-gray-200 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 block px-4 py-3 transition-all duration-200"
                                   placeholder="Confirma tu nueva contraseña">
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex justify-end mt-6 pt-6 border-t border-gray-200">
                        <button type="submit"
                                wire:loading.attr="disabled"
                                class="flex items-center px-6 py-3 bg-gradient-to-r from-orange-500 to-red-600 text-white rounded-lg hover:from-orange-600 hover:to-red-700 transition-all duration-200 font-semibold shadow-md hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2">
                            <i class="fas fa-key mr-2"></i>
                            <span wire:loading.remove wire:target="updatePassword">Cambiar Contraseña</span>
                            <span wire:loading wire:target="updatePassword">Cambiando...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Account Info -->
        <div class="mt-6 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl p-6 border border-blue-100">
            <div class="flex items-start space-x-4">
                <div class="bg-blue-500 rounded-full p-3">
                    <i class="fas fa-info-circle text-white"></i>
                </div>
                <div class="flex-1">
                    <h3 class="font-semibold text-gray-800 mb-2">Información de la Cuenta</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-600">
                        <div>
                            <span class="font-medium">Rol:</span>
                            <span class="ml-2 px-2 py-1 bg-blue-100 text-blue-800 rounded-md text-xs font-semibold">
                                {{ auth()->user()->getRoleNames()->first() ?? 'Sin rol asignado' }}
                            </span>
                        </div>
                        <div>
                            <span class="font-medium">Estado:</span>
                            @if(auth()->user()->status == 'active')
                                <span class="ml-2 px-2 py-1 bg-green-100 text-green-800 rounded-md text-xs font-semibold">
                                    <i class="fas fa-check-circle mr-1"></i>Activo
                                </span>
                            @else
                                <span class="ml-2 px-2 py-1 bg-red-100 text-red-800 rounded-md text-xs font-semibold">
                                    <i class="fas fa-times-circle mr-1"></i>Inactivo
                                </span>
                            @endif
                        </div>
                        <div>
                            <span class="font-medium">Fecha de Registro:</span>
                            <span class="ml-2">{{ auth()->user()->created_at->format('d/m/Y') }}</span>
                        </div>
                        <div>
                            <span class="font-medium">Última Actualización:</span>
                            <span class="ml-2">{{ auth()->user()->updated_at->format('d/m/Y H:i') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

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
        });
    </script>
    @endpush
</div>
