<script setup>
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import ApplicationMark from '@/Components/ApplicationMark.vue';
import Banner from '@/Components/Banner.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';

defineProps({
    title: String,
});

const showingNavigationDropdown = ref(false);

const switchToTeam = (team) => {
    router.put(route('current-team.update'), {
        team_id: team.id,
    }, {
        preserveState: false,
    });
};

const logout = () => {
    router.post(route('logout'));
};
</script>

<template>
    <div>

        <Head :title="title" />

        <Banner />

        <div class="min-h-screen bg-main pb-10">
            <nav class="sticky top-0 z-50 w-full">
                <div class="relative z-10 border-b-4 bg-main border-black">

                    <!-- Primary Navigation Menu -->
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                        <div class="flex justify-between h-16">
                            <div class="flex">
                                <!-- Logo -->
                                <div class="shrink-0 flex items-center">
                                    <Link :href="route('dashboard')">
                                    <ApplicationMark class="block h-9 w-auto" />
                                    </Link>
                                </div>

                                <!-- Navigation Links -->
                                <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                                    <NavLink :href="route('dashboard')" :active="route().current('dashboard')">
                                        測定
                                    </NavLink>

                                    <NavLink :href="route('preference')" :active="route().current('preference')">
                                        出題
                                    </NavLink>

                                    <NavLink :href="route('sentence')" :active="route().current('sentence')">
                                        文章
                                    </NavLink>

                                    <NavLink :href="route('stats')" :active="route().current('stats')">
                                        統計
                                    </NavLink>
                                </div>
                            </div>

                            <div class="hidden sm:flex sm:items-center sm:ml-6">
                                <div class="ml-3 relative">
                                    <!-- Teams Dropdown -->
                                    <Dropdown v-if="$page.props.jetstream.hasTeamFeatures" align="right" width="60">
                                        <template #trigger>
                                            <span class="inline-flex rounded-md">
                                                <button type="button"
                                                    class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:bg-gray-50 hover:text-gray-700 focus:outline-none focus:bg-gray-50 active:bg-gray-50 transition">
                                                    {{ $page.props.user.current_team.name }}

                                                    <svg class="ml-2 -mr-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                                                        fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                                        stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M8.25 15L12 18.75 15.75 15m-7.5-6L12 5.25 15.75 9" />
                                                    </svg>
                                                </button>
                                            </span>
                                        </template>

                                        <template #content>
                                            <div class="w-60">
                                                <!-- Team Management -->
                                                <template v-if="$page.props.jetstream.hasTeamFeatures">
                                                    <div class="block px-4 py-2 text-xs text-gray-400">
                                                        Manage Team
                                                    </div>

                                                    <!-- Team Settings -->
                                                    <DropdownLink
                                                        :href="route('teams.show', $page.props.user.current_team)">
                                                        Team Settings
                                                    </DropdownLink>

                                                    <DropdownLink v-if="$page.props.jetstream.canCreateTeams"
                                                        :href="route('teams.create')">
                                                        Create New Team
                                                    </DropdownLink>

                                                    <div class="border-t border-gray-100" />

                                                    <!-- Team Switcher -->
                                                    <div class="block px-4 py-2 text-xs text-gray-400">
                                                        Switch Teams
                                                    </div>

                                                    <template v-for="team in $page.props.user.all_teams" :key="team.id">
                                                        <form @submit.prevent="switchToTeam(team)">
                                                            <DropdownLink as="button">
                                                                <div class="flex items-center">
                                                                    <svg v-if="team.id == $page.props.user.current_team_id"
                                                                        class="mr-2 h-5 w-5 text-green-400"
                                                                        xmlns="http://www.w3.org/2000/svg" fill="none"
                                                                        viewBox="0 0 24 24" stroke-width="1.5"
                                                                        stroke="currentColor">
                                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                                            d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                                    </svg>

                                                                    <div>{{ team.name }}</div>
                                                                </div>
                                                            </DropdownLink>
                                                        </form>
                                                    </template>
                                                </template>
                                            </div>
                                        </template>
                                    </Dropdown>
                                </div>

                                <div class="ml-3 relative">
                                    <span class="inline-flex rounded-md">
                                        <NavLink :href="route('profile.show')" :active="route().current('profile.show')">
                                            {{ $page.props.user.name ? $page.props.user.name : 'アカウント設定' }}
                                        </NavLink>

                                        <form @submit.prevent="logout">
                                            <button
                                                class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium hover:text-gray-700 ">
                                                →
                                            </button>
                                        </form>
                                    </span>
                                </div>
                            </div>

                            <!-- Hamburger -->
                            <div class="-mr-2 flex items-center sm:hidden">
                                <button
                                    class="inline-flex items-center justify-center p-2 rounded-md text-black focus:outline-none transition"
                                    @click="showingNavigationDropdown = !showingNavigationDropdown">
                                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                        <path
                                            :class="{ 'hidden': showingNavigationDropdown, 'inline-flex': !showingNavigationDropdown }"
                                            stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 6h16M4 12h16M4 18h16" />
                                        <path
                                            :class="{ 'hidden': !showingNavigationDropdown, 'inline-flex': showingNavigationDropdown }"
                                            stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Responsive Navigation Menu -->
                    <div :class="{ 'block': showingNavigationDropdown, 'hidden': !showingNavigationDropdown }"
                        class="sm:hidden">
                        <div class="pt-2 pb-3 space-y-1">
                            <ResponsiveNavLink :href="route('dashboard')" :active="route().current('dashboard')">
                                測定
                            </ResponsiveNavLink>

                            <ResponsiveNavLink :href="route('preference')" :active="route().current('preference')">
                                出題
                            </ResponsiveNavLink>

                            <ResponsiveNavLink :href="route('sentence')" :active="route().current('sentence')">
                                文章
                            </ResponsiveNavLink>

                            <ResponsiveNavLink :href="route('stats')" :active="route().current('stats')">
                                統計
                            </ResponsiveNavLink>
                        </div>

                        <!-- Responsive Settings Options -->
                        <div class="pt-4 pb-1 border-t border-black">
                            <div class="flex items-center px-4">
                                <div v-if="$page.props.jetstream.managesProfilePhotos" class="shrink-0 mr-3">
                                    <img class="h-10 w-10 rounded-full object-cover"
                                        :src="$page.props.user.profile_photo_url" :alt="$page.props.user.name">
                                </div>

                                <div>
                                    <div class="font-medium text-base text-gray-800">
                                        {{ $page.props.user.name }}
                                    </div>
                                </div>
                            </div>

                            <div class="mt-3 space-y-1">
                                <ResponsiveNavLink :href="route('profile.show')" :active="route().current('profile.show')">
                                    プロフィール
                                </ResponsiveNavLink>

                                <ResponsiveNavLink v-if="$page.props.jetstream.hasApiFeatures"
                                    :href="route('api-tokens.index')" :active="route().current('api-tokens.index')">
                                    API Tokens
                                </ResponsiveNavLink>

                                <!-- Authentication -->
                                <form method="POST" @submit.prevent="logout">
                                    <ResponsiveNavLink as="button">
                                        ログアウト
                                    </ResponsiveNavLink>
                                </form>

                                <!-- Team Management -->
                                <template v-if="$page.props.jetstream.hasTeamFeatures">
                                    <div class="border-t border-gray-200" />

                                    <div class="block px-4 py-2 text-xs text-gray-400">
                                        Manage Team
                                    </div>

                                    <!-- Team Settings -->
                                    <ResponsiveNavLink :href="route('teams.show', $page.props.user.current_team)"
                                        :active="route().current('teams.show')">
                                        Team Settings
                                    </ResponsiveNavLink>

                                    <ResponsiveNavLink v-if="$page.props.jetstream.canCreateTeams"
                                        :href="route('teams.create')" :active="route().current('teams.create')">
                                        Create New Team
                                    </ResponsiveNavLink>

                                    <div class="border-t border-gray-200" />

                                    <!-- Team Switcher -->
                                    <div class="block px-4 py-2 text-xs text-gray-400">
                                        Switch Teams
                                    </div>

                                    <template v-for="team in $page.props.user.all_teams" :key="team.id">
                                        <form @submit.prevent="switchToTeam(team)">
                                            <ResponsiveNavLink as="button">
                                                <div class="flex items-center">
                                                    <svg v-if="team.id == $page.props.user.current_team_id"
                                                        class="mr-2 h-5 w-5 text-green-400"
                                                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                        stroke-width="1.5" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    </svg>
                                                    <div>{{ team.name }}</div>
                                                </div>
                                            </ResponsiveNavLink>
                                        </form>
                                    </template>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- フラッシュメッセージ -->
                <div v-if="$page.props.flash.message" @animationend="$page.props.flash.message = null"
                    class="flush bg-main font-bold absolute z-2 w-full text-center border-l-4 border-r-4 border-b-4 border-black rounded-b-md">
                    {{ $page.props.flash.message }}
                </div>
            </nav>

            <!-- Page Heading -->
            <header v-if="$slots.header" class="bg-white shadow">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    <slot name="header" />
                </div>
            </header>

            <!-- Page Content -->
            <main>
                <slot />
            </main>
        </div>
    </div>
</template>

<style scoped>
.flush {
    animation-name: flush;
    animation-delay: 3s;
    animation-duration: 2.5s;
    animation-timing-function: linear;
    animation-fill-mode: forwards;
}

@keyframes flush {
    0% {
        top: 68px;
    }

    100% {
        top: 0;
    }
}
</style>
