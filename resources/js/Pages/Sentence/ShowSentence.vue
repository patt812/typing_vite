<script setup>
  import { Head, useForm, usePage } from '@inertiajs/vue3';
  import { computed, onMounted, ref, nextTick } from 'vue';
  import AppModal from '@/Components/AppModal.vue';
  import Checkbox from '@/Components/Checkbox.vue';
  import ContentFrame from '@/Components/ContentFrame.vue';
  import DangerButton from '@/Components/DangerButton.vue';
  import InputError from '@/Components/InputError.vue';
  import InputLabel from '@/Components/InputLabel.vue';
  import PrimaryButton from '@/Components/PrimaryButton.vue';
  import SecondaryButton from '@/Components/SecondaryButton.vue';
  import TextInput from '@/Components/TextInput.vue';
  import AppLayout from '@/Layouts/AppLayout.vue';
  import SentenceList from '@/Pages/Sentence/Partials/SentenceList.vue';

  const props = defineProps({
    sentences: {
      type: Array,
      required: true,
    },
    status: {
      type: String,
      required: true,
    },
  });
  const guestPrefix = ref(usePage().props.user.id === null ? 'guest.' : '');

  const sentence = ref(null);

  const isInsert = ref(true);

  const insertTemplate = ref({
    sentence: '',
    kana: '',
    error: '',
  });
  const inserts = ref(Array.from({ length: 10 }, () => ({ ...insertTemplate.value })));

  const checkInserts = () => {
    const kanaList = props.sentences.map((storeSentence) => storeSentence.kana);
    let isValid = true;
    for (let i = 0; i < inserts.value.length; i += 1) {
      const row = inserts.value[i];
      if (!row.kana && !row.sentence) {
        inserts.value.splice(i, 1);
        i -= 1;
        continue;
      }
      if (row.kana && kanaList.includes(row.kana)) {
        inserts.value[i].error = '入力したかなは既に登録されているか、重複しています。';
        isValid = false;
        continue;
      } else {
        kanaList.push(row.kana);
      }

      if (!row.sentence) {
        inserts.value[i].error = '文章は入力必須です。';
        isValid = false;
        continue;
      }
      if (!row.kana) {
        inserts.value[i].error = 'かなは入力必須です。';
        isValid = false;
        continue;
      }
      const invalidChars = new Set(row.kana.match(/[^ぁ-ゞァ-ヾ！-／：-＠［-｀｛-～\d]/g));
      if (invalidChars.size) {
        inserts.value[i].error = `かなに使用できない文字が含まれています：${Array.from(
          invalidChars
        ).join(',')}`;
        isValid = false;
      } else {
        inserts.value[i].error = '';
      }
    }
    return isValid;
  };

  onMounted(() => {
    sentence.value.focus();
  });

  const fileInput = ref(null);
  const openCSVFileInput = () => {
    nextTick(() => {
      fileInput.value.click();
    });
  };

  const resetConfirm = ref(false);
  const tempCSVData = ref(null);

  const parseCSV = (contents) => {
    const newInserts = [];
    const lines = contents.split('\n').filter((line) => line.trim());

    lines.forEach((line) => {
      let csvSentence = '';
      let csvKana = '';
      const cells = line.split(',');
      if (cells.length === 2) {
        csvSentence = cells[0].replace(/^["']|["']$/g, '').trim();
        csvKana = cells[1].replace(/^["']|["']$/g, '').trim();
      }

      newInserts.push({ sentence: csvSentence, kana: csvKana, error: '' });
    });
    return newInserts;
  };

  const readCSV = (event) => {
    const input = event.target;
    const file = input.files[0];
    if (file) {
      const reader = new FileReader();
      reader.onload = function (e) {
        const contents = e.target.result;
        const newInserts = parseCSV(contents);
        // 既存の入力があるか確認
        if (inserts.value.some((item) => item.sentence || item.kana)) {
          resetConfirm.value = true;
          // 一時的にCSVデータを保存
          tempCSVData.value = newInserts;
        } else {
          // 既存の入力がない場合はそのまま反映
          inserts.value = newInserts;
          checkInserts();
        }
      };
      reader.readAsText(file);
    }
    input.value = null;
  };

  const resetAndLoadCSV = () => {
    if (tempCSVData.value) {
      inserts.value = tempCSVData.value;
      tempCSVData.value = null;
      checkInserts();
    }
    resetConfirm.value = false;
  };

  const form = useForm({
    id: null,
    sentence: '',
    kana: '',
    index: null,
  });

  // const updateForm = useForm({
  //   id: null,
  //   sentence: '',
  //   kana: '',
  // });

  const isSentenceAndKanaFilled = computed(() => !form.kana || !form.sentence);

  const appendInserts = (num) => {
    for (let i = 0; i < num; i += 1) {
      inserts.value.push({ ...insertTemplate.value });
    }
  };

  const submit = () => {
    if (isInsert.value) {
      form.put(route(`${guestPrefix.value}sentence.store`), {
        onSuccess: () => {
          form.reset();
        },
      });
    } else {
      form.put(route(`${guestPrefix.value}sentence.update`));
    }
  };

  const erase = () => {
    form.delete(route(`${guestPrefix.value}sentence.delete`), {
      onSuccess: () => {
        form.reset();
      },
    });
  };

  const processingBulkStore = ref(false);

  const bulkStore = () => {
    if (!checkInserts()) return;
    if (!inserts.value.length) {
      appendInserts(10);
      return;
    }

    const bulkStoreData = useForm({ sentences: inserts.value });
    processingBulkStore.value = true;
    bulkStoreData.post(route('sentences.store'), {
      onSuccess: () => {
        inserts.value = Array.from({ length: 10 }, () => ({ ...insertTemplate.value }));
      },
      onFinish: () => {
        processingBulkStore.value = false;
      },
    });
  };

  const onFill = (fillSentence) => {
    if (!fillSentence || (!guestPrefix.value && fillSentence.id === form.id)) {
      form.reset();
      isInsert.value = true;
      return;
    }
    form.id = fillSentence.id;
    form.sentence = fillSentence.sentence;
    form.kana = fillSentence.kana;
    form.index = fillSentence.index;
    isInsert.value = false;
  };

  const showRegisterModal = ref(false);

  const guestRegisterForm = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    take_over: true,
  });

  const submitGuestRegister = () => {
    guestRegisterForm.post(route('guest.register'), {
      onFinish: () => form.reset('password', 'password_confirmation'),
    });
  };
</script>

<template>
  <AppLayout>
    <Head title="文章管理" />

    <div>
      <ContentFrame>
        <template #title> 文章登録 </template>

        <template #content>
          <SentenceList :sentences="sentences" :from="'sentence'" @fill="onFill" />

          <form class="mt-4" @submit.prevent="submit">
            <div>
              <InputLabel for="sentence" value="文章" />
              <TextInput
                id="sentence"
                ref="sentence"
                v-model="form.sentence"
                type="text"
                class="mt-1 w-full max-w-2xl"
                required
              />
              <InputError :message="form.errors.sentence" />

              <InputLabel for="kana" class="mt-3" value="かな" />
              <TextInput
                id="kana"
                v-model="form.kana"
                type="text"
                class="mt-1 w-full max-w-2xl"
                required
              />
              <InputError :message="form.errors.kana" />
            </div>

            <div class="w-full mt-6 flex align-middle items-center justify-center">
              <PrimaryButton
                :disabled="
                  form.processing ||
                  isSentenceAndKanaFilled ||
                  ($page.props.user.id === null && sentences.length >= 10 && isInsert)
                "
              >
                {{ isInsert ? '登録' : '更新' }}
              </PrimaryButton>
              <DangerButton
                class="ml-16"
                :disabled="
                  form.processing ||
                  sentences.length <= 1 ||
                  ($page.props.user.id && !form.id) ||
                  (!$page.props.user.id && form.index === null)
                "
                @click.prevent="erase"
              >
                削除
              </DangerButton>
            </div>
          </form>

          <div
            v-if="$page.props.user.id === null && sentences.length >= 10"
            class="mt-6 text-center"
          >
            <div>
              ゲストが登録できる文章は、10件までです。<br />
              文章や統計をそのまま引き継いで登録できます。
            </div>
            <PrimaryButton class="mt-2 mx-auto" @click="showRegisterModal = true"
              >会員登録</PrimaryButton
            >
          </div>

          <InputError
            v-if="sentences.length <= 1"
            class="mt-4 text-center"
            message="登録した文章が１つのみの場合は、削除できません"
          />
        </template>
      </ContentFrame>

      <ContentFrame :is-guest="$page.props.user.id === null">
        <template #title> 一括登録 </template>

        <template #content>
          <div class="mt-1">文章とかなが入力されていない行は登録時に自動削除されます。</div>
          <div class="mt-1">Shift + Enterでも登録できます。</div>

          <div class="flex mt-3">
            <SecondaryButton @click="appendInserts(1)"> +1 </SecondaryButton>
            <SecondaryButton class="ml-3" @click="appendInserts(5)"> +5 </SecondaryButton>
            <SecondaryButton class="ml-3" @click="openCSVFileInput">CSV読込</SecondaryButton>
            <input ref="fileInput" type="file" accept=".csv" class="hidden" @change="readCSV" />
          </div>

          <form @submit.prevent="">
            <div class="w-full flex my-2 border-b-2 border-black">
              <div class="w-10" />
              <div class="font-bold w-6/12 text-center">文章</div>
              <div class="font-bold w-6/12 text-center">かな</div>
              <div class="w-10" />
              <div class="w-8" />
            </div>

            <div class="mt-1 scroll-bar overflow-auto max-h-80 w-full">
              <div v-for="(row, index) in inserts" :key="row">
                <div class="flex w-full mb-1">
                  <div class="w-10 text-center">
                    {{ index + 1 }}
                  </div>
                  <div class="w-6/12">
                    <TextInput
                      v-model="row.sentence"
                      class="w-full"
                      :disabled="processingBulkStore"
                      @keydown.shift.enter="bulkStore"
                    />
                  </div>
                  <div class="w-6/12">
                    <TextInput
                      v-model="row.kana"
                      class="w-full ml-1"
                      :disabled="processingBulkStore"
                      @keydown.shift.enter="bulkStore"
                    />
                  </div>
                  <div
                    class="flex items-center cursor-pointer w-4 ml-3"
                    @click="inserts.splice(index, 0, { ...row, error: '' })"
                  >
                    <svg
                      xmlns="http://www.w3.org/2000/svg"
                      class="w-fit h-[18px]"
                      viewBox="0 0 64 64"
                      aria-labelledby="title"
                      aria-describedby="desc"
                      role="img"
                      xmlns:xlink="http://www.w3.org/1999/xlink"
                    >
                      <title>Copy</title>
                      <desc>A line styled icon from Orion Icon Library.</desc>
                      <path
                        data-name="layer2"
                        fill="none"
                        stroke="#000000"
                        stroke-miterlimit="10"
                        stroke-width="4"
                        d="M16 48H2V2h46v14"
                        stroke-linejoin="round"
                        stroke-linecap="round"
                      />
                      <path
                        data-name="layer1"
                        fill="none"
                        stroke="#000000"
                        stroke-miterlimit="10"
                        stroke-width="4"
                        d="M16 16h46v46H16z"
                        stroke-linejoin="round"
                        stroke-linecap="round"
                      />
                    </svg>
                  </div>
                  <div
                    v-if="inserts.length > 1"
                    class="flex items-center cursor-pointer w-4 ml-1 mr-1"
                    @click="inserts.splice(index, 1)"
                  >
                    <svg
                      xmlns="http://www.w3.org/2000/svg"
                      class="w-fit h-6"
                      viewBox="0 0 64 64"
                      aria-labelledby="title"
                      aria-describedby="desc"
                      role="img"
                      xmlns:xlink="http://www.w3.org/1999/xlink"
                    >
                      <title>Close</title>
                      <desc>A line styled icon from Orion Icon Library.</desc>
                      <path
                        data-name="layer1"
                        fill="none"
                        stroke="#ff0000"
                        stroke-miterlimit="10"
                        stroke-width="6"
                        d="M41.999 20.002l-22 22m22 0L20 20"
                        stroke-linejoin="round"
                        stroke-linecap="round"
                      />
                    </svg>
                  </div>
                </div>
                <div class="mb-2 pl-9">
                  <InputError :message="row.error" />
                </div>
              </div>
            </div>

            <PrimaryButton
              type="button"
              class="mt-4 mx-auto"
              :disabled="processingBulkStore"
              @click="bulkStore"
            >
              一括登録
            </PrimaryButton>
          </form>
        </template>
      </ContentFrame>
    </div>

    <AppModal v-if="resetConfirm" :show="resetConfirm" @close="resetConfirm = false">
      <div class="mb-4 text-lg font-bold">CSVインポートの確認</div>
      <div>
        <p>CSVを読み込むと、既存の入力内容が上書きされます。</p>
        <p>このままCSVを読み込みますか？</p>

        <div class="mt-7 flex justify-center">
          <SecondaryButton @click="resetConfirm = false"> 戻る </SecondaryButton>
          <DangerButton class="ml-10" @click="resetAndLoadCSV">リセットする</DangerButton>
        </div>
      </div>
    </AppModal>

    <AppModal v-if="showRegisterModal" :show="showRegisterModal" @close="showRegisterModal = false">
      <div v-if="status" class="mb-4 font-medium text-sm text-green-600">
        {{ status }}
      </div>

      <form @submit.prevent="submitGuestRegister">
        <div>
          <InputLabel for="name" value="ユーザー名" />
          <TextInput
            id="name"
            v-model="guestRegisterForm.name"
            type="text"
            class="mt-1 block w-full"
            required
            autofocus
            autocomplete="name"
            maxlength="255"
          />
          <InputError class="mt-2" :message="guestRegisterForm.errors.name" />
        </div>

        <div class="mt-4">
          <InputLabel for="email" value="メールアドレス" />
          <TextInput
            id="email"
            v-model="guestRegisterForm.email"
            type="email"
            class="mt-1 block w-full"
            required
            maxlength="255"
          />
          <InputError class="mt-2" :message="guestRegisterForm.errors.email" />
        </div>

        <div class="mt-4">
          <InputLabel for="password" value="パスワード" />
          <TextInput
            id="password"
            v-model="guestRegisterForm.password"
            type="password"
            class="mt-1 block w-full"
            required
            autocomplete="new-password"
          />
          <InputError class="mt-2" :message="guestRegisterForm.errors.password" />
        </div>

        <div class="mt-4 mb-4">
          <InputLabel for="password_confirmation" value="パスワード（確認）" />
          <TextInput
            id="password_confirmation"
            v-model="guestRegisterForm.password_confirmation"
            type="password"
            class="mt-1 block w-full"
            required
            autocomplete="new-password"
          />
          <InputError class="mt-2" :message="guestRegisterForm.errors.password_confirmation" />
        </div>

        <label class="flex items-center">
          <Checkbox v-model:checked="guestRegisterForm.take_over" name="take_over" dashed="true" />
          <span class="ml-2 text-sm text-gray-600">文章や統計を引き継ぐ</span>
        </label>

        <PrimaryButton
          class="mt-4 mx-auto"
          :class="{ 'opacity-25': guestRegisterForm.processing }"
          :disabled="guestRegisterForm.processing"
        >
          登録
        </PrimaryButton>
      </form>
    </AppModal>
  </AppLayout>
</template>
