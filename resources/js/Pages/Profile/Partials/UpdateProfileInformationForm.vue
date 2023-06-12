<script setup>
  import { useForm, usePage } from '@inertiajs/vue3';
  import { ref } from 'vue';
  import InputError from '@/Components/InputError.vue';
  import InputLabel from '@/Components/InputLabel.vue';
  import PrimaryButton from '@/Components/PrimaryButton.vue';
  import TextInput from '@/Components/TextInput.vue';

  const props = defineProps({
    user: {
      type: Object,
      required: true,
    },
  });

  const form = useForm({
    _method: 'PUT',
    name: props.user.name,
    email: props.user.email,
    photo: null,
  });

  // const verificationLinkSent = ref(null);
  // const photoPreview = ref(null);
  const photoInput = ref(null);

  // const deletePhoto = () => {
  //   router.delete(route('current-user-photo.destroy'), {
  //     preserveScroll: true,
  //     onSuccess: () => {
  //       photoPreview.value = null;
  //       clearPhotoFileInput();
  //     },
  //   });
  // };

  // const sendEmailVerification = () => {
  //   verificationLinkSent.value = true;
  // };

  // const selectNewPhoto = () => {
  //   photoInput.value.click();
  // };

  // const updatePhotoPreview = () => {
  //   const photo = photoInput.value.files[0];

  //   if (!photo) return;

  //   const reader = new FileReader();

  //   reader.onload = (e) => {
  //     photoPreview.value = e.target.result;
  //   };

  //   reader.readAsDataURL(photo);
  // };

  const clearPhotoFileInput = () => {
    if (photoInput.value?.value) {
      photoInput.value.value = null;
    }
  };

  const updateProfileInformation = () => {
    if (photoInput.value) {
      const [file] = photoInput.value.files;
      form.photo = file;
    }

    form.post(route('user-profile-information.update'), {
      errorBag: 'updateProfileInformation',
      preserveScroll: true,
      onSuccess: () => {
        clearPhotoFileInput();
        usePage().props.flash.message = 'プロフィールを更新しました。';
      },
    });
  };
</script>

<template>
  <div>
    <form @submit.prevent="updateProfileInformation">
      <!-- ユーザー名 -->
      <div class="mt-2 mb-6">
        <InputLabel for="name" value="ユーザー名" />
        <TextInput
          id="name"
          v-model="form.name"
          type="text"
          class="mt-1 block w-full max-w-xl"
          autocomplete="name"
          required
          maxlength="255"
        />
        <InputError :message="form.errors.name" class="mt-2" />
      </div>

      <!-- メールアドレス -->
      <div class="mb-7">
        <InputLabel for="email" value="メールアドレス" required maxlength="255" />
        <TextInput
          id="email"
          v-model="form.email"
          type="email"
          class="mt-1 block w-full max-w-xl"
        />
        <InputError :message="form.errors.email" class="mt-2" />
      </div>

      <PrimaryButton
        type="submit"
        class="mx-auto px-10"
        :class="{ 'opacity-25': form.processing }"
        :disabled="form.processing"
      >
        保存
      </PrimaryButton>
    </form>
  </div>
</template>
