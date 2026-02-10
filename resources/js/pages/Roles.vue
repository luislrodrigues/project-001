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

        <div class="py-4 sm:py-6">
            <div class="mb-6">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
                    <div class="mb-4 sm:mb-0">
                        <h2 class="text-xl sm:text-3xl font-bold text-gray-800 flex items-center">
                            <i class="fas fa-user-shield text-blue-600 mr-3"></i>
                            Roles y permisos
                        </h2>
                        <p class="text-gray-500 text-sm mt-1">Gestiona los permisos asignados a cada rol del sistema</p>
                    </div>
                    <router-link
                        v-if="authStore.hasPermission('save role')"
                        :to="{ name: 'roles.create' }"
                        class="flex items-center justify-center w-full sm:w-auto px-5 py-3 sm:py-2.5 bg-blue-500 text-white rounded-lg hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors duration-200 shadow-sm font-medium"
                    >
                        <i class="fas fa-plus mr-2"></i>
                        Crear rol
                    </router-link>
                </div>
            </div>

            <!-- Filtro -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 mb-6">
                <div class="flex flex-col sm:flex-row gap-3">
                    <div class="flex-1">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-search mr-2 text-gray-400"></i>
                            Buscar por nombre de rol
                        </label>
                        <div class="relative">
                            <input
                                v-model="filterSearch"
                                type="text"
                                placeholder="Ej: Administrador, Supervisor..."
                                class="block w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 pl-10 pr-4 py-2.5 transition-all duration-200"
                            />
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-search text-gray-400"></i>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-end gap-2">
                        <button
                            type="button"
                            @click="filterSearch = ''"
                            class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-400 focus:ring-offset-2 transition-colors duration-200 font-medium"
                        >
                            <i class="fas fa-times mr-2"></i>
                            Limpiar
                        </button>
                        <button
                            type="button"
                            @click="fetchRoles"
                            class="px-5 py-2.5 bg-blue-500 hover:bg-blue-600 text-white rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors duration-200 font-medium"
                        >
                            <i class="fas fa-redo mr-2"></i>
                            Actualizar
                        </button>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div v-if="roles.length === 0 && !loading" class="p-12 text-center">
                    <i class="fas fa-user-shield text-gray-300 text-6xl mb-4"></i>
                    <p class="text-gray-500 text-lg">No hay roles configurados</p>
                </div>

                <div v-else class="overflow-x-auto">
                    <table class="min-w-[640px] w-full divide-y divide-gray-200">
                        <caption v-if="filterSearch" class="sr-only">
                            Mostrando {{ sortedRoles.length }} de {{ roles.length }} roles
                        </caption>
                        <thead class="bg-gradient-to-r from-blue-50 to-blue-100">
                            <tr>
                                <th
                                    class="px-6 py-4 text-left text-xs font-semibold text-blue-900 uppercase tracking-wider cursor-pointer hover:bg-blue-200/50 select-none"
                                    @click="toggleSortId"
                                    title="Clic para cambiar orden"
                                >
                                    <div class="flex items-center gap-1">
                                        #
                                        <i v-if="sortOrder === 'desc'" class="fas fa-sort-down text-sm"></i>
                                        <i v-else class="fas fa-sort-up text-sm"></i>
                                    </div>
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-blue-900 uppercase tracking-wider">
                                    <div class="flex items-center">
                                        <i class="fas fa-user-tag mr-2"></i>
                                        Rol
                                    </div>
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-blue-900 uppercase tracking-wider">
                                    <div class="flex items-center">
                                        <i class="fas fa-key mr-2"></i>
                                        Permisos
                                    </div>
                                </th>
                                <th v-if="authStore.hasPermission('edit role')" class="px-6 py-4 text-right text-xs font-semibold text-blue-900 uppercase tracking-wider">
                                    <div class="flex items-center justify-end">
                                        <i class="fas fa-cog mr-2"></i>
                                        Acciones
                                    </div>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="role in sortedRoles" :key="role.id" class="hover:bg-blue-50 transition-colors duration-150">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ role.id }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10 bg-blue-100 rounded-lg flex items-center justify-center mr-4">
                                            <i class="fas fa-user-tag text-blue-600"></i>
                                        </div>
                                        <div class="text-sm font-semibold text-gray-900">
                                            {{ role.name }}
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-3 py-1 text-sm font-medium rounded-full bg-blue-100 text-blue-800">
                                        {{ role.permissions_count }} permisos
                                    </span>
                                </td>
                                <td v-if="authStore.hasPermission('edit role')" class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <router-link
                                        :to="{ name: 'roles.edit', params: { id: role.id } }"
                                        class="inline-flex items-center justify-center px-4 py-2.5 bg-blue-500 text-white rounded-lg hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors font-medium text-sm"
                                    >
                                        <i class="fas fa-key mr-2"></i>
                                        Gestionar permisos
                                    </router-link>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div v-if="filterSearch && filteredRoles.length === 0" class="p-8 text-center border-t border-gray-200">
                    <i class="fas fa-search text-gray-300 text-4xl mb-3"></i>
                    <p class="text-gray-500">Ningún rol coincide con "{{ filterSearch }}"</p>
                    <button
                        type="button"
                        @click="filterSearch = ''"
                        class="mt-3 text-sm text-blue-600 hover:text-blue-800 font-medium"
                    >
                        Limpiar filtro
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useAuthStore } from '../stores/auth';
import api from '../services/api';

const authStore = useAuthStore();
const roles = ref([]);
const loading = ref(true);
const filterSearch = ref('');
const sortOrder = ref('asc'); // asc = menor a mayor, desc = mayor a menor

const filteredRoles = computed(() => {
    const term = (filterSearch.value || '').trim().toLowerCase();
    if (!term) return roles.value;
    return roles.value.filter((role) => role.name && role.name.toLowerCase().includes(term));
});

const sortedRoles = computed(() => {
    const list = [...filteredRoles.value];
    return list.sort((a, b) => {
        const idA = a.id ?? 0;
        const idB = b.id ?? 0;
        return sortOrder.value === 'asc' ? idA - idB : idB - idA;
    });
});

function toggleSortId() {
    sortOrder.value = sortOrder.value === 'asc' ? 'desc' : 'asc';
}

const fetchRoles = async () => {
    loading.value = true;
    try {
        const response = await api.get('/roles');
        roles.value = response.data.data || [];
    } catch (error) {
        console.error('Error al cargar roles:', error);
        roles.value = [];
    } finally {
        loading.value = false;
    }
};

onMounted(fetchRoles);
</script>
