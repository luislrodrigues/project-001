<div wire:ignore.self id="modalCreate" tabindex="-1" aria-hidden="true"
    class="hidden  overflow-hidden fixed inset-0 z-50 flex items-center justify-center">
    <div wire:loading wire:target="saveReport,updateReport,addRecommendation,deleteRecommendation"
        class="hidden overflow-y-auto overflow-x-hidden fixed inset-0 z-50 flex items-center justify-center">
        <div class="flex flex-col items-center justify-center h-full">
            <svg class="animate-spin h-16 w-16 text-blue-600 mr-3 mb-2" xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="10" stroke-opacity="0.25" stroke-width="2"
                    stroke="currentColor" fill="none"></circle>
                <path d="M12 2a10 10 0 00-9.5 7.5M12 2c2.5 0 5 1 7 2.5" stroke="currentColor" stroke-width="2"
                    fill="none"></path>
            </svg>
        </div>
    </div>
    <div class="relative p-4 w-full max-w-4xl">
        <!-- Modal content -->
        <div class="relative bg-white rounded-lg shadow dark:bg-gray-700 overflow-y-auto" style="max-height:85vh">
            <!-- Modal header -->
            <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                    {{ $reportIdToUpdate ? 'Editar reporte' : 'Crear nuevo reporte' }}
                </h3>
                <button wire:click="resetForm" type="button" id="closeModalButton"
                    class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white">
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                    </svg>
                    <span class="sr-only">Close modal</span>
                </button>
            </div>
            <!-- Modal body -->
            <form class="p-4 md:p-5"
                @if ($reportIdToUpdate) wire:submit.prevent="updateReport" @else wire:submit.prevent="saveReport" @endif>
                <div class="grid grid-cols-3 gap-4 mb-2">
                    <div>
                        <label for="equipment"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Equipo</label>
                        <select id="equipment"
                            class="block appearance-none w-full bg-white border border-gray-400 text-gray-700 py-2 px-4 pr-8 rounded-l leading-tight focus:outline-none focus:border-gray-500 focus:bg-white focus:border-l focus:border-r"
                            wire:model.lazy="equipment">
                            <option value="">Selecione</option>
                            @foreach ($equipments as $equipment)
                                <option value="{{ $equipment->id }}">{{ $equipment->name }}</option>
                            @endforeach
                        </select>
                        @error('equipment')
                            <span class="text-sm text-red-500">{{ $message }}</span>
                        @enderror
                    </div>
                    <div>
                        <label for="equipmentType"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Tipo equipo</label>
                        <input type="text" id="equipmentType"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                            placeholder="Ingresar ubicacion" wire:model.defer="equipmentType" disabled>
                    </div>
                    <div>
                        <label for="measurement_date"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Fecha medicion</label>
                        <input type="date" id="measurement_date"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                            wire:model.defer="measurement_date">
                        @error('measurement_date')
                            <span class="text-sm text-red-500">{{ $message }}</span>
                        @enderror
                    </div>
                    <div>
                        <label for="location"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Ubicacion</label>
                        <input type="text" id="location"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                            placeholder="Ingresar ubicacion" wire:model.defer="location" disabled>
                    </div>
                    <div>
                        <label for="process"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Proceso</label>
                        <input type="text" id="process"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                            placeholder="Ingresar Proceso" wire:model.defer="process" disabled>
                    </div>
 

                    <div>
                        <label for="condition"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Condicion</label>
                        <select name="condition" id="selecteEuipmentType"
                            class="block appearance-none w-full bg-white border border-gray-400 text-gray-700 py-2 px-4 pr-8 rounded-l leading-tight focus:outline-none focus:border-gray-500 focus:bg-white focus:border-l focus:border-r"
                            wire:model.lazy="condition"
                            wire:change="handleSelectChange($event.target.value,'condition')">
                            <option value="">Seleccione</option>
                            @foreach ($conditions as $option)
                                <option value="{{ $option['id'] }}">{{ $option['name'] }}</option>
                            @endforeach
                            <option value="add_new">Añadir nueva</option>
                        </select>

                        @error('condition')
                            <span class="text-sm text-red-500">{{ $message }}</span>
                        @enderror

                        <livewire:modal-create-select.modal-create-select :label="'Condicion'" :field="'condition'"
                            updateOptions="updatedConditions" />
                    </div>





           





                    
                    <div>
                        <label for="failure_mechanism"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Mecanismo falla</label>
                        <select name="failure_mechanism" id="selecteEuipmentType"
                            class="block appearance-none w-full bg-white border border-gray-400 text-gray-700 py-2 px-4 pr-8 rounded-l leading-tight focus:outline-none focus:border-gray-500 focus:bg-white focus:border-l focus:border-r"
                            wire:model.lazy="failure_mechanism"
                            wire:change="handleSelectChange($event.target.value,'failure_mechanism')">
                            <option value="">Seleccione</option>
                            @foreach ($mechanism_fails as $option)
                                <option value="{{ $option['id'] }}">{{ $option['name'] }}</option>
                            @endforeach
                            <option value="add_new">Añadir nueva</option>
                        </select>

                        @error('failure_mechanism')
                            <span class="text-sm text-red-500">{{ $message }}</span>
                        @enderror

                        <livewire:modal-create-select.modal-create-select :label="'Mecanismo falla'" :field="'failure_mechanism'"
                            updateOptions="updatedmechanismFails" />
                    </div>

                    
                    <div>
                        <label for="component_failure"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Componente falla</label>
                        <select name="component_failure" id="selecteEuipmentType"
                            class="block appearance-none w-full bg-white border border-gray-400 text-gray-700 py-2 px-4 pr-8 rounded-l leading-tight focus:outline-none focus:border-gray-500 focus:bg-white focus:border-l focus:border-r"
                            wire:model.lazy="component_failure"
                            wire:change="handleSelectChange($event.target.value,'component_failure')">
                            <option value="">Seleccione</option>
                            @foreach ($component_fails as $option)
                                <option value="{{ $option['id'] }}">{{ $option['name'] }}</option>
                            @endforeach
                            <option value="add_new">Añadir nueva</option>
                        </select>

                        @error('component_failure')
                            <span class="text-sm text-red-500">{{ $message }}</span>
                        @enderror

                        <livewire:modal-create-select.modal-create-select :label="'Componente falla'" :field="'component_failure'"
                            updateOptions="updatedcomponentFails" />
                    </div>

                    <div>
                        <label for="priority"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Prioridad</label>
                        <select id="priority"
                            class="block appearance-none w-full bg-white border border-gray-400 text-gray-700 py-2 px-4 pr-8 rounded-l leading-tight focus:outline-none focus:border-gray-500 focus:bg-white focus:border-l focus:border-r"
                            wire:model.defer="priority">
                            <option value="">Selecione</option>
                            <option value="Alta">Alta</option>
                            <option value="Media">Media</option>
                        </select>
                        @error('priority')
                            <span class="text-sm text-red-500">{{ $message }}</span>
                        @enderror
                    </div>
                    <div>
                        <label for="ot_created"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">OT creada</label>
                        <input type="text" id="ot_created"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                            placeholder="Ingresar OT creada" wire:model.defer="ot_created">
                        @error('ot_created')
                            <span class="text-sm text-red-500">{{ $message }}</span>
                        @enderror
                    </div>
                    <div>
                        <label for="status_ot"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Estado de OT</label>
                        <input type="text" id="status_ot"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                            placeholder="Ingresar estado de OT" wire:model.defer="status_ot">
                        @error('status_ot')
                            <span class="text-sm text-red-500">{{ $message }}</span>
                        @enderror
                    </div>
                    <div>
                        <label for="execution_date"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Fecha de
                            ejecucion</label>
                        <input type="date" id="execution_date"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                            wire:model.defer="execution_date">
                        @error('execution_date')
                            <span class="text-sm text-red-500">{{ $message }}</span>
                        @enderror
                    </div>
                    <div>
                        <label for="analist"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Analista</label>
                        <select id="analist"
                            class="block appearance-none w-full bg-white border border-gray-400 text-gray-700 py-2 px-4 pr-8 rounded-l leading-tight focus:outline-none focus:border-gray-500 focus:bg-white focus:border-l focus:border-r"
                            wire:model.lazy="analist">
                            <option value="">Selecione</option>
                            @foreach ($users as $analist)
                                <option value="{{ $analist->id }}">{{ $analist->name }}</option>
                            @endforeach
                        </select>


                        @error('analist')
                            <span class="text-sm text-red-500">{{ $message }}</span>
                        @enderror
                    </div>
                    <div>
                        <label for="verification_recommendation"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Verificacion
                            recomendacion</label>
                        <textarea type="text" id="verification_recommendation"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                            placeholder="Ingresar verificacion recomendacion" wire:model.defer="verification_recommendation"></textarea>
                        @error('verification_recommendation')
                            <span class="text-sm text-red-500">{{ $message }}</span>
                        @enderror
                    </div>
                    <div>
                        <label for="image_1"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Subir imagen 1</label>
                        <input type="file" id="image_1"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:text-white"
                            wire:model.defer="image_1">
                        @error('image_1')
                            <span class="text-sm text-red-500">{{ $message }}</span>
                        @enderror
                    </div>
                    <div>
                        <label for="image_2"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Subir imagen 2</label>
                        <input type="file" id="image_2"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:text-white"
                            wire:model.defer="image_2">
                        @error('image_2')
                            <span class="text-sm text-red-500">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                @foreach ($components as $index => $component)
                    <div>
                        <div  class="mb-2">
                            <label for="diagnostic_{{ $component->id }}"
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                Diagnóstico {{ $component->componentType->name }}
                            </label>
                            <textarea id="diagnostic_{{ $component->id }}"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                                placeholder="Ingresar diagnóstico" wire:model.defer="diagnostics.{{ $component->id }}"></textarea>
                            @error('diagnostics.' . $component->id)
                                <span class="text-sm text-red-500">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-2">
                            @if (isset($recommendations[$component->id]) && is_array($recommendations[$component->id]) && !empty($recommendations[$component->id]))
                                @foreach ($recommendations[$component->id] as $recIndex => $recommendation)
                                    <div class="mb-3">
                                        <label for="recommendation_{{ $component->id }}_{{ $recIndex }}"
                                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                            Recomendación {{ $recIndex + 1 }} {{ $component->componentType->name }}
                                        </label>
                                        <div class="flex items-center">
                                            <input type="text"
                                                id="recommendation_{{ $component->id }}_{{ $recIndex }}"
                                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                                                placeholder="Recomendación {{ $recIndex + 1 }} {{ $component->componentType->name }}"
                                                wire:model.defer="recommendations.{{ $component->id }}.{{ $recIndex }}">
                                            <button type="button"
                                                wire:click="deleteRecommendation({{ $component->id }}, {{ $recIndex }})"
                                                class="ml-2 bg-red-600 text-white hover:bg-red-700 rounded-lg px-3 py-1"
                                                wire:loading.attr="disabled">
                                                Eliminar
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                             @endif
                        
                            <div class="flex justify-center">
                                <button type="button"
                                    class="text-white inline-flex items-center bg-green-700 hover:bg-green-800 focus:ring-4 focus:outline-none focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800"
                                    wire:click="addRecommendation({{ $component->id }})">
                                    Agregar recomendación de  {{ $component->componentType->name }}
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach

                <div class="flex justify-between">
                    <button type="submit"
                        class="text-white inline-flex items-center bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                        {{ $reportIdToUpdate ? 'Actualizar' : 'Guardar' }}
                    </button>
                    <button wire:click="resetForm" type="button" id="closeModalButton2"
                        class="text-gray-700 bg-gray-200 hover:bg-gray-300 focus:ring-4 focus:outline-none focus:ring-gray-400 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-gray-600 dark:hover:bg-gray-700 dark:focus:ring-gray-800">
                        Cerrar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        const closeModalButton = document.getElementById('closeModalButton');
        const closeModalButton2 = document.getElementById('closeModalButton2');
        document.addEventListener('livewire:initialized', function() {
            Livewire.on('close-modal-report', () => {
                const modalCreate = document.getElementById('modalCreate');
                modalCreate.classList.add('hidden');
            });

        });
        closeModalButton.addEventListener('click', function() {
            modalCreate.classList.add('hidden');
        });
        closeModalButton2.addEventListener('click', function() {
            modalCreate.classList.add('hidden');
        });
    </script>
@endpush
