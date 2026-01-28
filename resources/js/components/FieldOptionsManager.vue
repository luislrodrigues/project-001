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
        <div class="relative z-50 p-4 w-full max-w-3xl my-auto" @click.stop>
            <div class="relative bg-white rounded-xl shadow-2xl overflow-hidden max-h-[90vh] flex flex-col">
                <!-- Header -->
                <div class="flex items-center justify-between px-4 py-3 bg-gradient-to-r from-blue-500 to-blue-600 rounded-t-xl">
                    <div class="flex items-center space-x-2">
                        <div class="bg-white bg-opacity-20 rounded-lg p-1.5">
                            <i class="fas fa-list text-white text-lg"></i>
                        </div>
                        <h3 class="text-lg font-bold text-white">
                            Gestionar Opciones
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
                    <!-- Formulario para agregar opción -->
                    <div class="mb-4 bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg p-3 border-2 border-blue-200">
                        <div class="flex items-center mb-3">
                            <div class="bg-blue-500 rounded-full p-1.5 mr-2">
                                <i class="fas fa-plus-circle text-white text-sm"></i>
                            </div>
                            <div>
                                <h4 class="text-base font-bold text-gray-800">
                                    Agregar Nueva Opción
                                </h4>
                                <p class="text-xs text-gray-600">
                                    Ingresa el label y valor de la opción
                                </p>
                            </div>
                        </div>
                        <div class="bg-white rounded-lg p-3 shadow-sm space-y-3">
                            <div>
                                <label class="flex items-center mb-1.5 text-xs font-medium text-gray-700">
                                    <i class="fas fa-tag mr-1.5 text-blue-600 text-xs"></i>
                                    Label (Texto visible)
                                    <span class="text-red-500 ml-1">*</span>
                                </label>
                                <input
                                    v-model="newOption.label"
                                    type="text"
                                    @keydown.enter.prevent="addOption"
                                    class="w-full bg-white border-2 border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 block px-3 py-2 transition-all duration-200"
                                    placeholder="Ej: Alta, Media, Baja"
                                />
                            </div>
                            <div>
                                <label class="flex items-center mb-1.5 text-xs font-medium text-gray-700">
                                    <i class="fas fa-code mr-1.5 text-blue-600 text-xs"></i>
                                    Valor
                                    <span class="text-red-500 ml-1">*</span>
                                </label>
                                <input
                                    v-model="newOption.value"
                                    type="text"
                                    @keydown.enter.prevent="addOption"
                                    class="w-full bg-white border-2 border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 block px-3 py-2 transition-all duration-200"
                                    placeholder="Ej: high, medium, low"
                                />
                            </div>
                            <div class="flex items-center">
                                <input
                                    v-model="newOption.is_default"
                                    type="checkbox"
                                    id="is_default"
                                    class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500"
                                />
                                <label for="is_default" class="ml-2 text-xs text-gray-700">
                                    Marcar como opción por defecto
                                </label>
                            </div>
                            <div class="flex justify-end">
                                <button
                                    type="button"
                                    @click="addOption"
                                    class="flex items-center px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg transition-colors duration-200 text-sm font-medium shadow-md hover:shadow-lg"
                                >
                                    <i class="fas fa-plus mr-1.5 text-xs"></i>
                                    Agregar Opción
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Lista de opciones -->
                    <div>
                        <div class="flex items-center justify-between mb-3 pb-2 border-b border-gray-200">
                            <div class="flex items-center">
                                <i class="fas fa-list-ul text-blue-600 mr-1.5 text-sm"></i>
                                <h4 class="text-base font-semibold text-gray-800">
                                    Opciones Configuradas
                                </h4>
                                <span v-if="options.length > 0" class="ml-2 px-2 py-0.5 bg-green-100 text-green-700 rounded-full text-xs font-semibold">
                                    {{ options.length }} {{ options.length === 1 ? 'opción' : 'opciones' }}
                                </span>
                            </div>
                        </div>

                        <div class="max-h-64 overflow-y-auto">
                            <div v-if="options.length > 0" class="space-y-2">
                                <div
                                    v-for="(option, index) in options"
                                    :key="index"
                                    class="flex items-center justify-between bg-gray-50 rounded-lg p-3 border border-gray-200 hover:bg-gray-100 transition-colors duration-200"
                                >
                                    <div class="flex items-center space-x-3 flex-1">
                                        <div class="flex items-center space-x-2">
                                            <button
                                                type="button"
                                                @click="moveOptionUp(index)"
                                                :disabled="index === 0"
                                                class="p-1 text-gray-400 hover:text-gray-600 disabled:opacity-30"
                                                title="Mover arriba"
                                            >
                                                <i class="fas fa-arrow-up text-xs"></i>
                                            </button>
                                            <button
                                                type="button"
                                                @click="moveOptionDown(index)"
                                                :disabled="index === options.length - 1"
                                                class="p-1 text-gray-400 hover:text-gray-600 disabled:opacity-30"
                                                title="Mover abajo"
                                            >
                                                <i class="fas fa-arrow-down text-xs"></i>
                                            </button>
                                        </div>
                                        <div class="flex-1">
                                            <div class="flex items-center space-x-2">
                                                <span class="text-sm font-medium text-gray-900">{{ option.label }}</span>
                                                <span class="text-xs text-gray-500">({{ option.value }})</span>
                                                <span
                                                    v-if="option.is_default"
                                                    class="px-2 py-0.5 bg-yellow-100 text-yellow-800 rounded-full text-xs font-medium"
                                                >
                                                    <i class="fas fa-star mr-1"></i>
                                                    Por defecto
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex items-center space-x-1.5 flex-shrink-0">
                                        <button
                                            type="button"
                                            @click="editOption(index)"
                                            class="px-2 py-1.5 bg-blue-500 hover:bg-blue-600 text-white rounded-lg transition-colors duration-200"
                                            title="Editar"
                                        >
                                            <i class="fas fa-edit text-xs"></i>
                                        </button>
                                        <button
                                            type="button"
                                            @click="removeOption(index)"
                                            class="px-2 py-1.5 bg-red-500 hover:bg-red-600 text-white rounded-lg transition-colors duration-200"
                                            title="Eliminar"
                                        >
                                            <i class="fas fa-trash text-xs"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div v-else class="text-center py-8 bg-gray-50 rounded-lg border-2 border-dashed border-gray-300">
                                <i class="fas fa-inbox text-3xl text-gray-400 mb-3"></i>
                                <p class="text-sm text-gray-500">No hay opciones configuradas</p>
                                <p class="text-xs text-gray-400 mt-1">
                                    Agrega opciones usando el formulario de arriba
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="flex justify-end px-4 py-3 bg-gray-50 border-t border-gray-200 flex-shrink-0">
                    <button
                        type="button"
                        @click="saveAndClose"
                        class="flex items-center px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg transition-colors duration-200 text-sm font-medium mr-2"
                    >
                        <i class="fas fa-save mr-1.5 text-xs"></i>
                        Guardar y Cerrar
                    </button>
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
import { ref, watch } from 'vue';

