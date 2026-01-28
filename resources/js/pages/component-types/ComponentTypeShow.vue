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
                            <i class="fas fa-snowplow text-blue-600 mr-3"></i>
                            Detalles del Tipo de Componente
                        </h2>
                        <p class="text-gray-500 text-sm mt-1">Información completa del tipo de componente</p>
                    </div>
                    <div class="flex items-center space-x-2">
                        <router-link
                            v-if="authStore.hasPermission('save equipment')"
                            :to="{ name: 'component-types.edit', params: { id: componentType.id } }"
                            class="text-yellow-600 hover:text-yellow-900 p-2 hover:bg-yellow-100 rounded-lg transition-colors"
                            title="Editar"
                        >
                            <i class="fas fa-edit text-xl"></i>
                        </router-link>
                        <router-link
                            :to="{ name: 'component-types' }"
                            class="text-gray-600 hover:text-gray-900 p-2 hover:bg-gray-100 rounded-lg transition-colors"
                            title="Volver"
                        >
                            <i class="fas fa-arrow-left text-xl"></i>
                        </router-link>
                    </div>
                </div>
            </div>

            <!-- Component Type Details -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="mb-6">
                    <div class="bg-gray-50 rounded-lg p-4">
                        <label class="flex items-center mb-2 text-sm font-medium text-gray-500">
                            <i class="fas fa-tag mr-2 text-blue-600"></i>
                            Nombre del Tipo
                        </label>
                        <p class="text-lg font-semibold text-gray-900">{{ componentType.name }}</p>
                    </div>
                </div>

                <!-- Fields -->
                <div v-if="componentType.fields && componentType.fields.length > 0">
                    <div class="flex items-center mb-4 pb-2 border-b border-gray-200">
                        <i class="fas fa-list-ul text-blue-600 mr-2"></i>
                        <h4 class="text-lg font-semibold text-gray-800">Atributos del Componente</h4>
                        <span class="ml-2 px-2 py-0.5 bg-blue-100 text-blue-700 rounded-full text-xs font-semibold">
                            {{ componentType.fields.length }} {{ componentType.fields.length === 1 ? 'atributo' : 'atributos' }}
                        </span>
                    </div>

                    <div class="space-y-4">
                        <div
                            v-for="(field, index) in componentType.fields"
                            :key="field.id"
                            class="bg-gray-50 rounded-lg p-4 border border-gray-200"
                        >
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex items-center space-x-2">
                                    <div class="bg-blue-100 rounded-full w-8 h-8 flex items-center justify-center">
                                        <span class="text-blue-600 font-semibold text-sm">{{ index + 1 }}</span>
                                    </div>
                                    <h5 class="font-medium text-gray-800">{{ field.name }}</h5>
                                    <span
                                        :class="getTypeBadgeClass(field.type)"
                                        class="ml-2 px-2 py-0.5 rounded-full text-xs font-medium"
                                    >
                                        {{ getTypeLabel(field.type) }}
                                    </span>
                                    <span v-if="field.is_required" class="ml-2 px-2 py-0.5 bg-red-100 text-red-700 rounded-full text-xs font-medium">
                                        Requerido
                                    </span>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                                <div v-if="field.unit">
                                    <span class="text-gray-500">Unidad:</span>
                                    <span class="ml-2 font-medium text-gray-900">{{ field.unit }}</span>
                                </div>
                            </div>

                            <!-- Opciones para select/multiselect -->
                            <div v-if="field.options && field.options.length > 0" class="mt-3 pt-3 border-t border-gray-200">
                                <div class="text-xs font-semibold text-gray-600 mb-2">
                                    Opciones disponibles:
                                </div>
                                <div class="flex flex-wrap gap-2">
                                    <span
                                        v-for="opt in field.options"
                                        :key="opt.id"
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
                <div v-else class="text-center py-8 text-gray-500">
                    <i class="fas fa-info-circle text-2xl mb-2"></i>
                    <p>No hay atributos configurados</p>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import { useAuthStore } from '../../stores/auth';
import api from '../../services/api';
import Swal from 'sweetalert2';

const router = useRouter();
const route = useRoute();
const authStore = useAuthStore();

const componentType = ref({});
const loading = ref(true);

const loadComponentType = async () => {
    loading.value = true;
    try {
        const response = await api.get(`/component-types/${route.params.id}`);
        componentType.value = response.data.data;
    } catch (error) {
        Swal.fire('Error', 'Error al cargar el tipo de componente', 'error');
        router.push({ name: 'component-types' });
    } finally {
        loading.value = false;
    }
};

const getTypeLabel = (type) => {
    const labels = {
        'text': '📝 Texto',
        'number': '🔢 Numérico',
        'textarea': '📄 Texto largo',
        'select': '☑️ Selección',
        'multiselect': '☑️ Selección Múltiple',
        'date': '📅 Fecha',
        'datetime': '📅 Fecha y Hora',
        'boolean': '✓ Sí/No'
    };
    return labels[type] || type;
};

const getTypeBadgeClass = (type) => {
    const classes = {
        'text': 'bg-gray-100 text-gray-800',
        'number': 'bg-blue-100 text-blue-800',
        'numeric': 'bg-blue-100 text-blue-800',
        'textarea': 'bg-purple-100 text-purple-800',
        'select': 'bg-green-100 text-green-800',
        'multiselect': 'bg-green-100 text-green-800',
        'date': 'bg-orange-100 text-orange-800',
        'datetime': 'bg-orange-100 text-orange-800',
        'boolean': 'bg-pink-100 text-pink-800'
    };
    return classes[type] || 'bg-gray-100 text-gray-800';
};

onMounted(() => {
    loadComponentType();
});
</script>
