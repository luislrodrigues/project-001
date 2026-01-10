<div wire:ignore.self id="modalCreate" tabindex="-1" aria-hidden="true" class="hidden overflow-hidden overflow-x-hidden fixed inset-0 z-50 flex items-center justify-center">
    <!-- Overlay con backdrop blur -->
    <div class="fixed inset-0 bg-gray-900 bg-opacity-75 backdrop-blur-sm transition-opacity z-40"
         onclick="document.getElementById('modalCreate').classList.add('hidden')"></div>

    <div wire:loading wire:target="saveComponent,updateComponent"
         class="hidden overflow-y-auto overflow-x-hidden fixed inset-0 z-60 flex items-center justify-center">
        <div class="flex flex-col items-center justify-center h-full">
            <svg class="animate-spin h-16 w-16 text-blue-600 mr-3 mb-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="10" stroke-opacity="0.25" stroke-width="2" stroke="currentColor" fill="none"></circle>
                <path d="M12 2a10 10 0 00-9.5 7.5M12 2c2.5 0 5 1 7 2.5" stroke="currentColor" stroke-width="2" fill="none"></path>
            </svg>
            <p class="text-gray-700 font-medium">Cargando...</p>
        </div>
    </div>
    <div class="relative p-4 w-full max-w-5xl z-50">
      <!-- Modal content -->
      <div class="relative bg-white rounded-xl shadow-2xl dark:bg-gray-800 overflow-hidden" style="max-height:90vh">
          <!-- Modal header con gradiente -->
          <div class="flex items-center justify-between px-6 py-4 bg-gradient-to-r from-blue-500 to-blue-600 rounded-t-xl">
              <div class="flex items-center space-x-3">
                  <div class="bg-white bg-opacity-20 rounded-lg p-2">
                      <i class="fas {{ $componentIdToUpdate ? 'fa-edit' : 'fa-plus-circle' }} text-white text-xl"></i>
                  </div>
                  <h3 class="text-xl font-bold text-white">
                    {{ $componentIdToUpdate ? 'Editar componente' : 'Crear nuevo componente' }}
                  </h3>
              </div>
              <button wire:click="resetForm" type="button" id="closeModalButton"
                      class="text-white hover:bg-white hover:bg-opacity-20 rounded-lg text-sm w-9 h-9 inline-flex justify-center items-center transition-colors duration-200">
                  <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                      <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                  </svg>
                  <span class="sr-only">Close modal</span>
              </button>
          </div>

          <!-- Modal body -->
          <div class="overflow-y-auto" style="max-height: calc(90vh - 80px)">
              <form class="p-6" @if($componentIdToUpdate) wire:submit.prevent="updateComponent" @else wire:submit.prevent="saveComponent" @endif>
                  <div class="mb-6">
                      <div class="flex items-center mb-4 pb-2 border-b border-gray-200">
                          <i class="fas fa-info-circle text-blue-600 mr-2"></i>
                          <h4 class="text-lg font-semibold text-gray-800 dark:text-white">Información Básica</h4>
                      </div>
                      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                          <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                              <label for="equipment" class="flex items-center mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                  <i class="fas fa-cog mr-2 text-blue-600"></i>
                                  Equipo
                                  <span class="text-red-500 ml-1">*</span>
                              </label>
                              <select name="type"
                                      class="block w-full bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 px-4 py-2.5 dark:bg-gray-600 dark:border-gray-500 dark:text-white transition-all duration-200"
                                      wire:model.lazy="equipment">
                                  <option value="">Seleccione</option>
                                  @foreach($equipments as $equipment)
                                      <option value="{{ $equipment->id }}">{{ $equipment->name }}</option>
                                  @endforeach
                             </select>
                              @error('equipment')
                                  <span class="flex items-center mt-1 text-xs text-red-600">
                                      <i class="fas fa-exclamation-circle mr-1"></i>
                                      {{ $message }}
                                  </span>
                              @enderror
                          </div>

                          <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                              <label for="component_type" class="flex items-center mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                  <i class="fas fa-snowplow mr-2 text-blue-600"></i>
                                  Tipo Componente
                                  <span class="text-red-500 ml-1">*</span>
                              </label>
                              <select name="component_type"
                                      class="block w-full bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 px-4 py-2.5 dark:bg-gray-600 dark:border-gray-500 dark:text-white transition-all duration-200"
                                      wire:model.live="component_type">
                                  <option value="">Seleccione</option>
                                  @foreach($component_types as $component_type)
                                      <option value="{{ $component_type->id }}">{{ $component_type->name }}</option>
                                  @endforeach
                             </select>
                              @error('component_type')
                                  <span class="flex items-center mt-1 text-xs text-red-600">
                                      <i class="fas fa-exclamation-circle mr-1"></i>
                                      {{ $message }}
                                  </span>
                              @enderror
                          </div>

                          <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                              <label for="brand" class="flex items-center mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                  <i class="fas fa-tag mr-2 text-blue-600"></i>
                                  Marca
                                  <span class="text-red-500 ml-1">*</span>
                              </label>
                              <div class="flex space-x-2">
                                  <select name="brand"
                                          class="flex-1 bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 px-4 py-2.5 dark:bg-gray-600 dark:border-gray-500 dark:text-white transition-all duration-200"
                                          wire:model.lazy="brand">
                                      <option value="">Seleccione</option>
                                      @foreach ($brands as $option)
                                          <option value="{{ $option['id'] }}">{{ $option['name'] }}</option>
                                      @endforeach
                                  </select>
                                  <button type="button"
                                          wire:click="openManageModal('brand')"
                                          class="px-4 py-2.5 bg-blue-500 hover:bg-blue-600 text-white rounded-lg transition-colors duration-200 flex items-center justify-center"
                                          title="Gestionar marcas">
                                      <i class="fas fa-cog"></i>
                                  </button>
                              </div>
                              <livewire:modal-create-select.modal-create-select :label="'marca'" :field="'brand'"
                                  updateOptions="updatedBrands" />
                              @error('brand')
                                  <span class="flex items-center mt-1 text-xs text-red-600">
                                      <i class="fas fa-exclamation-circle mr-1"></i>
                                      {{ $message }}
                                  </span>
                              @enderror
                          </div>
                      </div>
                  </div>

                  {{-- Campos Adicionales --}}
                  @if(count($additionalFields) > 0)
                  <div class="mb-6">
                      <div class="flex items-center mb-4 pb-2 border-b border-gray-200">
                          <i class="fas fa-list-ul text-blue-600 mr-2"></i>
                          <h4 class="text-lg font-semibold text-gray-800 dark:text-white">Atributos Adicionales</h4>
                      </div>
                      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                          @foreach($additionalFields as $field)
                              <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                  <label for="campo{{$field->id}}" class="flex items-center mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                      <i class="fas fa-info-circle mr-2 text-blue-600"></i>
                                      {{ $field->name }}
                                      @if(isset($field->unit) && $field->unit)
                                          <span class="ml-2 text-xs text-gray-500">({{ $field->unit }})</span>
                                      @endif
                                      @if($field->is_required ?? false)
                                          <span class="text-red-500 ml-1">*</span>
                                      @endif
                                  </label>

                                  @if($field->type == 'select' || $field->type == 'multiselect')
                                      <select id="campo{{$field->id}}"
                                          class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 block w-full px-4 py-2.5 dark:bg-gray-600 dark:border-gray-500 dark:text-white transition-all duration-200"
                                          wire:model.defer="fieldValues.{{ $field->id }}"
                                          @if($field->type == 'multiselect') multiple @endif>
                                          <option value="">Seleccione una opción</option>
                                          @if(isset($field->optionsArray) && is_array($field->optionsArray))
                                              @foreach($field->optionsArray as $option)
                                                  <option value="{{ $option }}">{{ $option }}</option>
                                              @endforeach
                                          @elseif($field->options && $field->options->count() > 0)
                                              @foreach($field->options as $option)
                                                  <option value="{{ $option->value }}">{{ $option->label }}</option>
                                              @endforeach
                                          @endif
                                      </select>
                                  @elseif($field->type == 'boolean')
                                      <select id="campo{{$field->id}}"
                                          class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 block w-full px-4 py-2.5 dark:bg-gray-600 dark:border-gray-500 dark:text-white transition-all duration-200"
                                          wire:model.defer="fieldValues.{{ $field->id }}">
                                          <option value="">Seleccione</option>
                                          <option value="1">Sí</option>
                                          <option value="0">No</option>
                                      </select>
                                  @elseif($field->type == 'date' || $field->type == 'datetime')
                                      <input type="{{ $field->type == 'date' ? 'date' : 'datetime-local' }}"
                                          id="campo{{$field->id}}"
                                          class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 block w-full px-4 py-2.5 dark:bg-gray-600 dark:border-gray-500 dark:text-white transition-all duration-200"
                                          placeholder="{{ $field->placeholder ?? 'Ingresar ' . $field->name }}"
                                          wire:model.defer="fieldValues.{{ $field->id }}">
                                  @elseif($field->type == 'textarea')
                                      <textarea id="campo{{$field->id}}"
                                          rows="3"
                                          class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 block w-full px-4 py-2.5 dark:bg-gray-600 dark:border-gray-500 dark:text-white transition-all duration-200"
                                          placeholder="{{ $field->placeholder ?? 'Ingresar ' . $field->name }}"
                                          wire:model.defer="fieldValues.{{ $field->id }}"></textarea>
                                  @else
                                      <input type="{{ $field->type == 'numeric' ? 'number' : ($field->type == 'email' ? 'email' : ($field->type == 'url' ? 'url' : 'text')) }}"
                                          id="campo{{$field->id}}"
                                          class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 block w-full px-4 py-2.5 dark:bg-gray-600 dark:border-gray-500 dark:text-white transition-all duration-200"
                                          placeholder="{{ $field->placeholder ?? 'Ingresar ' . $field->name }}"
                                          wire:model.defer="fieldValues.{{ $field->id }}">
                                  @endif

                                  @if(isset($field->help_text) && $field->help_text)
                                      <p class="mt-1 text-xs text-gray-500">
                                          <i class="fas fa-info-circle mr-1"></i>
                                          {{ $field->help_text }}
                                      </p>
                                  @endif

                                  @error("fieldValues.{$field->id}")
                                      <span class="flex items-center mt-1 text-xs text-red-600">
                                          <i class="fas fa-exclamation-circle mr-1"></i>
                                          {{ $message }}
                                      </span>
                                  @enderror
                              </div>
                          @endforeach
                      </div>
                  </div>
                  @endif

                  <!-- Footer -->
                  <div class="flex justify-end space-x-3 pt-6 mt-6 border-t border-gray-200 dark:border-gray-600">
                      <button wire:click="resetForm" type="button" id="closeModalButton2"
                              class="flex items-center px-6 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition-colors duration-200 font-medium">
                          <i class="fas fa-times mr-2"></i>
                          Cancelar
                      </button>
                      <button type="submit"
                              class="flex items-center px-6 py-2.5 bg-blue-500 hover:bg-blue-600 text-white rounded-lg transition-colors duration-200 font-medium shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                          <i class="fas fa-save mr-2"></i>
                          {{ $componentIdToUpdate ? 'Actualizar' : 'Guardar' }}
                      </button>
                  </div>
              </form>
          </div>
      </div>
  </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const modalCreate = document.getElementById('modalCreate');
        const closeModalButton = document.getElementById('closeModalButton');
        const closeModalButton2 = document.getElementById('closeModalButton2');

        if (!modalCreate) return;

        document.addEventListener('livewire:initialized', function () {
            Livewire.on('close-modal-component', () => {
                if (modalCreate) {
                    modalCreate.classList.add('hidden');
                }
            });
        });

        if (closeModalButton) {
            closeModalButton.addEventListener('click', function() {
                if (modalCreate) {
                    modalCreate.classList.add('hidden');
                }
            });
        }

        if (closeModalButton2) {
            closeModalButton2.addEventListener('click', function() {
                if (modalCreate) {
                    modalCreate.classList.add('hidden');
                }
            });
        }
    });
</script>
@endpush
