<!-- eslint-disable vue/multi-word-component-names -->
<script setup>
import ContentFrame from '@/Components/ContentFrame.vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import PlayScreen from '@/Pages/Play/PlayScreen.vue';

defineProps({
  sentences: {
    type: Array,
    required: true,
  },
});
</script>

<template>
  <AppLayout title="測定">
    <ContentFrame>
      <template #content>
        <PlayScreen :sentences="sentences" />
      </template>
    </ContentFrame>
  </AppLayout>
</template>
