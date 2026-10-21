<template>
    <div class="font-sans antialiased bg-gradient-to-br from-gray-50 via-blue-50 to-indigo-50 min-h-screen">
        <!-- Backdrop móvil -->
        <div
            v-show="mobileMenuOpen"
            class="fixed inset-0 bg-black/50 z-30 lg:hidden"
            aria-hidden="true"
            @click="mobileMenuOpen = false"
        ></div>

        <!-- Sidebar: móvil full-screen drawer, desktop fijo -->
        <aside
            id="sidebar"
            class="fixed left-0 top-0 h-screen sidebar-gradient shadow-2xl z-40 transition-transform duration-300 ease-out w-full max-w-[320px] lg:max-w-none -translate-x-full lg:translate-x-0 lg:w-auto rounded-r-2xl lg:rounded-none"
            :class="{ 'translate-x-0': mobileMenuOpen }"
            :style="isDesktop ? { width: sidebarCollapsed ? 'var(--sidebar-collapsed-width)' : 'var(--sidebar-width)' } : {}"
        >
            <!-- Logo and Toggle: móvil = cerrar (X), desktop = expandir/colapsar -->
            <div
                class="flex items-center justify-between border-b border-blue-400/30 py-4 sm:py-5 transition-all duration-300"
                :class="sidebarCollapsed && isDesktop ? 'px-2' : 'px-4 sm:px-6'"
            >
                <div class="flex items-center space-x-3 min-w-0 shrink">
                    <div
                        v-show="!sidebarCollapsed || !isDesktop"
                        class="bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm shrink-0 transition-all duration-300 w-10 h-10"
                    >
                        <i class="fas fa-cog text-white text-xl shrink-0"></i>
                    </div>
                    <span
                        v-show="!sidebarCollapsed || !isDesktop"
                        class="text-white font-bold text-xl transition-opacity duration-300 truncate"
                    >
                        3Sanchez
                    </span>
                </div>
                <!-- Móvil: botón cerrar -->
                <button
                    v-if="!isDesktop"
                    @click="mobileMenuOpen = false"
                    class="lg:hidden w-11 h-11 flex items-center justify-center rounded-full bg-white/15 hover:bg-white/25 text-white transition-colors -mr-1"
                    aria-label="Cerrar menú"
                >
                    <i class="fas fa-times text-xl"></i>
                </button>
                <!-- Desktop: toggle collapse/expand (siempre visible) -->
                <button
                    v-else
                    @click="toggleSidebar"
                    class="hidden lg:flex flex-shrink-0 text-white hover:bg-white/20 rounded-lg p-2 transition-colors min-w-[2.25rem] min-h-[2.25rem] items-center justify-center"
                    :aria-label="sidebarCollapsed ? 'Expandir menú' : 'Colapsar menú'"
                >
                    <i class="fas fa-bars text-lg"></i>
                </button>
            </div>

            <!-- User Profile Section -->
            <div
                class="px-4 py-4 border-b border-blue-400/30"
                :class="{ 'py-2': sidebarCollapsed && isDesktop }"
            >
                <router-link
                    :to="{ name: 'profile' }"
                    @click="mobileMenuOpen = false"
                    class="flex items-center space-x-3 bg-white/10 backdrop-blur-sm rounded-xl p-3 lg:p-3 min-h-[52px] lg:min-h-0 hover:bg-white/15 active:bg-white/20 transition-colors cursor-pointer"
                >
                    <div class="w-10 h-10 bg-gradient-to-br from-yellow-400 to-orange-500 rounded-full flex items-center justify-center text-white font-semibold text-sm shadow-lg flex-shrink-0">
                        {{ userInitials }}
                    </div>
                    <div
                        v-show="!sidebarCollapsed || !isDesktop"
                        class="flex-1 min-w-0 transition-opacity duration-300"
                    >
                        <p class="text-white font-semibold text-sm truncate">{{ authStore.user && authStore.user.name }}</p>
                        <p class="text-blue-100 text-xs truncate">{{ authStore.user && authStore.user.email }}</p>
                    </div>
                    <i
                        v-show="!sidebarCollapsed || !isDesktop"
                        class="fas fa-chevron-right text-blue-200 text-xs transition-opacity duration-300"
                    ></i>
                </router-link>
            </div>

            <!-- Navigation -->
            <!-- Orden: Inicio, Informes, Equipos, Componentes, Tipo Componente, Usuario, Empresa, Indicadores -->
            <nav class="px-4 py-4 space-y-1 overflow-y-auto overscroll-contain" style="max-height: calc(100vh - 220px);">
                <router-link
                    :to="{ name: 'dashboard' }"
                    @click="mobileMenuOpen = false"
                    class="nav-item flex items-center space-x-3 px-4 py-3.5 lg:py-3 min-h-[48px] lg:min-h-0 text-white rounded-xl transition-all duration-200 active:bg-white/20"
                    :class="isActive('dashboard') ? 'nav-item-active' : 'nav-item-hover'"
                    title="Inicio"
                >
                    <i class="fas fa-home text-lg w-5"></i>
                    <span
                        v-show="!sidebarCollapsed || !isDesktop"
                        class="font-medium transition-opacity duration-300"
                    >
                        Inicio
                    </span>
                </router-link>

                <router-link
                    v-if="authStore.hasPermission('consult report')"
                    :to="{ name: 'reports' }"
                    @click="mobileMenuOpen = false"
                    class="nav-item flex items-center space-x-3 px-4 py-3.5 lg:py-3 min-h-[48px] lg:min-h-0 text-white rounded-xl transition-all duration-200 active:bg-white/20"
                    :class="isActive('reports') ? 'nav-item-active' : 'nav-item-hover'"
                    title="Informes"
                >
                    <i class="fas fa-file-alt text-lg w-5"></i>
                    <span
                        v-show="!sidebarCollapsed || !isDesktop"
                        class="font-medium transition-opacity duration-300"
                    >
                        Informes
                    </span>
                </router-link>

                <router-link
                    v-if="authStore.hasPermission('consult equipment')"
                    :to="{ name: 'equipment' }"
                    @click="mobileMenuOpen = false"
                    class="nav-item flex items-center space-x-3 px-4 py-3.5 lg:py-3 min-h-[48px] lg:min-h-0 text-white rounded-xl transition-all duration-200 active:bg-white/20"
                    :class="isActive('equipment') ? 'nav-item-active' : 'nav-item-hover'"
                    title="Equipos"
                >
                    <i class="fas fa-toolbox text-lg w-5"></i>
                    <span
                        v-show="!sidebarCollapsed || !isDesktop"
                        class="font-medium transition-opacity duration-300"
                    >
                        Equipos
                    </span>
                </router-link>

                <router-link
                    v-if="authStore.hasPermission('consult component type')"
                    :to="{ name: 'component-types' }"
                    @click="mobileMenuOpen = false"
                    class="nav-item flex items-center space-x-3 px-4 py-3.5 lg:py-3 min-h-[48px] lg:min-h-0 text-white rounded-xl transition-all duration-200 active:bg-white/20"
                    :class="isActive('component-types') ? 'nav-item-active' : 'nav-item-hover'"
                    title="Tipo Componente"
                >
                    <i class="fas fa-snowplow text-lg w-5"></i>
                    <span
                        v-show="!sidebarCollapsed || !isDesktop"
                        class="font-medium transition-opacity duration-300"
                    >
                        Tipo Componente
                    </span>
                </router-link>

                <router-link
                    v-if="authStore.hasPermission('consult user')"
                    :to="{ name: 'users' }"
                    @click="mobileMenuOpen = false"
                    class="nav-item flex items-center space-x-3 px-4 py-3.5 lg:py-3 min-h-[48px] lg:min-h-0 text-white rounded-xl transition-all duration-200 active:bg-white/20"
                    :class="isActive('users') ? 'nav-item-active' : 'nav-item-hover'"
                    title="Usuario"
                >
                    <i class="fas fa-users text-lg w-5"></i>
                    <span
                        v-show="!sidebarCollapsed || !isDesktop"
                        class="font-medium transition-opacity duration-300"
                    >
                        Usuario
                    </span>
                </router-link>

                <router-link
                    v-if="authStore.hasPermission('consult company')"
                    :to="{ name: 'companies' }"
                    @click="mobileMenuOpen = false"
                    class="nav-item flex items-center space-x-3 px-4 py-3.5 lg:py-3 min-h-[48px] lg:min-h-0 text-white rounded-xl transition-all duration-200 active:bg-white/20"
                    :class="isActive('companies') ? 'nav-item-active' : 'nav-item-hover'"
                    title="Empresa"
                >
                    <i class="fas fa-building text-lg w-5"></i>
                    <span
                        v-show="!sidebarCollapsed || !isDesktop"
                        class="font-medium transition-opacity duration-300"
                    >
                        Empresa
                    </span>
                </router-link>

                <router-link
                    v-if="authStore.hasPermission('consult indicators')"
                    :to="{ name: 'indicators' }"
                    @click="mobileMenuOpen = false"
                    class="nav-item flex items-center space-x-3 px-4 py-3.5 lg:py-3 min-h-[48px] lg:min-h-0 text-white rounded-xl transition-all duration-200 active:bg-white/20"
                    :class="isActive('indicators') ? 'nav-item-active' : 'nav-item-hover'"
                    title="Indicadores"
                >
                    <i class="fas fa-chart-bar text-lg w-5"></i>
                    <span
                        v-show="!sidebarCollapsed || !isDesktop"
                        class="font-medium transition-opacity duration-300"
                    >
                        Indicadores
                    </span>
                </router-link>

                <router-link
                    v-if="authStore.hasPermission('consult role')"
                    :to="{ name: 'roles' }"
                    @click="mobileMenuOpen = false"
                    class="nav-item flex items-center space-x-3 px-4 py-3.5 lg:py-3 min-h-[48px] lg:min-h-0 text-white rounded-xl transition-all duration-200 active:bg-white/20"
                    :class="isActive('roles') ? 'nav-item-active' : 'nav-item-hover'"
                    title="Roles y permisos"
                >
                    <i class="fas fa-user-shield text-lg w-5"></i>
                    <span
                        v-show="!sidebarCollapsed || !isDesktop"
                        class="font-medium transition-opacity duration-300"
                    >
                        Roles y permisos
                    </span>
                </router-link>
            </nav>

            <!-- Logout Button -->
            <div class="absolute bottom-0 left-0 right-0 px-4 py-4 border-t border-blue-400/30">
                <button
                    @click="handleLogout"
                    class="nav-item flex items-center space-x-3 px-4 py-3.5 lg:py-3 min-h-[48px] lg:min-h-0 text-white rounded-xl bg-red-500/20 hover:bg-red-500/30 active:bg-red-500/40 transition-all duration-200 w-full"
                    title="Cerrar Sesión"
                >
                    <i class="fas fa-sign-out-alt text-lg w-5"></i>
                    <span
                        v-show="!sidebarCollapsed || !isDesktop"
                        class="font-medium transition-opacity duration-300"
                    >
                        Cerrar Sesión
                    </span>
                </button>
            </div>
        </aside>

        <!-- Main Content Area -->
        <div
            class="flex flex-col transition-all duration-300 ease-in-out min-w-0"
            :style="mainContentStyle"
        >
            <!-- Top Header -->
            <header class="bg-white/80 backdrop-blur-md shadow-sm border-b border-gray-200/50 sticky top-0 z-30">
                <div class="flex items-center gap-3 px-4 sm:px-6 py-3 sm:py-4">
                    <!-- Hamburger móvil -->
                    <button
                        class="lg:hidden w-10 h-10 flex items-center justify-center rounded-lg text-gray-600 hover:bg-gray-100"
                        @click="mobileMenuOpen = true"
                        aria-label="Abrir menú"
                    >
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                    <div class="flex-1 min-w-0">
                        <h1 class="text-lg sm:text-2xl font-bold text-gray-800 truncate">{{ pageTitle }}</h1>
                        <p class="text-xs sm:text-sm text-gray-500 truncate">{{ pageDescription }}</p>
                    </div>
                    <div class="flex items-center gap-2 sm:gap-4 shrink-0">
                        <!-- Notifications -->
                        <button class="relative p-2 text-gray-600 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors">
                            <i class="fas fa-bell text-lg"></i>
                            <span class="absolute top-0 right-0 w-2 h-2 bg-red-500 rounded-full"></span>
                        </button>
                        <!-- Profile Quick Access -->
                        <router-link
                            :to="{ name: 'profile' }"
                            class="flex items-center gap-2 px-3 py-2 sm:px-4 bg-gradient-to-r from-blue-500 to-indigo-600 text-white rounded-lg hover:from-blue-600 hover:to-indigo-700 transition-all shadow-md hover:shadow-lg"
                        >
                            <div class="w-8 h-8 bg-white/20 rounded-full flex items-center justify-center text-xs font-semibold shrink-0">
                                {{ userInitials }}
                            </div>
                            <span class="hidden sm:inline text-sm font-medium">Mi Perfil</span>
                        </router-link>
                    </div>
                </div>
            </header>

            <!-- Main Content -->
            <main class="flex-1 p-4 sm:p-6 min-h-screen min-w-0">
                <router-view />
            </main>

            <!-- Footer -->
            <footer class="bg-white/60 backdrop-blur-sm border-t border-gray-200/50 py-3 sm:py-4">
                <div class="px-4 sm:px-6 flex flex-col sm:flex-row items-center justify-between gap-2 text-center sm:text-left">
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
import { ref, computed, onMounted, onUnmounted, watch } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useAuthStore } from '../stores/auth';
import { useReportTypesStore } from '../stores/reportTypes';

