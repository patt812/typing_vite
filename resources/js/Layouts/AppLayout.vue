<script setup>
  import { Head, Link, router, useForm, usePage } from '@inertiajs/vue3';
  import { ref } from 'vue';
  import ApplicationMark from '@/Components/ApplicationMark.vue';
  import AppModal from '@/Components/AppModal.vue';
  import Checkbox from '@/Components/Checkbox.vue';
  import DangerButton from '@/Components/DangerButton.vue';
  import Dropdown from '@/Components/Dropdown.vue';
  import DropdownLink from '@/Components/DropdownLink.vue';
  import InputError from '@/Components/InputError.vue';
  import InputLabel from '@/Components/InputLabel.vue';
  import NavLink from '@/Components/NavLink.vue';
  import PrimaryButton from '@/Components/PrimaryButton.vue';
  import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
  import SecondaryButton from '@/Components/SecondaryButton.vue';
  import TextInput from '@/Components/TextInput.vue';

  defineProps({
    title: {
      type: String,
      default: '',
    },
  });

  const guestPrefix = ref(usePage().props.user.id === null ? 'guest.' : '');
  const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    take_over: true,
  });
  const openLogoutModal = ref(false);
  const showRegisterModal = ref(false);

  const showingNavigationDropdown = ref(false);

  const navigateRegisterModal = () => {
    openLogoutModal.value = false;
    showRegisterModal.value = true;
  };

  const switchToTeam = (team) => {
    router.put(
      route('current-team.update'),
      {
        team_id: team.id,
      },
      {
        preserveState: false,
      }
    );
  };

  const loggingOut = ref(false);

  const logout = (confirmed = false) => {
    if (loggingOut.value) return;
    if (guestPrefix.value && !confirmed) {
      openLogoutModal.value = true;
      return;
    }
    router.post(route(`${guestPrefix.value}logout`), null, {
      preserveScroll: true,
      onStart: () => {
        loggingOut.value = true;
      },
      onFinish: () => {
        loggingOut.value = false;
      },
    });
  };

  const guestRegister = () => {
    form.post(route('guest.register'), {
      onFinish: () => form.reset('password', 'password_confirmation'),
    });
  };
</script>

