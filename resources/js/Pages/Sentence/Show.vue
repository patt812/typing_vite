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
import SecondaryButton from '@/Components/SecondaryButton.vue';
import FlashMessage from '@/Components/FlashMessage.vue';
import ContentFrame from '@/Components/ContentFrame.vue';

const props = defineProps({
    sentences: Array,
    status: String,
});

const sentence = ref(null);

const isInsert = ref(true);

const insertTemplate = ref({
    sentence: '',
    kana: '',
    error: '',
})
const inserts = ref(Array.from({ length: 10 }, () => ({ ...insertTemplate.value })));

onMounted(() => {
    sentence.value.focus();
});

const form = useForm({
    id: null,
    sentence: '',
    kana: '',
});

const updateForm = useForm({
    id: null,
    sentence: '',
    kana: '',
});

const isSentenceAndKanaFilled = computed(() => !form.kana || !form.sentence);

const appendInserts = (num) => {
    for (let i = 0; i < num; i++) {
        inserts.value.push({ ...insertTemplate.value });
    }
}

const submit = () => {
    if (isInsert.value) {
        form.put(route('sentence.store'), {
            onSuccess: () => {
                form.reset();
            }
        });
    } else {
        form.put(route('sentence.update'));
    }
};

const erase = () => {
    form.delete(route('sentence.delete'), {
        onSuccess: () => {
            form.reset();
        }
    });
};

const processingBulkStore = ref(false);

const bulkStore = () => {
    const kanaList = props.sentences.map(sentence => sentence.kana);
    let isValid = true;
    for (let i = 0; i < inserts.value.length; i++) {
        const row = inserts.value[i];
        if (!row.kana && !row.sentence) {
            inserts.value.splice(i--, 1);
            continue;
        }
        if (row.kana && kanaList.includes(row.kana)) {
            inserts.value[i].error = '入力したかなは既に登録されているか、重複しています。';
            isValid = false;
            continue;
        } else {
            kanaList.push(row.kana);
        }

        if (!row.sentence) {
            inserts.value[i].error = '文章は入力必須です。';
            isValid = false;
            continue;
        }
        if (!row.kana) {
            inserts.value[i].error = 'かなは入力必須です。';
            isValid = false;
            continue;
        }
        const invalidChars = new Set(row.kana.match(/[^ぁ-ゞァ-ヾ！-／：-＠［-｀｛-～]/g));
        if (invalidChars.size) {
            inserts.value[i].error = 'かなに使用できない文字が含まれています：' + Array.from(invalidChars).join(',');
            isValid = false;
        } else {
            inserts.value[i].error = '';
        }
    }
    if (!inserts.value.length) {
        appendInserts(10);
        return;
    }
    if (!isValid) return;

    const bulkStoreData = useForm({ sentences: inserts.value });
    processingBulkStore.value = true;
    bulkStoreData.post(route('sentences.store'), {
        onSuccess: () => {
            inserts.value = Array.from({ length: 10 }, () => ({ ...insertTemplate.value }));
        },
        onFinish: () => processingBulkStore.value = false,
    });
};

const onFill = (sentence) => {
    if (!sentence || sentence.id == form.id) {
        form.reset();
        isInsert.value = true;
        return;
    }
    form.id = sentence.id;
    form.sentence = sentence.sentence;
    form.kana = sentence.kana;
    isInsert.value = false;
};
</script>

