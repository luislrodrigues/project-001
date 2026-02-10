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
                            <i class="fas fa-weight text-blue-600 mr-3"></i>
                            Componentes
                        </h2>
                        <p class="text-gray-500 text-sm mt-1">Gestiona los componentes del sistema</p>
                    </div>
                    <router-link
                        v-if="authStore.hasPermission('save equipment')"
                        :to="{ name: 'components.create' }"
                        class="flex items-center justify-center w-full sm:w-auto px-5 py-3 sm:py-2.5 bg-blue-500 text-white rounded-lg hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors duration-200 shadow-sm font-medium"
                    >
                        <i class="fas fa-plus mr-2"></i>
                        Crear Componente
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
                                Equipo
                            </label>
                            <select
                                v-model="filters.equipment_id"
                                @change="fetchComponents"
                                class="block w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 px-4 py-2.5 transition-all duration-200"
                            >
                                <option value="">Todos</option>
                                <option v-for="eq in filteredEquipments" :key="eq.id" :value="eq.id">
                                    {{ eq.name }}
                                </option>
                            </select>
                        </div>
                        <div class="flex-1 min-w-[200px]">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-snowplow mr-2 text-gray-400"></i>
                                Tipo Componente
                            </label>
                            <select
                                v-model="filters.component_type_id"
                                @change="fetchComponents"
                                class="block w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 px-4 py-2.5 transition-all duration-200"
                            >
                                <option value="">Todos</option>
                                <option v-for="type in componentTypes" :key="type.id" :value="type.id">
                                    {{ type.name }}
                                </option>
                            </select>
                        </div>
                        <div class="flex-1 min-w-[200px]">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-tag mr-2 text-gray-400"></i>
                                Marca
                            </label>
                            <select
                                v-model="filters.brand_id"
                                @change="fetchComponents"
                                class="block w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 px-4 py-2.5 transition-all duration-200"
                            >
                                <option value="">Todas</option>
                                <option v-for="brand in brands" :key="brand.id" :value="brand.id">
                                    {{ brand.name }}
                                </option>
                            </select>
                        </div>
                        <div class="flex-shrink-0 flex gap-2">
                            <button
                                type="button"
                                @click="clearFilters"
                                class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-400 focus:ring-offset-2 transition-colors duration-200 font-medium whitespace-nowrap"
                            >
                                <i class="fas fa-times mr-2"></i>
                                Limpiar
                            </button>
                            <button
                                @click="fetchComponents"
                                class="px-5 py-2.5 bg-blue-500 hover:bg-blue-600 text-white rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors duration-200 font-medium whitespace-nowrap"
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
                <div v-if="components.length === 0 && !loading" class="p-8 text-center">
                    <i class="fas fa-weight text-gray-300 text-6xl mb-4"></i>
                    <p class="text-gray-500 text-lg">No hay componentes registrados</p>
                </div>

                <div v-else class="overflow-x-auto">
                    <table class="min-w-[640px] w-full divide-y divide-gray-200">
                        <thead class="bg-gradient-to-r from-blue-50 to-blue-100">
                            <tr>
                                <th
                                    class="px-6 py-4 text-left text-xs font-semibold text-blue-900 uppercase tracking-wider cursor-pointer hover:bg-blue-200/50 rounded-tl select-none"
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
                                        <i class="fas fa-snowplow mr-2"></i>
                                        Tipo Componente
                                    </div>
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-blue-900 uppercase tracking-wider">
                                    <div class="flex items-center">
                                        <i class="fas fa-tag mr-2"></i>
                                        Marca
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
                            <tr v-for="component in components" :key="component.id" class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ component.id }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-700">{{ component.equipment && component.equipment.company && component.equipment.company.name ? component.equipment.company.name : '—' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ component.equipment && component.equipment.name ? component.equipment.name : '' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-500">{{ component.component_type && component.component_type.name ? component.component_type.name : '' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-500">{{ component.brand && component.brand.name ? component.brand.name : '' }}</div>
                                </td>
                                <td v-if="authStore.hasPermission('detalle equipment') || authStore.hasPermission('edit equipment') || authStore.hasPermission('delete equipment')" class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex items-center justify-end space-x-2">
                                        <router-link
                                            v-if="authStore.hasPermission('detalle equipment')"
                                            :to="{ name: 'components.show', params: { id: component.id } }"
                                            class="text-green-600 hover:text-green-900 p-2 hover:bg-green-50 rounded-lg transition-colors"
                                            title="Ver detalles"
                                        >
                                            <i class="fas fa-eye"></i>
                                        </router-link>
                                        <router-link
                                            v-if="authStore.hasPermission('edit equipment')"
                                            :to="{ name: 'components.edit', params: { id: component.id } }"
                                            class="text-blue-600 hover:text-blue-900 p-2 hover:bg-blue-50 rounded-lg transition-colors"
                                            title="Editar"
                                        >
                                            <i class="fas fa-edit"></i>
                                        </router-link>
                                        <button
                                            v-if="authStore.hasPermission('delete equipment')"
                                            @click="deleteComponent(component.id)"
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
                v-if="pagination && components.length > 0"
                :pagination="pagination"
                @page-change="changePage"
            />
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, reactive, computed, watch } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import { useAuthStore } from '../stores/auth';
import api from '../services/api';
import Swal from 'sweetalert2';
import Pagination from '../components/Pagination.vue';

const STORAGE_KEY = 'components-list-filters';

const router = useRouter();
const route = useRoute();
const authStore = useAuthStore();

const components = ref([]);
const loading = ref(false);
const pagination = ref(null);
const equipments = ref([]);
const componentTypes = ref([]);
const companies = ref([]);
const brands = ref([]);

const sortBy = ref('id');
const normalizeSortOrder = (v) => (v === 'asc' || v === 'desc' ? v : 'desc');
const sortOrder = ref(normalizeSortOrder(route.query.sort_order ?? 'desc'));

const filters = reactive({
    company_id: route.query.company_id ?? '',
    equipment_id: route.query.equipment_id ?? '',
    component_type_id: route.query.component_type_id ?? '',
    brand_id: route.query.brand_id ?? '',
});

let isRestoringFilters = false;

const syncQueryToUrl = (page = 1) => {
    const q = {
        page: String(page),
        sort_order: sortOrder.value,
        company_id: filters.company_id,
        equipment_id: filters.equipment_id,
        component_type_id: filters.component_type_id,
        brand_id: filters.brand_id
    };
    Object.keys(q).forEach(key => { if (q[key] === '') delete q[key]; });
    router.replace({ name: 'components', query: q });
    try {
        sessionStorage.setItem(STORAGE_KEY, JSON.stringify({ query: q }));
    } catch (_) {}
};

const applyQueryToFilters = () => {
    isRestoringFilters = true;
    const hasQuery = Object.keys(route.query).length > 0;
    if (hasQuery) {
        filters.company_id = route.query.company_id ?? '';
        filters.equipment_id = route.query.equipment_id ?? '';
        filters.component_type_id = route.query.component_type_id ?? '';
        filters.brand_id = route.query.brand_id ?? '';
        sortOrder.value = route.query.sort_order ?? 'desc';
    } else {
        try {
            const saved = sessionStorage.getItem(STORAGE_KEY);
            if (saved) {
                const { query } = JSON.parse(saved);
                if (query) {
                    filters.company_id = query.company_id ?? '';
                    filters.equipment_id = query.equipment_id ?? '';
                    filters.component_type_id = query.component_type_id ?? '';
                    filters.brand_id = query.brand_id ?? '';
                    sortOrder.value = query.sort_order ?? 'desc';
                }
            }
        } catch (_) {}
    }
    isRestoringFilters = false;
};

// Equipos filtrados por empresa seleccionada; sin duplicados por nombre (muestra cada nombre una vez)
const filteredEquipments = computed(() => {
    let list = equipments.value;
    if (filters.company_id) {
        const companyId = Number(filters.company_id);
        list = list.filter(
            (eq) => Number(eq.company_id ?? eq.company?.id) === companyId
        );
    }
    const seen = new Set();
    return list.filter((eq) => {
        const key = `${String(eq.name ?? '').trim()}|${Number(eq.company_id ?? eq.company?.id) ?? ''}`;
        if (seen.has(key)) return false;
        seen.add(key);
        return true;
    });
});

// Al cambiar empresa, limpiar equipo si ya no aplica
watch(
    () => filters.company_id,
    () => {
        if (!filters.company_id) {
            filters.equipment_id = '';
            if (!isRestoringFilters) fetchComponents(1);
            return;
        }
        const companyId = Number(filters.company_id);
        const currentEq = equipments.value.find((e) => Number(e.id) === Number(filters.equipment_id));
        if (currentEq && Number(currentEq.company_id ?? currentEq.company?.id) !== companyId) {
            filters.equipment_id = '';
        }
        if (!isRestoringFilters) fetchComponents(1);
    }
);

const toggleSortId = () => {
    sortOrder.value = sortOrder.value === 'desc' ? 'asc' : 'desc';
    fetchComponents(1);
};

onMounted(async () => {
    await Promise.all([loadCatalogs(), fetchComponents()]);
});

const loadCatalogs = async () => {
    try {
        const [equipmentsRes, componentTypesRes, companiesRes, brandsRes] = await Promise.all([
            api.get('/catalogs/equipment'),
            api.get('/catalogs/component-types'),
            api.get('/catalogs/companies'),
            api.get('/catalogs/brands'),
        ]);

        // Deduplicar equipos por id (evita duplicados en el dropdown)
        const rawEquipments = equipmentsRes.data.data ?? [];
        equipments.value = rawEquipments.filter(
            (eq, i, arr) => arr.findIndex((e) => Number(e.id) === Number(eq.id)) === i
        );
        componentTypes.value = componentTypesRes.data.data ?? [];
        companies.value = companiesRes.data.data ?? [];
        brands.value = brandsRes.data.data ?? [];
    } catch (error) {
        console.error('Error al cargar catálogos:', error);
    }
};

const clearFilters = () => {
    filters.company_id = '';
    filters.equipment_id = '';
    filters.component_type_id = '';
    filters.brand_id = '';
    fetchComponents(1);
};

const fetchComponents = async (page = 1) => {
    loading.value = true;
    try {
        const params = { page, per_page: 10, sort_by: sortBy.value, sort_order: normalizeSortOrder(sortOrder.value) };

        if (filters.company_id) params.company_id = filters.company_id;
        if (filters.equipment_id) params.equipment_id = filters.equipment_id;
        if (filters.component_type_id) params.component_type_id = filters.component_type_id;
        if (filters.brand_id) params.brand_id = filters.brand_id;

        const response = await api.get('/components', { params });
        components.value = response.data.data;
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
        Swal.fire('Error', 'Error al cargar componentes', 'error');
    } finally {
        loading.value = false;
    }
};

const changePage = (page) => {
    fetchComponents(page);
};

const deleteComponent = async (id) => {
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
            await api.delete(`/components/${id}`);
            Swal.fire('Eliminado', 'El componente se eliminó correctamente', 'success');
            fetchComponents();
        } catch (error) {
            Swal.fire('Error', 'Error al eliminar el componente', 'error');
        }
    }
};
</script>
