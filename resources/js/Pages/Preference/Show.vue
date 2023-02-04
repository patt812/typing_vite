<script setup>
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { computed, onMounted, ref } from '@vue/runtime-core';
import AppLayout from '@/Layouts/AppLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import Checkbox from '@/Components/Checkbox.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue'
import DangerButton from '@/Components/DangerButton.vue';

import SentenceList from '@/Pages/Sentence/Partials/SentenceList.vue'

const user = usePage().props.user;

const props = defineProps({
    sentences: Array,
    status: String,
});

const form = useForm(user.settings.setting_preferences);

const error = ref('');

const isNoSentenceSelected = computed(() => {
    return props.sentences.every(s => !s.is_selected)
});

const store = () => {
    form.put(route('preference.store'));
};

const storeSentence = () => {
    error.value = ''
    usePage().props.flash.message = '';

    if (isNoSentenceSelected.value) {
        return error.value = '文章は１つ以上選択してください'
    }
    const form = useForm({
        'sentence': props.sentences
    });
    form.put(route('preference.sentence.store'), {
        only: ['sentences', 'flash']
    });
};

</script>

<template>
    <AppLayout>

        <Head title="出題設定" />

        <div>
            <div v-if="$page.props.flash.message" class="font-medium text-sm text-green-600">
                {{ $page.props.flash.message }}
            </div>

            <SentenceList :sentences="sentences" from="preference" @store="storeSentence" />

            <InputError :message="error" />
            <form @submit.prevent="store">

                <div class="flex">
                    <Checkbox id="is_random" v-model:checked="form.is_random" />
                    <InputLabel for="is_random" value="ランダムに出題する" />
                </div>

                <div class="flex">
                    <InputLabel value="出題数" for="sentences" />
                    <TextInput type="text" id="sentences" v-model="form.sentences" />
                </div>

                <PrimaryButton :disabled="form.processing">保存する</PrimaryButton>
            </form>
        </div>
    </AppLayout>
</template>
