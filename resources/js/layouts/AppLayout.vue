<template>
    <div class="font-sans antialiased bg-gradient-to-br from-gray-50 via-blue-50 to-indigo-50 min-h-screen">
        <!-- Sidebar -->
        <aside 
            id="sidebar" 
            class="fixed left-0 top-0 h-screen sidebar-gradient shadow-2xl z-40 transition-all duration-300 ease-in-out"
            :style="{ width: sidebarCollapsed ? 'var(--sidebar-collapsed-width)' : 'var(--sidebar-width)' }"
        >
            <!-- Logo and Toggle -->
            <div class="flex items-center justify-between px-6 py-5 border-b border-blue-400/30">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm">
                        <i class="fas fa-cog text-white text-xl"></i>
                    </div>
                    <span 
                        v-show="!sidebarCollapsed"
                        class="text-white font-bold text-xl transition-opacity duration-300"
                    >
                        3Sanchez
                    </span>
                </div>
                <button 
                    @click="toggleSidebar"
                    class="text-white hover:bg-white/20 rounded-lg p-2 transition-colors"
                >
                    <i class="fas fa-bars text-lg"></i>
                </button>
            </div>

            <!-- User Profile Section -->
            <div 
                class="px-4 py-4 border-b border-blue-400/30"
                :class="{ 'py-2': sidebarCollapsed }"
            >
                <router-link
                    :to="{ name: 'profile' }"
                    class="flex items-center space-x-3 bg-white/10 backdrop-blur-sm rounded-xl p-3 hover:bg-white/15 transition-colors cursor-pointer"
                >
                    <div class="w-10 h-10 bg-gradient-to-br from-yellow-400 to-orange-500 rounded-full flex items-center justify-center text-white font-semibold text-sm shadow-lg flex-shrink-0">
                        {{ userInitials }}
                    </div>
                    <div 
                        v-show="!sidebarCollapsed"
                        class="flex-1 min-w-0 transition-opacity duration-300"
                    >
                        <p class="text-white font-semibold text-sm truncate">{{ authStore.user?.name }}</p>
                        <p class="text-blue-100 text-xs truncate">{{ authStore.user?.email }}</p>
                    </div>
                    <i 
                        v-show="!sidebarCollapsed"
                        class="fas fa-chevron-right text-blue-200 text-xs transition-opacity duration-300"
                    ></i>
                </router-link>
            </div>

            <!-- Navigation -->
            <nav class="px-4 py-4 space-y-2 overflow-y-auto" style="max-height: calc(100vh - 200px);">
                <router-link
                    :to="{ name: 'dashboard' }"
                    class="nav-item flex items-center space-x-3 px-4 py-3 text-white rounded-xl transition-all duration-200"
                    :class="isActive('dashboard') ? 'nav-item-active' : 'nav-item-hover'"
                    title="Inicio"
                >
                    <i class="fas fa-home text-lg w-5"></i>
                    <span 
                        v-show="!sidebarCollapsed"
                        class="font-medium transition-opacity duration-300"
                    >
                        Inicio
                    </span>
                </router-link>

                <router-link
                    :to="{ name: 'reports' }"
                    class="nav-item flex items-center space-x-3 px-4 py-3 text-white rounded-xl transition-all duration-200"
                    :class="isActive('reports') ? 'nav-item-active' : 'nav-item-hover'"
                    title="Informes"
                >
                    <i class="fas fa-file-alt text-lg w-5"></i>
                    <span 
                        v-show="!sidebarCollapsed"
                        class="font-medium transition-opacity duration-300"
                    >
                        Informes
                    </span>
                </router-link>

                <router-link
                    :to="{ name: 'equipment' }"
                    class="nav-item flex items-center space-x-3 px-4 py-3 text-white rounded-xl transition-all duration-200"
                    :class="isActive('equipment') ? 'nav-item-active' : 'nav-item-hover'"
                    title="Equipos"
                >
                    <i class="fas fa-toolbox text-lg w-5"></i>
                    <span 
                        v-show="!sidebarCollapsed"
                        class="font-medium transition-opacity duration-300"
                    >
                        Equipos
                    </span>
                </router-link>

                <router-link
                    v-if="authStore.hasPermission('consult equipment')"
                    :to="{ name: 'components' }"
                    class="nav-item flex items-center space-x-3 px-4 py-3 text-white rounded-xl transition-all duration-200"
                    :class="isActive('components') ? 'nav-item-active' : 'nav-item-hover'"
                    title="Componentes"
                >
                    <i class="fas fa-weight text-lg w-5"></i>
                    <span 
                        v-show="!sidebarCollapsed"
                        class="font-medium transition-opacity duration-300"
                    >
                        Componentes
                    </span>
                </router-link>

                <router-link
                    v-if="authStore.hasPermission('consult equipment')"
                    :to="{ name: 'component-types' }"
                    class="nav-item flex items-center space-x-3 px-4 py-3 text-white rounded-xl transition-all duration-200"
                    :class="isActive('component-types') ? 'nav-item-active' : 'nav-item-hover'"
                    title="Tipos de Componentes"
                >
                    <i class="fas fa-snowplow text-lg w-5"></i>
                    <span 
                        v-show="!sidebarCollapsed"
                        class="font-medium transition-opacity duration-300"
                    >
                        Tipos de Componentes
                    </span>
                </router-link>

                <router-link
                    :to="{ name: 'companies' }"
                    class="nav-item flex items-center space-x-3 px-4 py-3 text-white rounded-xl transition-all duration-200"
                    :class="isActive('companies') ? 'nav-item-active' : 'nav-item-hover'"
                    title="Empresas"
                >
                    <i class="fas fa-building text-lg w-5"></i>
                    <span 
                        v-show="!sidebarCollapsed"
                        class="font-medium transition-opacity duration-300"
                    >
                        Empresas
                    </span>
                </router-link>

                <router-link
                    v-if="authStore.hasPermission('consult user')"
                    :to="{ name: 'users' }"
                    class="nav-item flex items-center space-x-3 px-4 py-3 text-white rounded-xl transition-all duration-200"
                    :class="isActive('users') ? 'nav-item-active' : 'nav-item-hover'"
                    title="Usuarios"
                >
                    <i class="fas fa-users text-lg w-5"></i>
                    <span 
                        v-show="!sidebarCollapsed"
                        class="font-medium transition-opacity duration-300"
                    >
                        Usuarios
                    </span>
                </router-link>
            </nav>

            <!-- Logout Button -->
            <div class="absolute bottom-0 left-0 right-0 px-4 py-4 border-t border-blue-400/30">
                <button
                    @click="handleLogout"
                    class="nav-item flex items-center space-x-3 px-4 py-3 text-white rounded-xl bg-red-500/20 hover:bg-red-500/30 transition-all duration-200 w-full"
                    title="Cerrar Sesión"
                >
                    <i class="fas fa-sign-out-alt text-lg w-5"></i>
                    <span 
                        v-show="!sidebarCollapsed"
                        class="font-medium transition-opacity duration-300"
                    >
                        Cerrar Sesión
                    </span>
                </button>
            </div>
        </aside>

        <!-- Main Content Area -->
        <div 
            class="flex flex-col transition-all duration-300 ease-in-out"
            :style="{ marginLeft: sidebarCollapsed ? 'var(--sidebar-collapsed-width)' : 'var(--sidebar-width)' }"
        >
            <!-- Top Header -->
            <header class="bg-white/80 backdrop-blur-md shadow-sm border-b border-gray-200/50 sticky top-0 z-30">
                <div class="flex items-center justify-between px-6 py-4">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">{{ pageTitle }}</h1>
                        <p class="text-sm text-gray-500 mt-1">{{ pageDescription }}</p>
                    </div>
                    <div class="flex items-center space-x-4">
                        <!-- Notifications -->
                        <button class="relative p-2 text-gray-600 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors">
                            <i class="fas fa-bell text-lg"></i>
                            <span class="absolute top-0 right-0 w-2 h-2 bg-red-500 rounded-full"></span>
                        </button>
                        <!-- Profile Quick Access -->
                        <router-link
                            :to="{ name: 'profile' }"
                            class="flex items-center space-x-3 px-4 py-2 bg-gradient-to-r from-blue-500 to-indigo-600 text-white rounded-lg hover:from-blue-600 hover:to-indigo-700 transition-all shadow-md hover:shadow-lg"
                        >
                            <div class="w-8 h-8 bg-white/20 rounded-full flex items-center justify-center text-xs font-semibold">
                                {{ userInitials }}
                            </div>
                            <span class="text-sm font-medium">Mi Perfil</span>
                        </router-link>
                    </div>
                </div>
            </header>

            <!-- Main Content -->
            <main class="flex-1 p-6 min-h-screen">
                <router-view />
            </main>

            <!-- Footer -->
            <footer class="bg-white/60 backdrop-blur-sm border-t border-gray-200/50 py-4">
                <div class="px-6 flex items-center justify-between">
                    <p class="text-sm text-gray-600">
                        © {{ currentYear }} <span class="font-semibold text-blue-600">3Sanchez</span>. Todos los derechos reservados.
                    </p>
                    <div class="flex items-center space-x-4 text-sm text-gray-500">
                        <span class="flex items-center">
                            <i class="fas fa-shield-alt mr-2"></i>
                            Sistema Seguro
                        </span>
                    </div>
                </div>
            </footer>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useAuthStore } from '../stores/auth';

