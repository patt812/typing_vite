<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue'
import { ref } from '@vue/reactivity';

const props = defineProps({
    confirmsTwoFactorAuthentication: Boolean,
    sessions: Array,
    sentences: Array,
});
</script>

<template>
    <div>
        <div v-for="(sentence, index) in sentences" :key="sentence.id">
            <div class="flex cursor-pointer" @click="$emit('fill', sentence)">
                <div class="mr-2">{{ index + 1 }}</div>
                <div class="mr-2">{{ sentence.sentence }}</div>
                <div>{{ sentence.kana }}</div>
            </div>
        </div>
    </div>
</template>
