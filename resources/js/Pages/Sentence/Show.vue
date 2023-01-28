<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import { computed, onMounted, ref } from '@vue/runtime-core';
import AppLayout from '@/Layouts/AppLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue'
import DangerButton from '@/Components/DangerButton.vue';

import SentenceList from '@/Pages/Sentence/Partials/SentenceList.vue'

const props = defineProps({
    sentences: Array,
    status: String,
});

const sentence = ref(null);
const flush_message = computed(() => props.status);


onMounted(() => {
    sentence.value.focus();
});

const form = useForm({
    sentence: '',
    kana: ''
});

const updateForm = useForm({
    id: null,
    sentence: '',
    kana: ''
});

const canStore = computed(() => !form.kana || !form.sentence);
const canUpdate = computed(() => updateForm.id == null);

const store = () => {
    form.put(route('sentence.store'), {
        onSuccess: () => {
            form.reset();
        }
    });
};

const update = () => {
    updateForm.put(route('sentence.update'));
};

const erase = () => {
    updateForm.delete(route('sentence.delete'), {
        onSuccess: () => {
            updateForm.reset();
        }
    });
};

const fill = (sentence) => {
    if (sentence.id == updateForm.id) {
        updateForm.reset();
        return;
    }
    updateForm.id = sentence.id;
    updateForm.sentence = sentence.sentence;
    updateForm.kana = sentence.kana;
};
</script>

<template>
    <AppLayout>

        <Head title="文章管理" />

        <div>
            <SentenceList :sentences="sentences" @fill="fill" />

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

                <PrimaryButton :disabled="canStore">登録</PrimaryButton>
                <div v-if="flush_message" class="mb-4 font-medium text-sm text-green-600">
                    {{ flush_message }}
                </div>
            </form>

            <form @submit.prevent="update">
                <div>
                    <div class="flex">
                        <InputLabel for="sentence" value="文章" />
                        <TextInput id="sentence" ref="sentence" v-model="updateForm.sentence" required />
                    </div>
                    <InputError :message="updateForm.errors.sentence" />

                    <div class="flex">
                        <InputLabel for="kana" value="かな" />
                        <TextInput id="kana" v-model="updateForm.kana" required />
                    </div>
                    <InputError :message="updateForm.errors.kana" />
                </div>

                <div class="flex">
                    <PrimaryButton :disabled="canUpdate">更新</PrimaryButton>
                    <DangerButton @click.prevent="erase" :disabled="canUpdate">削除</DangerButton>
                </div>
                <div v-if="flush_message" class="mb-4 font-medium text-sm text-green-600">
                    {{ flush_message }}
                </div>
            </form>
        </div>
    </AppLayout>
</template>
