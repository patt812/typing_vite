<script setup>
import Play from '@/Pages/Play/Partials/Play.vue'
import { usePage } from '@inertiajs/inertia-vue3';
import { ref } from 'vue';

defineProps({
    confirmsTwoFactorAuthentication: Boolean,
    sessions: Array,
    sentences: Array,
});

const settings = usePage().props.value.user.settings.setting_plays;

const volume = ref(settings.volume);

const isMuted = ref(!settings.use_type_sound && !settings.use_beep_sound);

</script>

<template>
    <label for="sp-input">
        <div>
            <input class="absolute opacity-0 top-[-1000px] w-1 h-1 z-[-1]" id="sp-input" type="text" />
            <Play :sentences="sentences" :volume="volume" />
            <div class="flex mt-4 items-end justify-between">
                <div class="flex">
                    <img :src="volume > 0 ? '/icons/volume-on.png' : '/icons/volume-off.png'" alt="" class="h-8">
                    <input v-if="!isMuted" type="range" v-model="volume" min="0" max="1" step="0.01"
                        class="ml-3 accent-black" />
                </div>
            </div>
        </div>
    </label>
</template>
