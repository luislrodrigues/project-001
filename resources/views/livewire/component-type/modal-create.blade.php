<div wire:ignore.self id="modalCreate" tabindex="-1" aria-hidden="true"
    class="hidden overflow-hidden overflow-x-hidden fixed inset-0 z-50 flex items-center justify-center">
    <div wire:loading wire:target="saveType,updateEquipment"
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
                    {{ $IdToUpdate ? 'Editar tipo de componente' : 'Crear nuevo tipo de componente' }}
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
            <div>
                <form class="p-4 md:p-5"
                    @if ($IdToUpdate) wire:submit.prevent="updateTypeComponent" @else wire:submit.prevent="saveTypeComponent" @endif>
                    <div class="mb-6">
                        <label for="name"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Nombre</label>
                        <input type="text" id="name"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                            placeholder="Ingresar nombre" wire:model.defer="name">
                        @error('name')
                            <span class="text-sm text-red-500">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="mb-6">
                        <h4 class="font-semibold text-lg mb-2">Atributos</h4>
                        @foreach ($fields as $index => $field)
                            <div class="grid grid-cols-4 gap-4 mb-4">
                                <div>
                                    <label for="name"
                                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Nombre</label>
                                    <input type="text" id="name"
                                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                                        placeholder="Ingresar nombre"
                                        wire:model.defer="fields.{{ $index }}.name">
                                    @error("fields.$index.name")
                                        <span class="text-sm text-red-500">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div>
                                    <label
                                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Tipo</label>
                                    <select wire:model="fields.{{ $index }}.type"
                                        class="block appearance-none w-full bg-white border border-gray-400 text-gray-700 py-2 px-4 pr-8 rounded-l leading-tight focus:outline-none focus:border-gray-500 focus:bg-white focus:border-l focus:border-r">
                                        <option value="" >Selecione</option>
                                        <option value="text">Texto</option>
                                        <option value="numeric">Numérico</option>
                                        <option value="select">Selección</option>
                                    </select>
                                    @error("fields.$index.type")
                                        <span class="text-sm text-red-500">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div>
                                    <label for="unit"
                                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Unidad</label>
                                    <input type="text" id="unit"
                                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                                        placeholder="Ingresar unidad"
                                        wire:model.defer="fields.{{ $index }}.unit">
                                    @error("fields.$index.unit")
                                        <span class="text-sm text-red-500">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="mt-6 text-center">
                                    <button type="button" wire:click="removeField({{ $index }})"
                                        class="px-4 py-2 bg-red-500 text-white rounded">Eliminar</button>
                                </div>
                            </div>
                        @endforeach
                        <button type="button" wire:click="addField"
                            class="px-4 py-2 bg-green-500 text-white rounded">Agregar atributos</button>
                    </div>

                    <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded">Guardar</button>
                </form>
            </div>

        </div>
    </div>
</div>

@push('scripts')
    <script>
        const closeModalButton = document.getElementById('closeModalButton');
        const closeModalButton2 = document.getElementById('closeModalButton2');
        document.addEventListener('livewire:initialized', function() {
            Livewire.on('close-modal-type-component', () => {
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
