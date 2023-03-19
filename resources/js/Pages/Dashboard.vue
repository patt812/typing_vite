<script setup>
import ContentFrame from '@/Components/ContentFrame.vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import Play from '@/Pages/Play/Show.vue';
const props = defineProps({
    confirmsTwoFactorAuthentication: Boolean,
    sessions: Array,
    sentences: Array,
});
</script>

<template>
    <AppLayout title="測定">
        <ContentFrame>
            <template #content>
                <Play :sentences="sentences" />
            </template>
        </ContentFrame>
    </AppLayout>
</template>
