<div>
    <!-- Modal container -->
    <div id="modal-{{ $field }}-container"
         class="fixed z-50 inset-0 overflow-y-auto hidden flex items-center justify-center py-4"
         wire:ignore.self>
        <!-- Overlay con backdrop blur -->
        <div id="modal-{{ $field }}-overlay"
             class="fixed inset-0 bg-gray-900 bg-opacity-75 backdrop-blur-sm transition-opacity z-40"
             onclick="closeModalSelect('{{ $field }}')"></div>

        <!-- Modal content -->
        <div id="modal-{{ $field }}" class="relative z-50 p-4 w-full max-w-2xl my-auto" onclick="event.stopPropagation(); return false;">
            <div class="relative bg-white rounded-xl shadow-2xl dark:bg-gray-800 overflow-hidden max-h-[90vh] flex flex-col" onclick="event.stopPropagation(); return false;">
                <!-- Modal header con gradiente -->
                <div class="flex items-center justify-between px-4 py-3 bg-gradient-to-r from-blue-500 to-blue-600 rounded-t-xl">
                    <div class="flex items-center space-x-2">
                        <div class="bg-white bg-opacity-20 rounded-lg p-1.5">
                            <i class="fas fa-list text-white text-lg"></i>
                        </div>
                        <h3 class="text-lg font-bold text-white">
                            Gestionar {{ $label }}
                        </h3>
                    </div>
                    <button type="button"
                            onclick="closeModalSelect('{{ $field }}')"
                            class="text-white hover:bg-white hover:bg-opacity-20 rounded-lg text-sm w-7 h-7 inline-flex justify-center items-center transition-colors duration-200">
                        <svg class="w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                        </svg>
                        <span class="sr-only">Close modal</span>
                    </button>
                </div>

                <!-- Modal body -->
                <div class="p-6 overflow-y-auto flex-1" onclick="event.stopPropagation(); return false;">
                    <!-- Formulario para crear/editar -->
                    <div id="edit-form-{{ $field }}" class="mb-4 bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900 dark:to-blue-800 rounded-lg p-3 border-2 border-blue-200 dark:border-blue-700" wire:key="edit-form-{{ $field }}-{{ $editingId ?? 0 }}">
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center">
                                <div class="bg-blue-500 rounded-full p-1.5 mr-2">
                                    <i class="fas {{ $editingId ? 'fa-edit' : 'fa-plus-circle' }} text-white text-sm"></i>
                                </div>
                                <div>
                                    <h4 class="text-base font-bold text-gray-800 dark:text-white">
                                        {{ $editingId ? 'Editar' : 'Crear nuevo' }} {{ $label }}
                                    </h4>
                                    <p class="text-xs text-gray-600 dark:text-gray-300">
                                        {{ $editingId ? 'Modifica el nombre del registro' : 'Ingresa un nuevo registro' }}
                                    </p>
                                </div>
                            </div>
                            @if($editingId)
                                <button type="button"
                                        wire:click="cancelEdit"
                                        class="px-2 py-1.5 bg-gray-500 hover:bg-gray-600 text-white rounded-lg transition-colors duration-200"
                                        title="Cancelar edición">
                                    <i class="fas fa-times text-xs"></i>
                                </button>
                            @endif
                        </div>
                        <div class="bg-white dark:bg-gray-700 rounded-lg p-3 shadow-sm">
                            <label class="flex items-center mb-1.5 text-xs font-medium text-gray-700 dark:text-gray-300">
                                <i class="fas fa-tag mr-1.5 text-blue-600 text-xs"></i>
                                Nombre
                                <span class="text-red-500 ml-1">*</span>
                            </label>
                            <input type="text"
                                   id="input-value-new-{{ $field }}"
                                   wire:key="input-{{ $field }}-{{ $editingId ?? 0 }}"
                                   class="w-full bg-white border-2 border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 block px-3 py-2 dark:bg-gray-600 dark:border-gray-500 dark:text-white transition-all duration-200"
                                   placeholder="Ejemplo: Nombre del {{ strtolower($label) }}"
                                   wire:model="valueNew"
                                   wire:keydown.enter.prevent="saveField"
                                   onclick="event.stopPropagation();"
                                   onfocus="event.stopPropagation();"
                                   onkeydown="event.stopPropagation();"
                                   onkeyup="event.stopPropagation();">
                            @error('valueNew')
                                <span class="flex items-center mt-1.5 text-xs text-red-600">
                                    <i class="fas fa-exclamation-circle mr-1 text-xs"></i>
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>
                        <div class="flex justify-end mt-3">
                            <button type="button"
                                    wire:key="btn-save-{{ $field }}-{{ $editingId ?? 0 }}"
                                    wire:click="saveField"
                                    class="flex items-center px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg transition-colors duration-200 text-sm font-medium shadow-md hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                <i class="fas {{ $editingId ? 'fa-edit' : 'fa-save' }} mr-1.5 text-xs"></i>
                                <span wire:key="btn-text-{{ $field }}-{{ $editingId ?? 0 }}">{{ $editingId ? 'Actualizar' : 'Crear y Agregar' }}</span>
                            </button>
                        </div>
                    </div>

                    <!-- Lista de registros existentes -->
                    <div>
                        <div class="flex items-center justify-between mb-3 pb-2 border-b border-gray-200">
                            <div class="flex items-center">
                                <i class="fas fa-list-ul text-blue-600 mr-1.5 text-sm"></i>
                                <h4 class="text-base font-semibold text-gray-800 dark:text-white">
                                    Registros Existentes
                                </h4>
                            </div>
                            <div class="relative">
                                <input type="text"
                                       wire:model.live.debounce.300ms="search"
                                       placeholder="Buscar..."
                                       class="pl-8 pr-3 py-1.5 text-xs border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 w-32">
                                <i class="fas fa-search absolute left-2 top-1/2 transform -translate-y-1/2 text-gray-400 text-xs"></i>
                            </div>
                        </div>

                        <div class="max-h-56 overflow-y-auto">
                            @if($items->count() > 0)
                                <div class="space-y-1.5">
                                    @foreach($items as $item)
                                        <div class="flex items-center justify-between bg-gray-50 dark:bg-gray-700 rounded-lg p-2.5 border border-gray-200 dark:border-gray-600 hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors duration-200" onclick="event.stopPropagation(); return false;">
                                            <div class="flex items-center space-x-2 flex-1" onclick="event.stopPropagation();">
                                                <div class="bg-blue-100 dark:bg-blue-900 rounded-full w-6 h-6 flex items-center justify-center flex-shrink-0">
                                                    <i class="fas fa-tag text-blue-600 dark:text-blue-300 text-xs"></i>
                                                </div>
                                                <span class="text-xs font-medium text-gray-900 dark:text-white truncate">
                                                    {{ $item->name }}
                                                </span>
                                                @if(isset($item->status) && $item->status)
                                                    <span class="px-1.5 py-0.5 text-xs rounded-full flex-shrink-0
                                                        @if($item->status == 'active') bg-green-100 text-green-800 border border-green-300
                                                        @else bg-gray-100 text-gray-800 border border-gray-300
                                                        @endif">
                                                        {{ ucfirst($item->status) }}
                                                    </span>
                                                @endif
                                            </div>
                                            <div class="flex items-center space-x-1.5 flex-shrink-0" onclick="event.stopPropagation();">
                                                <button type="button"
                                                        wire:click.stop="startEdit({{ $item->id }})"
                                                        wire:loading.attr="disabled"
                                                        onclick="event.stopPropagation(); console.log('Botón editar clickeado, ID: {{ $item->id }}');"
                                                        class="px-2 py-1.5 bg-blue-500 hover:bg-blue-600 text-white rounded-lg transition-colors duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
                                                        title="Editar">
                                                    <i class="fas fa-edit text-xs"></i>
                                                </button>
                                                <button type="button"
                                                        wire:click.stop.prevent="deleteItem({{ $item->id }})"
                                                        wire:confirm="¿Estás seguro de que deseas eliminar este registro?"
                                                        wire:loading.attr="disabled"
                                                        onclick="event.stopPropagation(); return false;"
                                                        class="px-2 py-1.5 bg-red-500 hover:bg-red-600 text-white rounded-lg transition-colors duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
                                                        title="Eliminar">
                                                    <i class="fas fa-trash text-xs"></i>
                                                </button>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-8 bg-gray-50 dark:bg-gray-700 rounded-lg border-2 border-dashed border-gray-300 dark:border-gray-500">
                                    @if($search)
                                        <i class="fas fa-search text-3xl text-gray-400 mb-3"></i>
                                        <p class="text-sm text-gray-500">No se encontraron resultados para "{{ $search }}"</p>
                                    @else
                                        <i class="fas fa-inbox text-3xl text-gray-400 mb-3"></i>
                                        <p class="text-sm text-gray-500">No hay registros disponibles</p>
                                        <p class="text-xs text-gray-400 mt-1">Crea el primer registro usando el formulario de arriba</p>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="flex justify-end px-4 py-3 bg-gray-50 dark:bg-gray-700 border-t border-gray-200 dark:border-gray-600 flex-shrink-0">
                    <button type="button"
                            onclick="closeModalSelect('{{ $field }}')"
                            class="flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition-colors duration-200 text-sm font-medium">
                        <i class="fas fa-times mr-1.5 text-xs"></i>
                        Cerrar
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function closeModalSelect(field) {
        // Verificar si el clic fue en el input para evitar cerrar cuando se escribe
        const activeElement = document.activeElement;
        if (activeElement && activeElement.id === `input-value-new-${field}`) {
            return; // No cerrar si el input está activo
        }

        const container = document.getElementById(`modal-${field}-container`);
        if (container) {
            // Desmarcar como debe estar abierto
            container.removeAttribute('data-should-be-open');
            container.classList.add('hidden');
            const overlay = document.getElementById(`modal-${field}-overlay`);
            if (overlay) {
                overlay.classList.add('hidden');
            }
        }

        // Resetear el formulario del modal si está abierto
        if (typeof Livewire !== 'undefined') {
            Livewire.dispatch('reset-modal-select', { field: field });
        }
    }

    function openModalSelect(field) {
        const container = document.getElementById(`modal-${field}-container`);
        if (container) {
            // Marcar como debe estar abierto
            container.setAttribute('data-should-be-open', 'true');
            container.classList.remove('hidden');
            const overlay = document.getElementById(`modal-${field}-overlay`);
            if (overlay) {
                overlay.classList.remove('hidden');
            }
        }
    }

    function ensureModalStaysOpen(field) {
        const container = document.getElementById(`modal-${field}-container`);
        if (container && !container.classList.contains('hidden')) {
            // El modal está abierto, forzar que permanezca así
            container.classList.remove('hidden');
            const overlay = document.getElementById(`modal-${field}-overlay`);
            if (overlay) {
                overlay.classList.remove('hidden');
            }
        }
    }

    // Función para escuchar eventos de Livewire
    function setupLivewireListeners() {
        // Livewire 3
        if (typeof window.Livewire !== 'undefined') {
            // Escuchar el evento para abrir el modal
            window.Livewire.on('open-modal-select', (field) => {
                // En Livewire 3, el parámetro puede venir como string o como parte de un objeto
                const fieldValue = typeof field === 'string' ? field : (field?.detail || field);
                if (fieldValue) {
                    openModalSelect(fieldValue);
                }
            });

            // Escuchar el evento para cerrar el modal
            window.Livewire.on('close-modal-select', (field) => {
                const fieldValue = typeof field === 'string' ? field : (field?.detail || field);
                if (fieldValue) {
                    closeModalSelect(fieldValue);
                }
            });

            // Escuchar evento para mantener el modal abierto
            window.Livewire.on('keep-modal-open', (field) => {
                const fieldValue = typeof field === 'string' ? field : (field?.detail || field);
                if (fieldValue) {
                    // Forzar que el modal permanezca abierto después de múltiples intentos
                    ensureModalStaysOpen(fieldValue);
                    setTimeout(() => ensureModalStaysOpen(fieldValue), 50);
                    setTimeout(() => ensureModalStaysOpen(fieldValue), 100);
                    setTimeout(() => ensureModalStaysOpen(fieldValue), 200);
                    setTimeout(() => ensureModalStaysOpen(fieldValue), 500);
                }
            });

            // Variable para evitar ejecuciones múltiples del mismo evento
            let editProcessing = false;
            let lastEditField = null;
            let lastEditId = null;

            // Escuchar evento cuando se inicia la edición
            window.Livewire.on('edit-item-started', (data) => {
                // Livewire puede enviar los datos como array, objeto, o dentro de detail
                let editData = null;

                if (Array.isArray(data) && data.length > 0) {
                    editData = data[0]; // Tomar el primer elemento si es array
                } else if (data && typeof data === 'object') {
                    if (data.detail) {
                        editData = Array.isArray(data.detail) ? data.detail[0] : data.detail;
                    } else {
                        editData = data;
                    }
                }

                if (editData && editData.field) {
                    const field = editData.field;
                    const itemName = editData.name || '';
                    const itemId = editData.id || null;

                    // Evitar procesar el mismo evento múltiples veces
                    if (editProcessing && lastEditField === field && lastEditId === itemId) {
                        return;
                    }

                    editProcessing = true;
                    lastEditField = field;
                    lastEditId = itemId;

                    // Mantener modal abierto INMEDIATAMENTE y múltiples veces
                    const ensureModalOpen = () => {
                        if (typeof window.markModalAsOpen === 'function') {
                            window.markModalAsOpen(field);
                        }
                        const container = document.getElementById(`modal-${field}-container`);
                        if (container) {
                            container.classList.remove('hidden');
                            container.setAttribute('data-should-be-open', 'true');
                            const overlay = document.getElementById(`modal-${field}-overlay`);
                            if (overlay) {
                                overlay.classList.remove('hidden');
                            }
                        }
                    };

                    ensureModalOpen();

                    // Función para actualizar el input
                    function updateInputForEdit() {
                        ensureModalOpen(); // Siempre asegurar que el modal esté abierto

                        const input = document.getElementById(`input-value-new-${field}`);
                        if (input) {
                            input.value = itemName;

                            // Sincronizar con Livewire (Livewire 3 usa asignación directa)
                            if (window.Livewire) {
                                const wireIdElement = input.closest('[wire\\:id]');
                                if (wireIdElement) {
                                    const wireId = wireIdElement.getAttribute('wire:id');
                                    if (wireId) {
                                        const component = window.Livewire.find(wireId);
                                        if (component && component.$wire) {
                                            // En Livewire 3, actualizar propiedades directamente
                                            component.$wire.valueNew = itemName;
                                            if (itemId) {
                                                component.$wire.editingId = itemId;
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }

                    // Ejecutar inmediatamente
                    updateInputForEdit();
                    ensureModalOpen();

                    // Ejecutar después de delays reducidos
                    setTimeout(() => {
                        updateInputForEdit();
                        ensureModalOpen();
                    }, 50);

                    setTimeout(() => {
                        updateInputForEdit();
                        ensureModalOpen();
                        // Scroll al formulario
                        const formElement = document.getElementById(`edit-form-${field}`);
                        if (formElement) {
                            formElement.scrollIntoView({ behavior: 'smooth', block: 'start' });
                        }
                    }, 150);

                    // Resetear flag después de un tiempo
                    setTimeout(() => {
                        editProcessing = false;
                        ensureModalOpen();
                    }, 500);
                } else {
                    console.error('editData no tiene field:', editData);
                }
            });

            // Hook de Livewire para prevenir cierre del modal durante actualizaciones
            if (window.Livewire && window.Livewire.hook) {
                window.Livewire.hook('morph.updated', ({ el, component }) => {
                    // Después de que Livewire actualiza el DOM, verificar si el modal debe permanecer abierto
                    const modalContainers = document.querySelectorAll('[id^="modal-"][id$="-container"]');
                    modalContainers.forEach(container => {
                        if (container && !container.classList.contains('hidden')) {
                            const field = container.id.replace('modal-', '').replace('-container', '');
                            ensureModalStaysOpen(field);
                        }
                    });
                });
            }
        }
    }

    // Inicializar cuando el DOM esté listo
    document.addEventListener('DOMContentLoaded', function () {
        setupLivewireListeners();
    });

    // También escuchar cuando Livewire se inicializa
    document.addEventListener('livewire:initialized', function () {
        setupLivewireListeners();
    });

    // Para Livewire 2 (retrocompatibilidad)
    document.addEventListener('livewire:load', function () {
        setupLivewireListeners();
    });

    // Escuchar evento para hacer scroll al formulario cuando se edita
    document.addEventListener('livewire:initialized', function () {
        Livewire.on('scroll-to-form', () => {
            const formElement = document.querySelector('[id^="edit-form-"]');
            if (formElement) {
                formElement.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });

        // Interceptar TODAS las actualizaciones de Livewire para mantener modales abiertos
        Livewire.hook('morph.before', ({ el, component }) => {
            // Guardar estado de modales abiertos ANTES de que Livewire actualice el DOM
            document.querySelectorAll('[id^="modal-"][id$="-container"]').forEach(container => {
                if (!container.classList.contains('hidden')) {
                    container.setAttribute('data-should-be-open', 'true');
                    // Guardar el estado actual del display y clases
                    container.setAttribute('data-original-display', window.getComputedStyle(container).display);
                    container.setAttribute('data-original-classes', container.className);
                }
            });
        });

        // Interceptar durante cada paso del proceso de morphing
        Livewire.hook('morph', ({ el, component }) => {
            // En cada paso, mantener los modales abiertos
            requestAnimationFrame(() => {
                document.querySelectorAll('[id^="modal-"][id$="-container"][data-should-be-open="true"]').forEach(container => {
                    container.classList.remove('hidden');
                    container.style.display = container.getAttribute('data-original-display') || 'flex';
                });
            });
        });

        Livewire.hook('morph.updated', ({ el, component }) => {
            // Restaurar modales abiertos INMEDIATAMENTE después de que Livewire actualice el DOM
            requestAnimationFrame(() => {
                document.querySelectorAll('[id^="modal-"][id$="-container"]').forEach(container => {
                    if (container.hasAttribute('data-should-be-open')) {
                        // Forzar que el modal esté visible
                        container.classList.remove('hidden');
                        const originalDisplay = container.getAttribute('data-original-display') || 'flex';
                        container.style.display = originalDisplay;
                        container.style.visibility = 'visible';
                        container.style.opacity = '1';

                        const field = container.id.replace('modal-', '').replace('-container', '');
                        const overlay = document.getElementById(`modal-${field}-overlay`);
                        if (overlay) {
                            overlay.classList.remove('hidden');
                            overlay.style.display = 'block';
                            overlay.style.visibility = 'visible';
                        }
                    }
                });
            });
        });
    });

    // Función global para marcar un modal como "debe permanecer abierto"
    window.markModalAsOpen = function(field) {
        const container = document.getElementById(`modal-${field}-container`);
        if (container) {
            container.setAttribute('data-should-be-open', 'true');
            container.classList.remove('hidden');
            const overlay = document.getElementById(`modal-${field}-overlay`);
            if (overlay) {
                overlay.classList.remove('hidden');
            }
        }
    };

    // Función global para desmarcar un modal
    window.unmarkModalAsOpen = function(field) {
        const container = document.getElementById(`modal-${field}-container`);
        if (container) {
            container.removeAttribute('data-should-be-open');
        }
    };
</script>
