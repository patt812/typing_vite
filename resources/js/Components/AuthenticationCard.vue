<script setup>
import ContentFrame from '@/Components/ContentFrame.vue';
</script>

<template>
    <div class="bg-main min-h-screen mt-[-10px] flex flex-col justify-center items-center">
        <div>
            <slot name="logo" />
        </div>

        <ContentFrame :limit-size="false">
            <template #content>
                <slot />
            </template>
        </ContentFrame>
    </div>
</template>
