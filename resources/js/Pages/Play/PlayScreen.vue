<script setup>
  import { ref } from 'vue';
  import PlayTyping from '@/Pages/Play/Partials/PlayTyping.vue';

  const props = defineProps({
    sentences: {
      type: Array,
      required: true,
    },
    settings: {
      type: Object,
      default: () => ({
        volume: 0.5,
        use_type_sound: true,
        use_beep_sound: true,
      }),
    },
  });

  const volume = ref(props.settings.volume);

  const isMuted = ref(!props.settings.use_type_sound && !props.settings.use_beep_sound);
</script>

<template>
  <label for="sp-input">
    <div>
      <input id="sp-input" class="absolute opacity-0 top-[-1000px] w-1 h-1 z-[-1]" type="text" />
      <PlayTyping :sentences="sentences" :settings="settings" />
      <div class="flex mt-4 items-end justify-between">
        <div class="flex">
          <img
            :src="volume > 0 ? '/icons/volume-on.png' : '/icons/volume-off.png'"
            alt=""
            class="h-8"
          />
          <input
            v-if="!isMuted"
            v-model="volume"
            type="range"
            min="0"
            max="1"
            step="0.01"
            class="ml-3 accent-black"
          />
        </div>
      </div>
    </div>
  </label>
</template>
