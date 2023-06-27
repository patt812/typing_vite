<script setup>
  import { useForm } from '@inertiajs/vue3';
  import { ref } from 'vue';
  import AppModal from '@/Components/AppModal.vue';
  import Checkbox from '@/Components/Checkbox.vue';
  import InputError from '@/Components/InputError.vue';
  import InputLabel from '@/Components/InputLabel.vue';
  import PrimaryButton from '@/Components/PrimaryButton.vue';
  import TextInput from '@/Components/TextInput.vue';

  const showRegisterModal = ref(false);

  const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    take_over: true,
  });

  const submit = () => {
    form.post(route('guest.register'), {
      onFinish: () => form.reset('password', 'password_confirmation'),
    });
  };
</script>

<template>
  <div class="absolute z-50 !opacity-100 inset-0 flex items-center justify-center">
    <div class="bg-main text-center px-4 py-3 border-black border-2 rounded-3xl">
      <div>
        この機能は、会員専用です。<br />
        文章や統計をそのまま引き継いで登録できます。
      </div>
      <PrimaryButton class="mt-3 mx-auto" @click="showRegisterModal = true">会員登録</PrimaryButton>
    </div>
  </div>

  <AppModal v-if="showRegisterModal" :show="showRegisterModal" @close="showRegisterModal = false">
    <div v-if="status" class="mb-4 font-medium text-sm text-green-600">
      {{ status }}
    </div>

    <form @submit.prevent="submit">
      <div>
        <InputLabel for="name" value="ユーザー名" />
        <TextInput
          id="name"
          v-model="form.name"
          type="text"
          class="mt-1 block w-full"
          required
          autofocus
          autocomplete="name"
          maxlength="255"
        />
        <InputError class="mt-2" :message="form.errors.name" />
      </div>

      <div class="mt-4">
        <InputLabel for="email" value="メールアドレス" />
        <TextInput
          id="email"
          v-model="form.email"
          type="email"
          class="mt-1 block w-full"
          required
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
          autocomplete="new-password"
        />
        <InputError class="mt-2" :message="form.errors.password" />
      </div>

      <div class="mt-4 mb-4">
        <InputLabel for="password_confirmation" value="パスワード（確認）" />
        <TextInput
          id="password_confirmation"
          v-model="form.password_confirmation"
          type="password"
          class="mt-1 block w-full"
          required
          autocomplete="new-password"
        />
        <InputError class="mt-2" :message="form.errors.password_confirmation" />
      </div>

      <label class="flex items-center">
        <Checkbox v-model:checked="form.take_over" name="take_over" dashed="true" />
        <span class="ml-2 text-sm text-gray-600">文章や統計を引き継ぐ</span>
      </label>

      <PrimaryButton
        class="mt-4 mx-auto"
        :class="{ 'opacity-25': form.processing }"
        :disabled="form.processing"
      >
        登録
      </PrimaryButton>
    </form>
  </AppModal>
</template>
