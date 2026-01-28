<template>
    <div class="container mx-auto px-4 sm:px-8">
        <div v-if="loading" class="fixed top-0 left-0 w-screen h-screen flex items-center justify-center z-50 bg-black bg-opacity-30 backdrop-blur-sm">
            <div class="flex flex-col items-center justify-center h-full">
                <svg class="animate-spin h-16 w-16 text-blue-600 mr-3 mb-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="10" stroke-opacity="0.25" stroke-width="2" stroke="currentColor" fill="none"></circle>
                    <path d="M12 2a10 10 0 00-9.5 7.5M12 2c2.5 0 5 1 7 2.5" stroke="currentColor" stroke-width="2" fill="none"></path>
                </svg>
                <p class="text-gray-700 font-medium">Cargando...</p>
            </div>
        </div>

        <div v-else class="py-6">
            <!-- Header -->
            <div class="mb-6">
                <h2 class="text-3xl font-bold text-gray-800 flex items-center">
                    <i class="fas fa-user-circle text-blue-600 mr-3"></i>
                    Mi Perfil
                </h2>
                <p class="text-gray-500 text-sm mt-1">Administra tu información personal</p>
            </div>

            <!-- Profile Content -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Información del Perfil -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                        <div class="flex items-center mb-6 pb-4 border-b border-gray-200">
                            <i class="fas fa-user-edit text-blue-600 mr-2"></i>
                            <h4 class="text-lg font-semibold text-gray-800">Información Personal</h4>
                        </div>

                        <form @submit.prevent="updateProfile" class="space-y-6">
                            <!-- Nombre -->
                            <div class="bg-gray-50 rounded-lg p-4">
                                <label class="flex items-center mb-2 text-sm font-medium text-gray-700">
                                    <i class="fas fa-user mr-2 text-blue-600"></i>
                                    Nombre
                                    <span class="text-red-500 ml-1">*</span>
                                </label>
                                <input
                                    v-model="form.name"
                                    type="text"
                                    required
                                    class="block w-full bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 px-4 py-2.5 transition-all duration-200"
                                    placeholder="Ingresa tu nombre"
                                />
                                <p v-if="errors.name" class="mt-1 text-sm text-red-600">{{ errors.name }}</p>
                            </div>

                            <!-- Email -->
                            <div class="bg-gray-50 rounded-lg p-4">
                                <label class="flex items-center mb-2 text-sm font-medium text-gray-700">
                                    <i class="fas fa-envelope mr-2 text-blue-600"></i>
                                    Correo Electrónico
                                    <span class="text-red-500 ml-1">*</span>
                                </label>
                                <input
                                    v-model="form.email"
                                    type="email"
                                    required
                                    class="block w-full bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 px-4 py-2.5 transition-all duration-200"
                                    placeholder="correo@ejemplo.com"
                                />
                                <p v-if="errors.email" class="mt-1 text-sm text-red-600">{{ errors.email }}</p>
                            </div>

                            <!-- Estación de Trabajo -->
                            <div class="bg-gray-50 rounded-lg p-4">
                                <label class="flex items-center mb-2 text-sm font-medium text-gray-700">
                                    <i class="fas fa-desktop mr-2 text-blue-600"></i>
                                    Estación de Trabajo
                                </label>
                                <input
                                    v-model="form.workstation"
                                    type="text"
                                    class="block w-full bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 px-4 py-2.5 transition-all duration-200"
                                    placeholder="Ingresa tu estación de trabajo"
                                />
                                <p v-if="errors.workstation" class="mt-1 text-sm text-red-600">{{ errors.workstation }}</p>
                            </div>

                            <!-- Contraseña -->
                            <div class="bg-gray-50 rounded-lg p-4">
                                <label class="flex items-center mb-2 text-sm font-medium text-gray-700">
                                    <i class="fas fa-lock mr-2 text-blue-600"></i>
                                    Nueva Contraseña
                                    <span class="text-gray-400 text-xs ml-2">(Dejar vacío para no cambiar)</span>
                                </label>
                                <div class="relative">
                                    <input
                                        v-model="form.password"
                                        :type="showPassword ? 'text' : 'password'"
                                        class="block w-full bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 px-4 py-2.5 pr-10 transition-all duration-200"
                                        placeholder="Mínimo 8 caracteres"
                                    />
                                    <button
                                        type="button"
                                        @click="showPassword = !showPassword"
                                        class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600"
                                    >
                                        <i :class="showPassword ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                                    </button>
                                </div>
                                <p v-if="errors.password" class="mt-1 text-sm text-red-600">{{ errors.password }}</p>
                            </div>

                            <!-- Botones -->
                            <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200">
                                <button
                                    type="submit"
                                    :disabled="saving"
                                    class="px-6 py-2.5 bg-blue-500 hover:bg-blue-600 text-white rounded-lg transition-colors font-medium shadow-sm disabled:opacity-50 flex items-center"
                                >
                                    <i class="fas fa-save mr-2"></i>
                                    {{ saving ? 'Guardando...' : 'Guardar Cambios' }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Información Adicional -->
                <div class="space-y-6">
                    <!-- Información de la Cuenta -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                        <div class="flex items-center mb-4 pb-2 border-b border-gray-200">
                            <i class="fas fa-info-circle text-blue-600 mr-2"></i>
                            <h4 class="text-lg font-semibold text-gray-800">Información de la Cuenta</h4>
                        </div>
                        <div class="space-y-4">
                            <div>
                                <label class="text-xs font-medium text-gray-500 uppercase tracking-wider">Empresa</label>
                                <p class="text-sm font-semibold text-gray-900 mt-1">
                                    {{ userProfile?.company?.name || 'No asignada' }}
                                </p>
                            </div>
                            <div>
                                <label class="text-xs font-medium text-gray-500 uppercase tracking-wider">Rol</label>
                                <div class="mt-1 flex flex-wrap gap-2">
                                    <span
                                        v-for="role in userProfile?.roles"
                                        :key="role"
                                        class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-semibold"
                                    >
                                        {{ role }}
                                    </span>
                                    <span v-if="!userProfile?.roles?.length" class="text-sm text-gray-500">Sin rol asignado</span>
                                </div>
                            </div>
                            <div>
                                <label class="text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</label>
                                <p class="text-sm font-semibold text-gray-900 mt-1">
                                    <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs">
                                        Activo
                                    </span>
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Foto de Perfil -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                        <div class="flex items-center mb-4 pb-2 border-b border-gray-200">
                            <i class="fas fa-camera text-blue-600 mr-2"></i>
                            <h4 class="text-lg font-semibold text-gray-800">Foto de Perfil</h4>
                        </div>
                        <div class="flex flex-col items-center">
                            <div class="relative">
                                <img
                                    :src="userProfile?.profile_photo_url || `https://ui-avatars.com/api/?name=${encodeURIComponent(userProfile?.name || 'User')}&background=3b82f6&color=fff&size=128`"
                                    alt="Foto de perfil"
                                    class="w-32 h-32 rounded-full border-4 border-gray-200 object-cover"
                                />
                            </div>
                            <p class="text-xs text-gray-500 mt-4 text-center">
                                La foto de perfil se gestiona desde Jetstream
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, reactive } from 'vue';
import { useAuthStore } from '../stores/auth';
import api from '../services/api';
import Swal from 'sweetalert2';

