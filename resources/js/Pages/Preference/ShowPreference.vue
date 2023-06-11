<script setup>
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import Checkbox from '@/Components/Checkbox.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

import SentenceList from '@/Pages/Sentence/Partials/SentenceList.vue';
import ContentFrame from '@/Components/ContentFrame.vue';
import RadioInput from '@/Components/RadioInput.vue';

const { user } = usePage().props;

const props = defineProps({
  sentences: {
    type: Array,
    required: true,
  },
  status: {
    type: String,
    default: null,
  },
});

const form = useForm(user.settings.setting_preferences);

const error = ref('');

const isNoSentenceSelected = computed(() => props.sentences.every((s) => !s.is_selected));

const store = () => {
  form.put(route('preference.store'));
};

const storeSentence = () => {
  error.value = '';
  usePage().props.flash.message = '';

  if (isNoSentenceSelected.value) {
    error.value = '文章は１つ以上選択してください';
    return;
  }
  const sentenceForm = useForm({
    sentence: props.sentences,
  });
  sentenceForm.put(route('preference.sentence.store'), {
    only: ['sentences', 'flash'],
  });
};

</script>

<template>
  <AppLayout>
    <Head title="出題設定" />

    <ContentFrame>
      <template #title>
        出題設定
      </template>

      <template #content>
        <SentenceList
          :sentences="sentences" :show-if-selected="true"
          from="preference" @store="storeSentence"
        />
        <InputError :message="error" />

        <form @submit.prevent="store">
          <div class="flex mt-2 items-center">
            <Checkbox id="is_random" v-model:checked="form.is_random" dashed="true" />
            <InputLabel for="is_random" class="ml-2" value="ランダムに出題する" />
          </div>

          <div class="flex items-center mt-2">
            <InputLabel value="出題数" for="sentences" />
            <TextInput
              id="sentences" v-model="form.sentences"
              type="number" class="ml-2 w-full max-w-[12.8rem]" min="1"
              max="1000" required
            />
          </div>

          <InputLabel for="prior_no_stats" class="mt-3" value="統計がない文章の優先度" />
          <div class="flex mt-1 items-center">
            <RadioInput
              id="prior-every" v-model:checked="form.prior_no_stats" type="radio" name="prior-type"
              value="0"
            />
            <InputLabel for="prior-every" class="pt-0.5 ml-1" value="未選択でも優先して出題" />

            <RadioInput
              id="prior-selected" v-model:checked="form.prior_no_stats"
              type="radio" name="prior-type"
              class="ml-3" value="1"
            />
            <InputLabel for="prior-selected" class="pt-0.5 ml-1" value="選択された中で優先して出題" />

            <RadioInput
              id="prior-none" v-model:checked="form.prior_no_stats" type="radio" name="prior-type"
              class="ml-3" value="2"
            />
            <InputLabel for="prior-none" class="pt-0.5 ml-1" value="設定しない" />
          </div>

          <div class="flex mt-2 items-center">
            <Checkbox id="limit_wpm" v-model:checked="form.limit_wpm" dashed="true" />
            <InputLabel for="limit_wpm" class="ml-2" value="WPMで選ぶ" />
          </div>
          <div v-show="form.limit_wpm" class="flex items-center mt-2">
            <TextInput
              v-model="form.min_wpm"
              type="number" class="w-full max-w-[12.8rem]" min="0"
            />
            <span class="mx-2">〜</span>
            <TextInput
              v-model="form.max_wpm"
              type="number" class="w-full max-w-[12.8rem]" min="0"
            />
          </div>

          <div class="flex mt-3 items-center">
            <Checkbox id="limit_accuracy" v-model:checked="form.limit_accuracy" dashed="true" />
            <InputLabel for="limit_accuracy" class="ml-2" value="正答率で選ぶ" />
          </div>
          <div v-show="form.limit_accuracy" class="flex items-center mt-2">
            <TextInput
              v-model="form.min_accuracy" type="number" class="w-full max-w-[12.8rem]" min="0"
              max="100"
            />
            <span class="mx-2">〜</span>
            <TextInput
              v-model="form.max_accuracy" type="number" class="w-full max-w-[12.8rem]" min="0"
              max="100"
            />
          </div>

          <PrimaryButton class="mt-4 mx-auto" :disabled="form.processing">
            保存する
          </PrimaryButton>
        </form>
      </template>
    </ContentFrame>
  </AppLayout>
</template>
