<template>
    <div class="container mx-auto px-4 sm:px-8">
        <div class="py-6">
            <!-- Header -->
            <div class="mb-6">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h2 class="text-3xl font-bold text-gray-800 flex items-center">
                            <i class="fas fa-cog text-blue-600 mr-3"></i>
                            Crear Nuevo Equipo
                        </h2>
                        <p class="text-gray-500 text-sm mt-1">Completa el formulario para crear un nuevo equipo</p>
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

            <!-- Form -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <form @submit.prevent="saveEquipment" class="space-y-6">
                    <div>
                        <div class="flex items-center mb-4 pb-2 border-b border-gray-200">
                            <i class="fas fa-info-circle text-blue-600 mr-2"></i>
                            <h4 class="text-lg font-semibold text-gray-800">Información del Equipo</h4>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            <!-- Nombre -->
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

                            <!-- Tipo Equipo -->
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

                            <!-- Proceso -->
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

                            <!-- Ubicación -->
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

                            <!-- Código -->
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
                                <p v-if="errors.code" class="mt-1 text-sm text-red-600">{{ errors.code }}</p>
                            </div>

                            <!-- Tag -->
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
                                <p v-if="errors.tag" class="mt-1 text-sm text-red-600">{{ errors.tag }}</p>
                            </div>

                            <!-- Empresa -->
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

                            <!-- Descripción -->
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
                                <p v-if="errors.description" class="mt-1 text-sm text-red-600">{{ errors.description }}</p>
                            </div>

                            <!-- Imagen del equipo -->
                            <div class="bg-gray-50 rounded-lg p-4 md:col-span-3">
                                <label class="flex items-center mb-2 text-sm font-medium text-gray-700">
                                    <i class="fas fa-image mr-2 text-blue-600"></i>
                                    Imagen del equipo
                                </label>
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
                            {{ saving ? 'Guardando...' : 'Guardar' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modales para gestionar opciones -->
        <CreateSelectModal
            v-model:show="showEquipmentTypeModal"
            field="equipment_type"
            label="Tipo Equipo"
            api-endpoint="/catalogs/equipment-types"
            @item-saved="handleItemSaved"
            @options-updated="loadEquipmentTypes"
        />
        <CreateSelectModal
            v-model:show="showProcessModal"
            field="process"
            label="Proceso"
            api-endpoint="/catalogs/processes"
            @item-saved="handleItemSaved"
            @options-updated="loadProcesses"
        />
        <CreateSelectModal
            v-model:show="showUbicationModal"
            field="ubication"
            label="Ubicación"
            api-endpoint="/catalogs/ubications"
            @item-saved="handleItemSaved"
            @options-updated="loadUbications"
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

const router = useRouter();
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
});
const imageFile = ref(null);

const equipmentTypes = ref([]);
const processes = ref([]);
const ubications = ref([]);
const companies = ref([]);
const saving = ref(false);
const errors = ref({});
const showEquipmentTypeModal = ref(false);
const showProcessModal = ref(false);
const showUbicationModal = ref(false);

onMounted(async () => {
    await loadCatalogs();
});

const loadCatalogs = async () => {
    try {
        const [equipmentTypesRes, processesRes, ubicationsRes, companiesRes] = await Promise.all([
            api.get('/catalogs/equipment-types'),
            api.get('/catalogs/processes'),
            api.get('/catalogs/ubications'),
            api.get('/catalogs/companies'),
        ]);

        equipmentTypes.value = equipmentTypesRes.data.data;
        processes.value = processesRes.data.data;
        ubications.value = ubicationsRes.data.data;
        companies.value = companiesRes.data.data;
    } catch (error) {
        Swal.fire('Error', 'Error al cargar catálogos', 'error');
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
        processes.value = response.data.data;
    } catch (error) {
        console.error('Error al cargar procesos:', error);
    }
};

const loadUbications = async () => {
    try {
        const response = await api.get('/catalogs/ubications');
        ubications.value = response.data.data;
    } catch (error) {
        console.error('Error al cargar ubicaciones:', error);
    }
};

const handleItemSaved = (data) => {
    // Seleccionar automáticamente el nuevo registro creado
    if (data.field === 'equipment_type' && data.id) {
        form.equipment_type_id = data.id;
    } else if (data.field === 'process' && data.id) {
        form.process_id = data.id;
    } else if (data.field === 'ubication' && data.id) {
        form.ubication_id = data.id;
    }
};

const onImageChange = (event) => {
    const file = event.target.files?.[0];
    imageFile.value = file || null;
};

const saveEquipment = async () => {
    saving.value = true;
    errors.value = {};

    try {
        const formData = new FormData();
        formData.append('name', form.name);
        formData.append('code', form.code);
        formData.append('tag', form.tag);
        formData.append('description', form.description);
        formData.append('company_id', form.company_id);
        formData.append('process_id', form.process_id);
        formData.append('ubication_id', form.ubication_id);
        formData.append('equipment_type_id', form.equipment_type_id);
        if (imageFile.value) {
            formData.append('image', imageFile.value);
        }
        await api.post('/equipment', formData, {
            headers: { 'Content-Type': 'multipart/form-data' },
        });
        Swal.fire('Éxito', 'Equipo creado correctamente', 'success');
        router.push({ name: 'equipment' });
    } catch (error) {
        if (error.response?.data?.errors) {
            errors.value = error.response.data.errors;
        }
        const message = error.response?.data?.message || 'Error al guardar el equipo';
        Swal.fire('Error', message, 'error');
    } finally {
        saving.value = false;
    }
};
</script>
