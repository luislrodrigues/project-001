<template>
    <div class="container mx-auto px-4 sm:px-8">
        <!-- Modal para ampliar imagen -->
        <Teleport to="body">
            <Transition name="fade">
                <div
                    v-if="imageModalUrl"
                    class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/80 p-4"
                    @click.self="closeImageModal"
                >
                    <button
                        class="absolute top-4 right-4 z-10 w-10 h-10 flex items-center justify-center rounded-full bg-white/20 hover:bg-white/30 text-white text-xl"
                        @click="closeImageModal"
                        aria-label="Cerrar"
                    >
                        <i class="fas fa-times"></i>
                    </button>
                    <img
                        :src="imageModalUrl"
                        alt="Imagen ampliada"
                        class="max-w-[95vw] max-h-[90vh] w-auto h-auto object-contain"
                        @click.stop
                    />
                </div>
            </Transition>
        </Teleport>
        <div v-if="loading" class="fixed top-0 left-0 w-screen h-screen flex items-center justify-center z-50 bg-black bg-opacity-30 backdrop-blur-sm">
            <div class="flex flex-col items-center justify-center h-full">
                <svg class="animate-spin h-16 w-16 text-blue-600 mr-3 mb-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="10" stroke-opacity="0.25" stroke-width="2" stroke="currentColor" fill="none"></circle>
                    <path d="M12 2a10 10 0 00-9.5 7.5M12 2c2.5 0 5 1 7 2.5" stroke="currentColor" stroke-width="2" fill="none"></path>
                </svg>
                <p class="text-gray-700 font-medium">Cargando...</p>
            </div>
        </div>

        <div v-else-if="equipment" class="py-6">
            <!-- Header -->
            <div class="mb-6">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h2 class="text-3xl font-bold text-gray-800 flex items-center">
                            <i class="fas fa-cog text-blue-600 mr-3"></i>
                            Detalle del Equipo
                        </h2>
                        <p class="text-gray-500 text-sm mt-1">Información completa del equipo</p>
                    </div>
                    <div class="flex items-center space-x-3">
                        <router-link
                            v-if="authStore.hasPermission('save report')"
                            :to="{ name: 'reports.create', query: { equipment_id: equipment.id } }"
                            class="flex items-center px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-colors"
                        >
                            <i class="fas fa-file-alt mr-2"></i>
                            Crear reporte
                        </router-link>
                        <router-link
                            v-if="authStore.hasPermission('edit equipment')"
                            :to="{ name: 'equipment.edit', params: { id: equipment.id } }"
                            class="flex items-center px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors"
                        >
                            <i class="fas fa-edit mr-2"></i>
                            Editar
                        </router-link>
                        <router-link
                            :to="{ name: 'equipment' }"
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
                <!-- Imagen del equipo -->
                <div v-if="equipment.image_url" class="mb-6">
                    <label class="flex items-center mb-2 text-sm font-medium text-gray-700">
                        <i class="fas fa-image mr-2 text-blue-600"></i>
                        Imagen del equipo
                    </label>
                    <div class="relative group cursor-pointer inline-block" @click="openImageModal(equipment.image_url)">
                        <img
                            :src="equipment.image_url"
                            alt="Equipo"
                            class="max-h-64 rounded-lg object-contain border border-gray-200 bg-gray-50 hover:border-blue-500 transition-all"
                        />
                        <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-30 rounded-lg flex items-center justify-center transition-all pointer-events-none">
                            <i class="fas fa-search-plus text-white opacity-0 group-hover:opacity-100 text-2xl transition-opacity"></i>
                        </div>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Clic para ampliar</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-gray-50 rounded-lg p-4">
                        <label class="flex items-center mb-2 text-sm font-medium text-gray-700">
                            <i class="fas fa-tag mr-2 text-blue-600"></i>
                            Nombre
                        </label>
                        <div class="text-lg font-semibold text-gray-900 bg-white px-4 py-3 rounded-lg border border-gray-200">
                            {{ equipment.name }}
                        </div>
                    </div>

                    <div class="bg-gray-50 rounded-lg p-4">
                        <label class="flex items-center mb-2 text-sm font-medium text-gray-700">
                            <i class="fas fa-cog mr-2 text-blue-600"></i>
                            Tipo Equipo
                        </label>
                        <div class="text-lg font-semibold text-gray-900 bg-white px-4 py-3 rounded-lg border border-gray-200">
                            {{ (equipment.equipment_type && equipment.equipment_type.name) || 'N/A' }}
                        </div>
                    </div>

                    <div class="bg-gray-50 rounded-lg p-4">
                        <label class="flex items-center mb-2 text-sm font-medium text-gray-700">
                            <i class="fas fa-tasks mr-2 text-blue-600"></i>
                            Proceso
                        </label>
                        <div class="text-lg font-semibold text-gray-900 bg-white px-4 py-3 rounded-lg border border-gray-200">
                            {{ (equipment.process && equipment.process.name) || 'N/A' }}
                        </div>
                    </div>

                    <div class="bg-gray-50 rounded-lg p-4">
                        <label class="flex items-center mb-2 text-sm font-medium text-gray-700">
                            <i class="fas fa-map-marker-alt mr-2 text-blue-600"></i>
                            Ubicación
                        </label>
                        <div class="text-lg font-semibold text-gray-900 bg-white px-4 py-3 rounded-lg border border-gray-200">
                            {{ (equipment.ubication && equipment.ubication.name) || 'N/A' }}
                        </div>
                    </div>

                    <div class="bg-gray-50 rounded-lg p-4">
                        <label class="flex items-center mb-2 text-sm font-medium text-gray-700">
                            <i class="fas fa-barcode mr-2 text-blue-600"></i>
                            Código
                        </label>
                        <div class="text-lg font-semibold text-gray-900 bg-white px-4 py-3 rounded-lg border border-gray-200">
                            {{ equipment.code || 'N/A' }}
                        </div>
                    </div>

                    <div class="bg-gray-50 rounded-lg p-4">
                        <label class="flex items-center mb-2 text-sm font-medium text-gray-700">
                            <i class="fas fa-tag mr-2 text-blue-600"></i>
                            Tag
                        </label>
                        <div class="text-lg font-semibold text-gray-900 bg-white px-4 py-3 rounded-lg border border-gray-200">
                            {{ equipment.tag || 'N/A' }}
                        </div>
                    </div>

                    <div class="bg-gray-50 rounded-lg p-4">
                        <label class="flex items-center mb-2 text-sm font-medium text-gray-700">
                            <i class="fas fa-building mr-2 text-blue-600"></i>
                            Empresa
                        </label>
                        <div class="text-lg font-semibold text-gray-900 bg-white px-4 py-3 rounded-lg border border-gray-200">
                            {{ (equipment.company && equipment.company.name) || 'N/A' }}
                        </div>
                    </div>

                    <div class="bg-gray-50 rounded-lg p-4 md:col-span-2">
                        <label class="flex items-center mb-2 text-sm font-medium text-gray-700">
                            <i class="fas fa-align-left mr-2 text-blue-600"></i>
                            Descripción
                        </label>
                        <div class="text-sm text-gray-900 bg-white px-4 py-3 rounded-lg border border-gray-200 min-h-[60px]">
                            {{ equipment.description || 'N/A' }}
                        </div>
                    </div>
                </div>

                <!-- Componentes con atributos -->
                <div v-if="equipment.components && equipment.components.length > 0" class="mt-8 pt-6 border-t border-gray-200">
                    <div class="flex items-center mb-4 pb-2 border-b border-gray-200">
                        <i class="fas fa-puzzle-piece text-blue-600 mr-2"></i>
                        <h4 class="text-lg font-semibold text-gray-800">Componentes</h4>
                    </div>
                    <div class="space-y-4">
                        <div
                            v-for="component in equipment.components"
                            :key="component.id"
                            class="bg-gray-50 rounded-lg border border-gray-200 overflow-hidden"
                        >
                            <div class="px-4 py-3 bg-white border-b border-gray-200 flex flex-wrap items-center gap-x-4 gap-y-1">
                                <span class="font-semibold text-gray-800">
                                    {{ (component.component_type && component.component_type.name) || 'Componente' }}
                                </span>
                                <span v-if="component.brand && component.brand.name" class="text-sm text-gray-600">
                                    <i class="fas fa-tag mr-1 text-gray-400"></i>
                                    {{ component.brand.name }}
                                </span>
                            </div>
                            <div v-if="component.attributes && component.attributes.length > 0" class="p-4">
                                <dl class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                                    <div
                                        v-for="(attr, idx) in component.attributes"
                                        :key="idx"
                                        class="flex flex-col sm:flex-row sm:gap-2"
                                    >
                                        <dt class="text-sm font-medium text-gray-500 shrink-0">{{ attr.name }}:</dt>
                                        <dd class="text-sm text-gray-900">
                                            {{ attr.value || '—' }}{{ attr.unit ? ' ' + attr.unit : '' }}
                                        </dd>
                                    </div>
                                </dl>
                            </div>
                            <div v-else class="px-4 py-3 text-sm text-gray-500">
                                Sin atributos definidos
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

const equipment = ref(null);
const loading = ref(true);
const imageModalUrl = ref(null);

const openImageModal = (url) => {
    imageModalUrl.value = url;
};

const closeImageModal = () => {
    imageModalUrl.value = null;
};

onMounted(async () => {
    if (!authStore.hasPermission('detalle equipment')) {
        router.push({ name: 'equipment' });
        return;
    }
    await loadEquipment();
});

const loadEquipment = async () => {
    loading.value = true;
    try {
        const response = await api.get(`/equipment/${route.params.id}`);
        equipment.value = response.data.data;
    } catch (error) {
        Swal.fire('Error', 'Error al cargar el equipo', 'error');
        router.push({ name: 'equipment' });
    } finally {
        loading.value = false;
    }
};

</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.2s ease;
}
.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}
</style>
