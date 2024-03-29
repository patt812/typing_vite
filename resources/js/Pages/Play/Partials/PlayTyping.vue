<script setup>
  import { router } from '@inertiajs/core';
  import { useForm, usePage } from '@inertiajs/vue3';
  import { ref, computed, onMounted, onUnmounted, watch } from 'vue';
  import SecondaryButton from '@/Components/SecondaryButton.vue';
  import KeyboardLayout from '@/Pages/Play/Partials/KeyboardLayout.vue';
  import SpeedMeter from '@/Pages/Play/Partials/SpeedMeter.vue';
  import TypingAccuracy from '@/Pages/Play/Partials/TypingAccuracy.vue';
  import Typing from '@/Typing/typing';

  const props = defineProps({
    sentences: {
      type: Array,
      required: true,
    },
    filled: {
      type: Boolean,
      default: null,
    },
    settings: {
      type: Object,
      default: () => ({
        volume: 0.5,
        use_type_sound: true,
        use_beep_sound: true,
      }),
    },
  });

  const guestPrefix = ref(usePage().props.user.id === null ? 'guest.' : '');

  const typing = ref(
    new Typing({
      volume: props.volume,
      use_type_sound: props.settings.use_type_sound,
      use_beep_sound: props.settings.use_beep_sound,
    })
  );

  const stat = ref(null);

  const correctKey = computed(() => {
    const yetNotCorrects = typing.value.sentence.displayRoma.slice(typing.value.statistics.correct);
    if (!yetNotCorrects) return null;
    return yetNotCorrects[0];
  });

  const finishedSpan = ref(null);
  const unfinishedSpan = ref(null);
  const roma = ref(null);
  let romaSubstr = 0;

  const finishedRoma = computed(() => {
    const finished = typing.value.sentence.displayRoma.slice(0, typing.value.statistics.correct);

    const finishedWidth = finishedSpan.value?.offsetWidth || 0;
    const unfinishedWidth = unfinishedSpan.value?.offsetWidth || 0;
    const romaWidth = roma.value?.clientWidth || 0;

    const isOverHalfWidth = finishedWidth * 2 > romaWidth;
    const isOverFlow = romaSubstr || finishedWidth + unfinishedWidth > romaWidth;

    // 文字の過半数を超えたら未入力の部分を表示しない
    if (isOverFlow && isOverHalfWidth) {
      romaSubstr += 1;
      return finished.substring(romaSubstr);
    }

    return finished;
  });

  watch(
    () => finishedRoma.value.length,
    (length) => {
      if (length === 0) romaSubstr = 0;
    }
  );

  const unfinishedRoma = computed(() =>
    typing.value.sentence.displayRoma.slice(typing.value.statistics.correct)
  );

  const getStats = (i) => {
    const stats = {
      average: typing.value.result.accuracies[i],
      wpm: typing.value.result.avarages[i],
      missStreak: typing.value.result.missStreaks[i],
    };
    stat.value = stats;
  };

  const getSentence = () => {
    router.visit(route('sentences', { num: 3 }), {
      only: ['sentences', 'filled'],
      onSuccess: () => {
        typing.value.isPlayable = true;
        if (props.sentences && props.sentences.length) {
          typing.value.prepare(props.sentences);
        }
      },
    });

    typing.value.prepare(props.sentences);
  };

  const isRetrying = ref(false);

  const retryGame = () => {
    isRetrying.value = true;
    getSentence(0);
  };

  watch(
    () => typing.value.isFinished,
    (isFinished) => {
      if (isFinished) {
        const { result } = typing.value;
        const stats = typing.value.statistics;
        stats.totalWPM = typing.value.statistics.getTotalWPM();
        const form = useForm({
          result,
          stats,
        });
        form.post(route(`${guestPrefix.value}play.store`));
      }
    }
  );

  watch(
    () => props.volume,
    (volume) => {
      typing.value.changeVolume(volume);
    }
  );

  onMounted(() => {
    if (!props.sentences || props.filled) return;
    if (props.sentences && props.sentences.length) {
      typing.value.prepare(props.sentences);
    }
    document.addEventListener('keydown', {
      handleEvent: typing.value.start,
      typing: typing.value,
    });

    document.addEventListener('keydown', (event) => {
      if (event.key === 'Escape' && typing.value.isStarted) {
        getSentence(0);
      }
    });
  });

  onUnmounted(() => {
    typing.value.reset();
  });
</script>

