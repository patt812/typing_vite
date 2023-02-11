<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import Typing from '@/Typing/typing.js'
import { router } from '@inertiajs/core';
import { ref } from '@vue/reactivity';
import { onMounted, onUnmounted, watch } from '@vue/runtime-core';
import { Inertia } from '@inertiajs/inertia';

const props = defineProps({
    sentences: Array,
    filled: Boolean
});

const typing = ref(new Typing());

const getSentence = (counts) => {
    router.visit(route('sentences', { num: 3 }), {
        only: ['sentences', 'filled'],
        onSuccess: () => {
            typing.value.isPlayable = true;
            if (props.sentences && props.sentences.length) {
                typing.value.prepare(props.sentences);
            }
        }
    });

    typing.value.prepare(props.sentences);
};

watch(() => typing.value.isFinished, (isFinished) => {
    if (isFinished) {
        const result = typing.value.result;
        Inertia.post(route('play.store'), { result });
    }
});

onMounted(() => {
    console.log(props.filled);
    if (!props.sentences || props.filled) return;
    if (props.sentences && props.sentences.length) {
        typing.value.prepare(props.sentences);
    }
    document.addEventListener("keydown", {
        handleEvent: typing.value.start,
        typing: typing.value,
    });
});

onUnmounted(() => {

})
</script>

<template>
    <div>
        <div v-show="!typing.isStarted && !typing.isFinished">
            {{ typing.dialog ? typing.dialog : typing.DEFAULT_GAME_DIALOG }}
        </div>

        <div v-show="typing.isStarted && !typing.isFinished">
            <div>
                {{ typing.sentence.sentences[typing.sentence.current] }}
            </div>
            <div>
                {{ typing.sentence.kanasDisplay[typing.sentence.current] }}
            </div>
            <div>
                <span style="color: yellowgreen">{{
                    typing.sentence.displayRoma.substr(0, typing.statistics.correct)
                }}</span><span>{{ typing.sentence.displayRoma.substr(typing.statistics.correct) }}
                </span>
            </div>
            <div>{{ typing.statistics }}</div>
            <div>{{ typing.statistics.time }}</div>
            <div>{{ typing.countDown }}</div>
        </div>

        <div v-show="!typing.isStarted && typing.isFinished">
            <div>
                <div class="flex">
                    <div>正答率</div>
                    <div>{{ typing.statistics.accuracy }}%</div>
                </div>

                <div class="flex">
                    <div>合計時間</div>
                    <div>{{ typing.statistics.time }}</div>
                </div>

                <div class="flex">
                    <div>WPM</div>
                    <div>{{ typing.statistics.getTotalWPM() }}</div>
                </div>

                <div class="flex">
                    <div>総タイプ数</div>
                    <div>{{ typing.statistics.totalCorrect + typing.statistics.totalMistake }}</div>
                </div>

                <div class="flex">
                    <div>最大連続ミス数</div>
                    <div>{{ typing.statistics.maxMissStreak }}</div>
                </div>
            </div>

            <button @click="getSentence(0)">もう一度</button>
        </div>
    </div>
</template>
