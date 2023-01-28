<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue'
import { ref } from '@vue/reactivity';

const props = defineProps({
    sentence: Object,
    flush_message: String,
});
</script>

<template>
    <div>
        <form @submit.prevent="store">
            <div>
                <div class="flex">
                    <InputLabel for="sentence" value="文章" />
                    <TextInput id="sentence" ref="sentence" v-model="form.sentence" required />
                </div>
                <InputError :message="form.errors.sentence" />

                <div class="flex">
                    <InputLabel for="kana" value="かな" />
                    <TextInput id="kana" v-model="form.kana" required />
                </div>
                <InputError :message="form.errors.kana" />
            </div>

            <PrimaryButton>登録</PrimaryButton>
            <div v-if="flush_message" class="mb-4 font-medium text-sm text-green-600">
                {{ flush_message }}
            </div>
        </form>
    </div>
</template>
