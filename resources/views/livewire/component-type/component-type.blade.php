<!-- component -->
<div class="container mx-auto px-4 sm:px-8">
    <div wire:loading class="fixed top-0 left-0 w-screen h-screen flex items-center justify-center z-50">
        <div class="flex flex-col items-center justify-center h-full">
            <svg class="animate-spin h-16 w-16 text-blue-600 mr-3 mb-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="10" stroke-opacity="0.25" stroke-width="2" stroke="currentColor" fill="none"></circle>
                <path d="M12 2a10 10 0 00-9.5 7.5M12 2c2.5 0 5 1 7 2.5" stroke="currentColor" stroke-width="2" fill="none"></path>
            </svg>
        </div>
    </div>
    <div class="py-4">
        <div>
            <h2 class="text-2xl font-semibold leading-tight">Tipo de componentes</h2>
        </div>
        <div class="py-4">
             @can('save equipment')
                 <button  id="openModalButton" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 focus:outline-none focus:bg-blue-600 mt-4">
                     Crear tipo de componente
                 </button>
              @endcan
        </div>
        <div class="my-2 flex items-center">
            <div class="flex-1">
                <label for="search" class="block text-sm font-medium text-gray-700">Buscar:</label>
                <div class="relative">
                    <input id="search" type="text" placeholder="Buscar" class="block w-full bg-white border border-gray-400 text-gray-700 py-2 px-4 pr-8 rounded-l leading-tight focus:outline-none focus:border-gray-500 focus:bg-white focus:border-l focus:border-r" wire:model='search'>
                </div>
            </div>
            <button class="ml-4 px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 focus:outline-none focus:bg-blue-600 mt-4" wire:click="$refresh">Buscar</button>
        </div>
        <div class="-mx-4 sm:-mx-8 px-4 sm:px-8 py-4 overflow-x-auto">
            <div class="inline-block min-w-full shadow rounded-lg overflow-hidden">
                <table class="min-w-full leading-normal">
                    <thead>
                        <tr>
                            <th
                                class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider text-center">
                                Nombre
                            </th>
                            @can('actions equipment')
                            <th
                                class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider text-center">
                                Acciones
                            </th>
                            @endcan
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($type_components as $type_component)
                        <tr>
                            <td class="px-5 py-3 border-b border-gray-200 bg-white text-sm text-center">
                                {{ $type_component->name }}
                            </td>
                            @can('actions equipment')
                             <td class="px-5 py-3 border-b border-gray-200 bg-white text-sm text-center">
                                 <button class="mr-2"  wire:click="OpenModalEdit({{ $type_component->id }})">
                                     <i class="fas fa-edit fa-lg text-blue-300" title="Editar"></i>
                                 </button>
                                 <button class="ml-2" onclick="deleted('component-type.component-type', 'destroy',{{ $type_component->id }})" wire:loading.attr="disabled">
                                         <i class="fas fa-trash text-yellow-500" title="Eliminar"></i>
                                 </button>
                             </td>
                            @endcan
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="flex justify-center">
                    {{ $type_components->links() }}
                </div>
            </div>
        </div>
     @include('livewire.component-type.modal-create')
    </div>
    <script>
         const openModalButton = document.getElementById('openModalButton');
         const modalCreate = document.getElementById('modalCreate');
         openModalButton.addEventListener('click', function() {
             modalCreate.classList.remove('hidden');
         });
    </script>
 @push('scripts')
 <script>
     document.addEventListener('livewire:initialized', function () {
         Livewire.on('alertSuccess', (message) => {
             alertSuccess(message);
         });
         Livewire.on('alertFailed', (message) => {
             alertFailed(message);
         });
         Livewire.on('alertInfo', (message) => {
                 alertInfo(message);
         });
         Livewire.on('open-modal-edit-type-component', () => {
             const modalCreate = document.getElementById('modalCreate');
             modalCreate.classList.remove('hidden');
         });
     });
 </script>
 @endpush
 </div>
 