const authStore = useAuthStore();
const reportTypesStore = useReportTypesStore();
const router = useRouter();
const route = useRoute();

const sidebarCollapsed = ref(false);
const mobileMenuOpen = ref(false);
const isDesktop = ref(typeof window !== 'undefined' && window.innerWidth >= 1024);

const mainContentStyle = computed(() => {
    if (!isDesktop.value) return { marginLeft: 0 };
    return { marginLeft: sidebarCollapsed.value ? 'var(--sidebar-collapsed-width)' : 'var(--sidebar-width)' };
});

function onResize() {
    isDesktop.value = window.innerWidth >= 1024;
    if (isDesktop.value) mobileMenuOpen.value = false;
}

watch(() => route.path, () => {
    mobileMenuOpen.value = false;
});

onMounted(() => {
    reportTypesStore.load();
    onResize();
    window.addEventListener('resize', onResize);
});
onUnmounted(() => {
    window.removeEventListener('resize', onResize);
});

const userInitials = computed(() => {
    if (!authStore.user?.name) return 'U';
    return authStore.user.name.substring(0, 2).toUpperCase();
});

const currentYear = computed(() => new Date().getFullYear());

const pageTitles = {
    dashboard: 'Dashboard',
    reports: 'Informes',
    'reports.vibration': 'Reportes de Vibraciones',
    'reports.thermography': 'Reportes de Termografía',
    'reports.create': 'Crear Reporte',
    'reports.edit': 'Editar Reporte',
    'reports.show': 'Detalle del Reporte',
    equipment: 'Equipos',
    'equipment.vibration': 'Equipos (Vibraciones)',
    'equipment.thermography': 'Equipos (Termografía)',
    'equipment.create': 'Crear Equipo',
    'equipment.edit': 'Editar Equipo',
    'equipment.show': 'Detalle del Equipo',
    components: 'Componentes',
    'component-types': 'Tipos de Componentes',
    'component-types.vibration': 'Tipos de Componentes (Vibraciones)',
    'component-types.thermography': 'Tipos de Componentes (Termografía)',
    'component-types.create': 'Crear Tipo de Componente',
    'component-types.edit': 'Editar Tipo de Componente',
    'component-types.show': 'Detalles del Tipo de Componente',
    companies: 'Empresas',
    'companies.create': 'Crear Empresa',
    'companies.edit': 'Editar Empresa',
    'companies.show': 'Detalle de la Empresa',
    indicators: 'Indicadores',
    users: 'Usuarios',
    'users.create': 'Crear Usuario',
    'users.edit': 'Editar Usuario',
    'users.show': 'Detalles del Usuario',
    roles: 'Roles y permisos',
    'roles.create': 'Crear rol',
    'roles.edit': 'Editar permisos del rol',
    profile: 'Mi Perfil',
    'not-found': 'Página no encontrada',
};

