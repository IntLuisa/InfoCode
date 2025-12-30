<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';

</script>

<template>
    <AppLayout :title="title" :has_menu="false">
        <template #content>
            <div class="flex flex-col items-center justify-center px-6 py-8 mx-auto md:h-screen lg:py-0">
                <div class="grid grid-cols-1 sm:grid-cols-2 p-4 gap-4">
                    <Link :href="route('dashboard')" class="block p-4 px-20 text-center rounded-lg border border-2 text-[100px]
                            border-gray-300 text-gray-900
                            dark:text-white dark:border-gray-700
                            hover:text-blue-800 hover:border-blue-700 hover:bg-gray-100
                            dark:hover:border-blue-900 dark:hover:text-blue-300 dark:hover:bg-gray-800">
                    <i class="fa-solid fa-gears mx-auto"></i>
                    <div class="text-[20px] font-medium">Krenke</div>
                    </Link>
                </div>
            </div>
        </template>
    </AppLayout>
</template>
