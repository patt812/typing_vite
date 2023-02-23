<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import DangerButton from '@/Components/DangerButton.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue'
import Checkbox from '@/Components/Checkbox.vue';
import { computed, onMounted, ref } from 'vue';

const props = defineProps({
    sentences: Array,
    focusInput: Boolean,
    from: String,
    showIfSelected: Boolean
});

const isPreference = ref(['preference'].includes(props.from))

const searchBox = ref(null);

const searchWord = ref('');

const radioDiv = ref(0);

const selectedIndex = ref(null);

const emits = defineEmits('fill');

const select = (sentence, index) => {
    if (index == selectedIndex.value) {
        selectedIndex.value = null;
        emits('fill', null);
        return;
    }
    selectedIndex.value = index;
    emits('fill', sentence);
}

const filtered = computed(() => {
    let sentence = props.sentences;

    if (radioDiv.value == 1) {
        sentence = sentence.filter(s => {
            return s.is_selected
        })
    } else if (radioDiv.value == 2) {
        sentence = sentence.filter(s => {
            return !s.is_selected
        })
    }
    if (sentence.length && searchWord.value) {
        sentence = sentence.filter(s => {
            return s.kana.includes(searchWord.value) || s.sentence.includes(searchWord.value)
        });
    }
    return sentence;
});

onMounted(() => {
    if (props.focusInput) {
        searchBox.value.focus();
    }
})

</script>

<template>
    <div>
        <div>
            <div v-if="isPreference" class="flex">
                <input type="radio" name="list-type" id="list-type-all" v-model="radioDiv" value="0">
                <InputLabel for="list-type-all" value="すべて表示" />

                <input type="radio" name="list-type" id="list-type-on" v-model="radioDiv" value="1">
                <InputLabel for="list-type-on" value="出題する" />

                <input type="radio" name="list-type" id="list-type-off" v-model="radioDiv" value="2">
                <InputLabel for="list-type-off" value="出題しない" />
            </div>

            <div class="flex w-full mt-2">
                <InputLabel for="sentence-search" value="検索" />
                <TextInput ref="searchBox" id="sentence-search" class="ml-2 w-full max-w-md" v-model="searchWord" />
            </div>
        </div>

        <div class="w-full mt-2">
            <div class="flex items-center justify-between border-black border-b-2 font-bold">
                <div class="w-1/12 px-2 py-1"></div>
                <div v-if="showIfSelected" class="w-1/12 px-2 py-1">出題</div>
                <div class="w-5/12 px-2 py-1">文章</div>
                <div class="w-5/12 px-2 py-1">かな</div>
            </div>

            <div class="max-h-60 overflow-auto scroll-bar mb-3">
                <template v-for="(sentence, index) in filtered" :key="sentence.id">
                    <div class="flex items-center justify-between cursor-pointer py-1 border-black border-b-2"
                        :class="{ 'bg-black text-white': index == selectedIndex }" @click="select(sentence, index)">
                        <div class="w-1/12 px-2 text-center">{{ index + 1 }}</div>
                        <Checkbox v-if="showIfSelected" v-model:checked="sentence.is_selected" @click.stop="" />
                        <div :class="{ 'truncate': selectedIndex != index }" class="w-5/12 px-2">
                            {{ sentence.sentence }}
                        </div>
                        <div :class="{ 'truncate': selectedIndex != index }" class="w-5/12 px-2">
                            {{ sentence.kana }}
                        </div>
                    </div>
                </template>
                <div v-if="!filtered.length" class="text-center py-1 border-black border-b-2">見つかりませんでした...</div>
            </div>
        </div>

        <PrimaryButton v-if="isPreference" @click="$emit('store')">反映する</PrimaryButton>
    </div>
</template>
