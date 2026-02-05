<template>
    <div class="container mx-auto px-4 sm:px-8">
        <div v-if="loading" class="fixed top-0 left-0 w-screen h-screen flex items-center justify-center z-50 bg-black bg-opacity-30 backdrop-blur-sm">
            <div class="flex flex-col items-center justify-center h-full">
                <svg class="animate-spin h-16 w-16 text-blue-600 mr-3 mb-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="10" stroke-opacity="0.25" stroke-width="2" stroke="currentColor" fill="none"></circle>
                    <path d="M12 2a10 10 0 00-9.5 7.5M12 2c2.5 0 5 1 7 2.5" stroke="currentColor" stroke-width="2" fill="none"></path>
                </svg>
                <p class="text-gray-700 font-medium">Cargando...</p>
            </div>
        </div>

        <div v-else class="py-6">
            <!-- Header -->
            <div class="mb-6">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h2 class="text-3xl font-bold text-gray-800 flex items-center">
                            <i class="fas fa-file-alt text-blue-600 mr-3"></i>
                            Editar Reporte
                        </h2>
                        <p class="text-gray-500 text-sm mt-1">Modifica la información del reporte</p>
                    </div>
                    <router-link
                        :to="{ name: 'reports' }"
                        class="text-gray-600 hover:text-gray-900 p-2 hover:bg-gray-100 rounded-lg transition-colors"
                        title="Volver"
                    >
                        <i class="fas fa-arrow-left text-xl"></i>
                    </router-link>
                </div>
            </div>

            <!-- Form (similar a Create pero con datos precargados) -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <form @submit.prevent="updateReport" class="space-y-6">
                    <!-- Mismo formulario que Create pero con datos del reporte -->
                    <!-- Información General -->
                    <div>
                        <div class="flex items-center mb-4 pb-2 border-b border-gray-200">
                            <i class="fas fa-info-circle text-blue-600 mr-2"></i>
                            <h4 class="text-lg font-semibold text-gray-800">Información General</h4>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            <!-- Empresa (filtro) -->
                            <div class="bg-gray-50 rounded-lg p-4">
                                <label class="flex items-center mb-2 text-sm font-medium text-gray-700">
                                    <i class="fas fa-building mr-2 text-blue-600"></i>
                                    Empresa
                                </label>
                                <select
                                    v-model="filterCompanyId"
                                    @change="onCompanyFilterChange"
                                    class="block w-full bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 px-4 py-2.5 transition-all duration-200"
                                >
                                    <option value="">Seleccione empresa</option>
                                    <option v-for="c in companies" :key="c.id" :value="c.id">
                                        {{ c.name }}
                                    </option>
                                </select>
                                <p class="mt-1 text-xs text-gray-500">Filtra los equipos por empresa</p>
                            </div>
                            <!-- Equipo -->
                            <div class="bg-gray-50 rounded-lg p-4">
                                <label class="flex items-center mb-2 text-sm font-medium text-gray-700">
                                    <i class="fas fa-cog mr-2 text-blue-600"></i>
                                    Equipo
                                    <span class="text-red-500 ml-1">*</span>
                                </label>
                                <select
                                    v-model="form.equipment_id"
                                    @change="loadEquipmentData"
                                    required
                                    class="block w-full bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 px-4 py-2.5 transition-all duration-200"
                                >
                                    <option value="">Seleccione</option>
                                    <option v-for="eq in filteredEquipments" :key="eq.id" :value="eq.id">
                                        {{ eq.name }}{{ eq.company?.name ? ` (${eq.company.name})` : '' }}
                                    </option>
                                </select>
                                <p v-if="errors.equipment_id" class="mt-1 text-sm text-red-600">{{ errors.equipment_id }}</p>
                            </div>

                            <!-- Resto de campos similares a Create -->
                            <!-- Tipo Equipo (readonly) -->
                            <div class="bg-gray-50 rounded-lg p-4">
                                <label class="flex items-center mb-2 text-sm font-medium text-gray-700">
                                    <i class="fas fa-tag mr-2 text-blue-600"></i>
                                    Tipo Equipo
                                </label>
                                <input
                                    :value="equipmentData.equipment_type"
                                    type="text"
                                    disabled
                                    class="bg-gray-100 border border-gray-300 text-gray-700 text-sm rounded-lg block w-full px-4 py-2.5 cursor-not-allowed"
                                />
                            </div>

                            <!-- Fecha Medición -->
                            <div class="bg-gray-50 rounded-lg p-4">
                                <label class="flex items-center mb-2 text-sm font-medium text-gray-700">
                                    <i class="fas fa-calendar mr-2 text-blue-600"></i>
                                    Fecha Medición
                                    <span class="text-red-500 ml-1">*</span>
                                </label>
                                <input
                                    v-model="form.measurement_date"
                                    type="date"
                                    required
                                    class="block w-full bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 px-4 py-2.5 transition-all duration-200"
                                />
                                <p v-if="errors.measurement_date" class="mt-1 text-sm text-red-600">{{ errors.measurement_date }}</p>
                            </div>

                            <!-- Ubicación (readonly) -->
                            <div class="bg-gray-50 rounded-lg p-4">
                                <label class="flex items-center mb-2 text-sm font-medium text-gray-700">
                                    <i class="fas fa-map-marker-alt mr-2 text-blue-600"></i>
                                    Ubicación
                                </label>
                                <input
                                    :value="equipmentData.location"
                                    type="text"
                                    disabled
                                    class="bg-gray-100 border border-gray-300 text-gray-700 text-sm rounded-lg block w-full px-4 py-2.5 cursor-not-allowed"
                                />
                            </div>

                            <!-- Proceso (readonly) -->
                            <div class="bg-gray-50 rounded-lg p-4">
                                <label class="flex items-center mb-2 text-sm font-medium text-gray-700">
                                    <i class="fas fa-tasks mr-2 text-blue-600"></i>
                                    Proceso
                                </label>
                                <input
                                    :value="equipmentData.process"
                                    type="text"
                                    disabled
                                    class="bg-gray-100 border border-gray-300 text-gray-700 text-sm rounded-lg block w-full px-4 py-2.5 cursor-not-allowed"
                                />
                            </div>

                            <!-- Analista -->
                            <div class="bg-gray-50 rounded-lg p-4">
                                <label class="flex items-center mb-2 text-sm font-medium text-gray-700">
                                    <i class="fas fa-user mr-2 text-blue-600"></i>
                                    Analista
                                    <span class="text-red-500 ml-1">*</span>
                                </label>
                                <select
                                    v-model="form.analist_id"
                                    required
                                    class="block w-full bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 px-4 py-2.5 transition-all duration-200"
                                >
                                    <option value="">Seleccione</option>
                                    <option v-for="user in users" :key="user.id" :value="user.id">
                                        {{ user.name }}
                                    </option>
                                </select>
                                <p v-if="errors.analist_id" class="mt-1 text-sm text-red-600">{{ errors.analist_id }}</p>
                            </div>

                        </div>
                    </div>

                    <!-- Diagnósticos y Recomendaciones (igual que Create) -->
                    <div v-if="components.length > 0">
                        <div class="flex items-center mb-4 pb-2 border-b border-gray-200">
                            <i class="fas fa-stethoscope text-blue-600 mr-2"></i>
                            <h4 class="text-lg font-semibold text-gray-800">Diagnósticos y Recomendaciones</h4>
                        </div>
                        <div class="space-y-6">
                            <div
                                v-for="(component, index) in components"
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

                                <!-- Diagnóstico -->
                                <div class="mb-4">
                                    <label class="flex items-center mb-2 text-sm font-medium text-gray-700">
                                        <i class="fas fa-clipboard-list mr-2 text-blue-600"></i>
                                        Diagnóstico
                                    </label>
                                    <textarea
                                        :value="getDiagnosisText(component.id)"
                                        @input="updateDiagnosisText(component.id, $event.target.value)"
                                        class="block w-full bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 px-4 py-3 transition-all duration-200 min-h-[100px]"
                                        placeholder="Ingresar diagnóstico"
                                    ></textarea>
                                </div>

                                <!-- Imágenes del Componente -->
                                <div class="mb-4">
                                    <div class="flex items-center justify-between mb-3">
                                        <label class="flex items-center text-sm font-medium text-gray-700">
                                            <i class="fas fa-image mr-2 text-purple-600"></i>
                                            Imágenes
                                            <span v-if="getComponentImages(component.id)?.length" class="ml-2 px-2 py-0.5 bg-purple-100 text-purple-700 rounded-full text-xs font-semibold">
                                                {{ getComponentImages(component.id).length }}
                                            </span>
                                        </label>
                                        <button
                                            @click="addComponentImage(component.id)"
                                            type="button"
                                            class="flex items-center px-3 py-1.5 bg-purple-500 hover:bg-purple-600 text-white rounded-lg transition-colors text-xs font-medium"
                                        >
                                            <i class="fas fa-plus mr-1"></i>
                                            Agregar Imagen
                                        </button>
                                    </div>

                                    <!-- Imágenes existentes -->
                                    <div v-if="getComponentImages(component.id)?.length" class="mb-3 grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
                                        <div
                                            v-for="(imageUrl, imgIndex) in getComponentImages(component.id)"
                                            :key="imgIndex"
                                            class="relative group"
                                        >
                                            <img
                                                :src="imageUrl"
                                                :alt="`Imagen ${imgIndex + 1}`"
                                                class="w-full h-32 object-cover rounded-lg border-2 border-gray-200 hover:border-purple-500 transition-all"
                                            />
                                            <button
                                                @click="removeExistingImage(component.id, imgIndex)"
                                                type="button"
                                                class="absolute top-1 right-1 bg-red-500 hover:bg-red-600 text-white rounded-full w-6 h-6 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity"
                                                title="Eliminar imagen"
                                            >
                                                <i class="fas fa-times text-xs"></i>
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Nuevas imágenes a subir -->
                                    <div v-if="getComponentImagesForm(component.id)?.length" class="space-y-3">
                                        <div
                                            v-for="(imageObj, imgIndex) in getComponentImagesForm(component.id)"
                                            :key="imgIndex"
                                            class="bg-white rounded-lg p-3 border border-gray-200"
                                        >
                                            <div class="flex items-center space-x-3">
                                                <div class="flex-1">
                                                    <div class="flex items-center mb-2">
                                                        <span class="flex-shrink-0 bg-purple-100 text-purple-800 rounded-full w-6 h-6 flex items-center justify-center text-xs font-semibold mr-2">
                                                            {{ imgIndex + 1 }}
                                                        </span>
                                                        <span class="text-xs text-gray-500">Nueva Imagen {{ imgIndex + 1 }}</span>
                                                    </div>
                                                    <input
                                                        @change="handleComponentImageChange(component.id, imgIndex, $event)"
                                                        type="file"
                                                        accept="image/*"
                                                        class="block w-full bg-white border border-gray-300 text-gray-900 text-sm rounded-lg file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-purple-50 file:text-purple-700 hover:file:bg-purple-100"
                                                    />
                                                    <div v-if="imageObj.preview" class="mt-2">
                                                        <img :src="imageObj.preview" alt="Preview" class="max-w-full h-32 object-cover rounded-lg border border-gray-200" />
                                                    </div>
                                                </div>
                                                <button
                                                    @click="removeComponentImage(component.id, imgIndex)"
                                                    type="button"
                                                    class="px-3 py-2.5 bg-red-500 hover:bg-red-600 text-white rounded-lg transition-colors"
                                                    title="Eliminar imagen"
                                                >
                                                    <i class="fas fa-trash text-sm"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <div v-if="!getComponentImages(component.id)?.length && !getComponentImagesForm(component.id)?.length" class="text-center py-4 bg-gray-100 rounded-lg border-2 border-dashed border-gray-300">
                                        <p class="text-sm text-gray-500">No hay imágenes aún</p>
                                        <p class="text-xs text-gray-400 mt-1">Haz clic en "Agregar Imagen" para subir una imagen</p>
                                    </div>
                                </div>

                                <!-- Recomendaciones -->
                                <div>
                                    <div class="flex items-center justify-between mb-3">
                                        <label class="flex items-center text-sm font-medium text-gray-700">
                                            <i class="fas fa-lightbulb mr-2 text-yellow-600"></i>
                                            Recomendaciones
                                            <span v-if="getRecommendations(component.id)?.length" class="ml-2 px-2 py-0.5 bg-blue-100 text-blue-700 rounded-full text-xs font-semibold">
                                                {{ getRecommendations(component.id)?.length }}
                                            </span>
                                        </label>
                                        <button
                                            @click="addRecommendation(component.id)"
                                            type="button"
                                            class="flex items-center px-3 py-1.5 bg-green-500 hover:bg-green-600 text-white rounded-lg transition-colors text-xs font-medium"
                                        >
                                            <i class="fas fa-plus mr-1"></i>
                                            Agregar
                                        </button>
                                    </div>

                                    <div v-if="getRecommendations(component.id)?.length" class="space-y-2">
                                        <div
                                            v-for="(rec, recIndex) in getRecommendations(component.id)"
                                            :key="recIndex"
                                            class="flex items-center space-x-2"
                                        >
                                            <div class="flex-1">
                                                <div class="flex items-center mb-1">
                                                    <span class="flex-shrink-0 bg-yellow-100 text-yellow-800 rounded-full w-6 h-6 flex items-center justify-center text-xs font-semibold mr-2">
                                                        {{ recIndex + 1 }}
                                                    </span>
                                                    <span class="text-xs text-gray-500">Recomendación {{ recIndex + 1 }}</span>
                                                </div>
                                                <input
                                                    :value="getRecommendations(component.id)[recIndex]"
                                                    @input="updateRecommendation(component.id, recIndex, $event.target.value)"
                                                    type="text"
                                                    class="block w-full bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 px-4 py-2.5 transition-all duration-200"
                                                    :placeholder="`Recomendación ${recIndex + 1}`"
                                                />
                                            </div>
                                            <button
                                                @click="deleteRecommendation(component.id, recIndex)"
                                                type="button"
                                                class="px-3 py-2.5 bg-red-500 hover:bg-red-600 text-white rounded-lg transition-colors"
                                                title="Eliminar recomendación"
                                            >
                                                <i class="fas fa-trash text-sm"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div v-else class="text-center py-4 bg-gray-100 rounded-lg border-2 border-dashed border-gray-300">
                                        <p class="text-sm text-gray-500">No hay recomendaciones aún</p>
                                        <p class="text-xs text-gray-400 mt-1">Haz clic en "Agregar" para crear una recomendación</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Condición, Mecanismo Falla, Componente Falla, Prioridad, OT Creada, Estado de OT (al final de todo) -->
                    <div>
                        <div class="flex items-center mb-4 pb-2 border-b border-gray-200">
                            <i class="fas fa-list mr-2 text-blue-600"></i>
                            <h4 class="text-lg font-semibold text-gray-800">Otros datos</h4>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            <div class="bg-gray-50 rounded-lg p-4">
                                <label class="flex items-center mb-2 text-sm font-medium text-gray-700">
                                    <i class="fas fa-exclamation-triangle mr-2 text-blue-600"></i>
                                    Condición <span class="text-red-500 ml-1">*</span>
                                </label>
                                <div class="flex space-x-2 items-stretch">
                                    <select v-model="form.condition_id" required class="flex-1 min-w-0 bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 px-4 py-2.5">
                                        <option value="">Seleccione</option>
                                        <option v-for="condition in conditions" :key="condition.id" :value="condition.id">{{ condition.name }}</option>
                                    </select>
                                    <button type="button" @click="showConditionModal = true" class="flex-shrink-0 w-10 h-10 bg-blue-500 hover:bg-blue-600 text-white rounded-lg flex items-center justify-center" title="Gestionar condiciones"><i class="fas fa-cog text-sm"></i></button>
                                </div>
                                <p v-if="errors.condition_id" class="mt-1 text-sm text-red-600">{{ errors.condition_id }}</p>
                            </div>
                            <div class="bg-gray-50 rounded-lg p-4">
                                <label class="flex items-center mb-2 text-sm font-medium text-gray-700">
                                    <i class="fas fa-wrench mr-2 text-blue-600"></i>
                                    Mecanismo Falla <span class="text-red-500 ml-1">*</span>
                                </label>
                                <div class="flex space-x-2 items-stretch">
                                    <select v-model="form.mechanism_fail_id" required class="flex-1 min-w-0 bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 px-4 py-2.5">
                                        <option value="">Seleccione</option>
                                        <option v-for="fail in mechanismFails" :key="fail.id" :value="fail.id">{{ fail.name }}</option>
                                    </select>
                                    <button type="button" @click="showMechanismFailModal = true" class="flex-shrink-0 w-10 h-10 bg-blue-500 hover:bg-blue-600 text-white rounded-lg flex items-center justify-center" title="Gestionar mecanismos de falla"><i class="fas fa-cog text-sm"></i></button>
                                </div>
                                <p v-if="errors.mechanism_fail_id" class="mt-1 text-sm text-red-600">{{ errors.mechanism_fail_id }}</p>
                            </div>
                            <div class="bg-gray-50 rounded-lg p-4">
                                <label class="flex items-center mb-2 text-sm font-medium text-gray-700">
                                    <i class="fas fa-cog mr-2 text-blue-600"></i>
                                    Componente Falla <span class="text-red-500 ml-1">*</span>
                                </label>
                                <div class="flex space-x-2 items-stretch">
                                    <select v-model="form.component_fail_id" required class="flex-1 min-w-0 bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 px-4 py-2.5">
                                        <option value="">Seleccione</option>
                                        <option v-for="fail in componentFails" :key="fail.id" :value="fail.id">{{ fail.name }}</option>
                                    </select>
                                    <button type="button" @click="showComponentFailModal = true" class="flex-shrink-0 w-10 h-10 bg-blue-500 hover:bg-blue-600 text-white rounded-lg flex items-center justify-center" title="Gestionar componentes de falla"><i class="fas fa-cog text-sm"></i></button>
                                </div>
                                <p v-if="errors.component_fail_id" class="mt-1 text-sm text-red-600">{{ errors.component_fail_id }}</p>
                            </div>
                            <div class="bg-gray-50 rounded-lg p-4">
                                <label class="flex items-center mb-2 text-sm font-medium text-gray-700">
                                    <i class="fas fa-flag mr-2 text-blue-600"></i>
                                    Prioridad <span class="text-red-500 ml-1">*</span>
                                </label>
                                <select v-model="form.priority" required class="block w-full bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 px-4 py-2.5">
                                    <option value="">Seleccione</option>
                                    <option value="Alta">Alta</option>
                                    <option value="Media">Media</option>
                                    <option value="Baja">Baja</option>
                                </select>
                                <p v-if="errors.priority" class="mt-1 text-sm text-red-600">{{ errors.priority }}</p>
                            </div>
                            <div class="bg-gray-50 rounded-lg p-4">
                                <label class="flex items-center mb-2 text-sm font-medium text-gray-700">
                                    <i class="fas fa-check-circle mr-2 text-blue-600"></i>
                                    OT Creada
                                </label>
                                <input v-model="form.ot_created" type="text" class="block w-full bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 px-4 py-2.5" placeholder="Ingresar OT creada" />
                            </div>
                            <div class="bg-gray-50 rounded-lg p-4">
                                <label class="flex items-center mb-2 text-sm font-medium text-gray-700">
                                    <i class="fas fa-info-circle mr-2 text-blue-600"></i>
                                    Estado de OT
                                </label>
                                <select
                                    v-model="form.status_ot"
                                    class="block w-full bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 px-4 py-2.5"
                                >
                                    <option value="">Seleccione</option>
                                    <option value="Cerrada">Cerrada</option>
                                    <option value="Abierta">Abierta</option>
                                    <option value="En ejecución">En ejecución</option>
                                </select>
                            </div>
                            <div class="bg-gray-50 rounded-lg p-4">
                                <label class="flex items-center mb-2 text-sm font-medium text-gray-700">
                                    <i class="fas fa-calendar-check mr-2 text-blue-600"></i>
                                    Fecha de Ejecución
                                </label>
                                <input
                                    v-model="form.execution_date"
                                    type="date"
                                    class="block w-full bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 px-4 py-2.5 transition-all duration-200"
                                />
                            </div>
                            <div class="bg-gray-50 rounded-lg p-4 md:col-span-3">
                                <label class="flex items-center mb-2 text-sm font-medium text-gray-700">
                                    <i class="fas fa-check-double mr-2 text-blue-600"></i>
                                    Recomendación general
                                </label>
                                <textarea
                                    v-model="form.verification_recommendation"
                                    class="block w-full bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 px-4 py-3 transition-all duration-200 min-h-[100px]"
                                    placeholder="Ingresar recomendación general"
                                ></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
                        <router-link
                            :to="{ name: 'reports' }"
                            class="px-6 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition-colors font-medium"
                        >
                            <i class="fas fa-times mr-2"></i>
                            Cancelar
                        </router-link>
                        <button
                            type="submit"
                            :disabled="saving"
                            class="px-6 py-2.5 bg-blue-500 hover:bg-blue-600 text-white rounded-lg transition-colors font-medium shadow-sm disabled:opacity-50"
                        >
                            <i class="fas fa-save mr-2"></i>
                            {{ saving ? 'Actualizando...' : 'Actualizar' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modales para gestionar opciones -->
        <CreateSelectModal
            v-model:show="showConditionModal"
            field="condition"
            label="Condición"
            api-endpoint="/catalogs/conditions"
            @item-saved="handleItemSaved"
            @options-updated="loadConditions"
        />
        <CreateSelectModal
            v-model:show="showMechanismFailModal"
            field="failure_mechanism"
            label="Mecanismo Falla"
            api-endpoint="/catalogs/mechanism-fails"
            @item-saved="handleItemSaved"
            @options-updated="loadMechanismFails"
        />
        <CreateSelectModal
            v-model:show="showComponentFailModal"
            field="component_failure"
            label="Componente Falla"
            api-endpoint="/catalogs/component-fails"
            @item-saved="handleItemSaved"
            @options-updated="loadComponentFails"
        />
    </div>
</template>

<script setup>
import { ref, onMounted, reactive, computed } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import { useAuthStore } from '../../stores/auth';
import api from '../../services/api';
import Swal from 'sweetalert2';
import CreateSelectModal from '../../components/CreateSelectModal.vue';

const router = useRouter();
const route = useRoute();
const authStore = useAuthStore();

const form = reactive({
    equipment_id: '',
    measurement_date: '',
    condition_id: '',
    mechanism_fail_id: '',
    component_fail_id: '',
    priority: '',
    analist_id: '',
    ot_created: '',
    status_ot: '',
    execution_date: '',
    verification_recommendation: '',
    diagnosis: {},
    recommendations: {},
    component_images: {},
});

const equipmentData = reactive({
    equipment_type: '',
    location: '',
    process: '',
});


const equipments = ref([]);
const companies = ref([]);
const filterCompanyId = ref('');
const conditions = ref([]);
const mechanismFails = ref([]);
const componentFails = ref([]);
const users = ref([]);
const components = ref([]);
const loading = ref(true);
const saving = ref(false);
const errors = ref({});
const showConditionModal = ref(false);
const showMechanismFailModal = ref(false);
const showComponentFailModal = ref(false);
const report = ref(null);

onMounted(async () => {
    loading.value = true;
    try {
        await loadCatalogs();
        await loadReport();
    } finally {
        loading.value = false;
    }
});

const loadCatalogs = async () => {
    try {
        const [equipmentsRes, companiesRes, conditionsRes, mechanismFailsRes, componentFailsRes, usersRes] = await Promise.all([
            api.get('/catalogs/equipment'),
            api.get('/catalogs/companies'),
            api.get('/catalogs/conditions'),
            api.get('/catalogs/mechanism-fails'),
            api.get('/catalogs/component-fails'),
            api.get('/catalogs/users'),
        ]);

        equipments.value = equipmentsRes.data?.data ?? equipmentsRes.data ?? [];
        companies.value = companiesRes.data?.data ?? companiesRes.data ?? [];
        conditions.value = conditionsRes.data?.data ?? conditionsRes.data ?? [];
        mechanismFails.value = mechanismFailsRes.data?.data ?? mechanismFailsRes.data ?? [];
        componentFails.value = componentFailsRes.data?.data ?? componentFailsRes.data ?? [];
        users.value = usersRes.data?.data ?? usersRes.data ?? [];
    } catch (error) {
        console.error('Error al cargar catálogos:', error);
        const msg = error.response?.data?.message || error.message || 'Error al cargar catálogos';
        Swal.fire('Error', msg, 'error');
    }
};

const loadConditions = async () => {
    try {
        const response = await api.get('/catalogs/conditions');
        conditions.value = response.data.data;
    } catch (error) {
        console.error('Error al cargar condiciones:', error);
    }
};

const loadMechanismFails = async () => {
    try {
        const response = await api.get('/catalogs/mechanism-fails');
        mechanismFails.value = response.data.data;
    } catch (error) {
        console.error('Error al cargar mecanismos de falla:', error);
    }
};

const loadComponentFails = async () => {
    try {
        const response = await api.get('/catalogs/component-fails');
        componentFails.value = response.data.data;
    } catch (error) {
        console.error('Error al cargar componentes de falla:', error);
    }
};

const filteredEquipments = computed(() => {
    if (!filterCompanyId.value) return [];
    const cid = Number(filterCompanyId.value);
    return equipments.value.filter(
        (eq) => Number(eq.company_id ?? eq.company?.id) === cid
    );
});

const onCompanyFilterChange = () => {
    if (!form.equipment_id) return;
    const cid = filterCompanyId.value ? Number(filterCompanyId.value) : null;
    const selected = equipments.value.find((e) => Number(e.id) === Number(form.equipment_id));
    const mustClear = !cid || (selected && Number(selected.company_id ?? selected.company?.id) !== cid);
    if (mustClear) {
        form.equipment_id = '';
        equipmentData.equipment_type = '';
        equipmentData.location = '';
        equipmentData.process = '';
        components.value = [];
        form.diagnosis = {};
        form.recommendations = {};
        form.component_images = {};
    }
};

const handleItemSaved = (data) => {
    // Seleccionar automáticamente el nuevo registro creado
    if (data.field === 'condition' && data.id) {
        form.condition_id = data.id;
    } else if (data.field === 'failure_mechanism' && data.id) {
        form.mechanism_fail_id = data.id;
    } else if (data.field === 'component_failure' && data.id) {
        form.component_fail_id = data.id;
    }
};

const loadReport = async () => {
    try {
        const response = await api.get(`/reports/${route.params.id}`);
        report.value = response.data.data;
        const reportData = response.data.data;

        filterCompanyId.value = reportData.equipment?.company?.id || '';
        form.equipment_id = reportData.equipment_id;
        form.measurement_date = reportData.measurement_date;
        form.condition_id = reportData.condition?.id || '';
        form.mechanism_fail_id = reportData.mechanism_fail?.id || '';
        form.component_fail_id = reportData.component_fail?.id || '';
        form.priority = reportData.priority;
        form.analist_id = reportData.analist?.id || '';
        form.ot_created = reportData.ot_created || '';
        form.status_ot = reportData.status_ot || '';
        form.execution_date = reportData.execution_date || '';
        form.verification_recommendation = reportData.verification_recommendation || '';
        // Procesar diagnosis para separar texto e imágenes
        // Primero cargar componentes si están disponibles
        let componentsList = [];
        if (reportData.components && reportData.components.length > 0) {
            componentsList = reportData.components;
        }

        const processedDiagnosis = {};
        if (reportData.diagnosis) {
            // Verificar si es un array indexado numéricamente
            const isNumericArray = Array.isArray(reportData.diagnosis) &&
                reportData.diagnosis.length > 0 &&
                Object.keys(reportData.diagnosis).every((key, index) => key == index);

            if (isNumericArray && componentsList.length > 0) {
                // Mapear array indexado a IDs de componentes
                reportData.diagnosis.forEach((diag, index) => {
                    if (componentsList[index]) {
                        const componentId = String(componentsList[index].id);
                        if (typeof diag === 'object' && diag !== null) {
                            processedDiagnosis[componentId] = {
                                text: diag.text ?? '',
                                images: Array.isArray(diag.images) ? [...diag.images] : []
                            };
                        } else if (typeof diag === 'string') {
                            processedDiagnosis[componentId] = { text: diag, images: [] };
                        } else {
                            processedDiagnosis[componentId] = { text: '', images: [] };
                        }
                    }
                });
            } else {
                // Es un objeto con claves de componente
                Object.keys(reportData.diagnosis).forEach(componentId => {
                    const diag = reportData.diagnosis[componentId];
                    const normalizedId = String(componentId);
                    if (typeof diag === 'object' && diag !== null) {
                        processedDiagnosis[normalizedId] = {
                            text: diag.text ?? '',
                            images: Array.isArray(diag.images) ? [...diag.images] : []
                        };
                    } else if (typeof diag === 'string') {
                        processedDiagnosis[normalizedId] = { text: diag, images: [] };
                    } else {
                        processedDiagnosis[normalizedId] = { text: '', images: [] };
                    }
                });
            }
        }
        form.diagnosis = processedDiagnosis;

        // Normalizar recomendaciones con IDs como strings
        const processedRecommendations = {};
        if (reportData.recommendations && typeof reportData.recommendations === 'object') {
            // Verificar si es un array indexado numéricamente
            const isNumericArray = Array.isArray(reportData.recommendations) &&
                reportData.recommendations.length > 0 &&
                Object.keys(reportData.recommendations).every((key, index) => key == index);

            if (isNumericArray && componentsList.length > 0) {
                // Mapear array indexado a IDs de componentes
                reportData.recommendations.forEach((rec, index) => {
                    if (componentsList[index]) {
                        const componentId = String(componentsList[index].id);
                        processedRecommendations[componentId] = Array.isArray(rec) ? rec : [];
                    }
                });
            } else {
                // Es un objeto con claves de componente
                Object.keys(reportData.recommendations).forEach(componentId => {
                    const normalizedId = String(componentId);
                    processedRecommendations[normalizedId] = Array.isArray(reportData.recommendations[componentId])
                        ? reportData.recommendations[componentId]
                        : [];
                });
            }
        }
        form.recommendations = processedRecommendations;

        form.component_images = {}; // Inicializar para nuevas imágenes
        form.imagesToDelete = {}; // Para marcar imágenes a eliminar

        // Si el reporte ya tiene componentes, usarlos; si no, cargar del equipo
        if (reportData.components && reportData.components.length > 0) {
            components.value = reportData.components;
            // Asegurar que todos los componentes tengan diagnóstico y recomendaciones inicializados
            components.value.forEach(component => {
                const componentId = String(component.id);
                if (form.diagnosis[componentId] === undefined) {
                    form.diagnosis[componentId] = { text: '', images: [] };
                }
                if (form.recommendations[componentId] === undefined) {
                    form.recommendations[componentId] = [];
                }
                if (form.component_images[componentId] === undefined) {
                    form.component_images[componentId] = [];
                }
            });
        } else if (componentsList.length > 0) {
            // Si no hay componentes en reportData pero los tenemos de la lista, usarlos
            components.value = componentsList;
            components.value.forEach(component => {
                const componentId = String(component.id);
                if (form.diagnosis[componentId] === undefined) {
                    form.diagnosis[componentId] = { text: '', images: [] };
                }
                if (form.recommendations[componentId] === undefined) {
                    form.recommendations[componentId] = [];
                }
                if (form.component_images[componentId] === undefined) {
                    form.component_images[componentId] = [];
                }
            });
        }

        // Cargar datos del equipo (para información adicional)
        await loadEquipmentData();
    } catch (error) {
        Swal.fire('Error', 'Error al cargar el reporte', 'error');
        router.push({ name: 'reports' });
    }
};

const loadEquipmentData = async () => {
    if (!form.equipment_id) return;

    try {
        const response = await api.get(`/equipment/${form.equipment_id}`);
        const equipment = response.data.data;

        equipmentData.equipment_type = equipment.equipment_type?.name || '';
        equipmentData.location = equipment.ubication?.name || '';
        equipmentData.process = equipment.process?.name || '';

        // Solo cargar componentes si no se cargaron ya del reporte
        if (equipment.components && (!components.value || components.value.length === 0)) {
            components.value = equipment.components;
            components.value.forEach(component => {
                // Normalizar component.id a string para consistencia
                const componentId = String(component.id);
                // Solo inicializar si no existe (no sobrescribir datos existentes)
                if (form.diagnosis[componentId] === undefined) {
                    form.diagnosis[componentId] = { text: '', images: [] };
                }
                if (form.recommendations[componentId] === undefined) {
                    form.recommendations[componentId] = [];
                }
                if (form.component_images[componentId] === undefined) {
                    form.component_images[componentId] = [];
                }
            });
        } else if (equipment.components) {
            // Si ya hay componentes, solo asegurar que todos tengan datos inicializados
            components.value.forEach(component => {
                const componentId = String(component.id);
                if (form.diagnosis[componentId] === undefined) {
                    form.diagnosis[componentId] = { text: '', images: [] };
                }
                if (form.recommendations[componentId] === undefined) {
                    form.recommendations[componentId] = [];
                }
                if (form.component_images[componentId] === undefined) {
                    form.component_images[componentId] = [];
                }
            });
        }
    } catch (error) {
        console.error('Error al cargar datos del equipo:', error);
    }
};

const getDiagnosisText = (componentId) => {
    const normalizedId = String(componentId);
    const d = form.diagnosis[normalizedId];
    if (!d) return '';
    return typeof d === 'object' && d !== null ? (d.text ?? '') : String(d);
};

const updateDiagnosisText = (componentId, value) => {
    const normalizedId = String(componentId);
    const d = form.diagnosis[normalizedId];
    if (typeof d === 'object' && d !== null) {
        form.diagnosis[normalizedId] = { ...d, text: value };
    } else {
        form.diagnosis[normalizedId] = { text: value, images: [] };
    }
};

const getComponentImages = (componentId) => {
    const normalizedId = String(componentId);
    const d = form.diagnosis[normalizedId];
    if (typeof d === 'object' && d !== null && Array.isArray(d.images)) {
        return d.images;
    }
    return [];
};

const addComponentImage = (componentId) => {
    // Normalizar componentId a string para consistencia
    const normalizedId = String(componentId);
    if (!form.component_images[normalizedId]) {
        form.component_images[normalizedId] = [];
    }
    form.component_images[normalizedId].push({ file: null, preview: null });
};

const handleComponentImageChange = (componentId, imgIndex, event) => {
    // Normalizar componentId a string para consistencia
    const normalizedId = String(componentId);
    const file = event.target.files[0];
    if (file) {
        if (!form.component_images[normalizedId]) {
            form.component_images[normalizedId] = [];
        }
        form.component_images[normalizedId][imgIndex] = {
            file: file,
            preview: URL.createObjectURL(file)
        };
    }
};

const removeComponentImage = (componentId, imgIndex) => {
    // Normalizar componentId a string para consistencia
    const normalizedId = String(componentId);
    if (form.component_images[normalizedId] && form.component_images[normalizedId][imgIndex]) {
        if (form.component_images[normalizedId][imgIndex].preview) {
            URL.revokeObjectURL(form.component_images[normalizedId][imgIndex].preview);
        }
        form.component_images[normalizedId].splice(imgIndex, 1);
    }
};

const removeExistingImage = (componentId, imgIndex) => {
    const normalizedId = String(componentId);
    const d = form.diagnosis[normalizedId];
    if (typeof d !== 'object' || !d || !Array.isArray(d.images)) return;
    if (imgIndex < 0 || imgIndex >= d.images.length) return;
    const urlToDelete = d.images[imgIndex];
    // Marcar para que el backend borre el archivo
    if (!form.imagesToDelete) form.imagesToDelete = {};
    if (!form.imagesToDelete[normalizedId]) form.imagesToDelete[normalizedId] = [];
    form.imagesToDelete[normalizedId].push(urlToDelete);
    // Quitar la imagen del diagnóstico para que el formulario y el envío sean correctos
    form.diagnosis[normalizedId] = {
        ...d,
        images: d.images.filter((_, i) => i !== imgIndex)
    };
};

// Helper para obtener recomendaciones normalizando el ID
const getRecommendations = (componentId) => {
    const normalizedId = String(componentId);
    return form.recommendations[normalizedId] || [];
};

// Helper para obtener imágenes de componente normalizando el ID
const getComponentImagesForm = (componentId) => {
    const normalizedId = String(componentId);
    return form.component_images[normalizedId] || [];
};

const addRecommendation = (componentId) => {
    // Normalizar componentId a string para consistencia
    const normalizedId = String(componentId);
    if (!form.recommendations[normalizedId]) {
        form.recommendations[normalizedId] = [];
    }
    form.recommendations[normalizedId].push('');
};

const updateRecommendation = (componentId, index, value) => {
    // Normalizar componentId a string para consistencia
    const normalizedId = String(componentId);
    if (!form.recommendations[normalizedId]) {
        form.recommendations[normalizedId] = [];
    }
    form.recommendations[normalizedId][index] = value;
};

const deleteRecommendation = (componentId, index) => {
    // Normalizar componentId a string para consistencia
    const normalizedId = String(componentId);
    if (form.recommendations[normalizedId]) {
        form.recommendations[normalizedId].splice(index, 1);
    }
};

const updateReport = async () => {
    saving.value = true;
    errors.value = {};

    try {
        const formData = new FormData();
        formData.append('_method', 'PUT');

        Object.keys(form).forEach(key => {
            if (key === 'component_images') {
                // Enviar imágenes de componentes con formato simple
                Object.keys(form.component_images).forEach(componentId => {
                    form.component_images[componentId].forEach((imageObj, imgIndex) => {
                        if (imageObj && imageObj.file) {
                            formData.append(`component_images_${componentId}_${imgIndex}`, imageObj.file);
                        }
                    });
                });
            } else if (key === 'imagesToDelete') {
                // Enviar lista de imágenes a eliminar
                if (Object.keys(form.imagesToDelete).length > 0) {
                    formData.append('images_to_delete', JSON.stringify(form.imagesToDelete));
                }
            } else if (key === 'diagnosis' || key === 'recommendations') {
                formData.append(key, JSON.stringify(form[key]));
            } else if (form[key] !== null && form[key] !== '') {
                formData.append(key, form[key]);
            }
        });

        // Enviar también un JSON con la estructura de imágenes para referencia
        const componentImagesStructure = {};
        Object.keys(form.component_images).forEach(componentId => {
            componentImagesStructure[componentId] = form.component_images[componentId]
                .map((img, idx) => img.file ? `component_images_${componentId}_${idx}` : null)
                .filter(Boolean);
        });
        if (Object.keys(componentImagesStructure).length > 0) {
            formData.append('component_images_structure', JSON.stringify(componentImagesStructure));
        }

        await api.post(`/reports/${route.params.id}`, formData, {
            headers: {
                'Content-Type': 'multipart/form-data',
            },
        });

        Swal.fire('Éxito', 'Reporte actualizado correctamente', 'success');
        router.push({ name: 'reports' });
    } catch (error) {
        if (error.response?.status === 422 && error.response?.data?.errors) {
            const e = error.response.data.errors;
            errors.value = Object.fromEntries(
                Object.entries(e).map(([k, v]) => [k, Array.isArray(v) ? v[0] : v])
            );
        }
        const message = error.response?.data?.message || 'Error al actualizar el reporte';
        Swal.fire('Error', message, 'error');
    } finally {
        saving.value = false;
    }
};
</script>
