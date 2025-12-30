<script setup>
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    title: String,
});
</script>

<template>

    <Head :title="title" />
    <div class="flex flex-col justify-center min-h-screen align-middle">
        <div class="text-white text-center overflow-hidden">
            <div class="absolute top-4 right-6">
                <Link :href="route('login')"
                    class="ms-2 hover:text-gray-800 text-gray-600 dark:text-white dark:hover:text-primary-100">Entrar
                </Link>
            </div>
        </div>
        <div class="flex flex-col items-center text-white">
            <slot name="header" />
        </div>
        <slot />
    </div>
</template>
