import { createRouter, createWebHistory } from 'vue-router';
import { useAuthStore } from '../stores/auth';

const routes = [
    {
        path: '/login',
        name: 'login',
        component: () => import('../pages/Login.vue'),
        meta: { requiresGuest: true }
    },
    {
        path: '/forgot-password',
        name: 'forgot-password',
        component: () => import('../pages/ForgotPassword.vue'),
        meta: { requiresGuest: true }
    },
    {
        path: '/reset-password/:token',
        name: 'reset-password',
        component: () => import('../pages/ResetPassword.vue'),
        meta: { requiresGuest: true }
    },
    {
        path: '/',
        component: () => import('../layouts/AppLayout.vue'),
        meta: { requiresAuth: true },
        children: [
            {
                path: '',
                name: 'dashboard',
                component: () => import('../pages/Dashboard.vue')
            },
            {
                path: 'companies',
                name: 'companies',
                component: () => import('../pages/Companies.vue')
            },
            {
                path: 'companies/create',
                name: 'companies.create',
                component: () => import('../pages/companies/CompanyCreate.vue')
            },
            {
                path: 'companies/:id/edit',
                name: 'companies.edit',
                component: () => import('../pages/companies/CompanyEdit.vue'),
                props: true
            },
            {
                path: 'companies/:id',
                name: 'companies.show',
                component: () => import('../pages/companies/CompanyShow.vue'),
                props: true
            },
            {
                path: 'equipment',
                name: 'equipment',
                component: () => import('../pages/Equipment.vue')
            },
            {
                path: 'equipment/create',
                name: 'equipment.create',
                component: () => import('../pages/equipment/EquipmentCreate.vue')
            },
            {
                path: 'equipment/:id/edit',
                name: 'equipment.edit',
                component: () => import('../pages/equipment/EquipmentEdit.vue'),
                props: true
            },
            {
                path: 'equipment/:id',
                name: 'equipment.show',
                component: () => import('../pages/equipment/EquipmentShow.vue'),
                props: true
            },
            {
                path: 'reports',
                name: 'reports',
                component: () => import('../pages/Reports.vue')
            },
            {
                path: 'reports/create',
                name: 'reports.create',
                component: () => import('../pages/reports/ReportCreate.vue')
            },
            {
                path: 'reports/:id/edit',
                name: 'reports.edit',
                component: () => import('../pages/reports/ReportEdit.vue'),
                props: true
            },
            {
                path: 'reports/:id',
                name: 'reports.show',
                component: () => import('../pages/reports/ReportShow.vue'),
                props: true
            },
            // {
            //     path: 'components',
            //     name: 'components',
            //     component: () => import('../pages/Components.vue')
            // },
            // {
            //     path: 'components/create',
            //     name: 'components.create',
            //     component: () => import('../pages/components/ComponentCreate.vue')
            // },
            // {
            //     path: 'components/:id/edit',
            //     name: 'components.edit',
            //     component: () => import('../pages/components/ComponentEdit.vue'),
            //     props: true
            // },
            // {
            //     path: 'components/:id',
            //     name: 'components.show',
            //     component: () => import('../pages/components/ComponentShow.vue'),
            //     props: true
            // },
            {
                path: 'component-types',
                name: 'component-types',
                component: () => import('../pages/ComponentTypes.vue')
            },
            {
                path: 'component-types/create',
                name: 'component-types.create',
                component: () => import('../pages/component-types/ComponentTypeCreate.vue')
            },
            {
                path: 'component-types/:id/edit',
                name: 'component-types.edit',
                component: () => import('../pages/component-types/ComponentTypeEdit.vue'),
                props: true
            },
            {
                path: 'component-types/:id',
                name: 'component-types.show',
                component: () => import('../pages/component-types/ComponentTypeShow.vue'),
                props: true
            },
            {
                path: 'users',
                name: 'users',
                component: () => import('../pages/Users.vue')
            },
            {
                path: 'users/create',
                name: 'users.create',
                component: () => import('../pages/users/UserCreate.vue')
            },
            {
                path: 'users/:id/edit',
                name: 'users.edit',
                component: () => import('../pages/users/UserEdit.vue'),
                props: true
            },
            {
                path: 'users/:id',
                name: 'users.show',
                component: () => import('../pages/users/UserShow.vue'),
                props: true
            },
            {
                path: 'profile',
                name: 'profile',
                component: () => import('../pages/Profile.vue')
            },
            {
                path: '/:pathMatch(.*)*',
                name: 'not-found',
                component: () => import('../pages/NotFound.vue')
            }
        ]
    }
];

const router = createRouter({
    history: createWebHistory(),
    routes
});

router.beforeEach(async (to, from, next) => {
    const authStore = useAuthStore();

    if (to.meta.requiresAuth && !authStore.isAuthenticated) {
        next({ name: 'login' });
        return;
    }

    if (to.meta.requiresGuest && authStore.isAuthenticated) {
        next({ name: 'dashboard' });
        return;
    }

    next();
});

export default router;