const pageDescriptions = {
    dashboard: 'Panel principal del sistema',
    reports: 'Gestiona y visualiza los informes del sistema',
    'reports.vibration': 'Gestiona y visualiza los reportes de vibraciones',
    'reports.thermography': 'Listado de informes térmicos',
    'reports.create': 'Completa el formulario para crear un nuevo reporte',
    'reports.edit': 'Modifica la información del reporte',
    'reports.show': 'Información completa del reporte',
    equipment: 'Gestiona el inventario de equipos',
    'equipment.vibration': 'Gestiona los equipos para vibraciones',
    'equipment.thermography': 'Gestiona los equipos para termografía',
    'equipment.create': 'Completa el formulario para registrar un nuevo equipo',
    'equipment.edit': 'Modifica la información del equipo',
    'equipment.show': 'Información completa del equipo',
    components: 'Gestiona los componentes del sistema',
    'component-types': 'Gestiona los tipos de componentes del sistema',
    'component-types.vibration': 'Gestiona los tipos de componente para vibraciones',
    'component-types.thermography': 'Gestiona los tipos de componente para termografía',
    'component-types.create': 'Completa el formulario para crear un nuevo tipo de componente',
    'component-types.edit': 'Modifica la información del tipo de componente',
    'component-types.show': 'Información completa del tipo de componente',
    companies: 'Administra las empresas registradas',
    'companies.create': 'Completa el formulario para crear una nueva empresa',
    'companies.edit': 'Modifica la información de la empresa',
    'companies.show': 'Información completa de la empresa',
    indicators: 'Estadísticas e indicadores a partir de los datos del sistema',
    users: 'Gestiona los usuarios del sistema',
    'users.create': 'Completa el formulario para crear un nuevo usuario',
    'users.edit': 'Modifica la información del usuario',
    'users.show': 'Información completa del usuario',
    roles: 'Gestiona los permisos de cada rol',
    'roles.create': 'Define un nuevo rol y sus permisos',
    'roles.edit': 'Asigna o modifica los permisos del rol',
    profile: 'Administra tu información personal',
    'not-found': 'La ruta solicitada no existe',
};

