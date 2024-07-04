<div wire:ignore.self id="modalShow" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed inset-0 z-50 flex items-center justify-center">
    <div wire:loading wire:target="saveCompany,updateEquipment">
        <div class="flex flex-col items-center justify-center h-full">
            <svg class="animate-spin h-16 w-16 text-blue-600 mr-3 mb-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="10" stroke-opacity="0.25" stroke-width="2" stroke="currentColor" fill="none"></circle>
                <path d="M12 2a10 10 0 00-9.5 7.5M12 2c2.5 0 5 1 7 2.5" stroke="currentColor" stroke-width="2" fill="none"></path>
            </svg>
        </div>
    </div>
    <div class="relative p-4 w-full max-w-4xl">
        <!-- Modal content -->
        <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
            <!-- Modal header -->
            <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                    Detalle del equipo
                </h3>
                <button wire:click="resetForm" type="button" id="closeModalShowButton" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white">
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                    </svg>
                    <span class="sr-only">Close modal</span>
                </button>
            </div>
            <!-- Modal body -->
            <div class="p-4 md:p-5">
                <div class="grid grid-cols-3 gap-4 mb-6">
                    <div>
                        <label for="name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Nombre</label>
                        <span id="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 p-2.5 rounded-lg bg-blue-50 dark:bg-blue-100">{{ $name }}</span>
                    </div>
                    <div>
                        <label for="code" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Código</label>
                        <span id="code" class="block text-sm font-medium text-gray-700 dark:text-gray-300 p-2.5 rounded-lg bg-blue-50 dark:bg-blue-100">{{ $code }}</span>
                    </div>
                    <div>
                        <label for="tag" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Tag</label>
                        <span id="tag" class="block text-sm font-medium text-gray-700 dark:text-gray-300 p-2.5 rounded-lg bg-blue-50 dark:bg-blue-100">{{ $tag }}</span>
                    </div>
                    <div>
                        <label for="company" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Empresa</label>
                        <span id="company" class="block text-sm font-medium text-gray-700 dark:text-gray-300 p-2.5 rounded-lg bg-blue-50 dark:bg-blue-100">{{ optional($companies->find($company))->name }}</span>
                    </div>
                    <div>
                        <label for="brand" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Marca</label>
                        <span id="brand" class="block text-sm font-medium text-gray-700 dark:text-gray-300 p-2.5 rounded-lg bg-blue-50 dark:bg-blue-100">{{ optional($brands->find($brand))->name }}</span>
                    </div>
                    <div>
                        <label for="type" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Tipo</label>
                        <span id="type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 p-2.5 rounded-lg bg-blue-50 dark:bg-blue-100">{{ optional($equipment_types->find($type))->name }}</span>
                    </div>
                    @foreach($additionalFields as $field)
                    <div>
                        <label for="campo{{$field->id}}" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">{{ $field->name }} @if(isset($field->unit) && $field->unit) ({{ $field->unit }})@endif</label>
                        <span id="campo{{$field->id}}" class="block text-sm font-medium text-gray-700 dark:text-gray-300 p-2.5 rounded-lg bg-blue-50 dark:bg-blue-100">
                            @if(isset($fieldValues[$field->id]))
                                {{ $fieldValues[$field->id] }}
                            @endif
                        </span>
                    </div>
                    @endforeach
                </div>
                <div class="flex justify-between">
                    <button wire:click="resetForm" type="button" id="closeModalShowButton2" class="text-gray-700 bg-gray-200 hover:bg-gray-300 focus:ring-4 focus:outline-none focus:ring-gray-400 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-gray-600 dark:hover:bg-gray-700 dark:focus:ring-gray-800">
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
    document.addEventListener('livewire:initialized', function () {
        Livewire.on('close-modal-equipment', () => {
            const modalShow = document.getElementById('modalShow');
            modalShow.classList.add('hidden');
        });
    });
    closeModalShowButton.addEventListener('click', function() {
        modalShow.classList.add('hidden');
    });
    closeModalShowButton2.addEventListener('click', function() {
        modalShow.classList.add('hidden');
    });
</script>
@endpush
