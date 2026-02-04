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
            <!-- Header Section -->
            <div class="mb-6">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
                    <div class="mb-4 sm:mb-0">
                        <h2 class="text-xl sm:text-3xl font-bold text-gray-800 flex items-center">
                            <i class="fas fa-users text-blue-600 mr-3"></i>
                            Usuarios
                        </h2>
                        <p class="text-gray-500 text-sm mt-1">Gestiona los usuarios del sistema</p>
                    </div>
                    <router-link
                        v-if="authStore.hasPermission('save user')"
                        :to="{ name: 'users.create' }"
                        class="flex items-center justify-center w-full sm:w-auto px-5 py-3 sm:py-2.5 bg-blue-500 text-white rounded-lg hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors duration-200 shadow-sm font-medium"
                    >
                        <i class="fas fa-plus mr-2"></i>
                        Crear Usuario
                    </router-link>
                </div>

                <!-- Search Section -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                    <div class="flex flex-col sm:flex-row gap-3">
                        <div class="flex-1">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-building mr-2 text-gray-400"></i>
                                Empresa
                            </label>
                            <select
                                v-model="filters.company_id"
                                @change="fetchUsers"
                                class="block w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 px-4 py-2.5 transition-all duration-200"
                            >
                                <option value="">Todas</option>
                                <option v-for="company in companies" :key="company.id" :value="company.id">
                                    {{ company.name }}
                                </option>
                            </select>
                        </div>
                        <div class="flex-1">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-filter mr-2 text-gray-400"></i>
                                Estado
                            </label>
                            <select
                                v-model="filters.status"
                                @change="fetchUsers"
                                class="block w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 px-4 py-2.5 transition-all duration-200"
                            >
                                <option value="">Todos</option>
                                <option value="active">Activo</option>
                                <option value="inactive">Inactivo</option>
                            </select>
                        </div>
                        <div class="flex-1">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-search mr-2 text-gray-400"></i>
                                Buscar usuario
                            </label>
                            <div class="relative">
                                <input
                                    v-model="filters.search"
                                    type="text"
                                    placeholder="Buscar por nombre o email..."
                                    class="block w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 pl-10 pr-4 py-2.5 transition-all duration-200"
                                    @input="debounceSearch"
                                />
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-search text-gray-400"></i>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-end">
                            <button
                                @click="fetchUsers"
                                class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-400 focus:ring-offset-2 transition-colors duration-200 font-medium"
                            >
                                <i class="fas fa-redo mr-2"></i>
                                Actualizar
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Table Section -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div v-if="users.length === 0 && !loading" class="p-8 text-center">
                    <i class="fas fa-users text-gray-300 text-6xl mb-4"></i>
                    <p class="text-gray-500 text-lg">No hay usuarios registrados</p>
                </div>

                <div v-else class="overflow-x-auto">
                    <table class="min-w-[640px] w-full divide-y divide-gray-200">
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
                                        <i class="fas fa-building mr-2"></i>
                                        Empresa
                                    </div>
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-blue-900 uppercase tracking-wider">
                                    <div class="flex items-center">
                                        <i class="fas fa-user mr-2"></i>
                                        Nombre
                                    </div>
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-blue-900 uppercase tracking-wider">
                                    <div class="flex items-center">
                                        <i class="fas fa-briefcase mr-2"></i>
                                        Rol
                                    </div>
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-blue-900 uppercase tracking-wider">
                                    <div class="flex items-center">
                                        <i class="fas fa-envelope mr-2"></i>
                                        Correo
                                    </div>
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-blue-900 uppercase tracking-wider">
                                    <div class="flex items-center">
                                        <i class="fas fa-info-circle mr-2"></i>
                                        Estado
                                    </div>
                                </th>
                                <th v-if="authStore.hasPermission('edit user') || authStore.hasPermission('delete user') || authStore.hasPermission('inactive user')" class="px-6 py-4 text-right text-xs font-semibold text-blue-900 uppercase tracking-wider">
                                    <div class="flex items-center justify-end">
                                        <i class="fas fa-cog mr-2"></i>
                                        Acciones
                                    </div>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="user in users" :key="user.id" class="hover:bg-blue-50 transition-colors duration-150">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ user.id }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ user.company && user.company.name ? user.company.name : 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10 bg-blue-100 rounded-full flex items-center justify-center mr-4">
                                            <i class="fas fa-user text-blue-600"></i>
                                        </div>
                                        <div class="text-sm font-semibold text-gray-900">
                                            {{ user.name }}
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <span v-if="user.role" class="px-2 py-1 text-xs font-medium rounded-full bg-purple-100 text-purple-800">
                                        {{ user.role.name }}
                                    </span>
                                    <span v-else class="text-gray-400">Sin rol</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ user.email }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        :class="user.status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'"
                                        class="px-2 py-1 text-xs font-medium rounded-full"
                                    >
                                        {{ user.status === 'active' ? 'Activo' : 'Inactivo' }}
                                    </span>
                                </td>
                                <td v-if="authStore.hasPermission('edit user') || authStore.hasPermission('delete user') || authStore.hasPermission('inactive user')" class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex items-center justify-end space-x-2">
                                        <router-link
                                            :to="{ name: 'users.show', params: { id: user.id } }"
                                            class="text-blue-600 hover:text-blue-900 p-2 hover:bg-blue-100 rounded-lg transition-colors"
                                            title="Ver"
                                        >
                                            <i class="fas fa-eye"></i>
                                        </router-link>
                                        <router-link
                                            v-if="authStore.hasPermission('edit user')"
                                            :to="{ name: 'users.edit', params: { id: user.id } }"
                                            class="text-yellow-600 hover:text-yellow-900 p-2 hover:bg-yellow-100 rounded-lg transition-colors"
                                            title="Editar"
                                        >
                                            <i class="fas fa-edit"></i>
                                        </router-link>
                                        <button
                                            v-if="authStore.hasPermission('inactive user')"
                                            @click="toggleStatus(user)"
                                            :class="user.status === 'active' ? 'text-orange-600 hover:text-orange-900 hover:bg-orange-100' : 'text-green-600 hover:text-green-900 hover:bg-green-100'"
                                            class="p-2 rounded-lg transition-colors"
                                            :title="user.status === 'active' ? 'Desactivar' : 'Activar'"
                                        >
                                            <i :class="user.status === 'active' ? 'fas fa-ban' : 'fas fa-check'"></i>
                                        </button>
                                        <button
                                            v-if="authStore.hasPermission('delete user')"
                                            @click="deleteUser(user)"
                                            class="text-red-600 hover:text-red-900 p-2 hover:bg-red-100 rounded-lg transition-colors"
                                            title="Eliminar"
                                        >
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div v-if="pagination && pagination.last_page > 1" class="px-6 py-4 border-t border-gray-200 flex items-center justify-between">
                    <div class="text-sm text-gray-700">
                        Mostrando {{ pagination.from }} a {{ pagination.to }} de {{ pagination.total }} resultados
                    </div>
                    <div class="flex space-x-2">
                        <button
                            @click="changePage(pagination.current_page - 1)"
                            :disabled="pagination.current_page === 1"
                            class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                        >
                            Anterior
                        </button>
                        <button
                            @click="changePage(pagination.current_page + 1)"
                            :disabled="pagination.current_page === pagination.last_page"
                            class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                        >
                            Siguiente
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import { useAuthStore } from '../stores/auth';
import api from '../services/api';
import Swal from 'sweetalert2';