<template>
    <AppLayout>

        <Head title="文章管理" />

        <div>
            <FlashMessage />

            <ContentFrame>
                <template #title>文章登録</template>

                <template #content>
                    <SentenceList :sentences="sentences" :from="'sentence'" @fill="onFill" />

                    <form class="mt-4" @submit.prevent="submit">
                        <div>
                            <InputLabel for="sentence" value="文章" />
                            <TextInput ref="sentence" id="sentence" type="text" class="mt-1 w-full max-w-2xl"
                                v-model="form.sentence" required />
                            <InputError :message="form.errors.sentence" />

                            <InputLabel for="kana" class="mt-3" value="かな" />
                            <TextInput id="kana" type="text" class="mt-1 w-full max-w-2xl" v-model="form.kana" required />
                            <InputError :message="form.errors.kana" />
                        </div>

                        <div class="w-full mt-6 flex align-middle items-center justify-center">
                            <PrimaryButton :disabled="isSentenceAndKanaFilled">
                                {{ isInsert ? '登録' : '更新' }}
                            </PrimaryButton>
                            <DangerButton class="ml-16" @click.prevent="erase" :disabled="!form.id">
                                削除
                            </DangerButton>
                        </div>
                    </form>
                </template>
            </ContentFrame>

            <ContentFrame>
                <template #title>一括登録</template>

                <template #content>
                    <div class="mt-1">文章とかなが入力されていない行は登録時に自動削除されます。</div>
                    <div class="mt-1">Shift + Enterでも登録できます。</div>
                    <SecondaryButton class="mt-3" @click="appendInserts(1)">+1</SecondaryButton>
                    <SecondaryButton class="ml-3" @click="appendInserts(5)">+5</SecondaryButton>

                    <form @submit.prevent="">
                        <div class="w-full flex my-2 border-b-2 border-black">
                            <div class="w-10" />
                            <div class="font-bold w-6/12 text-center">文章</div>
                            <div class="font-bold w-6/12 text-center">かな</div>
                            <div class="w-10" />
                            <div class="w-8" />
                        </div>

                        <div class="mt-1 scroll-bar overflow-auto max-h-80 w-full">
                            <div v-for="(row, index) in inserts" :key="row">
                                <div class="flex w-full mb-1">
                                    <div class="w-10 text-center">{{ index + 1 }}</div>
                                    <div class="w-6/12">
                                        <TextInput class="w-full" v-model="row.sentence" :disabled="processingBulkStore"
                                            @keydown.shift.enter="bulkStore" />
                                    </div>
                                    <div class="w-6/12">
                                        <TextInput class="w-full ml-1" v-model="row.kana" :disabled="processingBulkStore"
                                            @keydown.shift.enter="bulkStore" />
                                    </div>
                                    <div class="flex items-center cursor-pointer w-fit ml-3"
                                        @click="inserts.splice(index, 0, { ...row, error: '' })">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-fit h-[18px]" viewBox="0 0 64 64"
                                            aria-labelledby="title" aria-describedby="desc" role="img"
                                            xmlns:xlink="http://www.w3.org/1999/xlink">
                                            <title>Copy</title>
                                            <desc>A line styled icon from Orion Icon Library.</desc>
                                            <path data-name="layer2" fill="none" stroke="#000000" stroke-miterlimit="10"
                                                stroke-width="4" d="M16 48H2V2h46v14" stroke-linejoin="round"
                                                stroke-linecap="round"></path>
                                            <path data-name="layer1" fill="none" stroke="#000000" stroke-miterlimit="10"
                                                stroke-width="4" d="M16 16h46v46H16z" stroke-linejoin="round"
                                                stroke-linecap="round"></path>
                                        </svg>
                                    </div>
                                    <div v-if="inserts.length > 1" class="flex items-center cursor-pointer w-fit ml-1 mr-1"
                                        @click="inserts.splice(index, 1)">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-fit h-6" viewBox="0 0 64 64"
                                            aria-labelledby="title" aria-describedby="desc" role="img"
                                            xmlns:xlink="http://www.w3.org/1999/xlink">
                                            <title>Close</title>
                                            <desc>A line styled icon from Orion Icon Library.</desc>
                                            <path data-name="layer1" fill="none" stroke="#ff0000" stroke-miterlimit="10"
                                                stroke-width="6" d="M41.999 20.002l-22 22m22 0L20 20"
                                                stroke-linejoin="round" stroke-linecap="round"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="mb-2 pl-9">
                                    <InputError :message="row.error" />
                                </div>
                            </div>
                        </div>

                        <PrimaryButton type="button" class="mt-4 mx-auto" @click="bulkStore"
                            :disabled="processingBulkStore">
                            一括登録
                        </PrimaryButton>
                    </form>
                </template>
            </ContentFrame>
        </div>
    </AppLayout>
</template>
