<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import { computed, onMounted, ref } from '@vue/runtime-core';
import AppLayout from '@/Layouts/AppLayout.vue';
import FlashMessage from '@/Components/FlashMessage.vue';
import Modal from '@/Components/Modal.vue';
import InputLabel from '@/Components/InputLabel.vue';
import Checkbox from '@/Components/Checkbox.vue';
import DangerButton from '@/Components/DangerButton.vue';
import SentenceList from '@/Pages/Sentence/Partials/SentenceList.vue'

const props = defineProps({
    sentences: Array,
    userStats: Object,
});

const selected = ref(null);

const resetConfirm = ref(false);

const deleteWithSentence = ref(false);

const updateForm = useForm({
    id: null,
    sentence: '',
    kana: ''
});

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

const fill = (sentence) => {
    selected.value = sentence;
};

</script>

<template>
    <AppLayout>

        <Head title="文章管理" />

        <div>
            <FlashMessage />

            <SentenceList :sentences="sentences" :from="'stats'" @fill="fill" />

            <div v-if="selected">
                <div v-if="selected.stat">
                    <div>プレイ数：{{ selected.stat.played }}</div>
                    <div>パーフェクト数：{{ selected.stat.perfect }}</div>
                    <div>最低WPM：{{ selected.stat.min_wpm }}</div>
                    <div>最高WPM：{{ selected.stat.max_wpm }}</div>
                    <div>平均WPM：{{ selected.stat.ave_wpm }}</div>
                    <div>最低正答率：{{ selected.stat.min_accuracy }}%</div>
                    <div>最高正答率：{{ selected.stat.max_accuracy }}%</div>
                    <div>平均正答率：{{ selected.stat.ave_accuracy }}%</div>
                    <div>最大連続ミス数：{{ selected.stat.max_miss_streak }}</div>

                    <DangerButton @click="resetConfirm = true">リセットする</DangerButton>
                </div>
                <div v-else>
                    統計はまだありません。
                </div>
            </div>
        </div>

        <div>
            <div>総合統計</div>
            <div>WPM：{{ userStats.wpm.toFixed(2).replace('.00', '') }}</div>
            <div>最高WPM：{{ userStats.max_wpm }}</div>
            <div>正答率：{{ userStats.accuracy.toFixed(2).replace('.00', '') }}%</div>
            <div>打鍵数：{{ userStats.typed }}</div>
            <div>プレイ回数：{{ userStats.played }}</div>
            <div>プレイ時間：{{ formatSeconds(userStats.played_seconds) }}</div>
        </div>

        <Modal :show="resetConfirm" @close="resetConfirm = false">
            <div v-if="selected && selected.stat">
                <div>この統計をリセットしますか？</div>
                <div>この操作は元に戻せません。</div>
                <div>{{ selected.sentence }}</div>
                <div class="flex">
                    <Checkbox v-model:checked="deleteWithSentence" id="delete_sentence" />
                    <InputLabel for="delete_sentence">文章も削除する</InputLabel>
                </div>
                <DangerButton @click="reset">リセット</DangerButton>
            </div>
        </Modal>
    </AppLayout>
</template>