const authStore = useAuthStore();
const router = useRouter();
const route = useRoute();

const sidebarCollapsed = ref(false);

const userInitials = computed(() => {
    if (!authStore.user?.name) return 'U';
    return authStore.user.name.substring(0, 2).toUpperCase();
});

const currentYear = computed(() => new Date().getFullYear());

const pageTitles = {
    dashboard: 'Dashboard',
    reports: 'Informes',
    equipment: 'Equipos',
    components: 'Componentes',
    'component-types': 'Tipos de Componentes',
    'component-types.create': 'Crear Tipo de Componente',
    'component-types.edit': 'Editar Tipo de Componente',
    'component-types.show': 'Detalles del Tipo de Componente',
    companies: 'Empresas',
    users: 'Usuarios',
    'users.create': 'Crear Usuario',
    'users.edit': 'Editar Usuario',
    'users.show': 'Detalles del Usuario',
    profile: 'Mi Perfil',
};

const pageDescriptions = {
    dashboard: 'Panel principal del sistema',
    reports: 'Gestiona y visualiza los informes del sistema',
    equipment: 'Gestiona el inventario de equipos',
    components: 'Gestiona los componentes del sistema',
    'component-types': 'Gestiona los tipos de componentes del sistema',
    'component-types.create': 'Completa el formulario para crear un nuevo tipo de componente',
    'component-types.edit': 'Modifica la información del tipo de componente',
    'component-types.show': 'Información completa del tipo de componente',
    companies: 'Administra las empresas registradas',
    users: 'Gestiona los usuarios del sistema',
    'users.create': 'Completa el formulario para crear un nuevo usuario',
    'users.edit': 'Modifica la información del usuario',
    'users.show': 'Información completa del usuario',
    profile: 'Administra tu información personal',
};

