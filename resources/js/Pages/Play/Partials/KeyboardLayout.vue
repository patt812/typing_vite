<script setup>
import { ref } from 'vue';

defineProps({
  correct: {
    type: String,
    required: true,
  },
});

const keyBoard = ref([
  ['', '1', '2', '3', '4', '5', '6', '7', '8', '9', '0', '-', '^', '¥', ''],
  ['', 'q', 'w', 'e', 'r', 't', 'y', 'u', 'i', 'o', 'p', '@', '[', ''],
  ['', 'a', 's', 'd', 'f', 'g', 'h', 'j', 'k', 'l', ';', ':', ']', ''],
  ['', 'z', 'x', 'c', 'v', 'b', 'n', 'm', ',', '.', '/', '_', ''],
]);
</script>

<template>
  <div
    v-for="(keys, index) in keyBoard" :key="keys"
    class="keys" :class="{ [`px-${index}`]: index > 0 }"
  >
    <div
      v-for="key in keys" :key="key" class="key"
      :class="{ 'target': correct && key == correct.toLowerCase() }"
    >
      {{ key }}
    </div>
  </div>
</template>

<style scoped>
.keys {
    @apply select-none mt-2 flex w-full h-12 justify-center items-center gap-x-1
}

.key {
    @apply flex justify-center items-center border rounded-[5px] border-black;
    @apply w-full text-center h-full;
}

.target {
    @apply bg-black text-white
}
</style>
