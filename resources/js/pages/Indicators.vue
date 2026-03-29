<template>
    <div class="container mx-auto px-4 sm:px-8">
        <div v-if="loading" class="fixed top-0 left-0 w-screen h-screen flex items-center justify-center z-50 bg-black/30 backdrop-blur-sm">
            <div class="flex flex-col items-center">
                <svg class="animate-spin h-16 w-16 text-blue-600 mb-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                </svg>
                <p class="text-gray-700 font-medium">Cargando indicadores...</p>
            </div>
        </div>

        <div v-else class="py-4 sm:py-6">
            <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h2 class="text-2xl sm:text-3xl font-bold text-gray-800 flex items-center">
                        <i class="fas fa-chart-bar text-blue-600 mr-3"></i>
                        Indicadores
                    </h2>
                    <p class="text-gray-500 text-sm mt-1">Estadísticas a partir de los datos del sistema</p>
                </div>
                <div class="flex flex-wrap items-center gap-4">
                    <div v-if="!authStore.hasRole('Cliente')" class="flex items-center gap-2">
                        <label for="empresa-select" class="text-sm font-medium text-gray-700 whitespace-nowrap">Ver detalle por empresa:</label>
                        <Select2
                            id="empresa-select"
                            v-model="selectedCompanyId"
                            :options="companies"
                            allLabel="Todas las empresas"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 px-3 py-2 min-w-[200px]"
                        />
                    </div>
                    <div class="flex items-center gap-2">
                        <label for="process-select" class="text-sm font-medium text-gray-700 whitespace-nowrap">Proceso:</label>
                        <Select2
                            id="process-select"
                            v-model="selectedProcessId"
                            :options="processes"
                            allLabel="Todos los procesos"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 px-3 py-2 min-w-[200px]"
                        />
                    </div>
                </div>
            </div>

            <!-- Indicadores generales (solo cuando no hay empresa seleccionada) -->
            <template v-if="!selectedCompanyId">
            <!-- Resumen numérico -->
            <section class="mb-10">
                <div class="grid grid-cols-2 gap-4 lg:grid-cols-5 lg:gap-6">
                    <div class="bg-white rounded-xl shadow-md border border-gray-100 p-6 flex items-center gap-4">
                        <div class="w-14 h-14 bg-blue-500 rounded-xl flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-building text-white text-2xl"></i>
                        </div>
                        <div class="min-w-0">
                            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Empresas</p>
                            <p class="text-2xl lg:text-3xl font-bold text-gray-900 mt-0.5">{{ totales.empresas }}</p>
                        </div>
                    </div>
                    <div class="bg-white rounded-xl shadow-md border border-gray-100 p-6 flex items-center gap-4">
                        <div class="w-14 h-14 bg-indigo-500 rounded-xl flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-toolbox text-white text-2xl"></i>
                        </div>
                        <div class="min-w-0">
                            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Equipos</p>
                            <p class="text-2xl lg:text-3xl font-bold text-gray-900 mt-0.5">{{ totales.equipos }}</p>
                        </div>
                    </div>
                    <div class="bg-white rounded-xl shadow-md border border-gray-100 p-6 flex items-center gap-4">
                        <div class="w-14 h-14 bg-green-500 rounded-xl flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-file-alt text-white text-2xl"></i>
                        </div>
                        <div class="min-w-0">
                            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Reportes</p>
                            <p class="text-2xl lg:text-3xl font-bold text-gray-900 mt-0.5">{{ totales.reportes }}</p>
                        </div>
                    </div>
                    <div class="bg-white rounded-xl shadow-md border border-gray-100 p-6 flex items-center gap-4">
                        <div class="w-14 h-14 bg-amber-500 rounded-xl flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-calendar-day text-white text-2xl"></i>
                        </div>
                        <div class="min-w-0">
                            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Este mes</p>
                            <p class="text-2xl lg:text-3xl font-bold text-gray-900 mt-0.5">{{ totales.reportes_este_mes }}</p>
                        </div>
                    </div>
                    <div class="bg-white rounded-xl shadow-md border border-gray-100 p-6 flex items-center gap-4 col-span-2 lg:col-span-1">
                        <div class="w-14 h-14 bg-teal-500 rounded-xl flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-calendar-alt text-white text-2xl"></i>
                        </div>
                        <div class="min-w-0">
                            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Este año</p>
                            <p class="text-2xl lg:text-3xl font-bold text-gray-900 mt-0.5">{{ totales.reportes_este_ano }}</p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Tendencias -->
            <section class="mb-10">
                <h3 class="text-lg font-semibold text-gray-700 mb-4 flex items-center">
                    <i class="fas fa-chart-line text-blue-500 mr-2"></i>
                    Tendencias
                </h3>
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Reportes por mes -->
                <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/80">
                        <h4 class="font-semibold text-gray-800 text-base"><i class="fas fa-calendar-alt text-blue-500 mr-2"></i>Reportes por mes</h4>
                        <p class="text-xs text-gray-500 mt-0.5">Últimos 12 meses</p>
                    </div>
                    <div class="p-6">
                        <div v-if="reportesPorMes.length === 0" class="h-72 flex items-center justify-center text-gray-500">Sin datos</div>
                        <div v-else class="flex items-end justify-between gap-2 h-72 pt-2">
                            <div
                                v-for="row in reportesPorMes"
                                :key="`${row.anio}-${row.mes}`"
                                class="flex-1 min-w-0 flex flex-col items-center group"
                            >
                                <span class="text-xs text-gray-500 mb-1.5 truncate w-full text-center" :title="row.label">{{ row.label }}</span>
                                <div
                                    class="w-full max-w-[28px] mx-auto bg-blue-500 rounded-t-md hover:bg-blue-600 transition-colors min-h-[6px]"
                                    :style="{ height: barHeightPorMes(row) + '%' }"
                                    :title="row.total + ' reportes'"
                                ></div>
                                <span class="text-xs font-semibold text-gray-700 mt-1.5">{{ row.total }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Reportes por condición -->
                <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/80">
                        <h4 class="font-semibold text-gray-800 text-base"><i class="fas fa-tags text-blue-500 mr-2"></i>Reportes por condición</h4>
                    </div>
                    <div class="p-6 flex flex-col sm:flex-row items-center gap-6">
                        <div v-if="reportesPorCondicion.length === 0" class="h-52 w-52 flex items-center justify-center text-gray-500">Sin datos</div>
                        <template v-else>
                            <div class="relative w-52 h-52 flex-shrink-0">
                                <svg viewBox="0 0 100 100" class="w-full h-full -rotate-90">
                                    <circle
                                        v-for="slice in donaCondicionSlices"
                                        :key="slice.key"
                                        cx="50"
                                        cy="50"
                                        r="40"
                                        fill="none"
                                        :stroke="slice.color"
                                        stroke-width="20"
                                        :stroke-dasharray="slice.dash"
                                        :stroke-dashoffset="slice.offset"
                                    />
                                </svg>
                                <div class="absolute inset-0 flex items-center justify-center">
                                    <span class="text-3xl font-bold text-gray-700">{{ totalCondicion }}</span>
                                </div>
                            </div>
                            <div class="flex-1 min-w-0 space-y-3">
                                <div
                                    v-for="row in reportesPorCondicion"
                                    :key="row.condition_id || 'sin'"
                                    class="flex items-center gap-3 text-sm"
                                >
                                    <span
                                        class="w-4 h-4 rounded-full flex-shrink-0 shadow-sm"
                                        :style="{ backgroundColor: row.color || '#94a3b8' }"
                                    ></span>
                                    <span class="truncate text-gray-800 min-w-0 max-w-[200px]" :title="row.nombre">{{ row.nombre }}</span>
                                    <span class="font-semibold text-gray-600 flex-shrink-0 tabular-nums">{{ row.total }}</span>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
                </div>
            </section>

            <!-- Por entidad -->
            <section class="mb-10">
                <h3 class="text-lg font-semibold text-gray-700 mb-4 flex items-center">
                    <i class="fas fa-sitemap text-blue-500 mr-2"></i>
                    Por entidad
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                    <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/80">
                            <h4 class="font-semibold text-gray-800 text-base"><i class="fas fa-building text-blue-500 mr-2"></i>Reportes por empresa</h4>
                        </div>
                        <div class="p-5">
                            <div v-if="reportesPorEmpresa.length === 0" class="py-10 text-center text-gray-500 text-sm">Sin datos</div>
                            <div v-else class="space-y-4 max-h-80 overflow-y-auto pr-1">
                                <div v-for="row in reportesPorEmpresa" :key="row.company_id || 'sin'" class="flex items-center gap-3">
                                    <span class="text-sm text-gray-800 truncate min-w-0 flex-1 max-w-[220px]" :title="row.nombre">{{ row.nombre }}</span>
                                    <div class="flex-1 h-8 bg-gray-100 rounded-lg overflow-hidden min-w-0 max-w-[200px]">
                                        <div class="h-full bg-indigo-500 rounded-lg transition-all" :style="{ width: pctEmpresa(row) + '%' }"></div>
                                    </div>
                                    <span class="text-sm font-semibold text-gray-700 w-9 text-right tabular-nums">{{ row.total }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/80">
                        <h4 class="font-semibold text-gray-800 text-base"><i class="fas fa-user-check text-blue-500 mr-2"></i>Reportes por analista</h4>
                    </div>
                    <div class="p-5">
                        <div v-if="reportesPorAnalista.length === 0" class="py-10 text-center text-gray-500 text-sm">Sin datos</div>
                        <div v-else class="space-y-4 max-h-80 overflow-y-auto pr-1">
                            <div v-for="row in reportesPorAnalista" :key="row.analist_id || 'sin'" class="flex items-center gap-3">
                                <span class="text-sm text-gray-800 truncate min-w-0 flex-1 max-w-[220px]" :title="row.nombre">{{ row.nombre }}</span>
                                <div class="flex-1 h-8 bg-gray-100 rounded-lg overflow-hidden min-w-0 max-w-[200px]">
                                    <div class="h-full bg-green-500 rounded-lg transition-all" :style="{ width: pctAnalista(row) + '%' }"></div>
                                </div>
                                <span class="text-sm font-semibold text-gray-700 w-9 text-right tabular-nums">{{ row.total }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/80">
                        <h4 class="font-semibold text-gray-800 text-base"><i class="fas fa-cogs text-blue-500 mr-2"></i>Reportes por proceso</h4>
                    </div>
                    <div class="p-5">
                        <div v-if="reportesPorProceso.length === 0" class="py-10 text-center text-gray-500 text-sm">Sin datos</div>
                        <div v-else class="space-y-4 max-h-80 overflow-y-auto pr-1">
                            <div v-for="row in reportesPorProceso" :key="row.process_id || 'sin'" class="flex items-center gap-3">
                                <span class="text-sm text-gray-800 truncate min-w-0 flex-1 max-w-[220px]" :title="row.nombre">{{ row.nombre }}</span>
                                <div class="flex-1 h-8 bg-gray-100 rounded-lg overflow-hidden min-w-0 max-w-[200px]">
                                    <div class="h-full bg-amber-500 rounded-lg transition-all" :style="{ width: pctProceso(row) + '%' }"></div>
                                </div>
                                <span class="text-sm font-semibold text-gray-700 w-9 text-right tabular-nums">{{ row.total }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden md:col-span-2 xl:col-span-1">
                    <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/80">
                        <h4 class="font-semibold text-gray-800 text-base"><i class="fas fa-toolbox text-blue-500 mr-2"></i>Equipos por empresa</h4>
                    </div>
                    <div class="p-5">
                        <div v-if="equiposPorEmpresa.length === 0" class="py-10 text-center text-gray-500 text-sm">Sin datos</div>
                        <div v-else class="space-y-4 max-h-80 overflow-y-auto pr-1">
                            <div v-for="row in equiposPorEmpresa" :key="row.company_id || 'sin'" class="flex items-center gap-3">
                                <span class="text-sm text-gray-800 truncate min-w-0 flex-1 max-w-[220px]" :title="row.nombre">{{ row.nombre }}</span>
                                <div class="flex-1 h-8 bg-gray-100 rounded-lg overflow-hidden min-w-0 max-w-[200px]">
                                    <div class="h-full bg-teal-500 rounded-lg transition-all" :style="{ width: pctEquiposEmpresa(row) + '%' }"></div>
                                </div>
                                <span class="text-sm font-semibold text-gray-700 w-9 text-right tabular-nums">{{ row.total }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                </div>
            </section>

            <!-- Por clasificación -->
            <section class="mb-10">
                <h3 class="text-lg font-semibold text-gray-700 mb-4 flex items-center">
                    <i class="fas fa-layer-group text-blue-500 mr-2"></i>
                    Por clasificación
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/80">
                        <h4 class="font-semibold text-gray-800 text-base"><i class="fas fa-cogs text-blue-500 mr-2"></i>Tipo de equipo</h4>
                    </div>
                    <div class="p-5">
                        <div v-if="reportesPorTipoEquipo.length === 0" class="py-10 text-center text-gray-500 text-sm">Sin datos</div>
                        <div v-else class="space-y-4 max-h-80 overflow-y-auto pr-1">
                            <div v-for="row in reportesPorTipoEquipo" :key="row.type_id || 'sin'" class="flex items-center gap-3">
                                <span class="text-sm text-gray-800 truncate min-w-0 flex-1 max-w-[220px]" :title="row.nombre">{{ row.nombre }}</span>
                                <div class="flex-1 h-8 bg-gray-100 rounded-lg overflow-hidden min-w-0 max-w-[200px]">
                                    <div class="h-full bg-violet-500 rounded-lg transition-all" :style="{ width: pctTipoEquipo(row) + '%' }"></div>
                                </div>
                                <span class="text-sm font-semibold text-gray-700 w-9 text-right tabular-nums">{{ row.total }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/80">
                        <h4 class="font-semibold text-gray-800 text-base"><i class="fas fa-map-marker-alt text-blue-500 mr-2"></i>Ubicación</h4>
                    </div>
                    <div class="p-5">
                        <div v-if="reportesPorUbicacion.length === 0" class="py-10 text-center text-gray-500 text-sm">Sin datos</div>
                        <div v-else class="space-y-4 max-h-80 overflow-y-auto pr-1">
                            <div v-for="row in reportesPorUbicacion" :key="row.ubication_id || 'sin'" class="flex items-center gap-3">
                                <span class="text-sm text-gray-800 truncate min-w-0 flex-1 max-w-[220px]" :title="row.nombre">{{ row.nombre }}</span>
                                <div class="flex-1 h-8 bg-gray-100 rounded-lg overflow-hidden min-w-0 max-w-[200px]">
                                    <div class="h-full bg-sky-500 rounded-lg transition-all" :style="{ width: pctUbicacion(row) + '%' }"></div>
                                </div>
                                <span class="text-sm font-semibold text-gray-700 w-9 text-right tabular-nums">{{ row.total }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/80">
                        <h4 class="font-semibold text-gray-800 text-base"><i class="fas fa-exclamation-triangle text-blue-500 mr-2"></i>Mecanismo de falla</h4>
                    </div>
                    <div class="p-5">
                        <div v-if="reportesPorMecanismoFalla.length === 0" class="py-10 text-center text-gray-500 text-sm">Sin datos</div>
                        <div v-else class="space-y-4 max-h-80 overflow-y-auto pr-1">
                            <div v-for="row in reportesPorMecanismoFalla" :key="row.mechanism_fail_id || 'sin'" class="flex items-center gap-3">
                                <span class="text-sm text-gray-800 truncate min-w-0 flex-1 max-w-[220px]" :title="row.nombre">{{ row.nombre }}</span>
                                <div class="flex-1 h-8 bg-gray-100 rounded-lg overflow-hidden min-w-0 max-w-[200px]">
                                    <div class="h-full bg-rose-500 rounded-lg transition-all" :style="{ width: pctMecanismoFalla(row) + '%' }"></div>
                                </div>
                                <span class="text-sm font-semibold text-gray-700 w-9 text-right tabular-nums">{{ row.total }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/80">
                        <h4 class="font-semibold text-gray-800 text-base"><i class="fas fa-puzzle-piece text-blue-500 mr-2"></i>Componente en Falla</h4>
                    </div>
                    <div class="p-5">
                        <div v-if="reportesPorComponenteFalla.length === 0" class="py-10 text-center text-gray-500 text-sm">Sin datos</div>
                        <div v-else class="space-y-4 max-h-80 overflow-y-auto pr-1">
                            <div v-for="row in reportesPorComponenteFalla" :key="row.component_fail_id || 'sin'" class="flex items-center gap-3">
                                <span class="text-sm text-gray-800 truncate min-w-0 flex-1 max-w-[220px]" :title="row.nombre">{{ row.nombre }}</span>
                                <div class="flex-1 h-8 bg-gray-100 rounded-lg overflow-hidden min-w-0 max-w-[200px]">
                                    <div class="h-full bg-orange-500 rounded-lg transition-all" :style="{ width: pctComponenteFalla(row) + '%' }"></div>
                                </div>
                                <span class="text-sm font-semibold text-gray-700 w-9 text-right tabular-nums">{{ row.total }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden md:col-span-2 xl:col-span-1">
                    <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/80">
                        <h4 class="font-semibold text-gray-800 text-base"><i class="fas fa-trophy text-blue-500 mr-2"></i>Equipos con más reportes</h4>
                    </div>
                    <div class="p-5">
                        <div v-if="topEquiposReportes.length === 0" class="py-10 text-center text-gray-500 text-sm">Sin datos</div>
                        <div v-else class="space-y-4 max-h-80 overflow-y-auto pr-1">
                            <div v-for="row in topEquiposReportes" :key="row.equipment_id" class="flex items-center gap-3">
                                <span class="text-sm text-gray-800 truncate min-w-0 flex-1 max-w-[220px]" :title="row.nombre">{{ row.nombre }}</span>
                                <div class="flex-1 h-8 bg-gray-100 rounded-lg overflow-hidden min-w-0 max-w-[200px]">
                                    <div class="h-full bg-indigo-500 rounded-lg transition-all" :style="{ width: pctTopEquipos(row) + '%' }"></div>
                                </div>
                                <span class="text-sm font-semibold text-gray-700 w-9 text-right tabular-nums">{{ row.total }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                </div>
            </section>
            </template>

            <!-- Solo indicadores de la empresa seleccionada -->
            <div v-if="selectedCompanyId" class="mt-6">
                <div v-if="loadingDetail" class="flex justify-center py-12">
                    <svg class="animate-spin h-10 w-10 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                </div>
                <template v-else-if="detailData">
                    <h3 class="text-xl font-bold text-gray-800 mb-6 flex items-center">
                        <i class="fas fa-building text-blue-500 mr-2"></i>
                        Indicadores de {{ nombreEmpresaDetalle }}
                    </h3>
                    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 lg:gap-6 mb-8">
                        <div class="bg-white rounded-xl shadow-md border border-gray-100 p-6 flex items-center gap-4">
                            <div class="w-14 h-14 bg-indigo-500 rounded-xl flex items-center justify-center flex-shrink-0"><i class="fas fa-toolbox text-white text-2xl"></i></div>
                            <div><p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Equipos</p><p class="text-2xl font-bold text-gray-900 mt-0.5">{{ detailTotalesEquipos }}</p></div>
                        </div>
                        <div class="bg-white rounded-xl shadow-md border border-gray-100 p-6 flex items-center gap-4">
                            <div class="w-14 h-14 bg-green-500 rounded-xl flex items-center justify-center flex-shrink-0"><i class="fas fa-file-alt text-white text-2xl"></i></div>
                            <div><p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Reportes</p><p class="text-2xl font-bold text-gray-900 mt-0.5">{{ detailTotalesReportes }}</p></div>
                        </div>
                        <div class="bg-white rounded-xl shadow-md border border-gray-100 p-6 flex items-center gap-4">
                            <div class="w-14 h-14 bg-amber-500 rounded-xl flex items-center justify-center flex-shrink-0"><i class="fas fa-calendar-day text-white text-2xl"></i></div>
                            <div><p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Este mes</p><p class="text-2xl font-bold text-gray-900 mt-0.5">{{ detailTotalesEsteMes }}</p></div>
                        </div>
                        <div class="bg-white rounded-xl shadow-md border border-gray-100 p-6 flex items-center gap-4 col-span-2 lg:col-span-1">
                            <div class="w-14 h-14 bg-teal-500 rounded-xl flex items-center justify-center flex-shrink-0"><i class="fas fa-calendar-alt text-white text-2xl"></i></div>
                            <div><p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Este año</p><p class="text-2xl font-bold text-gray-900 mt-0.5">{{ detailTotalesEsteAno }}</p></div>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                        <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">
                            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/80"><h4 class="font-semibold text-gray-800 text-base">Reportes por mes</h4></div>
                            <div class="p-5">
                                <div v-if="detailReportesPorMes.length === 0" class="h-52 flex items-center justify-center text-gray-500 text-sm">Sin datos</div>
                                <div v-else class="flex items-end justify-between gap-2 h-52 pt-2">
                                    <div v-for="row in detailReportesPorMes" :key="`${row.anio}-${row.mes}`" class="flex-1 min-w-0 flex flex-col items-center">
                                        <span class="text-xs text-gray-500 truncate w-full text-center" :title="row.label">{{ row.label }}</span>
                                        <div class="w-full max-w-[24px] mx-auto bg-blue-500 rounded-t-md min-h-[4px]" :style="{ height: detailBarHeightPorMes(row) + '%' }"></div>
                                        <span class="text-xs font-semibold mt-1">{{ row.total }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">
                            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/80"><h4 class="font-semibold text-gray-800 text-base">Reportes por condición</h4></div>
                            <div class="p-5 flex items-center gap-5">
                                <div v-if="detailReportesPorCondicion.length === 0" class="h-40 w-40 flex items-center justify-center text-gray-500 text-sm">Sin datos</div>
                                <template v-else>
                                    <div class="relative w-40 h-40 flex-shrink-0">
                                        <svg viewBox="0 0 100 100" class="w-full h-full -rotate-90">
                                            <circle v-for="slice in detailDonaSlices" :key="slice.key" cx="50" cy="50" r="40" fill="none" :stroke="slice.color" stroke-width="20" :stroke-dasharray="slice.dash" :stroke-dashoffset="slice.offset" />
                                        </svg>
                                        <div class="absolute inset-0 flex items-center justify-center"><span class="text-2xl font-bold text-gray-700">{{ detailTotalCondicion }}</span></div>
                                    </div>
                                    <div class="flex-1 space-y-2 text-sm">
                                        <div v-for="row in detailReportesPorCondicion" :key="row.condition_id || 'sin'" class="flex items-center gap-2">
                                            <span class="w-3.5 h-3.5 rounded-full flex-shrink-0 shadow-sm" :style="{ backgroundColor: row.color || '#94a3b8' }"></span>
                                            <span class="truncate min-w-0 max-w-[180px]" :title="row.nombre">{{ row.nombre }}</span>
                                            <span class="font-semibold tabular-nums">{{ row.total }}</span>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                        <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">
                            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/80"><h4 class="font-semibold text-gray-800 text-base">Reportes por proceso</h4></div>
                            <div class="p-5 space-y-3 max-h-64 overflow-y-auto pr-1">
                                <div v-for="row in detailReportesPorProceso" :key="row.process_id || 'sin'" class="flex items-center gap-3">
                                    <span class="text-sm truncate min-w-0 flex-1 max-w-[200px]" :title="row.nombre">{{ row.nombre }}</span>
                                    <div class="flex-1 h-7 bg-gray-100 rounded-lg min-w-0 max-w-[180px]"><div class="h-full bg-amber-500 rounded-lg" :style="{ width: detailPctProceso(row) + '%' }"></div></div>
                                    <span class="text-sm font-semibold w-8 text-right tabular-nums">{{ row.total }}</span>
                                </div>
                                <p v-if="detailReportesPorProceso.length === 0" class="text-gray-500 text-sm py-4">Sin datos</p>
                            </div>
                        </div>
                        <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">
                            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/80"><h4 class="font-semibold text-gray-800 text-base">Reportes por analista</h4></div>
                            <div class="p-5 space-y-3 max-h-64 overflow-y-auto pr-1">
                                <div v-for="row in detailReportesPorAnalista" :key="row.analist_id || 'sin'" class="flex items-center gap-3">
                                    <span class="text-sm truncate min-w-0 flex-1 max-w-[200px]" :title="row.nombre">{{ row.nombre }}</span>
                                    <div class="flex-1 h-7 bg-gray-100 rounded-lg min-w-0 max-w-[180px]"><div class="h-full bg-green-500 rounded-lg" :style="{ width: detailPctAnalista(row) + '%' }"></div></div>
                                    <span class="text-sm font-semibold w-8 text-right tabular-nums">{{ row.total }}</span>
                                </div>
                                <p v-if="detailReportesPorAnalista.length === 0" class="text-gray-500 text-sm py-4">Sin datos</p>
                            </div>
                        </div>
                        <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">
                            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/80"><h4 class="font-semibold text-gray-800 text-base">Tipo de equipo</h4></div>
                            <div class="p-5 space-y-3 max-h-64 overflow-y-auto pr-1">
                                <div v-for="row in detailReportesPorTipoEquipo" :key="row.type_id || 'sin'" class="flex items-center gap-3">
                                    <span class="text-sm truncate min-w-0 flex-1 max-w-[200px]" :title="row.nombre">{{ row.nombre }}</span>
                                    <div class="flex-1 h-7 bg-gray-100 rounded-lg min-w-0 max-w-[180px]"><div class="h-full bg-violet-500 rounded-lg" :style="{ width: detailPctTipoEquipo(row) + '%' }"></div></div>
                                    <span class="text-sm font-semibold w-8 text-right tabular-nums">{{ row.total }}</span>
                                </div>
                                <p v-if="detailReportesPorTipoEquipo.length === 0" class="text-gray-500 text-sm py-4">Sin datos</p>
                            </div>
                        </div>
                        <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">
                            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/80"><h4 class="font-semibold text-gray-800 text-base">Ubicación</h4></div>
                            <div class="p-5 space-y-3 max-h-64 overflow-y-auto pr-1">
                                <div v-for="row in detailReportesPorUbicacion" :key="row.ubication_id || 'sin'" class="flex items-center gap-3">
                                    <span class="text-sm truncate min-w-0 flex-1 max-w-[200px]" :title="row.nombre">{{ row.nombre }}</span>
                                    <div class="flex-1 h-7 bg-gray-100 rounded-lg min-w-0 max-w-[180px]"><div class="h-full bg-sky-500 rounded-lg" :style="{ width: detailPctUbicacion(row) + '%' }"></div></div>
                                    <span class="text-sm font-semibold w-8 text-right tabular-nums">{{ row.total }}</span>
                                </div>
                                <p v-if="detailReportesPorUbicacion.length === 0" class="text-gray-500 text-sm py-4">Sin datos</p>
                            </div>
                        </div>
                        <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">
                            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/80"><h4 class="font-semibold text-gray-800 text-base">Mecanismo de falla</h4></div>
                            <div class="p-5 space-y-3 max-h-64 overflow-y-auto pr-1">
                                <div v-for="row in detailReportesPorMecanismoFalla" :key="row.mechanism_fail_id || 'sin'" class="flex items-center gap-3">
                                    <span class="text-sm truncate min-w-0 flex-1 max-w-[200px]" :title="row.nombre">{{ row.nombre }}</span>
                                    <div class="flex-1 h-7 bg-gray-100 rounded-lg min-w-0 max-w-[180px]"><div class="h-full bg-rose-500 rounded-lg" :style="{ width: detailPctMecanismoFalla(row) + '%' }"></div></div>
                                    <span class="text-sm font-semibold w-8 text-right tabular-nums">{{ row.total }}</span>
                                </div>
                                <p v-if="detailReportesPorMecanismoFalla.length === 0" class="text-gray-500 text-sm py-4">Sin datos</p>
                            </div>
                        </div>
                        <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">
                            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/80"><h4 class="font-semibold text-gray-800 text-base">Componente en Falla</h4></div>
                            <div class="p-5 space-y-3 max-h-64 overflow-y-auto pr-1">
                                <div v-for="row in detailReportesPorComponenteFalla" :key="row.component_fail_id || 'sin'" class="flex items-center gap-3">
                                    <span class="text-sm truncate min-w-0 flex-1 max-w-[200px]" :title="row.nombre">{{ row.nombre }}</span>
                                    <div class="flex-1 h-7 bg-gray-100 rounded-lg min-w-0 max-w-[180px]"><div class="h-full bg-orange-500 rounded-lg" :style="{ width: detailPctComponenteFalla(row) + '%' }"></div></div>
                                    <span class="text-sm font-semibold w-8 text-right tabular-nums">{{ row.total }}</span>
                                </div>
                                <p v-if="detailReportesPorComponenteFalla.length === 0" class="text-gray-500 text-sm py-4">Sin datos</p>
                            </div>
                        </div>
                        <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden md:col-span-2 xl:col-span-1">
                            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/80"><h4 class="font-semibold text-gray-800 text-base">Equipos con más reportes</h4></div>
                            <div class="p-5 space-y-3 max-h-64 overflow-y-auto pr-1">
                                <div v-for="row in detailTopEquiposReportes" :key="row.equipment_id" class="flex items-center gap-3">
                                    <span class="text-sm truncate min-w-0 flex-1 max-w-[200px]" :title="row.nombre">{{ row.nombre }}</span>
                                    <div class="flex-1 h-7 bg-gray-100 rounded-lg min-w-0 max-w-[180px]"><div class="h-full bg-indigo-500 rounded-lg" :style="{ width: detailPctTopEquipos(row) + '%' }"></div></div>
                                    <span class="text-sm font-semibold w-8 text-right tabular-nums">{{ row.total }}</span>
                                </div>
                                <p v-if="detailTopEquiposReportes.length === 0" class="text-gray-500 text-sm py-4">Sin datos</p>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue';
import { useRouter } from 'vue-router';
import api from '../services/api';
import { useAuthStore } from '../stores/auth';
import Select2 from '../components/Select2.vue';

const router = useRouter();
const authStore = useAuthStore();
const loading = ref(true);
const data = ref({});
const companies = ref([]);
const processes = ref([]);
const selectedCompanyId = ref(null);
const selectedProcessId = ref(null);
const detailData = ref(null);
const loadingDetail = ref(false);

const totales = computed(() => {
    const t = data.value.totales;
    return {
        empresas: t?.empresas ?? 0,
        equipos: t?.equipos ?? 0,
        reportes: t?.reportes ?? 0,
        reportes_este_mes: t?.reportes_este_mes ?? 0,
        reportes_este_ano: t?.reportes_este_ano ?? 0,
    };
});
const reportesPorMes = computed(() => data.value.reportes_por_mes ?? []);
const reportesPorCondicion = computed(() => data.value.reportes_por_condicion ?? []);
const reportesPorEmpresa = computed(() => data.value.reportes_por_empresa ?? []);
const reportesPorAnalista = computed(() => data.value.reportes_por_analista ?? []);
const reportesPorProceso = computed(() => data.value.reportes_por_proceso ?? []);
const reportesPorTipoEquipo = computed(() => data.value.reportes_por_tipo_equipo ?? []);
const reportesPorUbicacion = computed(() => data.value.reportes_por_ubicacion ?? []);
const reportesPorMecanismoFalla = computed(() => data.value.reportes_por_mecanismo_falla ?? []);
const reportesPorComponenteFalla = computed(() => data.value.reportes_por_componente_falla ?? []);
const topEquiposReportes = computed(() => data.value.top_equipos_reportes ?? []);
const equiposPorEmpresa = computed(() => data.value.equipos_por_empresa ?? []);

const maxPorMes = computed(() => {
    if (reportesPorMes.value.length === 0) return 1;
    return Math.max(1, ...reportesPorMes.value.map((r) => r.total));
});
function barHeightPorMes(row) {
    return maxPorMes.value ? Math.max(5, (row.total / maxPorMes.value) * 100) : 0;
}

const totalCondicion = computed(() => reportesPorCondicion.value.reduce((s, r) => s + r.total, 0));
const donaCondicionSlices = computed(() => {
    const total = totalCondicion.value;
    const rows = reportesPorCondicion.value;
    if (total === 0 || rows.length === 0) return [];
    const colors = ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#ec4899', '#06b6d4', '#84cc16'];
    const circumference = 2 * Math.PI * 40;
    let offset = 0;
    let accumulated = 0;
    return rows.map((row, idx) => {
        const isLast = idx === rows.length - 1;
        const length = isLast
            ? Math.max(0, circumference - accumulated)
            : (row.total / total) * circumference;
        accumulated += length;
        const dash = `${length} ${circumference}`;
        const color = row.color || colors[idx % colors.length];
        const currentOffset = offset;
        offset -= length;
        return {
            key: `cond-${row.condition_id ?? 'sin'}-${idx}`,
            color,
            dash,
            offset: currentOffset,
        };
    });
});

const maxEmpresa = computed(() => {
    if (reportesPorEmpresa.value.length === 0) return 1;
    return Math.max(1, ...reportesPorEmpresa.value.map((r) => r.total));
});
const maxAnalista = computed(() => {
    if (reportesPorAnalista.value.length === 0) return 1;
    return Math.max(1, ...reportesPorAnalista.value.map((r) => r.total));
});
const maxProceso = computed(() => {
    if (reportesPorProceso.value.length === 0) return 1;
    return Math.max(1, ...reportesPorProceso.value.map((r) => r.total));
});
const maxEquiposEmpresa = computed(() => {
    if (equiposPorEmpresa.value.length === 0) return 1;
    return Math.max(1, ...equiposPorEmpresa.value.map((r) => r.total));
});
const maxTipoEquipo = computed(() => (reportesPorTipoEquipo.value.length === 0 ? 1 : Math.max(1, ...reportesPorTipoEquipo.value.map((r) => r.total))));
const maxUbicacion = computed(() => (reportesPorUbicacion.value.length === 0 ? 1 : Math.max(1, ...reportesPorUbicacion.value.map((r) => r.total))));
const maxMecanismoFalla = computed(() => (reportesPorMecanismoFalla.value.length === 0 ? 1 : Math.max(1, ...reportesPorMecanismoFalla.value.map((r) => r.total))));
const maxComponenteFalla = computed(() => (reportesPorComponenteFalla.value.length === 0 ? 1 : Math.max(1, ...reportesPorComponenteFalla.value.map((r) => r.total))));
const maxTopEquipos = computed(() => (topEquiposReportes.value.length === 0 ? 1 : Math.max(1, ...topEquiposReportes.value.map((r) => r.total))));

function pctEmpresa(row) {
    return maxEmpresa.value ? Math.round((row.total / maxEmpresa.value) * 100) : 0;
}
function pctAnalista(row) {
    return maxAnalista.value ? Math.round((row.total / maxAnalista.value) * 100) : 0;
}
function pctProceso(row) {
    return maxProceso.value ? Math.round((row.total / maxProceso.value) * 100) : 0;
}
function pctEquiposEmpresa(row) {
    return maxEquiposEmpresa.value ? Math.round((row.total / maxEquiposEmpresa.value) * 100) : 0;
}
function pctTipoEquipo(row) { return maxTipoEquipo.value ? Math.round((row.total / maxTipoEquipo.value) * 100) : 0; }
function pctUbicacion(row) { return maxUbicacion.value ? Math.round((row.total / maxUbicacion.value) * 100) : 0; }
function pctMecanismoFalla(row) { return maxMecanismoFalla.value ? Math.round((row.total / maxMecanismoFalla.value) * 100) : 0; }
function pctComponenteFalla(row) { return maxComponenteFalla.value ? Math.round((row.total / maxComponenteFalla.value) * 100) : 0; }
function pctTopEquipos(row) { return maxTopEquipos.value ? Math.round((row.total / maxTopEquipos.value) * 100) : 0; }

// Detalle por empresa
const nombreEmpresaDetalle = computed(() => (detailData.value && detailData.value.company_selected && detailData.value.company_selected.name) || 'Empresa');
const detailTotalesEquipos = computed(() => (detailData.value && detailData.value.totales && detailData.value.totales.equipos) ?? 0);
const detailTotalesReportes = computed(() => (detailData.value && detailData.value.totales && detailData.value.totales.reportes) ?? 0);
const detailTotalesEsteMes = computed(() => (detailData.value && detailData.value.totales && detailData.value.totales.reportes_este_mes) ?? 0);
const detailTotalesEsteAno = computed(() => (detailData.value && detailData.value.totales && detailData.value.totales.reportes_este_ano) ?? 0);
const detailReportesPorMes = computed(() => detailData.value?.reportes_por_mes ?? []);
const detailReportesPorCondicion = computed(() => detailData.value?.reportes_por_condicion ?? []);
const detailReportesPorProceso = computed(() => detailData.value?.reportes_por_proceso ?? []);
const detailReportesPorAnalista = computed(() => detailData.value?.reportes_por_analista ?? []);
const detailReportesPorTipoEquipo = computed(() => detailData.value?.reportes_por_tipo_equipo ?? []);
const detailReportesPorUbicacion = computed(() => detailData.value?.reportes_por_ubicacion ?? []);
const detailReportesPorMecanismoFalla = computed(() => detailData.value?.reportes_por_mecanismo_falla ?? []);
const detailReportesPorComponenteFalla = computed(() => detailData.value?.reportes_por_componente_falla ?? []);
const detailTopEquiposReportes = computed(() => detailData.value?.top_equipos_reportes ?? []);
const detailTotalCondicion = computed(() => detailReportesPorCondicion.value.reduce((s, r) => s + r.total, 0));
const detailMaxPorMes = computed(() => (detailReportesPorMes.value.length === 0 ? 1 : Math.max(1, ...detailReportesPorMes.value.map((r) => r.total))));
const detailMaxProceso = computed(() => (detailReportesPorProceso.value.length === 0 ? 1 : Math.max(1, ...detailReportesPorProceso.value.map((r) => r.total))));
const detailMaxAnalista = computed(() => (detailReportesPorAnalista.value.length === 0 ? 1 : Math.max(1, ...detailReportesPorAnalista.value.map((r) => r.total))));
const detailMaxTipoEquipo = computed(() => (detailReportesPorTipoEquipo.value.length === 0 ? 1 : Math.max(1, ...detailReportesPorTipoEquipo.value.map((r) => r.total))));
const detailMaxUbicacion = computed(() => (detailReportesPorUbicacion.value.length === 0 ? 1 : Math.max(1, ...detailReportesPorUbicacion.value.map((r) => r.total))));
const detailMaxMecanismoFalla = computed(() => (detailReportesPorMecanismoFalla.value.length === 0 ? 1 : Math.max(1, ...detailReportesPorMecanismoFalla.value.map((r) => r.total))));
const detailMaxComponenteFalla = computed(() => (detailReportesPorComponenteFalla.value.length === 0 ? 1 : Math.max(1, ...detailReportesPorComponenteFalla.value.map((r) => r.total))));
const detailMaxTopEquipos = computed(() => (detailTopEquiposReportes.value.length === 0 ? 1 : Math.max(1, ...detailTopEquiposReportes.value.map((r) => r.total))));

function detailBarHeightPorMes(row) {
    return detailMaxPorMes.value ? Math.max(5, (row.total / detailMaxPorMes.value) * 100) : 0;
}
function detailPctProceso(row) {
    return detailMaxProceso.value ? Math.round((row.total / detailMaxProceso.value) * 100) : 0;
}
function detailPctAnalista(row) {
    return detailMaxAnalista.value ? Math.round((row.total / detailMaxAnalista.value) * 100) : 0;
}
function detailPctTipoEquipo(row) { return detailMaxTipoEquipo.value ? Math.round((row.total / detailMaxTipoEquipo.value) * 100) : 0; }
function detailPctUbicacion(row) { return detailMaxUbicacion.value ? Math.round((row.total / detailMaxUbicacion.value) * 100) : 0; }
function detailPctMecanismoFalla(row) { return detailMaxMecanismoFalla.value ? Math.round((row.total / detailMaxMecanismoFalla.value) * 100) : 0; }
function detailPctComponenteFalla(row) { return detailMaxComponenteFalla.value ? Math.round((row.total / detailMaxComponenteFalla.value) * 100) : 0; }
function detailPctTopEquipos(row) { return detailMaxTopEquipos.value ? Math.round((row.total / detailMaxTopEquipos.value) * 100) : 0; }

const detailDonaSlices = computed(() => {
    const total = detailTotalCondicion.value;
    const rows = detailReportesPorCondicion.value;
    if (total === 0 || rows.length === 0) return [];
    const colors = ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#ec4899', '#06b6d4', '#84cc16'];
    const circumference = 2 * Math.PI * 40;
    let offset = 0;
    let accumulated = 0;
    return rows.map((row, idx) => {
        const isLast = idx === rows.length - 1;
        const length = isLast ? Math.max(0, circumference - accumulated) : (row.total / total) * circumference;
        accumulated += length;
        const dash = `${length} ${circumference}`;
        const color = row.color || colors[idx % colors.length];
        const currentOffset = offset;
        offset -= length;
        return {
            key: `detail-cond-${row.condition_id ?? 'sin'}-${idx}`,
            color,
            dash,
            offset: currentOffset,
        };
    });
});

function buildParams() {
    const p = {};
    if (selectedCompanyId.value) p.company_id = selectedCompanyId.value;
    if (selectedProcessId.value) p.process_id = selectedProcessId.value;
    return p;
}

async function loadIndicadores() {
    try {
        const response = await api.get('/indicadores', { params: buildParams() });
        data.value = response.data.data ?? {};
    } catch (error) {
        console.error('Error al cargar indicadores:', error);
    }
}

async function loadDetail() {
    if (!selectedCompanyId.value) {
        detailData.value = null;
        return;
    }
    loadingDetail.value = true;
    try {
        const response = await api.get('/indicadores', { params: buildParams() });
        detailData.value = response.data.data ?? {};
    } catch (error) {
        console.error('Error al cargar detalle por empresa:', error);
        detailData.value = null;
    } finally {
        loadingDetail.value = false;
    }
}

watch(selectedCompanyId, () => {
    loadDetail();
    if (!selectedCompanyId.value) loadIndicadores();
});
watch(selectedProcessId, () => {
    loadIndicadores();
    if (selectedCompanyId.value) loadDetail();
});

onMounted(async () => {
    if (!authStore.hasPermission('consult indicators')) {
        router.push({ name: 'dashboard' });
        return;
    }
    if (authStore.hasRole('Cliente') && authStore.user?.company?.id) {
        selectedCompanyId.value = authStore.user.company.id;
    }
    try {
        const [resIndicadores, resCompanies, resProcesses] = await Promise.all([
            api.get('/indicadores', { params: buildParams() }),
            authStore.hasRole('Cliente') ? Promise.resolve({ data: { data: authStore.user?.company ? [authStore.user.company] : [] } }) : api.get('/catalogs/companies').catch(() => ({ data: { data: [] } })),
            api.get('/catalogs/processes').catch(() => ({ data: { data: [] } })),
        ]);
        data.value = resIndicadores.data.data ?? {};
        companies.value = resCompanies.data?.data ?? resCompanies.data ?? [];
        processes.value = resProcesses.data?.data ?? resProcesses.data ?? [];
    } catch (error) {
        console.error('Error al cargar indicadores:', error);
    } finally {
        loading.value = false;
    }
    if (selectedCompanyId.value) loadDetail();
});
</script>
