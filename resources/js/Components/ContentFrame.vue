<script setup>
  import GuestRegister from '@/Components/GuestRegister.vue';

  defineProps({
    limitSize: {
      type: Boolean,
      default: true,
    },
    isGuest: {
      type: Boolean,
      default: false,
    },
  });
</script>

<template>
  <section
    :class="{
      'w-10/12': limitSize,
      'border-dashed': isGuest,
    }"
    class="max-w-4xl mx-auto mt-10 p-5 border-4 rounded-3xl border-black shadow-2xl relative"
  >
    <GuestRegister v-if="isGuest" />

    <div
      :class="{
        '!opacity-40': isGuest,
        '!pointer-events-none': isGuest,
        '!select-none': isGuest,
      }"
    >
      <div class="md:col-span-1 flex justify-between">
        <div class="px-4 sm:px-0">
          <h3 class="text-lg font-bold text-gray-900">
            <slot name="title" />
          </h3>
          <p class="mt-1 text-sm text-gray-600" />
        </div>
        <div class="px-4 sm:px-0" />
      </div>
      <div class="m-auto">
        <slot name="content" />
      </div>
    </div>
  </section>
</template>
