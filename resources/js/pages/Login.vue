<template>
    <div
        class="relative min-h-screen w-full flex flex-col justify-center items-center"
        :style="{ backgroundImage: `url('/images/login-1.jpg')`, backgroundSize: 'cover', backgroundPosition: 'center', backgroundRepeat: 'no-repeat' }"
    >
        <div class="relative w-full flex flex-col justify-center items-center min-h-screen">
            <div class="w-full max-w-md min-h-[600px] flex flex-col justify-center items-center border-2 border-white shadow-2xl rounded-2xl bg-white p-8">
                <div class="px-6 py-8 w-full">
                    <div class="flex justify-center mb-12 mt-2">
                        <img src="/images/logo-2.jpg" alt="Logo" width="400px">
                    </div>
                    <form class="mt-8 space-y-6" @submit.prevent="handleLogin">
                        <div class="rounded-md shadow-sm space-y-6">
                            <div v-if="error" class="mb-6">
                                <span class="text-red-500">{{ error }}</span>
                            </div>
                            <div class="mb-6">
                                <label for="email-address" class="sr-only">Email address</label>
                                <input
                                    id="email-address"
                                    v-model="form.email"
                                    name="email"
                                    type="email"
                                    autocomplete="email"
                                    required
                                    class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-t-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm"
                                    placeholder="Correo"
                                />
                            </div>
                            <div class="relative">
                                <div class="relative">
                                    <label for="password" class="sr-only">Password</label>
                                    <input
                                        id="password"
                                        v-model="form.password"
                                        name="password"
                                        type="password"
                                        autocomplete="current-password"
                                        required
                                        class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-b-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm"
                                        placeholder="Contraseña"
                                    />
                                </div>
                            </div>
                        </div>

                        <div>
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
                                    Iniciando sesión...
                                </span>
                                <span v-else>Iniciar Sesión</span>
                            </button>
                            <router-link
                                to="/forgot-password"
                                class="block w-full text-center mt-3 text-sm text-indigo-600 hover:text-indigo-800 bg-transparent"
                            >
                                ¿Olvidaste tu contraseña?
                            </router-link>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '../stores/auth';

const router = useRouter();
const authStore = useAuthStore();

const form = ref({
    email: '',
    password: ''
});

const loading = ref(false);
const error = ref('');

const handleLogin = async () => {
    loading.value = true;
    error.value = '';

    const result = await authStore.login(form.value);

    if (result.success) {
        router.push('/');
    } else {
        error.value = result.message || 'Error al iniciar sesión';
    }

    loading.value = false;
};
</script>
