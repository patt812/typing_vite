<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import ActionMessage from '@/Components/ActionMessage.vue';
import FormSection from '@/Components/FormSection.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

const passwordInput = ref(null);
const currentPasswordInput = ref(null);

const form = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
});

const updatePassword = () => {
    form.put(route('user-password.update'), {
        errorBag: 'updatePassword',
        preserveScroll: true,
        onSuccess: () => form.reset(),
        onError: () => {
            if (form.errors.password) {
                form.reset('password', 'password_confirmation');
                passwordInput.value.focus();
            }

            if (form.errors.current_password) {
                form.reset('current_password');
                currentPasswordInput.value.focus();
            }
        },
    });
};
</script>

<template>
    <div>
        <form @submit.prevent="updatePassword">

            <div class="mt-2 mb-6">
                <InputLabel for="current_password" value="現在のパスワード" />
                <TextInput id="current_password" ref="currentPasswordInput" v-model="form.current_password" type="password"
                    class="mt-1 block w-full max-w-xl" autocomplete="current-password" required maxlength="255" />
                <InputError :message="form.errors.current_password" class="mt-2" />
            </div>

            <div class="mb-6">
                <InputLabel for="password" value="新しいパスワード" />
                <TextInput id="password" ref="passwordInput" v-model="form.password" type="password"
                    class="mt-1 block w-full max-w-xl" autocomplete="new-password" required />
                <InputError :message="form.errors.password" class="mt-2" />
            </div>

            <div class="mb-7">
                <InputLabel for="password_confirmation" value="新しいパスワード（確認）" />
                <TextInput id="password_confirmation" v-model="form.password_confirmation" type="password"
                    class="mt-1 block w-full max-w-xl" autocomplete="new-password" required />
                <InputError :message="form.errors.password_confirmation" class="mt-2" />
            </div>

            <PrimaryButton class="mx-auto px-10" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                保存
            </PrimaryButton>
        </form>
    </div>
</template>
