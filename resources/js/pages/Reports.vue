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
                            <i class="fas fa-file-alt text-blue-600 mr-3"></i>
                            Reportes
                        </h2>
                        <p class="text-gray-500 text-sm mt-1">Gestiona y visualiza los reportes del sistema</p>
                    </div>
                    <router-link
                        v-if="authStore.hasPermission('save report')"
                        :to="{ name: 'reports.create' }"
                        class="flex items-center px-5 py-2.5 bg-blue-500 text-white rounded-lg hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors duration-200 shadow-sm font-medium"
                    >
                        <i class="fas fa-plus mr-2"></i>
                        Crear Reporte
                    </router-link>
                </div>

                <!-- Search Section -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-filter mr-2 text-gray-400"></i>
                                Empresa
                            </label>
                            <select
                                v-model="filters.company_id"
                                @change="fetchReports"
                                class="block w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 px-4 py-2.5 transition-all duration-200"
                            >
                                <option value="">Todas</option>
                                <option v-for="company in companies" :key="company.id" :value="company.id">
                                    {{ company.name }}
                                </option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-filter mr-2 text-gray-400"></i>
                                Equipo
                            </label>
                            <select
                                v-model="filters.equipment_id"
                                @change="fetchReports"
                                class="block w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 px-4 py-2.5 transition-all duration-200"
                            >
                                <option value="">Todos</option>
                                <option v-for="eq in equipments" :key="eq.id" :value="eq.id">
                                    {{ eq.name }}
                                </option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-filter mr-2 text-gray-400"></i>
                                Condición
                            </label>
                            <select
                                v-model="filters.condition_id"
                                @change="fetchReports"
                                class="block w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 px-4 py-2.5 transition-all duration-200"
                            >
                                <option value="">Todas</option>
                                <option v-for="condition in conditions" :key="condition.id" :value="condition.id">
                                    {{ condition.name }}
                                </option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-filter mr-2 text-gray-400"></i>
                                Prioridad
                            </label>
                            <select
                                v-model="filters.priority"
                                @change="fetchReports"
                                class="block w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 px-4 py-2.5 transition-all duration-200"
                            >
                                <option value="">Todas</option>
                                <option value="Alta">Alta</option>
                                <option value="Media">Media</option>
                                <option value="Baja">Baja</option>
                            </select>
                        </div>
                    </div>
                    <div class="mt-3 flex justify-end">
                        <button
                            @click="fetchReports"
                            class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-400 focus:ring-offset-2 transition-colors duration-200 font-medium"
                        >
                            <i class="fas fa-redo mr-2"></i>
                            Actualizar
                        </button>
                    </div>
                </div>
            </div>

            <!-- Table Section -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div v-if="reports.length === 0 && !loading" class="p-8 text-center">
                    <i class="fas fa-file-alt text-gray-300 text-6xl mb-4"></i>
                    <p class="text-gray-500 text-lg">No hay reportes registrados</p>
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
                                        <i class="fas fa-cog mr-2"></i>
                                        Equipo
                                    </div>
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-blue-900 uppercase tracking-wider">
                                    <div class="flex items-center">
                                        <i class="fas fa-calendar mr-2"></i>
                                        Fecha Medición
                                    </div>
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-blue-900 uppercase tracking-wider">
                                    <div class="flex items-center">
                                        <i class="fas fa-exclamation-triangle mr-2"></i>
                                        Condición
                                    </div>
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-blue-900 uppercase tracking-wider">
                                    <div class="flex items-center">
                                        <i class="fas fa-flag mr-2"></i>
                                        Prioridad
                                    </div>
                                </th>
                                <th class="px-6 py-4 text-right text-xs font-semibold text-blue-900 uppercase tracking-wider">
                                    <div class="flex items-center justify-end">
                                        <i class="fas fa-cog mr-2"></i>
                                        Acciones
                                    </div>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="report in reports" :key="report.id" class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ report.id }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-700">{{ report.equipment && report.equipment.company && report.equipment.company.name ? report.equipment.company.name : '—' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ report.equipment && report.equipment.name ? report.equipment.name : '' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-500">{{ formatDate(report.measurement_date) }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        :class="!report.condition || !report.condition.color ? getConditionClass(report.condition && report.condition.name) : ''"
                                        :style="report.condition && report.condition.color ? { backgroundColor: report.condition.color, color: '#fff', borderColor: report.condition.color } : {}"
                                        class="px-3 py-1 inline-flex text-xs font-semibold rounded-full border"
                                    >
                                        {{ report.condition && report.condition.name ? report.condition.name : '' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        :class="report.priority === 'Alta' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800'"
                                        class="px-3 py-1 inline-flex text-xs font-semibold rounded-full"
                                    >
                                        {{ report.priority }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex items-center justify-end space-x-2">
                                        <router-link
                                            :to="{ name: 'reports.show', params: { id: report.id } }"
                                            class="text-green-600 hover:text-green-900 p-2 hover:bg-green-50 rounded-lg transition-colors"
                                            title="Ver detalles"
                                        >
                                            <i class="fas fa-eye"></i>
                                        </router-link>
                                        <router-link
                                            v-if="authStore.hasPermission('edit report')"
                                            :to="{ name: 'reports.edit', params: { id: report.id } }"
                                            class="text-blue-600 hover:text-blue-900 p-2 hover:bg-blue-50 rounded-lg transition-colors"
                                            title="Editar"
                                        >
                                            <i class="fas fa-edit"></i>
                                        </router-link>
                                        <button
                                            v-if="authStore.hasPermission('delete report')"
                                            @click="deleteReport(report.id)"
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
            <div v-if="pagination && reports.length > 0" class="mt-4 bg-white rounded-lg shadow-sm border border-gray-200 p-4">
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
import { ref, onMounted, reactive } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import { useAuthStore } from '../stores/auth';
import api from '../services/api';
import Swal from 'sweetalert2';

const STORAGE_KEY = 'reports-list-filters';

const router = useRouter();
const route = useRoute();
const authStore = useAuthStore();

const reports = ref([]);
const loading = ref(false);
const pagination = ref(null);
const equipments = ref([]);
const conditions = ref([]);
const companies = ref([]);

const filters = reactive({
    equipment_id: route.query.equipment_id ?? '',
    condition_id: route.query.condition_id ?? '',
    priority: route.query.priority ?? '',
    company_id: route.query.company_id ?? '',
});

const sortBy = ref('id');
const normalizeSortOrder = (v) => (v === 'asc' || v === 'desc' ? v : 'desc');
const sortOrder = ref(normalizeSortOrder(route.query.sort_order ?? 'desc'));

const syncQueryToUrl = (page = 1) => {
    const q = {
        page: String(page),
        sort_order: sortOrder.value,
        equipment_id: filters.equipment_id,
        condition_id: filters.condition_id,
        priority: filters.priority,
        company_id: filters.company_id
    };
    Object.keys(q).forEach(key => { if (q[key] === '') delete q[key]; });
    router.replace({ name: 'reports', query: q });
    try {
        sessionStorage.setItem(STORAGE_KEY, JSON.stringify({ query: q }));
    } catch (_) {}
};

const applyQueryToFilters = () => {
    const hasQuery = Object.keys(route.query).length > 0;
    if (hasQuery) {
        filters.equipment_id = route.query.equipment_id ?? '';
        filters.condition_id = route.query.condition_id ?? '';
        filters.priority = route.query.priority ?? '';
        filters.company_id = route.query.company_id ?? '';
        sortOrder.value = normalizeSortOrder(route.query.sort_order ?? 'desc');
    } else {
        try {
            const saved = sessionStorage.getItem(STORAGE_KEY);
            if (saved) {
                const { query } = JSON.parse(saved);
                if (query) {
                    filters.equipment_id = query.equipment_id ?? '';
                    filters.condition_id = query.condition_id ?? '';
                    filters.priority = query.priority ?? '';
                    filters.company_id = query.company_id ?? '';
                    sortOrder.value = normalizeSortOrder(query.sort_order ?? 'desc');
                }
            }
        } catch (_) {}
    }
};

const toggleSortId = () => {
    sortOrder.value = sortOrder.value === 'desc' ? 'asc' : 'desc';
    fetchReports(1);
};

onMounted(async () => {
    applyQueryToFilters();
    await loadCatalogs();
    const page = Number(route.query.page) || 1;
    await fetchReports(page);
});

const loadCatalogs = async () => {
    try {
        const [equipmentsRes, conditionsRes, companiesRes] = await Promise.all([
            api.get('/equipment'),
            api.get('/catalogs/conditions'),
            api.get('/catalogs/companies'),
        ]);

        equipments.value = equipmentsRes.data.data;
        conditions.value = conditionsRes.data.data;
        companies.value = companiesRes.data.data;
    } catch (error) {
        console.error('Error al cargar catálogos:', error);
    }
};

const fetchReports = async (page = 1) => {
    loading.value = true;
    try {
        const params = { page, per_page: 10, sort_by: sortBy.value, sort_order: normalizeSortOrder(sortOrder.value) };

        if (filters.equipment_id) params.equipment_id = filters.equipment_id;
        if (filters.condition_id) params.condition_id = filters.condition_id;
        if (filters.priority) params.priority = filters.priority;
        if (filters.company_id) params.company_id = filters.company_id;

        const response = await api.get('/reports', { params });
        reports.value = response.data.data;
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
        console.error('Error al cargar reportes:', error);
        const message = error.response?.data?.message || error.message || 'Error al cargar reportes';
        Swal.fire('Error', message, 'error');
    } finally {
        loading.value = false;
    }
};

const changePage = (page) => {
    fetchReports(page);
};

const formatDate = (date) => {
    if (!date) return 'N/A';
    return new Date(date).toLocaleDateString('es-ES');
};

const getConditionClass = (conditionName) => {
    const classes = {
        'Alarma': 'bg-yellow-100 text-yellow-800 border border-yellow-300',
        'Critico': 'bg-red-100 text-red-800 border border-red-300',
        'Peligro': 'bg-orange-100 text-orange-800 border border-orange-300',
    };
    return classes[conditionName] || 'bg-blue-100 text-blue-800 border border-blue-300';
};

const deleteReport = async (id) => {
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
            await api.delete(`/reports/${id}`);
            Swal.fire('Eliminado', 'El reporte se eliminó correctamente', 'success');
            fetchReports();
        } catch (error) {
            Swal.fire('Error', 'Error al eliminar el reporte', 'error');
        }
    }
};
</script>
