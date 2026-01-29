<template>
    <div class="container mx-auto px-4 sm:px-8">
        <div class="py-6">
            <!-- Header -->
            <div class="mb-6">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h2 class="text-3xl font-bold text-gray-800 flex items-center">
                            <i class="fas fa-building text-blue-600 mr-3"></i>
                            Crear Nueva Empresa
                        </h2>
                        <p class="text-gray-500 text-sm mt-1">Completa el formulario para crear una nueva empresa</p>
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
                <form @submit.prevent="saveCompany" class="space-y-6">
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
                            :disabled="loading"
                            class="px-6 py-2.5 bg-blue-500 text-white rounded-lg hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors duration-200 shadow-sm font-medium disabled:opacity-50"
                        >
                            <i class="fas fa-save mr-2"></i>
                            {{ loading ? 'Guardando...' : 'Guardar' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '../../stores/auth';
import api from '../../services/api';
import Swal from 'sweetalert2';

const router = useRouter();
const authStore = useAuthStore();

const form = ref({
    name: '',
    emails: ['']
});

const loading = ref(false);
const errors = ref({});

const addEmail = () => {
    form.value.emails.push('');
};

const removeEmail = (index) => {
    if (form.value.emails.length > 1) {
        form.value.emails.splice(index, 1);
    }
};

const saveCompany = async () => {
    loading.value = true;
    errors.value = {};

    try {
        const emails = form.value.emails.filter(e => e.trim() !== '');
        const data = {
            name: form.value.name,
            emails: emails
        };

        await api.post('/companies', data);
        Swal.fire('Éxito', 'Empresa creada correctamente', 'success');
        router.push({ name: 'companies' });
    } catch (error) {
        if (error.response?.status === 422 && error.response?.data?.errors) {
            const e = error.response.data.errors;
            errors.value = Object.fromEntries(
                Object.entries(e).map(([k, v]) => [k, Array.isArray(v) ? v[0] : v])
            );
        }
        const message = error.response?.data?.message || 'Error al guardar empresa';
        Swal.fire('Error', message, 'error');
    } finally {
        loading.value = false;
    }
};
</script>
