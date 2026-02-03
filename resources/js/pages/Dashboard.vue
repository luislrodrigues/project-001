<template>
    <div class="container mx-auto px-4 sm:px-8">
        <div v-if="loading" class="fixed top-0 left-0 w-screen h-screen flex items-center justify-center z-50 bg-black bg-opacity-30 backdrop-blur-sm">
            <div class="flex flex-col items-center justify-center h-full">
                <svg class="animate-spin h-16 w-16 text-blue-600 mr-3 mb-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="10" stroke-opacity="0.25" stroke-width="2" stroke="currentColor" fill="none"></circle>
                    <path d="M12 2a10 10 0 00-9.5 7.5M12 2c2.5 0 5 1 7 2.5" stroke="currentColor" stroke-width="2" fill="none"></path>
                </svg>
                <p class="text-gray-700 font-medium">Cargando estadísticas...</p>
            </div>
        </div>

        <div v-else class="py-6">
            <!-- Estadísticas -->
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4 mb-8">
                <!-- Card Empresas -->
                <div class="bg-white rounded-lg shadow-md border border-gray-200 overflow-hidden hover:shadow-lg transition-shadow duration-200">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-building text-white text-xl"></i>
                                </div>
                            </div>
                            <div class="ml-4 flex-1">
                                <p class="text-sm font-medium text-gray-500">Empresas</p>
                                <p class="text-2xl font-bold text-gray-900 mt-1">{{ stats.companies || 0 }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card Equipos -->
                <div class="bg-white rounded-lg shadow-md border border-gray-200 overflow-hidden hover:shadow-lg transition-shadow duration-200">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-12 h-12 bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-toolbox text-white text-xl"></i>
                                </div>
                            </div>
                            <div class="ml-4 flex-1">
                                <p class="text-sm font-medium text-gray-500">Equipos</p>
                                <p class="text-2xl font-bold text-gray-900 mt-1">{{ stats.equipment || 0 }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card Reportes -->
                <div class="bg-white rounded-lg shadow-md border border-gray-200 overflow-hidden hover:shadow-lg transition-shadow duration-200">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-green-600 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-file-alt text-white text-xl"></i>
                                </div>
                            </div>
                            <div class="ml-4 flex-1">
                                <p class="text-sm font-medium text-gray-500">Reportes</p>
                                <p class="text-2xl font-bold text-gray-900 mt-1">{{ stats.reports || 0 }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card Este Mes -->
                <div class="bg-white rounded-lg shadow-md border border-gray-200 overflow-hidden hover:shadow-lg transition-shadow duration-200">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-12 h-12 bg-gradient-to-br from-yellow-500 to-orange-500 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-calendar-alt text-white text-xl"></i>
                                </div>
                            </div>
                            <div class="ml-4 flex-1">
                                <p class="text-sm font-medium text-gray-500">Este Mes</p>
                                <p class="text-2xl font-bold text-gray-900 mt-1">{{ stats.reports_this_month || 0 }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Módulos del Sistema - Orden: Informes, Equipos, Componentes, Tipo Componente, Usuario, Empresa -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Informes Card -->
                <router-link
                    v-if="authStore.hasPermission('consult report')"
                    :to="{ name: 'reports' }"
                    class="group"
                >
                    <div class="bg-white rounded-xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 border border-gray-100 overflow-hidden">
                        <div class="bg-gradient-to-r from-blue-500 to-blue-600 h-2"></div>
                        <div class="p-6">
                            <div class="flex items-center justify-between mb-4">
                                <div class="w-14 h-14 bg-blue-100 rounded-xl flex items-center justify-center group-hover:bg-blue-500 transition-colors duration-300">
                                    <i class="fas fa-file-alt text-2xl text-blue-600 group-hover:text-white transition-colors duration-300"></i>
                                </div>
                                <i class="fas fa-arrow-right text-gray-400 group-hover:text-blue-500 transform group-hover:translate-x-2 transition-all duration-300"></i>
                            </div>
                            <h3 class="text-xl font-bold text-gray-800 mb-2">Informes</h3>
                            <p class="text-gray-600 text-sm mb-4">Gestiona y visualiza los informes de diagnósticos del sistema</p>
                            <div class="flex items-center text-sm text-blue-600 font-semibold">
                                <span>Acceder</span>
                            </div>
                        </div>
                    </div>
                </router-link>

                <!-- Equipos Card -->
                <router-link
                    :to="{ name: 'equipment' }"
                    class="group"
                >
                    <div class="bg-white rounded-xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 border border-gray-100 overflow-hidden">
                        <div class="bg-gradient-to-r from-green-500 to-green-600 h-2"></div>
                        <div class="p-6">
                            <div class="flex items-center justify-between mb-4">
                                <div class="w-14 h-14 bg-green-100 rounded-xl flex items-center justify-center group-hover:bg-green-500 transition-colors duration-300">
                                    <i class="fas fa-toolbox text-2xl text-green-600 group-hover:text-white transition-colors duration-300"></i>
                                </div>
                                <i class="fas fa-arrow-right text-gray-400 group-hover:text-green-500 transform group-hover:translate-x-2 transition-all duration-300"></i>
                            </div>
                            <h3 class="text-xl font-bold text-gray-800 mb-2">Equipos</h3>
                            <p class="text-gray-600 text-sm mb-4">Gestiona el inventario completo de equipos y sus características</p>
                            <div class="flex items-center text-sm text-green-600 font-semibold">
                                <span>Acceder</span>
                            </div>
                        </div>
                    </div>
                </router-link>

                <!-- Componentes Card -->
                <router-link
                    v-if="authStore.hasPermission('consult equipment')"
                    :to="{ name: 'components' }"
                    class="group"
                >
                    <div class="bg-white rounded-xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 border border-gray-100 overflow-hidden">
                        <div class="bg-gradient-to-r from-purple-500 to-purple-600 h-2"></div>
                        <div class="p-6">
                            <div class="flex items-center justify-between mb-4">
                                <div class="w-14 h-14 bg-purple-100 rounded-xl flex items-center justify-center group-hover:bg-purple-500 transition-colors duration-300">
                                    <i class="fas fa-weight text-2xl text-purple-600 group-hover:text-white transition-colors duration-300"></i>
                                </div>
                                <i class="fas fa-arrow-right text-gray-400 group-hover:text-purple-500 transform group-hover:translate-x-2 transition-all duration-300"></i>
                            </div>
                            <h3 class="text-xl font-bold text-gray-800 mb-2">Componentes</h3>
                            <p class="text-gray-600 text-sm mb-4">Administra los componentes de los equipos del inventario</p>
                            <div class="flex items-center text-sm text-purple-600 font-semibold">
                                <span>Acceder</span>
                            </div>
                        </div>
                    </div>
                </router-link>

                <!-- Tipo Componente Card -->
                <router-link
                    v-if="authStore.hasPermission('consult equipment')"
                    :to="{ name: 'component-types' }"
                    class="group"
                >
                    <div class="bg-white rounded-xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 border border-gray-100 overflow-hidden">
                        <div class="bg-gradient-to-r from-indigo-500 to-indigo-600 h-2"></div>
                        <div class="p-6">
                            <div class="flex items-center justify-between mb-4">
                                <div class="w-14 h-14 bg-indigo-100 rounded-xl flex items-center justify-center group-hover:bg-indigo-500 transition-colors duration-300">
                                    <i class="fas fa-snowplow text-2xl text-indigo-600 group-hover:text-white transition-colors duration-300"></i>
                                </div>
                                <i class="fas fa-arrow-right text-gray-400 group-hover:text-indigo-500 transform group-hover:translate-x-2 transition-all duration-300"></i>
                            </div>
                            <h3 class="text-xl font-bold text-gray-800 mb-2">Tipo Componente</h3>
                            <p class="text-gray-600 text-sm mb-4">Configura los tipos de componentes disponibles en el sistema</p>
                            <div class="flex items-center text-sm text-indigo-600 font-semibold">
                                <span>Acceder</span>
                            </div>
                        </div>
                    </div>
                </router-link>

                <!-- Usuario Card -->
                <router-link
                    v-if="authStore.hasPermission('consult user')"
                    :to="{ name: 'users' }"
                    class="group"
                >
                    <div class="bg-white rounded-xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 border border-gray-100 overflow-hidden">
                        <div class="bg-gradient-to-r from-red-500 to-red-600 h-2"></div>
                        <div class="p-6">
                            <div class="flex items-center justify-between mb-4">
                                <div class="w-14 h-14 bg-red-100 rounded-xl flex items-center justify-center group-hover:bg-red-500 transition-colors duration-300">
                                    <i class="fas fa-users text-2xl text-red-600 group-hover:text-white transition-colors duration-300"></i>
                                </div>
                                <i class="fas fa-arrow-right text-gray-400 group-hover:text-red-500 transform group-hover:translate-x-2 transition-all duration-300"></i>
                            </div>
                            <h3 class="text-xl font-bold text-gray-800 mb-2">Usuario</h3>
                            <p class="text-gray-600 text-sm mb-4">Gestiona los usuarios del sistema, roles y permisos</p>
                            <div class="flex items-center text-sm text-red-600 font-semibold">
                                <span>Acceder</span>
                            </div>
                        </div>
                    </div>
                </router-link>

                <!-- Empresa Card -->
                <router-link
                    v-if="authStore.hasPermission('consult company')"
                    :to="{ name: 'companies' }"
                    class="group"
                >
                    <div class="bg-white rounded-xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 border border-gray-100 overflow-hidden">
                        <div class="bg-gradient-to-r from-orange-500 to-orange-600 h-2"></div>
                        <div class="p-6">
                            <div class="flex items-center justify-between mb-4">
                                <div class="w-14 h-14 bg-orange-100 rounded-xl flex items-center justify-center group-hover:bg-orange-500 transition-colors duration-300">
                                    <i class="fas fa-building text-2xl text-orange-600 group-hover:text-white transition-colors duration-300"></i>
                                </div>
                                <i class="fas fa-arrow-right text-gray-400 group-hover:text-orange-500 transform group-hover:translate-x-2 transition-all duration-300"></i>
                            </div>
                            <h3 class="text-xl font-bold text-gray-800 mb-2">Empresa</h3>
                            <p class="text-gray-600 text-sm mb-4">Administra las empresas y plantas registradas en el sistema</p>
                            <div class="flex items-center text-sm text-orange-600 font-semibold">
                                <span>Acceder</span>
                            </div>
                        </div>
                    </div>
                </router-link>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useAuthStore } from '../stores/auth';
import api from '../services/api';

const authStore = useAuthStore();
const stats = ref({});
const loading = ref(true);

onMounted(async () => {
    try {
        const response = await api.get('/dashboard');
        stats.value = response.data.data;
    } catch (error) {
        console.error('Error al cargar estadísticas:', error);
    } finally {
        loading.value = false;
    }
});
</script>
