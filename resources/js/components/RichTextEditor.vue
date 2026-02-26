<template>
  <div class="border border-gray-300 rounded-lg bg-white">
    <div
      class="flex items-center flex-wrap border-b border-gray-200 bg-gray-50 rounded-t-lg"
      :class="toolbarClass"
    >
      <button type="button" :class="buttonClass" @click="cmd('bold')" title="Negrita"><i class="fas fa-bold"></i></button>
      <button type="button" :class="buttonClass" @click="cmd('italic')" title="Cursiva"><i class="fas fa-italic"></i></button>
      <button type="button" :class="buttonClass" @click="cmd('underline')" title="Subrayado"><i class="fas fa-underline"></i></button>
      <span class="w-px h-5 bg-gray-300 mx-1"></span>
      <button type="button" :class="buttonClass" @click="cmd('insertUnorderedList')" title="Viñetas"><i class="fas fa-list-ul"></i></button>
      <button type="button" :class="buttonClass" @click="cmd('insertOrderedList')" title="Numerada"><i class="fas fa-list-ol"></i></button>
      <span class="w-px h-5 bg-gray-300 mx-1"></span>
      <button type="button" :class="buttonClass" @click="clearFormat" title="Limpiar formato"><i class="fas fa-eraser"></i></button>
    </div>
    <div
      ref="editor"
      class="px-4 py-3 text-sm text-gray-900 focus:outline-none whitespace-pre-wrap"
      :class="contentClass"
      contenteditable="true"
      :data-placeholder="placeholder"
      @input="onInput"
      @paste="onPaste"
      @keydown="onKeyDown"
    ></div>
  </div>
 </template>

<script setup>
import { ref, watch, onMounted, computed } from 'vue';

const props = defineProps({
  modelValue: { type: String, default: '' },
  placeholder: { type: String, default: 'Escribe aquí...' },
  compact: { type: Boolean, default: false }
});

const emit = defineEmits(['update:modelValue']);

const editor = ref(null);

const allowedTags = ['B','STRONG','I','EM','U','UL','OL','LI','BR','P','DIV'];

const setHTML = (html) => {
  if (!editor.value) return;
  const safe = sanitize(html || '');
  if (editor.value.innerHTML !== safe) {
    editor.value.innerHTML = safe;
  }
  ensurePlaceholder();
};

const getHTML = () => (editor.value ? editor.value.innerHTML : '');

watch(() => props.modelValue, (v) => {
  if (v !== getHTML()) setHTML(v || '');
});

onMounted(() => {
  setHTML(props.modelValue || '');
});

const sanitize = (html) => {
  const temp = document.createElement('div');
  temp.innerHTML = html || '';
  const walk = (node) => {
    for (let i = node.childNodes.length - 1; i >= 0; i--) {
      const child = node.childNodes[i];
      if (child.nodeType === 1) {
        if (!allowedTags.includes(child.nodeName)) {
          // Replace disallowed element with its children
          while (child.firstChild) node.insertBefore(child.firstChild, child);
          node.removeChild(child);
          continue;
        }
        walk(child);
      } else if (child.nodeType === 8) {
        node.removeChild(child); // remove comments
      }
    }
  };
  walk(temp);
  return temp.innerHTML;
};

const onInput = () => {
  ensurePlaceholder();
  emit('update:modelValue', sanitize(getHTML()));
};

const onPaste = (e) => {
  e.preventDefault();
  let text = (e.clipboardData || window.clipboardData).getData('text/plain');
  text = text.replace(/\r\n/g, '\n').replace(/\r/g, '\n');
  text = text.replace(/\t/g, '    ');
  document.execCommand('insertText', false, text);
};

const onKeyDown = (e) => {
  if (e.key === 'Enter') {
    e.preventDefault();
    if (e.shiftKey) {
      document.execCommand('insertLineBreak', false, null);
    } else {
      document.execCommand('insertParagraph', false, null);
    }
    onInput();
  } else if (e.key === 'Tab') {
    e.preventDefault();
    document.execCommand('insertText', false, '    ');
    onInput();
  }
};

const cmd = (command) => {
  document.execCommand(command, false, null);
  onInput();
};

const clearFormat = () => {
  document.execCommand('removeFormat', false, null);
  // Remove lists
  document.execCommand('insertParagraph', false, null);
  onInput();
};

const ensurePlaceholder = () => {
  if (!editor.value) return;
  const empty = editor.value.textContent.trim().length === 0 && editor.value.innerHTML.indexOf('<img') === -1;
  if (empty) {
    editor.value.classList.add('empty');
  } else {
    editor.value.classList.remove('empty');
  }
};

const contentClass = computed(() => {
  const minH = props.compact ? 'min-h-[60px]' : 'min-h-[120px]';
  const radius = props.compact ? 'rounded-lg' : 'rounded-b-lg';
  return `${minH} ${radius}`;
});

const toolbarClass = computed(() => (props.compact
  ? 'px-1 py-1 gap-1 text-xs'
  : 'px-2 py-1.5 gap-1.5 text-sm'
));
const buttonClass = computed(() => (props.compact
  ? 'px-1.5 py-0.5 rounded hover:bg-gray-200'
  : 'px-2 py-1 rounded hover:bg-gray-200'
));
</script>

<style scoped>
.empty:before {
  content: attr(data-placeholder);
  color: #9ca3af;
}
</style>
