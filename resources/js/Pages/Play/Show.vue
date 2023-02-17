<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import Play from '@/Pages/Play/Partials/Play.vue'
import TextInput from '@/Components/TextInput.vue';
import { usePage } from '@inertiajs/vue3';
import { ref } from 'vue';

defineProps({
    confirmsTwoFactorAuthentication: Boolean,
    sessions: Array,
    sentences: Array,
});

const settings = usePage().props.user.settings.setting_plays;

const volume = ref(settings.volume);

const isMuted = ref(!settings.use_type_sound && !settings.use_beep_sound);

</script>

<template>
    <div>
        <Play :sentences="sentences" :volume="volume" />
        <TextInput v-if="!isMuted" type="range" v-model="volume" min="0" max="1" step="0.01" />
</div>
</template>
