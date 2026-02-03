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

        <div class="py-6">
            <!-- Header Section -->
            <div class="mb-6">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
                    <div class="mb-4 sm:mb-0">
                        <h2 class="text-3xl font-bold text-gray-800 flex items-center">
                            <i class="fas fa-cog text-blue-600 mr-3"></i>
                            Equipos
                        </h2>
                        <p class="text-gray-500 text-sm mt-1">Gestiona los equipos del sistema</p>
                    </div>
                    <router-link
                        v-if="authStore.hasPermission('save equipment')"
                        :to="{ name: 'equipment.create' }"
                        class="flex items-center px-5 py-2.5 bg-blue-500 text-white rounded-lg hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors duration-200 shadow-sm font-medium"
                    >
                        <i class="fas fa-plus mr-2"></i>
                        Crear Equipo
                    </router-link>
                </div>

                <!-- Search Section -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                    <div class="flex flex-wrap items-end gap-3">
                        <div class="flex-1 min-w-[200px]">
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
                        <div class="flex items-end">
                            <button
                                @click="fetchEquipment"
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
                <div v-if="equipments.length === 0 && !loading" class="p-8 text-center">
                    <i class="fas fa-cog text-gray-300 text-6xl mb-4"></i>
                    <p class="text-gray-500 text-lg">No hay equipos registrados</p>
                </div>

                <div v-else class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
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
                                        <i class="fas fa-tag mr-2"></i>
                                        Nombre
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
                                <th v-if="authStore.hasPermission('edit equipment') || authStore.hasPermission('delete equipment')" class="px-6 py-4 text-right text-xs font-semibold text-blue-900 uppercase tracking-wider">
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
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-700">{{ equipment.company?.name || 'N/A' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ equipment.name }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-500">{{ equipment.equipment_type?.name || 'N/A' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-500">{{ equipment.ubication?.name || 'N/A' }}</div>
                                </td>
                                <td v-if="authStore.hasPermission('edit equipment') || authStore.hasPermission('delete equipment')" class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex items-center justify-end space-x-2">
                                        <router-link
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
            <div v-if="pagination && equipments.length > 0" class="mt-4 bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                <div class="flex flex-col sm:flex-row justify-between items-center space-y-2 sm:space-y-0">
                    <div class="text-sm text-gray-700">
                        Mostrando <span class="font-semibold">{{ pagination.from }}</span> a <span class="font-semibold">{{ pagination.to }}</span> de <span class="font-semibold">{{ pagination.total }}</span> resultados
                    </div>
                    <div class="flex space-x-2">
                        <button
                            v-if="pagination.prev_page_url"
                            @click="changePage(pagination.current_page - 1)"
                            class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors"
                        >
                            <i class="fas fa-chevron-left mr-1"></i>
                            Anterior
                        </button>
                        <button
                            v-if="pagination.next_page_url"
                            @click="changePage(pagination.current_page + 1)"
                            class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors"
                        >
                            Siguiente
                            <i class="fas fa-chevron-right ml-1"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '../stores/auth';
import api from '../services/api';
import Swal from 'sweetalert2';

const router = useRouter();
const authStore = useAuthStore();

const equipments = ref([]);
const companies = ref([]);
const equipmentTypes = ref([]);
const loading = ref(false);
const search = ref('');
const filters = ref({ company_id: '', equipment_type_id: '' });
const pagination = ref(null);
const sortBy = ref('id');
const sortOrder = ref('desc');

const toggleSortId = () => {
    sortOrder.value = sortOrder.value === 'desc' ? 'asc' : 'desc';
    fetchEquipment(1);
};

onMounted(async () => {
    await Promise.all([loadCompanies(), loadEquipmentTypes()]);
    fetchEquipment();
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

const fetchEquipment = async (page = 1) => {
    loading.value = true;
    try {
        const params = { page, per_page: 10, sort_by: sortBy.value, sort_order: sortOrder.value };
        if (search.value) params.search = search.value;
        if (filters.value.company_id) params.company_id = filters.value.company_id;
        if (filters.value.equipment_type_id) params.equipment_type_id = filters.value.equipment_type_id;
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
