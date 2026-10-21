<template>
    <div class="container mx-auto px-4 sm:px-8">
        <div class="py-6">
            <div class="mb-6">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div>
                        <h2 class="text-3xl font-bold text-gray-800 flex items-center">
                            <i class="fas fa-thermometer-half text-rose-600 mr-3"></i>
                            Reportes de Termografía
                        </h2>
                        <p class="text-gray-500 text-sm mt-1">Listado de informes térmicos</p>
                    </div>
                    <router-link
                        :to="{ name: 'reports' }"
                        class="w-10 h-10 flex items-center justify-center rounded-lg bg-gray-100 text-gray-700 hover:bg-gray-200 transition-colors"
                        title="Volver"
                    >
                        <i class="fas fa-arrow-left"></i>
                    </router-link>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="text-gray-600">
                    Próximamente.
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
</script>
