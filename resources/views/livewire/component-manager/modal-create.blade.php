<div wire:ignore.self id="modalCreate" tabindex="-1" aria-hidden="true" class="hidden overflow-hidden overflow-x-hidden fixed inset-0 z-50 flex items-center justify-center">
    <div wire:loading wire:target="saveComponent,updateComponent">
        <div class="flex flex-col items-center justify-center h-full">
            <svg class="animate-spin h-16 w-16 text-blue-600 mr-3 mb-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="10" stroke-opacity="0.25" stroke-width="2" stroke="currentColor" fill="none"></circle>
                <path d="M12 2a10 10 0 00-9.5 7.5M12 2c2.5 0 5 1 7 2.5" stroke="currentColor" stroke-width="2" fill="none"></path>
            </svg>
        </div>
    </div>
    <div class="relative p-4 w-full max-w-4xl">
      <!-- Modal content -->
      <div class="relative bg-white rounded-lg shadow dark:bg-gray-700 overflow-y-auto" style="max-height:85vh">
          <!-- Modal header -->
          <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600">
              <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                {{ $componentIdToUpdate ? 'Editar componente' : 'Crear nuevo componente' }}
              </h3>
              <button wire:click="resetForm" type="button" id="closeModalButton" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white">
                  <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                      <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                  </svg>
                  <span class="sr-only">Close modal</span>
              </button>
          </div>
          <!-- Modal body -->
          <form class="p-4 md:p-5" @if($componentIdToUpdate) wire:submit.prevent="updateComponent" @else wire:submit.prevent="saveComponent" @endif>
            <div class="grid grid-cols-3 gap-4 mb-6">
                <div>
                    <label for="equipment" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Equipo</label>
                        <select  name="type" class="block appearance-none w-full bg-white border border-gray-400 text-gray-700 py-2 px-4 pr-8 rounded-l leading-tight focus:outline-none focus:border-gray-500 focus:bg-white focus:border-l focus:border-r"  wire:model.lazy="equipment" >
                        <option value="">Selecione</option>
                        @foreach($equipments as $equipment)
                            <option value="{{ $equipment->id }}">{{ $equipment->name }}</option>
                        @endforeach
                   </select>
                    @error('equipment') <span class="text-sm text-red-500">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label for="brand" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Marca</label>
                        <select  name="brand" class="block appearance-none w-full bg-white border border-gray-400 text-gray-700 py-2 px-4 pr-8 rounded-l leading-tight focus:outline-none focus:border-gray-500 focus:bg-white focus:border-l focus:border-r"  wire:model.lazy="brand" >
                        <option value="">Selecione</option>
                        @foreach($brands as $brand)
                            <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                        @endforeach
                   </select>
                    @error('brand') <span class="text-sm text-red-500">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label for="component_type" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Tipo Componente</label>
                        <select  name="component_type" class="block appearance-none w-full bg-white border border-gray-400 text-gray-700 py-2 px-4 pr-8 rounded-l leading-tight focus:outline-none focus:border-gray-500 focus:bg-white focus:border-l focus:border-r"  wire:model.lazy="component_type" >
                        <option value="">Selecione</option>
                        @foreach($component_types as $component_type)
                            <option value="{{ $component_type->id }}">{{ $component_type->name }}</option>
                        @endforeach
                   </select>
                    @error('component_type') <span class="text-sm text-red-500">{{ $message }}</span> @enderror
                </div>


                @foreach($additionalFields as $field)
                    <div>
                        <label for="campo{{$field->id}}" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"> {{ $field->name }}     @if(isset($field->unit) && $field->unit) ({{ $field->unit }})@endif </label>
                        @if($field->type == 'select')
                            <select id="campo{{$field->id}}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500" wire:model.defer="fieldValues.{{ $field->id }}">
                                <option value="">Seleccione una opción</option>
                                @if(is_array($field->options))
                                @foreach($field->options as $option)
                                    <option value="{{ $option }}">{{ $option }}</option>
                                @endforeach
                            @endif
                            </select>
                        @else
                            <input  type="text" id="campo{{$field->id}}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500" placeholder="Ingresar {{ $field->name }}" wire:model.defer="fieldValues.{{ $field->id }}">
                        @endif
                        @error("fieldValues.{$field->id}")
                            <span class="text-sm text-red-500">{{ $message }}</span>
                        @enderror
                    </div>
                @endforeach 
                    {{-- <div>
                    <label for="brand" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Marca</label>
                        <select id="brand" class="block appearance-none w-full bg-white border border-gray-400 text-gray-700 py-2 px-4 pr-8 rounded-l leading-tight focus:outline-none focus:border-gray-500 focus:bg-white focus:border-l focus:border-r" wire:model.defer="brand">
                        <option value="">Selecione</option>
                        @foreach($brands as $brand)
                            <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                        @endforeach
                   </select>
                    @error('brand') <span class="text-sm text-red-500">{{ $message }}</span> @enderror
                </div> --}}
                {{-- <div>
                    <label for="type" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Equipo Motriz 1</label>
                        <select  name="type" class="block appearance-none w-full bg-white border border-gray-400 text-gray-700 py-2 px-4 pr-8 rounded-l leading-tight focus:outline-none focus:border-gray-500 focus:bg-white focus:border-l focus:border-r"  wire:model.lazy="type" >
                        <option value="">Selecione</option>
                        @foreach($equipment_types as $equipment_type)
                            <option value="{{ $equipment_type->id }}">{{ $equipment_type->name }}</option>
                        @endforeach
                   </select>
                    @error('type') <span class="text-sm text-red-500">{{ $message }}</span> @enderror
                </div> --}}
                {{-- @foreach($additionalFields as $field)
                <div>
                    <label for="campo{{$field->id}}" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"> {{ $field->name }}     @if(isset($field->unit) && $field->unit) ({{ $field->unit }})@endif </label>
                    @if($field->type == 'select')
                        <select id="campo{{$field->id}}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500" wire:model.defer="fieldValues.{{ $field->id }}">
                            <option value="">Seleccione una opción</option>
                            @if(is_array($field->options))
                            @foreach($field->options as $option)
                                <option value="{{ $option }}">{{ $option }}</option>
                            @endforeach
                        @endif
                        </select>
                    @else
                        <input  type="text" id="campo{{$field->id}}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500" placeholder="Ingresar {{ $field->name }}" wire:model.defer="fieldValues.{{ $field->id }}">
                    @endif
                    @error("fieldValues.{$field->id}")
                        <span class="text-sm text-red-500">{{ $message }}</span>
                    @enderror
                </div>
            @endforeach --}}
        </div>
              <div class="flex justify-between">
                  <button type="submit" class="text-white inline-flex items-center bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                    {{ $componentIdToUpdate ? 'Actualizar' : 'Guardar' }}
                  </button>
                  <button wire:click="resetForm" type="button" id="closeModalButton2" class="text-gray-700 bg-gray-200 hover:bg-gray-300 focus:ring-4 focus:outline-none focus:ring-gray-400 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-gray-600 dark:hover:bg-gray-700 dark:focus:ring-gray-800">
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
    document.addEventListener('livewire:initialized', function () {
        Livewire.on('close-modal-component', () => {
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
