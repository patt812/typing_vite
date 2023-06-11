<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticationCard from '@/Components/AuthenticationCard.vue';
import AuthenticationCardLogo from '@/Components/AuthenticationCardLogo.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

defineProps({
  status: {
    type: String,
    default: null,
  },
});

const form = useForm({
  email: '',
});

const submit = () => {
  form.post(route('password.email'));
};
</script>

<template>
  <Head title="パスワード再発行" />

  <AuthenticationCard>
    <template #logo>
      <AuthenticationCardLogo />
    </template>

    <h3 class="text-lg font-bold text-gray-900 pb-4">
      パスワードのリセット
    </h3>

    <div v-if="status" class="mb-4 font-medium text-sm text-green-600">
      {{ status }}
    </div>

    <form @submit.prevent="submit">
      <div>
        <InputLabel for="email" value="メールアドレス" />
        <TextInput
          id="email" v-model="form.email" type="email" class="mt-1 block w-full" required autofocus
          maxlength="255"
        />
        <InputError class="mt-2" :message="form.errors.email" />
      </div>

      <div class="flex items-center justify-center mt-3 mb-4">
        <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
          送信
        </PrimaryButton>
      </div>

      <Link
        :href="route('login')"
        class="block text-center underline text-sm text-gray-600 hover:text-gray-900"
      >
        ログインはこちら
      </Link>
    </form>
  </AuthenticationCard>
</template>