const authStore = useAuthStore();
const router = useRouter();

const users = ref([]);
const companies = ref([]);
const loading = ref(false);
const pagination = ref(null);
const route = useRoute();

const filters = ref({
    search: route.query.search ?? '',
    status: route.query.status ?? '',
    company_id: route.query.company_id ?? ''
});

const sortBy = ref('id');
const normalizeSortOrder = (v) => (v === 'asc' || v === 'desc' ? v : 'desc');
const sortOrder = ref(normalizeSortOrder(route.query.sort_order ?? 'desc'));

const toggleSortId = () => {
    sortOrder.value = sortOrder.value === 'desc' ? 'asc' : 'desc';
    fetchUsers(1);
};

let searchTimeout = null;

const STORAGE_KEY = 'users-list-filters';

const syncQueryToUrl = (page = 1) => {
    const q = {
        page: String(page),
        sort_order: normalizeSortOrder(sortOrder.value),
        ...filters.value
    };
    Object.keys(q).forEach(key => { if (q[key] === '') delete q[key]; });
    router.replace({ name: 'users', query: q });
    try {
        sessionStorage.setItem(STORAGE_KEY, JSON.stringify({ query: q, sortOrder: sortOrder.value }));
    } catch (_) {}
};

const fetchUsers = async (page = 1) => {
    loading.value = true;
    try {
        const params = {
            page,
            per_page: 10,
            sort_by: sortBy.value,
            sort_order: normalizeSortOrder(sortOrder.value),
            ...filters.value
        };

        Object.keys(params).forEach(key => {
            if (params[key] === '') {
                delete params[key];
            }
        });

        const response = await api.get('/users', { params });
        users.value = response.data.data;
        pagination.value = {
            current_page: response.data.current_page,
            last_page: response.data.last_page,
            from: response.data.from,
            to: response.data.to,
            total: response.data.total
        };
        syncQueryToUrl(response.data.current_page);
    } catch (error) {
        Swal.fire('Error', 'Error al cargar usuarios', 'error');
    } finally {
        loading.value = false;
    }
};

