<script setup>
import { ref, watch, nextTick } from 'vue';

const props = defineProps({
  modelValue: { type: Number, default: 0 },
});

const emit = defineEmits(['update:modelValue']);


const brlFormatter = new Intl.NumberFormat('pt-BR', {
  style: 'currency',
  currency: 'BRL',
  minimumFractionDigits: 2,
  maximumFractionDigits: 2,
});


function formatDisplayValue(cents) {
  if (!cents || cents === 0) return '';
  return brlFormatter.format(cents / 100);
}


function formatEditValue(cents) {
  if (!cents || cents === 0) return '';
  return (cents / 100).toFixed(2).replace('.', ',');
}

const inputValue = ref(formatDisplayValue(props.modelValue));
const isEditing = ref(false);


watch(() => props.modelValue, (newVal, oldVal) => {

  if (!isEditing.value && newVal !== oldVal) {
    inputValue.value = formatDisplayValue(newVal);
  }
}, { immediate: true });

function onFocus() {
  isEditing.value = true;
  if (props.modelValue && props.modelValue > 0) {
    inputValue.value = formatEditValue(props.modelValue);
  }
}

function onInput(e) {
  let onlyNumbers = e.target.value.replace(/\D/g, '');
  
  if (!onlyNumbers || onlyNumbers === '0') {
    inputValue.value = '';
    emit('update:modelValue', 0);
    return;
  }

  let cents = parseInt(onlyNumbers, 10);
  
  emit('update:modelValue', cents);
  
  inputValue.value = formatEditValue(cents);
}

function onBlur() {
  isEditing.value = false;
  
  nextTick(() => {
    if (props.modelValue && props.modelValue > 0) {
      inputValue.value = formatDisplayValue(props.modelValue);
    } else {
      inputValue.value = '';
    }
  });
}

function onKeydown(e) {
  const allowedKeys = ['Backspace', 'Delete', 'Tab', 'Escape', 'Enter', 'ArrowLeft', 'ArrowRight'];
  
  if (allowedKeys.includes(e.key)) {
    return;
  }
  
  if (!/^\d$/.test(e.key)) {
    e.preventDefault();
  }
}
</script>

<template>
  <input
    type="text"
    v-model="inputValue"
    @input="onInput"
    @focus="onFocus"
    @blur="onBlur"
    @keydown="onKeydown"
    placeholder="R$ 0,00"
    class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 
           focus:ring-opacity-50 rounded-md shadow-sm"
  />
</template>