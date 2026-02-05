<template>
    <div class="flex flex-col items-center justify-center min-h-[60vh] px-4">
        <div class="text-center max-w-md">
            <div class="mb-6">
                <i class="fas fa-exclamation-triangle text-6xl text-amber-500"></i>
            </div>
            <h1 class="text-3xl font-bold text-gray-800 mb-2">Página no encontrada</h1>
            <p class="text-gray-600 mb-8">
                La ruta a la que intentas acceder no existe o no está disponible.
            </p>
            <router-link
                :to="{ name: 'dashboard' }"
                class="inline-flex items-center gap-2 px-6 py-3 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors"
            >
                <i class="fas fa-home"></i>
                Volver al inicio
            </router-link>
        </div>
    </div>
</template>
