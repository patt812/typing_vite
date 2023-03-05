<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import { computed, onMounted, ref } from '@vue/runtime-core';
import AppLayout from '@/Layouts/AppLayout.vue';
import Modal from '@/Components/Modal.vue';
import InputLabel from '@/Components/InputLabel.vue';
import Checkbox from '@/Components/Checkbox.vue';
import DangerButton from '@/Components/DangerButton.vue';
import SentenceList from '@/Pages/Sentence/Partials/SentenceList.vue'
import ContentFrame from '@/Components/ContentFrame.vue';

const props = defineProps({
    sentences: Array,
    userStats: Object,
});

const selected = ref(null);

const resetConfirm = ref(false);

const resetAllConfirm = ref(false);

const deleteWithSentence = ref(false);

const updateForm = useForm({
    id: null,
    sentence: '',
    kana: ''
});

const resetAllForm = useForm({});

const statsCount = computed(() => props.sentences.filter(s => s.stat !== null).length);

const formatSeconds = (seconds) => {
    const hours = (seconds / 3600) | 0;
    const minutes = ((seconds % 3600) / 60) | 0;
    return `${hours}:${('00' + minutes).slice(-2)}:${('00' + ((seconds % 60) | 0)).slice(-2)}`;
}

const reset = () => {
    const form = useForm({
        delete_sentence: deleteWithSentence.value,
    });
    form.delete(route('stats.reset', selected.value.id), {
        onSuccess: () => {
            selected.value = null;
            resetConfirm.value = false;
            deleteWithSentence.value = false;
        }
    });
};

const resetAll = () => {
    resetAllForm.delete(route('stats.reset.all'), {
        onSuccess: () => {
            resetAllConfirm.value = false;
        }
    });
};

const fill = (sentence) => {
    selected.value = sentence;
};

</script>

<template>
    <AppLayout>

        <Head title="統計管理" />

        <ContentFrame>
            <template #title>個別統計</template>

            <template #content>
                <div>


                    <SentenceList :sentences="sentences" :from="'stats'" :focus-input="true" @fill="fill" />


                    <div v-if="selected" class="text-center mt-4">
                        <div v-if="selected.stat">
                            <div class="mb-1">プレイ数：{{ selected.stat.played }}</div>
                            <div class="mb-1">パーフェクト数：{{ selected.stat.perfect }}</div>
                            <div class="mb-1">最低WPM：{{ selected.stat.min_wpm }}</div>
                            <div class="mb-1">最高WPM：{{ selected.stat.max_wpm }}</div>
                            <div class="mb-1">平均WPM：{{ selected.stat.ave_wpm }}</div>
                            <div class="mb-1">最低正答率：{{ selected.stat.min_accuracy }}%</div>
                            <div class="mb-1">最高正答率：{{ selected.stat.max_accuracy }}%</div>
                            <div class="mb-1">平均正答率：{{ selected.stat.ave_accuracy }}%</div>
                            <div class="mb-1">最大連続ミス数：{{ selected.stat.max_miss_streak }}</div>

                            <DangerButton class="mt-4 mx-auto" @click="resetConfirm = true">
                                リセット
                            </DangerButton>
                        </div>
                        <div v-else>
                            統計はまだありません。
                        </div>
                    </div>
                    <div class="text-center mt-4" v-else>
                        文章をクリックすると統計を表示できます。
                    </div>
                </div>
            </template>
        </ContentFrame>

        <ContentFrame>
            <template #title>総合統計</template>

            <template #content>
                <div class="text-center mb-4">
                    <div class="mt-1">WPM：{{ userStats.wpm.toFixed(2).replace('.00', '') }}</div>
                    <div class="mt-1">最高WPM：{{ userStats.max_wpm }}</div>
                    <div class="mt-1">正答率：{{ userStats.accuracy.toFixed(2).replace('.00', '') }}%</div>
                    <div class="mt-1">打鍵数：{{ userStats.typed }}</div>
                    <div class="mt-1">プレイ回数：{{ userStats.played }}</div>
                    <div class="mt-1">プレイ時間：{{ formatSeconds(userStats.played_seconds) }}</div>
                </div>
            </template>
        </ContentFrame>
        <DangerButton class="mt-10 mx-auto shadow-xl" @click="resetAllConfirm = true">すべての統計をリセット</DangerButton>

        <Modal v-if="resetConfirm" :show="resetConfirm" @close="resetConfirm = false">
            <div class="mb-4 text-lg font-bold">統計のリセット</div>
            <div v-if="selected && selected.stat">
                <div>この統計をリセットしますか？</div>
                <div class="mt-2">この操作は元に戻せません。</div>
                <div class="mt-5 text-center font-bold">{{ selected.sentence }}</div>
                <div class="flex justify-center items-center align-middle mt-4">
                    <Checkbox v-model:checked="deleteWithSentence" :dashed="true" id="delete_sentence" />
                    <InputLabel for="delete_sentence" class="ml-2 !text-red-600 font-bold">文章も削除する</InputLabel>
                </div>
                <DangerButton @click="reset" class="mt-6 mx-auto">リセット</DangerButton>
            </div>
        </Modal>

        <Modal v-if="resetAllConfirm" :show="resetAllConfirm" @close="resetAllConfirm = false">
            <div class="mb-4 text-lg font-bold">すべての統計をリセット</div>
            <div>
                <div>
                    <span v-if="statsCount">{{ statsCount }}件の統計と</span>
                    <span>総合統計をリセットしますか？</span>
                </div>
                <div class="mt-2">この操作は元に戻せません。</div>
                <DangerButton class="mt-4 mx-auto" :disabled="resetAllForm.processing" @click="resetAll">
                    リセット
                </DangerButton>
            </div>
        </Modal>
    </AppLayout>
</template>
