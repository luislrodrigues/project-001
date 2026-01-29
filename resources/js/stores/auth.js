import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import api from '../services/api';

export const useAuthStore = defineStore('auth', () => {
    const user = ref(null);
    const token = ref(localStorage.getItem('token') || null);
    const permissions = ref([]);
    const roles = ref([]);

    const isAuthenticated = computed(() => !!token.value);

    function setToken(newToken) {
        token.value = newToken;
        if (newToken) {
            localStorage.setItem('token', newToken);
            api.defaults.headers.common['Authorization'] = `Bearer ${newToken}`;
        } else {
            localStorage.removeItem('token');
            delete api.defaults.headers.common['Authorization'];
        }
    }

    function setUser(userData) {
        user.value = userData;
        permissions.value = userData.permissions || [];
        roles.value = userData.roles || [];
    }

    async function login(credentials) {
        try {
            const response = await api.post('/login', credentials);
            const payload = response.data.data ?? response.data;
            setToken(payload.token);
            const userData = {
                ...payload.user,
                permissions: payload.permissions || [],
                roles: payload.roles || []
            };
            setUser(userData);
            return { success: true, data: payload };
        } catch (error) {
            const errorMessage = error.response?.data?.message 
                || error.response?.data?.error 
                || (error.response?.data?.errors?.email ? error.response.data.errors.email[0] : null)
                || 'Error al iniciar sesión';
            return {
                success: false,
                message: errorMessage
            };
        }
    }

    async function logout() {
        try {
            await api.post('/logout');
        } catch (error) {
            console.error('Error al cerrar sesión:', error);
        } finally {
            setToken(null);
            user.value = null;
            permissions.value = [];
            roles.value = [];
        }
    }

    function hasPermission(permission) {
        return permissions.value.includes(permission);
    }

    function hasRole(role) {
        return roles.value.includes(role);
    }

    return {
        user,
        token,
        permissions,
        roles,
        isAuthenticated,
        setToken,
        setUser,
        login,
        logout,
        hasPermission,
        hasRole
    };
});
