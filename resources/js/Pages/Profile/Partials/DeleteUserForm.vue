<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import DangerButton from '@/Components/DangerButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import Modal from '@/Components/Modal.vue';

const confirmingUserDeletion = ref(false);
const passwordInput = ref(null);

const form = useForm({
    password: '',
});

const confirmUserDeletion = () => {
    confirmingUserDeletion.value = true;

    setTimeout(() => passwordInput.value.focus(), 250);
};

const deleteUser = () => {
    form.delete(route('current-user.destroy'), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
        onError: () => passwordInput.value.focus(),
        onFinish: () => form.reset(),
    });
};

const closeModal = () => {
    confirmingUserDeletion.value = false;

    form.reset();
};
</script>

<template>
    <div class="flex justify-center">
        <DangerButton class="shadow-xl" @click="confirmUserDeletion">
            アカウント削除
        </DangerButton>
    </div>

    <!-- Delete Account Confirmation Modal -->
    <Modal :show="confirmingUserDeletion" @close="closeModal">
        <div>アカウントを削除しますか？</div>
        <div>登録した文章や記録など全てのデータが消されます。この処理は元に戻せません。</div>


        <div>
            <SecondaryButton @click="closeModal">
                戻る
            </SecondaryButton>

            <DangerButton class="ml-3" :class="{ 'opacity-25': form.processing }" :disabled="form.processing"
                @click="deleteUser">
                削除する
            </DangerButton>
        </div>
    </Modal>
</template>
