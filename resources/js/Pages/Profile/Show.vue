<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import DeleteUserForm from '@/Pages/Profile/Partials/DeleteUserForm.vue';
import LogoutOtherBrowserSessionsForm from '@/Pages/Profile/Partials/LogoutOtherBrowserSessionsForm.vue';
import SectionBorder from '@/Components/SectionBorder.vue';
import TwoFactorAuthenticationForm from '@/Pages/Profile/Partials/TwoFactorAuthenticationForm.vue';
import UpdatePasswordForm from '@/Pages/Profile/Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from '@/Pages/Profile/Partials/UpdateProfileInformationForm.vue';
import InputLabel from '@/Components/InputLabel.vue';
import Checkbox from '@/Components/Checkbox.vue';
import TextInput from '@/Components/TextInput.vue';
import { useForm, usePage } from '@inertiajs/vue3';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import FlashMessage from '@/Components/FlashMessage.vue';
import ContentFrame from '@/Components/ContentFrame.vue';

defineProps({
    confirmsTwoFactorAuthentication: Boolean,
    sessions: Array,
});

const settings = usePage().props.user.settings;

const form = useForm(settings.setting_plays);

const submit = () => {
    form.put(route('settings.sound', form.id));
}

</script>

<template>
    <AppLayout title="アカウント設定">

        <FlashMessage />

        <div>
            <ContentFrame>
                <template #title>サウンド設定</template>

                <template #content>
                    <div>
                        <div class="flex items-center ml-4 mt-1 mb-2">
                            <Checkbox id="use_type_sound" v-model:checked="form.use_type_sound" dashed="true" />
                            <InputLabel for="use_type_sound" class="ml-2" value="タイピング音を鳴らす" />
                        </div>
                        <div class="flex items-center ml-4 mb-4">
                            <Checkbox id="use_beep_sound" v-model:checked="form.use_beep_sound" dashed="true" />
                            <InputLabel for="use_beep_sound" class="ml-2" value="ミス音を鳴らす" />
                        </div>
                        <div class="mb-2">音量</div>
                        <TextInput type="range" v-model="form.volume" min="0" max="1" step="0.01" />
                    </div>
                    <PrimaryButton class="px-10" @click="submit">保存</PrimaryButton>
                </template>
            </ContentFrame>

            <ContentFrame>
                <template #title>ユーザー情報</template>

                <template #content>
                    <UpdateProfileInformationForm :user="$page.props.user" />
                </template>
            </ContentFrame>

            <ContentFrame>
                <template #title>パスワード変更</template>

                <template #content>
                    <UpdatePasswordForm class="mt-10 sm:mt-0" />
                </template>
            </ContentFrame>

            <div class="mt-10">
                <DeleteUserForm />
            </div>
        </div>
    </AppLayout>
</template>
