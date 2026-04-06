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
                            <i class="fas fa-file-alt text-blue-600 mr-3"></i>
                            Reportes
                        </h2>
                        <p class="text-gray-500 text-sm mt-1">Gestiona y visualiza los reportes del sistema</p>
                    </div>
                    <router-link
                        v-if="authStore.hasPermission('save report')"
                        :to="{ name: 'reports.create' }"
                        class="flex items-center justify-center w-full sm:w-auto px-5 py-3 sm:py-2.5 bg-blue-500 text-white rounded-lg hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors duration-200 shadow-sm font-medium"
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
                                <i class="fas fa-search mr-2 text-gray-400"></i>
                                Buscar por informe
                            </label>
                            <input
                                v-model="filters.consecutivo"
                                type="text"
                                placeholder="Ej: No: SS-0001 o 1"
                                class="block w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 px-4 py-2.5 transition-all duration-200"
                                @keyup.enter="fetchReports(1)"
                            />
                        </div>
                        <div v-if="!authStore.hasRole('Cliente')">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-filter mr-2 text-gray-400"></i>
                                Empresa
                            </label>
                            <Select2
                                v-model="filters.company_id"
                                :options="companies"
                                placeholder="Todas"
                                @change="() => fetchReports(1)"
                                class="block w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 px-4 py-2.5 transition-all duration-200"
                            />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-filter mr-2 text-gray-400"></i>
                                Proceso
                            </label>
                            <Select2
                                v-model="filters.process_id"
                                :options="processes"
                                allLabel="Todos"
                                @change="() => fetchReports(1)"
                                class="block w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 px-4 py-2.5 transition-all duration-200"
                            />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-calendar-alt mr-2 text-gray-400"></i>
                                Fecha medición (rango)
                            </label>
                            <RangeDatePicker
                                :start="filters.measurement_date_start"
                                :end="filters.measurement_date_end"
                                placeholder="AAAA-MM-DD - AAAA-MM-DD"
                                @update:start="val => { filters.measurement_date_start = val; }"
                                @update:end="val => { filters.measurement_date_end = val; }"
                                @apply="() => fetchReports(1)"
                            />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-filter mr-2 text-gray-400"></i>
                                Equipo
                            </label>
                            <Select2
                                v-model="filters.equipment_id"
                                :options="equipments"
                                allLabel="Todos"
                                @change="() => fetchReports(1)"
                                class="block w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 px-4 py-2.5 transition-all duration-200"
                            />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-filter mr-2 text-gray-400"></i>
                                Condición
                            </label>
                            <Select2
                                v-model="filters.condition_id"
                                :options="conditions"
                                allLabel="Todas"
                                @change="() => fetchReports(1)"
                                class="block w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 px-4 py-2.5 transition-all duration-200"
                            />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-filter mr-2 text-gray-400"></i>
                                Prioridad
                            </label>
                            <Select2
                                v-model="filters.priority_id"
                                :options="priorities"
                                allLabel="Todas"
                                @change="() => fetchReports(1)"
                                class="block w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 px-4 py-2.5 transition-all duration-200"
                            />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-filter mr-2 text-gray-400"></i>
                                Mecanismo de falla
                            </label>
                            <Select2
                                v-model="filters.mechanism_fail_id"
                                :options="mechanismFails"
                                allLabel="Todos"
                                @change="() => fetchReports(1)"
                                class="block w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 px-4 py-2.5 transition-all duration-200"
                            />
                        </div>
                        <div v-if="!authStore.hasRole('Cliente')">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-user-tie mr-2 text-gray-400"></i>
                                Analista
                            </label>
                            <Select2
                                v-model="filters.analist_id"
                                :options="analists"
                                allLabel="Todos"
                                @change="() => fetchReports(1)"
                                class="block w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 px-4 py-2.5 transition-all duration-200"
                            />
                        </div>
                    </div>
                    <div class="mt-3 flex items-center justify-between gap-2">
                        <div v-if="authStore.hasPermission('export report')">
                            <button
                                type="button"
                                @click="exportExcel"
                                :disabled="exporting"
                                class="px-5 py-2.5 bg-emerald-500 hover:bg-emerald-600 text-white rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition-colors duration-200 font-medium disabled:opacity-50 disabled:cursor-not-allowed"
                                title="Exportar a Excel los reportes filtrados"
                            >
                                <i v-if="!exporting" class="fas fa-file-excel mr-2"></i>
                                <svg v-else class="animate-spin h-4 w-4 mr-2 inline" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                                    <circle cx="12" cy="12" r="10" stroke-opacity="0.25" stroke-width="2" stroke="currentColor" fill="none"></circle>
                                    <path d="M12 2a10 10 0 00-9.5 7.5M12 2c2.5 0 5 1 7 2.5" stroke="currentColor" stroke-width="2" fill="none"></path>
                                </svg>
                                {{ exporting ? 'Exportando...' : 'Generar Excel' }}
                            </button>
                        </div>
                        <div class="flex gap-2 ml-auto">
                            <button
                                type="button"
                                @click="clearFilters"
                                class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-400 focus:ring-offset-2 transition-colors duration-200 font-medium"
                            >
                                <i class="fas fa-times mr-2"></i>
                                Limpiar
                            </button>
                            <button
                                @click="() => fetchReports(1)"
                                class="px-5 py-2.5 bg-blue-500 hover:bg-blue-600 text-white rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors duración-200 font-medium"
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
                <div v-if="reports.length === 0 && !loading" class="p-8 text-center">
                    <i class="fas fa-file-alt text-gray-300 text-6xl mb-4"></i>
                    <p class="text-gray-500 text-lg">No hay reportes registrados</p>
                </div>

                <div v-else>
                    <table class="w-full table-fixed divide-y divide-gray-200">
                        <thead class="bg-gradient-to-r from-blue-50 to-blue-100">
                            <tr>
                                <th
                                    class="w-[130px] px-3 py-3 text-left text-xs font-semibold text-blue-900 uppercase tracking-wider cursor-pointer hover:bg-blue-200/50 select-none"
                                    @click="toggleSortId"
                                    title="Clic para cambiar orden"
                                >
                                    <div class="flex items-center gap-1">
                                        <i class="fas fa-hashtag mr-2"></i>
                                        Informe
                                        <i v-if="sortOrder === 'desc'" class="fas fa-sort-down text-sm"></i>
                                        <i v-else class="fas fa-sort-up text-sm"></i>
                                    </div>
                                </th>
                                <th v-if="!authStore.hasRole('Cliente')" class="w-[190px] px-3 py-3 text-left text-xs font-semibold text-blue-900 uppercase tracking-wider">
                                    <div class="flex items-center">
                                        <i class="fas fa-building mr-2"></i>
                                        Empresa
                                    </div>
                                </th>
                                <th class="w-[240px] px-3 py-3 text-left text-xs font-semibold text-blue-900 uppercase tracking-wider">
                                    <div class="flex items-center">
                                        <i class="fas fa-cog mr-2"></i>
                                        Equipo
                                    </div>
                                </th>
                                <th class="w-[170px] px-3 py-3 text-left text-xs font-semibold text-blue-900 uppercase tracking-wider">
                                    <div class="flex items-center">
                                        <i class="fas fa-project-diagram mr-2"></i>
                                        Proceso
                                    </div>
                                </th>
                                <th class="w-[130px] px-3 py-3 text-left text-xs font-semibold text-blue-900 uppercase tracking-wider">
                                    <div class="flex items-center">
                                        <i class="fas fa-calendar mr-2"></i>
                                        Fecha Medición
                                    </div>
                                </th>
                                <th class="w-[120px] px-3 py-3 text-left text-xs font-semibold text-blue-900 uppercase tracking-wider">
                                    <div class="flex items-center">
                                        <i class="fas fa-exclamation-triangle mr-2"></i>
                                        Condición
                                    </div>
                                </th>
                                <th class="w-[200px] px-3 py-3 text-left text-xs font-semibold text-blue-900 uppercase tracking-wider">
                                    <div class="flex items-center">
                                        <i class="fas fa-tools mr-2"></i>
                                        Mecanismo de falla
                                    </div>
                                </th>
                                <th class="w-[120px] px-3 py-3 text-left text-xs font-semibold text-blue-900 uppercase tracking-wider">
                                    <div class="flex items-center">
                                        <i class="fas fa-flag mr-2"></i>
                                        Prioridad
                                    </div>
                                </th>
                                <th v-if="authStore.hasPermission('detalle report') || authStore.hasPermission('edit report') || authStore.hasPermission('delete report')" class="w-[120px] px-3 py-3 text-right text-xs font-semibold text-blue-900 uppercase tracking-wider">
                                    <div class="flex items-center justify-end">
                                        <i class="fas fa-cog mr-2"></i>
                                        Acciones
                                    </div>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="report in reports" :key="report.id" class="hover:bg-gray-50 transition-colors">
                                <td class="px-3 py-3 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ report.consecutivo ?? '—' }}
                                </td>
                                <td v-if="!authStore.hasRole('Cliente')" class="px-3 py-3 whitespace-normal align-top">
                                    <div class="text-sm text-gray-700 break-words leading-snug">{{ report.equipment && report.equipment.company && report.equipment.company.name ? report.equipment.company.name : '—' }}</div>
                                </td>
                                <td class="px-3 py-3 whitespace-normal align-top">
                                    <div class="text-sm font-medium text-gray-900 break-words leading-snug">{{ report.equipment && report.equipment.name ? report.equipment.name : '' }}</div>
                                </td>
                                <td class="px-3 py-3 whitespace-normal align-top">
                                    <div class="text-sm text-gray-500 break-words leading-snug">{{ report.equipment && report.equipment.process ? report.equipment.process : '—' }}</div>
                                </td>
                                <td class="px-3 py-3 whitespace-nowrap">
                                    <div class="text-sm text-gray-500">{{ formatDate(report.measurement_date) }}</div>
                                </td>
                                <td class="px-3 py-3 whitespace-nowrap">
                                    <span
                                        :class="!report.condition || !report.condition.color ? getConditionClass() : ''"
                                        :style="report.condition ? (report.condition.color ? { backgroundColor: report.condition.color, color: report.condition.text_color || '#000', borderColor: report.condition.color } : (report.condition.text_color ? { color: report.condition.text_color } : {})) : {}"
                                        class="px-3 py-1 inline-flex text-xs font-semibold rounded-full border"
                                    >
                                        {{ report.condition && report.condition.name ? report.condition.name : '' }}
                                    </span>
                                </td>
                                <td class="px-3 py-3 whitespace-normal align-top">
                                    <div class="text-sm text-gray-500 break-words leading-snug">{{ report.mechanism_fail?.name ?? '—' }}</div>
                                </td>
                                <td class="px-3 py-3 whitespace-nowrap">
                                    <span
                                        :class="priorityBadgeClass(report.priority)"
                                        :style="priorityBadgeStyle(report.priority)"
                                        class="px-3 py-1 inline-flex text-xs font-semibold rounded-full"
                                    >
                                        {{ report.priority?.name ?? report.priority }}
                                    </span>
                                </td>
                                <td v-if="authStore.hasPermission('detalle report') || authStore.hasPermission('edit report') || authStore.hasPermission('delete report')" class="px-3 py-3 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex items-center justify-end space-x-2">
                                        <router-link
                                            v-if="authStore.hasPermission('detalle report')"
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
            <Pagination
                v-if="pagination && reports.length > 0"
                :pagination="pagination"
                @page-change="changePage"
            />
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, reactive } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import { useAuthStore } from '../stores/auth';
import api from '../services/api';
import Swal from 'sweetalert2';
import Pagination from '../components/Pagination.vue';
import RangeDatePicker from '../components/RangeDatePicker.vue';
import Select2 from '../components/Select2.vue';

