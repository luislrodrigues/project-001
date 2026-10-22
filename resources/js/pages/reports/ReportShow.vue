<template>
    <div class="container mx-auto px-4 sm:px-8 relative">
        <!-- Modal para ampliar imagen (sin Teleport para evitar problemas en reportes) -->
        <Transition name="fade">
            <div
                v-if="imageModalUrl"
                class="fixed inset-0 z-[99999] flex items-center justify-center bg-black/90 p-4"
                style="position: fixed; top: 0; left: 0; right: 0; bottom: 0;"
                @click.self="closeImageModal"
            >
                <button
                    class="absolute top-4 right-4 z-10 w-10 h-10 flex items-center justify-center rounded-full bg-white/20 hover:bg-white/30 text-white text-xl"
                    @click="closeImageModal"
                    aria-label="Cerrar"
                >
                    <i class="fas fa-times"></i>
                </button>
                <img
                    v-if="imageModalUrl"
                    :src="imageModalUrl"
                    alt="Imagen ampliada"
                    class="max-w-[95vw] max-h-[90vh] w-auto h-auto object-contain"
                    @click.stop
                />
            </div>
        </Transition>
        <!-- Loading overlay para carga inicial -->
        <div v-if="loading" class="fixed top-0 left-0 w-screen h-screen flex items-center justify-center z-50 bg-black bg-opacity-30 backdrop-blur-sm">
            <div class="flex flex-col items-center justify-center h-full">
                <svg class="animate-spin h-16 w-16 text-blue-600 mr-3 mb-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="10" stroke-opacity="0.25" stroke-width="2" stroke="currentColor" fill="none"></circle>
                    <path d="M12 2a10 10 0 00-9.5 7.5M12 2c2.5 0 5 1 7 2.5" stroke="currentColor" stroke-width="2" fill="none"></path>
                </svg>
                <p class="text-gray-700 font-medium">Cargando...</p>
            </div>
        </div>

        <!-- Loading overlay para generación de PDF -->
        <div v-if="downloadingPdf" class="fixed top-0 left-0 w-screen h-screen flex items-center justify-center z-50 bg-black bg-opacity-30 backdrop-blur-sm">
            <div class="flex flex-col items-center justify-center h-full">
                <svg class="animate-spin h-16 w-16 text-green-600 mr-3 mb-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="10" stroke-opacity="0.25" stroke-width="2" stroke="currentColor" fill="none"></circle>
                    <path d="M12 2a10 10 0 00-9.5 7.5M12 2c2.5 0 5 1 7 2.5" stroke="currentColor" stroke-width="2" fill="none"></path>
                </svg>
                <p class="text-gray-700 font-medium">Generando PDF...</p>
            </div>
        </div>

        <div v-else-if="report" class="py-6">
            <!-- Header -->
            <div class="mb-6">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h2 class="text-3xl font-bold text-gray-800 flex items-center">
                            <i class="fas fa-file-alt text-blue-600 mr-3"></i>
                            Detalle del Reporte
                        </h2>
                        <p class="text-gray-500 text-sm mt-1">Información completa del reporte</p>
                    </div>
                    <div class="flex items-center space-x-3">
                        <router-link
                            v-if="authStore.hasPermission('edit report')"
                            :to="{ name: 'reports.edit', params: { id: report.id }, query: { report_type: reportTypeResolved } }"
                            class="flex items-center px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors"
                        >
                            <i class="fas fa-edit mr-2"></i>
                            Editar
                        </router-link>
                        <button
                            v-if="authStore.hasPermission('download pdf report')"
                            type="button"
                            @click.prevent="downloadPdf"
                            :disabled="downloadingPdf"
                            class="flex items-center px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            <i v-if="!downloadingPdf" class="fas fa-download mr-2"></i>
                            <svg v-else class="animate-spin h-4 w-4 mr-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                                <circle cx="12" cy="12" r="10" stroke-opacity="0.25" stroke-width="2" stroke="currentColor" fill="none"></circle>
                                <path d="M12 2a10 10 0 00-9.5 7.5M12 2c2.5 0 5 1 7 2.5" stroke="currentColor" stroke-width="2" fill="none"></path>
                            </svg>
                            {{ downloadingPdf ? 'Generando...' : 'PDF' }}
                        </button>
                        <button
                            v-if="authStore.hasPermission('send email report')"
                            type="button"
                            @click.prevent="openSendEmailModal"
                            :disabled="sendingEmail"
                            class="flex items-center px-4 py-2 bg-purple-500 text-white rounded-lg hover:bg-purple-600 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            <i v-if="!sendingEmail" class="fas fa-envelope mr-2"></i>
                            <svg v-else class="animate-spin h-4 w-4 mr-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                                <circle cx="12" cy="12" r="10" stroke-opacity="0.25" stroke-width="2" stroke="currentColor" fill="none"></circle>
                                <path d="M12 2a10 10 0 00-9.5 7.5M12 2c2.5 0 5 1 7 2.5" stroke="currentColor" stroke-width="2" fill="none"></path>
                            </svg>
                            {{ sendingEmail ? 'Enviando...' : 'Enviar correo' }}
                        </button>
                        <router-link
                            :to="{ name: backRouteName }"
                            class="text-gray-600 hover:text-gray-900 p-2 hover:bg-gray-100 rounded-lg transition-colors"
                            title="Volver"
                        >
                            <i class="fas fa-arrow-left text-xl"></i>
                        </router-link>
                    </div>
                </div>
            </div>

            <!-- Content -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <!-- Información General -->
                <div class="mb-8">
                    <div class="flex items-center mb-4 pb-2 border-b border-gray-200">
                        <i class="fas fa-info-circle text-blue-600 mr-2"></i>
                        <h4 class="text-lg font-semibold text-gray-800">Información General</h4>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <div class="bg-gray-50 rounded-lg p-4">
                            <label class="flex items-center mb-2 text-sm font-medium text-gray-700">
                                <i class="fas fa-hashtag mr-2 text-blue-600"></i>
                                Informe
                            </label>
                            <div class="text-sm font-semibold text-gray-900 bg-white px-4 py-3 rounded-lg border border-gray-200">
                                {{ report.consecutivo ?? 'N/A' }}
                            </div>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <label class="flex items-center mb-2 text-sm font-medium text-gray-700">
                                <i class="fas fa-building mr-2 text-blue-600"></i>
                                Empresa
                            </label>
                            <div class="text-sm font-semibold text-gray-900 bg-white px-4 py-3 rounded-lg border border-gray-200">
                                {{ report.equipment?.company?.name || 'N/A' }}
                            </div>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <label class="flex items-center mb-2 text-sm font-medium text-gray-700">
                                <i class="fas fa-cog mr-2 text-blue-600"></i>
                                Equipo
                            </label>
                            <div class="text-sm font-semibold text-gray-900 bg-white px-4 py-3 rounded-lg border border-gray-200">
                                {{ report.equipment?.name }}
                            </div>
                        </div>

                        <div class="bg-gray-50 rounded-lg p-4">
                            <label class="flex items-center mb-2 text-sm font-medium text-gray-700">
                                <i class="fas fa-tag mr-2 text-blue-600"></i>
                                Tipo Equipo
                            </label>
                            <div class="text-sm font-semibold text-gray-900 bg-white px-4 py-3 rounded-lg border border-gray-200">
                                {{ report.equipment?.equipment_type || 'N/A' }}
                            </div>
                        </div>

                        <div class="bg-gray-50 rounded-lg p-4">
                            <label class="flex items-center mb-2 text-sm font-medium text-gray-700">
                                <i class="fas fa-calendar mr-2 text-blue-600"></i>
                                Fecha Medición
                            </label>
                            <div class="text-sm font-semibold text-gray-900 bg-white px-4 py-3 rounded-lg border border-gray-200">
                                {{ formatDate(report.measurement_date) }}
                            </div>
                        </div>

                        <div class="bg-gray-50 rounded-lg p-4">
                            <label class="flex items-center mb-2 text-sm font-medium text-gray-700">
                                <i class="fas fa-map-marker-alt mr-2 text-blue-600"></i>
                                Ubicación
                            </label>
                            <div class="text-sm font-semibold text-gray-900 bg-white px-4 py-3 rounded-lg border border-gray-200">
                                {{ report.equipment?.location }}
                            </div>
                        </div>

                        <div class="bg-gray-50 rounded-lg p-4">
                            <label class="flex items-center mb-2 text-sm font-medium text-gray-700">
                                <i class="fas fa-tasks mr-2 text-blue-600"></i>
                                Proceso
                            </label>
                            <div class="text-sm font-semibold text-gray-900 bg-white px-4 py-3 rounded-lg border border-gray-200">
                                {{ report.equipment?.process }}
                            </div>
                        </div>

                        <div class="bg-gray-50 rounded-lg p-4">
                            <label class="flex items-center mb-2 text-sm font-medium text-gray-700">
                                <i class="fas fa-user mr-2 text-blue-600"></i>
                                Analista
                            </label>
                            <div class="text-sm font-semibold text-gray-900 bg-white px-4 py-3 rounded-lg border border-gray-200">
                                {{ report.analist?.name }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Datos de la medición (solo termografía) -->
                <div v-if="report.report_type === 'thermography'" class="mb-8">
                    <div class="flex items-center mb-4 pb-2 border-b border-gray-200">
                        <i class="fas fa-thermometer-half text-blue-600 mr-2"></i>
                        <h4 class="text-lg font-semibold text-gray-800">Datos de la medición</h4>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="bg-gray-50 rounded-lg p-4">
                            <label class="flex items-center mb-2 text-sm font-medium text-gray-700">
                                <i class="fas fa-adjust mr-2 text-blue-600"></i>
                                Emisividad
                            </label>
                            <div class="text-sm font-semibold text-gray-900 bg-white px-4 py-3 rounded-lg border border-gray-200">
                                {{ report.emissivity ?? '—' }}
                            </div>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <label class="flex items-center mb-2 text-sm font-medium text-gray-700">
                                <i class="fas fa-ruler-horizontal mr-2 text-blue-600"></i>
                                Distancia Medición (m)
                            </label>
                            <div class="text-sm font-semibold text-gray-900 bg-white px-4 py-3 rounded-lg border border-gray-200">
                                {{ report.distance_m ?? '—' }}
                            </div>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <label class="flex items-center mb-2 text-sm font-medium text-gray-700">
                                <i class="fas fa-temperature-low mr-2 text-blue-600"></i>
                                Temperatura Ambiente (°C)
                            </label>
                            <div class="text-sm font-semibold text-gray-900 bg-white px-4 py-3 rounded-lg border border-gray-200">
                                {{ report.ambient_temp ?? '—' }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Diagnósticos y Recomendaciones -->
                <div v-if="report.components && report.components.length > 0" class="mb-8">
                    <div class="flex items-center mb-4 pb-2 border-b border-gray-200">
                        <i class="fas fa-stethoscope text-blue-600 mr-2"></i>
                        <h4 class="text-lg font-semibold text-gray-800">Diagnósticos y Recomendaciones</h4>
                    </div>
                    <div class="space-y-6">
                        <div
                            v-for="(component, index) in report.components"
                            :key="component.id"
                            class="bg-gray-50 rounded-lg p-5 border border-gray-200"
                        >
                            <div class="flex items-center mb-4">
                                <div class="bg-blue-100 rounded-full w-8 h-8 flex items-center justify-center mr-3">
                                    <span class="text-blue-600 font-semibold text-sm">{{ index + 1 }}</span>
                                </div>
                                <h5 class="text-md font-semibold text-gray-800">
                                    {{ component.component_type?.name || 'Componente' }}
                                </h5>
                            </div>

                            <!-- Parámetros de la medición -->
                            <div v-if="getMeasurementValues(component.id).length" class="mb-4">
                                <label class="flex items-center mb-2 text-sm font-medium text-gray-700">
                                    <i class="fas fa-thermometer-half mr-2 text-rose-600"></i>
                                    Parámetros de la medición
                                </label>
                                <div class="bg-white border border-gray-300 rounded-lg p-4">
                                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                                        <div v-for="item in getMeasurementValues(component.id)" :key="item.field_id" class="flex items-baseline gap-2 text-sm">
                                            <span class="text-gray-500">{{ item.name }}:</span>
                                            <span class="font-semibold text-gray-800">
                                                {{ item.value }}<span v-if="item.unit" class="text-gray-500 font-normal"> {{ item.unit }}</span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="flex items-center mb-2 text-sm font-medium text-gray-700">
                                    <i class="fas fa-clipboard-list mr-2 text-blue-600"></i>
                                    Diagnóstico
                                </label>
                                <div
                                    class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg block w-full px-4 py-3 min-h-[100px] rte-view leading-relaxed"
                                    v-html="getDiagnosisHtml(component.id) || 'Sin diagnóstico'"
                                ></div>
                            </div>

                            <!-- Imágenes del Componente -->
                            <div v-if="getComponentImages(component.id)?.length" class="mb-4">
                                <label class="flex items-center mb-3 text-sm font-medium text-gray-700">
                                    <i class="fas fa-image mr-2 text-purple-600"></i>
                                    Imágenes ({{ getComponentImages(component.id).length }})
                                </label>
                                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
                                    <div
                                        v-for="(image, imgIndex) in getComponentImages(component.id)"
                                        :key="imgIndex"
                                        class="relative group cursor-pointer"
                                        role="button"
                                        tabindex="0"
                                        @click="openImageModal(image.url)"
                                        @keydown.enter="openImageModal(image.url)"
                                    >
                                        <img
                                            :src="image.url"
                                            :alt="image.caption || `Imagen ${imgIndex + 1}`"
                                            class="w-full h-32 object-contain rounded-lg border-2 border-gray-200 hover:border-purple-500 transition-all bg-gray-100 pointer-events-none"
                                        />
                                        <span
                                            class="absolute top-1 left-1 px-2 py-0.5 rounded-full text-[10px] font-semibold text-white pointer-events-none"
                                            :class="image.kind === 'visual' ? 'bg-blue-600' : 'bg-purple-600'"
                                        >{{ image.kindLabel }}</span>
                                        <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-30 transition-all rounded-lg flex items-center justify-center">
                                            <i class="fas fa-search-plus text-white opacity-0 group-hover:opacity-100 text-xl"></i>
                                        </div>
                                        <p v-if="image.caption" class="text-xs text-gray-600 mt-1 truncate">{{ image.caption }}</p>
                                    </div>
                                </div>
                            </div>

                            <div v-if="getRecommendations(component.id).length">
                                <label class="flex items-center mb-3 text-sm font-medium text-gray-700">
                                    <i class="fas fa-lightbulb mr-2 text-yellow-600"></i>
                                    Recomendaciones ({{ getRecommendations(component.id).length }})
                                </label>
                                <div class="space-y-2">
                                    <div
                                        v-for="(rec, recIndex) in getRecommendations(component.id)"
                                        :key="recIndex"
                                        class="bg-white rounded-lg p-3 border border-gray-200"
                                    >
                                        <div class="flex items-start">
                                            <span class="flex-shrink-0 bg-yellow-100 text-yellow-800 rounded-full w-6 h-6 flex items-center justify-center text-xs font-semibold mr-3 mt-0.5">
                                                {{ recIndex + 1 }}
                                            </span>
                                            <div class="text-gray-900 text-sm rte-view leading-relaxed" v-html="sanitizeHtml(rec)"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Condición, Mecanismo Falla, Componente Falla, Prioridad, OT Creada, Estado de OT (al final de todo) -->
                <div class="mb-8">
                    <div class="flex items-center mb-4 pb-2 border-b border-gray-200">
                        <i class="fas fa-list mr-2 text-blue-600"></i>
                        <h4 class="text-lg font-semibold text-gray-800">Otros datos</h4>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <div class="bg-gray-50 rounded-lg p-4">
                            <label class="flex items-center mb-2 text-sm font-medium text-gray-700">
                                <i class="fas fa-exclamation-triangle mr-2 text-blue-600"></i>
                                Condición
                            </label>
                            <div class="text-sm font-semibold text-gray-900 bg-white px-4 py-3 rounded-lg border border-gray-200">
                                <span
                                    :class="!report.condition?.color ? getConditionClass() : ''"
                                    :style="report.condition ? (report.condition.color ? { backgroundColor: report.condition.color, color: report.condition.text_color || '#000', borderColor: report.condition.color } : (report.condition.text_color ? { color: report.condition.text_color } : {})) : {}"
                                    class="px-3 py-1 inline-flex text-xs font-semibold rounded-full border"
                                >
                                    {{ report.condition?.name }}
                                </span>
                            </div>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <label class="flex items-center mb-2 text-sm font-medium text-gray-700">
                                <i class="fas fa-wrench mr-2 text-blue-600"></i>
                                Mecanismo Falla
                            </label>
                            <div class="text-sm font-semibold text-gray-900 bg-white px-4 py-3 rounded-lg border border-gray-200">
                                {{ report.mechanism_fail?.name || 'N/A' }}
                            </div>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <label class="flex items-center mb-2 text-sm font-medium text-gray-700">
                                <i class="fas fa-cog mr-2 text-blue-600"></i>
                                Componente Falla
                            </label>
                            <div class="text-sm font-semibold text-gray-900 bg-white px-4 py-3 rounded-lg border border-gray-200">
                                {{ report.component_fail?.name || 'N/A' }}
                            </div>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <label class="flex items-center mb-2 text-sm font-medium text-gray-700">
                                <i class="fas fa-flag mr-2 text-blue-600"></i>
                                Prioridad
                            </label>
                            <div class="text-sm font-semibold text-gray-900 bg-white px-4 py-3 rounded-lg border border-gray-200">
                                <span
                                    :class="priorityBadgeClass(report.priority)"
                                    :style="priorityBadgeStyle(report.priority)"
                                    class="px-3 py-1 inline-flex text-xs font-semibold rounded-full"
                                >
                                    {{ report.priority?.name ?? report.priority }}
                                </span>
                            </div>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <label class="flex items-center mb-2 text-sm font-medium text-gray-700">
                                <i class="fas fa-check-circle mr-2 text-blue-600"></i>
                                OT Creada
                            </label>
                            <div class="text-sm font-semibold text-gray-900 bg-white px-4 py-3 rounded-lg border border-gray-200">
                                {{ report.ot_created || 'No' }}
                            </div>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <label class="flex items-center mb-2 text-sm font-medium text-gray-700">
                                <i class="fas fa-info-circle mr-2 text-blue-600"></i>
                                Estado de OT
                            </label>
                            <div class="text-sm font-semibold text-gray-900 bg-white px-4 py-3 rounded-lg border border-gray-200">
                                {{ report.status_ot || 'N/A' }}
                            </div>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <label class="flex items-center mb-2 text-sm font-medium text-gray-700">
                                <i class="fas fa-calendar-check mr-2 text-blue-600"></i>
                                Fecha de Ejecución
                            </label>
                            <div class="text-sm font-semibold text-gray-900 bg-white px-4 py-3 rounded-lg border border-gray-200">
                                {{ report.execution_date ? formatDate(report.execution_date) : 'N/A' }}
                            </div>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-4 md:col-span-3">
                            <label class="flex items-center mb-2 text-sm font-medium text-gray-700">
                                <i class="fas fa-check-double mr-2 text-blue-600"></i>
                                Recomendaciones generales
                            </label>
                            <div class="text-sm text-gray-900 bg-white px-4 py-3 rounded-lg border border-gray-200 min-h-[60px]">
                                <template v-if="(report.general_recommendations || []).length">
                                    <ul class="list-disc pl-5 space-y-1">
                                        <li v-for="(rec, idx) in report.general_recommendations" :key="idx">
                                            <div class="rte-view leading-relaxed" v-html="sanitizeHtml(rec)"></div>
                                        </li>
                                    </ul>
                                </template>
                                <span v-else>N/A</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Enviar correo -->
        <div
            v-if="showEmailModal"
            class="fixed z-50 inset-0 overflow-y-auto flex items-center justify-center py-4"
            @click.self="closeSendEmailModal"
        >
            <div class="fixed inset-0 bg-gray-900 bg-opacity-75 backdrop-blur-sm" @click="closeSendEmailModal"></div>
            <div class="relative z-50 bg-white rounded-xl shadow-2xl w-full max-w-lg mx-4 max-h-[90vh] flex flex-col">
                <div class="flex items-center justify-between px-6 py-4 bg-gradient-to-r from-blue-500 to-blue-600 rounded-t-xl">
                    <h3 class="text-lg font-bold text-white">
                        <i class="fas fa-envelope mr-2"></i>
                        Enviar reporte por correo
                    </h3>
                    <button
                        type="button"
                        @click="closeSendEmailModal"
                        class="text-white hover:bg-white/20 rounded-lg p-1.5 transition-colors"
                    >
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="p-6 overflow-y-auto flex-1">
                    <!-- Usuarios de la empresa -->
                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-gray-700 mb-3">
                            <i class="fas fa-users mr-2 text-blue-600"></i>
                            Usuarios de la empresa {{ report?.equipment?.company?.name ? `(${report.equipment.company.name})` : '' }}
                        </label>
                        <div v-if="emailRecipientsLoading" class="text-sm text-gray-500 py-4">
                            <i class="fas fa-spinner fa-spin mr-2"></i>
                            Cargando usuarios...
                        </div>
                        <div v-else-if="emailRecipients.length === 0" class="text-sm text-gray-500 py-4 bg-gray-50 rounded-lg px-4">
                            No hay usuarios con correo en esta empresa.
                        </div>
                        <div v-else class="space-y-2 max-h-48 overflow-y-auto border border-gray-200 rounded-lg p-3">
                            <label
                                v-for="user in emailRecipients"
                                :key="user.id"
                                class="flex items-center gap-3 p-2 hover:bg-gray-50 rounded-lg cursor-pointer"
                            >
                                <input
                                    type="checkbox"
                                    :value="user.id"
                                    v-model="selectedUserIds"
                                    class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                />
                                <span class="text-sm text-gray-800">{{ user.name }}</span>
                                <span class="text-xs text-gray-500 truncate flex-1">{{ user.email }}</span>
                            </label>
                        </div>
                    </div>
                    <!-- Correos adicionales -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-plus-circle mr-2 text-blue-600"></i>
                            Agregar correos
                        </label>
                        <div class="flex gap-2">
                            <input
                                v-model="additionalEmailInput"
                                type="email"
                                placeholder="correo@ejemplo.com"
                                class="flex-1 border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                @keydown.enter.prevent="addAdditionalEmail"
                            />
                            <button
                                type="button"
                                @click="addAdditionalEmail"
                                class="px-4 py-2.5 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors"
                            >
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                        <div v-if="additionalEmailsList.length > 0" class="mt-3 space-y-2">
                            <div
                                v-for="(email, idx) in additionalEmailsList"
                                :key="idx"
                                class="flex items-center justify-between gap-2 py-2 px-3 bg-blue-50 border border-blue-200 rounded-lg text-sm"
                            >
                                <span class="text-gray-800">{{ email }}</span>
                                <button
                                    type="button"
                                    @click="removeAdditionalEmail(idx)"
                                    class="text-red-600 hover:text-red-800 hover:bg-red-100 rounded p-1 transition-colors"
                                    title="Quitar"
                                >
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Escriba el correo y presione Enter o el botón + para agregar</p>
                    </div>
                </div>
                <div class="flex justify-end gap-3 px-6 py-4 bg-gray-50 rounded-b-xl border-t border-gray-200">
                    <button
                        type="button"
                        @click="closeSendEmailModal"
                        class="px-4 py-2 text-gray-700 bg-gray-200 hover:bg-gray-300 rounded-lg font-medium transition-colors"
                    >
                        Cancelar
                    </button>
                    <button
                        type="button"
                        @click="confirmSendEmail"
                        :disabled="sendingEmail || (!hasSelectedRecipients() && !hasValidAdditionalEmails())"
                        class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 font-medium transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        <i v-if="sendingEmail" class="fas fa-spinner fa-spin mr-2"></i>
                        <i v-else class="fas fa-paper-plane mr-2"></i>
                        {{ sendingEmail ? 'Enviando...' : 'Enviar' }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import { useAuthStore } from '../../stores/auth';
import api from '../../services/api';
import Swal from 'sweetalert2';

const router = useRouter();
const route = useRoute();
const authStore = useAuthStore();

const normalizeReportType = (v) => {
    const s = (v ?? '').toString().trim().toLowerCase();
    return s === 'thermography' ? 'thermography' : 'vibration';
};

const report = ref(null);
const loading = ref(true);
const imageModalUrl = ref(null);
const downloadingPdf = ref(false);
const sendingEmail = ref(false);
const diagnosisByComponent = ref({});
const measurementValuesByComponent = ref([]);
const recommendationsByComponent = ref({});

const showEmailModal = ref(false);
const emailRecipients = ref([]);
const emailRecipientsLoading = ref(false);
const selectedUserIds = ref([]);
const additionalEmailInput = ref('');
const additionalEmailsList = ref([]);

const reportTypeResolved = computed(() => normalizeReportType(report.value?.report_type ?? route.query.report_type));
const backRouteName = computed(() => (reportTypeResolved.value === 'thermography' ? 'reports.thermography' : 'reports.vibration'));

const reportImagesByComponent = computed(() => {
    const grouped = {};
    (report.value?.images || []).forEach((image) => {
        const id = String(image.component_id);
        if (!grouped[id]) grouped[id] = [];
        grouped[id].push({
            url: image.url,
            kind: image.kind || 'thermal',
            kindLabel: image.kind_label || (image.kind === 'visual' ? 'Visual' : 'Térmica'),
            caption: image.caption || '',
            sortOrder: Number(image.sort_order ?? 0),
        });
    });
    Object.values(grouped).forEach((list) => list.sort((a, b) => a.sortOrder - b.sortOrder));
    return grouped;
});

onMounted(async () => {
    if (!authStore.hasPermission('detalle report')) {
        router.push({ name: backRouteName.value });
        return;
    }
    await loadReport();
});

function normalizeReportData(reportData) {
    const diagnosis = {};
    const recommendations = {};
    const componentsList = reportData.components && reportData.components.length > 0 ? reportData.components : [];

    if (reportData.diagnosis && typeof reportData.diagnosis === 'object') {
        const isNumericArray = Array.isArray(reportData.diagnosis) &&
            reportData.diagnosis.length > 0 &&
            Object.keys(reportData.diagnosis).every((key, index) => Number(key) === index);

        if (isNumericArray && componentsList.length > 0) {
            reportData.diagnosis.forEach((diag, index) => {
                if (componentsList[index]) {
                    const id = String(componentsList[index].id);
                    if (typeof diag === 'object' && (diag.text !== undefined || diag.images !== undefined)) {
                        diagnosis[id] = { text: diag.text ?? '', images: diag.images ?? [] };
                    } else if (typeof diag === 'string') {
                        diagnosis[id] = { text: diag, images: [] };
                    } else {
                        diagnosis[id] = { text: '', images: [] };
                    }
                }
            });
        } else {
            Object.keys(reportData.diagnosis).forEach((key) => {
                const id = String(key);
                const diag = reportData.diagnosis[key];
                if (typeof diag === 'object' && (diag.text !== undefined || diag.images !== undefined)) {
                    diagnosis[id] = { text: diag.text ?? '', images: Array.isArray(diag.images) ? diag.images : [] };
                } else if (typeof diag === 'string') {
                    diagnosis[id] = { text: diag, images: [] };
                } else {
                    diagnosis[id] = { text: '', images: [] };
                }
            });
        }
    }

    if (reportData.recommendations && typeof reportData.recommendations === 'object') {
        const isNumericArray = Array.isArray(reportData.recommendations) &&
            reportData.recommendations.length > 0 &&
            Object.keys(reportData.recommendations).every((key, index) => Number(key) === index);

        if (isNumericArray && componentsList.length > 0) {
            reportData.recommendations.forEach((rec, index) => {
                if (componentsList[index]) {
                    recommendations[String(componentsList[index].id)] = Array.isArray(rec) ? rec : [];
                }
            });
        } else {
            Object.keys(reportData.recommendations).forEach((key) => {
                recommendations[String(key)] = Array.isArray(reportData.recommendations[key])
                    ? reportData.recommendations[key]
                    : [];
            });
        }
    }

    return { diagnosis, recommendations };
}

const loadReport = async () => {
    loading.value = true;
    try {
        const response = await api.get(`/reports/${route.params.id}`);
        const reportData = response.data.data;
        report.value = reportData;
        const { diagnosis, recommendations } = normalizeReportData(reportData);
        diagnosisByComponent.value = diagnosis;
        recommendationsByComponent.value = recommendations;
        measurementValuesByComponent.value = reportData.measurement_values || [];
    } catch (error) {
        Swal.fire('Error', 'Error al cargar el reporte', 'error');
        router.push({ name: 'reports.vibration' });
    } finally {
        loading.value = false;
    }
};

const formatDate = (date) => {
    if (!date) return 'N/A';
    const str = String(date).trim();
    if (/^\d{4}-\d{2}-\d{2}$/.test(str)) {
        const [y, m, d] = str.split('-').map(Number);
        return new Date(y, m - 1, d).toLocaleDateString('es-ES');
    }
    return new Date(date).toLocaleDateString('es-ES');
};

const getConditionClass = () => {
    return 'bg-gray-100 text-gray-900 border border-gray-300';
};

// Usar colores registrados en la tabla priorities
const hasValidColor = (color) => color != null && String(color).trim() !== '';
const priorityBadgeClass = (priority) => {
    if (!priority) return 'bg-gray-100 text-gray-800';
    if (hasValidColor(priority.color)) return '';
    return priority.name === 'Alta' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800';
};
const priorityBadgeStyle = (priority) => {
    if (!priority) return {};
    const bg = priority.color != null ? String(priority.color).trim() : '';
    const fg = priority.text_color != null ? String(priority.text_color).trim() : '';
    if (bg === '' && fg === '') return {};
    return {
        backgroundColor: bg || 'transparent',
        color: fg || '#000000'
    };
};

const sanitizeFilename = (name) => (name || '').replace(/[\\/:*?"<>|]/g, ' ').replace(/\s+/g, ' ').trim();
const downloadPdf = async (event) => {
    if (event) {
        event.preventDefault();
        event.stopPropagation();
    }

    downloadingPdf.value = true;

    try {
        const response = await api.get(`/reports/${route.params.id}/pdf`, {
            responseType: 'blob'
        });
        const measurementRaw = report.value?.measurement_date;
        let datePart = '';
        if (measurementRaw) {
            const str = String(measurementRaw).trim();
            if (/^\d{4}-\d{2}-\d{2}$/.test(str)) {
                datePart = str;
            } else {
                const dt = new Date(measurementRaw);
                if (!Number.isNaN(dt.getTime())) {
                    const y = dt.getFullYear();
                    const m = String(dt.getMonth() + 1).padStart(2, '0');
                    const d = String(dt.getDate()).padStart(2, '0');
                    datePart = `${y}-${m}-${d}`;
                }
            }
        }
        const datePrefix = datePart ? datePart.replace(/-/g, '') : '';
        const typeName = normalizeReportType(report.value?.report_type) === 'thermography' ? 'Termografía' : 'Vibraciones';
        const fileNameParts = [
            datePrefix,
            'Informe',
            'Inspección',
            typeName,
            report.value?.equipment?.location,
            report.value?.equipment?.process,
            report.value?.equipment?.company?.name,
        ]
            .map((part) => sanitizeFilename(part))
            .filter((part) => part !== '');
        const filename = `${fileNameParts.join(' ')}.pdf`;
        const url = window.URL.createObjectURL(new Blob([response.data]));
        const link = document.createElement('a');
        link.href = url;
        link.setAttribute('download', filename);
        link.style.display = 'none';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        window.URL.revokeObjectURL(url);
    } catch (error) {
        console.error('Error al descargar PDF:', error);
        Swal.fire('Error', 'Error al descargar el PDF', 'error');
    } finally {
        downloadingPdf.value = false;
    }
};

const openSendEmailModal = async () => {
    showEmailModal.value = true;
    selectedUserIds.value = [];
    additionalEmailInput.value = '';
    additionalEmailsList.value = [];
    emailRecipients.value = [];
    emailRecipientsLoading.value = true;
    try {
        const response = await api.get(`/reports/${route.params.id}/email-recipients`);
        const users = response.data?.data ?? response.data ?? [];
        emailRecipients.value = users;
        selectedUserIds.value = [];
    } catch (error) {
        Swal.fire('Error', 'No se pudieron cargar los destinatarios', 'error');
        closeSendEmailModal();
    } finally {
        emailRecipientsLoading.value = false;
    }
};

const closeSendEmailModal = () => {
    showEmailModal.value = false;
    selectedUserIds.value = [];
    additionalEmailInput.value = '';
    additionalEmailsList.value = [];
};

const isValidEmail = (e) => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test((e || '').trim());

const addAdditionalEmail = () => {
    const email = (additionalEmailInput.value || '').trim();
    if (!email) return;
    if (!isValidEmail(email)) {
        Swal.fire('Aviso', 'Ingrese un correo electrónico válido', 'warning');
        return;
    }
    if (additionalEmailsList.value.includes(email)) return;
    additionalEmailsList.value.push(email);
    additionalEmailInput.value = '';
};

const removeAdditionalEmail = (idx) => {
    additionalEmailsList.value.splice(idx, 1);
};

const hasSelectedRecipients = () => selectedUserIds.value.length > 0;
const hasValidAdditionalEmails = () => additionalEmailsList.value.length > 0;

const confirmSendEmail = async () => {
    const additionalEmails = additionalEmailsList.value;
    if (!hasSelectedRecipients() && additionalEmails.length === 0) {
        Swal.fire('Aviso', 'Seleccione al menos un destinatario o agregue un correo', 'warning');
        return;
    }

    sendingEmail.value = true;
    try {
        await api.post(`/reports/${route.params.id}/send-email`, {
            user_ids: selectedUserIds.value,
            additional_emails: additionalEmails,
        });
        Swal.fire('Éxito', 'El correo se envió correctamente', 'success');
        closeSendEmailModal();
    } catch (error) {
        const message = error.response?.data?.message ?? 'Error al enviar el correo';
        Swal.fire('Error', message, 'error');
    } finally {
        sendingEmail.value = false;
    }
};

const getDiagnosisText = (componentId) => {
    const id = String(componentId);
    const d = diagnosisByComponent.value[id];
    if (!d) return '';
    return typeof d === 'string' ? d : (d.text || '');
};

const getComponentImages = (componentId) => {
    const id = String(componentId);
    const fromTable = reportImagesByComponent.value[id];
    if (fromTable && fromTable.length) return fromTable;

    const d = diagnosisByComponent.value[id];
    if (!d || typeof d !== 'object' || !Array.isArray(d.images)) return [];
    return d.images.map((url) => ({ url, kind: 'thermal', kindLabel: 'Térmica', caption: '', sortOrder: 0 }));
};

const getRecommendations = (componentId) => {
    const id = String(componentId);
    const recs = recommendationsByComponent.value[id];
    return Array.isArray(recs) ? recs : [];
};

const getMeasurementValues = (componentId) => {
    const id = String(componentId);
    return measurementValuesByComponent.value.filter((item) => String(item.component_id) === id);
};

const openImageModal = (url) => {
    imageModalUrl.value = url;
};

const closeImageModal = () => {
    imageModalUrl.value = null;
};

const allowedTags = ['b','strong','i','em','u','ul','ol','li','br','p','div','span'];
const sanitizeHtml = (html) => {
    if (!html) return '';
    let input = String(html).replace(/\r\n?/g, '\n');
    const temp = document.createElement('div');
    temp.innerHTML = input;
    const walker = (node) => {
        for (let i = node.childNodes.length - 1; i >= 0; i--) {
            const child = node.childNodes[i];
            if (child.nodeType === 1) {
                const tag = child.nodeName.toLowerCase();
                if (!allowedTags.includes(tag)) {
                    while (child.firstChild) node.insertBefore(child.firstChild, child);
                    node.removeChild(child);
                    continue;
                }
                // Preserve only safe color styles for span/div
                if (tag === 'span' || tag === 'div') {
                    const style = child.getAttribute('style') || '';
                    const parts = style.split(';').map(s => s.trim()).filter(Boolean);
                    const safe = [];
                    const colorRe = /^(#([0-9a-fA-F]{3}|[0-9a-fA-F]{6})|rgb(a)?\(\s*\d{1,3}\s*,\s*\d{1,3}\s*,\s*\d{1,3}(?:\s*,\s*(0|0?\.\d+|1))?\s*\))$/;
                    for (const part of parts) {
                        const [propRaw, valRaw] = part.split(':');
                        if (!propRaw || !valRaw) continue;
                        const prop = propRaw.trim().toLowerCase();
                        const val = valRaw.trim();
                        if ((prop === 'color' || prop === 'background-color') && colorRe.test(val)) {
                            safe.push(`${prop}: ${val}`);
                        }
                    }
                    // remove all attributes first
                    [...child.attributes].forEach(attr => child.removeAttribute(attr.name));
                    if (safe.length) {
                        child.setAttribute('style', safe.join('; '));
                    }
                } else {
                    // strip attributes on other tags
                    [...child.attributes].forEach(attr => child.removeAttribute(attr.name));
                }
                walker(child);
            } else if (child.nodeType === 8) {
                node.removeChild(child);
            }
        }
    };
    walker(temp);
    let out = temp.innerHTML;
    // Convert plain-text newlines to explicit <br> so they render
    out = out
        .replace(/(^|[^>])\n/g, (m, p1) => `${p1}<br>`) // single newlines
        .replace(/(\s*<br\s*\/?>\s*){2,}/gi, '<br><div></div>'); // group of line breaks -> block gap
    return out;
};
const getDiagnosisHtml = (componentId) => {
    const text = getDiagnosisText(componentId);
    return sanitizeHtml(text || '');
};
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.2s ease;
}
.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}
.rte-view {
    line-height: 1.9;
    white-space: pre-wrap;
    word-break: break-word;
}
.rte-view p,
.rte-view div {
    margin: 0 0 1rem 0;
}
.rte-view p:last-child,
.rte-view div:last-child {
    margin-bottom: 0;
}
.rte-view li {
    margin: 0.5rem 0;
}
</style>
