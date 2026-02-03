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

        <div v-else-if="component" class="py-6">
            <!-- Header -->
            <div class="mb-6">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h2 class="text-3xl font-bold text-gray-800 flex items-center">
                            <i class="fas fa-weight text-blue-600 mr-3"></i>
                            Detalle del Componente
                        </h2>
                        <p class="text-gray-500 text-sm mt-1">Información completa del componente</p>
                    </div>
                    <div class="flex items-center space-x-3">
                        <router-link
                            v-if="authStore.hasPermission('edit equipment')"
                            :to="{ name: 'components.edit', params: { id: component.id } }"
                            class="flex items-center px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors"
                        >
                            <i class="fas fa-edit mr-2"></i>
                            Editar
                        </router-link>
                        <router-link
                            :to="{ name: 'components' }"
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
                <!-- Información Básica -->
                <div class="mb-8">
                    <div class="flex items-center mb-4 pb-2 border-b border-gray-200">
                        <i class="fas fa-info-circle text-blue-600 mr-2"></i>
                        <h4 class="text-lg font-semibold text-gray-800">Información Básica</h4>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="bg-gray-50 rounded-lg p-4">
                            <label class="flex items-center mb-2 text-sm font-medium text-gray-700">
                                <i class="fas fa-cog mr-2 text-blue-600"></i>
                                Equipo
                            </label>
                            <div class="text-lg font-semibold text-gray-900 bg-white px-4 py-3 rounded-lg border border-gray-200">
                                {{ component.equipment?.name }}
                            </div>
                        </div>

                        <div class="bg-gray-50 rounded-lg p-4">
                            <label class="flex items-center mb-2 text-sm font-medium text-gray-700">
                                <i class="fas fa-snowplow mr-2 text-blue-600"></i>
                                Tipo Componente
                            </label>
                            <div class="text-lg font-semibold text-gray-900 bg-white px-4 py-3 rounded-lg border border-gray-200">
                                {{ component.component_type?.name }}
                            </div>
                        </div>

                        <div class="bg-gray-50 rounded-lg p-4">
                            <label class="flex items-center mb-2 text-sm font-medium text-gray-700">
                                <i class="fas fa-tag mr-2 text-blue-600"></i>
                                Marca
                            </label>
                            <div class="text-lg font-semibold text-gray-900 bg-white px-4 py-3 rounded-lg border border-gray-200">
                                {{ component.brand?.name }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Campos Adicionales -->
                <div v-if="additionalFieldsWithValues.length > 0">
                    <div class="flex items-center mb-4 pb-2 border-b border-gray-200">
                        <i class="fas fa-list-ul text-blue-600 mr-2"></i>
                        <h4 class="text-lg font-semibold text-gray-800">Atributos Adicionales</h4>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <div
                            v-for="item in additionalFieldsWithValues"
                            :key="item.fieldId"
                            class="bg-gray-50 rounded-lg p-4"
                        >
                            <label class="flex items-center mb-2 text-sm font-medium text-gray-700">
                                <i class="fas fa-info-circle mr-2 text-blue-600"></i>
                                {{ item.name }}
                                <span v-if="item.unit" class="ml-2 text-xs text-gray-500">({{ item.unit }})</span>
                            </label>
                            <div class="text-sm font-semibold text-gray-900 bg-white px-4 py-3 rounded-lg border border-gray-200">
                                {{ item.value || 'N/A' }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import { useAuthStore } from '../../stores/auth';
import api from '../../services/api';
import Swal from 'sweetalert2';

const router = useRouter();
const route = useRoute();
const authStore = useAuthStore();

const component = ref(null);
const loading = ref(true);
const fieldDefinitions = ref([]);

onMounted(async () => {
    await loadComponent();
});

const loadComponent = async () => {
    loading.value = true;
    try {
        const response = await api.get(`/components/${route.params.id}`);
        component.value = response.data.data;
        if (component.value?.component_type_id) {
            const fieldsRes = await api.get(`/components/type/${component.value.component_type_id}/fields`);
            fieldDefinitions.value = fieldsRes.data.data;
        }
    } catch (error) {
        Swal.fire('Error', 'Error al cargar el componente', 'error');
        router.push({ name: 'components' });
    } finally {
        loading.value = false;
    }
};

// Combinar definiciones de campos con valores, mostrando nombre real (Potencia, Velocidad, etc.)
const additionalFieldsWithValues = computed(() => {
    if (!component.value?.field_values) return [];
    const fv = component.value.field_values;
    const fieldsById = Object.fromEntries(
        (fieldDefinitions.value || []).map((f) => [String(f.id), f])
    );
    const items = [];
    if (Array.isArray(fv)) {
        fv.forEach((item) => {
            if (item && 'field_id' in item) {
                const fieldId = String(item.field_id);
                const def = fieldsById[fieldId];
                items.push({
                    fieldId,
                    name: def?.name ?? `Campo ${fieldId}`,
                    unit: def?.unit ?? '',
                    value: item.value ?? 'N/A',
                });
            }
        });
    } else {
        Object.keys(fv).forEach((fieldId) => {
            const item = fv[fieldId];
            const val = item && typeof item === 'object' && 'value' in item ? item.value : item;
            const def = fieldsById[String(fieldId)];
            items.push({
                fieldId,
                name: def?.name ?? `Campo ${fieldId}`,
                unit: def?.unit ?? '',
                value: val ?? 'N/A',
            });
        });
    }
    // Ordenar por el orden de fieldDefinitions si existen
    if (fieldDefinitions.value?.length) {
        const order = Object.fromEntries(
            fieldDefinitions.value.map((f, i) => [String(f.id), i])
        );
        items.sort((a, b) => (order[a.fieldId] ?? 999) - (order[b.fieldId] ?? 999));
    }
    return items;
});
</script>
