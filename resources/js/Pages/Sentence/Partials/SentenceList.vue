<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import DangerButton from '@/Components/DangerButton.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue'
import Checkbox from '@/Components/Checkbox.vue';
import { computed, ref } from 'vue';

const props = defineProps({
    sentences: Array,
    from: String,
});

const isPreference = ref(props.from == 'preference')

const searchWord = ref('');

const radioDiv = ref(0);

const filtered = computed(() => {
    let sentence = props.sentences;
    if (!isPreference.value) return sentence;

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

            <div class="flex">
                <InputLabel for="search-box" value="検索" />
                <TextInput id="search-box" v-model="searchWord" />
            </div>
        </div>

        <div v-if="isPreference" class="flex cursor-pointer">
            <div class="mr-2"></div>
            <div class="mr-2">出題</div>
            <div class="mr-2">かな</div>
            <div class="mr-2">文章</div>
        </div>

        <div v-for="(sentence, index) in filtered" :key="sentence.id">
            <div class="flex cursor-pointer" @click="$emit('fill', sentence)">
                <div class="mr-2">{{ index + 1 }}</div>
                <Checkbox v-model:checked="sentence.is_selected" />
                <div class="mr-2">{{ sentence.sentence }}</div>
                <div>{{ sentence.kana }}</div>
            </div>
        </div>

        <PrimaryButton v-if="isPreference" @click="$emit('store')">反映する</PrimaryButton>
    </div>
</template>
