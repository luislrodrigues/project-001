<template>
    <div class="container mx-auto px-4 sm:px-8">
        <div class="py-6">
            <!-- Header -->
            <div class="mb-6">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h2 class="text-3xl font-bold text-gray-800 flex items-center">
                            <i class="fas fa-user-plus text-blue-600 mr-3"></i>
                            Crear Nuevo Usuario
                        </h2>
                        <p class="text-gray-500 text-sm mt-1">Completa el formulario para crear un nuevo usuario</p>
                    </div>
                    <router-link
                        :to="{ name: 'users' }"
                        class="text-gray-600 hover:text-gray-900 p-2 hover:bg-gray-100 rounded-lg transition-colors"
                        title="Volver"
                    >
                        <i class="fas fa-arrow-left text-xl"></i>
                    </router-link>
                </div>
            </div>

            <!-- Form -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <form @submit.prevent="saveUser" class="space-y-6">
                    <!-- Información del Usuario -->
                    <div>
                        <div class="flex items-center mb-4 pb-2 border-b border-gray-200">
                            <i class="fas fa-info-circle text-blue-600 mr-2"></i>
                            <h4 class="text-lg font-semibold text-gray-800">Información del Usuario</h4>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="bg-gray-50 rounded-lg p-4">
                                <label class="flex items-center mb-2 text-sm font-medium text-gray-700">
                                    <i class="fas fa-user mr-2 text-blue-600"></i>
                                    Nombre
                                    <span class="text-red-500 ml-1">*</span>
                                </label>
                                <input
                                    v-model="form.name"
                                    type="text"
                                    required
                                    class="block w-full bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 px-4 py-2.5 transition-all duration-200"
                                    placeholder="Ingresar nombre"
                                />
                                <p v-if="errors.name" class="mt-1 text-sm text-red-600">{{ errors.name }}</p>
                            </div>

                            <div class="bg-gray-50 rounded-lg p-4">
                                <label class="flex items-center mb-2 text-sm font-medium text-gray-700">
                                    <i class="fas fa-envelope mr-2 text-blue-600"></i>
                                    Correo
                                    <span class="text-red-500 ml-1">*</span>
                                </label>
                                <input
                                    v-model="form.email"
                                    type="email"
                                    required
                                    class="block w-full bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 px-4 py-2.5 transition-all duration-200"
                                    placeholder="correo@ejemplo.com"
                                />
                                <p v-if="errors.email" class="mt-1 text-sm text-red-600">{{ errors.email }}</p>
                            </div>

                            <div class="bg-gray-50 rounded-lg p-4">
                                <label class="flex items-center mb-2 text-sm font-medium text-gray-700">
                                    <i class="fas fa-building mr-2 text-blue-600"></i>
                                    Empresa
                                    <span class="text-red-500 ml-1">*</span>
                                </label>
                                <select
                                    v-model="form.company_id"
                                    required
                                    class="block w-full bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 px-4 py-2.5 transition-all duration-200"
                                >
                                    <option value="">Seleccione una compañía</option>
                                    <option v-for="company in companies" :key="company.id" :value="company.id">
                                        {{ company.name }}
                                    </option>
                                </select>
                                <p v-if="errors.company_id" class="mt-1 text-sm text-red-600">{{ errors.company_id }}</p>
                            </div>

                            <div class="bg-gray-50 rounded-lg p-4">
                                <label class="flex items-center mb-2 text-sm font-medium text-gray-700">
                                    <i class="fas fa-briefcase mr-2 text-blue-600"></i>
                                    Cargo
                                </label>
                                <select
                                    v-model="form.role_id"
                                    class="block w-full bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 px-4 py-2.5 transition-all duration-200"
                                >
                                    <option value="">Seleccione un cargo</option>
                                    <option v-for="role in roles" :key="role.id" :value="role.id">
                                        {{ role.name }}
                                    </option>
                                </select>
                                <p v-if="errors.role_id" class="mt-1 text-sm text-red-600">{{ errors.role_id }}</p>
                            </div>

                            <div class="bg-gray-50 rounded-lg p-4">
                                <label class="flex items-center mb-2 text-sm font-medium text-gray-700">
                                    <i class="fas fa-briefcase mr-2 text-blue-600"></i>
                                    Puesto de Trabajo
                                    <span class="text-red-500 ml-1">*</span>
                                </label>
                                <input
                                    v-model="form.workstation"
                                    type="text"
                                    required
                                    class="block w-full bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 px-4 py-2.5 transition-all duration-200"
                                    placeholder="Ingresar puesto de trabajo"
                                />
                                <p v-if="errors.workstation" class="mt-1 text-sm text-red-600">{{ errors.workstation }}</p>
                            </div>

                            <div class="bg-gray-50 rounded-lg p-4">
                                <label class="flex items-center mb-2 text-sm font-medium text-gray-700">
                                    <i class="fas fa-lock mr-2 text-blue-600"></i>
                                    Contraseña
                                    <span class="text-red-500 ml-1">*</span>
                                </label>
                                <div class="relative">
                                    <input
                                        v-model="form.password"
                                        :type="showPassword ? 'text' : 'password'"
                                        required
                                        class="block w-full bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 px-4 py-2.5 pr-12 transition-all duration-200"
                                        placeholder="Ingresar contraseña"
                                    />
                                    <button
                                        type="button"
                                        @click="showPassword = !showPassword"
                                        class="absolute inset-y-0 right-0 flex items-center pr-4 text-gray-500 hover:text-gray-700"
                                    >
                                        <i :class="showPassword ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                                    </button>
                                </div>
                                <p v-if="errors.password" class="mt-1 text-sm text-red-600">{{ errors.password }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
                        <router-link
                            :to="{ name: 'users' }"
                            class="px-6 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition-colors font-medium"
                        >
                            Cancelar
                        </router-link>
                        <button
                            type="submit"
                            :disabled="saving"
                            class="px-6 py-2.5 bg-blue-500 hover:bg-blue-600 text-white rounded-lg transition-colors font-medium shadow-sm disabled:opacity-50"
                        >
                            <i class="fas fa-save mr-2"></i>
                            {{ saving ? 'Guardando...' : 'Guardar' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, reactive } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '../../stores/auth';
import api from '../../services/api';
import Swal from 'sweetalert2';

const router = useRouter();
const authStore = useAuthStore();

const form = reactive({
    name: '',
    email: '',
    password: '',
    workstation: '',
    company_id: '',
    role_id: ''
});

const companies = ref([]);
const roles = ref([]);
const saving = ref(false);
const errors = ref({});
const showPassword = ref(false);

const loadCatalogs = async () => {
    try {
        const [companiesRes, rolesRes] = await Promise.all([
            api.get('/catalogs/companies'),
            api.get('/catalogs/roles'),
        ]);

        companies.value = companiesRes.data.data;
        roles.value = rolesRes.data.data;
    } catch (error) {
        Swal.fire('Error', 'Error al cargar catálogos', 'error');
    }
};

const saveUser = async () => {
    saving.value = true;
    errors.value = {};

    try {
        await api.post('/users', form);
        Swal.fire('Éxito', 'Usuario creado correctamente', 'success');
        router.push({ name: 'users' });
    } catch (error) {
        if (error.response?.status === 422 && error.response?.data?.errors) {
            const e = error.response.data.errors;
            errors.value = Object.fromEntries(
                Object.entries(e).map(([k, v]) => [k, Array.isArray(v) ? v[0] : v])
            );
        }
        const message = error.response?.data?.message || 'Error al crear el usuario';
        Swal.fire('Error', message, 'error');
    } finally {
        saving.value = false;
    }
};

onMounted(() => {
    loadCatalogs();
});
</script>
