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
                            <i :class="titleIconClass" class="fas mr-3"></i>
                            {{ titleText }}
                        </h2>
                        <p class="text-gray-500 text-sm mt-1">{{ subtitleText }}</p>
                    </div>
                    <div class="flex items-center justify-end gap-2 w-full sm:w-auto">
                        <router-link
                            :to="{ name: 'component-types' }"
                            class="w-10 h-10 flex items-center justify-center rounded-lg bg-gray-100 text-gray-700 hover:bg-gray-200 transition-colors"
                            title="Volver"
                        >
                            <i class="fas fa-arrow-left"></i>
                        </router-link>
                        <router-link
                            v-if="authStore.hasPermission('save equipment')"
                            :to="{ name: 'component-types.create', query: { report_type: reportType } }"
                            class="flex items-center justify-center flex-1 sm:flex-none px-5 py-3 sm:py-2.5 bg-blue-500 text-white rounded-lg hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors duration-200 shadow-sm font-medium"
                        >
                            <i class="fas fa-plus mr-2"></i>
                            Crear Tipo de Componente
                        </router-link>
                    </div>
                </div>

                <!-- Search Section -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                    <div class="flex flex-col sm:flex-row gap-3">
                        <div class="flex-1">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-search mr-2 text-gray-400"></i>
                                Buscar tipo de componente
                            </label>
                            <ClearableInput
                                v-model="search"
                                placeholder="Buscar por nombre..."
                                @input="debounceSearch"
                                @clear="clearSearch"
                            />
                        </div>
                        <div class="flex items-end gap-2">
                            <button
                                type="button"
                                @click="clearFilters"
                                class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-400 focus:ring-offset-2 transition-colors duration-200 font-medium"
                            >
                                <i class="fas fa-times mr-2"></i>
                                Limpiar
                            </button>
                            <button
                                @click="fetchComponentTypes"
                                class="px-5 py-2.5 bg-blue-500 hover:bg-blue-600 text-white rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors duration-200 font-medium"
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
                <div v-if="componentTypes.length === 0 && !loading" class="p-8 text-center">
                    <i class="fas fa-snowplow text-gray-300 text-6xl mb-4"></i>
                    <p class="text-gray-500 text-lg">No hay tipos de componentes registrados</p>
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
                                        <i class="fas fa-tag mr-2"></i>
                                        Nombre del Tipo
                                    </div>
                                </th>
                                <th v-if="authStore.hasPermission('consult equipment') || authStore.hasPermission('edit equipment') || authStore.hasPermission('delete equipment')" class="px-6 py-4 text-right text-xs font-semibold text-blue-900 uppercase tracking-wider">
                                    <div class="flex items-center justify-end">
                                        <i class="fas fa-cog mr-2"></i>
                                        Acciones
                                    </div>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="type in componentTypes" :key="type.id" class="hover:bg-blue-50 transition-colors duration-150">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ type.id }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10 bg-blue-100 rounded-lg flex items-center justify-center mr-4">
                                            <i class="fas fa-snowplow text-blue-600"></i>
                                        </div>
                                        <div>
                                            <div class="text-sm font-semibold text-gray-900">
                                                {{ type.name }}
                                            </div>
                                            <div class="text-xs text-gray-500">
                                                Tipo de componente
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td v-if="authStore.hasPermission('detalle component type') || authStore.hasPermission('consult component type') || authStore.hasPermission('edit component type') || authStore.hasPermission('delete component type')" class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex items-center justify-end space-x-2">
                                        <router-link
                                            v-if="authStore.hasPermission('detalle component type')"
                                            :to="{ name: 'component-types.show', params: { id: type.id }, query: { report_type: reportType } }"
                                            class="inline-flex items-center px-3 py-2 bg-white border border-blue-100 hover:bg-blue-50 text-blue-600 rounded-lg transition-colors duration-200"
                                            title="Ver detalles"
                                        >
                                            <i class="fas fa-eye text-sm"></i>
                                            <span class="ml-1.5 text-xs font-medium">Ver</span>
                                        </router-link>
                                        <router-link
                                            v-if="authStore.hasPermission('edit component type')"
                                            :to="{ name: 'component-types.edit', params: { id: type.id }, query: { report_type: reportType } }"
                                            class="inline-flex items-center px-3 py-2 bg-blue-50 hover:bg-blue-100 text-blue-600 rounded-lg transition-colors duration-200"
                                            title="Editar"
                                        >
                                            <i class="fas fa-edit text-sm"></i>
                                            <span class="ml-1.5 text-xs font-medium">Editar</span>
                                        </router-link>
                                        <button
                                            v-if="authStore.hasPermission('delete component type')"
                                            @click="deleteComponentType(type)"
                                            class="inline-flex items-center px-3 py-2 bg-red-50 hover:bg-red-100 text-red-600 rounded-lg transition-colors duration-200"
                                            title="Eliminar"
                                        >
                                            <i class="fas fa-trash text-sm"></i>
                                            <span class="ml-1.5 text-xs font-medium">Eliminar</span>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <Pagination
                    v-if="pagination && componentTypes.length > 0"
                    :pagination="pagination"
                    @page-change="changePage"
                    class="px-6 py-4 border-t border-gray-200"
                />
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import { useAuthStore } from '../stores/auth';
import api from '../services/api';
import Swal from 'sweetalert2';
import Pagination from '../components/Pagination.vue';
import ClearableInput from '../components/ClearableInput.vue';

