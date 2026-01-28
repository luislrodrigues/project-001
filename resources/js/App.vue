<template>
    <div id="app">
        <router-view />
    </div>
</template>

<script setup>
import { onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from './stores/auth';
import api from './services/api';

const authStore = useAuthStore();
const router = useRouter();

onMounted(async () => {
    // Verificar si hay token guardado
    const token = localStorage.getItem('token');
    if (token) {
        authStore.setToken(token);
        // Intentar obtener el usuario actual para verificar que el token es válido
        try {
            const response = await api.get('/user');
            const userData = {
                ...response.data,
                permissions: response.data.permissions || [],
                roles: response.data.roles || []
            };
            authStore.setUser(userData);
        } catch (error) {
            // Token inválido, limpiar
            authStore.logout();
            if (router.currentRoute.value.meta.requiresAuth) {
                router.push('/login');
            }
        }
    }
});
</script>

<style>
/* Estilos globales si es necesario */
</style>