const STORAGE_KEY = 'reports-list-filters';

const router = useRouter();
const route = useRoute();
const authStore = useAuthStore();

const reports = ref([]);
const loading = ref(false);
const pendingRequests = ref(0);
const exporting = ref(false);
const pagination = ref(null);
const equipments = ref([]);
const conditions = ref([]);
const priorities = ref([]);
const mechanismFails = ref([]);
const companies = ref([]);
const processes = ref([]);
const analists = ref([]);

const filters = reactive({
    consecutivo: route.query.consecutivo ?? '',
    equipment_id: route.query.equipment_id ?? '',
    condition_id: route.query.condition_id ?? '',
    priority_id: route.query.priority_id ?? '',
    mechanism_fail_id: route.query.mechanism_fail_id ?? '',
    company_id: route.query.company_id ?? '',
    process_id: route.query.process_id ?? '',
    analist_id: route.query.analist_id ?? '',
    measurement_date_start: route.query.measurement_date_start ?? '',
    measurement_date_end: route.query.measurement_date_end ?? ''
});

const sortBy = ref('id');
const normalizeSortOrder = (v) => (v === 'asc' || v === 'desc' ? v : 'desc');
const sortOrder = ref(normalizeSortOrder(route.query.sort_order ?? 'desc'));

const syncQueryToUrl = (page = 1) => {
    const q = {
        page: String(page),
        sort_order: sortOrder.value,
        consecutivo: filters.consecutivo,
        equipment_id: filters.equipment_id,
        condition_id: filters.condition_id,
        priority_id: filters.priority_id,
        mechanism_fail_id: filters.mechanism_fail_id,
        company_id: filters.company_id,
        process_id: filters.process_id,
        analist_id: filters.analist_id,
        measurement_date_start: filters.measurement_date_start,
        measurement_date_end: filters.measurement_date_end
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
        filters.consecutivo = route.query.consecutivo ?? '';
        filters.equipment_id = route.query.equipment_id ?? '';
        filters.condition_id = route.query.condition_id ?? '';
        filters.priority_id = route.query.priority_id ?? '';
        filters.mechanism_fail_id = route.query.mechanism_fail_id ?? '';
        filters.company_id = route.query.company_id ?? '';
        filters.process_id = route.query.process_id ?? '';
        filters.analist_id = route.query.analist_id ?? '';
        filters.measurement_date_start = route.query.measurement_date_start ?? '';
        filters.measurement_date_end = route.query.measurement_date_end ?? '';
        sortOrder.value = normalizeSortOrder(route.query.sort_order ?? 'desc');
    } else {
        try {
            const saved = sessionStorage.getItem(STORAGE_KEY);
            if (saved) {
                const { query } = JSON.parse(saved);
                if (query) {
                    filters.consecutivo = query.consecutivo ?? '';
                    filters.equipment_id = query.equipment_id ?? '';
                    filters.condition_id = query.condition_id ?? '';
                    filters.priority_id = query.priority_id ?? '';
                    filters.mechanism_fail_id = query.mechanism_fail_id ?? '';
                    filters.company_id = query.company_id ?? '';
                    filters.process_id = query.process_id ?? '';
                    filters.analist_id = query.analist_id ?? '';
                    filters.measurement_date_start = query.measurement_date_start ?? '';
                    filters.measurement_date_end = query.measurement_date_end ?? '';
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

const beginLoading = () => {
    pendingRequests.value += 1;
    loading.value = true;
};

const endLoading = () => {
    pendingRequests.value = Math.max(0, pendingRequests.value - 1);
    loading.value = pendingRequests.value > 0;
};

onMounted(async () => {
    applyQueryToFilters();
    await loadCatalogs();
    const page = Number(route.query.page) || 1;
    await fetchReports(page);
});

const loadCatalogs = async () => {
    beginLoading();
    try {
    const [equipmentsRes, conditionsRes, prioritiesRes, mechanismFailsRes, companiesRes, processesRes, analistsRes] = await Promise.all([
        api.get('/catalogs/equipment'),
            api.get('/catalogs/conditions'),
            api.get('/catalogs/priorities'),
            api.get('/catalogs/mechanism-fails'),
            api.get('/catalogs/companies'),
            api.get('/catalogs/processes'),
            api.get('/catalogs/analists'),
        ]);

    equipments.value = equipmentsRes.data.data;
        conditions.value = conditionsRes.data.data;
        priorities.value = prioritiesRes.data.data ?? prioritiesRes.data ?? [];
        mechanismFails.value = mechanismFailsRes.data.data ?? mechanismFailsRes.data ?? [];
        companies.value = companiesRes.data.data;
        processes.value = processesRes.data.data ?? processesRes.data ?? [];
        analists.value = analistsRes.data.data ?? analistsRes.data ?? [];
    } catch (error) {
        console.error('Error al cargar catálogos:', error);
    } finally {
        endLoading();
    }
};

const clearFilters = () => {
    filters.consecutivo = '';
    filters.equipment_id = '';
    filters.condition_id = '';
    filters.priority_id = '';
    filters.mechanism_fail_id = '';
    filters.company_id = '';
    filters.process_id = '';
    filters.analist_id = '';
    filters.measurement_date_start = '';
    filters.measurement_date_end = '';
    fetchReports(1);
};

// Usar siempre los colores registrados en la tabla priorities cuando existan
const hasValidColor = (color) => color != null && String(color).trim() !== '';
const priorityBadgeClass = (priority) => {
    if (!priority) return 'bg-gray-100 text-gray-800';
    // Si la prioridad tiene color en BD, no aplicar clase de Tailwind para que se use el :style
    if (hasValidColor(priority.color)) return '';
    return priority.name === 'Alta' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800';
};
const priorityBadgeStyle = (priority) => {
    if (!priority) return {};
    // Prioridad: usar color y text_color de la tabla priorities (vienen en report.priority del API)
    const bg = priority.color != null ? String(priority.color).trim() : '';
    const fg = priority.text_color != null ? String(priority.text_color).trim() : '';
    if (bg === '' && fg === '') return {};
    return {
        backgroundColor: bg || 'transparent',
        color: fg || '#000000'
    };
};

const fetchReports = async (page = 1) => {
    beginLoading();
    try {
        const params = { page, per_page: 10, sort_by: sortBy.value, sort_order: normalizeSortOrder(sortOrder.value) };

        if (filters.consecutivo) params.consecutivo = filters.consecutivo.trim();
        if (filters.equipment_id) params.equipment_id = filters.equipment_id;
        if (filters.condition_id) params.condition_id = filters.condition_id;
        if (filters.priority_id) params.priority_id = filters.priority_id;
        if (filters.mechanism_fail_id) params.mechanism_fail_id = filters.mechanism_fail_id;
        if (filters.company_id) params.company_id = filters.company_id;
        if (filters.process_id) params.process_id = filters.process_id;
        if (filters.analist_id) params.analist_id = filters.analist_id;
        if (filters.measurement_date_start) params.measurement_date_start = filters.measurement_date_start;
        if (filters.measurement_date_end) params.measurement_date_end = filters.measurement_date_end;

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
        endLoading();
    }
};

const exportExcel = async () => {
    try {
        exporting.value = true;
        const payload = {
            sort_by: sortBy.value,
            sort_order: normalizeSortOrder(sortOrder.value)
        };
        if (filters.consecutivo) payload.consecutivo = filters.consecutivo.trim();
        if (filters.equipment_id) payload.equipment_id = filters.equipment_id;
        if (filters.condition_id) payload.condition_id = filters.condition_id;
        if (filters.priority_id) payload.priority_id = filters.priority_id;
        if (filters.mechanism_fail_id) payload.mechanism_fail_id = filters.mechanism_fail_id;
        if (filters.company_id) payload.company_id = filters.company_id;
        if (filters.process_id) payload.process_id = filters.process_id;
        if (filters.analist_id) payload.analist_id = filters.analist_id;
        if (filters.measurement_date_start) payload.measurement_date_start = filters.measurement_date_start;
        if (filters.measurement_date_end) payload.measurement_date_end = filters.measurement_date_end;
        const response = await api.post('/reports/export', payload, { responseType: 'blob' });
        const url = window.URL.createObjectURL(new Blob([response.data]));
        const link = document.createElement('a');
        const nowJs = new Date();
        const y = nowJs.getFullYear();
        const m = String(nowJs.getMonth() + 1).padStart(2, '0');
        const d = String(nowJs.getDate()).padStart(2, '0');
        const hh = String(nowJs.getHours()).padStart(2, '0');
        const mm = String(nowJs.getMinutes()).padStart(2, '0');
        const ss = String(nowJs.getSeconds()).padStart(2, '0');
        const filename = `Informes_Listado_${y}_${m}_${d}_${hh}_${mm}_${ss}.xlsx`;
        link.href = url;
        link.setAttribute('download', filename);
        link.style.display = 'none';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        window.URL.revokeObjectURL(url);
    } catch (error) {
        console.error('Error al exportar Excel:', error);
        const message = error.response?.data?.message || error.message || 'Error al exportar Excel';
        Swal.fire('Error', message, 'error');
    } finally {
        exporting.value = false;
    }
};

const changePage = (page) => {
    fetchReports(page);
};

const formatDate = (date) => {
    if (!date) return 'N/A';
    const str = String(date).trim();
    if (/^\d{4}-\d{2}-\d{2}$/.test(str)) {
        const [y, m, d] = str.split('-').map(Number);
        return new Date(y, m - 1, d).toLocaleDateString('es-ES');
    }
    return new Date(date).toLocaleDateString('es-ES');
};

const getConditionClass = () => {
    return 'bg-gray-100 text-gray-900 border border-gray-300';
};

// Date range handled via two native date inputs (with calendar UI)

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
