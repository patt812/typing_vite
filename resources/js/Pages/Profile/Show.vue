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
    <AppLayout title="Profile">

        <FlashMessage />

        <div>
            <ContentFrame>
                <div>
                    <div>サウンド設定</div>
                    <div class="flex">
                        <Checkbox id="use_type_sound" v-model:checked="form.use_type_sound" />
                        <InputLabel for="use_type_sound" value="効果音" />
                    </div>
                    <div class="flex">
                        <Checkbox id="use_beep_sound" v-model:checked="form.use_beep_sound" />
                        <InputLabel for="use_beep_sound" value="ミス音" />
                    </div>
                    <div>音量</div>
                    <TextInput type="range" v-model="form.volume" min="0" max="1" step="0.01" />
                </div>
                <PrimaryButton @click="submit">保存</PrimaryButton>
            </ContentFrame>

            <ContentFrame>
                <UpdateProfileInformationForm :user="$page.props.user" />
            </ContentFrame>

            <ContentFrame>
                <UpdatePasswordForm class="mt-10 sm:mt-0" />
            </ContentFrame>

            <ContentFrame>
                <DeleteUserForm class="mt-10 sm:mt-0" />
            </ContentFrame>

            <div v-if="0" class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
                <div v-if="$page.props.jetstream.canUpdateProfileInformation">

                    <SectionBorder />
                </div>

                <div v-if="$page.props.jetstream.canUpdatePassword">

                    <SectionBorder />
                </div>

                <div v-if="$page.props.jetstream.canManageTwoFactorAuthentication">
                    <TwoFactorAuthenticationForm :requires-confirmation="confirmsTwoFactorAuthentication"
                        class="mt-10 sm:mt-0" />

                    <SectionBorder />
                </div>

                <LogoutOtherBrowserSessionsForm :sessions="sessions" class="mt-10 sm:mt-0" />

                <template v-if="$page.props.jetstream.hasAccountDeletionFeatures">
                    <SectionBorder />

                </template>
            </div>
        </div>
    </AppLayout>
</template>
