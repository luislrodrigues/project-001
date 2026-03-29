<template>
    <div class="container mx-auto px-4 sm:px-8">
        <div v-if="catalogsLoading" class="fixed top-0 left-0 w-screen h-screen flex items-center justify-center z-50 bg-black bg-opacity-30 backdrop-blur-sm">
            <div class="flex flex-col items-center justify-center h-full">
                <svg class="animate-spin h-16 w-16 text-blue-600 mr-3 mb-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="10" stroke-opacity="0.25" stroke-width="2" stroke="currentColor" fill="none"></circle>
                    <path d="M12 2a10 10 0 00-9.5 7.5M12 2c2.5 0 5 1 7 2.5" stroke="currentColor" stroke-width="2" fill="none"></path>
                </svg>
                <p class="text-gray-700 font-medium">Cargando formulario...</p>
            </div>
        </div>
        <div v-else class="py-6">
            <!-- Header -->
            <div class="mb-6">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h2 class="text-3xl font-bold text-gray-800 flex items-center">
                            <i class="fas fa-weight text-blue-600 mr-3"></i>
                            Crear Nuevo Componente
                        </h2>
                        <p class="text-gray-500 text-sm mt-1">Completa el formulario para crear un nuevo componente</p>
                    </div>
                    <router-link
                        :to="{ name: 'components' }"
                        class="text-gray-600 hover:text-gray-900 p-2 hover:bg-gray-100 rounded-lg transition-colors"
                        title="Volver"
                    >
                        <i class="fas fa-arrow-left text-xl"></i>
                    </router-link>
                </div>
            </div>

            <!-- Form -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <form @submit.prevent="saveComponent" class="space-y-6">
                    <!-- Información Básica -->
                    <div>
                        <div class="flex items-center mb-4 pb-2 border-b border-gray-200">
                            <i class="fas fa-info-circle text-blue-600 mr-2"></i>
                            <h4 class="text-lg font-semibold text-gray-800">Información Básica</h4>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <!-- Equipo -->
                            <div class="bg-gray-50 rounded-lg p-4">
                                <label class="flex items-center mb-2 text-sm font-medium text-gray-700">
                                    <i class="fas fa-cog mr-2 text-blue-600"></i>
                                    Equipo
                                    <span class="text-red-500 ml-1">*</span>
                                </label>
                                <Select2
                                    v-model="form.equipment_id"
                                    :options="equipments"
                                    allLabel="Seleccione"
                                    required
                                    class="block w-full bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 px-4 py-2.5 transition-all duration-200"
                                />
                                <p v-if="errors.equipment_id" class="mt-1 text-sm text-red-600">{{ errors.equipment_id }}</p>
                            </div>

                            <!-- Tipo Componente -->
                            <div class="bg-gray-50 rounded-lg p-4">
                                <label class="flex items-center mb-2 text-sm font-medium text-gray-700">
                                    <i class="fas fa-snowplow mr-2 text-blue-600"></i>
                                    Tipo Componente
                                    <span class="text-red-500 ml-1">*</span>
                                </label>
                                <Select2
                                    v-model="form.component_type_id"
                                    :options="componentTypes"
                                    allLabel="Seleccione"
                                    required
                                    @change="loadComponentTypeFields"
                                    class="block w-full bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 px-4 py-2.5 transition-all duration-200"
                                />
                                <p v-if="errors.component_type_id" class="mt-1 text-sm text-red-600">{{ errors.component_type_id }}</p>
                            </div>

                            <!-- Marca -->
                            <div class="bg-gray-50 rounded-lg p-4">
                                <label class="flex items-center mb-2 text-sm font-medium text-gray-700">
                                    <i class="fas fa-tag mr-2 text-blue-600"></i>
                                    Marca
                                    <span class="text-red-500 ml-1">*</span>
                                </label>
                                <div class="flex space-x-2 items-stretch">
                                    <Select2
                                        v-model="form.brand_id"
                                        :options="brands"
                                        allLabel="Seleccione"
                                        required
                                        class="flex-1 min-w-0 bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 px-4 py-2.5 transition-all duration-200"
                                    />
                                    <button
                                        type="button"
                                        @click="showBrandModal = true"
                                        class="flex-shrink-0 w-10 h-10 bg-blue-500 hover:bg-blue-600 text-white rounded-lg transition-colors duration-200 flex items-center justify-center"
                                        title="Gestionar marcas"
                                    >
                                        <i class="fas fa-cog text-sm"></i>
                                    </button>
                                </div>
                                <p v-if="errors.brand_id" class="mt-1 text-sm text-red-600">{{ errors.brand_id }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Campos Adicionales -->
                    <div v-if="additionalFields.length > 0">
                        <div class="flex items-center mb-4 pb-2 border-b border-gray-200">
                            <i class="fas fa-list-ul text-blue-600 mr-2"></i>
                            <h4 class="text-lg font-semibold text-gray-800">Atributos Adicionales</h4>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            <div
                                v-for="field in additionalFields"
                                :key="field.id"
                                class="bg-gray-50 rounded-lg p-4"
                            >
                                <label class="flex items-center mb-2 text-sm font-medium text-gray-700">
                                    <i class="fas fa-info-circle mr-2 text-blue-600"></i>
                                    {{ field.name }}
                                    <span v-if="field.unit" class="ml-2 text-xs text-gray-500">({{ field.unit }})</span>
                                    <span v-if="field.is_required" class="text-red-500 ml-1">*</span>
                                </label>

                                <!-- Select/Multiselect -->
                                <Select2
                                    v-if="field.type === 'select' || field.type === 'multiselect'"
                                    :id="`field_${field.id}`"
                                    v-model="form.field_values[field.id]"
                                    :options="field.options"
                                    labelKey="label"
                                    valueKey="value"
                                    allLabel="Seleccione una opción"
                                    :multiple="field.type === 'multiselect'"
                                    :required="field.is_required"
                                    class="block w-full bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 px-4 py-2.5 transition-all duration-200"
                                />

                                <!-- Boolean -->
                                <Select2
                                    v-else-if="field.type === 'boolean'"
                                    :id="`field_${field.id}`"
                                    v-model="form.field_values[field.id]"
                                    :options="yesNoOptions"
                                    allLabel="Seleccione"
                                    :search="false"
                                    :required="field.is_required"
                                    class="block w-full bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 px-4 py-2.5 transition-all duration-200"
                                />

                                <!-- Date/Datetime -->
                                <input
                                    v-else-if="field.type === 'date' || field.type === 'datetime'"
                                    :type="field.type === 'date' ? 'date' : 'datetime-local'"
                                    :id="`field_${field.id}`"
                                    v-model="form.field_values[field.id]"
                                    :required="field.is_required"
                                    :placeholder="field.placeholder || `Ingresar ${field.name}`"
                                    class="block w-full bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 px-4 py-2.5 transition-all duration-200"
                                />

                                <!-- Textarea -->
                                <textarea
                                    v-else-if="field.type === 'textarea'"
                                    :id="`field_${field.id}`"
                                    v-model="form.field_values[field.id]"
                                    :required="field.is_required"
                                    :placeholder="field.placeholder || `Ingresar ${field.name}`"
                                    rows="3"
                                    class="block w-full bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 px-4 py-2.5 transition-all duration-200"
                                ></textarea>

                                <!-- Text, Number, Email, URL -->
                                <input
                                    v-else
                                    :type="getInputType(field.type)"
                                    :step="field.type === 'numeric' ? 'any' : undefined"
                                    :id="`field_${field.id}`"
                                    v-model="form.field_values[field.id]"
                                    :required="field.is_required"
                                    :placeholder="field.placeholder || `Ingresar ${field.name}`"
                                    class="block w-full bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 px-4 py-2.5 transition-all duration-200"
                                />

                                <p v-if="field.help_text" class="mt-1 text-xs text-gray-500">
                                    <i class="fas fa-info-circle mr-1"></i>
                                    {{ field.help_text }}
                                </p>
                                <p v-if="errors[`field_values.${field.id}`]" class="mt-1 text-sm text-red-600">
                                    {{ errors[`field_values.${field.id}`] }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
                        <router-link
                            :to="{ name: 'components' }"
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
                            {{ saving ? 'Guardando...' : 'Guardar' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal para gestionar marcas -->
        <CreateSelectModal
            v-model:show="showBrandModal"
            field="brand"
            label="Marca"
            api-endpoint="/catalogs/brands"
            @item-saved="handleItemSaved"
            @options-updated="loadBrands"
        />
    </div>
</template>

<script setup>
import { ref, onMounted, reactive } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '../../stores/auth';
import api from '../../services/api';
import Swal from 'sweetalert2';
import CreateSelectModal from '../../components/CreateSelectModal.vue';
import Select2 from '../../components/Select2.vue';

const router = useRouter();
const authStore = useAuthStore();

const form = reactive({
    equipment_id: '',
    component_type_id: '',
    brand_id: '',
    field_values: {},
});

const equipments = ref([]);
const componentTypes = ref([]);
const brands = ref([]);
const additionalFields = ref([]);
const catalogsLoading = ref(true);
const saving = ref(false);
const errors = ref({});
const showBrandModal = ref(false);

const yesNoOptions = [
    { id: '1', name: 'Sí' },
    { id: '0', name: 'No' },
];

onMounted(async () => {
    await loadCatalogs();
});

const loadCatalogs = async () => {
    catalogsLoading.value = true;
    try {
        const [equipmentsRes, componentTypesRes, brandsRes] = await Promise.all([
            api.get('/catalogs/equipment'),
            api.get('/catalogs/component-types'),
            api.get('/catalogs/brands'),
        ]);

        equipments.value = equipmentsRes.data?.data ?? equipmentsRes.data ?? [];
        componentTypes.value = componentTypesRes.data?.data ?? componentTypesRes.data ?? [];
        brands.value = brandsRes.data?.data ?? brandsRes.data ?? [];
    } catch (error) {
        console.error('Error al cargar catálogos:', error);
        const msg = error.response?.data?.message || error.message || 'Error al cargar catálogos';
        Swal.fire('Error', msg, 'error');
    } finally {
        catalogsLoading.value = false;
    }
};

const loadBrands = async () => {
    try {
        const response = await api.get('/catalogs/brands');
        brands.value = response.data.data;
    } catch (error) {
        console.error('Error al cargar marcas:', error);
    }
};

const handleItemSaved = (data) => {
    if (data.field === 'brand' && data.id) {
        form.brand_id = data.id;
    }
    loadBrands();
};

const loadComponentTypeFields = async () => {
    if (!form.component_type_id) {
        additionalFields.value = [];
        form.field_values = {};
        return;
    }

    try {
        const response = await api.get(`/components/type/${form.component_type_id}/fields`);
        additionalFields.value = response.data.data;
        form.field_values = {};
        additionalFields.value.forEach(field => {
            form.field_values[field.id] = '';
        });
    } catch (error) {
        Swal.fire('Error', 'Error al cargar campos del tipo de componente', 'error');
    }
};

const getInputType = (fieldType) => {
    const types = {
        'numeric': 'number',
        'email': 'email',
        'url': 'url',
        'text': 'text',
    };
    return types[fieldType] || 'text';
};

const saveComponent = async () => {
    saving.value = true;
    errors.value = {};

    try {
        await api.post('/components', form);
        Swal.fire('Éxito', 'Componente creado correctamente', 'success');
        router.push({ name: 'components' });
    } catch (error) {
        if (error.response?.status === 422 && error.response?.data?.errors) {
            const e = error.response.data.errors;
            errors.value = Object.fromEntries(
                Object.entries(e).map(([k, v]) => [k, Array.isArray(v) ? v[0] : v])
            );
        }
        const message = error.response?.data?.message || 'Error al guardar el componente';
        Swal.fire('Error', message, 'error');
    } finally {
        saving.value = false;
    }
};
</script>