<template>
  <div>
    <Head :title="title" />

    <ApplicationBanner />

    <div class="min-h-screen bg-main pb-10">
      <nav class="sticky top-0 z-50 w-full">
        <div class="relative z-10 border-b-4 bg-main border-black">
          <!-- Primary Navigation Menu -->
          <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
              <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                  <Link :href="route(guestPrefix + 'dashboard')">
                    <ApplicationMark class="block h-9 w-auto" />
                  </Link>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                  <NavLink
                    :href="route(guestPrefix + 'dashboard')"
                    :active="route().current(guestPrefix + 'dashboard')"
                  >
                    測定
                  </NavLink>

                  <NavLink
                    :href="route(guestPrefix + 'preference')"
                    :active="route().current(guestPrefix + 'preference')"
                  >
                    出題
                  </NavLink>

                  <NavLink
                    :href="route(guestPrefix + 'sentence')"
                    :active="route().current(guestPrefix + 'sentence')"
                  >
                    文章
                  </NavLink>

                  <NavLink
                    :href="route(guestPrefix + 'stats')"
                    :active="route().current(guestPrefix + 'stats')"
                  >
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
                        <button
                          type="button"
                          class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:bg-gray-50 hover:text-gray-700 focus:outline-none focus:bg-gray-50 active:bg-gray-50 transition"
                        >
                          {{ $page.props.user.current_team.name }}

                          <svg
                            class="ml-2 -mr-0.5 h-4 w-4"
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="1.5"
                            stroke="currentColor"
                          >
                            <path
                              stroke-linecap="round"
                              stroke-linejoin="round"
                              d="M8.25 15L12 18.75 15.75 15m-7.5-6L12 5.25 15.75 9"
                            />
                          </svg>
                        </button>
                      </span>
                    </template>

                    <template #content>
                      <div class="w-60">
                        <!-- Team Management -->
                        <template v-if="$page.props.jetstream.hasTeamFeatures">
                          <div class="block px-4 py-2 text-xs text-gray-400">Manage Team</div>

                          <!-- Team Settings -->
                          <DropdownLink :href="route('teams.show', $page.props.user.current_team)">
                            Team Settings
                          </DropdownLink>

                          <DropdownLink
                            v-if="$page.props.jetstream.canCreateTeams"
                            :href="route('teams.create')"
                          >
                            Create New Team
                          </DropdownLink>

                          <div class="border-t border-gray-100" />

                          <!-- Team Switcher -->
                          <div class="block px-4 py-2 text-xs text-gray-400">Switch Teams</div>

                          <template v-for="team in $page.props.user.all_teams" :key="team.id">
                            <form @submit.prevent="switchToTeam(team)">
                              <DropdownLink as="button">
                                <div class="flex items-center">
                                  <svg
                                    v-if="team.id == $page.props.user.current_team_id"
                                    class="mr-2 h-5 w-5 text-green-400"
                                    xmlns="http://www.w3.org/2000/svg"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke-width="1.5"
                                    stroke="currentColor"
                                  >
                                    <!-- eslint-disable-next-line max-len -->
                                    <path
                                      stroke-linecap="round"
                                      stroke-linejoin="round"
                                      d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                                    />
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
                    <NavLink
                      v-if="$page.props.user.id"
                      :href="route('profile.show')"
                      :active="route().current('profile.show')"
                    >
                      {{ $page.props.user.name ? $page.props.user.name : 'アカウント設定' }}
                    </NavLink>
                    <SecondaryButton v-else @click="showRegisterModal = true">
                      会員登録
                    </SecondaryButton>

                    <form @submit.prevent="logout(false)">
                      <button
                        class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium hover:text-gray-700"
                      >
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
                  @click="showingNavigationDropdown = !showingNavigationDropdown"
                >
                  <SecondaryButton class="mr-4" @click.stop="showRegisterModal = true">
                    会員登録
                  </SecondaryButton>
                  <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                    <path
                      :class="{
                        hidden: showingNavigationDropdown,
                        'inline-flex': !showingNavigationDropdown,
                      }"
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M4 6h16M4 12h16M4 18h16"
                    />
                    <path
                      :class="{
                        hidden: !showingNavigationDropdown,
                        'inline-flex': showingNavigationDropdown,
                      }"
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M6 18L18 6M6 6l12 12"
                    />
                  </svg>
                </button>
              </div>
            </div>
          </div>

          <!-- Responsive Navigation Menu -->
          <div
            :class="{ block: showingNavigationDropdown, hidden: !showingNavigationDropdown }"
            class="sm:hidden"
          >
            <div class="pt-2 pb-3 space-y-1">
              <ResponsiveNavLink
                :href="route(guestPrefix + 'dashboard')"
                :active="route().current(guestPrefix + 'dashboard')"
              >
                測定
              </ResponsiveNavLink>

              <ResponsiveNavLink
                :href="route(guestPrefix + 'preference')"
                :active="route().current(guestPrefix + 'preference')"
              >
                出題
              </ResponsiveNavLink>

              <ResponsiveNavLink
                :href="route(guestPrefix + 'sentence')"
                :active="route().current(guestPrefix + 'sentence')"
              >
                文章
              </ResponsiveNavLink>

              <ResponsiveNavLink
                :href="route(guestPrefix + 'stats')"
                :active="route().current(guestPrefix + 'stats')"
              >
                統計
              </ResponsiveNavLink>
            </div>

            <!-- Responsive Settings Options -->
            <div class="pt-4 pb-1 border-t border-black">
              <div class="flex items-center px-4">
                <div v-if="$page.props.jetstream.managesProfilePhotos" class="shrink-0 mr-3">
                  <img
                    class="h-10 w-10 rounded-full object-cover"
                    :src="$page.props.user.profile_photo_url"
                    :alt="$page.props.user.name"
                  />
                </div>

                <div>
                  <div class="font-medium text-base text-gray-800">
                    {{ $page.props.user.name }}
                  </div>
                </div>
              </div>

              <div class="mt-3 space-y-1">
                <ResponsiveNavLink
                  v-if="$page.props.user.id"
                  :href="route('profile.show')"
                  :active="route().current('profile.show')"
                >
                  プロフィール
                </ResponsiveNavLink>

                <ResponsiveNavLink
                  v-if="$page.props.jetstream.hasApiFeatures"
                  :href="route('api-tokens.index')"
                  :active="route().current('api-tokens.index')"
                >
                  API Tokens
                </ResponsiveNavLink>

                <!-- Authentication -->
                <form method="POST" @submit.prevent="logout(false)">
                  <ResponsiveNavLink as="button"> ログアウト </ResponsiveNavLink>
                </form>

                <!-- Team Management -->
                <template v-if="$page.props.jetstream.hasTeamFeatures">
                  <div class="border-t border-gray-200" />

                  <div class="block px-4 py-2 text-xs text-gray-400">Manage Team</div>

                  <!-- Team Settings -->
                  <ResponsiveNavLink
                    :href="route('teams.show', $page.props.user.current_team)"
                    :active="route().current('teams.show')"
                  >
                    Team Settings
                  </ResponsiveNavLink>

                  <ResponsiveNavLink
                    v-if="$page.props.jetstream.canCreateTeams"
                    :href="route('teams.create')"
                    :active="route().current('teams.create')"
                  >
                    Create New Team
                  </ResponsiveNavLink>

                  <div class="border-t border-gray-200" />

                  <!-- Team Switcher -->
                  <div class="block px-4 py-2 text-xs text-gray-400">Switch Teams</div>

                  <template v-for="team in $page.props.user.all_teams" :key="team.id">
                    <form @submit.prevent="switchToTeam(team)">
                      <ResponsiveNavLink as="button">
                        <div class="flex items-center">
                          <svg
                            v-if="team.id == $page.props.user.current_team_id"
                            class="mr-2 h-5 w-5 text-green-400"
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="1.5"
                            stroke="currentColor"
                          >
                            <path
                              stroke-linecap="round"
                              stroke-linejoin="round"
                              d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                            />
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
        <div
          v-if="$page.props.flash.message"
          class="flush bg-main font-bold absolute z-2 w-full text-center border-l-4 border-r-4 border-b-4 border-black rounded-b-md"
          @animationend="$page.props.flash.message = null"
        >
          {{ $page.props.flash.message }}
        </div>
      </nav>

      <!-- Page Heading -->
      <header v-if="$slots.header" class="bg-white shadow">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
          <slot name="header" />
        </div>
      </header>

      <!-- ゲスト時のログアウトモーダル -->
      <AppModal v-if="openLogoutModal" :show="openLogoutModal" @close="openLogoutModal = false">
        <div>
          <div class="font-bold">
            ログアウトすると、<span class="text-red-600">文章や統計が完全に削除</span
            >されます。<br />
            この作業は取り消せません。 <br />
            ログアウトしますか？
          </div>

          <div class="flex mt-3 mx-auto gap-12 items-center justify-center">
            <PrimaryButton :disabled="loggingOut" @click="navigateRegisterModal"
              >会員登録</PrimaryButton
            >
            <DangerButton :disabled="loggingOut" @click="logout(true)">ログアウト</DangerButton>
          </div>
        </div>
      </AppModal>

      <AppModal
        v-if="showRegisterModal"
        :show="showRegisterModal"
        @close="showRegisterModal = false"
      >
        <form @submit.prevent="guestRegister">
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
