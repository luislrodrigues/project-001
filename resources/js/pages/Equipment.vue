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
                            <i class="fas fa-cog text-blue-600 mr-3"></i>
                            Equipos
                        </h2>
                        <p class="text-gray-500 text-sm mt-1">Gestiona los equipos del sistema</p>
                    </div>
                    <router-link
                        v-if="authStore.hasPermission('save equipment')"
                        :to="{ name: 'equipment.create' }"
                        class="flex items-center justify-center w-full sm:w-auto px-5 py-3 sm:py-2.5 bg-blue-500 text-white rounded-lg hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors duration-200 shadow-sm font-medium"
                    >
                        <i class="fas fa-plus mr-2"></i>
                        Crear Equipo
                    </router-link>
                </div>

                <!-- Search Section -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                    <div class="flex flex-wrap items-end gap-3">
                        <div v-if="!authStore.hasRole('Cliente')" class="flex-1 min-w-[200px]">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-building mr-2 text-gray-400"></i>
                                Empresa
                            </label>
                            <select
                                v-model="filters.company_id"
                                @change="fetchEquipment"
                                class="block w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 px-4 py-2.5 transition-all duration-200"
                            >
                                <option value="">Todas</option>
                                <option v-for="company in companies" :key="company.id" :value="company.id">
                                    {{ company.name }}
                                </option>
                            </select>
                        </div>
                        <div class="flex-1 min-w-[200px]">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-project-diagram mr-2 text-gray-400"></i>
                                Proceso
                            </label>
                            <select
                                v-model="filters.process_id"
                                @change="fetchEquipment"
                                class="block w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 px-4 py-2.5 transition-all duration-200"
                            >
                                <option value="">Todos</option>
                                <option v-for="proc in processes" :key="proc.id" :value="proc.id">
                                    {{ proc.name }}
                                </option>
                            </select>
                        </div>
                        <div class="flex-1 min-w-[200px]">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-cog mr-2 text-gray-400"></i>
                                Tipo
                            </label>
                            <select
                                v-model="filters.equipment_type_id"
                                @change="fetchEquipment"
                                class="block w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 px-4 py-2.5 transition-all duration-200"
                            >
                                <option value="">Todos</option>
                                <option v-for="type in equipmentTypes" :key="type.id" :value="type.id">
                                    {{ type.name }}
                                </option>
                            </select>
                        </div>
                        <div class="flex-1 min-w-[200px]">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-search mr-2 text-gray-400"></i>
                                Buscar equipo
                            </label>
                            <div class="relative">
                                <input
                                    v-model="search"
                                    type="text"
                                    placeholder="Buscar por nombre..."
                                    class="block w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 pl-10 pr-4 py-2.5 transition-all duration-200"
                                    @input="fetchEquipment"
                                />
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-search text-gray-400"></i>
                                </div>
                            </div>
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
                                @click="fetchEquipment"
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
                <div v-if="equipments.length === 0 && !loading" class="p-8 text-center">
                    <i class="fas fa-cog text-gray-300 text-6xl mb-4"></i>
                    <p class="text-gray-500 text-lg">No hay equipos registrados</p>
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
                                <th v-if="!authStore.hasRole('Cliente')" class="px-6 py-4 text-left text-xs font-semibold text-blue-900 uppercase tracking-wider">
                                    <div class="flex items-center">
                                        <i class="fas fa-building mr-2"></i>
                                        Empresa
                                    </div>
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-blue-900 uppercase tracking-wider">
                                    <div class="flex items-center">
                                        <i class="fas fa-tag mr-2"></i>
                                        Nombre
                                    </div>
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-blue-900 uppercase tracking-wider">
                                    <div class="flex items-center">
                                        <i class="fas fa-project-diagram mr-2"></i>
                                        Proceso
                                    </div>
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-blue-900 uppercase tracking-wider">
                                    <div class="flex items-center">
                                        <i class="fas fa-cog mr-2"></i>
                                        Tipo
                                    </div>
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-blue-900 uppercase tracking-wider">
                                    <div class="flex items-center">
                                        <i class="fas fa-map-marker-alt mr-2"></i>
                                        Ubicación
                                    </div>
                                </th>
                                <th v-if="authStore.hasPermission('detalle equipment') || authStore.hasPermission('edit equipment') || authStore.hasPermission('delete equipment')" class="px-6 py-4 text-right text-xs font-semibold text-blue-900 uppercase tracking-wider">
                                    <div class="flex items-center justify-end">
                                        <i class="fas fa-cog mr-2"></i>
                                        Acciones
                                    </div>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="equipment in equipments" :key="equipment.id" class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ equipment.id }}
                                </td>
                                <td v-if="!authStore.hasRole('Cliente')" class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-700">{{ equipment.company && equipment.company.name ? equipment.company.name : 'N/A' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ equipment.name }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-500">{{ equipment.process && equipment.process.name ? equipment.process.name : '—' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-500">{{ equipment.equipment_type && equipment.equipment_type.name ? equipment.equipment_type.name : 'N/A' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-500">{{ equipment.ubication && equipment.ubication.name ? equipment.ubication.name : 'N/A' }}</div>
                                </td>
                                <td v-if="authStore.hasPermission('detalle equipment') || authStore.hasPermission('edit equipment') || authStore.hasPermission('delete equipment')" class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex items-center justify-end space-x-2">
                                        <router-link
                                            v-if="authStore.hasPermission('detalle equipment')"
                                            :to="{ name: 'equipment.show', params: { id: equipment.id } }"
                                            class="text-green-600 hover:text-green-900 p-2 hover:bg-green-50 rounded-lg transition-colors"
                                            title="Ver detalles"
                                        >
                                            <i class="fas fa-eye"></i>
                                        </router-link>
                                        <router-link
                                            v-if="authStore.hasPermission('edit equipment')"
                                            :to="{ name: 'equipment.edit', params: { id: equipment.id } }"
                                            class="text-blue-600 hover:text-blue-900 p-2 hover:bg-blue-50 rounded-lg transition-colors"
                                            title="Editar"
                                        >
                                            <i class="fas fa-edit"></i>
                                        </router-link>
                                        <button
                                            v-if="authStore.hasPermission('delete equipment')"
                                            @click="deleteEquipment(equipment.id)"
                                            class="text-red-600 hover:text-red-900 p-2 hover:bg-red-50 rounded-lg transition-colors"
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
            </div>

            <!-- Paginación -->
            <Pagination
                v-if="pagination && equipments.length > 0"
                :pagination="pagination"
                @page-change="changePage"
            />
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import { useAuthStore } from '../stores/auth';
import api from '../services/api';
import Swal from 'sweetalert2';
import Pagination from '../components/Pagination.vue';

const STORAGE_KEY = 'equipment-list-filters';

const router = useRouter();
const route = useRoute();
const authStore = useAuthStore();

const equipments = ref([]);
const companies = ref([]);
const equipmentTypes = ref([]);
const processes = ref([]);
const loading = ref(false);
const search = ref(route.query.search ?? '');
const filters = ref({
    company_id: route.query.company_id ?? '',
    equipment_type_id: route.query.equipment_type_id ?? '',
    process_id: route.query.process_id ?? ''
});
const pagination = ref(null);
const sortBy = ref('id');
const normalizeSortOrder = (v) => (v === 'asc' || v === 'desc' ? v : 'desc');
const sortOrder = ref(normalizeSortOrder(route.query.sort_order ?? 'desc'));

const syncQueryToUrl = (page = 1) => {
    const q = {
        page: String(page),
        sort_order: sortOrder.value,
        search: search.value,
        company_id: filters.value.company_id,
        equipment_type_id: filters.value.equipment_type_id,
        process_id: filters.value.process_id
    };
    Object.keys(q).forEach(key => { if (q[key] === '') delete q[key]; });
    router.replace({ name: 'equipment', query: q });
    try {
        sessionStorage.setItem(STORAGE_KEY, JSON.stringify({ query: q }));
    } catch (_) {}
};

const applyQueryToFilters = () => {
    const hasQuery = Object.keys(route.query).length > 0;
    if (hasQuery) {
        search.value = route.query.search ?? '';
        filters.value.company_id = route.query.company_id ?? '';
        filters.value.equipment_type_id = route.query.equipment_type_id ?? '';
        filters.value.process_id = route.query.process_id ?? '';
        sortOrder.value = route.query.sort_order ?? 'desc';
    } else {
        try {
            const saved = sessionStorage.getItem(STORAGE_KEY);
            if (saved) {
                const { query } = JSON.parse(saved);
                if (query) {
                    search.value = query.search ?? '';
                    filters.value.company_id = query.company_id ?? '';
                    filters.value.equipment_type_id = query.equipment_type_id ?? '';
                    sortOrder.value = query.sort_order ?? 'desc';
                }
            }
        } catch (_) {}
    }
};

const toggleSortId = () => {
    sortOrder.value = sortOrder.value === 'desc' ? 'asc' : 'desc';
    fetchEquipment(1);
};

onMounted(async () => {
    applyQueryToFilters();
    await Promise.all([loadCompanies(), loadEquipmentTypes(), loadProcesses()]);
    const page = Number(route.query.page) || 1;
    await fetchEquipment(page);
});

const loadCompanies = async () => {
    try {
        const response = await api.get('/catalogs/companies');
        companies.value = response.data.data;
    } catch (error) {
        console.error('Error al cargar empresas:', error);
    }
};

const loadEquipmentTypes = async () => {
    try {
        const response = await api.get('/catalogs/equipment-types');
        equipmentTypes.value = response.data.data;
    } catch (error) {
        console.error('Error al cargar tipos de equipo:', error);
    }
};

const loadProcesses = async () => {
    try {
        const response = await api.get('/catalogs/processes');
        processes.value = response.data.data ?? response.data ?? [];
    } catch (error) {
        console.error('Error al cargar procesos:', error);
    }
};

const clearFilters = () => {
    search.value = '';
    filters.value.company_id = '';
    filters.value.equipment_type_id = '';
    filters.value.process_id = '';
    fetchEquipment(1);
};

const fetchEquipment = async (page = 1) => {
    loading.value = true;
    try {
        const params = { page, per_page: 10, sort_by: sortBy.value, sort_order: normalizeSortOrder(sortOrder.value) };
        if (search.value) params.search = search.value;
        if (filters.value.company_id) params.company_id = filters.value.company_id;
        if (filters.value.equipment_type_id) params.equipment_type_id = filters.value.equipment_type_id;
        if (filters.value.process_id) params.process_id = filters.value.process_id;
        const response = await api.get('/equipment', { params });
        equipments.value = response.data.data;
        pagination.value = {
            current_page: response.data.current_page,
            from: response.data.from,
            to: response.data.to,
            total: response.data.total,
            last_page: response.data.last_page,
            prev_page_url: response.data.prev_page_url,
            next_page_url: response.data.next_page_url
        };
        syncQueryToUrl(response.data.current_page);
    } catch (error) {
        Swal.fire('Error', 'Error al cargar equipos', 'error');
    } finally {
        loading.value = false;
    }
};

const changePage = (page) => {
    fetchEquipment(page);
};

const deleteEquipment = async (id) => {
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
            await api.delete(`/equipment/${id}`);
            Swal.fire('Eliminado', 'El equipo se eliminó correctamente', 'success');
            fetchEquipment();
        } catch (error) {
            Swal.fire('Error', 'Error al eliminar el equipo', 'error');
        }
    }
};
</script>
