<!-- eslint-disable vue/multi-word-component-names -->
<script setup>
  import { computed } from 'vue';

  const emit = defineEmits(['update:checked']);

  const props = defineProps({
    checked: {
      type: [Array, Boolean],
      default: false,
    },
    value: {
      type: String,
      default: null,
    },
    dashed: {
      type: Boolean,
      default: false,
    },
  });

  const proxyChecked = computed({
    get() {
      return props.checked;
    },

    set(val) {
      emit('update:checked', val);
    },
  });
</script>

<template>
  <input
    v-model="proxyChecked"
    type="checkbox"
    :value="value"
    :class="{ 'border-dashed': dashed }"
    class="rounded bg-main text-black shadow-sm border-black border-2"
  />
</template>
