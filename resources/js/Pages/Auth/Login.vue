<!-- eslint-disable vue/multi-word-component-names -->
<script setup>
  import { Head, Link, useForm } from '@inertiajs/vue3';
  import AuthenticationCard from '@/Components/AuthenticationCard.vue';
  import AuthenticationCardLogo from '@/Components/AuthenticationCardLogo.vue';
  import Checkbox from '@/Components/Checkbox.vue';
  import InputError from '@/Components/InputError.vue';
  import InputLabel from '@/Components/InputLabel.vue';
  import PrimaryButton from '@/Components/PrimaryButton.vue';
  import SecondaryButton from '@/Components/SecondaryButton.vue';
  import TextInput from '@/Components/TextInput.vue';

  defineProps({
    canResetPassword: {
      type: Boolean,
      default: false,
    },
    status: {
      type: String,
      default: null,
    },
  });

  const form = useForm({
    email: '',
    password: '',
    remember: false,
  });

  const submit = () => {
    form
      .transform((data) => ({
        ...data,
        remember: form.remember ? 'on' : '',
      }))
      .post(route('login'), {
        onFinish: () => form.reset('password'),
      });
  };

  const guestForm = useForm({});
  const submitAsGuest = () => {
    guestForm.post(route('guest.login'));
  };
</script>

<template>
  <Head title="ログイン" />

  <AuthenticationCard>
    <template #logo>
      <AuthenticationCardLogo />
    </template>

    <div v-if="status" class="mb-4 font-medium text-sm text-green-600">
      {{ status }}
    </div>

    <form @submit.prevent="submit">
      <div>
        <InputLabel for="email" value="メールアドレス" />
        <TextInput
          id="email"
          v-model="form.email"
          type="email"
          class="mt-1 block w-full"
          required
          autofocus
          maxlength="255"
        />
        <InputError class="mt-2" :message="form.errors.email" />
      </div>

      <div class="mt-4">
        <InputLabel for="password" value="パスワード" />
        <TextInput
          id="password"
          v-model="form.password"
          type="password"
          class="mt-1 block w-full"
          required
          autocomplete="current-password"
        />
        <InputError class="mt-2" :message="form.errors.password" />
      </div>

      <div class="mt-4">
        <label class="flex items-center">
          <Checkbox v-model:checked="form.remember" name="remember" dashed="true" />
          <span class="ml-2 text-sm text-gray-600">ログイン状態を保存する</span>
        </label>
      </div>

      <PrimaryButton
        class="mt-3 mb-3 mx-auto"
        :class="{ 'opacity-25': form.processing || guestForm.processing }"
        :disabled="form.processing || guestForm.processing"
      >
        ログイン
      </PrimaryButton>

      <Link
        v-if="canResetPassword"
        :href="route('password.request')"
        class="mt-4 mb-3 block text-center underline text-sm text-gray-600 hover:text-gray-900"
      >
        パスワードを忘れた方はこちら
      </Link>

      <Link
        :href="route('register')"
        class="block underline text-center text-sm text-gray-600 hover:text-gray-900"
      >
        登録はこちら
      </Link>

      <hr class="hr-class border border-black mt-4 px-10" />

      <SecondaryButton
        class="mt-4 mx-auto text-center"
        :class="{ 'opacity-25': form.processing || guestForm.processing }"
        :disabled="form.processing || guestForm.processing"
        @click.prevent="submitAsGuest"
      >
        ゲストでお試し
      </SecondaryButton>
    </form>
  </AuthenticationCard>
</template>

<style scoped>
  .hr-class {
    width: calc(100% + 40px);
    margin-left: -20px;
    margin-right: -20px;
  }
</style>
