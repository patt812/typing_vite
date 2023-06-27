<script setup>
  import { useForm } from '@inertiajs/vue3';
  import { ref } from 'vue';
  import AppModal from '@/Components/AppModal.vue';
  import Checkbox from '@/Components/Checkbox.vue';
  import DangerButton from '@/Components/DangerButton.vue';
  import InputError from '@/Components/InputError.vue';
  import InputLabel from '@/Components/InputLabel.vue';
  import PrimaryButton from '@/Components/PrimaryButton.vue';
  import TextInput from '@/Components/TextInput.vue';

  const showRegisterModal = ref(true);

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
  <AppModal v-if="showRegisterModal" :show="showRegisterModal" @close="showRegisterModal = false">
    <div>
      <div class="font-bold">
        ログアウトすると、<span class="text-red-600">文章や統計が完全に削除</span>されます。<br />
        この作業は取り消せません。 <br />
        ログアウトしますか？
      </div>

      <div class="flex mt-3 mx-auto gap-12 items-center justify-center">
        <PrimaryButton>会員登録</PrimaryButton>
        <DangerButton @click="showRegisterModal = false">ログアウト</DangerButton>
      </div>
    </div>

    <form v-if="0" @submit.prevent="submit">
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
