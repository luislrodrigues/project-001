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
    const token = localStorage.getItem('token');
    if (token) {
        authStore.setToken(token);
        try {
            const response = await api.get('/user');
            const payload = response.data.data ?? response.data;
            const userData = {
                ...payload,
                permissions: payload.permissions || [],
                roles: payload.roles || []
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
</style>
