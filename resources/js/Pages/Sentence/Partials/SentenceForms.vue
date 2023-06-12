<script setup>
  import InputError from '@/Components/InputError.vue';
  import InputLabel from '@/Components/InputLabel.vue';
  import PrimaryButton from '@/Components/PrimaryButton.vue';
  import TextInput from '@/Components/TextInput.vue';

  defineProps({
    sentence: {
      type: String,
      required: true,
    },
  });
</script>

<template>
  <div>
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

      <PrimaryButton>登録</PrimaryButton>
    </form>
  </div>
</template>
