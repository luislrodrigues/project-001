<template>
    <div class="container mx-auto px-4 sm:px-8">
        <!-- Loading overlay para carga inicial -->
        <div v-if="loading" class="fixed top-0 left-0 w-screen h-screen flex items-center justify-center z-50 bg-black bg-opacity-30 backdrop-blur-sm">
            <div class="flex flex-col items-center justify-center h-full">
                <svg class="animate-spin h-16 w-16 text-blue-600 mr-3 mb-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="10" stroke-opacity="0.25" stroke-width="2" stroke="currentColor" fill="none"></circle>
                    <path d="M12 2a10 10 0 00-9.5 7.5M12 2c2.5 0 5 1 7 2.5" stroke="currentColor" stroke-width="2" fill="none"></path>
                </svg>
                <p class="text-gray-700 font-medium">Cargando...</p>
            </div>
        </div>

        <!-- Loading overlay para generación de PDF -->
        <div v-if="downloadingPdf" class="fixed top-0 left-0 w-screen h-screen flex items-center justify-center z-50 bg-black bg-opacity-30 backdrop-blur-sm">
            <div class="flex flex-col items-center justify-center h-full">
                <svg class="animate-spin h-16 w-16 text-green-600 mr-3 mb-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="10" stroke-opacity="0.25" stroke-width="2" stroke="currentColor" fill="none"></circle>
                    <path d="M12 2a10 10 0 00-9.5 7.5M12 2c2.5 0 5 1 7 2.5" stroke="currentColor" stroke-width="2" fill="none"></path>
                </svg>
                <p class="text-gray-700 font-medium">Generando PDF...</p>
            </div>
        </div>

        <div v-else-if="report" class="py-6">
            <!-- Header -->
            <div class="mb-6">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h2 class="text-3xl font-bold text-gray-800 flex items-center">
                            <i class="fas fa-file-alt text-blue-600 mr-3"></i>
                            Detalle del Reporte
                        </h2>
                        <p class="text-gray-500 text-sm mt-1">Información completa del reporte</p>
                    </div>
                    <div class="flex items-center space-x-3">
                        <router-link
                            v-if="authStore.hasPermission('edit report')"
                            :to="{ name: 'reports.edit', params: { id: report.id } }"
                            class="flex items-center px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors"
                        >
                            <i class="fas fa-edit mr-2"></i>
                            Editar
                        </router-link>
                        <button
                            type="button"
                            @click.prevent="downloadPdf"
                            :disabled="downloadingPdf"
                            class="flex items-center px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            <i v-if="!downloadingPdf" class="fas fa-download mr-2"></i>
                            <svg v-else class="animate-spin h-4 w-4 mr-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                                <circle cx="12" cy="12" r="10" stroke-opacity="0.25" stroke-width="2" stroke="currentColor" fill="none"></circle>
                                <path d="M12 2a10 10 0 00-9.5 7.5M12 2c2.5 0 5 1 7 2.5" stroke="currentColor" stroke-width="2" fill="none"></path>
                            </svg>
                            {{ downloadingPdf ? 'Generando...' : 'PDF' }}
                        </button>
                        <router-link
                            :to="{ name: 'reports' }"
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
                <!-- Información General -->
                <div class="mb-8">
                    <div class="flex items-center mb-4 pb-2 border-b border-gray-200">
                        <i class="fas fa-info-circle text-blue-600 mr-2"></i>
                        <h4 class="text-lg font-semibold text-gray-800">Información General</h4>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <div class="bg-gray-50 rounded-lg p-4">
                            <label class="flex items-center mb-2 text-sm font-medium text-gray-700">
                                <i class="fas fa-cog mr-2 text-blue-600"></i>
                                Equipo
                            </label>
                            <div class="text-sm font-semibold text-gray-900 bg-white px-4 py-3 rounded-lg border border-gray-200">
                                {{ report.equipment?.name }}
                            </div>
                        </div>

                        <div class="bg-gray-50 rounded-lg p-4">
                            <label class="flex items-center mb-2 text-sm font-medium text-gray-700">
                                <i class="fas fa-calendar mr-2 text-blue-600"></i>
                                Fecha Medición
                            </label>
                            <div class="text-sm font-semibold text-gray-900 bg-white px-4 py-3 rounded-lg border border-gray-200">
                                {{ formatDate(report.measurement_date) }}
                            </div>
                        </div>

                        <div class="bg-gray-50 rounded-lg p-4">
                            <label class="flex items-center mb-2 text-sm font-medium text-gray-700">
                                <i class="fas fa-map-marker-alt mr-2 text-blue-600"></i>
                                Ubicación
                            </label>
                            <div class="text-sm font-semibold text-gray-900 bg-white px-4 py-3 rounded-lg border border-gray-200">
                                {{ report.equipment?.location }}
                            </div>
                        </div>

                        <div class="bg-gray-50 rounded-lg p-4">
                            <label class="flex items-center mb-2 text-sm font-medium text-gray-700">
                                <i class="fas fa-tasks mr-2 text-blue-600"></i>
                                Proceso
                            </label>
                            <div class="text-sm font-semibold text-gray-900 bg-white px-4 py-3 rounded-lg border border-gray-200">
                                {{ report.equipment?.process }}
                            </div>
                        </div>

                        <div class="bg-gray-50 rounded-lg p-4">
                            <label class="flex items-center mb-2 text-sm font-medium text-gray-700">
                                <i class="fas fa-exclamation-triangle mr-2 text-blue-600"></i>
                                Condición
                            </label>
                            <div class="text-sm font-semibold text-gray-900 bg-white px-4 py-3 rounded-lg border border-gray-200">
                                <span
                                    :class="getConditionClass(report.condition?.name)"
                                    class="px-3 py-1 inline-flex text-xs font-semibold rounded-full"
                                >
                                    {{ report.condition?.name }}
                                </span>
                            </div>
                        </div>

                        <div class="bg-gray-50 rounded-lg p-4">
                            <label class="flex items-center mb-2 text-sm font-medium text-gray-700">
                                <i class="fas fa-flag mr-2 text-blue-600"></i>
                                Prioridad
                            </label>
                            <div class="text-sm font-semibold text-gray-900 bg-white px-4 py-3 rounded-lg border border-gray-200">
                                <span
                                    :class="report.priority === 'Alta' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800'"
                                    class="px-3 py-1 inline-flex text-xs font-semibold rounded-full"
                                >
                                    {{ report.priority }}
                                </span>
                            </div>
                        </div>

                        <div class="bg-gray-50 rounded-lg p-4">
                            <label class="flex items-center mb-2 text-sm font-medium text-gray-700">
                                <i class="fas fa-wrench mr-2 text-blue-600"></i>
                                Mecanismo Falla
                            </label>
                            <div class="text-sm font-semibold text-gray-900 bg-white px-4 py-3 rounded-lg border border-gray-200">
                                {{ report.mechanism_fail?.name || 'N/A' }}
                            </div>
                        </div>

                        <div class="bg-gray-50 rounded-lg p-4">
                            <label class="flex items-center mb-2 text-sm font-medium text-gray-700">
                                <i class="fas fa-cog mr-2 text-blue-600"></i>
                                Componente Falla
                            </label>
                            <div class="text-sm font-semibold text-gray-900 bg-white px-4 py-3 rounded-lg border border-gray-200">
                                {{ report.component_fail?.name || 'N/A' }}
                            </div>
                        </div>

                        <div class="bg-gray-50 rounded-lg p-4">
                            <label class="flex items-center mb-2 text-sm font-medium text-gray-700">
                                <i class="fas fa-user mr-2 text-blue-600"></i>
                                Analista
                            </label>
                            <div class="text-sm font-semibold text-gray-900 bg-white px-4 py-3 rounded-lg border border-gray-200">
                                {{ report.analist?.name }}
                            </div>
                        </div>

                        <div class="bg-gray-50 rounded-lg p-4">
                            <label class="flex items-center mb-2 text-sm font-medium text-gray-700">
                                <i class="fas fa-check-circle mr-2 text-blue-600"></i>
                                OT Creada
                            </label>
                            <div class="text-sm font-semibold text-gray-900 bg-white px-4 py-3 rounded-lg border border-gray-200">
                                {{ report.ot_created || 'No' }}
                            </div>
                        </div>

                        <div class="bg-gray-50 rounded-lg p-4">
                            <label class="flex items-center mb-2 text-sm font-medium text-gray-700">
                                <i class="fas fa-info-circle mr-2 text-blue-600"></i>
                                Estado de OT
                            </label>
                            <div class="text-sm font-semibold text-gray-900 bg-white px-4 py-3 rounded-lg border border-gray-200">
                                {{ report.status_ot || 'N/A' }}
                            </div>
                        </div>

                        <div class="bg-gray-50 rounded-lg p-4">
                            <label class="flex items-center mb-2 text-sm font-medium text-gray-700">
                                <i class="fas fa-calendar-check mr-2 text-blue-600"></i>
                                Fecha Ejecución
                            </label>
                            <div class="text-sm font-semibold text-gray-900 bg-white px-4 py-3 rounded-lg border border-gray-200">
                                {{ report.execution_date ? formatDate(report.execution_date) : 'N/A' }}
                            </div>
                        </div>

                        <div class="bg-gray-50 rounded-lg p-4 md:col-span-3">
                            <label class="flex items-center mb-2 text-sm font-medium text-gray-700">
                                <i class="fas fa-check-double mr-2 text-blue-600"></i>
                                Verificación Recomendación
                            </label>
                            <div class="text-sm text-gray-900 bg-white px-4 py-3 rounded-lg border border-gray-200 min-h-[60px]">
                                {{ report.verification_recommendation || 'N/A' }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Imágenes -->
                <div v-if="report.image_1 || report.image_2" class="mb-8">
                    <div class="flex items-center mb-4 pb-2 border-b border-gray-200">
                        <i class="fas fa-images text-blue-600 mr-2"></i>
                        <h4 class="text-lg font-semibold text-gray-800">Imágenes Adjuntas</h4>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div v-if="report.image_1" class="bg-gray-50 rounded-lg p-4">
                            <label class="block mb-2 text-sm font-medium text-gray-700">
                                <i class="fas fa-image mr-2 text-blue-600"></i>
                                Imagen 1
                            </label>
                            <a
                                :href="report.image_1"
                                download
                                target="_blank"
                                class="inline-flex items-center px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg transition-colors"
                            >
                                <i class="fas fa-download mr-2"></i>
                                Descargar
                            </a>
                        </div>
                        <div v-if="report.image_2" class="bg-gray-50 rounded-lg p-4">
                            <label class="block mb-2 text-sm font-medium text-gray-700">
                                <i class="fas fa-image mr-2 text-blue-600"></i>
                                Imagen 2
                            </label>
                            <a
                                :href="report.image_2"
                                download
                                target="_blank"
                                class="inline-flex items-center px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg transition-colors"
                            >
                                <i class="fas fa-download mr-2"></i>
                                Descargar
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Diagnósticos y Recomendaciones -->
                <div v-if="report.components && report.components.length > 0" class="mb-8">
                    <div class="flex items-center mb-4 pb-2 border-b border-gray-200">
                        <i class="fas fa-stethoscope text-blue-600 mr-2"></i>
                        <h4 class="text-lg font-semibold text-gray-800">Diagnósticos y Recomendaciones</h4>
                    </div>
                    <div class="space-y-6">
                        <div
                            v-for="(component, index) in report.components"
                            :key="component.id"
                            class="bg-gray-50 rounded-lg p-5 border border-gray-200"
                        >
                            <div class="flex items-center mb-4">
                                <div class="bg-blue-100 rounded-full w-8 h-8 flex items-center justify-center mr-3">
                                    <span class="text-blue-600 font-semibold text-sm">{{ index + 1 }}</span>
                                </div>
                                <h5 class="text-md font-semibold text-gray-800">
                                    {{ component.component_type?.name || 'Componente' }}
                                </h5>
                            </div>

                            <div class="mb-4">
                                <label class="flex items-center mb-2 text-sm font-medium text-gray-700">
                                    <i class="fas fa-clipboard-list mr-2 text-blue-600"></i>
                                    Diagnóstico
                                </label>
                                <div class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg block w-full px-4 py-3 min-h-[100px]">
                                    {{ getDiagnosisText(component.id) || 'Sin diagnóstico' }}
                                </div>
                            </div>

                            <!-- Imágenes del Componente -->
                            <div v-if="getComponentImages(component.id)?.length" class="mb-4">
                                <label class="flex items-center mb-3 text-sm font-medium text-gray-700">
                                    <i class="fas fa-image mr-2 text-purple-600"></i>
                                    Imágenes ({{ getComponentImages(component.id).length }})
                                </label>
                                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
                                    <div
                                        v-for="(imageUrl, imgIndex) in getComponentImages(component.id)"
                                        :key="imgIndex"
                                        class="relative group"
                                    >
                                        <img
                                            :src="imageUrl"
                                            :alt="`Imagen ${imgIndex + 1}`"
                                            class="w-full h-32 object-cover rounded-lg border-2 border-gray-200 hover:border-purple-500 transition-all cursor-pointer"
                                            @click="openImageModal(imageUrl)"
                                        />
                                        <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-30 transition-all rounded-lg flex items-center justify-center">
                                            <i class="fas fa-search-plus text-white opacity-0 group-hover:opacity-100 text-xl"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div v-if="report.recommendations?.[component.id]?.length">
                                <label class="flex items-center mb-3 text-sm font-medium text-gray-700">
                                    <i class="fas fa-lightbulb mr-2 text-yellow-600"></i>
                                    Recomendaciones ({{ report.recommendations[component.id].length }})
                                </label>
                                <div class="space-y-2">
                                    <div
                                        v-for="(rec, recIndex) in report.recommendations[component.id]"
                                        :key="recIndex"
                                        class="bg-white rounded-lg p-3 border border-gray-200"
                                    >
                                        <div class="flex items-start">
                                            <span class="flex-shrink-0 bg-yellow-100 text-yellow-800 rounded-full w-6 h-6 flex items-center justify-center text-xs font-semibold mr-3 mt-0.5">
                                                {{ recIndex + 1 }}
                                            </span>
                                            <input
                                                :value="rec"
                                                type="text"
                                                disabled
                                                class="bg-transparent border-none text-gray-900 text-sm w-full focus:outline-none"
                                            />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
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

const report = ref(null);
const loading = ref(true);
const downloadingPdf = ref(false);

onMounted(async () => {
    await loadReport();
});

const loadReport = async () => {
    loading.value = true;
    try {
        const response = await api.get(`/reports/${route.params.id}`);
        report.value = response.data.data;
    } catch (error) {
        Swal.fire('Error', 'Error al cargar el reporte', 'error');
        router.push({ name: 'reports' });
    } finally {
        loading.value = false;
    }
};

const formatDate = (date) => {
    if (!date) return 'N/A';
    return new Date(date).toLocaleDateString('es-ES');
};

const getConditionClass = (conditionName) => {
    const classes = {
        'Alarma': 'bg-yellow-100 text-yellow-800 border border-yellow-300',
        'Critico': 'bg-red-100 text-red-800 border border-red-300',
        'Peligro': 'bg-orange-100 text-orange-800 border border-orange-300',
    };
    return classes[conditionName] || 'bg-blue-100 text-blue-800 border border-blue-300';
};

const downloadPdf = async (event) => {
    if (event) {
        event.preventDefault();
        event.stopPropagation();
    }
    
    downloadingPdf.value = true;
    
    try {
        const response = await api.get(`/reports/${route.params.id}/pdf`, {
            responseType: 'blob'
        });
        const url = window.URL.createObjectURL(new Blob([response.data]));
        const link = document.createElement('a');
        link.href = url;
        link.setAttribute('download', `reporte-${route.params.id}.pdf`);
        link.style.display = 'none';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        window.URL.revokeObjectURL(url);
    } catch (error) {
        console.error('Error al descargar PDF:', error);
        Swal.fire('Error', 'Error al descargar el PDF', 'error');
    } finally {
        downloadingPdf.value = false;
    }
};

const getDiagnosisText = (componentId) => {
    // Normalizar componentId a string para consistencia
    const normalizedId = String(componentId);
    const diagnosis = report.value?.diagnosis?.[normalizedId];
    if (!diagnosis) return '';
    if (typeof diagnosis === 'string') return diagnosis;
    if (typeof diagnosis === 'object' && diagnosis.text) return diagnosis.text;
    return '';
};

const getComponentImages = (componentId) => {
    // Normalizar componentId a string para consistencia
    const normalizedId = String(componentId);
    let diagnosis = report.value?.diagnosis?.[normalizedId];
    
    // Si no se encuentra por ID, intentar buscar por índice si diagnosis es un array
    if (!diagnosis && Array.isArray(report.value?.diagnosis) && report.value?.components) {
        const componentIndex = report.value.components.findIndex(c => String(c.id) === normalizedId);
        if (componentIndex >= 0 && report.value.diagnosis[componentIndex]) {
            diagnosis = report.value.diagnosis[componentIndex];
        }
    }
    
    if (!diagnosis || typeof diagnosis !== 'object') return [];
    return diagnosis.images || [];
};

const openImageModal = (imageUrl) => {
    Swal.fire({
        imageUrl: imageUrl,
        imageAlt: 'Imagen del componente',
        showCloseButton: true,
        showConfirmButton: false,
        width: '90%',
        padding: '0',
        background: 'transparent',
        backdrop: 'rgba(0,0,0,0.8)'
    });
};
</script>
