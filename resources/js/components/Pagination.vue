<template>
    <div v-if="pagination && pagination.last_page > 0" class="mt-4 flex flex-col sm:flex-row items-center justify-between gap-4">
        <p class="text-sm text-gray-600">
            Mostrando <span class="font-medium text-gray-900">{{ pagination.from ?? 0 }}</span>
            - <span class="font-medium text-gray-900">{{ pagination.to ?? 0 }}</span>
            de <span class="font-medium text-gray-900">{{ pagination.total ?? 0 }}</span>
            resultados
        </p>
        <div class="flex items-center gap-1">
            <button
                :disabled="pagination.current_page <= 1"
                @click="$emit('page-change', pagination.current_page - 1)"
                class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-gray-600 hover:bg-gray-100 hover:text-gray-900 disabled:opacity-40 disabled:cursor-not-allowed disabled:hover:bg-transparent transition-colors"
                title="Anterior"
            >
                <i class="fas fa-chevron-left text-sm"></i>
            </button>
            <template v-for="page in pageNumbers" :key="page">
                <span v-if="page === '...'" class="px-2 text-gray-400">…</span>
                <button
                    v-else
                    @click="$emit('page-change', page)"
                    :class="[
                        'inline-flex items-center justify-center min-w-[36px] h-9 px-2 rounded-lg text-sm font-medium transition-colors',
                        page === pagination.current_page
                            ? 'bg-blue-600 text-white shadow-sm'
                            : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900'
                    ]"
                >
                    {{ page }}
                </button>
            </template>
            <button
                :disabled="pagination.current_page >= pagination.last_page"
                @click="$emit('page-change', pagination.current_page + 1)"
                class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-gray-600 hover:bg-gray-100 hover:text-gray-900 disabled:opacity-40 disabled:cursor-not-allowed disabled:hover:bg-transparent transition-colors"
                title="Siguiente"
            >
                <i class="fas fa-chevron-right text-sm"></i>
            </button>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    pagination: {
        type: Object,
        default: null,
    },
});

defineEmits(['page-change']);

const pageNumbers = computed(() => {
    const p = props.pagination;
    if (!p || !p.last_page) return [];
    const current = p.current_page;
    const last = p.last_page;
    if (last <= 1) return [1];
    const delta = 1;

    const range = [];
    const rangeWithDots = [];

    for (let i = Math.max(2, current - delta); i <= Math.min(last - 1, current + delta); i++) {
        range.push(i);
    }

    if (current - delta > 2) {
        rangeWithDots.push(1, '...');
    } else {
        rangeWithDots.push(1);
    }

    rangeWithDots.push(...range);

    if (current + delta < last - 1) {
        rangeWithDots.push('...', last);
    } else if (last > 1 && !range.includes(last)) {
        rangeWithDots.push(last);
    }

    return rangeWithDots;
});
</script>
