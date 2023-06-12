<script setup>
  import { Link } from '@inertiajs/vue3';
  import { computed } from 'vue';

  const props = defineProps({
    href: {
      type: String,
      required: true,
    },
    active: {
      type: Boolean,
      default: false,
    },
  });

  const classes = computed(() =>
    props.active
      ? 'inline-flex items-center px-1 pt-1 text-sm font-bold leading-5 text-gray-900 focus:outline-none transition'
      : 'inline-flex items-center px-1 pt-1 border-transparent text-sm font-medium leading-5 text-gray-500 focus:outline-none focus:text-black hover:text-black transition'
  );
</script>

<template>
  <Link :href="href" :class="classes">
    <slot />
  </Link>
</template>
