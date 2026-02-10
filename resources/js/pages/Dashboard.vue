<template>
    <div class="container mx-auto px-4 sm:px-8">
        <div class="py-4 sm:py-6">
            <!-- Módulos del Sistema - Orden: Informes, Equipos, Tipo Componente, Usuario, Empresa, Indicadores -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Informes Card -->
                <router-link
                    v-if="authStore.hasPermission('consult report')"
                    :to="{ name: 'reports' }"
                    class="group h-full flex flex-col"
                >
                    <div class="bg-white rounded-xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 border border-gray-100 overflow-hidden h-full flex flex-col">
                        <div class="bg-gradient-to-r from-blue-500 to-blue-600 h-2"></div>
                        <div class="p-6 flex-1 flex flex-col">
                            <div class="flex items-center justify-between mb-4">
                                <div class="w-14 h-14 bg-blue-100 rounded-xl flex items-center justify-center group-hover:bg-blue-500 transition-colors duration-300">
                                    <i class="fas fa-file-alt text-2xl text-blue-600 group-hover:text-white transition-colors duration-300"></i>
                                </div>
                                <i class="fas fa-arrow-right text-gray-400 group-hover:text-blue-500 transform group-hover:translate-x-2 transition-all duration-300"></i>
                            </div>
                            <h3 class="text-xl font-bold text-gray-800 mb-2">Informes</h3>
                            <p class="text-gray-600 text-sm mb-4 flex-1">Gestiona y visualiza los informes de diagnósticos del sistema</p>
                            <div class="flex items-center text-sm text-blue-600 font-semibold">
                                <span>Acceder</span>
                            </div>
                        </div>
                    </div>
                </router-link>

                <!-- Equipos Card -->
                <router-link
                    v-if="authStore.hasPermission('consult equipment')"
                    :to="{ name: 'equipment' }"
                    class="group h-full flex flex-col"
                >
                    <div class="bg-white rounded-xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 border border-gray-100 overflow-hidden h-full flex flex-col">
                        <div class="bg-gradient-to-r from-green-500 to-green-600 h-2"></div>
                        <div class="p-6 flex-1 flex flex-col">
                            <div class="flex items-center justify-between mb-4">
                                <div class="w-14 h-14 bg-green-100 rounded-xl flex items-center justify-center group-hover:bg-green-500 transition-colors duration-300">
                                    <i class="fas fa-toolbox text-2xl text-green-600 group-hover:text-white transition-colors duration-300"></i>
                                </div>
                                <i class="fas fa-arrow-right text-gray-400 group-hover:text-green-500 transform group-hover:translate-x-2 transition-all duration-300"></i>
                            </div>
                            <h3 class="text-xl font-bold text-gray-800 mb-2">Equipos</h3>
                            <p class="text-gray-600 text-sm mb-4 flex-1">Gestiona el inventario completo de equipos y sus características</p>
                            <div class="flex items-center text-sm text-green-600 font-semibold">
                                <span>Acceder</span>
                            </div>
                        </div>
                    </div>
                </router-link>

                <!-- Tipo Componente Card -->
                <router-link
                    v-if="!authStore.hasRole('Cliente') && authStore.hasPermission('consult component type')"
                    :to="{ name: 'component-types' }"
                    class="group h-full flex flex-col"
                >
                    <div class="bg-white rounded-xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 border border-gray-100 overflow-hidden h-full flex flex-col">
                        <div class="bg-gradient-to-r from-indigo-500 to-indigo-600 h-2"></div>
                        <div class="p-6 flex-1 flex flex-col">
                            <div class="flex items-center justify-between mb-4">
                                <div class="w-14 h-14 bg-indigo-100 rounded-xl flex items-center justify-center group-hover:bg-indigo-500 transition-colors duration-300">
                                    <i class="fas fa-snowplow text-2xl text-indigo-600 group-hover:text-white transition-colors duration-300"></i>
                                </div>
                                <i class="fas fa-arrow-right text-gray-400 group-hover:text-indigo-500 transform group-hover:translate-x-2 transition-all duration-300"></i>
                            </div>
                            <h3 class="text-xl font-bold text-gray-800 mb-2">Tipo Componente</h3>
                            <p class="text-gray-600 text-sm mb-4 flex-1">Configura los tipos de componentes disponibles en el sistema</p>
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
                    class="group h-full flex flex-col"
                >
                    <div class="bg-white rounded-xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 border border-gray-100 overflow-hidden h-full flex flex-col">
                        <div class="bg-gradient-to-r from-red-500 to-red-600 h-2"></div>
                        <div class="p-6 flex-1 flex flex-col">
                            <div class="flex items-center justify-between mb-4">
                                <div class="w-14 h-14 bg-red-100 rounded-xl flex items-center justify-center group-hover:bg-red-500 transition-colors duration-300">
                                    <i class="fas fa-users text-2xl text-red-600 group-hover:text-white transition-colors duration-300"></i>
                                </div>
                                <i class="fas fa-arrow-right text-gray-400 group-hover:text-red-500 transform group-hover:translate-x-2 transition-all duration-300"></i>
                            </div>
                            <h3 class="text-xl font-bold text-gray-800 mb-2">Usuario</h3>
                            <p class="text-gray-600 text-sm mb-4 flex-1">Gestiona los usuarios del sistema, roles y permisos</p>
                            <div class="flex items-center text-sm text-red-600 font-semibold">
                                <span>Acceder</span>
                            </div>
                        </div>
                    </div>
                </router-link>

                <!-- Empresa Card -->
                <router-link
                    v-if="!authStore.hasRole('Cliente') && authStore.hasPermission('consult company')"
                    :to="{ name: 'companies' }"
                    class="group h-full flex flex-col"
                >
                    <div class="bg-white rounded-xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 border border-gray-100 overflow-hidden h-full flex flex-col">
                        <div class="bg-gradient-to-r from-orange-500 to-orange-600 h-2"></div>
                        <div class="p-6 flex-1 flex flex-col">
                            <div class="flex items-center justify-between mb-4">
                                <div class="w-14 h-14 bg-orange-100 rounded-xl flex items-center justify-center group-hover:bg-orange-500 transition-colors duration-300">
                                    <i class="fas fa-building text-2xl text-orange-600 group-hover:text-white transition-colors duration-300"></i>
                                </div>
                                <i class="fas fa-arrow-right text-gray-400 group-hover:text-orange-500 transform group-hover:translate-x-2 transition-all duration-300"></i>
                            </div>
                            <h3 class="text-xl font-bold text-gray-800 mb-2">Empresa</h3>
                            <p class="text-gray-600 text-sm mb-4 flex-1">Administra las empresas y plantas registradas en el sistema</p>
                            <div class="flex items-center text-sm text-orange-600 font-semibold">
                                <span>Acceder</span>
                            </div>
                        </div>
                    </div>
                </router-link>

                <!-- Indicadores Card -->
                <router-link
                    v-if="authStore.hasPermission('consult indicators')"
                    :to="{ name: 'indicators' }"
                    class="group h-full flex flex-col"
                >
                    <div class="bg-white rounded-xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 border border-gray-100 overflow-hidden h-full flex flex-col">
                        <div class="bg-gradient-to-r from-teal-500 to-teal-600 h-2"></div>
                        <div class="p-6 flex-1 flex flex-col">
                            <div class="flex items-center justify-between mb-4">
                                <div class="w-14 h-14 bg-teal-100 rounded-xl flex items-center justify-center group-hover:bg-teal-500 transition-colors duration-300">
                                    <i class="fas fa-chart-bar text-2xl text-teal-600 group-hover:text-white transition-colors duration-300"></i>
                                </div>
                                <i class="fas fa-arrow-right text-gray-400 group-hover:text-teal-500 transform group-hover:translate-x-2 transition-all duration-300"></i>
                            </div>
                            <h3 class="text-xl font-bold text-gray-800 mb-2">Indicadores</h3>
                            <p class="text-gray-600 text-sm mb-4 flex-1">Estadísticas y gráficos por empresa, condición, proceso y más</p>
                            <div class="flex items-center text-sm text-teal-600 font-semibold">
                                <span>Acceder</span>
                            </div>
                        </div>
                    </div>
                </router-link>

                <!-- Roles y permisos Card -->
                <router-link
                    v-if="authStore.hasPermission('consult role')"
                    :to="{ name: 'roles' }"
                    class="group h-full flex flex-col"
                >
                    <div class="bg-white rounded-xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 border border-gray-100 overflow-hidden h-full flex flex-col">
                        <div class="bg-gradient-to-r from-blue-500 to-blue-600 h-2"></div>
                        <div class="p-6 flex-1 flex flex-col">
                            <div class="flex items-center justify-between mb-4">
                                <div class="w-14 h-14 bg-blue-100 rounded-xl flex items-center justify-center group-hover:bg-blue-500 transition-colors duration-300">
                                    <i class="fas fa-user-shield text-2xl text-blue-600 group-hover:text-white transition-colors duration-300"></i>
                                </div>
                                <i class="fas fa-arrow-right text-gray-400 group-hover:text-blue-500 transform group-hover:translate-x-2 transition-all duration-300"></i>
                            </div>
                            <h3 class="text-xl font-bold text-gray-800 mb-2">Roles y permisos</h3>
                            <p class="text-gray-600 text-sm mb-4 flex-1">Gestiona los permisos asignados a cada rol del sistema</p>
                            <div class="flex items-center text-sm text-blue-600 font-semibold">
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
import { useAuthStore } from '../stores/auth';

const authStore = useAuthStore();
</script>
