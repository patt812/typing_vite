<script setup>
  import { Link } from '@inertiajs/vue3';
  import { computed } from 'vue';

  const props = defineProps({
    active: {
      type: Boolean,
      default: false,
    },
    href: {
      type: String,
      default: null,
    },
    as: {
      type: String,
      default: null,
    },
  });

  const classes = computed(() =>
    props.active
      ? 'block pl-3 pr-4 py-2 border-l- text-base font-medium50 focus:outline-none transition'
      : 'block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-gray-600 transition'
  );
</script>

<template>
  <div>
    <button v-if="as == 'button'" :class="classes" class="w-full text-left">
      <slot />
    </button>

    <Link v-else :href="href" :class="classes">
      <slot />
    </Link>
  </div>
</template>
