<template>
    <div class="container mx-auto px-4 sm:px-8">
        <div v-if="loading" class="fixed top-0 left-0 w-screen h-screen flex items-center justify-center z-50 bg-black bg-opacity-30 backdrop-blur-sm">
            <div class="flex flex-col items-center justify-center h-full">
                <svg class="animate-spin h-16 w-16 text-blue-600 mr-3 mb-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="10" stroke-opacity="0.25" stroke-width="2" stroke="currentColor" fill="none"></circle>
                    <path d="M12 2a10 10 0 00-9.5 7.5M12 2c2.5 0 5 1 7 2.5" stroke="currentColor" stroke-width="2" fill="none"></path>
                </svg>
                <p class="text-gray-700 font-medium">Cargando tipo de componente...</p>
            </div>
        </div>

        <div v-else-if="!componentType" class="py-10 text-center">
            <p class="text-gray-500">No se encontró la información solicitada.</p>
            <router-link :to="{ name: backRouteName }" class="mt-4 inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                <i class="fas fa-arrow-left mr-2"></i>
                Volver al listado
            </router-link>
        </div>

        <div v-else class="py-6">
            <!-- Header -->
            <div class="mb-6">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h2 class="text-3xl font-bold text-gray-800 flex items-center">
                            <i :class="titleIconClass" class="fas mr-3"></i>
                            {{ titleText }}
                        </h2>
                        <p class="text-gray-500 text-sm mt-1">{{ subtitleText }}</p>
                    </div>
                    <div class="flex items-center space-x-2">
                        <router-link
                            v-if="authStore.hasPermission('edit component type')"
                            :to="{ name: 'component-types.edit', params: { id: componentType.id }, query: { report_type: reportTypeResolved } }"
                            class="text-yellow-600 hover:text-yellow-900 p-2 hover:bg-yellow-100 rounded-lg transition-colors"
                            title="Editar"
                        >
                            <i class="fas fa-edit text-xl"></i>
                        </router-link>
                        <router-link
                            :to="{ name: backRouteName }"
                            class="text-gray-600 hover:text-gray-900 p-2 hover:bg-gray-100 rounded-lg transition-colors"
                            title="Volver"
                        >
                            <i class="fas fa-arrow-left text-xl"></i>
                        </router-link>
                    </div>
                </div>
            </div>

            <!-- Content -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-gray-50 rounded-lg p-4">
                        <label class="flex items-center mb-2 text-sm font-medium text-gray-700">
                            <i class="fas fa-tag mr-2 text-blue-600"></i>
                            Nombre
                        </label>
                        <div class="text-lg font-semibold text-gray-900 bg-white px-4 py-3 rounded-lg border border-gray-200">
                            {{ componentType.name }}
                        </div>
                    </div>

                    <div class="bg-gray-50 rounded-lg p-4">
                        <label class="flex items-center mb-2 text-sm font-medium text-gray-700">
                            <i class="fas fa-info-circle mr-2 text-blue-600"></i>
                            Estado
                        </label>
                        <div class="bg-white px-4 py-3 rounded-lg border border-gray-200">
                            <span
                                :class="componentType.status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'"
                                class="px-3 py-1 inline-flex text-sm font-semibold rounded-full"
                            >
                                {{ statusLabel }}
                            </span>
                        </div>
                    </div>

                    <div class="bg-gray-50 rounded-lg p-4">
                        <label class="flex items-center mb-2 text-sm font-medium text-gray-700">
                            <i class="fas fa-layer-group mr-2 text-blue-600"></i>
                            Atributos definidos
                        </label>
                        <div class="text-lg font-semibold text-gray-900 bg-white px-4 py-3 rounded-lg border border-gray-200">
                            {{ attributesCount }}
                        </div>
                    </div>

                    <div class="bg-gray-50 rounded-lg p-4">
                        <label class="flex items-center mb-2 text-sm font-medium text-gray-700">
                            <i class="fas fa-calendar-plus mr-2 text-blue-600"></i>
                            Creado
                        </label>
                        <div class="text-sm text-gray-900 bg-white px-4 py-3 rounded-lg border border-gray-200">
                            {{ formatDate(componentType.created_at) }}
                        </div>
                    </div>

                    <div class="bg-gray-50 rounded-lg p-4">
                        <label class="flex items-center mb-2 text-sm font-medium text-gray-700">
                            <i class="fas fa-calendar-edit mr-2 text-blue-600"></i>
                            Actualizado
                        </label>
                        <div class="text-sm text-gray-900 bg-white px-4 py-3 rounded-lg border border-gray-200">
                            {{ formatDate(componentType.updated_at) }}
                        </div>
                    </div>
                </div>

                <!-- Attributes -->
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <div class="flex items-center mb-4 pb-2 border-b border-gray-200">
                        <i class="fas fa-list-ul text-blue-600 mr-2"></i>
                        <h4 class="text-lg font-semibold text-gray-800">Atributos del Componente</h4>
                        <span class="ml-2 px-2 py-0.5 bg-blue-100 text-blue-700 rounded-full text-xs font-semibold">
                            {{ attributesCount }} {{ attributesCount === 1 ? 'atributo' : 'atributos' }}
                        </span>
                    </div>

                    <div v-if="attributesCount === 0" class="text-center py-8 text-gray-500">
                        <i class="fas fa-info-circle text-2xl mb-2"></i>
                        <p>No hay atributos configurados</p>
                    </div>

                    <div v-else class="space-y-4">
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
                                    <span class="ml-2 px-2 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-700">
                                        {{ typeLabels[field.type] || field.type }}
                                    </span>
                                    <span v-if="field.is_required" class="ml-2 px-2 py-0.5 bg-red-100 text-red-700 rounded-full text-xs font-medium">
                                        Requerido
                                    </span>
                                </div>
                                <span class="text-xs text-gray-500">
                                    Orden {{ field.display_order }}
                                </span>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                                <div v-if="field.unit">
                                    <span class="text-gray-500">Unidad:</span>
                                    <span class="ml-2 font-medium text-gray-900">{{ field.unit }}</span>
                                </div>
                            </div>

                            <!-- Opciones para select/multiselect -->
                            <div v-if="fieldSupportsOptions(field.type) && field.options && field.options.length > 0" class="mt-3 pt-3 border-t border-gray-200">
                                <div class="text-xs font-semibold text-gray-600 mb-2">
                                    Opciones disponibles:
                                </div>
                                <div class="flex flex-wrap gap-2">
                                    <span
                                        v-for="option in field.options"
                                        :key="option.id"
                                        :class="option.is_default ? 'bg-yellow-100 text-yellow-800 border border-yellow-300' : 'bg-blue-100 text-blue-800 border border-blue-200'"
                                        class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium"
                                    >
                                        {{ option.label }}
                                        <i v-if="option.is_default" class="fas fa-star ml-1.5 text-yellow-500"></i>
                                    </span>
                                </div>
                            </div>
                            <div v-else-if="fieldSupportsOptions(field.type)" class="mt-3 pt-3 border-t border-gray-200 text-sm text-gray-500">
                                No hay opciones configuradas
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useAuthStore } from '../../stores/auth';
import api from '../../services/api';
import Swal from 'sweetalert2';

