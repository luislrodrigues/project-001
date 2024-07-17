<div wire:ignore.self id="modalShow" tabindex="-1" aria-hidden="true"
    class="hidden overflow-hidden fixed inset-0 z-50 flex items-center justify-center">
    <div wire:loading wire:target="saveCompany,updateEquipment">
        <div class="flex flex-col items-center justify-center h-full">
            <svg class="animate-spin h-16 w-16 text-blue-600 mr-3 mb-2" xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="10" stroke-opacity="0.25" stroke-width="2" stroke="currentColor"
                    fill="none"></circle>
                <path d="M12 2a10 10 0 00-9.5 7.5M12 2c2.5 0 5 1 7 2.5" stroke="currentColor" stroke-width="2"
                    fill="none"></path>
            </svg>
        </div>
    </div>
    <div class="relative p-4 w-full max-w-4xl">
        <div class="relative bg-white rounded-lg shadow dark:bg-gray-700 overflow-y-auto" style="max-height:85vh">
            <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                    {{ $reportIdToUpdate ? 'Editar reporte' : 'Crear nuevo reporte' }}
                </h3>
                <button wire:click="resetForm" type="button" id="closeModalShowButton"
                    class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white">
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                    </svg>
                    <span class="sr-only">Close modal</span>
                </button>
            </div>
            <div class="p-4 md:p-5">
                <div class="grid grid-cols-3 gap-4 mb-2">
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Equipo</label>
                        <span
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 p-2.5 rounded-lg bg-blue-50 dark:bg-blue-100">{{ $equipment->name }}</span>
                    </div>
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Fecha
                            medición</label>
                        <span
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 p-2.5 rounded-lg bg-blue-50 dark:bg-blue-100">{{ $measurement_date }}</span>
                    </div>
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Ubicación</label>
                        <span
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 p-2.5 rounded-lg bg-blue-50 dark:bg-blue-100">{{ $location }}</span>
                    </div>
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Proceso</label>
                        <span
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 p-2.5 rounded-lg bg-blue-50 dark:bg-blue-100">{{ $process }}</span>
                    </div>
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Condición</label>
                        <span
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 p-2.5 rounded-lg bg-blue-50 dark:bg-blue-100">{{ $condition }}</span>
                    </div>
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Mecanismo
                            falla</label>
                        <span
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 p-2.5 rounded-lg bg-blue-50 dark:bg-blue-100">{{ $failure_mechanism }}</span>
                    </div>
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Componente
                            falla</label>
                        <span
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 p-2.5 rounded-lg bg-blue-50 dark:bg-blue-100">{{ $component_failure }}</span>
                    </div>
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Prioridad</label>
                        <span
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 p-2.5 rounded-lg bg-blue-50 dark:bg-blue-100">{{ $priority }}</span>
                    </div>
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">OT creada</label>
                        <span
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 p-2.5 rounded-lg bg-blue-50 dark:bg-blue-100">{{ $ot_created }}</span>
                    </div>
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Estado de OT</label>
                        <span
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 p-2.5 rounded-lg bg-blue-50 dark:bg-blue-100">{{ $status_ot }}</span>
                    </div>
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Fecha de
                            ejecución</label>
                        <span
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 p-2.5 rounded-lg bg-blue-50 dark:bg-blue-100">{{ $execution_date }}</span>
                    </div>
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Analista</label>
                        <span
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 p-2.5 rounded-lg bg-blue-50 dark:bg-blue-100">{{ $analist }}</span>
                    </div>
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Verificación
                            recomendación</label>
                        <span
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 p-2.5 rounded-lg bg-blue-50 dark:bg-blue-100">{{ $verification_recommendation }}</span>
                    </div>
                    @if ($image_1)
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Imagen 1</label>
                            <a href="{{ asset('storage/' . $image_1) }}" download
                                class="text-blue-600 hover:underline flex items-center">
                                <i class="fas fa-download mr-2"></i> Descargar
                            </a>
                        </div>
                    @endif
                    @if ($image_2)
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Imagen 2</label>
                            <a href="{{ asset('storage/' . $image_2) }}" download
                                class="text-blue-600 hover:underline flex items-center">
                                <i class="fas fa-download mr-2"></i> Descargar
                            </a>
                        </div>
                    @endif
                    <div class="col-span-3">
                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Diagnóstico</label>
                        <span
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 p-2.5 rounded-lg bg-blue-50 dark:bg-blue-100">{{ $diagnosis }}</span>
                    </div>
                </div>
                <div class="grid mb-2">
                    <div>
                        <label
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Recomendaciónes</label>
                        @foreach ($recommendations as $index => $recommendation)
                            <span
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 p-2.5 rounded-lg bg-blue-50 dark:bg-blue-100">{{ $index + 1 }}.
                                {{ $recommendation }}</span>
                        @endforeach
                    </div>
                </div>
                <div class="flex justify-between">
                    <button type="button" wire:click="resetForm" id="closeModalShowButton2"
                        class="text-gray-700 bg-gray-200 hover:bg-gray-300 focus:ring-4 focus:outline-none focus:ring-gray-400 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-gray-600 dark:hover:bg-gray-700 dark:focus:ring-gray-800">
                        Cerrar
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>


@push('scripts')
    <script>
        const closeModalShowButton = document.getElementById('closeModalShowButton');
        const closeModalShowButton2 = document.getElementById('closeModalShowButton2');
        closeModalShowButton.addEventListener('click', function() {
            modalShow.classList.add('hidden');
        });
        closeModalShowButton2.addEventListener('click', function() {
            modalShow.classList.add('hidden');
        });
    </script>
@endpush
