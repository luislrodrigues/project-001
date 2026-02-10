<template>
    <div
        v-if="show"
        class="fixed z-50 inset-0 overflow-y-auto flex items-center justify-center py-4"
        @click.self="closeModal"
    >
        <!-- Overlay -->
        <div
            class="fixed inset-0 bg-gray-900 bg-opacity-75 backdrop-blur-sm transition-opacity z-40"
            @click="closeModal"
        ></div>

        <!-- Modal content -->
        <div class="relative z-50 p-4 w-full max-w-2xl my-auto" @click.stop>
            <div class="relative bg-white rounded-xl shadow-2xl overflow-hidden max-h-[90vh] flex flex-col">
                <!-- Header -->
                <div class="flex items-center justify-between px-4 py-3 bg-gradient-to-r from-blue-500 to-blue-600 rounded-t-xl">
                    <div class="flex items-center space-x-2">
                        <div class="bg-white bg-opacity-20 rounded-lg p-1.5">
                            <i class="fas fa-list text-white text-lg"></i>
                        </div>
                        <h3 class="text-lg font-bold text-white">
                            Gestionar {{ label }}
                        </h3>
                    </div>
                    <button
                        type="button"
                        @click="closeModal"
                        class="text-white hover:bg-white hover:bg-opacity-20 rounded-lg text-sm w-7 h-7 inline-flex justify-center items-center transition-colors duration-200"
                    >
                        <svg class="w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                        </svg>
                    </button>
                </div>

                <!-- Body -->
                <div class="p-6 overflow-y-auto flex-1">
                    <!-- Formulario para crear/editar -->
                    <div class="mb-4 bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg p-3 border-2 border-blue-200">
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center">
                                <div class="bg-blue-500 rounded-full p-1.5 mr-2">
                                    <i class="fas text-white text-sm" :class="editingId ? 'fa-edit' : 'fa-plus-circle'"></i>
                                </div>
                                <div>
                                    <h4 class="text-base font-bold text-gray-800">
                                        {{ editingId ? 'Editar' : 'Crear nuevo' }} {{ label }}
                                    </h4>
                                    <p class="text-xs text-gray-600">
                                        {{ editingId ? 'Modifica el nombre del registro' : 'Ingresa un nuevo registro' }}
                                    </p>
                                </div>
                            </div>
                            <button
                                v-if="editingId"
                                type="button"
                                @click="cancelEdit"
                                class="px-2 py-1.5 bg-gray-500 hover:bg-gray-600 text-white rounded-lg transition-colors duration-200"
                                title="Cancelar edición"
                            >
                                <i class="fas fa-times text-xs"></i>
                            </button>
                        </div>
                        <div class="bg-white rounded-lg p-3 shadow-sm space-y-3">
                            <div>
                                <label class="flex items-center mb-1.5 text-xs font-medium text-gray-700">
                                    <i class="fas fa-tag mr-1.5 text-blue-600 text-xs"></i>
                                    Nombre
                                    <span class="text-red-500 ml-1">*</span>
                                </label>
                                <input
                                    v-model="valueNew"
                                    type="text"
                                    @keydown.enter.prevent="saveField"
                                    class="w-full bg-white border-2 border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 block px-3 py-2 transition-all duration-200"
                                    :placeholder="`Ejemplo: Nombre del ${label.toLowerCase()}`"
                                />
                                <p v-if="errors.valueNew" class="mt-1.5 text-xs text-red-600">
                                    <i class="fas fa-exclamation-circle mr-1 text-xs"></i>
                                    {{ errors.valueNew }}
                                </p>
                            </div>
                            <div v-if="field === 'condition' || field === 'priority'">
                                <label class="flex items-center mb-1.5 text-xs font-medium text-gray-700">
                                    <i class="fas fa-palette mr-1.5 text-blue-600 text-xs"></i>
                                    Color de fondo
                                </label>
                                <div class="flex gap-2 items-center mb-3">
                                    <input
                                        v-model="valueColor"
                                        type="color"
                                        class="h-9 w-14 cursor-pointer rounded border border-gray-300"
                                        :title="valueColor"
                                    />
                                    <input
                                        v-model="valueColor"
                                        type="text"
                                        placeholder="#22c55e o green"
                                        class="flex-1 bg-white border-2 border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 block px-3 py-2"
                                    />
                                </div>
                                <label class="flex items-center mb-1.5 text-xs font-medium text-gray-700">
                                    <i class="fas fa-font mr-1.5 text-blue-600 text-xs"></i>
                                    Color de letra
                                </label>
                                <div class="flex gap-2 items-center">
                                    <input
                                        v-model="valueTextColor"
                                        type="color"
                                        class="h-9 w-14 cursor-pointer rounded border border-gray-300"
                                        :title="valueTextColor"
                                    />
                                    <input
                                        v-model="valueTextColor"
                                        type="text"
                                        placeholder="#000000 o black"
                                        class="flex-1 bg-white border-2 border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 block px-3 py-2"
                                    />
                                </div>
                            </div>
                        </div>
                        <div class="flex justify-end mt-3">
                            <button
                                type="button"
                                @click="saveField"
                                :disabled="saving"
                                class="flex items-center px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg transition-colors duration-200 text-sm font-medium shadow-md hover:shadow-lg disabled:opacity-50"
                            >
                                <i class="fas mr-1.5 text-xs" :class="editingId ? 'fa-edit' : 'fa-save'"></i>
                                {{ saving ? 'Guardando...' : (editingId ? 'Actualizar' : 'Crear y Agregar') }}
                            </button>
                        </div>
                    </div>

                    <!-- Lista de registros existentes -->
                    <div>
                        <div class="flex items-center justify-between mb-3 pb-2 border-b border-gray-200">
                            <div class="flex items-center">
                                <i class="fas fa-list-ul text-blue-600 mr-1.5 text-sm"></i>
                                <h4 class="text-base font-semibold text-gray-800">
                                    Registros Existentes
                                </h4>
                            </div>
                            <div class="relative">
                                <input
                                    v-model="search"
                                    type="text"
                                    placeholder="Buscar..."
                                    class="pl-8 pr-3 py-1.5 text-xs border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 w-32"
                                />
                                <i class="fas fa-search absolute left-2 top-1/2 transform -translate-y-1/2 text-gray-400 text-xs"></i>
                            </div>
                        </div>

                        <div class="max-h-56 overflow-y-auto">
                            <div v-if="filteredItems.length > 0" class="space-y-1.5">
                                <div
                                    v-for="item in filteredItems"
                                    :key="item.id"
                                    class="flex items-center justify-between bg-gray-50 rounded-lg p-2.5 border border-gray-200 hover:bg-gray-100 transition-colors duration-200"
                                >
                                    <div class="flex items-center space-x-2 flex-1">
                                        <div
                                            v-if="(field === 'condition' || field === 'priority') && item.color"
                                            class="rounded-full w-5 h-5 flex-shrink-0 border border-gray-300"
                                            :style="{ backgroundColor: item.color }"
                                        ></div>
                                        <div v-else class="bg-blue-100 rounded-full w-6 h-6 flex items-center justify-center flex-shrink-0">
                                            <i class="fas fa-tag text-blue-600 text-xs"></i>
                                        </div>
                                        <span class="text-xs font-medium text-gray-900 truncate">
                                            {{ item.name }}
                                        </span>
                                        <span
                                            v-if="item.status"
                                            :class="item.status === 'active' ? 'bg-green-100 text-green-800 border border-green-300' : 'bg-gray-100 text-gray-800 border border-gray-300'"
                                            class="px-1.5 py-0.5 text-xs rounded-full flex-shrink-0"
                                        >
                                            {{ item.status === 'active' ? 'Activo' : 'Inactivo' }}
                                        </span>
                                    </div>
                                    <div class="flex items-center space-x-1.5 flex-shrink-0">
                                        <button
                                            type="button"
                                            @click="startEdit(item.id)"
                                            class="px-2 py-1.5 bg-blue-500 hover:bg-blue-600 text-white rounded-lg transition-colors duration-200"
                                            title="Editar"
                                        >
                                            <i class="fas fa-edit text-xs"></i>
                                        </button>
                                        <button
                                            type="button"
                                            @click="deleteItem(item.id)"
                                            class="px-2 py-1.5 bg-red-500 hover:bg-red-600 text-white rounded-lg transition-colors duration-200"
                                            title="Eliminar"
                                        >
                                            <i class="fas fa-trash text-xs"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div v-else class="text-center py-8 bg-gray-50 rounded-lg border-2 border-dashed border-gray-300">
                                <i v-if="search" class="fas fa-search text-3xl text-gray-400 mb-3"></i>
                                <i v-else class="fas fa-inbox text-3xl text-gray-400 mb-3"></i>
                                <p class="text-sm text-gray-500">
                                    {{ search ? `No se encontraron resultados para "${search}"` : 'No hay registros disponibles' }}
                                </p>
                                <p v-if="!search" class="text-xs text-gray-400 mt-1">
                                    Crea el primer registro usando el formulario de arriba
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="flex justify-end px-4 py-3 bg-gray-50 border-t border-gray-200 flex-shrink-0">
                    <button
                        type="button"
                        @click="closeModal"
                        class="flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition-colors duration-200 text-sm font-medium"
                    >
                        <i class="fas fa-times mr-1.5 text-xs"></i>
                        Cerrar
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue';
import api from '../services/api';
import Swal from 'sweetalert2';

