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

const props = defineProps({
    sentences: Array,
    status: String,
});

const sentence = ref(null);
const flush_message = computed(() => props.status);

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
    sentence: '',
    kana: '',
});

const updateForm = useForm({
    id: null,
    sentence: '',
    kana: '',
});

const canStore = computed(() => !form.kana || !form.sentence);
const canUpdate = computed(() => updateForm.id == null);

const appendInserts = (num) => {
    for (let i = 0; i < num; i++) {
        inserts.value.push({ ...insertTemplate.value });
    }
}

const store = () => {
    form.put(route('sentence.store'), {
        onSuccess: () => {
            form.reset();
        }
    });
};

const bulkStore = () => {
    const kanaList = props.sentences.map(sentence => sentence.kana);
    let isValid = true;
    for (let i = 0; i < inserts.value.length; i++) {
        const row = inserts.value[i];
        if (!row.kana && !row.sentence) {
            inserts.value.splice(i--, 1);
            continue;
        }
        if (kanaList.includes(row.kana)) {
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

const processingBulkStore = ref(false);

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
            <FlashMessage />

            <SentenceList :sentences="sentences" :from="'sentence'" @fill="fill" />

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
            </form>

            <div>
                <div>一括登録</div>
                <div>※文章とかなが入力されていない行は登録時に自動削除されます</div>
                <SecondaryButton @click="appendInserts(1)">+1</SecondaryButton>
                <SecondaryButton @click="appendInserts(5)">+5</SecondaryButton>

                <form @submit.prevent="">
                    <table>
                        <thead>
                            <th />
                            <th>文章</th>
                            <th>かな</th>
                        </thead>

                        <tbody>
                            <tr v-for="(row, index) in inserts" :key="row">
                                <td>{{ index + 1 }}</td>
                                <td>
                                    <TextInput v-model="row.sentence" :disabled="processingBulkStore" />
                                </td>
                                <td>
                                    <TextInput v-model="row.kana" :disabled="processingBulkStore" />
                                </td>
                                <td class="cursor-pointer" @click="inserts.splice(index, 0, { ...row })">
                                    複製
                                </td>
                                <td v-if="inserts.length > 1" class="cursor-pointer" @click="inserts.splice(index, 1)">
                                    削除
                                </td>
                                <td>
                                    <InputError :message="row.error" />
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <InputError :message="updateForm.errors.kana" />
                    <PrimaryButton type="button" @click="bulkStore" :disabled="processingBulkStore">
                        登録
                    </PrimaryButton>
                </form>
            </div>
        </div>
    </AppLayout>
</template>
