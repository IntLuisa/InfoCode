<script setup>
import LandingPageLayout from '@/Layouts/LandingPageLayout.vue';
import { Link } from '@inertiajs/vue3';

const title = 'Welcome';

</script>

<template>
    <LandingPageLayout :title="title">
        <template #header>
            <Link :href="route('login')" class="flex flex-col items-center space-y-2">
            <img src="/assets/images/template/OrçaParque.png" class="h-100" />
            </Link>
        </template>
    </LandingPageLayout>
</template>