const authStore = useAuthStore();

const loading = ref(true);
const saving = ref(false);
const showPassword = ref(false);
const errors = ref({});
const userProfile = ref(null);

const form = reactive({
    name: '',
    email: '',
    workstation: '',
    password: '',
});

onMounted(async () => {
    await loadProfile();
});

const loadProfile = async () => {
    loading.value = true;
    try {
        const response = await api.get('/user');
        userProfile.value = response.data;
        
        form.name = userProfile.value.name || '';
        form.email = userProfile.value.email || '';
        form.workstation = userProfile.value.workstation || '';
    } catch (error) {
        Swal.fire('Error', 'Error al cargar el perfil', 'error');
    } finally {
        loading.value = false;
    }
};

const updateProfile = async () => {
    saving.value = true;
    errors.value = {};

    try {
        const data = {
            name: form.name,
            email: form.email,
            workstation: form.workstation,
        };

        if (form.password) {
            data.password = form.password;
        }

        const response = await api.put('/profile', data);
        
        // Actualizar el store con los nuevos datos
        authStore.setUser({
            ...authStore.user,
            ...response.data.user,
        });

        Swal.fire('Éxito', 'Perfil actualizado correctamente', 'success');
        
        // Recargar el perfil para obtener datos actualizados
        await loadProfile();
        
        // Limpiar el campo de contraseña
        form.password = '';
    } catch (error) {
        if (error.response?.data?.errors) {
            errors.value = error.response.data.errors;
        }
        const message = error.response?.data?.message || 'Error al actualizar el perfil';
        Swal.fire('Error', message, 'error');
    } finally {
        saving.value = false;
    }
};
</script>
