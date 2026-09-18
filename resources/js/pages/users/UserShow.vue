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
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h2 class="text-3xl font-bold text-gray-800 flex items-center">
                            <i class="fas fa-user text-blue-600 mr-3"></i>
                            Detalles del Usuario
                        </h2>
                        <p class="text-gray-500 text-sm mt-1">Información completa del usuario</p>
                    </div>
                    <div class="flex items-center space-x-2">
                        <router-link
                            v-if="authStore.hasPermission('edit user')"
                            :to="{ name: 'users.edit', params: { id: user.id } }"
                            class="text-yellow-600 hover:text-yellow-900 p-2 hover:bg-yellow-100 rounded-lg transition-colors"
                            title="Editar"
                        >
                            <i class="fas fa-edit text-xl"></i>
                        </router-link>
                        <router-link
                            :to="{ name: 'users' }"
                            class="text-gray-600 hover:text-gray-900 p-2 hover:bg-gray-100 rounded-lg transition-colors"
                            title="Volver"
                        >
                            <i class="fas fa-arrow-left text-xl"></i>
                        </router-link>
                    </div>
                </div>
            </div>

            <!-- User Details -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-gray-50 rounded-lg p-4">
                        <label class="flex items-center mb-2 text-sm font-medium text-gray-500">
                            <i class="fas fa-user mr-2 text-blue-600"></i>
                            Nombre
                        </label>
                        <p class="text-lg font-semibold text-gray-900">{{ user.name }}</p>
                    </div>

                    <div class="bg-gray-50 rounded-lg p-4">
                        <label class="flex items-center mb-2 text-sm font-medium text-gray-500">
                            <i class="fas fa-envelope mr-2 text-blue-600"></i>
                            Correo
                        </label>
                        <p class="text-lg font-semibold text-gray-900">{{ user.email }}</p>
                    </div>

                    <div class="bg-gray-50 rounded-lg p-4">
                        <label class="flex items-center mb-2 text-sm font-medium text-gray-500">
                            <i class="fas fa-building mr-2 text-blue-600"></i>
                            Empresa
                        </label>
                        <p class="text-lg font-semibold text-gray-900">{{ user.company?.name || 'N/A' }}</p>
                    </div>

                    <div class="bg-gray-50 rounded-lg p-4">
                        <label class="flex items-center mb-2 text-sm font-medium text-gray-500">
                            <i class="fas fa-briefcase mr-2 text-blue-600"></i>
                            Rol
                        </label>
                        <p v-if="user.role" class="text-lg font-semibold text-gray-900">
                            <span class="px-3 py-1 text-sm font-medium rounded-full bg-purple-100 text-purple-800">
                                {{ user.role.name }}
                            </span>
                        </p>
                        <p v-else class="text-lg font-semibold text-gray-400">Sin rol</p>
                    </div>

                    <div class="bg-gray-50 rounded-lg p-4">
                        <label class="flex items-center mb-2 text-sm font-medium text-gray-500">
                            <i class="fas fa-briefcase mr-2 text-blue-600"></i>
                            Cargo
                        </label>
                        <p class="text-lg font-semibold text-gray-900">{{ user.workstation || 'N/A' }}</p>
                    </div>

                    <div class="bg-gray-50 rounded-lg p-4">
                        <label class="flex items-center mb-2 text-sm font-medium text-gray-500">
                            <i class="fas fa-info-circle mr-2 text-blue-600"></i>
                            Estado
                        </label>
                        <p>
                            <span
                                :class="user.status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'"
                                class="px-3 py-1 text-sm font-medium rounded-full"
                            >
                                {{ user.status === 'active' ? 'Activo' : 'Inactivo' }}
                            </span>
                        </p>
                    </div>

                    <div class="bg-gray-50 rounded-lg p-4 md:col-span-2">
                        <label class="flex items-center mb-2 text-sm font-medium text-gray-500">
                            <i class="fas fa-key mr-2 text-blue-600"></i>
                            Contraseña
                        </label>
                        <div v-if="user.raw_password" class="flex items-center gap-3">
                            <p class="text-lg font-semibold text-gray-900 font-mono tracking-wide break-all">
                                {{ showPassword ? user.raw_password : '••••••••' }}
                            </p>
                            <button
                                type="button"
                                @click="showPassword = !showPassword"
                                class="text-gray-500 hover:text-blue-600 p-2 hover:bg-blue-100 rounded-lg transition-colors"
                                :title="showPassword ? 'Ocultar contraseña' : 'Mostrar contraseña'"
                            >
                                <i :class="showPassword ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                            </button>
                            <button
                                type="button"
                                @click="copyPassword"
                                class="text-gray-500 hover:text-blue-600 p-2 hover:bg-blue-100 rounded-lg transition-colors"
                                title="Copiar contraseña"
                            >
                                <i class="fas fa-copy"></i>
                            </button>
                        </div>
                        <p v-else class="text-lg font-semibold text-gray-400">No disponible</p>
                    </div>
                </div>

                <div v-if="user.created_at || user.updated_at" class="mt-6 pt-6 border-t border-gray-200">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-500">
                        <div v-if="user.created_at">
                            <i class="fas fa-calendar-plus mr-2"></i>
                            Creado: {{ formatDate(user.created_at) }}
                        </div>
                        <div v-if="user.updated_at">
                            <i class="fas fa-calendar-edit mr-2"></i>
                            Actualizado: {{ formatDate(user.updated_at) }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import { useAuthStore } from '../../stores/auth';
import api from '../../services/api';
import Swal from 'sweetalert2';

const router = useRouter();
const route = useRoute();
const authStore = useAuthStore();

const user = ref({});
const loading = ref(true);
const showPassword = ref(false);

const loadUser = async () => {
    loading.value = true;
    try {
        const response = await api.get(`/users/${route.params.id}`, {
            params: { with_password: 1 }
        });
        user.value = response.data.data;
    } catch (error) {
        Swal.fire('Error', 'Error al cargar el usuario', 'error');
        router.push({ name: 'users' });
    } finally {
        loading.value = false;
    }
};

const copyPassword = async () => {
    if (!user.value.raw_password) return;
    try {
        if (navigator.clipboard && window.isSecureContext) {
            await navigator.clipboard.writeText(user.value.raw_password);
        } else {
            const textarea = document.createElement('textarea');
            textarea.value = user.value.raw_password;
            textarea.style.position = 'fixed';
            textarea.style.opacity = '0';
            document.body.appendChild(textarea);
            textarea.select();
            document.execCommand('copy');
            document.body.removeChild(textarea);
        }
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'success',
            title: 'Contraseña copiada',
            showConfirmButton: false,
            timer: 1500
        });
    } catch (error) {
        Swal.fire('Error', 'No se pudo copiar la contraseña', 'error');
    }
};

const formatDate = (dateString) => {
    if (!dateString) return 'N/A';
    const date = new Date(dateString);
    return date.toLocaleString('es-ES', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
};

onMounted(() => {
    if (!authStore.hasPermission('detalle user')) {
        router.push({ name: 'users' });
        return;
    }
    loadUser();
});
</script>
