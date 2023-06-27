<script setup>
  import { computed, onMounted, ref } from 'vue';
  import Checkbox from '@/Components/Checkbox.vue';
  import InputLabel from '@/Components/InputLabel.vue';
  import PrimaryButton from '@/Components/PrimaryButton.vue';
  import RadioInput from '@/Components/RadioInput.vue';
  import TextInput from '@/Components/TextInput.vue';

  const props = defineProps({
    sentences: {
      type: Array,
      required: true,
    },
    from: {
      type: String,
      required: true,
    },
    focusInput: {
      type: Boolean,
      default: false,
    },
    showIfSelected: {
      type: Boolean,
      default: false,
    },
  });

  const isPreference = ref(['preference'].includes(props.from));

  const searchBox = ref(null);

  const searchWord = ref('');

  const radioDiv = ref(0);

  const selectedIndex = ref(null);

  const emits = defineEmits('fill');

  const select = (sentenceRow, index) => {
    const sentence = sentenceRow;
    if (index === selectedIndex.value) {
      selectedIndex.value = null;
      emits('fill', null);
      return;
    }
    sentence.index = index;
    selectedIndex.value = index;

    emits('fill', sentence);
  };

  const filtered = computed(() => {
    let sentence = Object.values(props.sentences);

    if (radioDiv.value === 1) {
      sentence = sentence.filter((s) => s.is_selected);
    } else if (radioDiv.value === 2) {
      sentence = sentence.filter((s) => !s.is_selected);
    }
    if (sentence.length && searchWord.value) {
      sentence = sentence.filter(
        (s) => s.kana.includes(searchWord.value) || s.sentence.includes(searchWord.value)
      );
    }
    return sentence;
  });

  onMounted(() => {
    if (props.focusInput) {
      searchBox.value.focus();
    }
  });
</script>

<template>
  <div>
    <div>
      <div v-if="isPreference" class="flex mt-1 items-center align-middle">
        <RadioInput id="list-type-all" v-model:checked="radioDiv" name="list-type" value="0" />
        <InputLabel class="ml-1 pt-0.5" for="list-type-all" value="すべて表示" />

        <RadioInput
          id="list-type-on"
          v-model:checked="radioDiv"
          type="radio"
          class="ml-3"
          name="list-type"
          :value="1"
        />
        <InputLabel class="ml-1 pt-0.5" for="list-type-on" value="出題する" />

        <RadioInput
          id="list-type-off"
          v-model:checked="radioDiv"
          type="radio"
          class="ml-3"
          name="list-type"
          :value="2"
        />
        <InputLabel class="ml-1 pt-0.5" for="list-type-off" value="出題しない" />
      </div>

      <div class="flex w-full mt-3">
        <InputLabel for="sentence-search" value="検索" />
        <TextInput
          id="sentence-search"
          ref="searchBox"
          v-model="searchWord"
          class="ml-2 w-full max-w-md"
        />
      </div>
    </div>

    <div class="w-full mt-2">
      <div class="flex items-center justify-between border-black border-b-2 font-bold">
        <div class="w-1/12 px-2 py-1" />
        <div v-if="showIfSelected" class="w-1/12 px-2 py-1">出題</div>
        <div class="w-5/12 px-2 py-1">文章</div>
        <div class="w-5/12 px-2 py-1">かな</div>
      </div>

      <div class="max-h-60 overflow-auto scroll-bar mb-3">
        <template v-for="(sentence, index) in filtered" :key="sentence">
          <div
            class="flex items-center justify-between cursor-pointer py-1 border-black border-b-2"
            :class="{ 'bg-black text-white': index == selectedIndex }"
            @click="select(sentence, index)"
          >
            <div class="w-1/12 px-2 text-center">
              {{ index + 1 }}
            </div>
            <Checkbox
              v-if="showIfSelected"
              v-model:checked="sentence.is_selected"
              :class="{ 'checked:border-white border-white': index == selectedIndex }"
              @click.stop=""
            />
            <div :class="{ truncate: selectedIndex != index }" class="w-5/12 px-2">
              {{ sentence.sentence }}
            </div>
            <div :class="{ truncate: selectedIndex != index }" class="w-5/12 px-2">
              {{ sentence.kana }}
            </div>
          </div>
        </template>
        <div v-if="!filtered.length" class="text-center py-1 border-black border-b-2">
          見つかりませんでした...
        </div>
      </div>
    </div>

    <PrimaryButton v-if="isPreference" class="mt-4 mx-auto" @click="$emit('store')">
      反映する
    </PrimaryButton>
  </div>
</template>