<template>
  <div>
    <div v-show="!typing.isStarted && !typing.isFinished">
      <div>{{ typing.dialog ? typing.dialog : typing.DEFAULT_GAME_DIALOG }}</div>
      <div class="lg:hidden">ここをタップするとキーボードが出ます</div>
    </div>

    <div v-if="typing.isStarted && !typing.isFinished">
      <div class="text-lg w-full overflow-hidden">
        {{ typing.sentence.sentences[typing.sentence.current] }}
      </div>
      <div class="text-lg">
        {{ typing.sentence.kanasDisplay[typing.sentence.current] }}
      </div>
      <div id="c" ref="roma" class="text-lg w-full overflow-hidden">
        <span ref="finishedSpan">{{ finishedRoma }}</span>
        <span ref="unfinishedSpan" class="opacity-40">{{ unfinishedRoma }}</span>
      </div>
      <div>{{ typing.countDown }}</div>

      <div class="hidden sm:block md:mt-32">
        <div class="flex flex-wrap">
          <div class="mt-3 mb-3 flex items-center">
            <div>{{ typing.statistics.time }}</div>
          </div>
          <div class="w-full flex flex-wrap gap-8">
            <div class="flex items-center flex-wrap">
              <SpeedMeter class="mb-3" :angle="typing.statistics.getCurrentWPM()" />
              <TypingAccuracy
                class="ml-4"
                :accuracy="
                  typing.statistics.calcAccuracy(
                    typing.statistics.correct,
                    typing.statistics.mistake
                  )
                "
              />
            </div>
            <div class="flex items-center flex-wrap">
              <SpeedMeter class="mb-3" :angle="typing.statistics.getTotalWPM()" />
              <TypingAccuracy
                class="ml-4"
                :accuracy="
                  typing.statistics.calcAccuracy(
                    typing.statistics.totalCorrect,
                    typing.statistics.totalMistake
                  )
                "
              />
            </div>
          </div>
        </div>

        <KeyboardLayout :correct="correctKey" />
      </div>
      <div class="sm:hidden md:mt-32">
        <div class="mt-3 mb-3">
          <div>{{ typing.statistics.time }}</div>
          <div>現在WPM：{{ typing.statistics.currentWPM }}</div>
          <div>
            現在成功率：{{
              typing.statistics.calcAccuracy(typing.statistics.correct, typing.statistics.mistake)
            }}%
          </div>
          <div>合計WPM：{{ typing.statistics.totalWPM }}</div>
          <div>
            合計成功率：{{
              typing.statistics.calcAccuracy(
                typing.statistics.totalCorrect,
                typing.statistics.totalMistake
              )
            }}%
          </div>
        </div>
      </div>
    </div>

    <div v-if="!typing.isStarted && typing.isFinished">
      <div class="mb-4">
        <div class="flex">
          <div>出題数：</div>
          <div>{{ typing.sentence.current }}</div>
        </div>

        <div class="flex">
          <div>正答率：</div>
          <div>
            {{
              typing.statistics.calcAccuracy(
                typing.statistics.totalCorrect,
                typing.statistics.totalMistake
              )
            }}%
          </div>
        </div>

        <div class="flex">
          <div>合計時間：</div>
          <div>{{ typing.statistics.time }}</div>
        </div>

        <div class="flex">
          <div>WPM：</div>
          <div>{{ typing.statistics.getTotalWPM() }}</div>
        </div>

        <div class="flex">
          <div>総タイプ数：</div>
          <div>{{ typing.statistics.totalCorrect + typing.statistics.totalMistake }}</div>
        </div>

        <div class="flex">
          <div>最大連続ミス数：</div>
          <div>{{ typing.statistics.maxMissStreak }}</div>
        </div>
      </div>

      <div v-show="!isRetrying">
        <div class="scroll-bar max-h-60 overflow-auto">
          <div
            v-for="i of typing.sentence.ids.length"
            :key="i"
            class="flex cursor-pointer my-2 border-b-2 border-black"
            @click="getStats(i - 1)"
          >
            <div>{{ i }}</div>
            <div class="ml-2">
              {{ typing.sentence.sentences[i - 1] }}
            </div>
            <!-- <div @click="getStats(i - 1)">{{ typing.sentence.kanasDisplay[i - 1] }}</div> -->
          </div>
        </div>

        <div v-if="stat" class="text-center mt-3">
          <div>WPM：{{ stat.wpm }}</div>
          <div>正答率：{{ stat.average }}%</div>
          <div>連続ミス数：{{ stat.missStreak }}</div>
        </div>

        <div class="flex justify-center mt-3">
          <SecondaryButton @click="retryGame"> もう一度 </SecondaryButton>
        </div>
      </div>
    </div>
  </div>
</template>
