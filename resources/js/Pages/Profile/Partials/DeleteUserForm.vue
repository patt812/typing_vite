<script setup>
  import { useForm } from '@inertiajs/vue3';
  import { ref } from 'vue';
  import AppModal from '@/Components/AppModal.vue';
  import DangerButton from '@/Components/DangerButton.vue';
  import SecondaryButton from '@/Components/SecondaryButton.vue';

  const confirmingUserDeletion = ref(false);
  const passwordInput = ref(null);

  const form = useForm({
    password: '',
  });

  const closeModal = () => {
    confirmingUserDeletion.value = false;

    form.reset();
  };

  const confirmUserDeletion = () => {
    confirmingUserDeletion.value = true;

    setTimeout(() => passwordInput.value.focus(), 250);
  };

  const deleteUser = () => {
    form.delete(route('profile.destroy'), {
      preserveScroll: true,
      onSuccess: () => closeModal(),
      onError: () => passwordInput.value.focus(),
      onFinish: () => form.reset(),
    });
  };
</script>

<template>
  <div class="flex justify-center">
    <DangerButton class="shadow-xl" @click="confirmUserDeletion"> アカウント削除 </DangerButton>
  </div>

  <!-- Delete Account Confirmation Modal -->
  <AppModal v-if="confirmingUserDeletion" :show="confirmingUserDeletion" @close="closeModal">
    <div class="mb-4 text-lg font-bold">アカウントの削除</div>

    <div>アカウントを削除しますか？</div>
    <div>登録した文章や記録など全てのデータが消されます。</div>
    <div>この処理は元に戻せません。</div>

    <div class="mt-7 flex justify-center">
      <SecondaryButton @click="closeModal"> 戻る </SecondaryButton>

      <DangerButton
        class="ml-10"
        :class="{ 'opacity-25': form.processing }"
        :disabled="form.processing"
        @click="deleteUser"
      >
        削除する
      </DangerButton>
    </div>
  </AppModal>
</template>
