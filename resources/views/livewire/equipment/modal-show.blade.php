<div wire:ignore.self id="modalShow" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed inset-0 z-50 flex items-center justify-center">
    <!-- Overlay con backdrop blur -->
    <div class="fixed inset-0 bg-gray-900 bg-opacity-75 backdrop-blur-sm transition-opacity z-40"
         onclick="document.getElementById('modalShow').classList.add('hidden')"></div>
    
    <div wire:loading wire:target="saveCompany,updateEquipment"
        class="hidden overflow-y-auto overflow-x-hidden fixed inset-0 z-60 flex items-center justify-center">
        <div class="flex flex-col items-center justify-center h-full">
            <svg class="animate-spin h-16 w-16 text-blue-600 mr-3 mb-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="10" stroke-opacity="0.25" stroke-width="2" stroke="currentColor" fill="none"></circle>
                <path d="M12 2a10 10 0 00-9.5 7.5M12 2c2.5 0 5 1 7 2.5" stroke="currentColor" stroke-width="2" fill="none"></path>
            </svg>
            <p class="text-gray-700 font-medium">Cargando...</p>
        </div>
    </div>
    <div class="relative p-4 w-full max-w-4xl z-50">
        <!-- Modal content -->
        <div class="relative bg-white rounded-xl shadow-2xl dark:bg-gray-800 overflow-hidden">
            <!-- Modal header con gradiente -->
            <div class="flex items-center justify-between px-6 py-4 bg-gradient-to-r from-blue-500 to-blue-600 rounded-t-xl">
                <div class="flex items-center space-x-3">
                    <div class="bg-white bg-opacity-20 rounded-lg p-2">
                        <i class="fas fa-cog text-white text-xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-white">
                        Detalle del Equipo
                    </h3>
                </div>
                <button wire:click="resetForm" type="button" id="closeModalShowButton" 
                        class="text-white hover:bg-white hover:bg-opacity-20 rounded-lg text-sm w-9 h-9 inline-flex justify-center items-center transition-colors duration-200">
                    <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                    </svg>
                    <span class="sr-only">Close modal</span>
                </button>
            </div>
            
            <!-- Modal body -->
            <div class="overflow-y-auto" style="max-height: calc(90vh - 80px)">
                <div class="p-6">
                    <div class="mb-6">
                        <div class="flex items-center mb-4 pb-2 border-b border-gray-200">
                            <i class="fas fa-info-circle text-blue-600 mr-2"></i>
                            <h4 class="text-lg font-semibold text-gray-800 dark:text-white">Información del Equipo</h4>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                <label class="flex items-center mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                    <i class="fas fa-tag mr-2 text-blue-600"></i>
                                    Nombre
                                </label>
                                <div class="text-sm font-semibold text-gray-900 dark:text-white bg-white dark:bg-gray-600 px-4 py-3 rounded-lg border border-gray-200 dark:border-gray-500">
                                    {{ $name }}
                                </div>
                            </div>
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                <label class="flex items-center mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                    <i class="fas fa-barcode mr-2 text-blue-600"></i>
                                    Código
                                </label>
                                <div class="text-sm font-semibold text-gray-900 dark:text-white bg-white dark:bg-gray-600 px-4 py-3 rounded-lg border border-gray-200 dark:border-gray-500">
                                    {{ $code }}
                                </div>
                            </div>
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                <label class="flex items-center mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                    <i class="fas fa-tag mr-2 text-blue-600"></i>
                                    Tag
                                </label>
                                <div class="text-sm font-semibold text-gray-900 dark:text-white bg-white dark:bg-gray-600 px-4 py-3 rounded-lg border border-gray-200 dark:border-gray-500">
                                    {{ $tag }}
                                </div>
                            </div>
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                <label class="flex items-center mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                    <i class="fas fa-building mr-2 text-blue-600"></i>
                                    Empresa
                                </label>
                                <div class="text-sm font-semibold text-gray-900 dark:text-white bg-white dark:bg-gray-600 px-4 py-3 rounded-lg border border-gray-200 dark:border-gray-500">
                                    {{ optional($companies->find($company))->name ?? 'N/A' }}
                                </div>
                            </div>
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 md:col-span-2">
                                <label class="flex items-center mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                    <i class="fas fa-align-left mr-2 text-blue-600"></i>
                                    Descripción
                                </label>
                                <div class="text-sm text-gray-900 dark:text-white bg-white dark:bg-gray-600 px-4 py-3 rounded-lg border border-gray-200 dark:border-gray-500 min-h-[80px]">
                                    {{ $tag ?? 'Sin descripción' }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="flex justify-end px-6 py-4 bg-gray-50 dark:bg-gray-700 border-t border-gray-200 dark:border-gray-600">
                <button wire:click="resetForm" type="button" id="closeModalShowButton2" 
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
