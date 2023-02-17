<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import Typing from '@/Typing/typing.js'
import { router } from '@inertiajs/core';
import { ref } from '@vue/reactivity';
import { onMounted, onUnmounted, watch } from '@vue/runtime-core';
import { Inertia } from '@inertiajs/inertia';
import { usePage } from '@inertiajs/vue3';

const props = defineProps({
    sentences: Array,
    filled: Boolean,
    volume: Number,
});

const settings = usePage().props.user.settings.setting_plays;

const typing = ref(new Typing({
    volume: props.volume,
    use_type_sound: settings.use_type_sound,
    use_beep_sound: settings.use_beep_sound
}));

const stat = ref(null);

const getStats = (i) => {
    const stats = {
        average: typing.value.result.accuracies[i],
        wpm: typing.value.result.avarages[i],
        missStreak: typing.value.result.missStreaks[i],
    }
    stat.value = stats;
};

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

watch(() => props.volume, (volume) => {
    typing.value.changeVolume(volume);
})

onMounted(() => {
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
    typing.value.reset();
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

        <div v-if="!typing.isStarted && typing.isFinished">
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

            <div v-for="i of typing.sentence.ids.length" :key="i" class="cursor-pointer">
                <div @click="getStats(i)">{{ typing.sentence.sentences[i] }}</div>
                <div @click="getStats(i)">{{ typing.sentence.kanasDisplay[i] }}</div>
            </div>

            <div v-if="stat">
                <div>WPM：{{ stat.wpm }}</div>
                <div>正答率：{{ stat.average }}%</div>
                <div>連続ミス数：{{ stat.missStreak }}</div>
            </div>

            <button @click="getSentence(0)">もう一度</button>
        </div>
    </div>
</template>
