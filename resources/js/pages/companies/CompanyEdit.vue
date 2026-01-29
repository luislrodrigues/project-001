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
            <!-- Header -->
            <div class="mb-6">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h2 class="text-3xl font-bold text-gray-800 flex items-center">
                            <i class="fas fa-building text-blue-600 mr-3"></i>
                            Editar Empresa
                        </h2>
                        <p class="text-gray-500 text-sm mt-1">Modifica la información de la empresa</p>
                    </div>
                    <router-link
                        :to="{ name: 'companies' }"
                        class="text-gray-600 hover:text-gray-900 p-2 hover:bg-gray-100 rounded-lg transition-colors"
                        title="Volver"
                    >
                        <i class="fas fa-arrow-left text-xl"></i>
                    </router-link>
                </div>
            </div>

            <!-- Form -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <form @submit.prevent="updateCompany" class="space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-building mr-2 text-blue-600"></i>
                            Nombre de la empresa
                            <span class="text-red-500 ml-1">*</span>
                        </label>
                        <input
                            v-model="form.name"
                            type="text"
                            required
                            class="block w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 px-4 py-3 transition-all duration-200"
                            placeholder="Ingresar nombre de la empresa"
                        />
                        <p v-if="errors.name" class="mt-1 text-sm text-red-600">{{ errors.name }}</p>
                    </div>

                    <div>
                        <div class="flex items-center justify-between mb-3">
                            <label class="block text-sm font-medium text-gray-700">
                                <i class="fas fa-envelope mr-2 text-blue-600"></i>
                                Correos Electrónicos
                                <span class="text-red-500 ml-1">*</span>
                            </label>
                            <button
                                @click="addEmail"
                                type="button"
                                class="flex items-center px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-colors text-sm font-medium"
                            >
                                <i class="fas fa-plus mr-2"></i>
                                Agregar Correo
                            </button>
                        </div>
                        <div class="space-y-3">
                            <div v-for="(email, index) in form.emails" :key="index" class="flex gap-2 items-start">
                                <div class="flex-1">
                                    <input
                                        v-model="form.emails[index]"
                                        type="email"
                                        required
                                        class="block w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 px-4 py-2.5 transition-all duration-200"
                                        :placeholder="`Correo ${index + 1}`"
                                    />
                                    <p v-if="errors[`emails.${index}`]" class="mt-1 text-sm text-red-600">
                                        {{ errors[`emails.${index}`] }}
                                    </p>
                                </div>
                                <button
                                    v-if="form.emails.length > 1"
                                    @click="removeEmail(index)"
                                    type="button"
                                    class="px-4 py-2.5 bg-red-500 hover:bg-red-600 text-white rounded-lg transition-colors"
                                    title="Eliminar correo"
                                >
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
                        <router-link
                            :to="{ name: 'companies' }"
                            class="px-6 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-400 focus:ring-offset-2 transition-colors duration-200 font-medium"
                        >
                            Cancelar
                        </router-link>
                        <button
                            type="submit"
                            :disabled="saving"
                            class="px-6 py-2.5 bg-blue-500 text-white rounded-lg hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors duration-200 shadow-sm font-medium disabled:opacity-50"
                        >
                            <i class="fas fa-save mr-2"></i>
                            {{ saving ? 'Actualizando...' : 'Actualizar' }}
                        </button>
                    </div>
                </form>
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

const form = ref({
    name: '',
    emails: ['']
});

const loading = ref(true);
const saving = ref(false);
const errors = ref({});

onMounted(async () => {
    await loadCompany();
});

const loadCompany = async () => {
    loading.value = true;
    try {
        const response = await api.get(`/companies/${route.params.id}`);
        const company = response.data.data;
        form.value = {
            name: company.name,
            emails: company.emails.length > 0 ? company.emails : ['']
        };
    } catch (error) {
        Swal.fire('Error', 'Error al cargar la empresa', 'error');
        router.push({ name: 'companies' });
    } finally {
        loading.value = false;
    }
};

const addEmail = () => {
    form.value.emails.push('');
};

const removeEmail = (index) => {
    if (form.value.emails.length > 1) {
        form.value.emails.splice(index, 1);
    }
};

const updateCompany = async () => {
    saving.value = true;
    errors.value = {};

    try {
        const emails = form.value.emails.filter(e => e.trim() !== '');
        const data = {
            name: form.value.name,
            emails: emails
        };

        await api.post(`/companies/${route.params.id}`, data);
        Swal.fire('Éxito', 'Empresa actualizada correctamente', 'success');
        router.push({ name: 'companies' });
    } catch (error) {
        if (error.response?.status === 422 && error.response?.data?.errors) {
            const e = error.response.data.errors;
            errors.value = Object.fromEntries(
                Object.entries(e).map(([k, v]) => [k, Array.isArray(v) ? v[0] : v])
            );
        }
        const message = error.response?.data?.message || 'Error al actualizar empresa';
        Swal.fire('Error', message, 'error');
    } finally {
        saving.value = false;
    }
};
</script>