const fetchCompanies = async () => {
    try {
        const response = await api.get('/catalogs/companies');
        companies.value = response.data.data;
    } catch (error) {
        console.error('Error al cargar empresas:', error);
    }
};

const debounceSearch = () => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        fetchUsers(1);
    }, 300);
};

const changePage = (page) => {
    if (page >= 1 && page <= pagination.value.last_page) {
        fetchUsers(page);
    }
};

const toggleStatus = async (user) => {
    const result = await Swal.fire({
        title: '¿Estás seguro?',
        text: `¿Deseas ${user.status === 'active' ? 'desactivar' : 'activar'} este usuario?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí',
        cancelButtonText: 'Cancelar'
    });

    if (result.isConfirmed) {
        try {
            await api.post(`/users/${user.id}/toggle-status`);
            Swal.fire('Éxito', 'Estado del usuario actualizado correctamente', 'success');
            await fetchUsers(pagination.value.current_page);
        } catch (error) {
            Swal.fire('Error', 'Error al actualizar el estado del usuario', 'error');
        }
    }
};

const deleteUser = async (user) => {
    const result = await Swal.fire({
        title: '¿Estás seguro?',
        text: 'Esta acción no se puede deshacer',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    });

    if (result.isConfirmed) {
        try {
            await api.delete(`/users/${user.id}`);
            Swal.fire('Eliminado', 'Usuario eliminado correctamente', 'success');
            await fetchUsers(pagination.value.current_page);
        } catch (error) {
            Swal.fire('Error', 'Error al eliminar el usuario', 'error');
        }
    }
};

const applyQueryToFilters = () => {
    const hasQuery = Object.keys(route.query).length > 0;
    if (hasQuery) {
        filters.value.search = route.query.search ?? '';
        filters.value.status = route.query.status ?? '';
        filters.value.company_id = route.query.company_id ?? '';
        sortOrder.value = normalizeSortOrder(route.query.sort_order ?? 'desc');
    } else {
        try {
            const saved = sessionStorage.getItem(STORAGE_KEY);
            if (saved) {
                const { query } = JSON.parse(saved);
                if (query) {
                    filters.value.search = query.search ?? '';
                    filters.value.status = query.status ?? '';
                    filters.value.company_id = query.company_id ?? '';
                    sortOrder.value = normalizeSortOrder(query.sort_order ?? 'desc');
                }
            }
        } catch (_) {}
    }
};

onMounted(async () => {
    applyQueryToFilters();
    await fetchCompanies();
    const page = Number(route.query.page) || 1;
    await fetchUsers(page);
});
</script>
