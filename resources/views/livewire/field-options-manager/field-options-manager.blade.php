<div>
    <!-- Modal para gestionar opciones -->
    @if($showModal && $field)
    <div wire:ignore.self 
         class="fixed inset-0 z-50 overflow-y-auto" 
         aria-labelledby="modal-title" 
         role="dialog" 
         aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <!-- Overlay -->
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" 
                 wire:click="closeModal"></div>

            <!-- Modal panel -->
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
                <!-- Header -->
                <div class="bg-blue-600 px-4 py-3 sm:px-6 flex justify-between items-center">
                    <h3 class="text-lg leading-6 font-medium text-white" id="modal-title">
                        <i class="fas fa-cog mr-2"></i>
                        Gestionar Opciones: <strong>{{ $field->name ?? '' }}</strong>
                    </h3>
                    <button wire:click="closeModal" 
                            class="text-white hover:text-gray-200 focus:outline-none">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>

                <!-- Body -->
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <!-- Agregar Nueva Opción -->
                    <div class="mb-6 border-b pb-4">
                        <h4 class="text-md font-semibold text-gray-700 mb-3">
                            <i class="fas fa-plus-circle mr-2 text-green-500"></i>
                            Agregar Nueva Opción
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Nombre <span class="text-red-500">*</span>
                                </label>
                                <input type="text" 
                                       wire:model="newOption.label"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                       placeholder="Ej: 143T Frame">
                                @error('newOption.label') 
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p> 
                                @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Valor <span class="text-red-500">*</span>
                                </label>
                                <input type="text" 
                                       wire:model="newOption.value"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                       placeholder="Ej: 143T">
                                @error('newOption.value') 
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p> 
                                @enderror
                            </div>
                        </div>
                        <div class="mt-3">
                            <button wire:click="addOption" 
                                    class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                                <i class="fas fa-plus mr-2"></i>
                                Agregar Opción
                            </button>
                        </div>
                    </div>

                    <!-- Lista de Opciones -->
                    <div>
                        <h4 class="text-md font-semibold text-gray-700 mb-3">
                            <i class="fas fa-list mr-2 text-blue-500"></i>
                            Opciones Existentes ({{ count($options) }})
                        </h4>
                        
                        @if(count($options) > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-12">
                                            Orden
                                        </th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Nombre
                                        </th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Valor
                                        </th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Estado
                                        </th>
                                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Acciones
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($options as $index => $option)
                                    <tr class="{{ $option['status'] === 'inactive' ? 'bg-gray-100 opacity-60' : '' }} hover:bg-gray-50">
                                        <!-- Orden -->
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            <div class="flex flex-col space-y-1">
                                                @if($index > 0)
                                                <button wire:click="moveUp({{ $option['id'] }})" 
                                                        class="text-blue-600 hover:text-blue-800"
                                                        title="Mover arriba">
                                                    <i class="fas fa-arrow-up text-xs"></i>
                                                </button>
                                                @else
                                                <span class="text-gray-300 text-xs"><i class="fas fa-arrow-up"></i></span>
                                                @endif
                                                
                                                @if($index < count($options) - 1)
                                                <button wire:click="moveDown({{ $option['id'] }})" 
                                                        class="text-blue-600 hover:text-blue-800"
                                                        title="Mover abajo">
                                                    <i class="fas fa-arrow-down text-xs"></i>
                                                </button>
                                                @else
                                                <span class="text-gray-300 text-xs"><i class="fas fa-arrow-down"></i></span>
                                                @endif
                                            </div>
                                        </td>

                                        <!-- Nombre y Valor -->
                                        @if($editingOptionId === $option['id'])
                                        <td colspan="2" class="px-4 py-3">
                                            <div class="grid grid-cols-2 gap-2">
                                                <div>
                                                    <input type="text" 
                                                           wire:model="editingOption.label"
                                                           class="w-full px-2 py-1 text-sm border border-gray-300 rounded">
                                                    @error('editingOption.label') 
                                                        <p class="text-xs text-red-600">{{ $message }}</p> 
                                                    @enderror
                                                </div>
                                                <div>
                                                    <input type="text" 
                                                           wire:model="editingOption.value"
                                                           class="w-full px-2 py-1 text-sm border border-gray-300 rounded">
                                                    @error('editingOption.value') 
                                                        <p class="text-xs text-red-600">{{ $message }}</p> 
                                                    @enderror
                                                </div>
                                            </div>
                                        </td>
                                        @else
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <span class="text-sm font-medium text-gray-900">
                                                    @if($option['is_default'])
                                                        <i class="fas fa-star text-yellow-500 mr-1"></i>
                                                    @endif
                                                    {{ $option['label'] }}
                                                </span>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                            <code class="bg-gray-100 px-2 py-1 rounded">{{ $option['value'] }}</code>
                                        </td>
                                        @endif

                                        <!-- Estado -->
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            @if($editingOptionId === $option['id'])
                                            <label class="flex items-center" title="Marcar como opción por defecto">
                                                <input type="checkbox" 
                                                       wire:model="editingOption.is_default"
                                                       class="rounded border-gray-300 text-blue-600">
                                                <span class="ml-2 text-xs text-gray-700">
                                                    <i class="fas fa-star text-yellow-500"></i>
                                                </span>
                                            </label>
                                            @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $option['status'] === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                {{ $option['status'] === 'active' ? 'Activa' : 'Inactiva' }}
                                            </span>
                                            @endif
                                        </td>

                                        <!-- Acciones -->
                                        <td class="px-4 py-3 whitespace-nowrap text-center text-sm font-medium">
                                            @if($editingOptionId === $option['id'])
                                            <div class="flex justify-center space-x-2">
                                                <button wire:click="updateOption" 
                                                        class="text-green-600 hover:text-green-900"
                                                        title="Guardar">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                                <button wire:click="cancelEdit" 
                                                        class="text-gray-600 hover:text-gray-900"
                                                        title="Cancelar">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </div>
                                            @else
                                            <div class="flex justify-center space-x-2">
                                                <button wire:click="editOption({{ $option['id'] }})" 
                                                        class="text-blue-600 hover:text-blue-900"
                                                        title="Editar">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                @if($option['status'] === 'active')
                                                <button wire:click="toggleStatus({{ $option['id'] }})" 
                                                        class="text-yellow-600 hover:text-yellow-900"
                                                        title="Desactivar">
                                                    <i class="fas fa-eye-slash"></i>
                                                </button>
                                                @else
                                                <button wire:click="toggleStatus({{ $option['id'] }})" 
                                                        class="text-green-600 hover:text-green-900"
                                                        title="Activar">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                @endif
                                                <button wire:click="toggleDefault({{ $option['id'] }})" 
                                                        class="text-yellow-600 hover:text-yellow-900"
                                                        title="{{ $option['is_default'] ? 'Quitar opción por defecto' : 'Marcar como opción por defecto' }}">
                                                    <i class="fas fa-star"></i>
                                                </button>
                                                <button wire:click="deleteOption({{ $option['id'] }})" 
                                                        wire:confirm="¿Estás seguro de eliminar esta opción?"
                                                        class="text-red-600 hover:text-red-900"
                                                        title="Eliminar">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @else
                        <div class="text-center py-8 bg-gray-50 rounded-lg">
                            <i class="fas fa-inbox text-4xl text-gray-400 mb-2"></i>
                            <p class="text-gray-500">No hay opciones agregadas aún</p>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Footer -->
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" 
                            wire:click="closeModal"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Cerrar
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
