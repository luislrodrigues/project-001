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
                            <i class="fas fa-weight text-blue-600 mr-3"></i>
                            Componentes
                        </h2>
                        <p class="text-gray-500 text-sm mt-1">Gestiona los componentes del sistema</p>
                    </div>
                    <router-link
                        v-if="authStore.hasPermission('save equipment')"
                        :to="{ name: 'components.create' }"
                        class="flex items-center px-5 py-2.5 bg-blue-500 text-white rounded-lg hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors duration-200 shadow-sm font-medium"
                    >
                        <i class="fas fa-plus mr-2"></i>
                        Crear Componente
                    </router-link>
                </div>

                <!-- Search Section -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                        <div>
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
                                <option v-for="eq in equipments" :key="eq.id" :value="eq.id">
                                    {{ eq.name }}
                                </option>
                            </select>
                        </div>
                        <div>
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
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-building mr-2 text-gray-400"></i>
                                Empresa
                            </label>
                            <select
                                v-model="filters.company_id"
                                @change="fetchComponents"
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
                    </div>
                    <div class="mt-3 flex justify-end">
                        <button
                            @click="fetchComponents"
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
                <div v-if="components.length === 0 && !loading" class="p-8 text-center">
                    <i class="fas fa-weight text-gray-300 text-6xl mb-4"></i>
                    <p class="text-gray-500 text-lg">No hay componentes registrados</p>
                </div>

                <div v-else class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gradient-to-r from-blue-50 to-blue-100">
                            <tr>
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
                                <th class="px-6 py-4 text-right text-xs font-semibold text-blue-900 uppercase tracking-wider">
                                    <div class="flex items-center justify-end">
                                        <i class="fas fa-cog mr-2"></i>
                                        Acciones
                                    </div>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="component in components" :key="component.id" class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ component.equipment?.name }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-500">{{ component.component_type?.name }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-500">{{ component.brand?.name }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex items-center justify-end space-x-2">
                                        <router-link
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
            <div v-if="pagination && components.length > 0" class="mt-4 bg-white rounded-lg shadow-sm border border-gray-200 p-4">
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
import { useRouter } from 'vue-router';
import { useAuthStore } from '../stores/auth';
import api from '../services/api';
import Swal from 'sweetalert2';

const router = useRouter();
const authStore = useAuthStore();

const components = ref([]);
const loading = ref(false);
const pagination = ref(null);
const equipments = ref([]);
const componentTypes = ref([]);
const companies = ref([]);
const brands = ref([]);

const filters = reactive({
    equipment_id: '',
    component_type_id: '',
    company_id: '',
    brand_id: '',
});

onMounted(async () => {
    await Promise.all([loadCatalogs(), fetchComponents()]);
});

const loadCatalogs = async () => {
    try {
        const [equipmentsRes, componentTypesRes, companiesRes, brandsRes] = await Promise.all([
            api.get('/equipment'),
            api.get('/component-types'),
            api.get('/catalogs/companies'),
            api.get('/catalogs/brands'),
        ]);

        equipments.value = equipmentsRes.data.data;
        componentTypes.value = componentTypesRes.data.data;
        companies.value = companiesRes.data.data;
        brands.value = brandsRes.data.data;
    } catch (error) {
        console.error('Error al cargar catálogos:', error);
    }
};

const fetchComponents = async (page = 1) => {
    loading.value = true;
    try {
        const params = { page, per_page: 10 };
        
        if (filters.equipment_id) params.equipment_id = filters.equipment_id;
        if (filters.component_type_id) params.component_type_id = filters.component_type_id;
        if (filters.company_id) params.company_id = filters.company_id;
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
