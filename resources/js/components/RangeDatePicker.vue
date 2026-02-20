<template>
  <div class="relative" @keydown.esc="close">
    <input
      type="text"
      :value="displayValue"
      :placeholder="placeholder || 'AAAA-MM-DD - AAAA-MM-DD'"
      readonly
      class="block w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 px-4 py-2.5 transition-all duration-200 cursor-pointer"
      @click="toggle"
    />
    <div
      v-if="isOpen"
      class="absolute z-20 mt-2 bg-white border border-gray-200 rounded-lg shadow-lg p-3 w-[min(340px,90vw)]"
    >
      <div class="grid grid-cols-2 gap-2">
        <div>
          <label class="block text-xs font-medium text-gray-600 mb-1">Desde</label>
          <input
            type="date"
            v-model="localStart"
            class="block w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 px-3 py-2"
            @change="applyStart"
          />
        </div>
        <div>
          <label class="block text-xs font-medium text-gray-600 mb-1">Hasta</label>
          <input
            type="date"
            v-model="localEnd"
            class="block w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 px-3 py-2"
            @change="applyEnd"
          />
        </div>
      </div>
      <div class="mt-3 flex items-center justify-between">
        <button
          type="button"
          class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-md text-sm"
          @click="clear"
        >
          Limpiar
        </button>
        <div class="flex items-center gap-2">
          <button
            type="button"
            class="px-3 py-1.5 bg-blue-500 hover:bg-blue-600 text-white rounded-md text-sm disabled:opacity-50 disabled:cursor-not-allowed"
            :disabled="!canApply"
            @click="apply"
          >
            Aplicar
          </button>
          <button
            type="button"
            class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-md text-sm"
            @click="close"
          >
            Cerrar
          </button>
        </div>
      </div>
    </div>
  </div>
  <div v-if="isOpen" class="fixed inset-0 z-10" @click="close"></div>
</template>

<script setup>
import { ref, computed, watch, onMounted, onBeforeUnmount } from 'vue';

const props = defineProps({
  start: { type: String, default: '' },
  end: { type: String, default: '' },
  placeholder: { type: String, default: '' }
});

const emit = defineEmits(['update:start', 'update:end', 'apply']);

const isOpen = ref(false);
const localStart = ref(props.start || '');
const localEnd = ref(props.end || '');

watch(() => props.start, (v) => { localStart.value = v || ''; });
watch(() => props.end, (v) => { localEnd.value = v || ''; });

const displayValue = computed(() => {
  const s = (localStart.value || '').trim();
  const e = (localEnd.value || '').trim();
  if (s && e) return `${s} - ${e}`;
  if (s) return s;
  if (e) return `hasta ${e}`;
  return '';
});

const toggle = () => { isOpen.value = !isOpen.value; };
const close = () => { isOpen.value = false; };
const clear = () => {
  localStart.value = '';
  localEnd.value = '';
  emit('update:start', '');
  emit('update:end', '');
  emit('apply');
};
const applyStart = () => { emit('update:start', localStart.value || ''); };
const applyEnd = () => { emit('update:end', localEnd.value || ''); };

const canApply = computed(() => {
  return Boolean((localStart.value || '').trim()) && Boolean((localEnd.value || '').trim());
});
const apply = () => {
  if (!canApply.value) return;
  emit('apply');
  close();
};

const onKeydown = (e) => {
  if (e.key === 'Escape') close();
};
onMounted(() => {
  window.addEventListener('keydown', onKeydown);
});
onBeforeUnmount(() => {
  window.removeEventListener('keydown', onKeydown);
});
</script>
