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

        <div v-else-if="company" class="py-6">
            <!-- Header -->
            <div class="mb-6">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h2 class="text-3xl font-bold text-gray-800 flex items-center">
                            <i class="fas fa-building text-blue-600 mr-3"></i>
                            Detalle de la Empresa
                        </h2>
                        <p class="text-gray-500 text-sm mt-1">Información completa de la empresa</p>
                    </div>
                    <div class="flex items-center space-x-3">
                        <router-link
                            v-if="authStore.hasPermission('edit company')"
                            :to="{ name: 'companies.edit', params: { id: company.id } }"
                            class="flex items-center px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors"
                        >
                            <i class="fas fa-edit mr-2"></i>
                            Editar
                        </router-link>
                        <router-link
                            :to="{ name: 'companies' }"
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
                            <i class="fas fa-building mr-2 text-blue-600"></i>
                            Nombre
                        </label>
                        <div class="text-lg font-semibold text-gray-900 bg-white px-4 py-3 rounded-lg border border-gray-200">
                            {{ company.name }}
                        </div>
                    </div>

                    <div class="bg-gray-50 rounded-lg p-4">
                        <label class="flex items-center mb-2 text-sm font-medium text-gray-700">
                            <i class="fas fa-chart-pie mr-2 text-blue-600"></i>
                            Segmento de la empresa
                        </label>
                        <div class="text-lg font-semibold text-gray-900 bg-white px-4 py-3 rounded-lg border border-gray-200">
                            {{ company.company_segment?.name ?? 'Sin segmento' }}
                        </div>
                    </div>

                    <div class="bg-gray-50 rounded-lg p-4">
                        <label class="flex items-center mb-2 text-sm font-medium text-gray-700">
                            <i class="fas fa-info-circle mr-2 text-blue-600"></i>
                            Estado
                        </label>
                        <div class="bg-white px-4 py-3 rounded-lg border border-gray-200">
                            <span
                                :class="company.status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
                                class="px-3 py-1 inline-flex text-sm font-semibold rounded-full"
                            >
                                {{ company.status === 'active' ? 'Activo' : 'Inactivo' }}
                            </span>
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

const company = ref(null);
const loading = ref(true);

onMounted(async () => {
    await loadCompany();
});

const loadCompany = async () => {
    loading.value = true;
    try {
        const response = await api.get(`/companies/${route.params.id}`);
        company.value = response.data.data;
    } catch (error) {
        Swal.fire('Error', 'Error al cargar la empresa', 'error');
        router.push({ name: 'companies' });
    } finally {
        loading.value = false;
    }
};
</script>
