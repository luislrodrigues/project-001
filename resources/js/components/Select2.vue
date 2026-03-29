<template>
    <select ref="selectEl" :disabled="disabled" :multiple="multiple" class="w-full">
        <option v-if="!multiple" value="">{{ allLabel }}</option>
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

const init = async () => {
    if (!selectEl.value) return;
    await ensureSelect2();
    const $el = $(selectEl.value);
    $el.off('change.select2-vue');
    try {
        $el.select2('destroy');
    } catch (_) {}

    $el.select2({
        width: '100%',
        allowClear: false,
        minimumResultsForSearch: props.search ? 0 : Infinity,
    });

    setValue(props.modelValue);

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
    try {
        $el.select2('destroy');
    } catch (_) {}
});

watch(
    () => props.options,
    async () => {
        await nextTick();
        await init();
    },
    { deep: true }
);

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
