<div>
    <div id="modal-{{ $field }}" class="fixed z-10 inset-0 overflow-y-auto hidden">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="bg-white rounded-lg shadow-xl p-6">
                <h2 class="text-lg font-semibold">{{ $label }}</h2>
                <input type="text" class="block w-full border-gray-300 rounded-md mt-4" placeholder="{{ $label }}" wire:model="valueNew">
                @error('valueNew') <span class="text-sm text-red-500">{{ $message }}</span> @enderror
                <div class="mt-4 flex justify-end">
                    <button type="button" class="px-4 py-2 bg-gray-500 text-white rounded-md mr-2" onclick="closeModal('{{ $field }}')">
                        Cancelar
                    </button>
                    <button type="button" class="px-4 py-2 bg-blue-500 text-white rounded-md" wire:click="saveField">
                        Guardar
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Escuchar el evento para abrir el modal
        Livewire.on('open-modal-select', (field) => {
            const modal = document.getElementById(`modal-${field}`);
            if (modal) {
                modal.classList.remove('hidden'); // Mostrar el modal
            }
        });

        // Escuchar el evento para cerrar el modal
        Livewire.on('close-modal-select', (field) => {
            const modal = document.getElementById(`modal-${field}`);
            if (modal) {
                modal.classList.add('hidden'); // Ocultar el modal
            }
        });
    });

    // Función para cerrar el modal desde el botón "Cancelar"
    function closeModal(field) {
        const modal = document.getElementById(`modal-${field}`);
        if (modal) {
            modal.classList.add('hidden'); // Ocultar el modal
        }
    }
</script>
