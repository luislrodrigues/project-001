<template>
    <div class="relative">
        <input
            :id="id"
            :value="inputValue"
            :type="type"
            :placeholder="placeholder"
            :disabled="disabled"
            :class="inputClass"
            @input="onInput"
            @keyup.enter="$emit('enter')"
        />
        <div v-if="icon" class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
            <i :class="[icon, 'text-gray-400']"></i>
        </div>
        <button
            v-if="hasValue"
            type="button"
            class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-red-500 transition-colors"
            title="Limpiar este filtro"
            @click="clear"
        >
            <i class="fas fa-times-circle"></i>
        </button>
    </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    modelValue: {
        type: [String, Number],
        default: '',
    },
    id: {
        type: String,
        default: '',
    },
    type: {
        type: String,
        default: 'text',
    },
    placeholder: {
        type: String,
        default: '',
    },
    icon: {
        type: [String, Boolean],
        default: 'fas fa-search',
    },
    inputClass: {
        type: String,
        default: 'block w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 pl-10 pr-10 py-2.5 transition-all duration-200',
    },
    disabled: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(['update:modelValue', 'input', 'clear', 'enter']);

const hasValue = computed(() => props.modelValue !== '' && props.modelValue !== null && props.modelValue !== undefined);

const inputValue = computed(() => (props.modelValue === null || props.modelValue === undefined ? '' : props.modelValue));

const onInput = (event) => {
    emit('update:modelValue', event.target.value);
    emit('input', event.target.value);
};

const clear = () => {
    emit('update:modelValue', '');
    emit('clear');
};
</script>
