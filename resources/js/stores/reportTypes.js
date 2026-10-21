import { defineStore } from 'pinia';
import { ref } from 'vue';
import api from '../services/api';

/**
 * Catálogo de tipos de reporte (GET /catalogs/report-types).
 * Mientras el catálogo no esté disponible se usan los códigos que ya existen
 * en los datos, para que el comportamiento previo no cambie.
 */
const FALLBACK_TYPES = [
    { code: 'vibration', name: 'Vibraciones', consecutivo_prefix: 'SS', icon: 'fas fa-wave-square', color: '#3b82f6', display_order: 1 },
    { code: 'thermography', name: 'Termografía', consecutivo_prefix: 'TH', icon: 'fas fa-thermometer-half', color: '#f43f5e', display_order: 2 },
];

const DEFAULT_CODE = 'vibration';

export const useReportTypesStore = defineStore('reportTypes', () => {
    const types = ref([...FALLBACK_TYPES]);
    const loaded = ref(false);
    let pending = null;

    function normalize(code) {
        const value = (code ?? '').toString().trim().toLowerCase();
        return types.value.some((type) => type.code === value) ? value : DEFAULT_CODE;
    }

    function byCode(code) {
        const value = normalize(code);
        return types.value.find((type) => type.code === value) || null;
    }

    async function load(force = false) {
        if (loaded.value && !force) return types.value;
        if (pending) return pending;

        pending = api
            .get('/catalogs/report-types')
            .then((response) => {
                const data = response.data?.data ?? response.data;
                if (Array.isArray(data) && data.length > 0) {
                    types.value = data;
                }
                loaded.value = true;
                return types.value;
            })
            .catch((error) => {
                console.error('Error al cargar los tipos de reporte:', error);
                return types.value;
            })
            .finally(() => {
                pending = null;
            });

        return pending;
    }

    return { types, loaded, load, byCode, normalize };
});