const props = defineProps({
    show: {
        type: Boolean,
        default: false
    },
    options: {
        type: Array,
        default: () => []
    }
});

const emit = defineEmits(['update:show', 'update:options']);

const newOption = ref({
    label: '',
    value: '',
    is_default: false
});

const editingIndex = ref(null);

watch(() => props.show, (newVal) => {
    if (!newVal) {
        resetNewOption();
        editingIndex.value = null;
    }
});

const addOption = () => {
    if (!newOption.value.label.trim() || !newOption.value.value.trim()) {
        return;
    }

    // Verificar que el valor no esté duplicado
    const existingValues = props.options.map(opt => opt.value);
    if (existingValues.includes(newOption.value.value)) {
        alert('Este valor ya existe');
        return;
    }

    const options = [...props.options];
    
    if (editingIndex.value !== null) {
        // Editar opción existente
        options[editingIndex.value] = {
            ...newOption.value,
            display_order: options[editingIndex.value].display_order || (editingIndex.value + 1)
        };
        editingIndex.value = null;
    } else {
        // Agregar nueva opción
        options.push({
            ...newOption.value,
            display_order: options.length + 1
        });
    }

    // Si se marca como default, desmarcar otros
    if (newOption.value.is_default) {
        options.forEach((opt, index) => {
            if (index !== (editingIndex.value !== null ? editingIndex.value : options.length - 1)) {
                opt.is_default = false;
            }
        });
    }

    emit('update:options', options);
    resetNewOption();
};

const editOption = (index) => {
    editingIndex.value = index;
    newOption.value = { ...props.options[index] };
};

const removeOption = (index) => {
    const options = [...props.options];
    options.splice(index, 1);
    // Reordenar
    options.forEach((opt, i) => {
        opt.display_order = i + 1;
    });
    emit('update:options', options);
};

const moveOptionUp = (index) => {
    if (index === 0) return;
    const options = [...props.options];
    [options[index - 1], options[index]] = [options[index], options[index - 1]];
    // Actualizar display_order
    options.forEach((opt, i) => {
        opt.display_order = i + 1;
    });
    emit('update:options', options);
};

const moveOptionDown = (index) => {
    if (index === props.options.length - 1) return;
    const options = [...props.options];
    [options[index], options[index + 1]] = [options[index + 1], options[index]];
    // Actualizar display_order
    options.forEach((opt, i) => {
        opt.display_order = i + 1;
    });
    emit('update:options', options);
};

const resetNewOption = () => {
    newOption.value = {
        label: '',
        value: '',
        is_default: false
    };
};

const saveAndClose = () => {
    emit('update:show', false);
};

const closeModal = () => {
    emit('update:show', false);
};
</script>
