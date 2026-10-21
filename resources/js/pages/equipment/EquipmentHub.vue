<template>
    <div class="container mx-auto px-4 sm:px-8">
        <div class="py-6">
            <div class="mb-6">
                <h2 class="text-3xl font-bold text-gray-800 flex items-center">
                    <i class="fas fa-toolbox text-green-600 mr-3"></i>
                    Equipos
                </h2>
                <p class="text-gray-500 text-sm mt-1">Selecciona el tipo de equipos</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <router-link
                    v-for="type in reportTypesStore.types"
                    :key="type.code"
                    :to="{ name: `equipment.${type.code}` }"
                    class="group"
                >
                    <div class="bg-white rounded-xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 border border-gray-100 overflow-hidden">
                        <div class="h-2" :class="styleFor(type.code).bar"></div>
                        <div class="p-6">
                            <div class="flex items-center justify-between mb-4">
                                <div class="w-14 h-14 rounded-xl flex items-center justify-center transition-colors duration-300" :class="[styleFor(type.code).iconBg, styleFor(type.code).iconBgHover]">
                                    <i class="text-2xl transition-colors duration-300" :class="[type.icon, styleFor(type.code).iconText, styleFor(type.code).iconTextHover]"></i>
                                </div>
                                <i class="fas fa-arrow-right text-gray-400 transform group-hover:translate-x-2 transition-all duration-300" :class="styleFor(type.code).arrowHover"></i>
                            </div>
                            <h3 class="text-xl font-bold text-gray-800 mb-2">Equipos de {{ type.name }}</h3>
                            <p class="text-gray-600 text-sm">Gestiona y visualiza los equipos para {{ type.name.toLowerCase() }}</p>
                        </div>
                    </div>
                </router-link>
            </div>
        </div>
    </div>
</template>

<script setup>
import { onMounted } from 'vue';
import { useReportTypesStore } from '../../stores/reportTypes';

const reportTypesStore = useReportTypesStore();

const cardStyles = {
    vibration: {
        bar: 'bg-gradient-to-r from-green-500 to-emerald-600',
        iconBg: 'bg-green-100',
        iconBgHover: 'group-hover:bg-green-500',
        iconText: 'text-green-600',
        iconTextHover: 'group-hover:text-white',
        arrowHover: 'group-hover:text-green-500',
    },
    thermography: {
        bar: 'bg-gradient-to-r from-rose-500 to-pink-600',
        iconBg: 'bg-rose-100',
        iconBgHover: 'group-hover:bg-rose-500',
        iconText: 'text-rose-600',
        iconTextHover: 'group-hover:text-white',
        arrowHover: 'group-hover:text-rose-500',
    },
    default: {
        bar: 'bg-gradient-to-r from-slate-400 to-slate-500',
        iconBg: 'bg-slate-100',
        iconBgHover: 'group-hover:bg-slate-500',
        iconText: 'text-slate-600',
        iconTextHover: 'group-hover:text-white',
        arrowHover: 'group-hover:text-slate-500',
    },
};

const styleFor = (code) => cardStyles[code] || cardStyles.default;

onMounted(() => {
    reportTypesStore.load();
});
</script>