const route = useRoute();
const router = useRouter();
const authStore = useAuthStore();

const loading = ref(true);
const componentType = ref(null);

const normalizeReportType = (v) => (v === 'thermography' ? 'thermography' : 'vibration');
const reportTypeResolved = computed(() => normalizeReportType(componentType.value?.report_type ?? route.query.report_type));
const backRouteName = computed(() => (reportTypeResolved.value === 'thermography' ? 'component-types.thermography' : 'component-types.vibration'));
const titleText = computed(() => (reportTypeResolved.value === 'thermography' ? 'Detalles del Tipo de Componente (Termografía)' : 'Detalles del Tipo de Componente (Vibraciones)'));
const subtitleText = computed(() => (reportTypeResolved.value === 'thermography' ? 'Información completa del tipo de componente de termografía' : 'Información completa del tipo de componente de vibraciones'));
const titleIconClass = computed(() => (reportTypeResolved.value === 'thermography' ? 'fa-thermometer-half text-rose-600' : 'fa-wave-square text-blue-600'));

const typeLabels = {
    text: 'Texto',
    numeric: 'Numérico',
    textarea: 'Texto largo',
    select: 'Selección',
    multiselect: 'Selección múltiple',
    date: 'Fecha',
    datetime: 'Fecha y hora',
    boolean: 'Sí / No',
};

const attributesCount = computed(() => componentType.value?.fields?.length ?? 0);

const statusLabel = computed(() => {
    if (!componentType.value) return '';
    return componentType.value.status === 'active' ? 'Activo' : 'Inactivo';
});

const statusBadgeClass = computed(() => {
    if (!componentType.value) return '';
    return componentType.value.status === 'active'
        ? 'bg-green-100 text-green-700 border border-green-200'
        : 'bg-gray-100 text-gray-600 border border-gray-200';
});

const fieldSupportsOptions = (type) => ['select', 'multiselect'].includes(type);

const formatDate = (value) => {
    if (!value) return '—';
    try {
        const date = new Date(value);
        return date.toLocaleString('es-PE', { dateStyle: 'medium', timeStyle: 'short' });
    } catch (error) {
        return value;
    }
};

const fetchComponentType = async () => {
    loading.value = true;
    try {
        const response = await api.get(`/component-types/${route.params.id}`);
        componentType.value = response.data.data;
    } catch (error) {
        Swal.fire('Error', 'No se pudo cargar el tipo de componente.', 'error');
        router.push({ name: backRouteName.value });
    } finally {
        loading.value = false;
    }
};

onMounted(() => {
    if (!authStore.hasPermission('detalle component type')) {
        router.push({ name: backRouteName.value });
        return;
    }
    fetchComponentType();
});
</script>
