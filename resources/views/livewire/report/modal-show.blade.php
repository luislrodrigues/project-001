<div wire:ignore.self id="modalShow" tabindex="-1" aria-hidden="true"
    class="hidden overflow-hidden fixed inset-0 z-50 flex items-center justify-center">
    <!-- Overlay con backdrop blur -->
    <div class="fixed inset-0 bg-gray-900 bg-opacity-75 backdrop-blur-sm transition-opacity z-40"
         onclick="document.getElementById('modalShow').classList.add('hidden')"></div>
    
    <div wire:loading wire:target="saveCompany,updateEquipment,sendEmailReport" 
         class="hidden overflow-y-auto overflow-x-hidden fixed inset-0 z-60 flex items-center justify-center">
        <div class="flex flex-col items-center justify-center h-full">
            <svg class="animate-spin h-16 w-16 text-blue-600 mr-3 mb-2" xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="10" stroke-opacity="0.25" stroke-width="2" stroke="currentColor"
                    fill="none"></circle>
                <path d="M12 2a10 10 0 00-9.5 7.5M12 2c2.5 0 5 1 7 2.5" stroke="currentColor" stroke-width="2"
                    fill="none"></path>
            </svg>
            <p class="text-gray-700 font-medium">Cargando...</p>
        </div>
    </div>
    <div class="relative p-4 w-full max-w-5xl z-50">
        <div class="relative bg-white rounded-xl shadow-2xl dark:bg-gray-800 overflow-hidden" style="max-height:90vh">
            <!-- Modal header con gradiente -->
            <div class="flex items-center justify-between px-6 py-4 bg-gradient-to-r from-blue-500 to-blue-600 rounded-t-xl">
                <div class="flex items-center space-x-3">
                    <div class="bg-white bg-opacity-20 rounded-lg p-2">
                        <i class="fas fa-file-alt text-white text-xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-white">
                        Detalle del Reporte
                    </h3>
                </div>
                <button wire:click="resetForm" type="button" id="closeModalShowButton"
                    class="text-white hover:bg-white hover:bg-opacity-20 rounded-lg text-sm w-9 h-9 inline-flex justify-center items-center transition-colors duration-200">
                    <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                    </svg>
                    <span class="sr-only">Close modal</span>
                </button>
            </div>
            
            <div class="overflow-y-auto" style="max-height: calc(90vh - 80px)">
                <div class="p-6">
                    {{-- Información General --}}
                    <div class="mb-8">
                        <div class="flex items-center mb-4 pb-2 border-b border-gray-200">
                            <i class="fas fa-info-circle text-blue-600 mr-2"></i>
                            <h4 class="text-lg font-semibold text-gray-800 dark:text-white">Información General</h4>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                <label class="flex items-center mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                    <i class="fas fa-cog mr-2 text-blue-600"></i>
                                    Equipo
                                </label>
                                <div class="text-sm font-semibold text-gray-900 dark:text-white bg-white dark:bg-gray-600 px-4 py-3 rounded-lg border border-gray-200 dark:border-gray-500">
                                    {{ $equipment_name }}
                                </div>
                            </div>
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                <label class="flex items-center mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                    <i class="fas fa-calendar mr-2 text-blue-600"></i>
                                    Fecha Medición
                                </label>
                                <div class="text-sm font-semibold text-gray-900 dark:text-white bg-white dark:bg-gray-600 px-4 py-3 rounded-lg border border-gray-200 dark:border-gray-500">
                                    {{ $measurement_date }}
                                </div>
                            </div>
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                <label class="flex items-center mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                    <i class="fas fa-map-marker-alt mr-2 text-blue-600"></i>
                                    Ubicación
                                </label>
                                <div class="text-sm font-semibold text-gray-900 dark:text-white bg-white dark:bg-gray-600 px-4 py-3 rounded-lg border border-gray-200 dark:border-gray-500">
                                    {{ $location }}
                                </div>
                            </div>
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                <label class="flex items-center mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                    <i class="fas fa-tasks mr-2 text-blue-600"></i>
                                    Proceso
                                </label>
                                <div class="text-sm font-semibold text-gray-900 dark:text-white bg-white dark:bg-gray-600 px-4 py-3 rounded-lg border border-gray-200 dark:border-gray-500">
                                    {{ $process }}
                                </div>
                            </div>
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                <label class="flex items-center mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                    <i class="fas fa-exclamation-triangle mr-2 text-blue-600"></i>
                                    Condición
                                </label>
                                <div class="text-sm font-semibold text-gray-900 dark:text-white bg-white dark:bg-gray-600 px-4 py-3 rounded-lg border border-gray-200 dark:border-gray-500">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium 
                                        @if($condition_name == 'Alarma') bg-yellow-100 text-yellow-800 border border-yellow-300
                                        @elseif($condition_name == 'Critico') bg-red-100 text-red-800 border border-red-300
                                        @elseif($condition_name == 'Peligro') bg-orange-100 text-orange-800 border border-orange-300
                                        @else bg-blue-100 text-blue-800 border border-blue-300
                                        @endif">
                                        {{ $condition_name }}
                                    </span>
                                </div>
                            </div>
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                <label class="flex items-center mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                    <i class="fas fa-flag mr-2 text-blue-600"></i>
                                    Prioridad
                                </label>
                                <div class="text-sm font-semibold text-gray-900 dark:text-white bg-white dark:bg-gray-600 px-4 py-3 rounded-lg border border-gray-200 dark:border-gray-500">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium 
                                        @if($priority == 'Alta') bg-red-100 text-red-800 border border-red-300
                                        @else bg-yellow-100 text-yellow-800 border border-yellow-300
                                        @endif">
                                        {{ $priority }}
                                    </span>
                                </div>
                            </div>
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                <label class="flex items-center mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                    <i class="fas fa-wrench mr-2 text-blue-600"></i>
                                    Mecanismo Falla
                                </label>
                                <div class="text-sm font-semibold text-gray-900 dark:text-white bg-white dark:bg-gray-600 px-4 py-3 rounded-lg border border-gray-200 dark:border-gray-500">
                                    {{ $failure_mechanism_name ?? 'N/A' }}
                                </div>
                            </div>
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                <label class="flex items-center mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                    <i class="fas fa-cog mr-2 text-blue-600"></i>
                                    Componente Falla
                                </label>
                                <div class="text-sm font-semibold text-gray-900 dark:text-white bg-white dark:bg-gray-600 px-4 py-3 rounded-lg border border-gray-200 dark:border-gray-500">
                                    {{ $component_failure_name ?? 'N/A' }}
                                </div>
                            </div>
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                <label class="flex items-center mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                    <i class="fas fa-user mr-2 text-blue-600"></i>
                                    Analista
                                </label>
                                <div class="text-sm font-semibold text-gray-900 dark:text-white bg-white dark:bg-gray-600 px-4 py-3 rounded-lg border border-gray-200 dark:border-gray-500">
                                    {{ $analist }}
                                </div>
                            </div>
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                <label class="flex items-center mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                    <i class="fas fa-check-circle mr-2 text-blue-600"></i>
                                    OT Creada
                                </label>
                                <div class="text-sm font-semibold text-gray-900 dark:text-white bg-white dark:bg-gray-600 px-4 py-3 rounded-lg border border-gray-200 dark:border-gray-500">
                                    {{ $ot_created ?? 'No' }}
                                </div>
                            </div>
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                <label class="flex items-center mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                    <i class="fas fa-info-circle mr-2 text-blue-600"></i>
                                    Estado de OT
                                </label>
                                <div class="text-sm font-semibold text-gray-900 dark:text-white bg-white dark:bg-gray-600 px-4 py-3 rounded-lg border border-gray-200 dark:border-gray-500">
                                    {{ $status_ot ?? 'N/A' }}
                                </div>
                            </div>
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                <label class="flex items-center mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                    <i class="fas fa-calendar-check mr-2 text-blue-600"></i>
                                    Fecha Ejecución
                                </label>
                                <div class="text-sm font-semibold text-gray-900 dark:text-white bg-white dark:bg-gray-600 px-4 py-3 rounded-lg border border-gray-200 dark:border-gray-500">
                                    {{ $execution_date ?? 'N/A' }}
                                </div>
                            </div>
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 md:col-span-3">
                                <label class="flex items-center mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                    <i class="fas fa-check-double mr-2 text-blue-600"></i>
                                    Verificación Recomendación
                                </label>
                                <div class="text-sm text-gray-900 dark:text-white bg-white dark:bg-gray-600 px-4 py-3 rounded-lg border border-gray-200 dark:border-gray-500 min-h-[60px]">
                                    {{ $verification_recommendation ?? 'N/A' }}
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Imágenes --}}
                    @if($image_1 || $image_2)
                    <div class="mb-8">
                        <div class="flex items-center mb-4 pb-2 border-b border-gray-200">
                            <i class="fas fa-images text-blue-600 mr-2"></i>
                            <h4 class="text-lg font-semibold text-gray-800 dark:text-white">Imágenes Adjuntas</h4>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @if ($image_1)
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                <label class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                    <i class="fas fa-image mr-2 text-blue-600"></i>
                                    Imagen 1
                                </label>
                                <a href="{{ asset('storage/' . $image_1) }}" download target="_blank"
                                    class="inline-flex items-center px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg transition-colors duration-200">
                                    <i class="fas fa-download mr-2"></i>
                                    Descargar
                                </a>
                            </div>
                            @endif
                            @if ($image_2)
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                <label class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                    <i class="fas fa-image mr-2 text-blue-600"></i>
                                    Imagen 2
                                </label>
                                <a href="{{ asset('storage/' . $image_2) }}" download target="_blank"
                                    class="inline-flex items-center px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg transition-colors duration-200">
                                    <i class="fas fa-download mr-2"></i>
                                    Descargar
                                </a>
                            </div>
                            @endif
                        </div>
                    </div>
                    @endif

                    {{-- Diagnósticos y Recomendaciones --}}
                    @if(count($components) > 0)
                    <div class="mb-8">
                        <div class="flex items-center mb-4 pb-2 border-b border-gray-200">
                            <i class="fas fa-stethoscope text-blue-600 mr-2"></i>
                            <h4 class="text-lg font-semibold text-gray-800 dark:text-white">Diagnósticos y Recomendaciones</h4>
                        </div>
                        <div class="space-y-6">
                            @foreach ($components as $index => $component)
                                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-5 border border-gray-200 dark:border-gray-600">
                                    <div class="flex items-center mb-4">
                                        <div class="bg-blue-100 dark:bg-blue-900 rounded-full w-8 h-8 flex items-center justify-center mr-3">
                                            <span class="text-blue-600 dark:text-blue-300 font-semibold text-sm">{{ $index + 1 }}</span>
                                        </div>
                                        <h5 class="text-md font-semibold text-gray-800 dark:text-white">
                                            {{ $component->componentType->name }}
                                        </h5>
                                    </div>
                                    
                                    <div class="mb-4">
                                        <label class="flex items-center mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                            <i class="fas fa-clipboard-list mr-2 text-blue-600"></i>
                                            Diagnóstico
                                        </label>
                                        <textarea id="diagnostic_{{ $component->id }}"
                                                disabled
                                                class="bg-white dark:bg-gray-600 border border-gray-300 dark:border-gray-500 text-gray-900 dark:text-white text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full px-4 py-3 min-h-[100px]"
                                                placeholder="Sin diagnóstico"
                                                wire:model.defer="diagnostics.{{ $component->id }}"></textarea>
                                    </div>

                                    @if (isset($recommendations[$component->id]) && is_array($recommendations[$component->id]) && !empty($recommendations[$component->id]))
                                    <div>
                                        <label class="flex items-center mb-3 text-sm font-medium text-gray-700 dark:text-gray-300">
                                            <i class="fas fa-lightbulb mr-2 text-yellow-600"></i>
                                            Recomendaciones ({{ count($recommendations[$component->id]) }})
                                        </label>
                                        <div class="space-y-2">
                                            @foreach ($recommendations[$component->id] as $recIndex => $recommendation)
                                                <div class="bg-white dark:bg-gray-600 rounded-lg p-3 border border-gray-200 dark:border-gray-500">
                                                    <div class="flex items-start">
                                                        <span class="flex-shrink-0 bg-yellow-100 text-yellow-800 rounded-full w-6 h-6 flex items-center justify-center text-xs font-semibold mr-3 mt-0.5">
                                                            {{ $recIndex + 1 }}
                                                        </span>
                                                        <input type="text"
                                                            disabled
                                                            id="recommendation_{{ $component->id }}_{{ $recIndex }}"
                                                            class="bg-transparent border-none text-gray-900 dark:text-white text-sm w-full focus:outline-none"
                                                            value="{{ $recommendation }}"
                                                            wire:model.defer="recommendations.{{ $component->id }}.{{ $recIndex }}">
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Footer -->
            <div class="flex justify-between items-center px-6 py-4 bg-gray-50 dark:bg-gray-700 border-t border-gray-200 dark:border-gray-600">
                <button type="button" wire:click="sendEmailReport" 
                        class="flex items-center px-5 py-2.5 bg-green-500 hover:bg-green-600 text-white rounded-lg transition-colors duration-200 font-medium shadow-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                    <i class="fas fa-envelope mr-2"></i>
                    Enviar Notificación
                </button>
                <button type="button" wire:click="resetForm" id="closeModalShowButton2"
                    class="flex items-center px-6 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition-colors duration-200 font-medium">
                    <i class="fas fa-times mr-2"></i>
                    Cerrar
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const modalShow = document.getElementById('modalShow');
            const closeModalShowButton = document.getElementById('closeModalShowButton');
            const closeModalShowButton2 = document.getElementById('closeModalShowButton2');
            
            if (!modalShow) return;

            if (closeModalShowButton) {
                closeModalShowButton.addEventListener('click', function() {
                    if (modalShow) {
                        modalShow.classList.add('hidden');
                    }
                });
            }
            
            if (closeModalShowButton2) {
                closeModalShowButton2.addEventListener('click', function() {
                    if (modalShow) {
                        modalShow.classList.add('hidden');
                    }
                });
            }
        });
    </script>
@endpush