const props = defineProps({
    show: {
        type: Boolean,
        default: false
    },
    field: {
        type: String,
        required: true
    },
    label: {
        type: String,
        required: true
    },
    apiEndpoint: {
        type: String,
        required: true
    }
});

const emit = defineEmits(['update:show', 'item-saved', 'options-updated']);

const valueNew = ref('');
const valueColor = ref('#22c55e');
const valueTextColor = ref('#000000');
const editingId = ref(null);
const search = ref('');
const items = ref([]);
const saving = ref(false);
const loading = ref(false);
const errors = ref({});

const endpointMap = {
    'brand': '/catalogs/brands',
    'company_segment': '/catalogs/company-segments',
    'equipment_type': '/catalogs/equipment-types',
    'process': '/catalogs/processes',
    'ubication': '/catalogs/ubications',
    'condition': '/catalogs/conditions',
    'priority': '/catalogs/priorities',
    'component_failure': '/catalogs/component-fails',
    'failure_mechanism': '/catalogs/mechanism-fails',
};

const createEndpointMap = {
    'brand': 'brands',
    'company_segment': 'company-segments',
    'equipment_type': 'equipment-types',
    'process': 'processes',
    'ubication': 'ubications',
    'condition': 'conditions',
    'priority': 'priorities',
    'component_failure': 'component-fails',
    'failure_mechanism': 'mechanism-fails',
};

