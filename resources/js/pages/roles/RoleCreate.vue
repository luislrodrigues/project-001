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
            <div class="mb-6">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h2 class="text-3xl font-bold text-gray-800 flex items-center">
                            <i class="fas fa-user-plus text-blue-600 mr-3"></i>
                            Crear rol
                        </h2>
                        <p class="text-gray-500 text-sm mt-1">Define el nombre del rol y asigna los permisos iniciales</p>
                    </div>
                    <router-link
                        :to="{ name: 'roles' }"
                        class="text-gray-600 hover:text-gray-900 p-2 hover:bg-gray-100 rounded-lg transition-colors"
                        title="Volver"
                    >
                        <i class="fas fa-arrow-left text-xl"></i>
                    </router-link>
                </div>
            </div>

            <form @submit.prevent="createRole" class="space-y-6">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-tag text-blue-600 mr-2"></i>
                        Nombre del rol
                        <span class="text-red-500 ml-1">*</span>
                    </label>
                    <input
                        v-model.trim="form.name"
                        type="text"
                        required
                        maxlength="255"
                        placeholder="Ej: Soporte, Auditor..."
                        class="block w-full border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 px-4 py-2.5"
                    />
                    <p v-if="errors.name" class="mt-1 text-sm text-red-600">{{ errors.name }}</p>
                </div>

                <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                        <h4 class="text-lg font-semibold text-gray-800 flex items-center">
                            <i class="fas fa-key text-blue-500 mr-2"></i>
                            Permisos
                        </h4>
                        <p class="text-sm text-gray-500 mt-1">Marca los permisos que tendrá este rol</p>
                    </div>
                    <div
                        v-for="(perms, resourceKey) in groupedPermissions"
                        :key="resourceKey"
                        class="border-b border-gray-200 last:border-b-0"
                    >
                        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                            <h4 class="text-lg font-semibold text-gray-800 flex items-center">
                                <i class="fas fa-folder-open text-blue-500 mr-2"></i>
                                {{ resourceLabels[resourceKey] || resourceKey }}
                            </h4>
                        </div>
                        <div class="px-6 py-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                            <label
                                v-for="perm in perms"
                                :key="perm.name"
                                class="flex items-center space-x-3 p-3 rounded-lg hover:bg-blue-50 cursor-pointer transition-colors"
                            >
                                <input
                                    v-model="selectedPermissions"
                                    type="checkbox"
                                    :value="perm.name"
                                    class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                />
                                <span class="text-sm text-gray-700">{{ formatPermissionLabel(perm.name) }}</span>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="flex flex-wrap items-center gap-3">
                    <button
                        type="submit"
                        :disabled="saving"
                        class="inline-flex items-center justify-center px-5 py-2.5 bg-blue-500 text-white rounded-lg hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors font-medium disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        <i v-if="saving" class="fas fa-spinner fa-spin mr-2"></i>
                        <i v-else class="fas fa-plus mr-2"></i>
                        {{ saving ? 'Creando...' : 'Crear rol' }}
                    </button>
                    <router-link
                        :to="{ name: 'roles' }"
                        class="inline-flex items-center px-5 py-2.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-400 focus:ring-offset-2 transition-colors font-medium"
                    >
                        Cancelar
                    </router-link>
                </div>
            </form>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import api from '../../services/api';
import Swal from 'sweetalert2';

const router = useRouter();
const loading = ref(true);
const saving = ref(false);
const form = ref({ name: '' });
const errors = ref({});
const allPermissions = ref([]);
const selectedPermissions = ref([]);

const resourceLabels = {
    user: 'Usuario',
    company: 'Empresa',
    equipment: 'Equipo',
    'component type': 'Tipo de componente',
    report: 'Reporte',
    indicators: 'Indicadores',
    role: 'Rol',
};

function getResourceKey(permissionName) {
    const parts = permissionName.split(' ');
    if (parts.length >= 2) {
        return parts.slice(1).join(' ');
    }
    return 'otros';
}

function formatPermissionLabel(permissionName) {
    const parts = permissionName.split(' ');
    const action = parts[0];
    const actionLabels = {
        consult: 'Consultar',
        save: 'Crear',
        edit: 'Editar',
        delete: 'Eliminar',
        detalle: 'Detalle',
        inactive: 'Activar/Desactivar',
        download: 'Descargar PDF',
        send: 'Enviar correo',
        export: 'Exportar Excel',
    };
    return actionLabels[action] || action;
}

const groupedPermissions = computed(() => {
    const groups = {};
    for (const perm of allPermissions.value) {
        let key = getResourceKey(perm.name);
        if (key === 'pdf report' || key === 'email report') key = 'report';
        if (!groups[key]) groups[key] = [];
        groups[key].push(perm);
    }
    const order = ['role', 'user', 'company', 'equipment', 'component type', 'report', 'indicators'];
    const sorted = {};
    for (const key of order) {
        if (groups[key]) sorted[key] = groups[key];
    }
    for (const key of Object.keys(groups)) {
        if (!sorted[key]) sorted[key] = groups[key];
    }
    return sorted;
});

async function loadPermissions() {
    const res = await api.get('/permissions');
    allPermissions.value = res.data.data || [];
}

async function createRole() {
    errors.value = {};
    if (!form.value.name || !form.value.name.trim()) {
        errors.value.name = 'El nombre del rol es obligatorio.';
        return;
    }
    saving.value = true;
    try {
        const res = await api.post('/roles', {
            name: form.value.name.trim(),
            permissions: selectedPermissions.value,
        });
        await Swal.fire('Rol creado', 'El rol se ha creado correctamente.', 'success');
        const roleId = res.data.data?.id;
        if (roleId) {
            router.push({ name: 'roles.edit', params: { id: roleId } });
        } else {
            router.push({ name: 'roles' });
        }
    } catch (error) {
        const data = error.response?.data;
        const msg = data?.message || 'Error al crear el rol.';
        if (data?.errors && typeof data.errors === 'object') {
            errors.value = data.errors;
        }
        Swal.fire('Error', msg, 'error');
    } finally {
        saving.value = false;
    }
}

onMounted(async () => {
    loading.value = true;
    try {
        await loadPermissions();
    } catch (error) {
        Swal.fire('Error', 'No se pudieron cargar los permisos.', 'error');
        router.push({ name: 'roles' });
    } finally {
        loading.value = false;
    }
});
</script>
