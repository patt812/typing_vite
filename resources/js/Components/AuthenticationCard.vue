<script setup>
import ContentFrame from '@/Components/ContentFrame.vue';
</script>

<template>
    <div class="bg-main min-h-screen max-w-[2560px] mx-auto relative mt-[-10px] flex flex-col justify-center items-center">
        <div class="absolute z-10 top-10 -left-10 bg-black text-white px-16 py-1 -rotate-45 font-bold">
            {{ $page.props.app_name }}
        </div>
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
