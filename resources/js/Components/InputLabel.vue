<script setup>
  defineProps({
    value: {
      type: String,
      default: '',
    },
  });
</script>

<template>
  <label class="block font-medium text-black">
    <span v-if="value">{{ value }}</span>
    <span v-else>
      <slot />
    </span>
  </label>
</template>