const props = defineProps({
    reportType: {
        type: String,
        default: 'vibration',
    },
});

const reportType = computed(() => (props.reportType === 'thermography' ? 'thermography' : 'vibration'));
const STORAGE_KEY = computed(() => `component-types-list-filters-${reportType.value}`);

const authStore = useAuthStore();
const router = useRouter();
const route = useRoute();

const titleText = computed(() => (reportType.value === 'thermography' ? 'Tipos de Componentes (Termografía)' : 'Tipos de Componentes (Vibraciones)'));
const subtitleText = computed(() => (reportType.value === 'thermography' ? 'Gestiona los tipos de componente para termografía' : 'Gestiona los tipos de componente para vibraciones'));
const titleIconClass = computed(() => (reportType.value === 'thermography' ? 'fa-thermometer-half text-rose-600' : 'fa-wave-square text-blue-600'));

const componentTypes = ref([]);
const loading = ref(false);
const pagination = ref(null);
const search = ref(route.query.search ?? '');
const sortBy = ref('id');
const normalizeSortOrder = (v) => (v === 'asc' || v === 'desc' ? v : 'desc');
const sortOrder = ref(normalizeSortOrder(route.query.sort_order ?? 'desc'));

const syncQueryToUrl = (page = 1) => {
    const q = { page: String(page), sort_order: normalizeSortOrder(sortOrder.value), search: search.value };
    Object.keys(q).forEach(key => { if (q[key] === '') delete q[key]; });
    router.replace({ name: route.name, query: q });
    try {
        sessionStorage.setItem(STORAGE_KEY.value, JSON.stringify({ query: q }));
    } catch (_) {}
};

const applyQueryToFilters = () => {
    const hasQuery = Object.keys(route.query).length > 0;
    if (hasQuery) {
        search.value = route.query.search ?? '';
        sortOrder.value = route.query.sort_order ?? 'desc';
    } else {
        try {
            const saved = sessionStorage.getItem(STORAGE_KEY.value);
            if (saved) {
                const { query } = JSON.parse(saved);
                if (query) {
                    search.value = query.search ?? '';
                    sortOrder.value = query.sort_order ?? 'desc';
                }
            }
        } catch (_) {}
    }
};

const toggleSortId = () => {
    sortOrder.value = sortOrder.value === 'desc' ? 'asc' : 'desc';
    fetchComponentTypes(1);
};

let searchTimeout = null;

const fetchComponentTypes = async (page = 1) => {
    loading.value = true;
    try {
        const params = {
            page,
            per_page: 10,
            sort_by: sortBy.value,
            sort_order: normalizeSortOrder(sortOrder.value),
            report_type: reportType.value,
        };

        if (search.value) {
            params.search = search.value;
        }

        const response = await api.get('/component-types', { params });
        componentTypes.value = response.data.data;
        pagination.value = {
            current_page: response.data.current_page,
            last_page: response.data.last_page,
            from: response.data.from,
            to: response.data.to,
            total: response.data.total
        };
        syncQueryToUrl(response.data.current_page);
    } catch (error) {
        Swal.fire('Error', 'Error al cargar tipos de componentes', 'error');
    } finally {
        loading.value = false;
    }
};

const debounceSearch = () => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        fetchComponentTypes(1);
    }, 300);
};

const clearFilters = () => {
    search.value = '';
    fetchComponentTypes(1);
};

// Limpia únicamente el filtro de búsqueda
const clearSearch = () => {
    clearTimeout(searchTimeout);
    search.value = '';
    fetchComponentTypes(1);
};

const changePage = (page) => {
    if (page >= 1 && page <= pagination.value.last_page) {
        fetchComponentTypes(page);
    }
};

const deleteComponentType = async (type) => {
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
            await api.delete(`/component-types/${type.id}`);
            Swal.fire('Eliminado', 'Tipo de componente eliminado correctamente', 'success');
            await fetchComponentTypes(pagination.value.current_page);
        } catch (error) {
            Swal.fire('Error', 'Error al eliminar el tipo de componente', 'error');
        }
    }
};

onMounted(() => {
    applyQueryToFilters();
    const page = Number(route.query.page) || 1;
    fetchComponentTypes(page);
});
</script>
