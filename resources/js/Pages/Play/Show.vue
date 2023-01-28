<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import Play from '@/Pages/Play/Partials/Play.vue'

defineProps({
    confirmsTwoFactorAuthentication: Boolean,
    sessions: Array,
    sentences: Array,
});
</script>

<template>
    <div>
        <Play :sentences="sentences" />
    </div>
</template>
