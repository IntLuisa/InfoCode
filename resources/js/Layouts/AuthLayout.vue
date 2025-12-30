<script setup>
import DarkMode from '@/Components/DarkMode.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    title: String,
    status: String,
});
</script>

<template>

    <Head :title="title" />

    <div class="fixed top-4 right-6">
        <DarkMode />
    </div>
    <div class="flex flex-col items-center justify-center px-6 py-8 mx-auto md:h-screen lg:py-0">
        <Link :href="route('home')" class="flex items-center text-2xl font-semibold">
        <img src="/assets/images/template/logo.png" class="mb-4 h-40" />
        </Link>

        <div v-if="status" class="mb-4 font-medium text-sm text-green-600 dark:text-green-400">
            {{ status }}
        </div>

        <div
            class="w-full bg-white rounded-lg shadow dark:border md:mt-0 sm:max-w-md xl:p-0 dark:bg-gray-800 dark:border-gray-700">
            <slot />
        </div>
    </div>
</template>
