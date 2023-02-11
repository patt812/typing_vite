<script setup>
</script>

<template>
    <div>
        <div v-if="$page.props.flash.message" class="font-medium text-sm text-green-600">
            {{ $page.props.flash.message }}
        </div>
    </div>
</template>
