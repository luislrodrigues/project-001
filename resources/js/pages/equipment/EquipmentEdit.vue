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
                            <i class="fas fa-cog text-blue-600 mr-3"></i>
                            Editar Equipo
                        </h2>
                        <p class="text-gray-500 text-sm mt-1">Modifica la información del equipo</p>
                    </div>
                    <router-link
                        :to="{ name: 'equipment' }"
                        class="text-gray-600 hover:text-gray-900 p-2 hover:bg-gray-100 rounded-lg transition-colors"
                        title="Volver"
                    >
                        <i class="fas fa-arrow-left text-xl"></i>
                    </router-link>
                </div>
            </div>

            <!-- Form (similar a Create) -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <form @submit.prevent="updateEquipment" class="space-y-6">
                    <div>
                        <div class="flex items-center mb-4 pb-2 border-b border-gray-200">
                            <i class="fas fa-info-circle text-blue-600 mr-2"></i>
                            <h4 class="text-lg font-semibold text-gray-800">Información del Equipo</h4>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            <!-- Mismos campos que Create -->
                            <div class="bg-gray-50 rounded-lg p-4">
                                <label class="flex items-center mb-2 text-sm font-medium text-gray-700">
                                    <i class="fas fa-tag mr-2 text-blue-600"></i>
                                    Nombre
                                    <span class="text-red-500 ml-1">*</span>
                                </label>
                                <input
                                    v-model="form.name"
                                    type="text"
                                    required
                                    class="block w-full bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 px-4 py-2.5 transition-all duration-200"
                                    placeholder="Ingresar nombre"
                                />
                                <p v-if="errors.name" class="mt-1 text-sm text-red-600">{{ errors.name }}</p>
                            </div>

                            <div class="bg-gray-50 rounded-lg p-4">
                                <label class="flex items-center mb-2 text-sm font-medium text-gray-700">
                                    <i class="fas fa-cog mr-2 text-blue-600"></i>
                                    Tipo equipo
                                    <span class="text-red-500 ml-1">*</span>
                                </label>
                                <div class="flex space-x-2 items-stretch">
                                    <select
                                        v-model="form.equipment_type_id"
                                        required
                                        class="flex-1 min-w-0 bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 px-4 py-2.5 transition-all duration-200"
                                    >
                                        <option value="">Seleccione</option>
                                        <option v-for="type in equipmentTypes" :key="type.id" :value="type.id">
                                            {{ type.name }}
                                        </option>
                                    </select>
                                    <button
                                        type="button"
                                        @click="showEquipmentTypeModal = true"
                                        class="flex-shrink-0 w-10 h-10 bg-blue-500 hover:bg-blue-600 text-white rounded-lg transition-colors duration-200 flex items-center justify-center"
                                        title="Gestionar tipos de equipo"
                                    >
                                        <i class="fas fa-cog text-sm"></i>
                                    </button>
                                </div>
                                <p v-if="errors.equipment_type_id" class="mt-1 text-sm text-red-600">{{ errors.equipment_type_id }}</p>
                            </div>

                            <div class="bg-gray-50 rounded-lg p-4">
                                <label class="flex items-center mb-2 text-sm font-medium text-gray-700">
                                    <i class="fas fa-tasks mr-2 text-blue-600"></i>
                                    Proceso
                                    <span class="text-red-500 ml-1">*</span>
                                </label>
                                <div class="flex space-x-2 items-stretch">
                                    <select
                                        v-model="form.process_id"
                                        required
                                        class="flex-1 min-w-0 bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 px-4 py-2.5 transition-all duration-200"
                                    >
                                        <option value="">Seleccione</option>
                                        <option v-for="process in processes" :key="process.id" :value="process.id">
                                            {{ process.name }}
                                        </option>
                                    </select>
                                    <button
                                        type="button"
                                        @click="showProcessModal = true"
                                        class="flex-shrink-0 w-10 h-10 bg-blue-500 hover:bg-blue-600 text-white rounded-lg transition-colors duration-200 flex items-center justify-center"
                                        title="Gestionar procesos"
                                    >
                                        <i class="fas fa-cog text-sm"></i>
                                    </button>
                                </div>
                                <p v-if="errors.process_id" class="mt-1 text-sm text-red-600">{{ errors.process_id }}</p>
                            </div>

                            <div class="bg-gray-50 rounded-lg p-4">
                                <label class="flex items-center mb-2 text-sm font-medium text-gray-700">
                                    <i class="fas fa-map-marker-alt mr-2 text-blue-600"></i>
                                    Ubicación
                                    <span class="text-red-500 ml-1">*</span>
                                </label>
                                <div class="flex space-x-2 items-stretch">
                                    <select
                                        v-model="form.ubication_id"
                                        required
                                        class="flex-1 min-w-0 bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 px-4 py-2.5 transition-all duration-200"
                                    >
                                        <option value="">Seleccione</option>
                                        <option v-for="ubication in ubications" :key="ubication.id" :value="ubication.id">
                                            {{ ubication.name }}
                                        </option>
                                    </select>
                                    <button
                                        type="button"
                                        @click="showUbicationModal = true"
                                        class="flex-shrink-0 w-10 h-10 bg-blue-500 hover:bg-blue-600 text-white rounded-lg transition-colors duration-200 flex items-center justify-center"
                                        title="Gestionar ubicaciones"
                                    >
                                        <i class="fas fa-cog text-sm"></i>
                                    </button>
                                </div>
                                <p v-if="errors.ubication_id" class="mt-1 text-sm text-red-600">{{ errors.ubication_id }}</p>
                            </div>

                            <div class="bg-gray-50 rounded-lg p-4">
                                <label class="flex items-center mb-2 text-sm font-medium text-gray-700">
                                    <i class="fas fa-barcode mr-2 text-blue-600"></i>
                                    Código
                                </label>
                                <input
                                    v-model="form.code"
                                    type="text"
                                    class="block w-full bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 px-4 py-2.5 transition-all duration-200"
                                    placeholder="Ingresar código"
                                />
                            </div>

                            <div class="bg-gray-50 rounded-lg p-4">
                                <label class="flex items-center mb-2 text-sm font-medium text-gray-700">
                                    <i class="fas fa-tag mr-2 text-blue-600"></i>
                                    Tag
                                </label>
                                <input
                                    v-model="form.tag"
                                    type="text"
                                    class="block w-full bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 px-4 py-2.5 transition-all duration-200"
                                    placeholder="Ingresar tag"
                                />
                            </div>

                            <div class="bg-gray-50 rounded-lg p-4">
                                <label class="flex items-center mb-2 text-sm font-medium text-gray-700">
                                    <i class="fas fa-building mr-2 text-blue-600"></i>
                                    Empresa
                                    <span class="text-red-500 ml-1">*</span>
                                </label>
                                <select
                                    v-model="form.company_id"
                                    required
                                    class="block w-full bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 px-4 py-2.5 transition-all duration-200"
                                >
                                    <option value="">Seleccione</option>
                                    <option v-for="company in companies" :key="company.id" :value="company.id">
                                        {{ company.name }}
                                    </option>
                                </select>
                                <p v-if="errors.company_id" class="mt-1 text-sm text-red-600">{{ errors.company_id }}</p>
                            </div>

                            <div class="bg-gray-50 rounded-lg p-4 md:col-span-3">
                                <label class="flex items-center mb-2 text-sm font-medium text-gray-700">
                                    <i class="fas fa-align-left mr-2 text-blue-600"></i>
                                    Descripción
                                </label>
                                <textarea
                                    v-model="form.description"
                                    class="block w-full bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 px-4 py-2.5 transition-all duration-200 min-h-[80px]"
                                    placeholder="Ingresar descripción"
                                ></textarea>
                            </div>

                            <div class="bg-gray-50 rounded-lg p-4 md:col-span-3">
                                <label class="flex items-center mb-2 text-sm font-medium text-gray-700">
                                    <i class="fas fa-image mr-2 text-blue-600"></i>
                                    Imagen del equipo
                                </label>
                                <img
                                    v-if="imageUrl"
                                    :src="imageUrl"
                                    alt="Equipo"
                                    class="mb-3 max-h-40 rounded-lg object-contain border border-gray-200"
                                />
                                <input
                                    type="file"
                                    accept="image/jpeg,image/jpg,image/png"
                                    @change="onImageChange"
                                    class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
                                />
                                <p class="mt-1 text-xs text-gray-500">JPG o PNG. Máx. 2 MB. Se mostrará en el reporte PDF.</p>
                                <p v-if="errors.image" class="mt-1 text-sm text-red-600">{{ errors.image }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Componentes del equipo -->
                    <div class="pt-6 border-t border-gray-200">
                        <div class="flex items-center justify-between mb-4 pb-2 border-b border-gray-200">
                            <div class="flex items-center">
                                <i class="fas fa-puzzle-piece text-blue-600 mr-2"></i>
                                <h4 class="text-lg font-semibold text-gray-800">Componentes del equipo</h4>
                            </div>
                            <button
                                type="button"
                                @click="addComponent"
                                class="flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors duration-200 shadow-sm"
                            >
                                <i class="fas fa-plus mr-2"></i>
                                Agregar componente
                            </button>
                        </div>
                        <p class="text-sm text-gray-500 mb-4">Opcional. Añade o modifica los componentes que forman parte de este equipo.</p>

                        <div v-if="form.components.length === 0" class="bg-gray-50 border border-dashed border-gray-200 rounded-lg p-6 text-center text-gray-500">
                            <i class="fas fa-puzzle-piece text-2xl mb-2 text-gray-400"></i>
                            <p>No hay componentes agregados. Haz clic en "Agregar componente" para añadir uno.</p>
                        </div>

                        <div v-else class="space-y-6">
                            <div
                                v-for="(comp, compIndex) in form.components"
                                :key="compIndex"
                                class="bg-gray-50 rounded-lg border border-gray-200 p-4"
                            >
                                <div class="flex items-center justify-between mb-4">
                                    <span class="font-medium text-gray-800">Componente #{{ compIndex + 1 }}</span>
                                    <div class="flex items-center gap-1">
                                        <button
                                            type="button"
                                            @click="moveComponent(compIndex, -1)"
                                            :disabled="compIndex === 0"
                                            class="p-2 rounded text-gray-600 hover:bg-gray-200 disabled:opacity-30 disabled:cursor-not-allowed transition-colors"
                                            title="Subir"
                                        >
                                            <i class="fas fa-arrow-up"></i>
                                        </button>
                                        <button
                                            type="button"
                                            @click="moveComponent(compIndex, 1)"
                                            :disabled="compIndex === form.components.length - 1"
                                            class="p-2 rounded text-gray-600 hover:bg-gray-200 disabled:opacity-30 disabled:cursor-not-allowed transition-colors"
                                            title="Bajar"
                                        >
                                            <i class="fas fa-arrow-down"></i>
                                        </button>
                                        <button
                                            type="button"
                                            @click="removeComponent(compIndex)"
                                            class="p-2 rounded text-red-600 hover:bg-red-50 transition-colors"
                                            title="Quitar componente"
                                        >
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Tipo de componente <span class="text-red-500">*</span></label>
                                        <select
                                            v-model="comp.component_type_id"
                                            @change="loadComponentTypeFieldsForRow(compIndex)"
                                            class="block w-full bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-blue-500 px-4 py-2.5"
                                        >
                                            <option value="">Seleccione</option>
                                            <option v-for="type in componentTypes" :key="type.id" :value="type.id">
                                                {{ type.name }}
                                            </option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Marca <span class="text-red-500">*</span></label>
                                        <div class="flex space-x-2 items-stretch">
                                            <select
                                                v-model="comp.brand_id"
                                                class="flex-1 min-w-0 bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-blue-500 px-4 py-2.5"
                                            >
                                                <option value="">Seleccione</option>
                                                <option v-for="brand in brands" :key="brand.id" :value="brand.id">
                                                    {{ brand.name }}
                                                </option>
                                            </select>
                                            <button
                                                type="button"
                                                @click="openBrandModal(compIndex)"
                                                class="flex-shrink-0 w-10 h-10 bg-blue-500 hover:bg-blue-600 text-white rounded-lg transition-colors duration-200 flex items-center justify-center"
                                                title="Gestionar marcas"
                                            >
                                                <i class="fas fa-cog text-sm"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div v-if="comp._fields && comp._fields.length > 0" class="pt-4 border-t border-gray-200">
                                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                        <div
                                            v-for="field in comp._fields"
                                            :key="field.id"
                                            class="bg-white rounded-lg p-3 border border-gray-200"
                                        >
                                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                                {{ field.name }}
                                                <span v-if="field.unit" class="text-xs text-gray-500">({{ field.unit }})</span>
                                                <span v-if="field.is_required" class="text-red-500">*</span>
                                            </label>
                                            <select
                                                v-if="field.type === 'select' || field.type === 'multiselect'"
                                                v-model="comp.field_values[field.id]"
                                                :multiple="field.type === 'multiselect'"
                                                class="block w-full border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-blue-500 px-3 py-2"
                                            >
                                                <option value="">Seleccione</option>
                                                <option v-for="opt in (field.options || [])" :key="opt.id" :value="opt.value">
                                                    {{ opt.label || opt.value }}
                                                </option>
                                            </select>
                                            <select
                                                v-else-if="field.type === 'boolean'"
                                                v-model="comp.field_values[field.id]"
                                                class="block w-full border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-blue-500 px-3 py-2"
                                            >
                                                <option value="">Seleccione</option>
                                                <option value="1">Sí</option>
                                                <option value="0">No</option>
                                            </select>
                                            <input
                                                v-else-if="field.type === 'date' || field.type === 'datetime'"
                                                :type="field.type === 'date' ? 'date' : 'datetime-local'"
                                                v-model="comp.field_values[field.id]"
                                                class="block w-full border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-blue-500 px-3 py-2"
                                            />
                                            <textarea
                                                v-else-if="field.type === 'textarea'"
                                                v-model="comp.field_values[field.id]"
                                                rows="2"
                                                class="block w-full border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-blue-500 px-3 py-2"
                                            />
                                            <input
                                                v-else
                                                :type="field.type === 'numeric' ? 'number' : 'text'"
                                                :step="field.type === 'numeric' ? 'any' : undefined"
                                                v-model="comp.field_values[field.id]"
                                                class="block w-full border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-blue-500 px-3 py-2"
                                            />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
                        <router-link
                            :to="{ name: 'equipment' }"
                            class="px-6 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition-colors font-medium"
                        >
                            Cancelar
                        </router-link>
                        <button
                            type="submit"
                            :disabled="saving"
                            class="px-6 py-2.5 bg-blue-500 hover:bg-blue-600 text-white rounded-lg transition-colors font-medium shadow-sm disabled:opacity-50"
                        >
                            <i class="fas fa-save mr-2"></i>
                            {{ saving ? 'Actualizando...' : 'Actualizar' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modales para gestionar opciones -->
        <CreateSelectModal
            :show="showEquipmentTypeModal"
            @update:show="showEquipmentTypeModal = $event"
            field="equipment_type"
            label="Tipo Equipo"
            api-endpoint="/catalogs/equipment-types"
            @item-saved="handleItemSaved"
            @options-updated="loadEquipmentTypes"
        />
        <CreateSelectModal
            :show="showProcessModal"
            @update:show="showProcessModal = $event"
            field="process"
            label="Proceso"
            api-endpoint="/catalogs/processes"
            @item-saved="handleItemSaved"
            @options-updated="loadProcesses"
        />
        <CreateSelectModal
            :show="showUbicationModal"
            @update:show="showUbicationModal = $event"
            field="ubication"
            label="Ubicación"
            api-endpoint="/catalogs/ubications"
            @item-saved="handleItemSaved"
            @options-updated="loadUbications"
        />
        <CreateSelectModal
            :show="showBrandModal"
            @update:show="showBrandModal = $event"
            field="brand"
            label="Marca"
            api-endpoint="/catalogs/brands"
            @item-saved="handleBrandSaved"
            @options-updated="loadBrands"
        />
    </div>
</template>

<script setup>
import { ref, onMounted, reactive } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import { useAuthStore } from '../../stores/auth';
import api from '../../services/api';
import Swal from 'sweetalert2';
import CreateSelectModal from '../../components/CreateSelectModal.vue';

const router = useRouter();
const route = useRoute();
const authStore = useAuthStore();

const form = reactive({
    name: '',
    equipment_type_id: '',
    process_id: '',
    ubication_id: '',
    code: '',
    tag: '',
    description: '',
    company_id: '',
    components: [],
});

const equipmentTypes = ref([]);
const processes = ref([]);
const ubications = ref([]);
const companies = ref([]);
const componentTypes = ref([]);
const brands = ref([]);
const loading = ref(true);
const saving = ref(false);
const errors = ref({});
const showEquipmentTypeModal = ref(false);
const showProcessModal = ref(false);
const showUbicationModal = ref(false);
const showBrandModal = ref(false);
const currentComponentIndexForBrand = ref(null);
const imageUrl = ref(null);
const imageFile = ref(null);

onMounted(async () => {
    loading.value = true;
    try {
        await loadCatalogs();
        await loadEquipment();
    } finally {
        loading.value = false;
    }
});

const loadCatalogs = async () => {
    try {
        const [equipmentTypesRes, processesRes, ubicationsRes, companiesRes, componentTypesRes, brandsRes] = await Promise.all([
            api.get('/catalogs/equipment-types'),
            api.get('/catalogs/processes'),
            api.get('/catalogs/ubications'),
            api.get('/catalogs/companies'),
            api.get('/catalogs/component-types'),
            api.get('/catalogs/brands'),
        ]);

        equipmentTypes.value = equipmentTypesRes.data?.data ?? equipmentTypesRes.data ?? [];
        processes.value = processesRes.data?.data ?? processesRes.data ?? [];
        ubications.value = ubicationsRes.data?.data ?? ubicationsRes.data ?? [];
        companies.value = companiesRes.data?.data ?? companiesRes.data ?? [];
        componentTypes.value = componentTypesRes.data?.data ?? componentTypesRes.data ?? [];
        brands.value = brandsRes.data?.data ?? brandsRes.data ?? [];
    } catch (error) {
        Swal.fire('Error', 'Error al cargar catálogos', 'error');
    }
};

const loadEquipment = async () => {
    try {
        const response = await api.get(`/equipment/${route.params.id}`);
        const equipment = response.data?.data ?? response.data;

        form.name = equipment.name ?? '';
        form.equipment_type_id = equipment.equipment_type_id ?? (equipment.equipment_type?.id ?? '');
        form.process_id = equipment.process_id ?? (equipment.process?.id ?? '');
        form.ubication_id = equipment.ubication_id ?? (equipment.ubication?.id ?? '');
        form.code = equipment.code ?? '';
        form.tag = equipment.tag ?? '';
        form.description = equipment.description ?? '';
        form.company_id = equipment.company_id ?? (equipment.company?.id ?? '');
        imageUrl.value = equipment.image_url || null;

        const components = equipment.components ?? [];
        const savedFieldValues = components.map((c) => ({ ...(c.field_values || {}) }));
        form.components = components.map((c) => ({
            component_type_id: c.component_type_id ?? (c.component_type?.id ?? ''),
            brand_id: c.brand_id ?? (c.brand?.id ?? ''),
            field_values: {},
            _fields: [],
        }));
        for (let i = 0; i < form.components.length; i++) {
            await loadComponentTypeFieldsForRow(i);
            const defaults = form.components[i].field_values || {};
            form.components[i].field_values = { ...defaults, ...savedFieldValues[i] };
        }
    } catch (error) {
        Swal.fire('Error', 'Error al cargar el equipo', 'error');
        router.push({ name: 'equipment' });
    }
};

const addComponent = () => {
    form.components.push({
        component_type_id: '',
        brand_id: '',
        field_values: {},
        _fields: [],
    });
};

const removeComponent = (index) => {
    form.components.splice(index, 1);
};

const moveComponent = (index, direction) => {
    const newIndex = index + direction;
    if (newIndex < 0 || newIndex >= form.components.length) return;
    const arr = form.components;
    [arr[index], arr[newIndex]] = [arr[newIndex], arr[index]];
};

const loadComponentTypeFieldsForRow = async (index) => {
    const comp = form.components[index];
    if (!comp || !comp.component_type_id) {
        comp._fields = [];
        comp.field_values = {};
        return;
    }
    try {
        const response = await api.get(`/components/type/${comp.component_type_id}/fields`);
        const fields = response.data?.data ?? response.data ?? [];
        comp._fields = fields;
        comp.field_values = {};
        fields.forEach((f) => {
            comp.field_values[f.id] = f.type === 'multiselect' ? (comp.field_values[f.id] || []) : (comp.field_values[f.id] ?? '');
        });
    } catch (error) {
        console.error('Error al cargar campos del tipo de componente:', error);
        comp._fields = [];
        comp.field_values = {};
    }
};

const loadEquipmentTypes = async () => {
    try {
        const response = await api.get('/catalogs/equipment-types');
        equipmentTypes.value = response.data?.data ?? response.data ?? [];
    } catch (error) {
        console.error('Error al cargar tipos de equipo:', error);
    }
};

const loadProcesses = async () => {
    try {
        const response = await api.get('/catalogs/processes');
        processes.value = response.data?.data ?? response.data ?? [];
    } catch (error) {
        console.error('Error al cargar procesos:', error);
    }
};

const loadUbications = async () => {
    try {
        const response = await api.get('/catalogs/ubications');
        ubications.value = response.data?.data ?? response.data ?? [];
    } catch (error) {
        console.error('Error al cargar ubicaciones:', error);
    }
};

const loadBrands = async () => {
    try {
        const response = await api.get('/catalogs/brands');
        brands.value = response.data?.data ?? response.data ?? [];
    } catch (error) {
        console.error('Error al cargar marcas:', error);
    }
};

const handleItemSaved = (data) => {
    if (data.field === 'equipment_type' && data.id) {
        form.equipment_type_id = data.id;
    } else if (data.field === 'process' && data.id) {
        form.process_id = data.id;
    } else if (data.field === 'ubication' && data.id) {
        form.ubication_id = data.id;
    }
};

const openBrandModal = (compIndex) => {
    currentComponentIndexForBrand.value = compIndex;
    showBrandModal.value = true;
};

const handleBrandSaved = (data) => {
    if (data.field === 'brand' && data.id && currentComponentIndexForBrand.value !== null) {
        const comp = form.components[currentComponentIndexForBrand.value];
        if (comp) {
            comp.brand_id = data.id;
        }
    }
    currentComponentIndexForBrand.value = null;
    loadBrands();
};

const onImageChange = (event) => {
    const file = event.target.files?.[0];
    imageFile.value = file || null;
    if (file) {
        imageUrl.value = URL.createObjectURL(file);
    }
};

const updateEquipment = async () => {
    saving.value = true;
    errors.value = {};

    try {
        const formData = new FormData();
        formData.append('_method', 'PUT');
        formData.append('name', form.name);
        formData.append('code', form.code ?? '');
        formData.append('tag', form.tag ?? '');
        formData.append('description', form.description ?? '');
        formData.append('company_id', form.company_id);
        formData.append('process_id', form.process_id);
        formData.append('ubication_id', form.ubication_id);
        formData.append('equipment_type_id', form.equipment_type_id);
        if (imageFile.value) {
            formData.append('image', imageFile.value);
        }
        const componentsPayload = form.components
            .filter((c) => c.component_type_id && c.brand_id)
            .map((c, index) => ({
                component_type_id: c.component_type_id,
                brand_id: c.brand_id,
                display_order: index,
                field_values: c.field_values || {},
            }));
        formData.append('components', JSON.stringify(componentsPayload));
        await api.post(`/equipment/${route.params.id}`, formData, {
            headers: { 'Content-Type': 'multipart/form-data' },
        });
        Swal.fire('Éxito', 'Equipo actualizado correctamente', 'success');
        router.push({ name: 'equipment' });
    } catch (error) {
        if (error.response?.status === 422 && error.response?.data?.errors) {
            const e = error.response.data.errors;
            errors.value = Object.fromEntries(
                Object.entries(e).map(([k, v]) => [k, Array.isArray(v) ? v[0] : v])
            );
        }
        const message = error.response?.data?.message || 'Error al actualizar el equipo';
        Swal.fire('Error', message, 'error');
    } finally {
        saving.value = false;
    }
};
</script>