// Título y descripción de los tipos que no estén en los mapas de arriba,
// para que un tipo nuevo no necesite tocar este archivo.
const typeSectionMeta = {
    reports: {
        title: (name) => `Reportes de ${name}`,
        description: (name) => `Gestiona y visualiza los informes de ${name.toLowerCase()}`,
    },
    equipment: {
        title: (name) => `Equipos (${name})`,
        description: (name) => `Gestiona los equipos para ${name.toLowerCase()}`,
    },
    'component-types': {
        title: (name) => `Tipos de Componentes (${name})`,
        description: (name) => `Gestiona los tipos de componente para ${name.toLowerCase()}`,
    },
};

const dynamicTypePage = (routeName) => {
    const [section, code] = String(routeName || '').split('.');
    const meta = typeSectionMeta[section];
    if (!meta || !code) return null;

    const type = reportTypesStore.types.find((item) => item.code === code);
    if (!type) return null;

    return { title: meta.title(type.name), description: meta.description(type.name) };
};

const pageTitle = computed(() => {
    if (pageTitles[route.name]) return pageTitles[route.name];
    return dynamicTypePage(route.name)?.title ?? route.name;
});

const pageDescription = computed(() => {
    if (pageDescriptions[route.name]) return pageDescriptions[route.name];
    return dynamicTypePage(route.name)?.description ?? '';
});