const pageTitle = computed(() => {
    return pageTitles[route.name] || 'Dashboard';
});

const pageDescription = computed(() => {
    return pageDescriptions[route.name] || 'Bienvenido de vuelta';
});

const isActive = (routeName) => {
    if (routeName === 'components') {
        return route.name === 'components' || route.name === 'components.create' || route.name === 'components.edit' || route.name === 'components.show';
    }
    if (routeName === 'equipment') {
        return route.name === 'equipment' || route.name === 'equipment.create' || route.name === 'equipment.edit' || route.name === 'equipment.show';
    }
    if (routeName === 'companies') {
        return route.name === 'companies' || route.name === 'companies.create' || route.name === 'companies.edit' || route.name === 'companies.show';
    }
    if (routeName === 'reports') {
        return route.name === 'reports' || route.name === 'reports.create' || route.name === 'reports.edit' || route.name === 'reports.show';
    }
    if (routeName === 'users') {
        return route.name === 'users' || route.name === 'users.create' || route.name === 'users.edit' || route.name === 'users.show';
    }
    if (routeName === 'component-types') {
        return route.name === 'component-types' || route.name === 'component-types.create' || route.name === 'component-types.edit' || route.name === 'component-types.show';
    }
    return route.name === routeName;
};

const toggleSidebar = () => {
    sidebarCollapsed.value = !sidebarCollapsed.value;
};

const handleLogout = async () => {
    await authStore.logout();
    router.push('/login');
};
</script>
