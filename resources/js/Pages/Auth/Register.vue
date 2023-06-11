<!-- eslint-disable vue/multi-word-component-names -->
<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticationCard from '@/Components/AuthenticationCard.vue';
import AuthenticationCardLogo from '@/Components/AuthenticationCardLogo.vue';
import Checkbox from '@/Components/Checkbox.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

const form = useForm({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
  terms: false,
});

const submit = () => {
  form.post(route('register'), {
    onFinish: () => form.reset('password', 'password_confirmation'),
  });
};
</script>

<template>
  <Head title="登録" />

  <AuthenticationCard>
    <template #logo>
      <AuthenticationCardLogo />
    </template>

    <form @submit.prevent="submit">
      <div>
        <InputLabel for="name" value="ユーザー名" />
        <TextInput
          id="name" v-model="form.name" type="text" class="mt-1 block w-full" required autofocus
          autocomplete="name" maxlength="255"
        />
        <InputError class="mt-2" :message="form.errors.name" />
      </div>

      <div class="mt-4">
        <InputLabel for="email" value="メールアドレス" />
        <TextInput
          id="email" v-model="form.email" type="email" class="mt-1 block w-full" required
          maxlength="255"
        />
        <InputError class="mt-2" :message="form.errors.email" />
      </div>

      <div class="mt-4">
        <InputLabel for="password" value="パスワード" />
        <TextInput
          id="password" v-model="form.password" type="password" class="mt-1 block w-full" required
          autocomplete="new-password"
        />
        <InputError class="mt-2" :message="form.errors.password" />
      </div>

      <div class="mt-4 mb-4">
        <InputLabel for="password_confirmation" value="パスワード（確認）" />
        <TextInput
          id="password_confirmation" v-model="form.password_confirmation" type="password"
          class="mt-1 block w-full" required autocomplete="new-password"
        />
        <InputError class="mt-2" :message="form.errors.password_confirmation" />
      </div>

      <div v-if="$page.props.jetstream.hasTermsAndPrivacyPolicyFeature" class="mt-4">
        <InputLabel for="terms">
          <div class="flex items-center">
            <Checkbox id="terms" v-model:checked="form.terms" name="terms" required />

            <div class="ml-2">
              I agree to the <a
                target="_blank" :href="route('terms.show')"
                class="underline text-sm text-gray-600 hover:text-gray-900"
              >Terms of Service</a> and <a
                target="_blank" :href="route('policy.show')"
                class="underline text-sm text-gray-600 hover:text-gray-900"
              >Privacy Policy</a>
            </div>
          </div>
          <InputError class="mt-2" :message="form.errors.terms" />
        </InputLabel>
      </div>

      <PrimaryButton
        class="mb-4 mx-auto"
        :class="{ 'opacity-25': form.processing }"
        :disabled="form.processing"
      >
        登録
      </PrimaryButton>

      <Link
        :href="route('login')"
        class="block text-center underline text-sm text-gray-600 hover:text-gray-900"
      >
        ログインはこちら
      </Link>
    </form>
  </AuthenticationCard>
</template>