const isActive = (routeName) => {
    if (routeName === 'components') {
        return route.name === 'components' || route.name === 'components.create' || route.name === 'components.edit' || route.name === 'components.show';
    }
    if (routeName === 'equipment') {
        return route.name === routeName || String(route.name || '').startsWith('equipment.');
    }
    if (routeName === 'companies') {
        return route.name === 'companies' || route.name === 'companies.create' || route.name === 'companies.edit' || route.name === 'companies.show';
    }
    if (routeName === 'reports') {
        return route.name === routeName
            || String(route.name || '').startsWith('reports.');
    }
    if (routeName === 'users') {
        return route.name === 'users' || route.name === 'users.create' || route.name === 'users.edit' || route.name === 'users.show';
    }
    if (routeName === 'roles') {
        return route.name === 'roles' || route.name === 'roles.create' || route.name === 'roles.edit';
    }
    if (routeName === 'component-types') {
        return route.name === routeName || String(route.name || '').startsWith('component-types.');
    }
    if (routeName === 'indicators') {
        return route.name === 'indicators';
    }
    return route.name === routeName;
};

const toggleSidebar = () => {
    if (window.innerWidth >= 1024) {
        sidebarCollapsed.value = !sidebarCollapsed.value;
    } else {
        mobileMenuOpen.value = false;
    }
};

const handleLogout = async () => {
    await authStore.logout();
    router.push('/login');
};
</script>
