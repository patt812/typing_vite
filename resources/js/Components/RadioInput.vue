<script setup>
import { computed, onMounted, ref } from 'vue';

const props = defineProps({
  modelValue: {
    type: String,
    default: '',
  },
  checked: {
    type: [Array, Boolean],
    default: false,
  },
});

const emit = defineEmits(['update:modelValue']);

const input = ref(null);

onMounted(() => {
  if (input.value.hasAttribute('autofocus')) {
    input.value.focus();
  }
});

defineExpose({ focus: () => input.value.focus() });

const proxyChecked = computed({
  get() {
    return props.checked;
  },

  set(val) {
    // eslint-disable-next-line vue/require-explicit-emits
    emit('update:checked', val);
  },
});
</script>

<template>
  <input
    ref="input" v-model="proxyChecked" type="radio"
    class="bg-main border-2 border-black border-dashed checked:bg-black
    focus:ring-black focus:checked:bg-black focus:opacity-70
    focus:ring-opacity-50 active:checked:ring-black active:checked:bg-black
    hover:checked:bg-black hover:ring-black hover:opacity-60 shadow-sm"
    :value="modelValue" @input="$emit('update:modelValue', $event.target.value)"
  >
</template>
