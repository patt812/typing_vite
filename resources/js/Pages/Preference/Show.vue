<script setup>
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { computed, onMounted, ref } from '@vue/runtime-core';
import AppLayout from '@/Layouts/AppLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import FlashMessage from '@/Components/FlashMessage.vue';
import Checkbox from '@/Components/Checkbox.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue'

import SentenceList from '@/Pages/Sentence/Partials/SentenceList.vue'
import ContentFrame from '@/Components/ContentFrame.vue';

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

        <ContentFrame>
            <FlashMessage />

            <SentenceList :sentences="sentences" from="preference" @store="storeSentence" />
            <InputError :message="error" />

            <form @submit.prevent="store">

                <div class="flex">
                    <Checkbox id="limit_wpm" v-model:checked="form.limit_wpm" />
                    <InputLabel for="limit_wpm" value="WPMで選ぶ" />
                </div>
                <div v-show="form.limit_wpm" class="flex">
                    <TextInput type="number" v-model="form.min_wpm" />
                    <span>〜</span>
                    <TextInput type="number" v-model="form.max_wpm" />
                </div>

                <div class="flex">
                    <Checkbox id="limit_accuracy" v-model:checked="form.limit_accuracy" />
                    <InputLabel for="limit_accuracy" value="正答率で選ぶ" />
                </div>
                <div v-show="form.limit_accuracy" class="flex">
                    <TextInput type="number" v-model="form.min_accuracy" />
                    <span>〜</span>
                    <TextInput type="number" v-model="form.max_accuracy" />
                </div>

                <InputLabel for="prior_no_stats" value="統計がない文章の優先度" />
                <div class="flex">
                    <input type="radio" name="prior-type" id="prior-every" v-model="form.prior_no_stats" value="0">
                    <InputLabel for="prior-every" value="未選択でも優先して出題" />

                    <input type="radio" name="prior-type" id="prior-selected" v-model="form.prior_no_stats" value="1">
                    <InputLabel for="prior-selected" value="選択された中で優先して出題" />

                    <input type="radio" name="prior-type" id="prior-none" v-model="form.prior_no_stats" value="2">
                    <InputLabel for="prior-none" value="設定しない" />
                </div>

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
        </ContentFrame>
    </AppLayout>
</template>
