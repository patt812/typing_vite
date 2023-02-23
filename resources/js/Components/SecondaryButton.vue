<script setup>
defineProps({
    type: {
        type: String,
        default: 'button',
    },
});
</script>

<template>
    <button :type="type"
        class="inline-flex items-center px-4 py-2 bg-main border-black border-2 rounded-md font-extrabold text-xs text-black uppercase tracking-widest shadow-sm hover:opacity-60 focus:outline-none  focus:ring-0  active:shadow-lg  disabled:opacity-25 transition">
        <slot />
    </button>
</template>
