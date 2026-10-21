<template>
    <select ref="selectEl" :disabled="disabled" :multiple="multiple" class="w-full">
        <option v-if="!multiple && includeEmptyOption" value="">{{ allLabel }}</option>
        <option v-for="opt in normalizedOptions" :key="opt.value" :value="opt.value">
            {{ opt.label }}
        </option>
    </select>
</template>

<script setup>
import { computed, ref, onMounted, onBeforeUnmount, watch, nextTick } from 'vue';
import $ from 'jquery';

if (typeof window !== 'undefined') {
    window.$ = $;
    window.jQuery = $;
}

const props = defineProps({
    modelValue: {
        type: [String, Number, Array],
        default: '',
    },
    options: {
        type: Array,
        default: () => [],
    },
    allLabel: {
        type: String,
        default: 'Todos',
    },
    includeEmptyOption: {
        type: Boolean,
        default: true,
    },
    placeholder: {
        type: String,
        default: '',
    },
    multiple: {
        type: Boolean,
        default: false,
    },
    disabled: {
        type: Boolean,
        default: false,
    },
    labelKey: {
        type: String,
        default: 'name',
    },
    valueKey: {
        type: String,
        default: 'id',
    },
    search: {
        type: Boolean,
        default: true,
    },
    allowClear: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(['update:modelValue', 'change']);

const selectEl = ref(null);

const normalizedOptions = computed(() => {
    return (props.options || [])
        .map((o) => {
            const rawValue = o?.[props.valueKey];
            const rawLabel = o?.[props.labelKey] ?? o?.label ?? o?.name ?? o?.value ?? '';
            return {
                value: rawValue == null ? '' : String(rawValue),
                label: String(rawLabel),
            };
        })
        .filter((opt) => opt.value !== '');
});

const setValue = (value) => {
    if (!selectEl.value) return;
    const $el = $(selectEl.value);
    const normalized = props.multiple
        ? Array.isArray(value)
            ? value.map((v) => String(v))
            : []
        : value == null
          ? ''
          : String(value);
    $el.val(normalized).trigger('change.select2');
};

let select2LoadPromise = null;

// Guardas para evitar que al pulsar la X de limpiar (allowClear) se abra el desplegable
let clearGuards = [];

const teardownClearGuards = () => {
    clearGuards.forEach(({ el, handler }) => {
        el.removeEventListener('mousedown', handler, true);
        el.removeEventListener('touchstart', handler, true);
    });
    clearGuards = [];
};

const ensureSelect2 = async () => {
    if ($.fn && $.fn.select2) return;
    if (!select2LoadPromise) {
        select2LoadPromise = (async () => {
            const mod = await import('select2/dist/js/select2.full.js');
            const factory = mod?.default ?? mod;
            if (typeof factory === 'function') {
                factory(window, $);
            }
            if (!($.fn && $.fn.select2) && typeof window !== 'undefined' && window.jQuery?.fn?.select2) {
                $.fn.select2 = window.jQuery.fn.select2;
            }
        })();
    }
    await select2LoadPromise;
};

const destroySelect2 = ($el) => {
    if (!$el.data('select2')) return;
    try {
        // Cerrar antes de destruir evita dejar el desplegable abierto en el DOM
        if ($el.select2('isOpen')) $el.select2('close');
        $el.select2('destroy');
    } catch (_) {}
};

const init = async () => {
    if (!selectEl.value) return;
    await ensureSelect2();
    const $el = $(selectEl.value);
    $el.off('change.select2-vue');
    $el.off('select2:opening.select2-vue');
    teardownClearGuards();
    destroySelect2($el);

    $el.select2({
        width: '100%',
        allowClear: props.allowClear,
        closeOnSelect: !props.multiple,
        placeholder: props.placeholder || (props.includeEmptyOption ? props.allLabel : undefined),
        minimumResultsForSearch: props.search ? 0 : Infinity,
        language: {
            errorLoading: () => 'No se pudieron cargar los resultados',
            inputTooLong: (args) => `Elimine ${args.input.length - args.maximum} caracteres`,
            inputTooShort: (args) => `Ingrese ${args.minimum - args.input.length} caracteres más`,
            loadingMore: () => 'Cargando más resultados…',
            maximumSelected: (args) => `Solo puede seleccionar ${args.maximum} elemento${args.maximum === 1 ? '' : 's'}`,
            noResults: () => 'No hay resultados',
            searching: () => 'Buscando…',
            removeAllItems: () => 'Limpiar filtro',
        },
    });

    setValue(props.modelValue);

    // Select2 dispara un 'toggle' interno al limpiar con la X, lo que abre el desplegable.
    // Se detecta el clic sobre la X y se cancela esa apertura.
    let clearClicked = false;
    const containerEl = $el.data('select2')?.$container?.[0] ?? null;

    if (containerEl) {
        const onMouseDownCapture = (evt) => {
            const target = evt.target;
            clearClicked = Boolean(
                target && typeof target.closest === 'function' && target.closest('.select2-selection__clear')
            );
        };
        containerEl.addEventListener('mousedown', onMouseDownCapture, true);
        containerEl.addEventListener('touchstart', onMouseDownCapture, true);
        clearGuards.push({ el: containerEl, handler: onMouseDownCapture });
    }

    $el.on('select2:opening.select2-vue', (evt) => {
        if (clearClicked) {
            clearClicked = false;
            evt.preventDefault();
        }
    });

    $el.on('change.select2-vue', () => {
        const val = $el.val();
        const out = props.multiple ? (Array.isArray(val) ? val : []) : (val ?? '');
        emit('update:modelValue', out);
        emit('change', out);
    });
};

onMounted(async () => {
    await nextTick();
    await init();
});

onBeforeUnmount(() => {
    if (!selectEl.value) return;
    const $el = $(selectEl.value);
    $el.off('change.select2-vue');
    $el.off('select2:opening.select2-vue');
    teardownClearGuards();
    destroySelect2($el);
});

// Re-inicializar solo cuando las opciones realmente cambian; evita destruir y
// recrear Select2 (y su desplegable) por cada recarga del catálogo.
const optionsSignature = computed(() =>
    normalizedOptions.value.map((opt) => `${opt.value}\u0000${opt.label}`).join('\u0001')
);

watch(optionsSignature, async (value, oldValue) => {
    if (value === oldValue) return;
    await nextTick();
    await init();
});

watch(
    () => props.modelValue,
    (v) => {
        setValue(v);
    }
);

watch(
    () => props.disabled,
    (v) => {
        if (!selectEl.value) return;
        $(selectEl.value).prop('disabled', v);
    }
);

watch(
    () => props.multiple,
    async () => {
        await nextTick();
        await init();
    }
);
</script>
