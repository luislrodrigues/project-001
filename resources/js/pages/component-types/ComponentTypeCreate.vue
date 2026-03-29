<template>
    <div class="container mx-auto px-4 sm:px-8">
        <div class="py-6">
            <!-- Header -->
            <div class="mb-6">
                            <div class="flex items-center justify-between mb-4">
                    <div>
                        <h2 class="text-3xl font-bold text-gray-800 flex items-center">
                            <i class="fas fa-snowplow text-blue-600 mr-3"></i>
                            Crear Nuevo Tipo de Componente
                        </h2>
                        <p class="text-gray-500 text-sm mt-1">Completa el formulario para crear un nuevo tipo de componente</p>
                    </div>
                    <router-link
                        :to="{ name: 'component-types' }"
                        class="text-gray-600 hover:text-gray-900 p-2 hover:bg-gray-100 rounded-lg transition-colors"
                        title="Volver"
                    >
                        <i class="fas fa-arrow-left text-xl"></i>
                    </router-link>
                </div>
            </div>

            <!-- Form -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <form @submit.prevent="saveComponentType" class="space-y-6">
                    <!-- Información Básica -->
                    <div>
                        <div class="flex items-center mb-4 pb-2 border-b border-gray-200">
                            <i class="fas fa-info-circle text-blue-600 mr-2"></i>
                            <h4 class="text-lg font-semibold text-gray-800">Información Básica</h4>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <label class="flex items-center mb-2 text-sm font-medium text-gray-700">
                                <i class="fas fa-tag mr-2 text-blue-600"></i>
                                Nombre del tipo de componente
                                <span class="text-red-500 ml-1">*</span>
                            </label>
                            <input
                                v-model="form.name"
                                type="text"
                                required
                                class="block w-full bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 px-4 py-2.5 transition-all duration-200"
                                placeholder="Ej: Motor, Sensor, Válvula..."
                            />
                            <p v-if="errors.name" class="mt-1 text-sm text-red-600">{{ errors.name }}</p>
                        </div>
                    </div>

                    <!-- Atributos -->
                    <div>
                        <div class="flex items-center justify-between mb-4 pb-2 border-b border-gray-200">
                            <div class="flex items-center">
                                <i class="fas fa-list-ul text-blue-600 mr-2"></i>
                                <h4 class="text-lg font-semibold text-gray-800">Atributos del Componente</h4>
                            </div>
                            <button
                                type="button"
                                @click="addField"
                                class="flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors duration-200 shadow-sm"
                            >
                                <i class="fas fa-plus mr-2"></i>
                                Agregar Atributo
                            </button>
                        </div>

                        <div v-if="form.fields.length === 0" class="bg-yellow-50 border border-yellow-200 rounded-lg p-6 text-center">
                            <i class="fas fa-info-circle text-yellow-600 text-2xl mb-2"></i>
                            <p class="text-gray-600">No hay atributos agregados. Haz clic en "Agregar Atributo" para comenzar.</p>
                        </div>

                        <div class="space-y-4">
                            <div
                                v-for="(field, index) in form.fields"
                                :key="index"
                                class="bg-white border border-gray-200 rounded-lg p-5 shadow-sm hover:shadow-md transition-shadow duration-200"
                            >
                                <div class="flex items-center justify-between mb-4">
                                    <div class="flex items-center space-x-2">
                                        <div class="bg-blue-100 rounded-full w-8 h-8 flex items-center justify-center">
                                            <span class="text-blue-600 font-semibold text-sm">{{ index + 1 }}</span>
                                        </div>
                                        <h5 class="font-medium text-gray-800">Atributo #{{ index + 1 }}</h5>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <div class="flex items-center gap-2">
                                            <button
                                                type="button"
                                                @click="moveFieldUp(index)"
                                                class="w-9 h-9 flex items-center justify-center text-blue-600 border border-blue-200 rounded-lg transition-colors duration-200"
                                                :class="index === 0 ? 'opacity-40 cursor-not-allowed' : 'hover:bg-blue-50'"
                                                :disabled="index === 0"
                                                title="Mover arriba"
                                            >
                                                <i class="fas fa-arrow-up text-sm"></i>
                                            </button>
                                            <button
                                                type="button"
                                                @click="moveFieldDown(index)"
                                                class="w-9 h-9 flex items-center justify-center text-blue-600 border border-blue-200 rounded-lg transition-colors duration-200"
                                                :class="index === form.fields.length - 1 ? 'opacity-40 cursor-not-allowed' : 'hover:bg-blue-50'"
                                                :disabled="index === form.fields.length - 1"
                                                title="Mover abajo"
                                            >
                                                <i class="fas fa-arrow-down text-sm"></i>
                                            </button>
                                        </div>
                                        <button
                                            type="button"
                                            @click="removeField(index)"
                                            class="flex items-center px-3 py-2 bg-red-100 hover:bg-red-200 text-red-700 rounded-lg transition-colors duration-200"
                                        >
                                            <i class="fas fa-trash mr-1"></i>
                                            <span class="text-sm">Eliminar</span>
                                        </button>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                                    <!-- Nombre del atributo -->
                                    <div class="md:col-span-2">
                                        <label class="flex items-center mb-2 text-sm font-medium text-gray-700">
                                            <i class="fas fa-font mr-2 text-gray-500 text-xs"></i>
                                            Nombre del atributo
                                            <span class="text-red-500 ml-1">*</span>
                                        </label>
                                        <input
                                            v-model="field.name"
                                            type="text"
                                            required
                                            class="block w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 px-4 py-2.5 transition-all duration-200"
                                            placeholder="Ej: Potencia, Velocidad, Temperatura..."
                                        />
                                        <p v-if="errors[`fields.${index}.name`]" class="mt-1 text-xs text-red-600">
                                            {{ errors[`fields.${index}.name`] }}
                                        </p>
                                    </div>

                                    <!-- Tipo de campo -->
                                    <div>
                                        <label class="flex items-center mb-2 text-sm font-medium text-gray-700">
                                            <i class="fas fa-tag mr-2 text-gray-500 text-xs"></i>
                                            Tipo
                                            <span class="text-red-500 ml-1">*</span>
                                        </label>
                                        <Select2
                                            v-model="field.type"
                                            :options="fieldTypeOptions"
                                            allLabel="Seleccione"
                                            :search="false"
                                            class="block w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 px-4 py-2.5 transition-all duration-200"
                                        />
                                        <p v-if="errors[`fields.${index}.type`]" class="mt-1 text-xs text-red-600">
                                            {{ errors[`fields.${index}.type`] }}
                                        </p>
                                    </div>

                                    <!-- Unidad (solo para tipo numeric) -->
                                    <div v-if="field.type === 'numeric'">
                                        <label class="flex items-center mb-2 text-sm font-medium text-gray-700">
                                            <i class="fas fa-ruler mr-2 text-gray-500 text-xs"></i>
                                            Unidad
                                        </label>
                                        <input
                                            v-model="field.unit"
                                            type="text"
                                            class="block w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 px-4 py-2.5 transition-all duration-200"
                                            placeholder="Ej: kW, RPM, °C..."
                                        />
                                    </div>

                                    <!-- Campo requerido -->
                                    <div v-if="field.type !== 'number'" class="flex items-end">
                                        <div class="w-full">
                                            <label class="flex items-center mb-2 text-sm font-medium text-gray-700">
                                                <i class="fas fa-asterisk mr-2 text-gray-500 text-xs"></i>
                                                Requerido
                                            </label>
                                            <div class="flex items-center h-10">
                                                <input
                                                    v-model="field.is_required"
                                                    type="checkbox"
                                                    class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500"
                                                />
                                                <span class="ml-2 text-xs text-gray-600">Campo obligatorio</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Sección de opciones (solo para select/multiselect) -->
                                <div v-if="field.type === 'select' || field.type === 'multiselect'" class="mt-4 pt-4 border-t border-gray-200">
                                    <label class="flex items-center mb-3 text-sm font-medium text-gray-700">
                                        <i class="fas fa-list mr-2 text-blue-600"></i>
                                        Opciones de selección
                                        <span v-if="field.options && field.options.length > 0" class="ml-2 px-2 py-0.5 bg-green-100 text-green-700 rounded-full text-xs font-semibold">
                                            {{ field.options.length }} {{ field.options.length === 1 ? 'opción' : 'opciones' }}
                                        </span>
                                    </label>

                                    <button
                                        type="button"
                                        @click="openOptionsManager(index)"
                                        class="w-full px-4 py-2.5 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors duration-200 shadow-sm"
                                    >
                                        <i class="fas fa-cog mr-2"></i>
                                        Gestionar Opciones
                                    </button>

                                    <!-- Vista previa de opciones -->
                                    <div v-if="field.options && field.options.length > 0" class="mt-3 p-3 bg-blue-50 rounded-lg border border-blue-200">
                                        <div class="text-xs font-semibold text-blue-700 mb-2 flex items-center">
                                            <i class="fas fa-eye mr-1"></i>
                                            Vista previa
                                        </div>
                                        <div class="flex flex-wrap gap-2">
                                            <span
                                                v-for="(opt, optIndex) in field.options"
                                                :key="optIndex"
                                                :class="opt.is_default ? 'bg-yellow-100 text-yellow-800 border border-yellow-300' : 'bg-blue-100 text-blue-800 border border-blue-200'"
                                                class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium"
                                            >
                                                {{ opt.label }}
                                                <i v-if="opt.is_default" class="fas fa-star ml-1.5 text-yellow-500"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
                        <router-link
                            :to="{ name: 'component-types' }"
                            class="px-6 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition-colors font-medium"
                        >
                            Cancelar
                        </router-link>
                        <button
                            type="submit"
                            :disabled="saving || form.fields.length === 0"
                            class="px-6 py-2.5 bg-blue-500 hover:bg-blue-600 text-white rounded-lg transition-colors font-medium shadow-sm disabled:opacity-50"
                        >
                            <i class="fas fa-save mr-2"></i>
                            {{ saving ? 'Guardando...' : 'Guardar' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal para gestionar opciones -->
        <FieldOptionsManager
            v-model:show="showOptionsModal"
            :options="currentFieldOptions"
            @update:options="updateFieldOptions"
        />
    </div>
</template>

<script setup>
import { ref, reactive } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '../../stores/auth';
import api from '../../services/api';
import Swal from 'sweetalert2';
import FieldOptionsManager from '../../components/FieldOptionsManager.vue';
import Select2 from '../../components/Select2.vue';

const router = useRouter();
const authStore = useAuthStore();

const form = reactive({
    name: '',
    fields: []
});

const saving = ref(false);
const errors = ref({});
const showOptionsModal = ref(false);
const currentFieldIndex = ref(null);
const currentFieldOptions = ref([]);

const fieldTypeOptions = [
    { id: 'text', name: '📝 Texto' },
    { id: 'numeric', name: '🔢 Numérico' },
    { id: 'textarea', name: '📄 Texto largo' },
    { id: 'select', name: '☑️ Selección' },
    { id: 'multiselect', name: '☑️ Selección Múltiple' },
    { id: 'date', name: '📅 Fecha' },
    { id: 'datetime', name: '📅 Fecha y Hora' },
    { id: 'boolean', name: '✓ Sí/No' },
];

const addField = () => {
    form.fields.push({
        name: '',
        type: '',
        unit: '',
        is_required: false,
        options: []
    });
};

const removeField = (index) => {
    form.fields.splice(index, 1);
};

const moveField = (fromIndex, toIndex) => {
    if (toIndex < 0 || toIndex >= form.fields.length) return;
    const movedField = form.fields.splice(fromIndex, 1)[0];
    form.fields.splice(toIndex, 0, movedField);
};

const moveFieldUp = (index) => {
    moveField(index, index - 1);
};

const moveFieldDown = (index) => {
    moveField(index, index + 1);
};

const openOptionsManager = (index) => {
    currentFieldIndex.value = index;
    currentFieldOptions.value = form.fields[index].options || [];
    showOptionsModal.value = true;
};

const updateFieldOptions = (options) => {
    if (currentFieldIndex.value !== null) {
        form.fields[currentFieldIndex.value].options = options;
        currentFieldOptions.value = options;
    }
};

const saveComponentType = async () => {
    if (form.fields.length === 0) {
        Swal.fire('Atención', 'Debes agregar al menos un atributo', 'warning');
        return;
    }

    // Validar que campos select/multiselect tengan opciones
    for (let i = 0; i < form.fields.length; i++) {
        const field = form.fields[i];
        if ((field.type === 'select' || field.type === 'multiselect') && (!field.options || field.options.length === 0)) {
            Swal.fire('Error', `El campo "${field.name}" requiere al menos una opción`, 'error');
            return;
        }
    }

    saving.value = true;
    errors.value = {};

    try {
        await api.post('/component-types', form);
        Swal.fire('Éxito', 'Tipo de componente creado correctamente', 'success');
        router.push({ name: 'component-types' });
    } catch (error) {
        if (error.response?.status === 422 && error.response?.data?.errors) {
            const e = error.response.data.errors;
            errors.value = Object.fromEntries(
                Object.entries(e).map(([k, v]) => [k, Array.isArray(v) ? v[0] : v])
            );
        }
        const message = error.response?.data?.message || 'Error al crear el tipo de componente';
        Swal.fire('Error', message, 'error');
    } finally {
        saving.value = false;
    }
};
</script>
