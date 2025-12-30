<script setup>
import { ref } from 'vue';

const theme = ref(localStorage.getItem('color-theme') || 'dark');
const dark = ref(theme.value === 'dark');

const toggleMode = () => {
    if(dark.value) {
        document.documentElement.classList.add('dark');
    } else {
        document.documentElement.classList.remove('dark');
    }
    localStorage.setItem('color-theme', dark.value ? 'dark' : 'light');
};

toggleMode();

</script>

<template>
    <div class="cursor-pointer" @click="dark = !dark;toggleMode()">
        <i v-if="dark" class="fa-solid fa-sun text-gray-400"></i>
        <i v-else class="fa-solid fa-moon text-gray-400"></i>
    </div>
</template>
