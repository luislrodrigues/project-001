<template>
    <div
        class="relative min-h-screen w-full flex flex-col justify-center items-center"
        :style="{ backgroundImage: `url('/images/login-1.jpg')`, backgroundSize: 'cover', backgroundPosition: 'center', backgroundRepeat: 'no-repeat' }"
    >
        <div class="relative w-full flex flex-col justify-center items-center min-h-screen">
            <div class="w-full max-w-md min-h-[500px] flex flex-col justify-center items-center border-2 border-white shadow-2xl rounded-2xl bg-white p-8">
                <div class="px-6 py-8 w-full">
                    <div class="flex justify-center mb-12 mt-2">
                        <img src="/images/logo-2.jpg" alt="Logo" width="400px">
                    </div>
                    <form v-if="!sent" class="mt-8 space-y-6" @submit.prevent="handleSubmit">
                        <h2 class="text-xl font-semibold text-gray-800 text-center mb-4">Recuperar contraseña</h2>
                        <p class="text-sm text-gray-600 text-center mb-6">
                            Ingresa tu correo y te enviaremos un enlace para restablecer tu contraseña.
                        </p>
                        <div class="rounded-md shadow-sm space-y-6">
                            <div class="mb-6">
                                <label for="email" class="sr-only">Correo electrónico</label>
                                <input
                                    id="email"
                                    v-model="form.email"
                                    name="email"
                                    type="email"
                                    autocomplete="email"
                                    required
                                    class="appearance-none rounded relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm"
                                    placeholder="Correo electrónico"
                                />
                            </div>
                        </div>
                        <div class="space-y-4">
                            <button
                                type="submit"
                                :disabled="loading"
                                class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50"
                            >
                                <span v-if="loading" class="flex items-center">
                                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Enviando...
                                </span>
                                <span v-else>Enviar enlace</span>
                            </button>
                            <router-link
                                to="/login"
                                class="block text-center text-sm text-indigo-600 hover:text-indigo-800"
                            >
                                Volver al inicio de sesión
                            </router-link>
                        </div>
                    </form>
                    <div v-else class="mt-8 space-y-6 text-center">
                        <div class="rounded-lg bg-green-50 border border-green-200 p-6">
                            <i class="fas fa-check-circle text-4xl text-green-600 mb-4"></i>
                            <h2 class="text-lg font-semibold text-gray-800 mb-2">Correo enviado</h2>
                            <p class="text-sm text-gray-600 mb-4">
                                Se ha enviado un enlace a tu correo para restablecer tu contraseña. Revisa tu bandeja de entrada y la carpeta de spam.
                            </p>
                        </div>
                        <router-link
                            to="/login"
                            class="inline-flex justify-center w-full py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700"
                        >
                            Volver al inicio de sesión
                        </router-link>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import Swal from 'sweetalert2';
import api from '../services/api';

const form = ref({ email: '' });
const loading = ref(false);
const sent = ref(false);

const handleSubmit = async () => {
    loading.value = true;

    try {
        const { data } = await api.post('/forgot-password', form.value);
        if (data.success) {
            sent.value = true;
        } else {
            Swal.fire('Error', data.message || 'Error al enviar el correo', 'error');
        }
    } catch (e) {
        Swal.fire('Error', e.response?.data?.message || 'Error al enviar el correo', 'error');
    } finally {
        loading.value = false;
    }
};
</script>