const filteredItems = computed(() => {
    if (!search.value) return items.value;
    return items.value.filter(item =>
        item.name.toLowerCase().includes(search.value.toLowerCase())
    );
});

watch(() => props.show, async (newVal) => {
    if (newVal) {
        await loadItems();
    } else {
        resetForm();
    }
});

const loadItems = async () => {
    loading.value = true;
    try {
        const endpoint = endpointMap[props.field] || props.apiEndpoint;
        const response = await api.get(endpoint);
        items.value = response.data.data || [];
    } catch (error) {
        console.error('Error al cargar registros:', error);
        items.value = [];
    } finally {
        loading.value = false;
    }
};

const startEdit = (id) => {
    const item = items.value.find(i => i.id === id);
    if (item) {
        editingId.value = id;
        valueNew.value = item.name;
        valueColor.value = item.color || '#22c55e';
        valueTextColor.value = item.text_color || '#000000';
        errors.value = {};
    }
};

const cancelEdit = () => {
    editingId.value = null;
    valueNew.value = '';
    valueColor.value = '#22c55e';
    valueTextColor.value = '#000000';
    errors.value = {};
};

const saveField = async () => {
    saving.value = true;
    errors.value = {};

    if (!valueNew.value.trim()) {
        errors.value.valueNew = 'El nombre es requerido';
        saving.value = false;
        return;
    }

    try {
        let catalogName = createEndpointMap[props.field];
        if (!catalogName && props.apiEndpoint) {
            catalogName = props.apiEndpoint.replace('/catalogs/', '').replace(/^\//, '');
        }

        if (!catalogName) {
            throw new Error('No se pudo determinar el nombre del catálogo');
        }

        const endpoint = `/catalogs/${catalogName}`;

        const payload = { name: valueNew.value.trim() };
        if (catalogName === 'conditions' || catalogName === 'priorities') {
            if (valueColor.value) payload.color = valueColor.value.trim();
            if (valueTextColor.value) payload.text_color = valueTextColor.value.trim();
        }
        if (editingId.value) {
            await api.put(`${endpoint}/${editingId.value}`, payload);
            Swal.fire('Éxito', 'Registro actualizado correctamente', 'success');
        } else {
            const response = await api.post(endpoint, payload);
            Swal.fire('Éxito', 'Registro creado correctamente', 'success');

            // Emitir evento con el ID del nuevo registro
            emit('item-saved', {
                field: props.field,
                id: response.data.data?.id || response.data.id
            });

            closeModal();
        }

        await loadItems();
        emit('options-updated');
        resetForm();
    } catch (error) {
        if (error.response?.data?.errors) {
            errors.value = error.response.data.errors;
        }
        const message = error.response?.data?.message || 'Error al guardar el registro';
        Swal.fire('Error', message, 'error');
    } finally {
        saving.value = false;
    }
};

const deleteItem = async (id) => {
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
            // Obtener el nombre del catálogo desde el mapa o desde apiEndpoint
            let catalogName = createEndpointMap[props.field];
            if (!catalogName && props.apiEndpoint) {
                catalogName = props.apiEndpoint.replace('/catalogs/', '').replace(/^\//, '');
            }

            if (!catalogName) {
                throw new Error('No se pudo determinar el nombre del catálogo');
            }

            const endpoint = `/catalogs/${catalogName}`;
            await api.delete(`${endpoint}/${id}`);
            Swal.fire('Eliminado', 'Registro eliminado correctamente', 'success');
            await loadItems();
            emit('options-updated');
        } catch (error) {
            Swal.fire('Error', 'Error al eliminar el registro', 'error');
        }
    }
};

const resetForm = () => {
    valueNew.value = '';
    valueColor.value = '#22c55e';
    valueTextColor.value = '#000000';
    editingId.value = null;
    search.value = '';
    errors.value = {};
};

const closeModal = () => {
    emit('update:show', false);
};
</script>
