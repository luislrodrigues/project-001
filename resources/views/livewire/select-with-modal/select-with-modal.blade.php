<div>
    <!-- Label y Select -->
    <div>
        <label for="{{$field}}" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">{{$label}}</label>
        <select name="{{$field}}" id="selectField" class="block appearance-none w-full bg-white border border-gray-400 text-gray-700 py-2 px-4 pr-8 rounded-l leading-tight focus:outline-none focus:border-gray-500 focus:bg-white focus:border-l focus:border-r"  wire:change="handleSelection($event.target.value, '{{ $field }}')" wire:model="{{ $field }}">
            <option value="">Seleccione</option>
            @if(!empty($options) && count($options) > 0)
                @foreach($options as $option)
                    <option value="{{ $option['id'] }}">{{ $option['name'] }}</option>  <!-- Accede con la clave del arreglo -->
                @endforeach
            @else
                <option value="" disabled>No hay opciones disponibles</option>
          @endif
            <option value="add_new">Añadir nueva</option>
        </select>
    </div>

    <!-- Modal -->
    <div wire:ignore.self id="modalCreateSelect" class="fixed z-10 inset-0 overflow-y-auto hidden">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="bg-white rounded-lg shadow-xl p-6">
                <h2 class="text-lg font-semibold">{{$label}}</h2>
                <input type="text" class="block w-full border-gray-300 rounded-md mt-4" placeholder="Nueva marca" wire:model="valueNew">
                @error('valueNew') <span class="text-sm text-red-500">{{ $message }}</span> @enderror
                <div class="mt-4 flex justify-end">
                    <button type="button" class="px-4 py-2 bg-gray-500 text-white rounded-md mr-2" id="closeModalButtonSelect" >Cancelar</button>
                    <button type="button" class="px-4 py-2 bg-blue-500 text-white rounded-md" wire:click="saveField" >Guardar</button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        const selectBrand = document.getElementById('selectField');
        const modalCreateSelect = document.getElementById('modalCreateSelect');
        const buttonCloseModal = document.getElementById('closeModalButtonSelect');

        // Detectar la selección de "Añadir nueva"
        selectBrand.addEventListener('change', function () {
            if (this.value === 'add_new') {
                modalCreateSelect.classList.remove('hidden'); // Muestra el modal
            }
        });

        // Ocultar el modal al hacer clic en "Cancelar"
        buttonCloseModal.addEventListener('click', function () {
            modalCreateSelect.classList.add('hidden');
        });

        document.addEventListener('livewire:initialized', function () {
        Livewire.on('close-modal-select', () => {
            const modalCreateSelect = document.getElementById('modalCreateSelect');
            modalCreateSelect.classList.add('hidden');
        });

    });
    </script>
    @endpush
</div>
